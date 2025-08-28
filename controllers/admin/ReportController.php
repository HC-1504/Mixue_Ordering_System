<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../models/Order.php';
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/Category.php';

class ReportController
{
    private $orderModel;
    private $productModel;
    private $categoryModel;
    private $apiBaseUrl;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->apiBaseUrl = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/Assignment/api/admin';
    }

    public function index()
    {
        // Check admin permissions
        if (!in_array(Session::get('user_role'), ['admin', 'manager'])) {
            Session::set('report_errors', ['You do not have permission to access reports.']);
            header('Location: dashboard.php');
            exit();
        }

        $page_title = 'Reports Dashboard';
        require_once __DIR__ . '/../../views/admin/reports/index.php';
    }

    /**
     * Generate sales report by consuming Order API
     */
    public function generateSalesReport($startDate = null, $endDate = null)
    {
        try {
            // For now, use direct database access to ensure it works
            // TODO: Re-enable API consumption once we debug the session issue
            $ordersData = $this->getOrdersFromDatabase();

            if (!$ordersData || $ordersData['status'] !== 'success') {
                throw new Exception('Failed to fetch orders data');
            }

            $orders = $ordersData['data']['orders'];

            // Filter orders by date if provided
            if ($startDate && $endDate) {
                $originalCount = count($orders);
                $orders = array_filter($orders, function ($order) use ($startDate, $endDate) {
                    $orderDate = date('Y-m-d', strtotime($order['created_at']));
                    return $orderDate >= $startDate && $orderDate <= $endDate;
                });
                $filteredCount = count($orders);
                error_log("Sales Report - Date filter: {$startDate} to {$endDate}, Orders: {$originalCount} -> {$filteredCount}");
            }

            // Calculate sales metrics
            $totalSales = 0;
            $totalOrders = count($orders);
            $statusCounts = [];
            $dailySales = [];
            $completedOrdersCount = 0;


            foreach ($orders as $order) {
                if ($order['status'] === 'Completed' || $order['status'] === 'Delivered') {

                    $totalSales += (float)$order['total'];
                    $completedOrdersCount++;
                }

                // Count by status
                $status = $order['status'];
                $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;

                // Group by date
                $date = date('Y-m-d', strtotime($order['created_at']));
                $dailySales[$date] = ($dailySales[$date] ?? 0) + (float)$order['total'];
            }

            // Sort daily sales by date (oldest first)
            ksort($dailySales);

            // Calculate average order value



            $averageOrderValue = $completedOrdersCount > 0 ? $totalSales / $completedOrdersCount : 0;

            return [
                'status' => 'success',
                'data' => [
                    'total_sales' => $totalSales,
                    'total_orders' => $totalOrders,
                    'average_order_value' => $averageOrderValue,
                    'status_distribution' => $statusCounts,
                    'daily_sales' => $dailySales,
                    'orders' => $orders, // 添加订单数据用于表格显示
                    'date_range' => [
                        'start' => $startDate,
                        'end' => $endDate
                    ]
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to generate sales report: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate order status report
     */
    public function generateOrderStatusReport($startDate = null, $endDate = null)
    {
        try {
            // For now, use direct database access to ensure it works
            // TODO: Re-enable API consumption once we debug the session issue
            $ordersData = $this->getOrdersFromDatabase();

            if (!$ordersData || $ordersData['status'] !== 'success') {
                throw new Exception('Failed to fetch orders data');
            }

            $orders = $ordersData['data']['orders'];

            // Filter orders by date if provided
            if ($startDate && $endDate) {
                $originalCount = count($orders);
                $orders = array_filter($orders, function ($order) use ($startDate, $endDate) {
                    $orderDate = date('Y-m-d', strtotime($order['created_at']));
                    return $orderDate >= $startDate && $orderDate <= $endDate;
                });
                $filteredCount = count($orders);
                error_log("Status Report - Date filter: {$startDate} to {$endDate}, Orders: {$originalCount} -> {$filteredCount}");
            }

            $statusReport = [];
            $totalOrders = count($orders);

            foreach ($orders as $order) {
                $status = $order['status'];
                if (!isset($statusReport[$status])) {
                    $statusReport[$status] = [
                        'count' => 0,
                        'percentage' => 0,
                        'total_value' => 0
                    ];
                }

                $statusReport[$status]['count']++;
                $statusReport[$status]['total_value'] += (float)$order['total'];
            }

            // Calculate percentages
            foreach ($statusReport as $status => &$data) {
                $data['percentage'] = $totalOrders > 0 ? ($data['count'] / $totalOrders) * 100 : 0;
            }

            return [
                'status' => 'success',
                'data' => [
                    'total_orders' => $totalOrders,
                    'status_breakdown' => $statusReport
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to generate order status report: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Consume Order API to get orders data
     */
    private function consumeOrderAPI($filters = [])
    {
        // Use explicit .php endpoint for compatibility on environments without URL rewriting
        $url = $this->apiBaseUrl . '/orders.php';

        // Add query parameters
        if (!empty($filters)) {
            $url .= '?' . http_build_query($filters);
        }

        // Initialize cURL with simpler approach
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Further reduced timeout for faster fallback

        // Set headers
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        // Add session cookie if available
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $headers[] = 'Cookie: ' . $_SERVER['HTTP_COOKIE'];
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Execute request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Debug information
        error_log("API Debug - URL: {$url}, HTTP Code: {$httpCode}, Error: " . ($error ?: 'none'));

        if ($response === false || $error) {
            throw new Exception("API request failed: {$error}");
        }

        if ($httpCode !== 200) {
            throw new Exception("API request failed with HTTP code: {$httpCode}");
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response from API');
        }

        return $data;
    }

    /**
     * Get specific order details from API
     */
    public function getOrderDetailsFromAPI($orderId)
    {
        try {
            // Support .php endpoint and id via query parameter
            $url = $this->apiBaseUrl . '/orders.php?id=' . urlencode($orderId);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            // Set headers
            $headers = [
                'Content-Type: application/json',
                'Accept: application/json',
            ];

            // Add session cookie if available
            if (isset($_SERVER['HTTP_COOKIE'])) {
                $headers[] = 'Cookie: ' . $_SERVER['HTTP_COOKIE'];
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($response === false || $error) {
                throw new Exception("API request failed: {$error}");
            }

            if ($httpCode !== 200) {
                throw new Exception("API request failed with HTTP code: {$httpCode}");
            }

            $data = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response from API');
            }

            return $data;
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to fetch order details: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Fallback method to get orders directly from database if API fails
     */
    private function getOrdersFromDatabase()
    {
        try {
            // Check if orderModel is properly initialized
            if (!$this->orderModel) {
                throw new Exception('Order model not initialized');
            }

            // Try to get orders with error handling
            $orders = $this->orderModel->getAllOrdersWithFilters('', '', 1000, 0);

            if ($orders === false) {
                throw new Exception('Failed to retrieve orders from database');
            }

            return [
                'status' => 'success',
                'data' => [
                    'orders' => $orders,
                    'pagination' => [
                        'total' => count($orders),
                        'limit' => 1000,
                        'offset' => 0,
                        'has_more' => false
                    ]
                ]
            ];
        } catch (Exception $e) {
            error_log("Database fallback error: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database fallback failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate comprehensive business report
     */
    public function generateBusinessReport($startDate = null, $endDate = null)
    {
        try {
            // Get sales report with date filter
            $salesReport = $this->generateSalesReport($startDate, $endDate);
            if ($salesReport['status'] === 'error') {
                throw new Exception($salesReport['message']);
            }

            // Get order status report with date filter
            $statusReport = $this->generateOrderStatusReport($startDate, $endDate);
            if ($statusReport['status'] === 'error') {
                throw new Exception($statusReport['message']);
            }

            // Get product performance data
            $products = $this->productModel->getAll();
            $categories = $this->categoryModel->getAll();

            return [
                'status' => 'success',
                'data' => [
                    'sales_summary' => $salesReport['data'],
                    'order_status_summary' => $statusReport['data'],
                    'products_count' => count($products),
                    'categories_count' => count($categories),
                    'generated_at' => date('Y-m-d H:i:s'),
                    'date_range' => [
                        'start' => $startDate,
                        'end' => $endDate
                    ]
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to generate business report: ' . $e->getMessage()
            ];
        }
    }
}

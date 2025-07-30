<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/session.php';

class ReportController {
    private PDO $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    
    public function index() {
        // 只有manager可以查看报告
        if (Session::get('user_role') !== 'manager') {
            Session::set('report_errors', ['You do not have permission to access sales reports.']);
            header('Location: ../admin/dashboard.php');
            exit();
        }
        
        $dateRange = $_GET['range'] ?? 'today';
        $branchId = $_GET['branch'] ?? 'all';
        
        $salesData = $this->getSalesData($dateRange, $branchId);
        $topProducts = $this->getTopProducts($dateRange, $branchId);
        $salesByStatus = $this->getSalesByStatus($dateRange, $branchId);
        $salesByBranch = $this->getSalesByBranch($dateRange);
        $salesByType = $this->getSalesByType($dateRange, $branchId);
        $branches = $this->getAllBranches();
        
        $page_title = 'Sales Reports';
        require_once __DIR__ . '/../../views/admin/reports/index.php';
    }
    
    private function getSalesData($dateRange, $branchId) {
        $dateCondition = $this->getDateCondition($dateRange);
        $branchCondition = $branchId !== 'all' ? "AND o.branch_id = :branch_id" : "";
        
        $sql = "
            SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'Completed' THEN total ELSE 0 END) as total_revenue,
                SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed_orders,
                SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
                AVG(CASE WHEN status = 'Completed' THEN total ELSE NULL END) as avg_order_value
            FROM orders o 
            WHERE {$dateCondition} {$branchCondition}
        ";
        
        $stmt = $this->pdo->prepare($sql);
        if ($branchId !== 'all') {
            $stmt->bindParam(':branch_id', $branchId);
        }
        $this->bindDateParams($stmt, $dateRange);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    private function getTopProducts($dateRange, $branchId, $limit = 10) {
        $dateCondition = $this->getDateCondition($dateRange, 'o');
        $branchCondition = $branchId !== 'all' ? "AND o.branch_id = :branch_id" : "";
        
        $sql = "
            SELECT 
                p.name as product_name,
                SUM(od.quantity) as total_quantity,
                SUM(od.quantity * p.price) as total_revenue,
                COUNT(DISTINCT o.id) as order_count
            FROM order_details od
            JOIN products p ON od.product_id = p.id
            JOIN orders o ON od.order_id = o.id
            WHERE o.status = 'Completed' AND {$dateCondition} {$branchCondition}
            GROUP BY p.id, p.name
            ORDER BY total_quantity DESC
            LIMIT {$limit}
        ";
        
        $stmt = $this->pdo->prepare($sql);
        if ($branchId !== 'all') {
            $stmt->bindParam(':branch_id', $branchId);
        }
        $this->bindDateParams($stmt, $dateRange);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    private function getSalesByStatus($dateRange, $branchId) {
        $dateCondition = $this->getDateCondition($dateRange);
        $branchCondition = $branchId !== 'all' ? "AND o.branch_id = :branch_id" : "";
        
        $sql = "
            SELECT 
                status,
                COUNT(*) as count,
                SUM(total) as revenue
            FROM orders o
            WHERE {$dateCondition} {$branchCondition}
            GROUP BY status
            ORDER BY count DESC
        ";
        
        $stmt = $this->pdo->prepare($sql);
        if ($branchId !== 'all') {
            $stmt->bindParam(':branch_id', $branchId);
        }
        $this->bindDateParams($stmt, $dateRange);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    private function getSalesByBranch($dateRange) {
        $dateCondition = $this->getDateCondition($dateRange, 'o');
        
        $sql = "
            SELECT 
                b.name as branch_name,
                COUNT(o.id) as total_orders,
                SUM(CASE WHEN o.status = 'Completed' THEN o.total ELSE 0 END) as revenue,
                SUM(CASE WHEN o.status = 'Completed' THEN 1 ELSE 0 END) as completed_orders
            FROM branches b
            LEFT JOIN orders o ON b.id = o.branch_id AND {$dateCondition}
            GROUP BY b.id, b.name
            ORDER BY revenue DESC
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $this->bindDateParams($stmt, $dateRange);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    private function getSalesByType($dateRange, $branchId) {
        $dateCondition = $this->getDateCondition($dateRange);
        $branchCondition = $branchId !== 'all' ? "AND o.branch_id = :branch_id" : "";
        
        $sql = "
            SELECT 
                type,
                COUNT(*) as count,
                SUM(CASE WHEN status = 'Completed' THEN total ELSE 0 END) as revenue
            FROM orders o
            WHERE {$dateCondition} {$branchCondition}
            GROUP BY type
            ORDER BY revenue DESC
        ";
        
        $stmt = $this->pdo->prepare($sql);
        if ($branchId !== 'all') {
            $stmt->bindParam(':branch_id', $branchId);
        }
        $this->bindDateParams($stmt, $dateRange);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    private function getAllBranches() {
        $stmt = $this->pdo->prepare("SELECT id, name FROM branches ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    private function getDateCondition($dateRange, $tableAlias = 'o') {
        switch ($dateRange) {
            case 'today':
                return "DATE({$tableAlias}.created_at) = CURDATE()";
            case 'yesterday':
                return "DATE({$tableAlias}.created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            case 'week':
                return "{$tableAlias}.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            case 'month':
                return "{$tableAlias}.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            case 'year':
                return "{$tableAlias}.created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
            default:
                return "DATE({$tableAlias}.created_at) = CURDATE()";
        }
    }
    
    private function bindDateParams($stmt, $dateRange) {
        // This method is for consistency, but our current date conditions don't use parameters
        // If we need custom date ranges in the future, we can add parameter binding here
    }
} 
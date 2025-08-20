<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/LoggerTrait.php';

class Order
{
    use LoggerTrait;

    protected $conn;
    protected $pdo; // Required for LoggerTrait

    public function __construct()
    {
        $this->conn = Database::getInstance();
        $this->pdo = $this->conn; // LoggerTrait expects $pdo property
    }

    /**
     * Get available branches
     */
    public function getBranches()
    {
        $stmt = $this->conn->query("SELECT id, name FROM branches");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Calculate total from cart items
     */
    public function calculateSubtotal($cart)
    {
        $total = 0;
        $stmt = $this->conn->prepare("SELECT price FROM products WHERE id = ?");
        foreach ($cart as $item) {
            $stmt->execute([intval($item['id'])]);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $total += $row['price'] * $item['quantity'];
            }
        }
        return $total;
    }

    /**
     * Temporarily save order in session before confirming payment
     */
    public function savePendingOrder($data)
    {
        $_SESSION['pending_order'] = $data;

        // Log order confirmation
        $this->logEvent('INFO', 'ORDER_CONFIRM', [
            'user_id' => $data['user_id'],
            'order_type' => $data['type'],
            'amount' => $data['total'],
            'branch_id' => $data['branch_id'] ?? null,
            'delivery_fee' => $data['delivery_fee'] ?? 0
        ]);
    }

    /**
     * Save confirmed order to the database
     */
    public function createOrder(array $data)
    {
        // Validate required fields
        if (empty($data['user_id']) || empty($data['phone']) || !isset($data['type']) || !isset($data['total'])) {
            throw new Exception('Missing required order fields.');
        }

        // Fallback/default values
        $data['status']  = $data['status'] ?? 'Pending';

        // Ensure pickup orders have a valid address
        if ($data['type'] === 'pickup' && empty($data['address'])) {
            $data['address'] = null;
        } elseif ($data['type'] === 'delivery') {
            $data['branch_id'] = null; // delivery doesn't need branch
            if (empty($data['address'])) {
                throw new Exception('Address is required for delivery orders.');
            }
        }

        if (!isset($data['branch_id']) || $data['branch_id'] === '') {
            $data['branch_id'] = null;
        }

        $sql = "INSERT INTO orders (user_id, phone, address, delivery_fee, total, status, type, branch_id, created_at)
                VALUES (:user_id, :phone, :address, :delivery_fee, :total, :status, :type, :branch_id, NOW())";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':user_id', intval($data['user_id']), PDO::PARAM_INT);
        $stmt->bindValue(':phone', $data['phone'], PDO::PARAM_STR);
        if (is_null($data['address'])) {
            $stmt->bindValue(':address', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':address', $data['address'], PDO::PARAM_STR);
        }
        if (isset($data['delivery_fee'])) {
            $stmt->bindValue(':delivery_fee', (string)$data['delivery_fee'], PDO::PARAM_STR);
        } else {
            $stmt->bindValue(':delivery_fee', null, PDO::PARAM_NULL);
        }
        $stmt->bindValue(':total', (string)$data['total'], PDO::PARAM_STR);
        $stmt->bindValue(':status', $data['status'], PDO::PARAM_STR);
        $stmt->bindValue(':type', $data['type'], PDO::PARAM_STR);
        if (is_null($data['branch_id'])) {
            $stmt->bindValue(':branch_id', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':branch_id', intval($data['branch_id']), PDO::PARAM_INT);
        }

        if ($stmt->execute()) {
            $orderId = $this->conn->lastInsertId();

            // Log successful order creation
            $this->logEvent('INFO', 'ORDER_CREATE_SUCCESS', [
                'user_id' => $data['user_id'],
                'order_id' => $orderId,
                'amount' => $data['total'],
                'order_type' => $data['type'],
                'branch_id' => $data['branch_id'] ?? null,
                'delivery_fee' => $data['delivery_fee'] ?? 0,
                'status' => $data['status']
            ]);

            return $orderId;
        } else {
            $errorInfo = $stmt->errorInfo();

            // Log failed order creation
            $this->logEvent('CRITICAL', 'ORDER_CREATE_FAIL', [
                'user_id' => $data['user_id'],
                'error_message' => $errorInfo[2],
                'amount' => $data['total'],
                'order_type' => $data['type']
            ]);

            throw new Exception("Order insert failed: " . $errorInfo[2]);
        }
    }

    /**
     * Save ordered items into order_details table
     */
    public function addDetails($orderId, $cart)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO order_details (order_id, product_id, quantity, temperature, sugar)
            VALUES (?, ?, ?, ?, ?)
        ");

        $itemsAdded = 0;
        foreach ($cart as $item) {
            if ($stmt->execute([
                intval($orderId),
                intval($item['id']),
                intval($item['quantity']),
                $item['temperature'] ?? null,
                $item['sugar'] ?? null,
            ])) {
                $itemsAdded++;
            }
        }

        // Log order details addition
        $this->logEvent('INFO', 'ORDER_DETAILS_ADDED', [
            'order_id' => $orderId,
            'items_count' => count($cart),
            'items_added' => $itemsAdded
        ]);
    }

    public function find($id)
    {
        $stmt = $this->conn->prepare("
        SELECT o.*, b.name AS branch_name
        FROM orders o
        LEFT JOIN branches b ON o.branch_id = b.id
        WHERE o.id = ?
    ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get total count of orders
     */
    public function getCount()
    {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM orders");
        return $stmt->fetchColumn();
    }

    /**
     * Get recent orders with user information
     */
    public function getRecentOrders($limit = 5)
    {
        $stmt = $this->conn->prepare(
            "
            SELECT o.*, u.name as user_name, b.name as branch_name
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN branches b ON o.branch_id = b.id
            ORDER BY o.created_at DESC
            LIMIT " . intval($limit)
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get single order with related info (for API)
     */
    public function getOrderById(int $orderId)
    {
        $stmt = $this->conn->prepare(
            "
            SELECT o.*, u.name AS customer_name, u.email AS customer_email,
                   b.name AS branch_name
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN branches b ON o.branch_id = b.id
            WHERE o.id = ?
        "
        );
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * List orders with optional search/status filters and pagination (for API)
     */
    public function getAllOrdersWithFilters(string $search = '', string $status = '', int $limit = 50, int $offset = 0): array
    {
        $conditions = [];
        $params = [];

        if ($status !== '') {
            $conditions[] = 'o.status = ?';
            $params[] = $status;
        }

        if ($search !== '') {
            $conditions[] = '(u.name LIKE ? OR u.email LIKE ? OR CAST(o.id AS CHAR) LIKE ?)';
            $like = "%{$search}%";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $whereSql = '';
        if (count($conditions) > 0) {
            $whereSql = 'WHERE ' . implode(' AND ', $conditions);
        }

        $sql = "
            SELECT o.*, u.name AS customer_name, u.email AS customer_email, b.name AS branch_name
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN branches b ON o.branch_id = b.id
            {$whereSql}
            ORDER BY o.created_at DESC
            LIMIT " . intval($limit) . " OFFSET " . intval($offset);

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Total count for the same filters (for API pagination)
     */
    public function getOrdersCount(string $search = '', string $status = ''): int
    {
        $conditions = [];
        $params = [];

        if ($status !== '') {
            $conditions[] = 'o.status = ?';
            $params[] = $status;
        }

        if ($search !== '') {
            $conditions[] = '(u.name LIKE ? OR u.email LIKE ? OR CAST(o.id AS CHAR) LIKE ?)';
            $like = "%{$search}%";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $whereSql = '';
        if (count($conditions) > 0) {
            $whereSql = 'WHERE ' . implode(' AND ', $conditions);
        }

        $sql = "
            SELECT COUNT(*)
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            {$whereSql}
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Update an order status (for API)
     */
    public function updateOrderStatus(int $orderId, string $newStatus): bool
    {
        $stmt = $this->conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$newStatus, $orderId]);
    }
}

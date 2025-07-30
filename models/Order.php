<?php
require_once __DIR__ . '/../includes/db.php';

class Order
{
    protected $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance();
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
            return $this->conn->lastInsertId();
        } else {
            $errorInfo = $stmt->errorInfo();
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

        foreach ($cart as $item) {
            $stmt->execute([
                intval($orderId),
                intval($item['id']),
                intval($item['quantity']),
                $item['temperature'] ?? null,
                $item['sugar'] ?? null,
            ]);
        }
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
        $stmt = $this->conn->prepare("
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
}

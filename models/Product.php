<?php
require_once __DIR__ . '/LoggerTrait.php'; // Include the new trait
require_once __DIR__ . '/../includes/db.php'; // Include database connection

class Product {
    use LoggerTrait; // Use the trait here
    private $pdo;
    private $uploads_dir = __DIR__ . '/../admin/uploads/';

    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    
    // ... getAll(), findById(), getCount() methods remain unchanged ...
    public function getAll() {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                ORDER BY p.id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getCount() {
        return $this->pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    }


    public function create($data, $file) {
        $image_name = $this->handleUpload($file);
        if ($image_name === false) {
            return false;
        }

        $sql = "INSERT INTO products (name, description, price, category_id, is_available, image) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([
            $data['name'], $data['description'], $data['price'],
            $data['category_id'], $data['is_available'], $image_name
        ]);

        if ($success) {
            $newId = $this->pdo->lastInsertId();
            // LOG THE EVENT
            $this->logEvent('INFO', 'PRODUCT_CREATE', [
                'product_id' => $newId,
                'name' => $data['name'],
                'price' => $data['price']
            ]);
            
            // Send notification to users about new product
            $this->sendProductNotification($data);
        }
        return $success;
    }
    
    /**
     * Send notification about new product
     */
    private function sendProductNotification($data): void
    {
        try {
            // Include all necessary notification files
            require_once __DIR__ . '/../app/Notification/NotificationSubject.php';
            require_once __DIR__ . '/../app/Notification/NotificationObserver.php';
            require_once __DIR__ . '/../app/Notification/NotificationManager.php';
            require_once __DIR__ . '/../app/Notification/EmailNotificationObserver.php';
            require_once __DIR__ . '/../app/Notification/NotificationBootstrap.php';
            
            // Initialize notification system
            \App\Notification\NotificationBootstrap::initialize();
            
            $notificationManager = \App\Notification\NotificationBootstrap::getNotificationManager();
            $notificationManager->notify('product_created', $data);
        } catch (\Exception $e) {
            error_log("Failed to send product notification: " . $e->getMessage());
        }
    }

    public function update($id, $data, $file) {
        $product = $this->findById($id);
        $image_name = $product->image;

        if (isset($file) && $file['error'] == 0) {
            // ... (image handling code remains the same) ...
            $new_image = $this->handleUpload($file);
            if ($new_image) {
                if ($image_name !== 'default.jpg' && file_exists($this->uploads_dir . $image_name)) {
                    unlink($this->uploads_dir . $image_name);
                }
                $image_name = $new_image;
            }
        }

        $sql = "UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, is_available = ?, image = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([
            $data['name'], $data['description'], $data['price'],
            $data['category_id'], $data['is_available'], $image_name, $id
        ]);

        if ($success) {
            // LOG THE EVENT
            $this->logEvent('INFO', 'PRODUCT_UPDATE', [
                'product_id' => $id,
                'updated_name' => $data['name']
            ]);
        }
        return $success;
    }

    public function delete($id) {
        $product = $this->findById($id);
        if (!$product) return false;

        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([$id]);

        if ($success) {
            // LOG THE EVENT
            $this->logEvent('INFO', 'PRODUCT_DELETE', [
                'product_id' => $id,
                'deleted_name' => $product->name,
                'deleted_image' => $product->image
            ]);
            if ($product->image !== 'default.jpg' && file_exists($this->uploads_dir . $product->image)) {
                unlink($this->uploads_dir . $product->image);
            }
        }
        return $success;
    }

    private function handleUpload($file) {
        if (!is_dir($this->uploads_dir)) {
            mkdir($this->uploads_dir, 0755, true);
        }
        if (isset($file) && $file['error'] == 0) {
            $safe_filename = time() . '_' . basename($file["name"]);
            $target_file = $this->uploads_dir . $safe_filename;
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                return $safe_filename;
            }
        }
        // 对于新产品或上传失败的情况，返回默认图片
        return 'default.jpg';
    }

    // Fongyee added
    public static function getAvailableWithCategory() {
        $conn = Database::getInstance();
        $stmt = $conn->query("
            SELECT p.*, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_available = 1 AND (c.is_active = 1 OR c.is_active IS NULL)
            ORDER BY c.name, p.name
        ");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get all products with search and filter functionality
     */
    public function getAllWithFilters($search = '', $category_filter = '', $status_filter = '') {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE 1=1";
        
        $params = [];
        
        // Search filter
        if (!empty($search)) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        // Category filter
        if (!empty($category_filter)) {
            $sql .= " AND p.category_id = ?";
            $params[] = $category_filter;
        }
        
        // Status filter
        if ($status_filter !== '') {
            $sql .= " AND p.is_available = ?";
            $params[] = $status_filter;
        }
        
        $sql .= " ORDER BY p.id DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
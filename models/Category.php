<?php
require_once __DIR__ . '/LoggerTrait.php'; // Include the new trait
require_once __DIR__ . '/../includes/db.php'; // Include database connection

class Category {
    use LoggerTrait; // Use the trait here
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    
    // ... getAll(), findById(), getCount() methods remain unchanged ...
    public function getAll() {
        return $this->pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
    }
    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public function getCount() {
        return $this->pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    }

    public function create($name) {
        $stmt = $this->pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $success = $stmt->execute([$name]);
        if ($success) {
            // LOG THE EVENT
            $this->logEvent('INFO', 'CATEGORY_CREATE', [
                'category_id' => $this->pdo->lastInsertId(),
                'name' => $name
            ]);
        }
        return $success;
    }

    public function update($id, $name) {
        $stmt = $this->pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $success = $stmt->execute([$name, $id]);
        if ($success) {
            // LOG THE EVENT
            $this->logEvent('INFO', 'CATEGORY_UPDATE', [
                'category_id' => $id,
                'updated_name' => $name
            ]);
        }
        return $success;
    }

    public function delete($id) {
        $category = $this->findById($id);
        if (!$category) return false;

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            return false;
        }

        $delete_stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
        $success = $delete_stmt->execute([$id]);
        if ($success) {
            // LOG THE EVENT
            $this->logEvent('INFO', 'CATEGORY_DELETE', [
                'category_id' => $id,
                'deleted_name' => $category->name
            ]);
        }
        return $success;
    }
}
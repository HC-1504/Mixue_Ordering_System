<?php
require_once __DIR__ . '/LoggerTrait.php';
require_once __DIR__ . '/../includes/db.php';

class Branch {
    use LoggerTrait;
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function getAllBranches() { // Renamed from getAll for clarity with controller
        return $this->pdo->query("SELECT id, name FROM branches ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM branches WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Ensure consistent fetch type
    }
    public function getCount() {
        return $this->pdo->query("SELECT COUNT(*) FROM branches")->fetchColumn();
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO branches (name, address, phone) VALUES (?, ?, ?)");
        $success = $stmt->execute([
            $data['name'],
            $data['address'],
            $data['phone']
        ]);
        if ($success) {
            $this->logEvent('INFO', 'BRANCH_CREATE', [
                'branch_id' => $this->pdo->lastInsertId(),
                'name' => $data['name']
            ]);
            $this->sendBranchNotification($data);
        }
        return $success;
    }
    
    private function sendBranchNotification($data): void
    {
        try {
            require_once __DIR__ . '/../app/Notification/NotificationSubject.php';
            require_once __DIR__ . '/../app/Notification/NotificationObserver.php';
            require_once __DIR__ . '/../app/Notification/NotificationManager.php';
            require_once __DIR__ . '/../app/Notification/EmailNotificationObserver.php';
            require_once __DIR__ . '/../app/Notification/NotificationBootstrap.php';
            
            \App\Notification\NotificationBootstrap::initialize();
            
            $notificationManager = \App\Notification\NotificationBootstrap::getNotificationManager();
            $notificationManager->notify('branch_created', $data);
        } catch (\Exception $e) {
            error_log("Failed to send branch notification: " . $e->getMessage());
        }
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE branches SET name = ?, address = ?, phone = ? WHERE id = ?");
        $success = $stmt->execute([
            $data['name'],
            $data['address'],
            $data['phone'],
            $id
        ]);
        if ($success) {
            $this->logEvent('INFO', 'BRANCH_UPDATE', [
                'branch_id' => $id,
                'updated_name' => $data['name']
            ]);
        }
        return $success;
    }

    public function delete($id) {
        $branch = $this->findById($id);
        if (!$branch) return false;

        $stmt = $this->pdo->prepare("DELETE FROM branches WHERE id = ?");
        $success = $stmt->execute([$id]);
        if ($success) {
            $this->logEvent('INFO', 'BRANCH_DELETE', [
                'branch_id' => $id,
                'deleted_name' => $branch['name'] // Access as array
            ]);
        }
        return $success;
    }
}
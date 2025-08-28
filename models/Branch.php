<?php
require_once __DIR__ . '/LoggerTrait.php'; // Include the new trait
require_once __DIR__ . '/../includes/db.php'; // Include database connection

class Branch
{
    use LoggerTrait; // Use the trait here
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    // ... getAll(), findById(), getCount() methods remain unchanged ...
    public function getAll()
    {
        return $this->pdo->query("SELECT * FROM branches ORDER BY name ASC")->fetchAll();
    }
    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM branches WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public function getCount()
    {
        return $this->pdo->query("SELECT COUNT(*) FROM branches")->fetchColumn();
    }


    public function create($data)
    {
        $stmt = $this->pdo->prepare("INSERT INTO branches (name, address, phone) VALUES (?, ?, ?)");
        $success = $stmt->execute([
            $data['name'],
            $data['address'],
            $data['phone']
        ]);
        if ($success) {
            // LOG THE EVENT
            $this->logEvent('INFO', 'BRANCH_CREATE', [
                'branch_id' => $this->pdo->lastInsertId(),
                'name' => $data['name']
            ]);

            // Send notification to users about new branch
            $this->sendBranchNotification($data);
        }
        return $success;
    }

    /**
     * Send notification about new branch
     */
    private function sendBranchNotification($data): void
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
            $notificationManager->notify('branch_created', $data);
        } catch (\Exception $e) {
            error_log("Failed to send branch notification: " . $e->getMessage());
        }
    }

    public function update($id, $data)
    {
        $stmt = $this->pdo->prepare("UPDATE branches SET name = ?, address = ?, phone = ? WHERE id = ?");
        $success = $stmt->execute([
            $data['name'],
            $data['address'],
            $data['phone'],
            $id
        ]);
        if ($success) {
            // LOG THE EVENT
            $this->logEvent('INFO', 'BRANCH_UPDATE', [
                'branch_id' => $id,
                'updated_name' => $data['name']
            ]);
        }
        return $success;
    }

    public function delete($id)
    {
        $branch = $this->findById($id);
        if (!$branch) return false;

        $stmt = $this->pdo->prepare("DELETE FROM branches WHERE id = ?");
        $success = $stmt->execute([$id]);
        if ($success) {
            // LOG THE EVENT
            $this->logEvent('INFO', 'BRANCH_DELETE', [
                'branch_id' => $id,
                'deleted_name' => $branch->name
            ]);
        }
        return $success;
    }

    /**
     * Find the nearest branch to a given set of coordinates.
     *
     * @param float $lat User's latitude
     * @param float $lon User's longitude
     * @return array|null The nearest branch data or null if no branches are available.
     */
    public function findNearestBranch(float $lat, float $lon): ?array
    {
        // Fetch all branches that have coordinates
        $stmt = $this->pdo->query("SELECT * FROM branches WHERE latitude IS NOT NULL AND longitude IS NOT NULL");
        $branches = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($branches)) {
            return null;
        }

        $nearestBranch = null;
        $shortestDistance = -1;

        foreach ($branches as $branch) {
            $branchLat = $branch['latitude'];
            $branchLon = $branch['longitude'];

            // Haversine formula to calculate distance in kilometers
            $earthRadius = 6371; // Radius of the earth in km
            $latFrom = deg2rad($lat);
            $lonFrom = deg2rad($lon);
            $latTo = deg2rad($branchLat);
            $lonTo = deg2rad($branchLon);

            $latDelta = $latTo - $latFrom;
            $lonDelta = $lonTo - $lonFrom;

            $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

            $distance = $angle * $earthRadius;

            if ($shortestDistance == -1 || $distance < $shortestDistance) {
                $shortestDistance = $distance;
                $nearestBranch = $branch;
                $nearestBranch['distance_km'] = $shortestDistance; // Add distance to the result array
            }
        }

        return $nearestBranch;
    }
}

<?php

namespace App\Notification;

/**
 * Notification Manager
 * Concrete implementation of NotificationSubject interface
 * Manages observers and notifies them of events
 */
class NotificationManager implements NotificationSubject
{
    /**
     * @var NotificationObserver[]
     */
    private array $observers = [];
    
    /**
     * Singleton instance
     */
    private static ?NotificationManager $instance = null;
    
    /**
     * Private constructor for singleton pattern
     */
    private function __construct() {}
    
    /**
     * Get singleton instance
     */
    public static function getInstance(): NotificationManager
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Attach an observer to this subject
     */
    public function attach(NotificationObserver $observer): void
    {
        $this->observers[] = $observer;
    }
    
    /**
     * Detach an observer from this subject
     */
    public function detach(NotificationObserver $observer): void
    {
        $key = array_search($observer, $this->observers, true);
        if ($key !== false) {
            unset($this->observers[$key]);
            $this->observers = array_values($this->observers);
        }
    }
    
    /**
     * Notify all attached observers
     */
    public function notify(string $event, array $data): void
    {
        foreach ($this->observers as $observer) {
            try {
                $observer->update($event, $data);
            } catch (\Exception $e) {
                // Log error but don't stop other observers
                error_log("Observer notification failed: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Get count of attached observers
     */
    public function getObserverCount(): int
    {
        return count($this->observers);
    }
} 
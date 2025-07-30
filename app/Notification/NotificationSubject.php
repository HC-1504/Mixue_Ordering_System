<?php

namespace App\Notification;

/**
 * Notification Subject Interface
 * Defines the contract for subjects that can notify observers
 */
interface NotificationSubject
{
    /**
     * Attach an observer to this subject
     */
    public function attach(NotificationObserver $observer): void;
    
    /**
     * Detach an observer from this subject
     */
    public function detach(NotificationObserver $observer): void;
    
    /**
     * Notify all attached observers
     */
    public function notify(string $event, array $data): void;
} 
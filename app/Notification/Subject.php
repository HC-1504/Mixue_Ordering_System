<?php

namespace App\Notification;

/**
 * Subject interface for notification system
 */
interface Subject
{
    /**
     * Attach an observer to this subject
     * 
     * @param Observer $observer
     * @return void
     */
    public function attach(Observer $observer): void;
    
    /**
     * Detach an observer from this subject
     * 
     * @param Observer $observer
     * @return void
     */
    public function detach(Observer $observer): void;
    
    /**
     * Notify all attached observers
     * 
     * @param string $eventType
     * @param array $data
     * @return void
     */
    public function notify(string $eventType, array $data): void;
} 
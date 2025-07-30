<?php

namespace App\Notification;

/**
 * Observer interface for notification system
 */
interface Observer
{
    /**
     * Update method called when subject notifies observers
     * 
     * @param string $eventType The type of event (e.g., 'product_created', 'branch_created')
     * @param array $data The data associated with the event
     * @return void
     */
    public function update(string $eventType, array $data): void;
} 
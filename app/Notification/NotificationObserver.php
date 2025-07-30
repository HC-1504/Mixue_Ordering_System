<?php

namespace App\Notification;

/**
 * Notification Observer Interface
 * Defines the contract for observers that can receive notifications
 */
interface NotificationObserver
{
    /**
     * Update method called when subject notifies observers
     * 
     * @param string $event The event type (e.g., 'product_created', 'branch_created')
     * @param array $data The event data
     */
    public function update(string $event, array $data): void;
} 
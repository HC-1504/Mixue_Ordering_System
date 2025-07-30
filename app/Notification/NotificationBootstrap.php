<?php

namespace App\Notification;

/**
 * Notification Bootstrap
 * Initializes the notification system with observers
 */
class NotificationBootstrap
{
    /**
     * Initialize the notification system
     * This should be called once at application startup
     */
    public static function initialize(): void
    {
        $notificationManager = NotificationManager::getInstance();
        
        // Attach email notification observer
        $emailObserver = new EmailNotificationObserver();
        $notificationManager->attach($emailObserver);
        
        // You can add more observers here in the future
        // For example: SMS notification, push notification, etc.
    }
    
    /**
     * Get the notification manager instance
     */
    public static function getNotificationManager(): NotificationManager
    {
        return NotificationManager::getInstance();
    }
} 
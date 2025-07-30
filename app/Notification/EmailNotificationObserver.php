<?php

namespace App\Notification;

// Include autoloader for PHPMailer
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Email Notification Observer
 * Sends email notifications to users when products or branches are created
 */
class EmailNotificationObserver implements NotificationObserver
{
    private $pdo;
    
    // SMTP Configuration (same as auth.php)
    const SMTP_HOST = 'smtp.gmail.com';
    const SMTP_USER = 'testinnnnggg@gmail.com';
    const SMTP_PASS = 'lluajtwcvqzhmzkn';
    const SMTP_PORT = 587;
    
    public function __construct() {
        $this->pdo = \Database::getInstance();
    }
    
    /**
     * Update method called when subject notifies observers
     */
    public function update(string $event, array $data): void
    {
        switch ($event) {
            case 'product_created':
                $this->sendProductNotification($data);
                break;
            case 'branch_created':
                $this->sendBranchNotification($data);
                break;
            default:
                // Unknown event, ignore
                break;
        }
    }
    
    /**
     * Send notification email for new product
     */
    private function sendProductNotification(array $data): void
    {
        $users = $this->getAllUsers();
        
        foreach ($users as $user) {
            $this->sendEmail(
                $user->email,
                $user->name,
                'New Product Available - Mixue System',
                $this->getProductEmailTemplate($data, $user->name)
            );
        }
    }
    
    /**
     * Send notification email for new branch
     */
    private function sendBranchNotification(array $data): void
    {
        $users = $this->getAllUsers();
        
        foreach ($users as $user) {
            $this->sendEmail(
                $user->email,
                $user->name,
                'New Branch Opening - Mixue System',
                $this->getBranchEmailTemplate($data, $user->name)
            );
        }
    }
    
    /**
     * Get all users from database
     */
    private function getAllUsers(): array
    {
        $stmt = $this->pdo->prepare("SELECT id, name, email FROM users WHERE role = 'user'");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Send email using PHPMailer
     */
    private function sendEmail(string $toEmail, string $toName, string $subject, string $htmlBody): bool
    {
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = self::SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = self::SMTP_USER;
            $mail->Password   = self::SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = self::SMTP_PORT;
            $mail->SMTPDebug  = 0;
            
            // Recipients
            $mail->setFrom(self::SMTP_USER, 'Mixue System');
            $mail->addAddress($toEmail, $toName);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            
            // Plain text version
            $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $htmlBody));
            
            $mail->send();
            $this->logEmailSent($toEmail, $subject);
            return true;
            
        } catch (Exception $e) {
            error_log("Email notification failed: " . $mail->ErrorInfo);
            return false;
        }
    }
    
    /**
     * Get HTML email template for product notification
     */
    private function getProductEmailTemplate(array $data, string $userName): string
    {
        $productName = htmlspecialchars($data['name'] ?? 'New Product');
        $productPrice = number_format($data['price'] ?? 0, 2);
        $productDescription = htmlspecialchars($data['description'] ?? '');
        
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='text-align: center; margin-bottom: 30px;'>
                <h1 style='color: #e74c3c; margin: 0;'>🍦 Mixue System</h1>
                <p style='color: #7f8c8d; margin: 10px 0 0 0;'>Delicious treats await you!</p>
            </div>
            
            <div style='background-color: #f8f9fa; padding: 25px; border-radius: 10px; margin-bottom: 25px;'>
                <h2 style='color: #2c3e50; margin-top: 0;'>🎉 New Product Alert!</h2>
                <p style='color: #34495e; line-height: 1.6;'>Hello " . htmlspecialchars($userName) . ",</p>
                <p style='color: #34495e; line-height: 1.6;'>We're excited to announce a new addition to our menu!</p>
            </div>
            
            <div style='background-color: #ffffff; border: 2px solid #e74c3c; border-radius: 10px; padding: 25px; margin-bottom: 25px;'>
                <h3 style='color: #e74c3c; margin-top: 0;'>🍨 " . $productName . "</h3>
                <p style='color: #34495e; font-size: 18px; font-weight: bold; margin: 15px 0;'>
                    Price: RM " . $productPrice . "
                </p>
                <p style='color: #7f8c8d; line-height: 1.6; margin-bottom: 20px;'>
                    " . $productDescription . "
                </p>
                <div style='text-align: center;'>
                    <a href='" . $this->getMenuUrl() . "' style='display: inline-block; padding: 12px 30px; background-color: #e74c3c; color: white; text-decoration: none; border-radius: 25px; font-weight: bold;'>
                        View Menu
                    </a>
                </div>
            </div>
            
            <div style='text-align: center; color: #7f8c8d; font-size: 14px;'>
                <p>Thank you for being a valued customer!</p>
                <p>The Mixue Team</p>
            </div>
        </div>";
    }
    
    /**
     * Get HTML email template for branch notification
     */
    private function getBranchEmailTemplate(array $data, string $userName): string
    {
        $branchName = htmlspecialchars($data['name'] ?? 'New Branch');
        $branchAddress = htmlspecialchars($data['address'] ?? '');
        $branchPhone = htmlspecialchars($data['phone'] ?? '');
        
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='text-align: center; margin-bottom: 30px;'>
                <h1 style='color: #e74c3c; margin: 0;'>🍦 Mixue System</h1>
                <p style='color: #7f8c8d; margin: 10px 0 0 0;'>More locations to serve you!</p>
            </div>
            
            <div style='background-color: #f8f9fa; padding: 25px; border-radius: 10px; margin-bottom: 25px;'>
                <h2 style='color: #2c3e50; margin-top: 0;'>🏪 New Branch Opening!</h2>
                <p style='color: #34495e; line-height: 1.6;'>Hello " . htmlspecialchars($userName) . ",</p>
                <p style='color: #34495e; line-height: 1.6;'>Great news! We've opened a new branch to serve you better!</p>
            </div>
            
            <div style='background-color: #ffffff; border: 2px solid #e74c3c; border-radius: 10px; padding: 25px; margin-bottom: 25px;'>
                <h3 style='color: #e74c3c; margin-top: 0;'>📍 " . $branchName . "</h3>
                <div style='margin: 20px 0;'>
                    <p style='color: #34495e; margin: 10px 0;'>
                        <strong>Address:</strong><br>
                        " . $branchAddress . "
                    </p>
                    <p style='color: #34495e; margin: 10px 0;'>
                        <strong>Phone:</strong><br>
                        " . $branchPhone . "
                    </p>
                </div>
                <div style='text-align: center;'>
                    <a href='" . $this->getLocationsUrl() . "' style='display: inline-block; padding: 12px 30px; background-color: #e74c3c; color: white; text-decoration: none; border-radius: 25px; font-weight: bold;'>
                        View All Locations
                    </a>
                </div>
            </div>
            
            <div style='text-align: center; color: #7f8c8d; font-size: 14px;'>
                <p>We look forward to serving you at our new location!</p>
                <p>The Mixue Team</p>
            </div>
        </div>";
    }
    
    /**
     * Get menu URL
     */
    private function getMenuUrl(): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $baseUrl = defined('BASE_URL') ? BASE_URL : '/Assignment';
        return "http://" . $host . $baseUrl . "/routes/menu.php";
    }
    
    /**
     * Get locations URL
     */
    private function getLocationsUrl(): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $baseUrl = defined('BASE_URL') ? BASE_URL : '/Assignment';
        return "http://" . $host . $baseUrl . "/views/locations.php";
    }
    
    /**
     * Log email sent for tracking
     */
    private function logEmailSent(string $email, string $subject): void
    {
        try {
            $sql = "INSERT INTO notification_logs (email, subject, sent_at) VALUES (?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email, $subject]);
        } catch (\Exception $e) {
            error_log("Failed to log email notification: " . $e->getMessage());
        }
    }
} 
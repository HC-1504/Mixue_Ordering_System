# 🧊 Mixue Ordering System

A comprehensive web-based ordering system for Mixue Ice Cream & Tea, built with PHP and modern design patterns. This system provides a complete solution for managing orders, products, users, and administrative functions.

![Mixue Logo](assets/images/mixue-logo.png)

## 📋 Table of Contents

- [Features](#-features)
- [Architecture & Design Patterns](#-architecture--design-patterns)
- [Technology Stack](#-technology-stack)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [API Endpoints](#-api-endpoints)
- [Database Schema](#-database-schema)
- [Security Features](#-security-features)
- [Contributing](#-contributing)
- [License](#-license)

## ✨ Features

### 🛒 Customer Features
- **User Registration & Authentication** - Secure account creation and login
- **Product Browsing** - Browse menu items with categories and detailed information
- **Shopping Cart** - Add/remove items, modify quantities
- **Order Management** - Place orders, view order history
- **Payment Processing** - Secure payment handling
- **Account Management** - Profile updates, password changes
- **Balance Reload** - Top up account balance for purchases
- **Order Tracking** - Real-time order status updates

### 👨‍💼 Admin Features
- **Dashboard** - Comprehensive overview of system metrics
- **Product Management** - CRUD operations for menu items
- **Category Management** - Organize products into categories
- **Order Management** - View, update, and track all orders
- **User Management** - Manage customer accounts and roles
- **Branch Management** - Manage store locations
- **Sales Reports** - Detailed analytics and reporting
- **Security Monitoring** - Login attempt tracking and account lockouts

### 🔐 Security Features
- **Account Lockout Protection** - Automatic lockout after failed login attempts
- **Session Management** - Secure session handling with regeneration
- **CSRF Protection** - Cross-site request forgery prevention
- **Password Security** - Complex password requirements and history tracking
- **Security Logging** - Comprehensive audit trail
- **Email Notifications** - Password reset and security alerts

## 🏗️ Architecture & Design Patterns

This project implements several advanced design patterns for maintainability and scalability:

### 🎭 Decorator Pattern
**Location**: `app/Auth/`
- **DatabaseAuthenticator** - Core authentication logic
- **AccountLockoutDecorator** - Adds security features (lockout, failed attempts)
- **SessionLoginDecorator** - Adds session management

### 👁️ Observer Pattern  
**Location**: `app/Notification/`
- **NotificationManager** - Subject that manages observers
- **EmailNotificationObserver** - Sends email notifications for events
- **Event-driven notifications** for product/branch updates

### 🎯 Strategy Pattern
**Location**: `app/Strategies/`
- **DeliveryStrategy** - Interface for delivery methods
- **SelfPickup** - Self-pickup delivery strategy
- **GrabDelivery** - Third-party delivery integration

### 🏭 Factory Pattern
**Location**: `controllers/auth.php`
- Centralized service creation and dependency injection
- Clean separation of concerns

## 💻 Technology Stack

- **Backend**: PHP 8.0+
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Email**: PHPMailer
- **Architecture**: MVC Pattern
- **Dependency Management**: Composer
- **Session Management**: PHP Sessions
- **Security**: Custom authentication system with decorators

## 🚀 Installation

### Prerequisites
- PHP 8.0 or higher
- MySQL/MariaDB
- Apache/Nginx web server
- Composer

### Quick Start
1. Clone the repository
2. Import `mixue_db.sql` into your MySQL database
3. Update database configuration in `includes/db.php`
4. Access via `http://localhost/Assignment/`

### Step 1: Clone the Repository
```bash
git clone https://github.com/HC-1504/Mixue_Ordering_System.git
cd Mixue_Ordering_System
```

### Step 2: Install Dependencies
```bash
composer install
```

### Step 3: Database Setup
1. Create a MySQL database named `mixue_db`
2. Import the provided database schema:
   ```bash
   mysql -u your_username -p mixue_db < mixue_db.sql
   ```
   Or using phpMyAdmin:
   - Open phpMyAdmin
   - Create database `mixue_db`
   - Import the `mixue_db.sql` file
3. Update database configuration in `includes/db.php`

### Step 4: Configuration
1. Update `includes/config.php` with your base URL
2. Configure email settings in `controllers/auth.php`
3. Set up proper file permissions

### Step 5: Verify Database Setup
After importing `mixue_db.sql`, verify that all tables are created:
- Check that the database contains all required tables
- Verify sample data is loaded (optional)
- Test database connection from the application

### Step 6: Web Server Setup
- **Apache**: Place in `htdocs/Assignment/`
- **Nginx**: Configure virtual host pointing to project directory

## ⚙️ Configuration

### Database Configuration
Update `includes/db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'mixue_db');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### Email Configuration
Update `controllers/auth.php`:
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'your_email@gmail.com');
define('SMTP_PASS', 'your_app_password');
define('SMTP_PORT', 587);
```

### Base URL Configuration
Update `includes/config.php`:
```php
define('BASE_URL', '/your_project_path');
```

## 📖 Usage

### Customer Workflow
1. **Registration**: Create account at `/views/login_logout_modules/register.php`
2. **Login**: Access system at `/views/login_logout_modules/login.php`
3. **Browse Menu**: View products at `/routes/menu.php`
4. **Add to Cart**: Use cart functionality at `/routes/cart.php`
5. **Checkout**: Complete order at `/routes/order.php`
6. **Payment**: Process payment at `/routes/payment.php`

### Admin Workflow
1. **Login**: Access admin panel at `/admin/dashboard.php`
2. **Manage Products**: CRUD operations at `/admin/products.php`
3. **View Orders**: Monitor orders at `/admin/orders.php`
4. **Generate Reports**: Analytics at `/admin/reports.php`
5. **User Management**: Manage users at `/admin/users.php`

## 🛡️ Security Features

### Authentication Security
- **Account Lockout**: 5 failed attempts = 15-minute lockout
- **Password Complexity**: Minimum 8 characters with mixed case, numbers, symbols
- **Password History**: Prevents reuse of last 5 passwords
- **Session Security**: Automatic session regeneration

### Logging & Monitoring
- **Security Events**: All login attempts, failures, and lockouts
- **Audit Trail**: Complete user action logging
- **Email Alerts**: Notifications for security events

## 📊 Database Schema

The complete database schema is provided in `mixue_db.sql` file. This includes all tables, relationships, and sample data.

### Core Tables
- `users` - User accounts and authentication
- `products` - Menu items and product information
- `categories` - Product categorization
- `orders` - Customer orders
- `order_details` - Order line items
- `branches` - Store locations
- `reloads` - Account balance transactions
- `security_logs` - Security event logging
- `password_resets` - Password reset tokens
- `password_history` - Password change history

### Database Import Instructions

#### Method 1: Command Line
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE mixue_db;"

# Import schema and data
mysql -u root -p mixue_db < mixue_db.sql
```

#### Method 2: phpMyAdmin
1. Open phpMyAdmin in your browser
2. Click "New" to create a database
3. Name it `mixue_db` and click "Create"
4. Select the `mixue_db` database
5. Click "Import" tab
6. Choose the `mixue_db.sql` file
7. Click "Go" to import

#### Method 3: MySQL Workbench
1. Open MySQL Workbench
2. Connect to your MySQL server
3. Go to Server → Data Import
4. Select "Import from Self-Contained File"
5. Browse and select `mixue_db.sql`
6. Create new schema `mixue_db` or select existing
7. Click "Start Import"

## 📁 Project Structure

```
Mixue_Ordering_System/
├── � mixue_db.sql                  # Database schema and sample data
├── �📂 app/                          # Application core classes
│   ├── 📂 Auth/                     # Authentication decorators
│   │   ├── AuthenticatorInterface.php
│   │   ├── DatabaseAuthenticator.php
│   │   ├── AccountLockoutDecorator.php
│   │   └── SessionLoginDecorator.php
│   ├── 📂 Notification/             # Observer pattern implementation
│   │   ├── NotificationManager.php
│   │   ├── EmailNotificationObserver.php
│   │   └── Observer.php
│   ├── 📂 Strategies/               # Strategy pattern for delivery
│   │   ├── DeliveryStrategy.php
│   │   ├── SelfPickup.php
│   │   └── GrabDelivery.php
│   ├── AuthService.php              # Main authentication service
│   └── SecurityLogger.php           # Security event logging
├── 📂 admin/                        # Admin panel entry points
├── 📂 controllers/                  # MVC Controllers
│   ├── 📂 admin/                    # Admin-specific controllers
│   └── auth.php                     # Service factory
├── 📂 models/                       # Data models
├── 📂 views/                        # View templates
│   ├── 📂 admin/                    # Admin views
│   ├── 📂 login_logout_modules/     # Authentication views
│   └── 📂 order/                    # Order-related views
├── 📂 routes/                       # Route handlers
├── 📂 includes/                     # Shared includes
├── 📂 assets/                       # Static assets (CSS, JS, images)
└── 📂 vendor/                       # Composer dependencies
```

## 🔧 Development Setup

### Local Development with XAMPP
1. Install XAMPP
2. Clone repository to `C:/xampp/htdocs/Assignment/`
3. Start Apache and MySQL services
4. Access via `http://localhost/Assignment/`

### Environment Variables
Create a `.env` file (optional) or configure directly in files:
```env
DB_HOST=localhost
DB_NAME=mixue_db
DB_USER=root
DB_PASS=

SMTP_HOST=smtp.gmail.com
SMTP_USER=your_email@gmail.com
SMTP_PASS=your_app_password
SMTP_PORT=587

BASE_URL=/Assignment
```



### Performance Optimization
- Enable PHP OPcache for production
- Optimize database queries with proper indexing
- Use CDN for static assets
- Implement caching for frequently accessed data

## 🙏 Acknowledgments

- Mixue Ice Cream & Tea for inspiration
- Bootstrap team for the UI framework
- PHPMailer contributors for email functionality
- Open source community for various tools and libraries

---

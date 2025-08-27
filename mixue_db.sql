-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 27, 2025 at 03:48 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mixue_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `address`, `phone`) VALUES
(1, 'Mixue Taman Yayasan', '16A, Jalan Bistari 4/3, Taman Yayasan, 85000 Segamat, Johor Darul Ta\'zim', '0127505253'),
(2, 'Mixue U Sentral Segamat', 'Lg floor, U Sentral Segamat, Jalan Utama 3/2, Taman Bandar Utama, 85000 Segamat District, Johor', ''),
(3, 'Mixue Labis', 'NO.92,TINGKAT BAWAH, Jln Segamat, TAMAN ASIA TIMUR, 85300 Labis, Johor', '0127505253'),
(4, 'Mixue UOA Bangsar', '5, Jalan Bangsar Utama 1, Bangsar, 59000 Kuala Lumpur, Wilayah Persekutuan Kuala Lumpur', '0179179175'),
(5, 'Mixue Tangkak', 'lc288 jalan muar tangkak, 84900 Muar, Johor Darul Ta\'zim', ''),
(6, 'Mixue MMU Melaka', '8, Jalan Ixora, Pusat Komersial Ixora, Hang Tuah Jaya Bukit beruang, 75450 Melaka', ''),
(7, 'Mixue Jalan Sinar Bakri 1, Muar', '15, Jalan Sinar Bakri 1, Pusat Perniagaan Sinar Bakri, 84200 Muar, Johor', ''),
(8, 'Mixue Jalan Ipoh', 'Jln Sultan Azlan Shah, Jalan Ipoh, 51200 Kuala Lumpur, Wilayah Persekutuan Kuala Lumpur', ''),
(9, 'Mixue NU Sentral', 'Nu Sentral, Kuala Lumpur Sentral, 50470 Kuala Lumpur, Federal Territory of Kuala Lumpur', '');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `is_active`) VALUES
(1, 'coffee', 1),
(2, 'fruit drink', 1),
(3, 'milk tea', 1),
(4, 'ice cream', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notification_logs`
--

CREATE TABLE `notification_logs` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('sent','failed') DEFAULT 'sent',
  `error_message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `delivery_fee` decimal(8,2) DEFAULT NULL,
  `status` enum('Pending','Preparing','Out for Delivery','Completed','Cancelled') DEFAULT 'Pending',
  `type` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_details_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `temperature` varchar(10) DEFAULT NULL,
  `sugar` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_history`
--

CREATE TABLE `password_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime NOT NULL,
  `payment_method` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category_id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT 'default.jpg',
  `is_available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `category_id`, `image`, `is_available`) VALUES
(1, 'Ice Cream Toffee Hazelnut Latte', '', 5.00, 1, '1752837402_Ice Cream Toffee Hazelnut Latte_20241018164435A050.png', 1),
(2, 'Ice Cream Latte', '', 5.00, 1, '1752837421_Ice Cream Latte_20241018164354A048.png', 1),
(3, 'Ice Cream Mocha', '', 5.00, 1, '1752837467_Ice Cream Mocha_20241018164414A049.png', 1),
(4, 'Lemon Black Tea', '', 5.00, 2, '1752837596_Lemon Black Tea_20241018163052A047.png', 1),
(5, 'Lemon Jasmine Tea', '', 5.00, 2, '1752837622_Lemon Jasmine Tea_20241018163035A046.png', 1),
(6, 'Fresh Lemonade', '', 5.00, 2, '1752837637_Fresh Lemonade_20241018163013A045.png', 1),
(8, 'Creamy Mango Boba', '', 5.00, 2, '1752837747_Creamy Mango Boba_20241018162955A044.png', 1),
(9, 'Passion Fruit Bubble Tea', '', 5.00, 2, '1752837784_Passion Fruit Bubble Tea_20241018162937A043.png', 1),
(10, 'Kiwi Jasmine Tea', '', 5.00, 2, '1752837797_Kiwi  Jasmine Tea_20241018162918A042.png', 1),
(11, 'Peach Jasmine Tea', '', 5.00, 2, '1752837810_Peach Jasmine Tea_20241018162900A041.png', 1),
(12, 'Peach Black Tea', '', 5.00, 2, '1752837824_Peach Black Tea_20241018162841A040.png', 1),
(13, 'Strawberry Creamy Drink', '', 5.00, 2, '1752837842_Strawberry Creamy Drink_20241018162030A030.png', 1),
(14, 'Kiwi Creamy Drink', '', 5.00, 2, '1752837856_Kiwi Creamy Drink_20241018162011A029.png', 1),
(15, 'Peach Mi-Shake', '', 5.00, 2, '1752837961_Peach Mi-Shake_20241018160742A023.png', 1),
(16, 'Strawberry Mi-Shake', '', 5.00, 2, '1752837977_Strawberry Mi-Shake_20241018160715A022.png', 1),
(17, 'Strawberry-Crispy Sundae', '', 5.00, 4, '1752837997_Strawberry-Crispy Sundae_20241018161830A028.png', 1),
(18, 'O-Crispy Sundae', '', 5.00, 4, '1752838008_O-Crispy Sundae_20241018160945A027.png', 1),
(19, 'Super Mango Sundae', '', 5.00, 4, '1752838030_Super Mango Sundae_20241018160915A026.png', 1),
(20, 'Super Boba Sundae', '', 5.00, 1, '1752838046_Super Boba Sundae_20241018160835A025.png', 1),
(21, 'MIXUE Ice Cream', '', 5.00, 4, '1752838063_MIXUE Ice Cream_20241018160646A021.png', 1),
(22, 'Boba Mi-Shake', '', 5.00, 3, '1752838091_Boba Mi-Shake_20241018160804A024.png', 1),
(23, 'Twin-Topping Milk Tea', '', 5.00, 3, '1752838137_Twin-Topping Milk Tea_20241018162442A039.png', 1),
(24, 'Classical Milk Tea', '', 5.00, 3, '1752838169_Classical Milk Tea_20241018162423A038.png', 1),
(25, 'Super-Triple Milk Tea', '', 5.00, 3, '1752838185_Super-Triple Milk Tea_20241018162240A036.png', 1),
(26, 'O-coco Milk Tea', '', 5.00, 3, '1752838199_O-coco Milk Tea_20241018162202A035.png', 1),
(28, 'Toffee Hazelnut Milk Tea', '', 5.00, 3, '1752838247_Toffee Hazelnut Milk Tea_20241018162108A032.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `reloads`
--

CREATE TABLE `reloads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL,
  `payment_type` varchar(50) NOT NULL DEFAULT 'Others'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `security_logs`
--

CREATE TABLE `security_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `level` enum('INFO','WARN','CRITICAL') NOT NULL,
  `event_type` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin','manager') NOT NULL DEFAULT 'user',
  `failed_login_attempts` int(11) NOT NULL DEFAULT 0,
  `account_locked_until` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_logs`
--
ALTER TABLE `notification_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_sent_at` (`sent_at`),
  ADD KEY `idx_notification_status` (`status`),
  ADD KEY `idx_notification_email_sent` (`email`,`sent_at`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_details_id`);

--
-- Indexes for table `password_history`
--
ALTER TABLE `password_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token_hash` (`token_hash`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_payments_order` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `reloads`
--
ALTER TABLE `reloads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `security_logs`
--
ALTER TABLE `security_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notification_logs`
--
ALTER TABLE `notification_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_details_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_history`
--
ALTER TABLE `password_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `reloads`
--
ALTER TABLE `reloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `security_logs`
--
ALTER TABLE `security_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `password_history`
--
ALTER TABLE `password_history`
  ADD CONSTRAINT `password_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reloads`
--
ALTER TABLE `reloads`
  ADD CONSTRAINT `reloads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 30, 2025 at 04:19 PM
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

--
-- Dumping data for table `notification_logs`
--

INSERT INTO `notification_logs` (`id`, `email`, `subject`, `sent_at`, `status`, `error_message`) VALUES
(1, 'admin@mixue.com', 'New Product Available - Mixue System', '2025-07-24 15:32:02', 'sent', NULL),
(2, 'user1@example.com', 'New Product Available - Mixue System', '2025-07-24 15:32:06', 'sent', NULL),
(3, 'admin@mixue.com', 'New Product Available - Mixue System', '2025-07-24 16:14:13', 'sent', NULL),
(4, 'user1@example.com', 'New Product Available - Mixue System', '2025-07-24 16:14:16', 'sent', NULL),
(5, 'admin@mixue.com', 'New Product Available - Mixue System', '2025-07-24 16:15:19', 'sent', NULL),
(6, 'user1@example.com', 'New Product Available - Mixue System', '2025-07-24 16:15:23', 'sent', NULL),
(7, 'admin@mixue.com', 'New Product Available - Mixue System', '2025-07-24 16:17:17', 'sent', NULL),
(8, 'user1@example.com', 'New Product Available - Mixue System', '2025-07-24 16:17:22', 'sent', NULL),
(9, 'admin@mixue.com', 'New Branch Opening - Mixue System', '2025-07-24 16:17:25', 'sent', NULL),
(10, 'user1@example.com', 'New Branch Opening - Mixue System', '2025-07-24 16:17:29', 'sent', NULL),
(11, 'admin@mixue.com', 'New Branch Opening - Mixue System', '2025-07-24 16:17:32', 'sent', NULL),
(12, 'user1@example.com', 'New Branch Opening - Mixue System', '2025-07-24 16:17:36', 'sent', NULL),
(13, 'admin@mixue.com', 'New Branch Opening - Mixue System', '2025-07-24 16:48:24', 'sent', NULL),
(14, 'user1@example.com', 'New Branch Opening - Mixue System', '2025-07-24 16:48:28', 'sent', NULL),
(15, 'admin@mixue.com', 'New Branch Opening - Mixue System', '2025-07-24 16:50:51', 'sent', NULL),
(16, 'user1@example.com', 'New Branch Opening - Mixue System', '2025-07-24 16:50:55', 'sent', NULL);

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

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `phone`, `address`, `total`, `delivery_fee`, `status`, `type`, `created_at`, `branch_id`) VALUES
(34, 5, '1212', NULL, 10.00, NULL, 'Pending', 'pickup', '2025-07-23 20:57:50', 3),
(35, 5, '123456', 'an address', 15.00, NULL, 'Pending', 'delivery', '2025-07-23 20:59:48', NULL),
(36, 5, '123456', NULL, 25.00, NULL, 'Pending', 'pickup', '2025-07-23 21:00:20', 2),
(37, 5, '123456', 'another address', 5.00, NULL, 'Pending', 'delivery', '2025-07-23 21:35:10', NULL),
(38, 5, '123456', NULL, 5.00, NULL, 'Pending', 'pickup', '2025-07-23 23:53:04', 3),
(39, 5, '123456', 'sasassa', 9.00, 4.00, 'Pending', 'delivery', '2025-07-23 23:59:52', NULL),
(40, 5, '123456', NULL, 5.00, 0.00, 'Pending', 'pickup', '2025-07-24 00:02:21', 4),
(41, 8, '123', 'segamat', 9.00, 4.00, 'Pending', 'delivery', '2025-07-30 21:11:14', NULL);

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

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`order_details_id`, `order_id`, `product_id`, `quantity`, `temperature`, `sugar`) VALUES
(55, 34, 8, 1, 'Hot', '100%'),
(56, 34, 11, 1, 'Hot', '100%'),
(57, 35, 8, 2, 'Cold', '0%'),
(58, 35, 6, 1, 'Hot', '100%'),
(59, 36, 8, 1, 'Hot', '100%'),
(60, 36, 6, 4, 'Hot', '0%'),
(61, 37, 3, 1, 'Cold', '100%'),
(62, 38, 3, 1, 'Cold', '100%'),
(63, 39, 8, 1, 'Hot', '100%'),
(64, 40, 3, 1, 'Cold', '100%'),
(65, 41, 3, 1, 'Cold', '100%');

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

--
-- Dumping data for table `password_history`
--

INSERT INTO `password_history` (`id`, `user_id`, `password_hash`, `created_at`) VALUES
(1, 1, '$argon2id$v=19$m=65536,t=4,p=1$Y1g5SC9QMjY5NjNjVmZGaQ$v0uQBsk4D/w5SnZfzr2d47mY3b/9jjSUT8OANr0fUiE', '2025-07-10 06:29:11'),
(2, 1, '$argon2id$v=19$m=65536,t=4,p=1$cnZqSkJrNUhHWUFNMDNwcw$ezDgUVIc2mUyaTxtSnKzHx5aEowiILg5eD53OKq3lRk', '2025-07-13 02:19:53'),
(3, 3, '$argon2id$v=19$m=65536,t=4,p=1$bEVWMnl3dDF5L2JqNy85Mg$uw+SCQRdzFyBJmzwMHuTdaT/j52J8Fp1aZYKcXUhUE4', '2025-07-18 12:07:48'),
(4, 2, '$argon2id$v=19$m=65536,t=4,p=1$ZUdCbzQ0RWFiNTVWL1liaA$HRPysuRCoHMhOfRlbHmcKJJ/dtHnVG0M+UzQ4BB9m8g', '2025-07-18 14:38:54'),
(5, 4, '$argon2id$v=19$m=65536,t=4,p=1$LjFFN21uT0pUNjNkdms4aw$FMzI/nW7qS9OzRDavQrmShvGQSCIcmwUisBoG4S87u8', '2025-07-18 14:41:40'),
(6, 1, '$argon2id$v=19$m=65536,t=4,p=1$bFdKbUU4bXhHZHh4bVNxZg$Ii9Ott1bSzt8h6piHHN0b6jMMql63yP6n9l7WyxNwKE', '2025-07-21 13:44:38'),
(7, 5, '$argon2id$v=19$m=65536,t=4,p=1$S0p6VlgvTjJXVktsaGtoOA$2KbE7EXR98OS7+LOW7xPd1w1b9bmYtkT10mCdQp6hzc', '2025-07-22 08:14:54'),
(8, 6, '$argon2id$v=19$m=65536,t=4,p=1$YlpkRWJGTnFMRm95Nm1pLw$PZ0GjuK3iPEnv3tFWLdX/lJK+ymXsleWBIMu0+co2/c', '2025-07-29 14:33:40'),
(9, 1, '$argon2id$v=19$m=65536,t=4,p=1$aUYuekZhYS41VW5ycTlxLw$hMul+F0iaV1my6peA5GCY87ZbgsxPUXXVpV+3AlysfU', '2025-07-29 14:43:05'),
(10, 7, '$argon2id$v=19$m=65536,t=4,p=1$RWt6MEhCMllFSU5IZjNtaw$10QZBfrkQe4CJ7L+mN/f12/1z2UFzyy038KUhsQhUGo', '2025-07-30 05:45:08'),
(11, 8, '$argon2id$v=19$m=65536,t=4,p=1$bTU1WlZSZ1ouZ3o0UlNvYw$vbZVojTLidxJMhxx4LV9e/piz28DYi9yzQBmycTXnK8', '2025-07-30 06:09:46'),
(12, 9, '$argon2id$v=19$m=65536,t=4,p=1$NUZldjFiNnYzVEJxczFkTw$WVNg4EE+7ZW9WBuRGJYwLBpEwxfXSYUacoc+soqvsdE', '2025-07-30 09:13:01');

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

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token_hash`, `expires_at`) VALUES
(1, 1, '9d2ccdca9fe88fd7d56590993a994426ced665d134d15a4bfd0bd39295e3583e', '2025-07-10 08:59:52'),
(2, 1, '252ab9ca2684fc7da107b3b6c8aa1884bfd8a8de27c458ad4ec43cc5636fb865', '2025-07-10 09:02:13'),
(3, 1, '9fe948e511117cdc01ed146df500b5a4ab73cd4703446623bc341fd8e1942073', '2025-07-10 09:06:57'),
(4, 1, '4271afdb18c89edaf168e135645ebb66689dbb1b3d112b3224b8d1576ef13d9f', '2025-07-10 09:08:21'),
(5, 1, '57b727dfeb6ade2a726339796e7e232ad2c8d8164cfa19639ba9f3bc1acec7c0', '2025-07-10 09:10:04'),
(6, 1, '9581c8ecc4092a42f5b371417bb4f33fcb8950506479b6172dd875fb49aca56a', '2025-07-10 09:11:05'),
(7, 1, '58e30e458da3eccbd4f4fe86cb727313e46bca973d97f9c2fbcb46a73766fca5', '2025-07-13 04:25:00'),
(8, 1, '680f8a6ff48913fb929023df16e7372b1475fae212309e1e0c9a9d306530e6bd', '2025-07-13 04:26:22'),
(9, 1, '3be807b4d1cb348935943935e00cf4ee1c2dafeb598b5a43765d21b997a3217d', '2025-07-13 04:31:34'),
(10, 1, '60a2f4d704eb13a8064927467f1f1d83fd4fd6c61e0ef91913d42976c7f312d2', '2025-07-13 04:33:01'),
(15, 1, 'a9d3928d0465ed6080133c5fad982937c5f063112d54a0393b2a0150a15e4894', '2025-07-30 16:10:09'),
(16, 1, 'aee3c43f48dc690f714995a107cd89eb99ec78c37de6d32813c69ec1eef0b2c1', '2025-07-30 16:16:14');

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

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `amount`, `payment_date`, `payment_method`) VALUES
(35, 34, 10.00, '2025-07-23 20:57:50', 'GrabPay'),
(36, 35, 15.00, '2025-07-23 20:59:48', 'TNG eWallet'),
(37, 36, 25.00, '2025-07-23 21:00:20', 'Online Banking'),
(38, 37, 5.00, '2025-07-23 21:35:10', 'Online Banking'),
(39, 38, 5.00, '2025-07-23 23:53:04', 'GrabPay'),
(40, 39, 9.00, '2025-07-23 23:59:52', 'GrabPay'),
(41, 40, 5.00, '2025-07-24 00:02:21', 'Others'),
(42, 41, 9.00, '2025-07-30 21:11:14', 'TNG eWallet');

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
(7, 'Fresh Lemonade', '', 5.00, 2, '1752837723_Fresh Lemonade_20241018163013A045.png', 1),
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
(28, 'Toffee Hazelnut Milk Tea', '', 5.00, 3, '1752838247_Toffee Hazelnut Milk Tea_20241018162108A032.png', 1),
(29, 'Brown Sugar Bubble Tea', '', 5.00, 3, '1752838272_Brown Sugar Bubble Tea_20241018162049A031.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `reloads`
--

CREATE TABLE `reloads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_intent_id` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reloads`
--

INSERT INTO `reloads` (`id`, `user_id`, `amount`, `created_at`) VALUES
(1, 1, 50.00, '2025-07-21 22:29:16'),
(2, 1, 1.20, '2025-07-21 22:42:42'),
(3, 1, 2.30, '2025-07-22 00:06:18'),
(4, 5, 100.00, '2025-07-23 04:14:45'),
(5, 5, 100.00, '2025-07-23 20:01:48'),
(6, 8, 200.00, '2025-07-30 21:10:33');

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

--
-- Dumping data for table `security_logs`
--

INSERT INTO `security_logs` (`id`, `user_id`, `ip_address`, `level`, `event_type`, `message`, `created_at`) VALUES
(1, NULL, '::1', 'WARN', 'REGISTER_FAIL_VALIDATION', '{\"email\":\"leonghuichun@gmail.com\",\"reason\":\"Password must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special symbol.\"}', '2025-07-10 06:28:36'),
(2, 1, '::1', 'INFO', 'REGISTER_SUCCESS', '{\"user_id\":\"1\"}', '2025-07-10 06:29:11'),
(3, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1}', '2025-07-10 06:29:23'),
(4, 1, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":1}', '2025-07-10 06:29:37'),
(5, NULL, '::1', 'WARN', 'PASS_RESET_FAIL_NO_USER', '{\"email\":\"leon@gmail.com\"}', '2025-07-10 06:42:53'),
(6, 1, '::1', 'INFO', 'PASS_RESET_REQUEST', '{\"user_id\":1}', '2025-07-10 06:44:52'),
(7, 1, '::1', 'INFO', 'PASS_RESET_REQUEST', '{\"user_id\":1}', '2025-07-10 06:47:13'),
(8, 1, '::1', 'INFO', 'PASS_RESET_REQUEST', '{\"user_id\":1}', '2025-07-10 06:51:57'),
(9, 1, '::1', 'INFO', 'PASS_RESET_REQUEST', '{\"user_id\":1}', '2025-07-10 06:53:21'),
(10, 1, '::1', 'INFO', 'PASS_RESET_REQUEST', '{\"user_id\":1}', '2025-07-10 06:55:04'),
(11, 1, '::1', 'INFO', 'PASS_RESET_REQUEST', '{\"user_id\":1}', '2025-07-10 06:56:05'),
(12, 1, '::1', 'WARN', 'PASS_RESET_FAIL_VALIDATION', '{\"user_id\":1,\"reason\":\"The new passwords do not match., The new password does not meet complexity requirements.\"}', '2025-07-10 06:56:24'),
(13, NULL, '::1', 'CRITICAL', 'EMAIL_SEND_FAIL', '{\"email\":\"leonghuichun@gmail.com\",\"error\":\"SMTP Error: Could not connect to SMTP host.\"}', '2025-07-13 02:10:10'),
(14, 1, '::1', 'INFO', 'PASS_RESET_REQUEST', '{\"user_id\":1}', '2025-07-13 02:10:10'),
(15, NULL, '::1', 'CRITICAL', 'EMAIL_SEND_FAIL', '{\"email\":\"leonghuichun@gmail.com\",\"error\":\"SMTP Error: Could not connect to SMTP host.\"}', '2025-07-13 02:11:33'),
(16, 1, '::1', 'INFO', 'PASS_RESET_REQUEST', '{\"user_id\":1}', '2025-07-13 02:11:33'),
(17, NULL, '::1', 'INFO', 'EMAIL_SEND_SUCCESS', '{\"email\":\"leonghuichun@gmail.com\"}', '2025-07-13 02:16:38'),
(18, 1, '::1', 'INFO', 'PASS_RESET_REQUEST', '{\"user_id\":1}', '2025-07-13 02:16:38'),
(19, NULL, '::1', 'INFO', 'EMAIL_SEND_SUCCESS', '{\"email\":\"leonghuichun@gmail.com\"}', '2025-07-13 02:18:05'),
(20, 1, '::1', 'INFO', 'PASS_RESET_REQUEST', '{\"user_id\":1}', '2025-07-13 02:18:05'),
(21, NULL, '::1', 'INFO', 'EMAIL_SEND_SUCCESS', '{\"email\":\"leonghuichun@gmail.com\"}', '2025-07-13 02:19:03'),
(22, 1, '::1', 'INFO', 'PASS_RESET_REQUEST', '{\"user_id\":1}', '2025-07-13 02:19:03'),
(23, 1, '::1', 'WARN', 'PASS_RESET_FAIL_VALIDATION', '{\"user_id\":1,\"reason\":\"You cannot reuse one of your last 5 passwords.\"}', '2025-07-13 02:19:36'),
(24, 1, '::1', 'INFO', 'PASS_RESET_SUCCESS', '{\"user_id\":1}', '2025-07-13 02:19:53'),
(25, 1, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":1}', '2025-07-13 02:20:05'),
(26, 1, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":1}', '2025-07-13 12:23:53'),
(27, 1, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":1}', '2025-07-13 12:24:02'),
(28, 1, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":1}', '2025-07-13 12:24:27'),
(29, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"user\"}', '2025-07-13 12:24:36'),
(30, 1, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":1}', '2025-07-13 12:25:26'),
(31, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"user\"}', '2025-07-13 13:54:03'),
(32, 1, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":1}', '2025-07-13 13:57:51'),
(33, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"admin\",\"is_admin\":true}', '2025-07-18 09:34:01'),
(34, 2, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":2,\"role\":\"admin\"}', '2025-07-18 11:12:41'),
(35, 2, '::1', 'INFO', 'BRANCH_CREATE', '{\"branch_id\":\"7\",\"name\":\"Mixue Jalan Sinar Bakri 1, Muar\",\"admin_user_id\":2}', '2025-07-18 11:41:05'),
(36, 2, '::1', 'INFO', 'PRODUCT_UPDATE', '{\"product_id\":\"29\",\"updated_name\":\"Brown Sugar Bubble Tea\",\"admin_user_id\":2}', '2025-07-18 11:41:13'),
(37, 2, '::1', 'INFO', 'PRODUCT_UPDATE', '{\"product_id\":\"29\",\"updated_name\":\"Brown Sugar Bubble Tea\",\"admin_user_id\":2}', '2025-07-18 11:41:53'),
(38, 2, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":2,\"role\":\"admin\"}', '2025-07-18 11:43:49'),
(39, 2, '::1', 'INFO', 'CATEGORY_CREATE', '{\"category_id\":\"5\",\"name\":\"aaa\",\"admin_user_id\":2}', '2025-07-18 11:44:18'),
(40, 2, '::1', 'INFO', 'CATEGORY_DELETE', '{\"category_id\":\"5\",\"deleted_name\":\"aaa\",\"admin_user_id\":2}', '2025-07-18 11:44:35'),
(41, 2, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":2}', '2025-07-18 12:03:34'),
(42, 2, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":2,\"role\":\"admin\"}', '2025-07-18 12:04:02'),
(43, 2, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":2}', '2025-07-18 12:04:06'),
(44, 3, '::1', 'INFO', 'REGISTER_SUCCESS', '{\"user_id\":\"3\"}', '2025-07-18 12:07:48'),
(45, 3, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":3,\"role\":\"user\"}', '2025-07-18 12:08:19'),
(46, 3, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":3}', '2025-07-18 12:08:32'),
(47, 2, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":2,\"role\":\"admin\"}', '2025-07-18 12:09:34'),
(48, 2, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":2}', '2025-07-18 12:09:37'),
(49, 2, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":2,\"role\":\"admin\"}', '2025-07-18 12:13:50'),
(50, 2, '::1', 'INFO', 'CATEGORY_CREATE', '{\"category_id\":\"6\",\"name\":\"wang zi zhen\",\"admin_user_id\":2}', '2025-07-18 12:14:00'),
(51, 2, '::1', 'INFO', 'CATEGORY_CREATE', '{\"category_id\":\"7\",\"name\":\"wang zi zhen\",\"admin_user_id\":2}', '2025-07-18 12:17:25'),
(52, 2, '::1', 'INFO', 'CATEGORY_DELETE', '{\"category_id\":\"6\",\"deleted_name\":\"wang zi zhen\",\"admin_user_id\":2}', '2025-07-18 12:17:29'),
(53, 2, '::1', 'INFO', 'CATEGORY_DELETE', '{\"category_id\":\"7\",\"deleted_name\":\"wang zi zhen\",\"admin_user_id\":2}', '2025-07-18 12:17:48'),
(54, 2, '::1', 'INFO', 'CATEGORY_CREATE', '{\"category_id\":\"8\",\"name\":\"MIXUE Ice Cream\",\"admin_user_id\":2}', '2025-07-18 12:17:51'),
(55, 2, '::1', 'INFO', 'CATEGORY_DELETE', '{\"category_id\":\"8\",\"deleted_name\":\"MIXUE Ice Cream\",\"admin_user_id\":2}', '2025-07-18 12:18:06'),
(56, 2, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":2}', '2025-07-18 13:58:15'),
(57, 2, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":2,\"role\":\"admin\"}', '2025-07-18 13:58:24'),
(58, 2, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":2}', '2025-07-18 14:23:33'),
(60, 4, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":4}', '2025-07-18 14:25:01'),
(61, 4, 'LOCALHOST', 'INFO', 'ROLE_UPDATE_TO_MANAGER', '{\"email\":\"manager@mixue.com\",\"updated_by\":\"system_script\"}', '2025-07-18 14:28:23'),
(62, 4, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":4,\"role\":\"manager\"}', '2025-07-18 14:30:02'),
(63, 4, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":4}', '2025-07-18 14:30:17'),
(64, 2, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":2,\"role\":\"admin\"}', '2025-07-18 14:30:26'),
(65, 2, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":2}', '2025-07-18 14:31:16'),
(66, 4, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":4,\"role\":\"manager\"}', '2025-07-18 14:31:41'),
(67, 4, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":4}', '2025-07-18 14:31:46'),
(68, 3, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":3}', '2025-07-18 14:31:53'),
(69, 3, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":3,\"role\":\"user\"}', '2025-07-18 14:32:05'),
(70, 3, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":3}', '2025-07-18 14:32:12'),
(71, 3, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":3,\"role\":\"user\"}', '2025-07-18 14:32:23'),
(72, 3, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":3}', '2025-07-18 14:32:23'),
(73, 2, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":2,\"role\":\"admin\"}', '2025-07-18 14:32:34'),
(74, 2, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":2}', '2025-07-18 14:38:26'),
(75, 2, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":2,\"role\":\"admin\"}', '2025-07-18 14:38:36'),
(76, 2, '::1', 'INFO', 'PASS_CHANGE_SUCCESS', '{\"user_id\":2}', '2025-07-18 14:38:54'),
(77, 2, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":2}', '2025-07-18 14:38:55'),
(78, 2, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":2}', '2025-07-18 14:39:06'),
(79, 2, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":2,\"role\":\"admin\"}', '2025-07-18 14:39:13'),
(80, 2, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":2}', '2025-07-18 14:39:19'),
(81, 4, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":4}', '2025-07-18 14:39:29'),
(82, 4, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":4}', '2025-07-18 14:39:46'),
(83, NULL, '::1', 'INFO', 'EMAIL_SEND_SUCCESS', '{\"email\":\"wangzz-jm23@student.tarc.edu.my\"}', '2025-07-18 14:41:12'),
(84, 4, '::1', 'INFO', 'PASS_RESET_REQUEST', '{\"user_id\":4}', '2025-07-18 14:41:12'),
(85, 4, '::1', 'INFO', 'PASS_RESET_SUCCESS', '{\"user_id\":4}', '2025-07-18 14:41:40'),
(86, 4, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":4,\"role\":\"manager\"}', '2025-07-18 14:41:48'),
(87, NULL, '::1', 'WARN', 'LOGIN_FAIL_NO_USER', '{\"email\":\"manager@mixue.com\"}', '2025-07-18 15:24:04'),
(88, NULL, '::1', 'WARN', 'LOGIN_FAIL_NO_USER', '{\"email\":\"manager@mixue.com\"}', '2025-07-18 15:24:23'),
(89, 4, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":4,\"role\":\"manager\"}', '2025-07-18 15:24:37'),
(90, 2, '::1', 'INFO', 'ROLE_CHANGE_SUCCESS', '{\"user_id\":2,\"manager_id\":4,\"old_role\":\"admin\",\"new_role\":\"user\",\"user_email\":\"admin@mixue.com\"}', '2025-07-18 15:24:49'),
(91, 3, '::1', 'INFO', 'ROLE_CHANGE_SUCCESS', '{\"user_id\":3,\"manager_id\":4,\"old_role\":\"user\",\"new_role\":\"admin\",\"user_email\":\"zi189wangzhen@gmail.com\"}', '2025-07-18 15:24:59'),
(92, 4, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":4}', '2025-07-18 15:25:03'),
(93, 2, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":2}', '2025-07-18 15:25:10'),
(94, 2, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":2}', '2025-07-18 15:25:16'),
(95, 2, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":2,\"role\":\"user\"}', '2025-07-18 15:25:27'),
(96, 2, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":2}', '2025-07-18 15:32:48'),
(97, 3, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":3,\"role\":\"admin\"}', '2025-07-18 15:32:56'),
(98, 3, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":3}', '2025-07-18 15:34:14'),
(99, 4, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":4,\"role\":\"manager\"}', '2025-07-18 15:34:22'),
(100, NULL, '::1', 'INFO', 'EMAIL_SEND_SUCCESS', '{\"email\":\"leonghuichun@gmail.com\"}', '2025-07-21 13:44:05'),
(101, 1, '::1', 'INFO', 'PASS_RESET_REQUEST', '{\"user_id\":1}', '2025-07-21 13:44:05'),
(102, 1, '::1', 'INFO', 'PASS_RESET_SUCCESS', '{\"user_id\":1}', '2025-07-21 13:44:38'),
(103, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"user\"}', '2025-07-21 13:44:45'),
(104, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"user\"}', '2025-07-21 14:26:44'),
(105, 1, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":1}', '2025-07-21 14:47:58'),
(106, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"user\"}', '2025-07-21 14:48:15'),
(107, 1, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":1}', '2025-07-21 15:14:26'),
(108, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"user\"}', '2025-07-21 15:14:35'),
(109, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"user\"}', '2025-07-21 15:34:36'),
(110, 1, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":1}', '2025-07-21 15:40:30'),
(111, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"user\"}', '2025-07-21 15:40:39'),
(112, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"user\"}', '2025-07-21 15:51:44'),
(113, 1, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":1}', '2025-07-21 16:06:45'),
(114, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"user\"}', '2025-07-21 16:07:05'),
(115, 1, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":1}', '2025-07-21 16:30:27'),
(116, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"user\"}', '2025-07-21 16:34:59'),
(117, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"user\"}', '2025-07-21 16:38:34'),
(118, 1, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":1}', '2025-07-21 16:39:15'),
(119, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"user\"}', '2025-07-21 16:39:31'),
(120, 1, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":1}', '2025-07-21 16:40:12'),
(121, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"user\"}', '2025-07-22 01:04:07'),
(122, 1, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":1}', '2025-07-22 01:06:13'),
(123, 2, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":2}', '2025-07-22 08:12:50'),
(124, NULL, '::1', 'WARN', 'LOGIN_FAIL_NO_USER', '{\"email\":\"user1@example.com\"}', '2025-07-22 08:12:57'),
(125, NULL, '::1', 'WARN', 'LOGIN_FAIL_NO_USER', '{\"email\":\"user1@example.com\"}', '2025-07-22 08:14:13'),
(126, 5, '::1', 'INFO', 'REGISTER_SUCCESS', '{\"user_id\":\"5\"}', '2025-07-22 08:14:54'),
(127, NULL, '::1', 'WARN', 'REGISTER_FAIL_VALIDATION', '{\"email\":\"user1@example.com\",\"reason\":\"An account with this email address already exists.\"}', '2025-07-22 08:16:43'),
(128, 5, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":5,\"role\":\"user\"}', '2025-07-22 08:17:39'),
(129, 5, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":5,\"role\":\"user\"}', '2025-07-22 20:14:08'),
(130, 5, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":5,\"role\":\"user\"}', '2025-07-22 20:49:01'),
(131, 5, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":5,\"role\":\"user\"}', '2025-07-22 21:33:33'),
(132, 5, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":5,\"role\":\"user\"}', '2025-07-22 22:12:44'),
(133, 5, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":5}', '2025-07-22 22:29:47'),
(134, 5, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":5,\"role\":\"user\"}', '2025-07-22 22:29:53'),
(135, 5, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":5,\"role\":\"user\"}', '2025-07-23 05:32:11'),
(136, 5, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":5,\"role\":\"user\"}', '2025-07-23 06:32:40'),
(137, 5, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":5,\"role\":\"user\"}', '2025-07-23 07:03:34'),
(138, 5, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":5,\"role\":\"user\"}', '2025-07-23 08:32:45'),
(139, 5, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":5,\"role\":\"user\"}', '2025-07-23 10:58:09'),
(140, 5, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":5,\"role\":\"user\"}', '2025-07-23 11:30:44'),
(141, 5, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":5,\"role\":\"user\"}', '2025-07-23 12:01:39'),
(142, 5, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":5,\"role\":\"user\"}', '2025-07-23 12:31:59'),
(143, 5, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":5,\"role\":\"user\"}', '2025-07-23 13:14:33'),
(144, 5, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":5}', '2025-07-23 13:39:02'),
(145, 5, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":5,\"role\":\"user\"}', '2025-07-23 15:42:48'),
(146, 5, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":5,\"role\":\"user\"}', '2025-07-24 03:51:40'),
(147, 3, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":3,\"role\":\"admin\"}', '2025-07-24 15:31:47'),
(148, 3, '::1', 'INFO', 'PRODUCT_CREATE', '{\"product_id\":\"30\",\"name\":\"gemini\",\"price\":\"1000\",\"admin_user_id\":3}', '2025-07-24 15:31:59'),
(149, 3, '::1', 'INFO', 'PRODUCT_DELETE', '{\"product_id\":\"30\",\"deleted_name\":\"gemini\",\"deleted_image\":\"default.jpg\",\"admin_user_id\":3}', '2025-07-24 15:32:41'),
(150, 3, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":3}', '2025-07-24 15:33:06'),
(151, 3, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":3,\"role\":\"admin\"}', '2025-07-24 15:59:20'),
(152, NULL, 'UNKNOWN', 'INFO', 'PRODUCT_CREATE', '{\"product_id\":\"31\",\"name\":\"\\u6d4b\\u8bd5\\u51b0\\u6dc7\\u6dcb\\u4ea7\\u54c1\",\"price\":15.9,\"admin_user_id\":null}', '2025-07-24 16:15:15'),
(153, 3, '::1', 'INFO', 'PRODUCT_DELETE', '{\"product_id\":\"31\",\"deleted_name\":\"\\u6d4b\\u8bd5\\u51b0\\u6dc7\\u6dcb\\u4ea7\\u54c1\",\"deleted_image\":\"default.jpg\",\"admin_user_id\":3}', '2025-07-24 16:25:05'),
(154, 3, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":3}', '2025-07-24 16:26:41'),
(155, 4, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":4,\"role\":\"manager\"}', '2025-07-24 16:26:58'),
(156, 4, '::1', 'INFO', 'BRANCH_CREATE', '{\"branch_id\":\"8\",\"name\":\"\",\"admin_user_id\":4}', '2025-07-24 16:48:20'),
(157, 4, '::1', 'INFO', 'BRANCH_CREATE', '{\"branch_id\":\"9\",\"name\":\"\",\"admin_user_id\":4}', '2025-07-24 16:50:47'),
(158, 4, '::1', 'INFO', 'BRANCH_UPDATE', '{\"branch_id\":\"6\",\"updated_name\":\"Mixue NU Sentral\",\"admin_user_id\":4}', '2025-07-24 16:50:57'),
(159, 4, '::1', 'INFO', 'BRANCH_UPDATE', '{\"branch_id\":\"9\",\"updated_name\":\"Mixue NU Sentral\",\"admin_user_id\":4}', '2025-07-24 16:51:15'),
(160, 4, '::1', 'INFO', 'BRANCH_UPDATE', '{\"branch_id\":\"9\",\"updated_name\":\"Mixue NU Sentral\",\"admin_user_id\":4}', '2025-07-24 16:54:12'),
(161, 4, '::1', 'INFO', 'BRANCH_UPDATE', '{\"branch_id\":\"9\",\"updated_name\":\"Mixue NU Sentral\",\"admin_user_id\":4}', '2025-07-24 16:56:15'),
(162, 3, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":3,\"role\":\"admin\"}', '2025-07-24 17:00:08'),
(163, 3, '::1', 'INFO', 'BRANCH_UPDATE', '{\"branch_id\":\"9\",\"updated_name\":\"Mixue NU Sentral\",\"admin_user_id\":3}', '2025-07-24 17:00:24'),
(164, 3, '::1', 'INFO', 'BRANCH_UPDATE', '{\"branch_id\":\"4\",\"updated_name\":\"Mixue UOA Bangsar\",\"admin_user_id\":3}', '2025-07-24 17:13:53'),
(165, 3, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":3,\"role\":\"admin\"}', '2025-07-24 18:13:54'),
(166, 1, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":1}', '2025-07-29 14:31:47'),
(167, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"admin\"}', '2025-07-29 14:32:15'),
(168, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"admin\"}', '2025-07-29 14:32:26'),
(169, 6, '::1', 'INFO', 'REGISTER_SUCCESS', '{\"user_id\":\"6\"}', '2025-07-29 14:33:40'),
(170, 6, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":6,\"role\":\"user\"}', '2025-07-29 14:40:12'),
(171, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"admin\"}', '2025-07-29 14:40:21'),
(172, 1, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":1}', '2025-07-29 14:40:28'),
(173, 1, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":1}', '2025-07-29 14:40:36'),
(174, NULL, '::1', 'INFO', 'EMAIL_SEND_SUCCESS', '{\"email\":\"leonghuichun@gmail.com\"}', '2025-07-29 14:40:52'),
(175, 1, '::1', 'INFO', 'PASS_RESET_REQUEST', '{\"user_id\":1}', '2025-07-29 14:40:52'),
(176, 1, '::1', 'WARN', 'PASS_RESET_FAIL_VALIDATION', '{\"user_id\":1,\"reason\":\"You cannot reuse one of your last 5 passwords.\"}', '2025-07-29 14:42:17'),
(177, 1, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":1}', '2025-07-29 14:42:36'),
(178, 1, '::1', 'INFO', 'PASS_RESET_SUCCESS', '{\"user_id\":1}', '2025-07-29 14:43:05'),
(179, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"admin\"}', '2025-07-29 14:46:30'),
(180, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"user\"}', '2025-07-29 14:47:55'),
(181, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"user\"}', '2025-07-29 14:49:26'),
(182, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"user\"}', '2025-07-29 14:50:25'),
(183, 1, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":1,\"role\":\"user\"}', '2025-07-29 14:51:29'),
(184, 6, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":6,\"role\":\"user\"}', '2025-07-29 14:59:45'),
(185, 1, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":1}', '2025-07-30 05:14:06'),
(186, 6, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":6}', '2025-07-30 05:14:25'),
(187, 6, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":6}', '2025-07-30 05:14:43'),
(188, 6, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":6}', '2025-07-30 05:24:30'),
(189, 6, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":6}', '2025-07-30 05:24:39'),
(190, 6, '::1', 'CRITICAL', 'ACCOUNT_LOCKED', '{\"user_id\":6}', '2025-07-30 05:25:48'),
(191, 6, '::1', 'WARN', 'LOGIN_FAIL_LOCKED', '{\"user_id\":6}', '2025-07-30 05:26:17'),
(192, 4, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":4}', '2025-07-30 05:26:46'),
(193, 6, '::1', 'WARN', 'LOGIN_FAIL_LOCKED', '{\"user_id\":6}', '2025-07-30 05:33:32'),
(194, 4, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":4}', '2025-07-30 05:33:52'),
(195, 4, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":4}', '2025-07-30 05:38:40'),
(196, 6, '::1', 'WARN', 'LOGIN_FAIL_LOCKED', '{\"user_id\":6}', '2025-07-30 05:38:51'),
(197, 7, '::1', 'INFO', 'REGISTER_SUCCESS', '{\"user_id\":\"7\"}', '2025-07-30 05:45:08'),
(198, 7, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":7,\"role\":\"user\"}', '2025-07-30 05:48:20'),
(199, 7, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":7}', '2025-07-30 05:48:43'),
(200, 6, '::1', 'CRITICAL', 'ACCOUNT_LOCKED', '{\"user_id\":6}', '2025-07-30 05:52:26'),
(201, 6, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":6}', '2025-07-30 05:52:26'),
(202, 6, '::1', 'WARN', 'LOGIN_FAIL_LOCKED', '{\"user_id\":6}', '2025-07-30 05:52:38'),
(203, 4, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":4}', '2025-07-30 05:52:53'),
(204, 4, '::1', 'CRITICAL', 'ACCOUNT_LOCKED', '{\"user_id\":4}', '2025-07-30 05:53:01'),
(205, 4, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":4}', '2025-07-30 05:53:01'),
(206, 4, '::1', 'WARN', 'LOGIN_FAIL_LOCKED', '{\"user_id\":4}', '2025-07-30 05:53:09'),
(207, 4, '::1', 'WARN', 'LOGIN_FAIL_LOCKED', '{\"user_id\":4}', '2025-07-30 05:53:19'),
(208, 5, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":5,\"role\":\"user\"}', '2025-07-30 05:53:44'),
(209, 5, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":5}', '2025-07-30 05:54:04'),
(210, 6, '::1', 'WARN', 'LOGIN_FAIL_LOCKED', '{\"user_id\":6}', '2025-07-30 05:54:17'),
(211, 7, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":7,\"role\":\"user\"}', '2025-07-30 05:54:27'),
(212, 7, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":7}', '2025-07-30 05:54:35'),
(213, 8, '::1', 'INFO', 'REGISTER_SUCCESS', '{\"user_id\":\"8\"}', '2025-07-30 06:09:46'),
(214, 8, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":8,\"role\":\"user\"}', '2025-07-30 06:09:57'),
(215, 6, '::1', 'CRITICAL', 'ACCOUNT_LOCKED', '{\"user_id\":6}', '2025-07-30 07:22:31'),
(216, 6, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":6}', '2025-07-30 07:22:31'),
(217, 7, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":7}', '2025-07-30 07:22:41'),
(218, 8, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":8,\"role\":\"user\"}', '2025-07-30 07:22:53'),
(219, 8, '::1', 'INFO', 'LOGOUT_SUCCESS', '{\"user_id\":8}', '2025-07-30 07:30:11'),
(220, 8, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":8,\"role\":\"user\"}', '2025-07-30 07:37:24'),
(221, 9, '::1', 'INFO', 'REGISTER_SUCCESS', '{\"user_id\":\"9\"}', '2025-07-30 09:13:01'),
(222, 9, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":9,\"role\":\"user\"}', '2025-07-30 09:13:14'),
(223, 6, '::1', 'CRITICAL', 'ACCOUNT_LOCKED', '{\"user_id\":6}', '2025-07-30 11:41:41'),
(224, 6, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":6}', '2025-07-30 11:41:41'),
(225, 8, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":8,\"role\":\"user\"}', '2025-07-30 11:41:50'),
(226, 8, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":8,\"role\":\"user\"}', '2025-07-30 12:33:59'),
(227, 8, '::1', 'WARN', 'LOGIN_FAIL_WRONG_PASS', '{\"user_id\":8}', '2025-07-30 12:53:13'),
(228, 8, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":8,\"role\":\"user\"}', '2025-07-30 12:53:22'),
(229, 8, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":8,\"role\":\"user\"}', '2025-07-30 13:12:44'),
(230, 8, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":8,\"role\":\"admin\"}', '2025-07-30 13:13:26'),
(231, 8, '::1', 'INFO', 'LOGIN_SUCCESS', '{\"user_id\":8,\"role\":\"admin\"}', '2025-07-30 13:45:21'),
(232, NULL, '::1', 'INFO', 'EMAIL_SEND_SUCCESS', '{\"email\":\"leonghuichun@gmail.com\"}', '2025-07-30 13:55:13'),
(233, 1, '::1', 'INFO', 'PASS_RESET_REQUEST', '{\"user_id\":1}', '2025-07-30 13:55:13'),
(234, NULL, '::1', 'INFO', 'EMAIL_SEND_SUCCESS', '{\"email\":\"leonghuichun@gmail.com\"}', '2025-07-30 14:01:17'),
(235, 1, '::1', 'INFO', 'PASS_RESET_REQUEST', '{\"user_id\":1}', '2025-07-30 14:01:17'),
(236, 1, '::1', 'WARN', 'PASS_RESET_FAIL_VALIDATION', '{\"user_id\":1,\"reason\":\"You cannot reuse one of your last 5 passwords.\"}', '2025-07-30 14:01:44');

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
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `failed_login_attempts`, `account_locked_until`, `created_at`, `balance`) VALUES
(1, 'leong', 'leonghuichun@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$aUYuekZhYS41VW5ycTlxLw$hMul+F0iaV1my6peA5GCY87ZbgsxPUXXVpV+3AlysfU', 'user', 1, NULL, '2025-07-10 06:29:11', 53.50),
(2, 'Admin', 'admin@mixue.com', '$argon2id$v=19$m=65536,t=4,p=1$ZUdCbzQ0RWFiNTVWL1liaA$HRPysuRCoHMhOfRlbHmcKJJ/dtHnVG0M+UzQ4BB9m8g', 'user', 0, NULL, '2025-07-18 11:08:23', 0.00),
(3, 'wang zi zhen', 'zi189wangzhen@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$bEVWMnl3dDF5L2JqNy85Mg$uw+SCQRdzFyBJmzwMHuTdaT/j52J8Fp1aZYKcXUhUE4', 'admin', 0, NULL, '2025-07-18 12:07:48', 0.00),
(4, 'Manager', 'wangzz-jm23@student.tarc.edu.my', '$argon2id$v=19$m=65536,t=4,p=1$LjFFN21uT0pUNjNkdms4aw$FMzI/nW7qS9OzRDavQrmShvGQSCIcmwUisBoG4S87u8', 'manager', 5, '2025-07-30 08:08:01', '2025-07-18 14:22:26', 0.00),
(5, 'user1', 'user1@example.com', '$argon2id$v=19$m=65536,t=4,p=1$S0p6VlgvTjJXVktsaGtoOA$2KbE7EXR98OS7+LOW7xPd1w1b9bmYtkT10mCdQp6hzc', 'user', 0, NULL, '2025-07-22 08:14:54', 6.00),
(6, 'aaa', 'testinggg@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$YlpkRWJGTnFMRm95Nm1pLw$PZ0GjuK3iPEnv3tFWLdX/lJK+ymXsleWBIMu0+co2/c', 'user', 8, '2025-07-30 13:56:41', '2025-07-29 14:33:40', 0.00),
(7, 'LHC', 'leonghc-jm23@student.tarc.edu.my', '$argon2id$v=19$m=65536,t=4,p=1$RWt6MEhCMllFSU5IZjNtaw$10QZBfrkQe4CJ7L+mN/f12/1z2UFzyy038KUhsQhUGo', 'user', 1, NULL, '2025-07-30 05:45:08', 0.00),
(8, 'abcd', 'huichun200204@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$bTU1WlZSZ1ouZ3o0UlNvYw$vbZVojTLidxJMhxx4LV9e/piz28DYi9yzQBmycTXnK8', 'admin', 0, NULL, '2025-07-30 06:09:46', 191.00),
(9, 'bbb', 'bbb@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$NUZldjFiNnYzVEJxczFkTw$WVNg4EE+7ZW9WBuRGJYwLBpEwxfXSYUacoc+soqvsdE', 'admin', 0, NULL, '2025-07-30 09:13:01', 0.00);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `notification_logs`
--
ALTER TABLE `notification_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_details_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `password_history`
--
ALTER TABLE `password_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `reloads`
--
ALTER TABLE `reloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `security_logs`
--
ALTER TABLE `security_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=237;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 15, 2025 at 03:40 PM
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
-- Database: `project_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','in_progress','completed') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_id`, `name`, `description`, `start_date`, `end_date`, `created_at`, `status`) VALUES
(1, 'Website Development', 'Developing an e-commerce website', '2025-03-02', '2025-03-20', '2025-03-14 12:04:42', 'in_progress'),
(2, 'Mobile App', 'Building a mobile app for online shopping', '2025-02-20', '2025-08-30', '2025-03-14 12:04:42', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `project_finances`
--

CREATE TABLE `project_finances` (
  `finance_id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `description` text DEFAULT NULL,
  `finance_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_finances`
--

INSERT INTO `project_finances` (`finance_id`, `project_id`, `amount`, `type`, `description`, `finance_date`, `created_at`) VALUES
(1, 1, 10000.00, 'income', 'Initial funding for the website', '2025-01-15', '2025-03-14 12:04:42'),
(2, 1, 5000.00, 'expense', 'Domain & Hosting', '2025-02-01', '2025-03-14 12:04:42'),
(3, 2, 15000.00, 'income', 'Investor funding for mobile app', '2025-03-05', '2025-03-14 12:04:42'),
(8, 2, 245.00, 'expense', 'sdf', '0000-00-00', '2025-03-15 08:37:33'),
(11, 1, 2345.00, 'income', '2asdaasd', '2025-03-15', '2025-03-15 10:24:08'),
(14, 1, 23.00, 'income', 'asdad', '2025-03-15', '2025-03-15 13:57:21');

-- --------------------------------------------------------

--
-- Table structure for table `project_profit`
--

CREATE TABLE `project_profit` (
  `profit_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `net_profit` decimal(10,2) NOT NULL DEFAULT 0.00,
  `distributed_profit` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_profit`
--

INSERT INTO `project_profit` (`profit_id`, `project_id`, `net_profit`, `distributed_profit`, `created_at`) VALUES
(1, 1, 5000.00, 5000.00, '2025-03-14 12:04:42'),
(2, 2, 10000.00, 3000.00, '2025-03-14 12:04:42');

-- --------------------------------------------------------

--
-- Table structure for table `project_profit_distribution`
--

CREATE TABLE `project_profit_distribution` (
  `distribution_id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `status` enum('pending','paid') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_profit_distribution`
--

INSERT INTO `project_profit_distribution` (`distribution_id`, `project_id`, `user_id`, `amount`, `status`, `created_at`) VALUES
(1, 1, 1, 1000.00, 'paid', '2025-03-14 12:04:42'),
(2, 1, 2, 500.00, 'pending', '2025-03-14 12:04:42'),
(3, 2, 3, 2000.00, 'paid', '2025-03-14 12:04:42');

-- --------------------------------------------------------

--
-- Table structure for table `project_users`
--

CREATE TABLE `project_users` (
  `project_user_id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `contribution_percentage` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_users`
--

INSERT INTO `project_users` (`project_user_id`, `project_id`, `user_id`, `contribution_percentage`) VALUES
(8, 1, 1, 0.00),
(9, 1, 2, 0.00),
(10, 1, 3, 0.00),
(11, 2, 1, 0.00),
(12, 2, 2, 0.00),
(13, 2, 3, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `transaction_type` enum('income','expense','deposit','withdrawal') NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `recorded_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `user_id`, `project_id`, `amount`, `transaction_type`, `category`, `description`, `transaction_date`, `recorded_by`) VALUES
(1, 1, 1, 5000.00, 'deposit', 'Investment', 'Alice invested in website development', '2025-03-14 12:04:42', NULL),
(3, 3, 2, 10000.00, 'deposit', 'Investment', 'Charlie funded mobile app', '2025-03-14 12:04:42', NULL),
(4, NULL, 1, 120.00, 'income', NULL, 'asd', '2025-03-14 12:26:56', NULL),
(5, NULL, 2, 20200.00, 'income', NULL, 'dss', '2025-03-14 12:27:06', NULL),
(6, NULL, 2, 200.00, 'expense', NULL, 'asd', '2025-03-14 12:27:12', NULL),
(7, 1, NULL, 20.00, 'deposit', 'wallet', 'Wallet deposit', '2025-03-14 13:18:59', NULL),
(8, 1, NULL, 23.00, 'deposit', NULL, NULL, '2025-03-14 14:16:09', NULL),
(9, NULL, NULL, 233.00, 'expense', NULL, 'adsas', '2025-03-14 14:34:45', NULL),
(11, 2, NULL, 566.00, 'withdrawal', NULL, NULL, '2025-03-14 14:35:29', NULL),
(12, 2, NULL, 5660.00, 'deposit', NULL, 'asdda', '2025-03-14 14:42:27', NULL),
(13, 2, NULL, 226.00, 'withdrawal', NULL, 'asd', '2025-03-14 14:42:50', NULL),
(14, 2, NULL, 232.00, 'withdrawal', NULL, 'aada', '2025-03-14 14:43:29', NULL),
(15, 1, NULL, 400.00, 'deposit', NULL, 'ada', '2025-03-14 14:50:35', NULL),
(16, 1, NULL, 23.00, 'withdrawal', NULL, 'asd', '2025-03-14 14:51:52', NULL),
(23, 1, NULL, 23.00, 'deposit', NULL, 'asdaasdas', '2025-03-14 17:59:27', NULL),
(24, NULL, 1, 2345.00, 'income', NULL, '2asdaasd', '2025-03-15 08:37:18', NULL),
(25, NULL, 2, 245.00, 'expense', NULL, 'sdf', '2025-03-15 08:37:33', NULL),
(26, 1, NULL, 3456.00, 'deposit', NULL, 'asda', '2025-03-15 08:48:44', NULL),
(30, 3, NULL, 2.00, 'deposit', NULL, 'sdf', '2025-03-15 08:54:40', NULL),
(33, NULL, NULL, 2343.00, 'expense', NULL, 'asd', '2025-03-15 10:27:17', NULL),
(34, NULL, NULL, 234.00, 'income', NULL, 'asda', '2025-03-15 09:22:38', NULL),
(35, NULL, NULL, 1111.00, 'income', NULL, 'asda', '2025-03-15 09:23:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','employee','investor') NOT NULL,
  `wallet_balance` decimal(12,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password_hash`, `role`, `wallet_balance`, `created_at`) VALUES
(1, 'Alice Johnsonas', 'alice@example.com', 'hashedpassword1', 'admin', 5020.00, '2025-03-14 12:04:42'),
(2, 'Bob Smith', 'bob@example.com', 'hashedpassword2', 'employee', 2000.00, '2025-03-14 12:04:42'),
(3, 'Charlie Brown', 'charlie@example.com', 'hashedpassword3', 'investor', 10000.00, '2025-03-14 12:04:42');

-- --------------------------------------------------------

--
-- Table structure for table `user_wallets`
--

CREATE TABLE `user_wallets` (
  `wallet_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `transaction_type` enum('deposit','withdrawal') NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_wallets`
--

INSERT INTO `user_wallets` (`wallet_id`, `user_id`, `amount`, `transaction_type`, `status`, `transaction_date`) VALUES
(1, 1, 3223.67, 'deposit', 'approved', '2025-03-14 12:04:42'),
(2, 2, 6636.67, 'deposit', 'approved', '2025-03-14 12:04:42'),
(3, 3, 12536.67, 'deposit', 'approved', '2025-03-14 12:04:42'),
(9, 1, 223.00, 'withdrawal', 'pending', '2025-03-15 14:16:43');

-- --------------------------------------------------------

--
-- Table structure for table `withdrawal_requests`
--

CREATE TABLE `withdrawal_requests` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `withdrawal_requests`
--

INSERT INTO `withdrawal_requests` (`request_id`, `user_id`, `amount`, `status`, `requested_at`, `processed_at`) VALUES
(1, 1, 1000.00, 'approved', '2025-03-14 12:04:42', '2025-03-14 12:04:42'),
(2, 2, 500.00, 'pending', '2025-03-14 12:04:42', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`);

--
-- Indexes for table `project_finances`
--
ALTER TABLE `project_finances`
  ADD PRIMARY KEY (`finance_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `project_profit`
--
ALTER TABLE `project_profit`
  ADD PRIMARY KEY (`profit_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `project_profit_distribution`
--
ALTER TABLE `project_profit_distribution`
  ADD PRIMARY KEY (`distribution_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `project_users`
--
ALTER TABLE `project_users`
  ADD PRIMARY KEY (`project_user_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_wallets`
--
ALTER TABLE `user_wallets`
  ADD PRIMARY KEY (`wallet_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `withdrawal_requests`
--
ALTER TABLE `withdrawal_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `project_finances`
--
ALTER TABLE `project_finances`
  MODIFY `finance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `project_profit`
--
ALTER TABLE `project_profit`
  MODIFY `profit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `project_profit_distribution`
--
ALTER TABLE `project_profit_distribution`
  MODIFY `distribution_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `project_users`
--
ALTER TABLE `project_users`
  MODIFY `project_user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_wallets`
--
ALTER TABLE `user_wallets`
  MODIFY `wallet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `withdrawal_requests`
--
ALTER TABLE `withdrawal_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `project_finances`
--
ALTER TABLE `project_finances`
  ADD CONSTRAINT `project_finances_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE;

--
-- Constraints for table `project_profit`
--
ALTER TABLE `project_profit`
  ADD CONSTRAINT `project_profit_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE;

--
-- Constraints for table `project_profit_distribution`
--
ALTER TABLE `project_profit_distribution`
  ADD CONSTRAINT `project_profit_distribution_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_profit_distribution_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `project_users`
--
ALTER TABLE `project_users`
  ADD CONSTRAINT `project_users_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE SET NULL;

--
-- Constraints for table `user_wallets`
--
ALTER TABLE `user_wallets`
  ADD CONSTRAINT `user_wallets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `withdrawal_requests`
--
ALTER TABLE `withdrawal_requests`
  ADD CONSTRAINT `withdrawal_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

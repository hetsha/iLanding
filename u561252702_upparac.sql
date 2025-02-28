-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 28, 2025 at 04:39 PM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u561252702_upparac`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `last_login` text NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `last_login`, `username`, `password`, `created_at`) VALUES
(1, '2025-02-28 16:28:18', 'admin', '$2y$10$iS7SoSJVaErzIs/FYsk3C.z.YxVgLHfKLkzAWyAFlcXv/XRUb/wuO', '2025-02-02 07:03:11'),
(2, '2025-02-06 13:12:53', 'het', '$2y$10$j7s3Yfr/.HZ5/YDi5ULpiOlwm5waKNxSEvKorsWiNOD22JNtIFtr2', '2025-02-06 04:56:22');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `business_role` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `card_pic` varchar(255) DEFAULT NULL,
  `date_added` timestamp NULL DEFAULT current_timestamp(),
  `added_by` enum('hetd','het b','jainam','akshat') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`company_id`, `company_name`, `business_role`, `address`, `contact_number`, `card_pic`, `date_added`, `added_by`) VALUES
(1, 'NAMAH WELLNESS', 'NEIGHBORHOOD PHARMACY', 'SASTRINAGAR', '07947413324', '', '2025-02-26 07:19:01', 'het b'),
(2, 'JANAK MEDICAL', 'MEDICAL', 'NAVA VADAJ', '9879838237', '', '2025-02-26 07:19:48', 'het b'),
(3, 'MAHAVIR MEDICAL', 'MEDICAL', 'BHIMJIPURA', '9998673348', '', '2025-02-26 07:21:50', 'het b'),
(4, 'SUN & STEP CAKE MONGI', 'CAKE', 'BHIMJIPURS', '9725080650', '', '2025-02-26 07:23:03', 'het b'),
(5, 'GAYTRI MEDICAL', 'MEDICAL', 'VYASHVADI', '8758141649', '', '2025-02-26 07:23:47', 'het b'),
(6, 'GAYATRI MED', ' MEDICAL', 'NAVA VADAJ', '67947124783', '', '2025-02-26 07:24:45', 'het b'),
(7, 'GAJANAND CAKE', 'CAKE ', 'VYASHVADI', '8141115511', '', '2025-02-26 07:25:29', 'het b'),
(8, 'ASHOK NAMKEEN', 'NAMKEEN', 'NAVA VADAJ', '9824141917', '', '2025-02-26 07:27:20', 'het b'),
(9, 'JAY SHREE MAHAKALI TRAVELS', 'TRAVELS', 'NAVA VADAJ', '99988091.0', '', '2025-02-26 07:31:19', 'het b'),
(10, 'SHREE RAM MOTORS', 'MOTORS', 'NAVA VADAJ', '9723133743', '', '2025-02-26 07:32:27', 'het b'),
(11, 'PATIDAR AUTO CUNSALTANT', '2ND HAND CAR', 'NARANPURA', '9726333111', '', '2025-02-26 07:33:53', 'het b'),
(12, 'SUN CAR DECOR', 'CAR', 'NARSNPURA', '7777998477', '', '2025-02-26 07:34:38', 'het b'),
(13, 'RS MOTOTLAND', 'CAR ', 'COMMERCE 6ROAD', '9327739971', '', '2025-02-26 07:35:28', 'het b'),
(14, 'BAPASITARAM MOTORS', 'CAR', 'SOLA', '9913710064', '', '2025-02-26 07:36:09', 'het b');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `subject`, `message`, `created_at`, `is_read`) VALUES
(2, '', '', '', '', '2025-02-10 11:36:11', 1),
(3, 'Lucy Gordon', 'lucygordon.mkt@gmail.com', 'SEO expansion for website', 'Hello upparac.com,\r\n\r\nBring in 10 to 20 organic clients who value your services using white-hat SEO techniques.\r\n\r\nWould you like to learn more about it?\r\n\r\nWell wishes,\r\nLucy Gordon | Digital Marketing Manager\r\n\r\n\r\n\r\nNote: - If you’re not Interested in our Services, send us  &quot;opt-out&quot;', '2025-02-20 14:16:36', 1),
(4, 'Anky ', 'info@letsgetuoptimize.com', 'Re: Increase google organic ranking &amp; SEO', 'Hey team upparac.com,\r\n\r\nI would like to discuss SEO!\r\n\r\nI can help your website to get on first page of Google and increase the number of leads and sales you are getting from your website.\r\n\r\nMay I send you a quote &amp; price list?\r\n\r\nBests Regards,\r\nAnky\r\nLets Get You Optimize\r\nAccounts Manager\r\nwww.letsgetuoptimize.com\r\nPhone No: +1 (949) 508-0277', '2025-02-26 02:12:15', 1);

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `portfolio`
--

CREATE TABLE `portfolio` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_projects`
--

CREATE TABLE `portfolio_projects` (
  `project_id` int(11) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `status` enum('current','break','completed') NOT NULL,
  `project_description` text NOT NULL,
  `project_link` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image1` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `image4` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `portfolio_projects`
--

INSERT INTO `portfolio_projects` (`project_id`, `project_name`, `status`, `project_description`, `project_link`, `created_at`, `image1`, `image2`, `image3`, `image4`) VALUES
(1, 'ora watch', 'current', 'ora watch website that sell the watches online ', 'http://orawatch.free.nf/', '2025-02-02 07:07:18', '1738480038_Screenshot 2025-02-02 123659.png', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` enum('current','break','completed') DEFAULT 'current',
  `description` text DEFAULT NULL,
  `total_income` decimal(10,2) DEFAULT 0.00,
  `total_expenses` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_distributed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `name`, `status`, `description`, `total_income`, `total_expenses`, `created_at`, `is_distributed`) VALUES
(1, 'bhavya packaging', 'current', 'bhavya packaging of pacakging matrials product lsiting page,home about ,contact ', 5000.00, 0.00, '2025-02-06 04:58:07', 0);

-- --------------------------------------------------------

--
-- Table structure for table `project_expenses`
--

CREATE TABLE `project_expenses` (
  `expense_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `expense_amount` decimal(10,2) NOT NULL,
  `expense_description` text DEFAULT NULL,
  `expense_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_income`
--

CREATE TABLE `project_income` (
  `income_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `income_amount` decimal(10,2) NOT NULL,
  `income_description` text DEFAULT NULL,
  `income_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_users`
--

CREATE TABLE `project_users` (
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `profit` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('Designer','Connector','Coder','Marketing') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `created_at`) VALUES
(1, 'Het', 'hetshah6315@gmail.com', 'Coder', '2025-02-04 18:05:25'),
(2, 'het shah', 'het@gmail.com', 'Designer', '2025-02-04 18:06:00'),
(3, 'akshat', 'akshatjshah2005@gmail.com', 'Connector', '2025-02-04 18:06:16'),
(4, 'jainam', 'jainam@gmail.com', 'Marketing', '2025-02-04 18:06:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `portfolio`
--
ALTER TABLE `portfolio`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `portfolio_projects`
--
ALTER TABLE `portfolio_projects`
  ADD PRIMARY KEY (`project_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_expenses`
--
ALTER TABLE `project_expenses`
  ADD PRIMARY KEY (`expense_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `project_income`
--
ALTER TABLE `project_income`
  ADD PRIMARY KEY (`income_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `project_users`
--
ALTER TABLE `project_users`
  ADD PRIMARY KEY (`project_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `portfolio`
--
ALTER TABLE `portfolio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `portfolio_projects`
--
ALTER TABLE `portfolio_projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `project_expenses`
--
ALTER TABLE `project_expenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_income`
--
ALTER TABLE `project_income`
  MODIFY `income_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

--
-- Constraints for table `project_expenses`
--
ALTER TABLE `project_expenses`
  ADD CONSTRAINT `project_expenses_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_income`
--
ALTER TABLE `project_income`
  ADD CONSTRAINT `project_income_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_users`
--
ALTER TABLE `project_users`
  ADD CONSTRAINT `project_users_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `project_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

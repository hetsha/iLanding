-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 15, 2025 at 06:24 PM
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

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `last_login`, `created_at`) VALUES
(1, 'admin', '$2y$10$iS7SoSJVaErzIs/FYsk3C.z.YxVgLHfKLkzAWyAFlcXv/XRUb/wuO', '2025-03-15 18:02:58', '2025-02-02 07:03:11'),
(2, 'het', '$2y$10$j7s3Yfr/.HZ5/YDi5ULpiOlwm5waKNxSEvKorsWiNOD22JNtIFtr2', '2025-02-06 13:12:53', '2025-02-06 04:56:22');

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`company_id`, `company_name`, `business_role`, `address`, `contact_number`, `card_pic`, `added_by`, `date_added`) VALUES
(1, 'NAMAH WELLNESS', 'NEIGHBORHOOD PHARMACY', 'SASTRINAGAR', '07947413324', '', 'het b', '2025-02-26 07:19:01'),
(2, 'JANAK MEDICAL', 'MEDICAL', 'NAVA VADAJ', '9879838237', '', 'het b', '2025-02-26 07:19:48'),
(3, 'MAHAVIR MEDICAL', 'MEDICAL', 'BHIMJIPURA', '9998673348', '', 'het b', '2025-02-26 07:21:50'),
(4, 'SUN & STEP CAKE MONGI', 'CAKE', 'BHIMJIPURS', '9725080650', '', 'het b', '2025-02-26 07:23:03'),
(5, 'GAYTRI MEDICAL', 'MEDICAL', 'VYASHVADI', '8758141649', '', 'het b', '2025-02-26 07:23:47'),
(6, 'GAYATRI MED', ' MEDICAL', 'NAVA VADAJ', '67947124783', '', 'het b', '2025-02-26 07:24:45'),
(7, 'GAJANAND CAKE', 'CAKE ', 'VYASHVADI', '8141115511', '', 'het b', '2025-02-26 07:25:29'),
(8, 'ASHOK NAMKEEN', 'NAMKEEN', 'NAVA VADAJ', '9824141917', '', 'het b', '2025-02-26 07:27:20'),
(9, 'JAY SHREE MAHAKALI TRAVELS', 'TRAVELS', 'NAVA VADAJ', '99988091.0', '', 'het b', '2025-02-26 07:31:19'),
(10, 'SHREE RAM MOTORS', 'MOTORS', 'NAVA VADAJ', '9723133743', '', 'het b', '2025-02-26 07:32:27'),
(11, 'PATIDAR AUTO CUNSALTANT', '2ND HAND CAR', 'NARANPURA', '9726333111', '', 'het b', '2025-02-26 07:33:53'),
(12, 'SUN CAR DECOR', 'CAR', 'NARSNPURA', '7777998477', '', 'het b', '2025-02-26 07:34:38'),
(13, 'RS MOTOTLAND', 'CAR ', 'COMMERCE 6ROAD', '9327739971', '', 'het b', '2025-02-26 07:35:28'),
(14, 'BAPASITARAM MOTORS', 'CAR', 'SOLA', '9913710064', '', 'het b', '2025-02-26 07:36:09'),
(15, 'padmavati gruh udhyog', 'gruh udhyog', 'jivraj', '9824465444', '', 'hetd', '2025-03-11 12:02:31');

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `subject`, `is_read`, `created_at`, `message`) VALUES
(2, '', '', '', 1, '2025-02-10 11:36:11', ''),
(3, 'Lucy Gordon', 'lucygordon.mkt@gmail.com', 'SEO expansion for website', 1, '2025-02-20 14:16:36', 'Hello upparac.com,\r\n\r\nBring in 10 to 20 organic clients who value your services using white-hat SEO techniques.\r\n\r\nWould you like to learn more about it?\r\n\r\nWell wishes,\r\nLucy Gordon | Digital Marketing Manager\r\n\r\n\r\n\r\nNote: - If you’re not Interested in our Services, send us  &quot;opt-out&quot;'),
(4, 'Anky ', 'info@letsgetuoptimize.com', 'Re: Increase google organic ranking &amp; SEO', 1, '2025-02-26 02:12:15', 'Hey team upparac.com,\r\n\r\nI would like to discuss SEO!\r\n\r\nI can help your website to get on first page of Google and increase the number of leads and sales you are getting from your website.\r\n\r\nMay I send you a quote &amp; price list?\r\n\r\nBests Regards,\r\nAnky\r\nLets Get You Optimize\r\nAccounts Manager\r\nwww.letsgetuoptimize.com\r\nPhone No: +1 (949) 508-0277'),
(5, 'Paul S', 'letsgetuoptimize@gmail.com', 'Re: Let&#039;s Improve Your SEO Rankings! ', 1, '2025-03-02 11:50:50', 'Hey team upparac.com,\r\n\r\nHope your doing well!\r\n\r\nI just following your website and realized that despite having a good design; but it was not ranking high on any of the Search Engines (Google, Yahoo &amp; Bing) for most of the keywords related to your business.\r\n\r\nWe can place your website on Google&#039;s 1st page.\r\n\r\n*  Top ranking on Google search!\r\n*  Improve website clicks and views!\r\n*  Increase Your Leads, clients &amp; Revenue!\r\n\r\nInterested? Please provide your name, contact information, and email.\r\n\r\nWell wishes,\r\nPaul S\r\n+1 (949) 313-8897\r\nPaul S| Lets Get You Optimize\r\nSr SEO consultant\r\nwww.letsgetuoptimize.com\r\nPhone No: +1 (949) 313-8897'),
(6, 'Paul S', 'letsgetuoptimize@gmail.com', 'Re: SEO Packages ', 1, '2025-03-04 06:22:51', 'Hey team upparac.com,\r\n\r\nHope your doing well!\r\n\r\nI just following your website and realized that despite having a good design; but it was not ranking high on any of the Search Engines (Google, Yahoo &amp; Bing) for most of the keywords related to your business.\r\n\r\nWe can place your website on Google&#039;s 1st page.\r\n\r\n*  Top ranking on Google search!\r\n*  Improve website clicks and views!\r\n*  Increase Your Leads, clients &amp; Revenue!\r\n\r\nInterested? Please provide your name, contact information, and email.\r\n\r\nWell wishes,\r\nPaul S\r\n+1 (949) 313-8897\r\nPaul S| Lets Get You Optimize\r\nSr SEO consultant\r\nwww.letsgetuoptimize.com\r\nPhone No: +1 (949) 313-8897');

--
-- Dumping data for table `portfolio_projects`
--

INSERT INTO `portfolio_projects` (`project_id`, `project_name`, `status`, `project_description`, `project_link`, `created_at`, `image1`, `image2`, `image3`, `image4`) VALUES
(1, 'ora watch', 'current', 'ora watch website that sell the watches online ', 'http://orawatch.free.nf/', '2025-02-02 07:07:18', '1738480038_Screenshot 2025-02-02 123659.png', NULL, NULL, NULL),
(3, 'cake shop', 'current', 'cake shop website that provides cakes to customers ', 'https://wheat-hare-976288.hostingersite.com/', '2025-03-11 14:38:53', '1741703933_Screenshot 2025-03-11 200613.png', NULL, NULL, NULL),
(4, 'grocery store', 'current', 'grocery store website that build in php with full customized desgin', 'https://linen-sheep-454084.hostingersite.com/', '2025-03-11 14:45:31', '1741704331_Screenshot 2025-03-11 201334.png', NULL, NULL, NULL),
(5, 'coida technology', 'current', 'coid technology team website to manage business', 'https://coidabiz.web.app/', '2025-03-11 14:48:20', '1741704500_Screenshot 2025-03-11 201624.png', NULL, NULL, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

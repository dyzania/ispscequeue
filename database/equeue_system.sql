-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 14, 2026 at 07:23 PM
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
-- Database: `equeue_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chatbot_data`
--

CREATE TABLE `chatbot_data` (
  `id` int(11) NOT NULL,
  `content` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chatbot_data`
--

INSERT INTO `chatbot_data` (`id`, `content`, `updated_at`) VALUES
(1, 'We are The ISPSC Main Campus Registrar. Located at Admin Building ISPSC Main, San Nicolas, Candon City, Ilocos Sur.\r\n\r\nRequest of Grades {\r\n* Price: php50 per semester\r\n* Requirements: Valid ID, OTR, Route Sheet\r\n}', '2026-02-14 17:00:52');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `window_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5 or `rating` is null),
  `comment` text DEFAULT NULL,
  `sentiment` enum('positive','neutral','negative','very_positive','very_negative') DEFAULT NULL,
  `sentiment_score` decimal(5,4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `ticket_id`, `user_id`, `window_id`, `rating`, `comment`, `sentiment`, `sentiment_score`, `created_at`) VALUES
(27, 38, 23, 14, NULL, 'nicee', 'positive', 0.4000, '2026-02-14 16:05:54'),
(28, 39, 23, 14, NULL, 'Bxbx', 'neutral', 0.0000, '2026-02-14 16:15:35'),
(29, 40, 23, 14, NULL, 'Very very nice', 'positive', 0.4000, '2026-02-14 16:17:46'),
(30, 41, 23, 14, NULL, 'super good', 'positive', 0.4000, '2026-02-14 16:20:22'),
(31, 42, 23, 14, NULL, 'angasss', 'neutral', 0.0000, '2026-02-14 16:21:50'),
(32, 43, 23, 14, NULL, 'Ayossss', 'neutral', 0.0000, '2026-02-14 16:23:05'),
(33, 44, 23, 14, NULL, 'Hhhhh', 'neutral', 0.0000, '2026-02-14 16:28:23'),
(34, 45, 23, 14, NULL, 'Okayyyy', 'positive', 0.4000, '2026-02-14 16:30:39'),
(35, 46, 23, 14, NULL, 'very good', 'positive', 0.4000, '2026-02-14 16:48:38'),
(36, 47, 23, 14, NULL, 'Genesis', 'neutral', 0.0000, '2026-02-14 17:33:04'),
(37, 48, 23, 14, NULL, 'Niceee', 'positive', 0.4000, '2026-02-14 17:34:26'),
(38, 49, 23, 14, NULL, 'Niceee', 'positive', 0.4000, '2026-02-14 17:38:43'),
(39, 50, 23, 14, NULL, 'Niceee', 'positive', 0.4000, '2026-02-14 17:47:38'),
(40, 51, 23, 14, NULL, 'niceee', 'positive', 0.4000, '2026-02-14 17:50:32'),
(41, 52, 23, 14, NULL, 'Niceeee', 'neutral', 0.0000, '2026-02-14 17:57:26'),
(42, 53, 23, 14, NULL, 'okayyy', 'positive', 0.4000, '2026-02-14 18:01:03'),
(43, 54, 23, 14, NULL, 'Goodss', 'positive', 0.4000, '2026-02-14 18:02:32'),
(44, 55, 23, 14, NULL, 'Lupettt', 'neutral', 0.0000, '2026-02-14 18:16:02'),
(45, 56, 23, 14, NULL, 'Goods', 'positive', 0.4000, '2026-02-14 18:19:05'),
(46, 57, 23, 14, NULL, 'Niceeee', 'neutral', 0.0000, '2026-02-14 18:20:24');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `type` enum('ticket_created','turn_next','serving','now_serving','completed','cancelled') DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `ticket_id`, `type`, `message`, `is_read`, `created_at`) VALUES
(121, 23, 38, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-14 15:59:43'),
(122, 23, 38, 'now_serving', 'You are now being served.', 1, '2026-02-14 16:01:34'),
(123, 23, 38, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:01:37'),
(124, 23, 38, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:01:39'),
(125, 23, 38, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:01:39'),
(126, 23, 38, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:01:40'),
(127, 23, 38, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:01:40'),
(128, 23, 38, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:01:40'),
(129, 23, 38, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:01:40'),
(130, 23, 38, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:01:41'),
(131, 23, 38, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:01:41'),
(132, 23, 38, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:01:41'),
(133, 23, 38, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:01:41'),
(134, 23, 38, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:01:41'),
(135, 23, 38, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:01:42'),
(136, 23, 38, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:01:42'),
(137, 23, 39, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-14 16:06:03'),
(138, 23, 39, 'now_serving', 'You are now being served.', 1, '2026-02-14 16:06:10'),
(139, 23, 39, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:06:17'),
(140, 23, 39, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:06:17'),
(141, 23, 39, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:06:18'),
(142, 23, 39, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:06:18'),
(143, 23, 39, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:06:19'),
(144, 23, 39, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:06:19'),
(145, 23, 39, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:06:19'),
(146, 23, 39, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:06:19'),
(147, 23, 39, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:06:19'),
(148, 23, 39, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:06:19'),
(149, 23, 39, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:06:20'),
(150, 23, 39, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:06:20'),
(151, 23, 39, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:06:21'),
(152, 23, 39, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:06:21'),
(153, 23, 39, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:06:21'),
(154, 23, 39, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:06:22'),
(155, 23, 39, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:06:22'),
(156, 23, 39, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:06:22'),
(157, 23, 39, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:06:28'),
(158, 23, 39, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:06:29'),
(159, 23, 40, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-14 16:15:43'),
(160, 23, 40, 'now_serving', 'You are now being served.', 1, '2026-02-14 16:16:06'),
(161, 23, 40, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:17:13'),
(162, 23, 41, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-14 16:17:56'),
(163, 23, 41, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:18:20'),
(164, 23, 42, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-14 16:20:45'),
(165, 23, 42, 'now_serving', 'You are now being served.', 1, '2026-02-14 16:21:21'),
(166, 23, 42, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:21:37'),
(167, 23, 43, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-14 16:22:06'),
(168, 23, 43, 'now_serving', 'You are now being served.', 1, '2026-02-14 16:22:44'),
(169, 23, 43, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:22:52'),
(170, 23, 44, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-14 16:27:38'),
(171, 23, 44, 'now_serving', 'You are now being served.', 1, '2026-02-14 16:27:55'),
(172, 23, 44, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:27:56'),
(173, 23, 45, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-14 16:29:21'),
(174, 23, 45, 'now_serving', 'You are now being served.', 1, '2026-02-14 16:29:42'),
(175, 23, 45, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:30:26'),
(176, 23, 46, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-14 16:46:56'),
(177, 23, 46, 'now_serving', 'You are now being served.', 1, '2026-02-14 16:47:16'),
(178, 23, 46, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 16:47:29'),
(179, 23, 47, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-14 17:22:58'),
(180, 23, 47, 'now_serving', 'You are now being served.', 1, '2026-02-14 17:23:19'),
(181, 23, 47, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 17:23:56'),
(182, 23, 48, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-14 17:33:37'),
(183, 23, 48, 'now_serving', 'You are now being served.', 1, '2026-02-14 17:33:49'),
(184, 23, 48, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 17:33:54'),
(185, 23, 49, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-14 17:36:52'),
(186, 23, 49, 'now_serving', 'You are now being served.', 1, '2026-02-14 17:37:04'),
(187, 23, 49, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 17:38:33'),
(188, 23, 50, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-14 17:41:11'),
(189, 23, 50, 'now_serving', 'You are now being served.', 1, '2026-02-14 17:41:26'),
(190, 23, 50, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 17:42:23'),
(191, 23, 51, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-14 17:47:54'),
(192, 23, 51, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 17:49:17'),
(193, 23, 52, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-14 17:51:05'),
(194, 23, 52, 'now_serving', 'You are now being served.', 1, '2026-02-14 17:52:39'),
(195, 23, 52, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 17:54:02'),
(196, 23, 53, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-14 17:58:32'),
(197, 23, 53, 'now_serving', 'You are now being served.', 1, '2026-02-14 17:58:44'),
(198, 23, 53, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 17:58:52'),
(199, 23, 54, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-14 18:01:15'),
(200, 23, 54, 'now_serving', 'You are now being served.', 1, '2026-02-14 18:01:26'),
(201, 23, 54, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 18:01:37'),
(202, 23, 55, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-14 18:12:51'),
(203, 23, 55, 'now_serving', 'You are now being served.', 1, '2026-02-14 18:12:59'),
(204, 23, 55, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 18:13:53'),
(205, 23, 56, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-14 18:16:16'),
(206, 23, 56, 'now_serving', 'You are now being served.', 1, '2026-02-14 18:16:25'),
(207, 23, 56, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 18:16:36'),
(208, 23, 57, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-14 18:19:29'),
(209, 23, 57, 'now_serving', 'You are now being served.', 1, '2026-02-14 18:19:35'),
(210, 23, 57, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 18:19:47'),
(211, 23, 58, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-14 18:22:24'),
(212, 23, 58, 'now_serving', 'You are now being served.', 1, '2026-02-14 18:22:40'),
(213, 23, 58, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-14 18:22:51');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `service_code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `estimated_time` int(11) DEFAULT 10,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `service_code`, `description`, `requirements`, `estimated_time`, `is_active`, `created_at`, `updated_at`) VALUES
(14, 'Certificate of Grades', 'COG', '', 'Route Sheet\r\nValid ID\r\nCashier Receipt', 5, 1, '2026-02-14 02:22:06', '2026-02-14 07:40:12'),
(15, 'Request of Diploma', 'DPLM', '', 'Official Transcript of Records', 30, 1, '2026-02-14 02:23:01', '2026-02-14 02:23:01'),
(16, 'Good Moral Character', 'GMC', 'Good Moral Character', 'Valid ID\r\nOfficial Transcript of Records', 25, 1, '2026-02-14 07:39:51', '2026-02-14 07:39:51');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `ticket_number` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `window_id` int(11) DEFAULT NULL,
  `status` enum('waiting','called','serving','completed','cancelled') DEFAULT 'waiting',
  `staff_notes` text DEFAULT NULL,
  `queue_position` int(11) DEFAULT NULL,
  `called_at` timestamp NULL DEFAULT NULL,
  `served_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `ticket_number`, `user_id`, `service_id`, `window_id`, `status`, `staff_notes`, `queue_position`, `called_at`, `served_at`, `completed_at`, `created_at`, `updated_at`, `is_archived`) VALUES
(38, 'COG-20260214-0001', 23, 14, 14, 'completed', NULL, NULL, '2026-02-14 15:59:43', '2026-02-14 16:01:34', '2026-02-14 16:01:42', '2026-02-14 15:54:53', '2026-02-14 16:01:42', 0),
(39, 'COG-20260215-0001', 23, 14, 14, 'completed', NULL, NULL, '2026-02-14 16:06:03', '2026-02-14 16:06:10', '2026-02-14 16:06:29', '2026-02-14 16:05:57', '2026-02-14 16:06:36', 1),
(40, 'COG-20260215-0002', 23, 14, 14, 'completed', NULL, NULL, '2026-02-14 16:15:43', '2026-02-14 16:16:06', '2026-02-14 16:17:13', '2026-02-14 16:15:38', '2026-02-14 16:17:13', 0),
(41, 'COG-20260215-0003', 23, 14, 14, 'completed', NULL, NULL, '2026-02-14 16:17:56', NULL, '2026-02-14 16:18:20', '2026-02-14 16:17:51', '2026-02-14 16:18:20', 1),
(42, 'DPLM-20260215-0001', 23, 15, 14, 'completed', NULL, NULL, '2026-02-14 16:20:45', '2026-02-14 16:21:21', '2026-02-14 16:21:37', '2026-02-14 16:20:25', '2026-02-14 16:21:37', 0),
(43, 'COG-20260215-0004', 23, 14, 14, 'completed', NULL, NULL, '2026-02-14 16:22:06', '2026-02-14 16:22:44', '2026-02-14 16:22:52', '2026-02-14 16:21:52', '2026-02-14 16:22:52', 0),
(44, 'COG-20260215-0005', 23, 14, 14, 'completed', NULL, NULL, '2026-02-14 16:27:38', '2026-02-14 16:27:55', '2026-02-14 16:27:56', '2026-02-14 16:27:23', '2026-02-14 16:27:56', 0),
(45, 'COG-20260215-0006', 23, 14, 14, 'completed', NULL, NULL, '2026-02-14 16:29:21', '2026-02-14 16:29:42', '2026-02-14 16:30:26', '2026-02-14 16:29:14', '2026-02-14 16:30:26', 0),
(46, 'COG-20260215-0007', 23, 14, 14, 'completed', NULL, NULL, '2026-02-14 16:46:56', '2026-02-14 16:47:16', '2026-02-14 16:47:29', '2026-02-14 16:46:34', '2026-02-14 16:47:29', 0),
(47, 'COG-20260215-0008', 23, 14, 14, 'completed', 'kindly receive your document here', NULL, '2026-02-14 17:22:58', '2026-02-14 17:23:19', '2026-02-14 17:23:56', '2026-02-14 17:22:49', '2026-02-14 17:23:56', 0),
(48, 'COG-20260215-0009', 23, 14, 14, 'completed', 'Your document is ready to be received', NULL, '2026-02-14 17:33:36', '2026-02-14 17:33:49', '2026-02-14 17:33:54', '2026-02-14 17:33:09', '2026-02-14 17:33:54', 0),
(49, 'COG-20260215-0010', 23, 14, 14, 'completed', 'Your document is ready to be received, Bobo mo pre', NULL, '2026-02-14 17:36:52', '2026-02-14 17:37:04', '2026-02-14 17:38:33', '2026-02-14 17:34:29', '2026-02-14 17:38:33', 0),
(50, 'COG-20260215-0011', 23, 14, 14, 'completed', '', NULL, '2026-02-14 17:41:11', '2026-02-14 17:41:26', '2026-02-14 17:42:23', '2026-02-14 17:40:57', '2026-02-14 17:42:23', 1),
(51, 'COG-20260215-0012', 23, 14, 14, 'completed', 'Okay na po\n', NULL, '2026-02-14 17:47:54', NULL, '2026-02-14 17:49:17', '2026-02-14 17:47:42', '2026-02-14 17:49:17', 1),
(52, 'COG-20260215-0013', 23, 14, 14, 'completed', 'Your document is ready to be received', NULL, '2026-02-14 17:51:05', '2026-02-14 17:52:39', '2026-02-14 17:54:02', '2026-02-14 17:50:36', '2026-02-14 17:54:02', 0),
(53, 'DPLM-20260215-0002', 23, 15, 14, 'completed', 'Your document is ready to be received', NULL, '2026-02-14 17:58:32', '2026-02-14 17:58:44', '2026-02-14 17:58:52', '2026-02-14 17:58:27', '2026-02-14 17:58:52', 0),
(54, 'COG-20260215-0014', 23, 14, 14, 'completed', 'Your document is ready to be received', NULL, '2026-02-14 18:01:15', '2026-02-14 18:01:26', '2026-02-14 18:01:37', '2026-02-14 18:01:06', '2026-02-14 18:01:37', 0),
(55, 'COG-20260215-0015', 23, 14, 14, 'completed', 'Your document is ready to be received', NULL, '2026-02-14 18:12:51', '2026-02-14 18:12:59', '2026-02-14 18:13:53', '2026-02-14 18:11:50', '2026-02-14 18:13:53', 0),
(56, 'COG-20260215-0016', 23, 14, 14, 'completed', 'Your document is ready to be received', NULL, '2026-02-14 18:16:16', '2026-02-14 18:16:25', '2026-02-14 18:16:36', '2026-02-14 18:16:08', '2026-02-14 18:16:36', 0),
(57, 'COG-20260215-0017', 23, 14, 14, 'completed', 'Your document is ready to be received', NULL, '2026-02-14 18:19:28', '2026-02-14 18:19:35', '2026-02-14 18:19:47', '2026-02-14 18:19:20', '2026-02-14 18:19:47', 0),
(58, 'COG-20260215-0018', 23, 14, 14, 'completed', 'Your document is ready to be received', NULL, '2026-02-14 18:22:24', '2026-02-14 18:22:40', '2026-02-14 18:22:51', '2026-02-14 18:20:36', '2026-02-14 18:22:51', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `school_id` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `role` enum('user','staff','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `verification_token` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `school_id`, `password`, `full_name`, `role`, `created_at`, `updated_at`, `verification_token`, `is_verified`) VALUES
(1, 'admin@equeue.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', '2026-02-13 16:38:21', '2026-02-13 16:38:21', NULL, 0),
(18, 'w01@window.local', NULL, '$2y$10$Rm1uBXHsPpHPHOq/lyIyN.oS0ofP6rbdVHXE6aUPvlaJ7T83XrOle', 'Staff W-01', 'staff', '2026-02-14 08:04:35', '2026-02-14 08:04:35', NULL, 1),
(19, 'w02@window.local', NULL, '$2y$10$X86VU44sQQXZ98dGLcAvk.IfiR8QzF3WWrJvDgJ7Na742.cPLB1a.', 'Staff W-02', 'staff', '2026-02-14 14:21:48', '2026-02-14 14:21:48', NULL, 1),
(23, 'exodusgalimba@gmail.com', 'NLP-22-00679', '$2y$10$sRFKAlRJLMK/1yDfg6bNi.hVVkG2qqOZPxUe7BL/W1DtUm1ZpKjPi', 'Genesis Manzano', 'user', '2026-02-14 15:53:29', '2026-02-14 15:54:15', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `windows`
--

CREATE TABLE `windows` (
  `id` int(11) NOT NULL,
  `window_number` varchar(50) NOT NULL,
  `window_name` varchar(255) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `windows`
--

INSERT INTO `windows` (`id`, `window_number`, `window_name`, `staff_id`, `is_active`, `created_at`, `updated_at`) VALUES
(14, 'W-01', 'Window 1', 18, 1, '2026-02-14 08:04:35', '2026-02-14 17:54:35'),
(15, 'W-02', 'Window 2', 19, 1, '2026-02-14 14:21:48', '2026-02-14 14:22:01');

-- --------------------------------------------------------

--
-- Table structure for table `window_services`
--

CREATE TABLE `window_services` (
  `id` int(11) NOT NULL,
  `window_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `is_enabled` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `window_services`
--

INSERT INTO `window_services` (`id`, `window_id`, `service_id`, `is_enabled`, `created_at`) VALUES
(37, 14, 14, 1, '2026-02-14 08:04:45'),
(38, 14, 15, 1, '2026-02-14 08:04:45'),
(39, 14, 16, 1, '2026-02-14 08:04:45'),
(40, 15, 14, 1, '2026-02-14 14:22:01'),
(41, 15, 15, 1, '2026-02-14 14:22:01'),
(42, 15, 16, 1, '2026-02-14 14:22:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `chatbot_data`
--
ALTER TABLE `chatbot_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `window_id` (`window_id`),
  ADD KEY `idx_ticket` (`ticket_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_sentiment` (`sentiment`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_read` (`is_read`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `service_code` (`service_code`),
  ADD KEY `idx_code` (`service_code`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_number` (`ticket_number`),
  ADD KEY `window_id` (`window_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_service` (`service_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_queue` (`service_id`,`status`,`created_at`),
  ADD KEY `idx_queue_lookup` (`service_id`,`status`,`created_at`,`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `school_id` (`school_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `windows`
--
ALTER TABLE `windows`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `window_number` (`window_number`),
  ADD KEY `idx_staff` (`staff_id`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `window_services`
--
ALTER TABLE `window_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_window_service` (`window_id`,`service_id`),
  ADD KEY `idx_window` (`window_id`),
  ADD KEY `idx_service` (`service_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chatbot_data`
--
ALTER TABLE `chatbot_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=214;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `windows`
--
ALTER TABLE `windows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `window_services`
--
ALTER TABLE `window_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_3` FOREIGN KEY (`window_id`) REFERENCES `windows` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`window_id`) REFERENCES `windows` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `windows`
--
ALTER TABLE `windows`
  ADD CONSTRAINT `windows_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `window_services`
--
ALTER TABLE `window_services`
  ADD CONSTRAINT `window_services_ibfk_1` FOREIGN KEY (`window_id`) REFERENCES `windows` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `window_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

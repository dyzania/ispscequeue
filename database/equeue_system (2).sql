-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 08, 2026 at 11:16 AM
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
-- Table structure for table `ai_context`
--

CREATE TABLE `ai_context` (
  `id` int(11) NOT NULL,
  `content` longtext DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `office_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ai_context`
--

INSERT INTO `ai_context` (`id`, `content`, `updated_at`, `office_id`) VALUES
(1, '<h2>Certificate of Grades (COG)</h2><p>Requirements:</p><ul><li>Route Sheet,</li><li>Receipt</li><li>ID</li></ul>', '2026-02-19 08:13:01', 1);

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `office_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `image_path`, `created_at`, `updated_at`, `office_id`) VALUES
(8, '𝗜𝗦𝗣𝗦𝗖 𝗔𝗡𝗔𝗥𝗔𝗔𝗥 -𝗝𝗔𝗡𝗨𝗔𝗥𝗬 𝟮𝟬𝟮𝟲 𝗥𝗘𝗚𝗨𝗟𝗔𝗥 𝗜𝗦𝗦𝗨𝗘', '<p>Here are the news and updates you need to know about our college for January 2026.</p><p><br></p><p>You can read the newsletter through this link:</p><p>https://online.fliphtml5.com/.../January-2026-Anaraar-8fEK/</p><p>You may also scan the QR code in the image to access it easily on your mobile device.</p><p><br></p><p>This newsletter is brought to you by the ISPSC Office for Strategic Communication and Institutional Branding.</p><p><br></p><p>#𝗜𝗦𝗣𝗦𝗖</p><p>#𝗨𝗻𝗶𝘃𝗲𝗿𝘀𝗶𝘁𝘆𝗢𝗳𝗜𝗹𝗼𝗰𝗼𝘀𝗣𝗵𝗶𝗹𝗶𝗽𝗽𝗶𝗻𝗲𝘀</p><p>#𝗢𝗻𝘄𝗮𝗿𝗱𝘀𝗨𝗜𝗣</p>', 'uploads/announcements/6996d9a5642ac.jpg', '2026-02-19 09:36:37', '2026-02-19 09:36:37', 1);

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
(75, 57, 38, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-02-19 08:46:54'),
(76, 57, 40, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-19 08:48:51'),
(78, 54, 36, 'turn_next', 'It\'s your turn! Please proceed to Window 3', 1, '2026-02-19 08:49:32'),
(79, 55, 37, 'turn_next', 'It\'s your turn! Please proceed to Window 4', 1, '2026-02-19 08:49:46'),
(80, 55, 37, 'now_serving', 'You are now being served.', 1, '2026-02-19 08:50:32'),
(81, 55, 37, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-19 08:51:00'),
(82, 57, 40, 'now_serving', 'You are now being served.', 1, '2026-02-19 08:51:39'),
(85, 54, 36, 'now_serving', 'You are now being served.', 1, '2026-02-19 08:52:18'),
(86, 54, 36, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-19 08:52:24'),
(87, 57, 40, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-19 08:52:50'),
(88, 54, 41, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-02-19 08:53:13'),
(89, 54, 42, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-19 08:53:27'),
(90, 54, 42, 'now_serving', 'You are now being served.', 1, '2026-02-19 08:53:33'),
(91, 54, 42, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-19 08:53:37'),
(95, 55, 44, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-19 09:19:41'),
(96, 55, 44, 'now_serving', 'You are now being served.', 1, '2026-02-19 09:19:56'),
(97, 55, 44, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-19 09:20:04'),
(98, 57, 46, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-19 09:20:12'),
(99, 57, 46, 'now_serving', 'You are now being served.', 1, '2026-02-19 09:20:19'),
(100, 57, 46, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-19 09:20:27'),
(101, 58, 47, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-19 09:20:38'),
(102, 58, 47, 'now_serving', 'You are now being served.', 1, '2026-02-19 09:21:13'),
(103, 58, 47, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-19 09:22:08'),
(104, 54, 45, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-19 09:24:24'),
(105, 58, 48, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-19 09:24:34'),
(106, 58, 48, 'now_serving', 'You are now being served.', 1, '2026-02-19 09:24:46'),
(107, 58, 48, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-19 09:24:50'),
(108, 54, 45, 'now_serving', 'You are now being served.', 1, '2026-02-19 09:26:40'),
(109, 54, 45, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-19 09:26:45'),
(110, 55, 51, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-19 09:30:58'),
(111, 55, 51, 'now_serving', 'You are now being served.', 1, '2026-02-19 09:31:10'),
(112, 55, 51, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-19 09:31:32'),
(113, 54, 52, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-02-19 09:31:53'),
(114, 54, 52, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-02-19 09:32:46'),
(115, 57, 53, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 0, '2026-02-19 09:42:39'),
(116, 57, 53, 'now_serving', 'You are now being served.', 0, '2026-02-19 09:43:26'),
(117, 57, 53, 'completed', 'Transaction completed. Please provide your feedback.', 0, '2026-02-19 09:44:01'),
(118, 54, 54, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 0, '2026-03-08 08:26:12'),
(119, 58, 55, 'turn_next', 'It\'s your turn! Please proceed to Window 1', 1, '2026-03-08 08:26:51'),
(120, 58, 55, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-08 08:31:54'),
(121, 54, 54, 'cancelled', 'Your ticket has been cancelled.', 0, '2026-03-08 08:31:57'),
(122, 59, 57, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-08 08:41:49'),
(123, 59, 57, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-08 08:42:13'),
(124, 59, 58, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-08 08:42:27'),
(125, 59, 58, 'now_serving', 'You are now being served.', 1, '2026-03-08 08:43:05'),
(126, 59, 58, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-03-08 08:43:22'),
(127, 59, 59, 'turn_next', 'It\'s your turn! Please proceed to Assesment Counters', 1, '2026-03-08 08:57:05'),
(128, 59, 59, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-08 08:57:46'),
(129, 59, 60, 'turn_next', 'It\'s your turn! Please proceed to Registrar Window 01', 1, '2026-03-08 08:58:39'),
(130, 59, 60, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-08 09:29:25'),
(131, 59, 61, 'turn_next', 'It\'s your turn! Please proceed to Assesment Counters', 1, '2026-03-08 09:30:05'),
(132, 59, 61, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-08 09:30:14'),
(133, 59, 62, 'turn_next', 'It\'s your turn! Please proceed to Assesment Counters', 1, '2026-03-08 09:30:51'),
(134, 59, 62, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-08 09:32:03'),
(135, 59, 63, 'turn_next', 'It\'s your turn! Please proceed to Extra', 1, '2026-03-08 09:33:25'),
(136, 59, 64, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-08 09:41:37'),
(137, 59, 64, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-08 09:47:13'),
(138, 59, 65, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-08 09:47:25'),
(139, 59, 65, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-08 09:49:00'),
(140, 59, 66, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-08 09:50:26'),
(141, 59, 67, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-08 09:58:31'),
(142, 59, 67, 'now_serving', 'You are now being served.', 1, '2026-03-08 09:59:46'),
(143, 59, 67, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-03-08 10:05:06'),
(144, 59, 70, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-08 10:10:08');

-- --------------------------------------------------------

--
-- Table structure for table `offices`
--

CREATE TABLE `offices` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `offices`
--

INSERT INTO `offices` (`id`, `name`, `code`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Registrar Office', 'RGSTR', 'Campus Registrar Office', 1, '2026-03-08 08:17:36', '2026-03-08 08:46:55'),
(2, 'Student Affair Service', 'SAS', 'Student Affair Service Office', 1, '2026-03-08 08:46:55', '2026-03-08 08:46:55');

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
  `staff_notes` text DEFAULT NULL,
  `estimated_time` int(11) DEFAULT 10,
  `target_time` int(11) DEFAULT 10,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `office_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `service_code`, `description`, `requirements`, `staff_notes`, `estimated_time`, `target_time`, `is_active`, `created_at`, `updated_at`, `office_id`) VALUES
(14, 'Certificate of Grades', 'CG', '', 'Route Sheet\r\nValid ID\r\nCashier Receipt', '', 10, 10, 1, '2026-02-14 02:22:06', '2026-03-08 08:29:31', 1),
(15, 'Request of Diploma', 'DP', '', 'Official Transcript of Records', '', 10, 30, 1, '2026-02-14 02:23:01', '2026-03-08 08:30:27', 1),
(16, 'Good Moral Character', 'GM', 'Good Moral Character', 'Valid ID\r\nOfficial Transcript of Records', '', 10, 25, 1, '2026-02-14 07:39:51', '2026-03-08 08:30:01', 1),
(17, 'General Inquiry', 'GQ', 'General Concerns', 'None', '', 10, 15, 1, '2026-02-14 19:44:00', '2026-03-08 08:29:54', 1),
(18, 'Request for Official Transcript of Records', 'TR', 'Transcript of Records', 'Clearance\r\nLatest ID Picture\r\nSchool ID', 'na ay', 10, 4326, 1, '2026-02-19 08:31:08', '2026-03-08 08:30:16', 1),
(19, 'Request for Enrollment and Billing', 'EB', 'Certificate of Enrollment and Billing', 'Registration Form\r\nScholl ID Duly Validated', 'Don&amp;#039;t be stupid', 10, 5, 1, '2026-02-19 08:34:35', '2026-03-08 08:30:08', 1),
(20, 'Scholarship', 'SC', '', 'Valid ID\r\nForm Sheet', '', 10, 10, 1, '2026-03-08 08:52:56', '2026-03-08 08:52:56', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `ticket_number` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `auto_generated` tinyint(1) DEFAULT 0,
  `user_note` text DEFAULT NULL,
  `window_id` int(11) DEFAULT NULL,
  `status` enum('waiting','called','serving','completed','cancelled') DEFAULT 'waiting',
  `staff_notes` text DEFAULT NULL,
  `queue_position` int(11) DEFAULT NULL,
  `called_at` timestamp NULL DEFAULT NULL,
  `served_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_archived` tinyint(1) DEFAULT 0,
  `service_time_accumulated` int(11) DEFAULT 0,
  `office_id` int(11) NOT NULL DEFAULT 1,
  `is_priority` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `ticket_number`, `user_id`, `service_id`, `auto_generated`, `user_note`, `window_id`, `status`, `staff_notes`, `queue_position`, `called_at`, `served_at`, `completed_at`, `created_at`, `updated_at`, `is_archived`, `service_time_accumulated`, `office_id`, `is_priority`) VALUES
(70, 'CG8-001', 59, 14, 0, NULL, 26, 'called', '', 1, '2026-03-08 10:10:08', NULL, NULL, '2026-03-08 10:09:59', '2026-03-08 10:10:32', 1, 0, 1, 0);

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
  `is_verified` tinyint(1) DEFAULT 0,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `last_read_announcement_id` int(11) DEFAULT 0,
  `announcement_subscription` tinyint(1) DEFAULT 0,
  `login_attempts` int(11) DEFAULT 0,
  `lockout_until` datetime DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL,
  `college` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `school_id`, `password`, `full_name`, `role`, `created_at`, `updated_at`, `is_verified`, `otp_code`, `otp_expiry`, `last_read_announcement_id`, `announcement_subscription`, `login_attempts`, `lockout_until`, `office_id`, `college`) VALUES
(1, 'admin@equeue.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', '2026-02-13 16:38:21', '2026-02-13 16:38:21', 1, NULL, NULL, 0, 0, 0, NULL, 1, NULL),
(59, 'exodusgalimba@gmail.com', 'NLP-22-00679', '$2y$10$PYdjkPMY3.Dptfosjcqhne/dxWwXa6LLfSEIvAG5/Vmj9o5r7jiXq', 'Genesis Manzano', 'user', '2026-03-08 08:40:37', '2026-03-08 08:56:38', 1, NULL, NULL, 8, 0, 0, NULL, NULL, 'CAS'),
(60, 'registrar_admin@equeue.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Registrar Admin', 'admin', '2026-03-08 08:46:55', '2026-03-08 08:46:55', 1, NULL, NULL, 0, 0, 0, NULL, 1, NULL),
(61, 'sas_admin@equeue.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'SAS Admin', 'admin', '2026-03-08 08:46:55', '2026-03-08 08:46:55', 1, NULL, NULL, 0, 0, 0, NULL, 2, NULL),
(65, 'sas_w02@equeue.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'SAS Staff W02', 'staff', '2026-03-08 08:48:28', '2026-03-08 08:48:28', 1, NULL, NULL, 0, 0, 0, NULL, 2, NULL),
(70, 'rgstr-w01@window.local', NULL, '$2y$10$JXpQSHXg5vSsR7TVAjNyuus25.FHfl6SNUNSrGpXnlUDEXdD3vDJi', 'Staff W-01', 'staff', '2026-03-08 09:37:11', '2026-03-08 09:37:11', 1, '910400', '2026-03-08 17:52:11', 0, 0, 0, NULL, 1, NULL),
(71, 'rgstr-w02@window.local', NULL, '$2y$10$Nep.nYgU3fiNKVzakCaxK.0exLE3Bev0CSD8vu9J/vzoIHqX0KV3q', 'Staff W-02', 'staff', '2026-03-08 09:37:20', '2026-03-08 09:37:20', 1, '524932', '2026-03-08 17:52:20', 0, 0, 0, NULL, 1, NULL),
(72, 'rgstr-w03@window.local', NULL, '$2y$10$xZaXCfMl1lFtdd3A5MJz0u1qJz25GRSGlN7BWYYwAjm4PwUrPL2xK', 'Staff W-03', 'staff', '2026-03-08 09:37:30', '2026-03-08 09:37:30', 1, '949949', '2026-03-08 17:52:30', 0, 0, 0, NULL, 1, NULL),
(73, 'rgstr-w04@window.local', NULL, '$2y$10$jet0A0vGk5BxqB7TiyMcIu9tf1INdLsvlIrfFYeeJd43ysVoK0zCa', 'Staff W-04', 'staff', '2026-03-08 09:37:39', '2026-03-08 09:37:39', 1, '949472', '2026-03-08 17:52:39', 0, 0, 0, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `windows`
--

CREATE TABLE `windows` (
  `id` int(11) NOT NULL,
  `window_number` varchar(50) NOT NULL,
  `window_name` varchar(255) NOT NULL,
  `location_info` varchar(255) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `office_id` int(11) NOT NULL DEFAULT 1,
  `preferred_colleges` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `windows`
--

INSERT INTO `windows` (`id`, `window_number`, `window_name`, `location_info`, `staff_id`, `is_active`, `created_at`, `updated_at`, `office_id`, `preferred_colleges`) VALUES
(21, 'SAS-W02', 'Assesment Counters', NULL, 65, 1, '2026-03-08 08:46:55', '2026-03-08 08:55:38', 2, 'CAS,SCJE,CTE,CBME'),
(26, 'W-01', 'CAS', NULL, 70, 1, '2026-03-08 09:37:11', '2026-03-08 09:45:42', 1, 'CAS,SCJE,CTE,CBME'),
(27, 'W-02', 'CBME', NULL, 71, 0, '2026-03-08 09:37:20', '2026-03-08 09:37:20', 1, NULL),
(28, 'W-03', 'CTE', NULL, 72, 0, '2026-03-08 09:37:30', '2026-03-08 09:37:30', 1, NULL),
(29, 'W-04', 'SCJE', NULL, 73, 0, '2026-03-08 09:37:39', '2026-03-08 09:37:39', 1, NULL);

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
(40, 15, 14, 0, '2026-02-14 14:22:01'),
(41, 15, 15, 0, '2026-02-14 14:22:01'),
(42, 15, 16, 0, '2026-02-14 14:22:01'),
(43, 14, 17, 1, '2026-02-14 19:47:13'),
(44, 15, 17, 1, '2026-02-14 19:47:13'),
(49, 16, 14, 0, '2026-02-15 09:04:36'),
(50, 16, 17, 0, '2026-02-15 09:04:37'),
(51, 16, 16, 0, '2026-02-15 09:04:38'),
(52, 16, 15, 1, '2026-02-15 09:04:38'),
(53, 17, 14, 0, '2026-02-15 09:05:56'),
(54, 17, 15, 0, '2026-02-15 09:05:56'),
(55, 17, 16, 0, '2026-02-15 09:05:56'),
(56, 17, 17, 0, '2026-02-15 09:05:56'),
(57, 18, 14, 0, '2026-02-15 16:18:16'),
(58, 18, 15, 0, '2026-02-15 16:18:16'),
(59, 18, 16, 0, '2026-02-15 16:18:16'),
(60, 18, 17, 0, '2026-02-15 16:18:16'),
(61, 19, 14, 0, '2026-02-15 16:18:28'),
(62, 19, 15, 1, '2026-02-15 16:18:28'),
(63, 19, 16, 0, '2026-02-15 16:18:28'),
(64, 19, 17, 0, '2026-02-15 16:18:28'),
(65, 14, 19, 1, '2026-02-19 08:43:57'),
(66, 14, 18, 1, '2026-02-19 08:43:58'),
(67, 17, 18, 1, '2026-02-19 08:48:08'),
(68, 21, 20, 1, '2026-03-08 08:55:55'),
(69, 20, 14, 1, '2026-03-08 08:58:29'),
(70, 20, 17, 1, '2026-03-08 08:58:29'),
(71, 20, 16, 1, '2026-03-08 08:58:31'),
(72, 20, 19, 1, '2026-03-08 08:58:33'),
(73, 20, 18, 1, '2026-03-08 08:58:34'),
(74, 20, 15, 1, '2026-03-08 08:58:36'),
(75, 26, 14, 1, '2026-03-08 09:40:03'),
(76, 26, 17, 1, '2026-03-08 09:40:04'),
(77, 26, 16, 1, '2026-03-08 09:40:05'),
(78, 26, 19, 1, '2026-03-08 09:40:07'),
(79, 26, 18, 1, '2026-03-08 09:40:08'),
(80, 26, 15, 1, '2026-03-08 09:40:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ai_context`
--
ALTER TABLE `ai_context`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ai_context_office` (`office_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_announcements_office` (`office_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `window_id` (`window_id`),
  ADD KEY `idx_ticket` (`ticket_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_read` (`is_read`);

--
-- Indexes for table `offices`
--
ALTER TABLE `offices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `office_code` (`code`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `service_code` (`service_code`),
  ADD KEY `idx_code` (`service_code`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `fk_services_office` (`office_id`);

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
  ADD KEY `fk_tickets_office` (`office_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `school_id` (`school_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_otp` (`otp_code`),
  ADD KEY `fk_users_office` (`office_id`);

--
-- Indexes for table `windows`
--
ALTER TABLE `windows`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `window_number` (`window_number`),
  ADD KEY `idx_staff` (`staff_id`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `fk_windows_office` (`office_id`);

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
-- AUTO_INCREMENT for table `ai_context`
--
ALTER TABLE `ai_context`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT for table `offices`
--
ALTER TABLE `offices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `windows`
--
ALTER TABLE `windows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `window_services`
--
ALTER TABLE `window_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ai_context`
--
ALTER TABLE `ai_context`
  ADD CONSTRAINT `fk_ai_context_office` FOREIGN KEY (`office_id`) REFERENCES `offices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `fk_announcements_office` FOREIGN KEY (`office_id`) REFERENCES `offices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `fk_services_office` FOREIGN KEY (`office_id`) REFERENCES `offices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `fk_tickets_office` FOREIGN KEY (`office_id`) REFERENCES `offices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_office` FOREIGN KEY (`office_id`) REFERENCES `offices` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `windows`
--
ALTER TABLE `windows`
  ADD CONSTRAINT `fk_windows_office` FOREIGN KEY (`office_id`) REFERENCES `offices` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

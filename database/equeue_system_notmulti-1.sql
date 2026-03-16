-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 16, 2026 at 08:07 AM
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
-- Database: `equeue_system_notmulti`
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
(1, '<h2>Certificate of Grades (COG)</h2><p>Requirements:</p><ul><li>Route Sheet,</li><li>Receipt</li><li>ID</li></ul><p><br></p><h2>Enrollment Form</h2><p>Requirements:</p><ul><li>Valid Id</li><li>Receipt</li></ul><p><br></p><p>[Ticket Creation Flow]</p><p>1. Office Selection: Users must first select a \"Destination Office\" (e.g., Registrar, Cashier, Admission, Clinic) from their Dashboard. Each office shows real-time stats including Open Windows and Queue Waitlist sizes.</p><p>2. Service Selection: After selecting an office, users are taken to the \"Get Ticket\" page where they see a list of services specific to that office.&nbsp;</p><p>3. Joining the Queue: Users hit the \"Get Ticket\" button on a service card to join the waitlist for that specific service.</p><p><br></p><p>[Important Rules]</p><p>- Users can only get a new ticket if they do not currently have an active ticket.&nbsp;</p><p>- An \"active ticket\" includes any ticket with the status \'waiting\', \'called\', or \'serving\'.</p><p>- Users cannot hold multiple queue positions simultaneously.</p><p>- If a user has an active ticket, they are shown a banner identifying their current Queue Ticket number, Status, and Estimated Wait Time instead of the \"Get New Ticket\" prompt.</p><p><br></p><p>Q: How do I know when it\'s my turn?</p><p>A: You can monitor your position in real-time on your Dashboard or the \"My Ticket\" page. When it\'s your turn, the status will change to \"Calling,\" the specific Window Number will be displayed on your screen, and an audio announcement will be made in the waiting area.</p><p><br></p><p>Q: Can I cancel my ticket if I change my mind or have to leave?</p><p>A: Yes. You can cancel your ticket at any time while its status is \"Waiting\". Go to the \"My Ticket\" page and click the \"Cancel Ticket\" button at the bottom of your ticket card. This action cannot be undone.</p><p><br></p><p>Q: What does the \"Step Back\" or \"Snooze\" button do?</p><p>A: If you need more time before your turn, the \"Step Back\" option on the \"My Ticket\" page pushes your ticket back by 3 spots in the queue, giving you extra time without losing your place entirely.&nbsp;</p><p><br></p><p>Q: How do I leave feedback about my service?</p><p>A: After your transaction is finished and your ticket status changes to \"Completed,\" a feedback form will automatically appear on your \"My Ticket\" page. You can rate your experience and leave comments to help the office improve..</p><p><br></p><p>Q: What happens if I miss my turn when called?</p><p>A: The staff will call your number a few times. If you do not proceed to the designated window promptly, your ticket may be marked as \"Archived\" or cancelled by the staff member, and you will need to get a new ticket.</p><p><br></p><p>Q: Do I need to bring my Student ID?</p><p>A: Yes, it is highly recommended to bring your valid School ID, as most offices require it to verify your identity and process school-related transactions.</p><p><br></p><p><br></p><p><br></p>', '2026-03-16 06:02:41', 1);

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
  `client_type` enum('Student','Non-Teaching','Faculty','Alumni','Parent/Guardian','Others') DEFAULT NULL,
  `client_type_others` varchar(255) DEFAULT NULL,
  `contact_means` enum('In person','Over the Telephone','University Help Desk','Others') DEFAULT NULL,
  `contact_means_others` varchar(255) DEFAULT NULL,
  `cc_awareness` tinyint(4) DEFAULT NULL,
  `cc_visibility` tinyint(4) DEFAULT NULL,
  `cc_helpfulness` tinyint(4) DEFAULT NULL,
  `rating_responsiveness_1` tinyint(4) DEFAULT NULL,
  `rating_responsiveness_2` tinyint(4) DEFAULT NULL,
  `rating_reliability` tinyint(4) DEFAULT NULL,
  `rating_access` tinyint(4) DEFAULT NULL,
  `rating_communication` tinyint(4) DEFAULT NULL,
  `rating_costs` tinyint(4) DEFAULT NULL,
  `rating_integrity` tinyint(4) DEFAULT NULL,
  `rating_courtesy` tinyint(4) DEFAULT NULL,
  `rating_outcome` tinyint(4) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5 or `rating` is null),
  `comment` text DEFAULT NULL,
  `sentiment` enum('positive','neutral','negative','very_positive','very_negative') DEFAULT NULL,
  `sentiment_score` decimal(5,4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `ticket_id`, `user_id`, `window_id`, `client_type`, `client_type_others`, `contact_means`, `contact_means_others`, `cc_awareness`, `cc_visibility`, `cc_helpfulness`, `rating_responsiveness_1`, `rating_responsiveness_2`, `rating_reliability`, `rating_access`, `rating_communication`, `rating_costs`, `rating_integrity`, `rating_courtesy`, `rating_outcome`, `rating`, `comment`, `sentiment`, `sentiment_score`, `created_at`) VALUES
(40, 109, 59, 26, 'Alumni', '', 'In person', '', 4, 3, 2, 5, 5, 5, 4, 5, 5, 5, 5, 5, NULL, 'nice one', 'positive', 0.4000, '2026-03-11 16:05:10'),
(41, 110, 59, 26, 'Student', '', 'In person', '', 2, 2, 2, 5, 5, 5, 5, 5, 5, 5, 5, 5, NULL, 'all goods', 'neutral', 0.0000, '2026-03-11 17:11:18'),
(42, 111, 59, 39, 'Student', '', 'In person', '', 1, 2, 2, 5, 5, 5, 5, 5, 5, 5, 5, 5, NULL, 'nice', 'positive', 0.4000, '2026-03-11 18:04:01'),
(43, 112, 59, 39, 'Faculty', '', 'In person', '', 4, 5, 4, 5, 5, 5, 5, 5, 5, 5, 5, 5, NULL, 'sheesh!', 'very_positive', 0.8000, '2026-03-11 18:08:44'),
(44, 113, 59, 41, 'Student', '', 'In person', '', 1, 1, 1, 5, 5, 4, 5, 5, 5, 5, 5, 5, NULL, 'Okay', 'positive', 0.4000, '2026-03-12 02:36:40'),
(45, 114, 59, 41, 'Student', '', 'In person', '', 1, 1, 1, 5, 5, 5, 5, 5, 5, 5, 5, 5, NULL, 'nice system', 'positive', 0.4000, '2026-03-13 09:05:02'),
(46, 116, 83, 41, 'Student', '', 'In person', '', 1, 1, 1, 5, 5, 5, 5, 5, 5, 5, 5, 5, NULL, 'okay', 'positive', 0.4000, '2026-03-13 09:17:55'),
(47, 118, 97, 41, 'Student', '', 'In person', '', 1, 1, 1, 5, 4, 5, 4, 5, 4, 5, 5, 5, NULL, 'nice one', 'neutral', 0.0000, '2026-03-16 06:16:26');

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
(144, 59, 70, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-08 10:10:08'),
(145, 59, 70, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-08 14:09:56'),
(146, 59, 71, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-08 14:11:24'),
(147, 59, 71, 'now_serving', 'You are now being served.', 1, '2026-03-08 14:11:46'),
(148, 59, 71, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-03-08 14:12:04'),
(149, 59, 72, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-08 14:13:20'),
(150, 59, 72, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-08 14:14:32'),
(151, 59, 73, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-08 14:15:02'),
(152, 59, 73, 'now_serving', 'You are now being served.', 1, '2026-03-08 14:15:18'),
(153, 59, 73, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-03-08 14:15:25'),
(154, 59, 74, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-08 14:19:58'),
(155, 59, 74, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-08 15:35:07'),
(156, 59, 75, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-08 16:17:31'),
(157, 59, 75, 'now_serving', 'You are now being served.', 1, '2026-03-08 16:21:12'),
(158, 59, 75, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-03-08 16:21:14'),
(159, 59, 76, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-08 16:28:12'),
(160, 59, 76, 'now_serving', 'You are now being served.', 1, '2026-03-10 11:28:21'),
(161, 59, 76, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-10 11:42:22'),
(162, 59, 77, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-10 11:44:14'),
(163, 59, 77, 'now_serving', 'You are now being served.', 1, '2026-03-10 11:51:27'),
(164, 59, 77, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-03-10 12:13:33'),
(165, 59, 78, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-10 12:17:44'),
(166, 59, 78, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-10 12:19:39'),
(167, 59, 79, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-10 12:21:27'),
(168, 59, 79, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-10 12:22:31'),
(169, 59, 80, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-10 12:41:45'),
(170, 59, 80, 'now_serving', 'You are now being served.', 1, '2026-03-10 12:41:56'),
(171, 59, 80, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-10 12:44:04'),
(172, 59, 81, 'turn_next', 'It\'s your turn! Please proceed to SCJE', 1, '2026-03-10 13:17:40'),
(173, 59, 81, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-10 14:47:34'),
(174, 59, 82, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-10 15:03:51'),
(175, 59, 83, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-10 15:44:13'),
(176, 83, 84, 'turn_next', 'It\'s your turn! Please proceed to CTE', 1, '2026-03-10 16:10:26'),
(177, 59, 85, 'turn_next', 'It\'s your turn! Please proceed to CTE', 1, '2026-03-10 16:10:40'),
(178, 59, 85, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-10 16:11:55'),
(179, 83, 84, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-10 16:11:57'),
(180, 83, 87, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-10 17:25:31'),
(181, 83, 88, 'turn_next', 'It\'s your turn! Please proceed to Conference Room', 1, '2026-03-10 17:38:31'),
(182, 59, 86, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-10 23:23:18'),
(183, 83, 88, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-10 23:26:17'),
(184, 59, 89, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-11 01:19:10'),
(185, 59, 90, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-11 01:20:14'),
(186, 59, 90, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-03-11 01:21:16'),
(187, 59, 91, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-11 01:24:15'),
(188, 59, 92, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-11 01:24:31'),
(189, 59, 92, 'now_serving', 'You are now being served.', 1, '2026-03-11 01:24:58'),
(190, 59, 92, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-03-11 01:25:19'),
(191, 86, 93, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-11 02:32:55'),
(192, 86, 93, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-11 02:37:44'),
(193, 86, 94, 'turn_next', 'It\'s your turn! Please proceed to CBME', 1, '2026-03-11 02:38:20'),
(194, 85, 95, 'turn_next', 'It\'s your turn! Please proceed to CBME', 1, '2026-03-11 02:40:26'),
(195, 85, 95, 'now_serving', 'You are now being served.', 1, '2026-03-11 02:43:26'),
(196, 85, 95, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-03-11 02:45:58'),
(197, 85, 97, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-11 03:04:52'),
(198, 85, 97, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-11 03:06:04'),
(199, 86, 94, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-11 03:11:06'),
(200, 85, 99, 'turn_next', 'It\'s your turn! Please proceed to CBME', 1, '2026-03-11 03:11:12'),
(201, 86, 100, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-11 03:12:59'),
(202, 86, 100, 'now_serving', 'You are now being served.', 1, '2026-03-11 03:13:25'),
(203, 86, 100, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-03-11 03:14:38'),
(204, 86, 101, 'turn_next', 'It\'s your turn! Please proceed to Assesment Counters', 1, '2026-03-11 03:21:01'),
(205, 85, 99, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-11 03:24:24'),
(206, 85, 103, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-11 03:28:59'),
(207, 86, 101, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-11 03:32:00'),
(208, 86, 105, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-11 03:32:28'),
(209, 86, 106, 'cancelled', 'Your ticket has been cancelled.', 1, '2026-03-11 03:38:14'),
(210, 59, 108, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-11 15:41:50'),
(211, 59, 108, 'now_serving', 'You are now being served.', 1, '2026-03-11 15:41:57'),
(212, 59, 108, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-03-11 15:42:05'),
(213, 59, 109, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-11 16:01:34'),
(214, 59, 109, 'now_serving', 'You are now being served.', 1, '2026-03-11 16:01:44'),
(215, 59, 109, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-03-11 16:01:47'),
(216, 59, 110, 'turn_next', 'It\'s your turn! Please proceed to CAS', 1, '2026-03-11 17:09:52'),
(217, 59, 110, 'now_serving', 'You are now being served.', 1, '2026-03-11 17:10:05'),
(218, 59, 110, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-03-11 17:10:08'),
(219, 59, 111, 'turn_next', 'It\'s your turn! Please proceed to Window 2', 1, '2026-03-11 18:02:40'),
(220, 59, 111, 'now_serving', 'You are now being served.', 1, '2026-03-11 18:02:49'),
(221, 59, 111, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-03-11 18:02:57'),
(222, 59, 112, 'turn_next', 'It\'s your turn! Please proceed to Window 2', 1, '2026-03-11 18:04:52'),
(223, 59, 112, 'now_serving', 'You are now being served.', 1, '2026-03-11 18:05:01'),
(224, 59, 112, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-03-11 18:05:05'),
(225, 59, 113, 'turn_next', 'It\'s your turn! Please proceed to Window 4', 1, '2026-03-12 02:34:25'),
(226, 59, 113, 'now_serving', 'You are now being served.', 1, '2026-03-12 02:34:33'),
(227, 59, 113, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-03-12 02:34:41'),
(228, 59, 114, 'turn_next', 'It\'s your turn! Please proceed to Window 4', 1, '2026-03-12 02:36:57'),
(229, 59, 114, 'now_serving', 'You are now being served.', 1, '2026-03-12 02:37:02'),
(230, 59, 114, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-03-12 02:37:05'),
(231, 83, 116, 'turn_next', 'It\'s your turn! Please proceed to Window 4', 1, '2026-03-13 09:16:35'),
(232, 83, 116, 'now_serving', 'You are now being served.', 1, '2026-03-13 09:16:45'),
(233, 83, 116, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-03-13 09:17:06'),
(234, 83, 117, 'turn_next', 'It\'s your turn! Please proceed to Window 4', 1, '2026-03-16 05:49:03'),
(235, 83, 117, 'now_serving', 'You are now being served.', 1, '2026-03-16 05:49:18'),
(236, 83, 117, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-03-16 05:49:31'),
(237, 97, 118, 'turn_next', 'It\'s your turn! Please proceed to Window 4', 1, '2026-03-16 06:14:47'),
(238, 97, 118, 'now_serving', 'You are now being served.', 1, '2026-03-16 06:15:20'),
(239, 97, 118, 'completed', 'Transaction completed. Please provide your feedback.', 1, '2026-03-16 06:15:31');

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
(17, 'General Inquiry', 'GQ', 'General Concerns', 'None', '', 10, 15, 1, '2026-02-14 19:44:00', '2026-03-08 08:29:54', 1),
(18, 'Request for Official Transcript of Records', 'TR', 'Transcript of Records', 'Clearance\r\nLatest ID Picture\r\nSchool ID', 'na ay', 10, 4326, 1, '2026-02-19 08:31:08', '2026-03-08 08:30:16', 1),
(19, 'Request for Enrollment and Billing', 'EB', 'Certificate of Enrollment and Billing', 'Registration Form\r\nScholl ID Duly Validated', 'Don&amp;#039;t be stupid', 10, 5, 1, '2026-02-19 08:34:35', '2026-03-08 08:30:08', 1),
(20, 'Scholarship', 'SC', '', 'Valid ID\r\nForm Sheet', '', 10, 10, 1, '2026-03-08 08:52:56', '2026-03-08 08:52:56', 2),
(21, 'Good Moral Character', 'GC', 'Good Moral', 'Valid ID\r\nRequirements', '', 10, 10, 1, '2026-03-10 13:02:21', '2026-03-10 13:02:21', 2),
(22, 'General Inquiry', 'GQ', 'General Inquiry', 'Valid ID\r\nRequired IDs', '', 10, 10, 1, '2026-03-10 13:03:55', '2026-03-10 13:03:55', 2),
(23, 'Lost ID', 'LD', 'Lost Identification', 'Request Form', '', 10, 10, 1, '2026-03-11 01:01:36', '2026-03-11 01:01:36', 1),
(24, 'Job Hiring', 'JH', 'Job Hiring', 'Requirements\r\nID', '', 10, 10, 1, '2026-03-11 17:56:57', '2026-03-11 17:56:57', 1);

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
(109, 'CG12-001', 59, 14, 0, NULL, 26, 'completed', 'Your document is ready for release', 1, '2026-03-11 16:01:34', '2026-03-11 16:01:44', '2026-03-11 16:01:47', '2026-03-11 16:01:25', '2026-03-11 16:01:47', 0, 3, 1, 0),
(110, 'EB12-001', 59, 19, 0, NULL, 26, 'completed', 'Your document is ready for release', 1, '2026-03-11 17:09:52', '2026-03-11 17:10:05', '2026-03-11 17:10:08', '2026-03-11 17:09:40', '2026-03-11 17:10:08', 0, 3, 1, 0),
(111, 'CG12-002', 59, 14, 0, NULL, 39, 'completed', 'Your document is ready for release', 1, '2026-03-11 18:02:40', '2026-03-11 18:02:48', '2026-03-11 18:02:57', '2026-03-11 18:01:34', '2026-03-11 18:02:57', 0, 9, 1, 0),
(112, 'GQ12-001', 59, 17, 0, NULL, 39, 'completed', 'Your document is ready for release', 1, '2026-03-11 18:04:52', '2026-03-11 18:05:01', '2026-03-11 18:05:05', '2026-03-11 18:04:45', '2026-03-11 18:05:05', 0, 4, 1, 0),
(113, 'CG12-003', 59, 14, 0, NULL, 41, 'completed', 'Your document is ready for release', 1, '2026-03-12 02:34:25', '2026-03-12 02:34:33', '2026-03-12 02:34:41', '2026-03-12 02:34:02', '2026-03-12 02:34:41', 0, 8, 1, 0),
(114, 'CG12-004', 59, 14, 0, NULL, 41, 'completed', 'Your document is ready for release', 1, '2026-03-12 02:36:57', '2026-03-12 02:37:02', '2026-03-12 02:37:05', '2026-03-12 02:36:53', '2026-03-12 02:37:05', 0, 3, 1, 0),
(115, 'CG13-001', 59, 14, 0, NULL, NULL, 'waiting', NULL, 1, NULL, NULL, NULL, '2026-03-13 09:11:05', '2026-03-13 09:11:05', 0, 0, 1, 0),
(116, 'CG13-002', 83, 14, 0, NULL, 41, 'completed', 'Your document is ready for release', 2, '2026-03-13 09:16:35', '2026-03-13 09:16:45', '2026-03-13 09:17:06', '2026-03-13 09:14:51', '2026-03-13 09:17:06', 0, 21, 1, 0),
(117, 'CG16-001', 83, 14, 0, NULL, 41, 'completed', 'Your document is ready for release.', 1, '2026-03-16 05:49:03', '2026-03-16 05:49:18', '2026-03-16 05:49:31', '2026-03-16 05:48:51', '2026-03-16 05:49:31', 0, 13, 1, 0),
(118, 'CG16-002', 97, 14, 0, NULL, 41, 'completed', 'Your document is ready for release.', 1, '2026-03-16 06:14:47', '2026-03-16 06:15:20', '2026-03-16 06:15:31', '2026-03-16 06:10:46', '2026-03-16 06:15:31', 0, 11, 1, 0);

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
(60, 'registrar_admin@equeue.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Registrar Admin', 'admin', '2026-03-08 08:46:55', '2026-03-10 11:26:09', 1, NULL, NULL, 0, 0, 0, NULL, 1, NULL),
(61, 'sas_admin@equeue.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'SAS Admin', 'admin', '2026-03-08 08:46:55', '2026-03-08 08:46:55', 1, NULL, NULL, 0, 0, 0, NULL, 2, NULL),
(83, 'eklabushgulliver@gmail.com', 'NLP-22-00001', '$2y$10$70uWNsEFLHdeVYXw10rtke62I3ew2NcBx2Wf.9DGwQ0s2NSo2D9GC', 'Hezekiah Publico', 'user', '2026-03-10 15:20:57', '2026-03-10 15:21:57', 1, NULL, NULL, 8, 0, 0, NULL, NULL, 'CTE'),
(85, 'seangalace28@gmail.com', 'NLP-22-00021', '$2y$10$U77PrT15qy2VIcR2p/xvn.qdx5tGNWBEHYIm73j5Fm2x/cPZDjgWu', 'Sean Christian Andre G. Galace', 'user', '2026-03-11 02:18:34', '2026-03-11 02:19:03', 1, NULL, NULL, 0, 0, 0, NULL, NULL, 'CAS'),
(86, 'thanosthemadtitan0101@gmail.com', 'NLP-22-00673', '$2y$10$i1vD.Pl.Z2MFlJT2m/hX7eIqABZmBSSWh.DCn4A4Sqgt.VvYNY4Hi', 'Jay Paulo', 'user', '2026-03-11 02:26:30', '2026-03-11 03:15:21', 1, NULL, NULL, 8, 0, 0, NULL, NULL, 'CAS'),
(88, 'losfuerte16@gmail.com', 'nlp-11-12094', '$2y$10$z9LY6X2Ki/VX1Wo6rhvTGOva163kuKGf3rvJ1L1wBROpL8pUVWKAC', 'jan', 'user', '2026-03-11 02:32:52', '2026-03-11 02:32:52', 0, '347075', '2026-03-11 10:47:52', 0, 0, 0, NULL, NULL, 'CTE'),
(89, 'mikelserran22@gmail.com', NULL, '$2y$10$s3TpSCJXAszhH6RIW2HAtO.XkccskhwlBJJ0p2kSMgIFJxk9NunTC', 'Mikel Serran', 'user', '2026-03-11 03:42:43', '2026-03-11 03:42:43', 0, '029317', '2026-03-11 11:57:43', 0, 0, 0, NULL, NULL, 'CAS'),
(92, 'ispsc-w01@window.local', NULL, '$2y$10$65oS2hMAuTv5SqspxL5a../.kGHtsUeNZBIt/tTY0r7qpVx1uv1H6', 'Staff W-01', 'staff', '2026-03-11 17:54:03', '2026-03-11 17:54:03', 1, '685390', '2026-03-12 02:09:03', 0, 0, 0, NULL, NULL, NULL),
(93, 'ispsc-w02@window.local', NULL, '$2y$10$JU2itL6GgOtYCS0CSGQFGuLgRr/FhYQKJ6687tXM5PVkewHs3oyp.', 'Staff W-02', 'staff', '2026-03-11 17:57:22', '2026-03-11 17:57:22', 1, '231586', '2026-03-12 02:12:22', 0, 0, 0, NULL, NULL, NULL),
(94, 'ispsc-w03@window.local', NULL, '$2y$10$xuhIcBcZoxfr3GgkCtGzpOlyIHr146w7tm7IllPOxNaw2QsBPj8i2', 'Staff W-03', 'staff', '2026-03-12 02:31:06', '2026-03-12 02:31:06', 1, '885252', '2026-03-12 10:46:06', 0, 0, 0, NULL, NULL, NULL),
(95, 'ispsc-w04@window.local', NULL, '$2y$10$AMRD9Q0DNh7bQyKbWzLAe.dQEJNSAGkqNJ7XEisc7h7KUU42FeYhG', 'Staff W-04', 'staff', '2026-03-12 02:31:16', '2026-03-12 02:31:16', 1, '236655', '2026-03-12 10:46:16', 0, 0, 0, NULL, NULL, NULL),
(96, 'ispsc-w05@window.local', NULL, '$2y$10$B3R8kKBLAw9hAMBl21WN5.iFmcEMwuH7P3noxkO3r..OjPVLv8tFm', 'Staff W-05', 'staff', '2026-03-12 02:31:26', '2026-03-12 02:31:26', 1, '847662', '2026-03-12 10:46:26', 0, 0, 0, NULL, NULL, NULL),
(97, 'exodusgalimba@gmail.com', 'NLP-22-00600', '$2y$10$V4wUuD47KQqruqWPXXmzeekr2k9GxDQD.ORF88mxF5kvLgErcEXEa', 'Genesis Manzano', 'user', '2026-03-16 06:07:08', '2026-03-16 06:21:23', 1, NULL, NULL, 8, 0, 0, NULL, NULL, 'SCJE'),
(98, 'paulojayboy@gmail.con', NULL, '$2y$10$xpnqdvETlZevGs6zIQbPh.NeMa5W3xb9./MTE.ovZKWIaHPo4YrTG', 'shamrin', 'user', '2026-03-16 06:13:38', '2026-03-16 06:13:38', 0, '369717', '2026-03-16 14:28:38', 0, 0, 0, NULL, NULL, 'CAS');

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
(38, 'W-01', 'Window 1', NULL, 92, 1, '2026-03-11 17:54:03', '2026-03-11 17:57:54', 1, NULL),
(39, 'W-02', 'Window 2', NULL, 93, 1, '2026-03-11 17:57:22', '2026-03-11 17:58:53', 1, NULL),
(40, 'W-03', 'Window 3', NULL, 94, 1, '2026-03-12 02:31:06', '2026-03-12 02:33:00', 1, NULL),
(41, 'W-04', 'Window 4', NULL, 95, 1, '2026-03-12 02:31:16', '2026-03-16 06:18:10', 1, 'CTE'),
(42, 'W-05', 'Window 5', NULL, 96, 0, '2026-03-12 02:31:26', '2026-03-12 02:31:26', 1, NULL);

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
(80, 26, 15, 1, '2026-03-08 09:40:10'),
(81, 27, 14, 1, '2026-03-10 12:44:41'),
(82, 27, 17, 1, '2026-03-10 12:44:43'),
(83, 27, 16, 1, '2026-03-10 12:44:43'),
(84, 27, 19, 1, '2026-03-10 12:44:44'),
(85, 27, 18, 1, '2026-03-10 12:44:46'),
(86, 27, 15, 1, '2026-03-10 12:44:47'),
(87, 28, 14, 1, '2026-03-10 12:45:18'),
(88, 28, 17, 1, '2026-03-10 12:45:19'),
(89, 28, 16, 1, '2026-03-10 12:45:20'),
(90, 28, 19, 1, '2026-03-10 12:45:21'),
(91, 28, 18, 1, '2026-03-10 12:45:22'),
(92, 28, 15, 1, '2026-03-10 12:45:24'),
(93, 29, 14, 0, '2026-03-10 12:46:13'),
(94, 29, 17, 1, '2026-03-10 12:46:14'),
(95, 29, 16, 1, '2026-03-10 12:46:15'),
(96, 29, 19, 1, '2026-03-10 12:46:16'),
(97, 29, 18, 1, '2026-03-10 12:46:20'),
(98, 29, 15, 1, '2026-03-10 12:46:21'),
(99, 32, 22, 1, '2026-03-10 17:37:02'),
(100, 32, 20, 1, '2026-03-10 17:37:04'),
(101, 32, 21, 1, '2026-03-10 17:37:05'),
(102, 33, 22, 1, '2026-03-10 17:37:28'),
(103, 33, 21, 1, '2026-03-10 17:37:29'),
(104, 33, 20, 1, '2026-03-10 17:37:30'),
(105, 34, 22, 1, '2026-03-10 17:37:49'),
(106, 34, 21, 1, '2026-03-10 17:37:50'),
(107, 34, 20, 1, '2026-03-10 17:38:14'),
(108, 26, 23, 1, '2026-03-11 16:10:20'),
(109, 38, 14, 1, '2026-03-11 17:57:33'),
(110, 38, 17, 1, '2026-03-11 17:57:34'),
(111, 38, 22, 1, '2026-03-11 17:57:35'),
(112, 38, 21, 1, '2026-03-11 17:57:37'),
(113, 38, 24, 1, '2026-03-11 17:57:38'),
(114, 38, 23, 1, '2026-03-11 17:57:39'),
(115, 38, 19, 1, '2026-03-11 17:57:42'),
(116, 38, 18, 1, '2026-03-11 17:57:43'),
(117, 38, 15, 1, '2026-03-11 17:57:45'),
(118, 38, 20, 1, '2026-03-11 17:57:46'),
(119, 39, 14, 1, '2026-03-11 17:59:00'),
(120, 39, 17, 1, '2026-03-11 17:59:02'),
(121, 39, 22, 1, '2026-03-11 17:59:03'),
(122, 39, 21, 1, '2026-03-11 17:59:05'),
(123, 39, 24, 1, '2026-03-11 17:59:06'),
(124, 39, 23, 1, '2026-03-11 17:59:09'),
(125, 39, 18, 1, '2026-03-11 17:59:11'),
(126, 39, 19, 1, '2026-03-11 17:59:12'),
(127, 39, 15, 1, '2026-03-11 17:59:13'),
(128, 39, 20, 1, '2026-03-11 17:59:14'),
(129, 41, 14, 1, '2026-03-12 02:33:24'),
(130, 41, 17, 1, '2026-03-12 03:49:09'),
(131, 41, 22, 1, '2026-03-12 03:49:10'),
(132, 41, 21, 1, '2026-03-12 03:49:11'),
(133, 41, 24, 1, '2026-03-12 03:49:12'),
(134, 41, 23, 1, '2026-03-12 03:49:13'),
(135, 41, 18, 1, '2026-03-12 03:49:17'),
(136, 41, 19, 1, '2026-03-12 03:49:18'),
(137, 41, 15, 1, '2026-03-12 03:49:20'),
(138, 41, 20, 1, '2026-03-12 03:49:21');

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
  ADD UNIQUE KEY `unique_service_office` (`service_code`,`office_id`),
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
  ADD UNIQUE KEY `unique_window_office` (`window_number`,`office_id`),
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=240;

--
-- AUTO_INCREMENT for table `offices`
--
ALTER TABLE `offices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `windows`
--
ALTER TABLE `windows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `window_services`
--
ALTER TABLE `window_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

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

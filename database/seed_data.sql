-- Seed Data for ISPSC E-Queue System
-- Extracted from the LIVE database state
-- Categorized by table for easy reference.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+08:00";

-- --------------------------------------------------------
-- 1. OFFICES
-- --------------------------------------------------------
INSERT IGNORE INTO `offices` (`id`, `name`, `code`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Registrar Office', 'RGSTR', 'Campus Registrar Office', 1, '2026-03-08 16:17:36', '2026-03-08 16:46:55'),
(2, 'Student Affair Service', 'SAS', 'Student Affair Service Office', 1, '2026-03-08 16:46:55', '2026-03-08 16:46:55');

-- --------------------------------------------------------
-- 2. USERS
-- --------------------------------------------------------
INSERT IGNORE INTO `users` (`id`, `email`, `school_id`, `password`, `full_name`, `role`, `created_at`, `updated_at`, `is_verified`, `otp_code`, `otp_expiry`, `last_read_announcement_id`, `announcement_subscription`, `login_attempts`, `lockout_until`, `office_id`, `college`) VALUES
(1, 'admin@equeue.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', '2026-02-14 00:38:21', '2026-02-14 00:38:21', 1, NULL, NULL, 0, 0, 0, NULL, 1, NULL),
(18, 'w01@window.local', NULL, '$2y$10$Rm1uBXHsPpHPHOq/lyIyN.oS0ofP6rbdVHXE6aUPvlaJ7T83XrOle', 'Staff W-01', 'staff', '2026-02-14 16:04:35', '2026-02-14 16:04:35', 1, NULL, NULL, 0, 0, 0, NULL, 1, NULL),
(19, 'w02@window.local', NULL, '$2y$10$X86VU44sQQXZ98dGLcAvk.IfiR8QzF3WWrJvDgJ7Na742.cPLB1a.', 'Staff W-02', 'staff', '2026-02-14 22:21:48', '2026-02-14 22:21:48', 1, NULL, NULL, 0, 0, 0, NULL, 1, NULL),
(24, 'w03@window.local', NULL, '$2y$10$eL39jKmSNhnElHktV1l39.P6EZ60vhUsRTDdNGwWW/HpFVQkA18Ra', 'Staff W-03', 'staff', '2026-02-15 15:47:03', '2026-02-15 15:47:03', 1, NULL, NULL, 0, 0, 0, NULL, 1, NULL),
(25, 'w04@window.local', NULL, '$2y$10$0M4mIZjkDcgCnK7jxdNvUOjvaYp9nrZ/nvAxhBCpMVw8YXdREaPky', 'Staff W-04', 'staff', '2026-02-15 15:47:11', '2026-02-15 15:47:11', 1, NULL, NULL, 0, 0, 0, NULL, 1, NULL),
(26, 'w05@window.local', NULL, '$2y$10$qvhTIOh761yDJ/b0avt6C.l38yYzzpQJFWHGVJxIBmHd946Fn4lp6', 'Staff W-05', 'staff', '2026-02-15 15:47:16', '2026-02-15 15:47:16', 1, NULL, NULL, 0, 0, 0, NULL, 1, NULL),
(59, 'exodusgalimba@gmail.com', 'NLP-22-00679', '$2y$10$PYdjkPMY3.Dptfosjcqhne/dxWwXa6LLfSEIvAG5/Vmj9o5r7jiXq', 'Genesis Manzano', 'user', '2026-03-08 16:40:37', '2026-03-08 16:41:26', 1, NULL, NULL, 0, 0, 0, NULL, NULL, 'CAS'),
(60, 'registrar_admin@equeue.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Registrar Admin', 'admin', '2026-03-08 16:46:55', '2026-03-08 16:46:55', 1, NULL, NULL, 0, 0, 0, NULL, 1, NULL),
(61, 'sas_admin@equeue.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'SAS Admin', 'admin', '2026-03-08 16:46:55', '2026-03-08 16:46:55', 1, NULL, NULL, 0, 0, 0, NULL, 2, NULL),
(64, 'registrar_w01@equeue.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Registrar Staff W01', 'staff', '2026-03-08 16:48:28', '2026-03-08 16:48:28', 1, NULL, NULL, 0, 0, 0, NULL, 1, NULL),
(65, 'sas_w02@equeue.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'SAS Staff W02', 'staff', '2026-03-08 16:48:28', '2026-03-08 16:48:28', 1, NULL, NULL, 0, 0, 0, NULL, 2, NULL);

-- --------------------------------------------------------
-- 3. SERVICES
-- --------------------------------------------------------
INSERT IGNORE INTO `services` (`id`, `service_name`, `service_code`, `description`, `requirements`, `staff_notes`, `estimated_time`, `target_time`, `is_active`, `created_at`, `updated_at`, `office_id`) VALUES
(14, 'Certificate of Grades', 'CG', '', 'Route Sheet\r\nValid ID\r\nCashier Receipt', '', 10, 10, 1, '2026-02-14 10:22:06', '2026-03-08 16:29:31', 1),
(15, 'Request of Diploma', 'DP', '', 'Official Transcript of Records', '', 10, 30, 1, '2026-02-14 10:23:01', '2026-03-08 16:30:27', 1),
(16, 'Good Moral Character', 'GM', 'Good Moral Character', 'Valid ID\r\nOfficial Transcript of Records', '', 10, 25, 1, '2026-02-14 15:39:51', '2026-03-08 16:30:01', 1),
(17, 'General Inquiry', 'GQ', 'General Concerns', 'None', '', 10, 15, 1, '2026-02-15 03:44:00', '2026-03-08 16:29:54', 1),
(18, 'Request for Official Transcript of Records', 'TR', 'Transcript of Records', 'Clearance\r\nLatest ID Picture\r\nSchool ID', 'na ay', 10, 4326, 1, '2026-02-19 16:31:08', '2026-03-08 16:30:16', 1),
(19, 'Request for Enrollment and Billing', 'EB', 'Certificate of Enrollment and Billing', 'Registration Form\r\nScholl ID Duly Validated', 'Don&amp;#039;t be stupid', 10, 5, 1, '2026-02-19 16:34:35', '2026-03-08 16:30:08', 1);

-- --------------------------------------------------------
-- 4. WINDOWS
-- --------------------------------------------------------
INSERT IGNORE INTO `windows` (`id`, `window_number`, `window_name`, `location_info`, `staff_id`, `is_active`, `created_at`, `updated_at`, `office_id`, `preferred_colleges`) VALUES
(14, 'W-01', 'CAS', NULL, 18, 1, '2026-02-14 16:04:35', '2026-03-08 16:37:14', 1, 'CAS,SCJE,CTE,CBME'),
(15, 'W-02', 'SCJE', NULL, 19, 1, '2026-02-14 22:21:48', '2026-03-08 16:32:35', 1, NULL),
(16, 'W-03', 'CBME', NULL, 24, 1, '2026-02-15 15:47:03', '2026-03-08 16:34:28', 1, NULL),
(17, 'W-04', 'CTE', NULL, 25, 1, '2026-02-15 15:47:11', '2026-03-08 16:34:34', 1, NULL),
(18, 'W-05', 'Priority', NULL, 26, 0, '2026-02-15 15:47:16', '2026-03-08 16:34:45', 1, NULL),
(20, 'REG-W01', 'Registrar Window 01', NULL, 64, 1, '2026-03-08 16:46:55', '2026-03-08 16:49:49', 1, NULL),
(21, 'SAS-W02', 'SAS Window 02', NULL, 65, 1, '2026-03-08 16:46:55', '2026-03-08 16:49:49', 2, NULL);

-- --------------------------------------------------------
-- 5. WINDOW_SERVICES
-- --------------------------------------------------------
INSERT IGNORE INTO `window_services` (`id`, `window_id`, `service_id`, `is_enabled`, `created_at`) VALUES
(37, 14, 14, 1, '2026-02-14 16:04:45'),
(38, 14, 15, 1, '2026-02-14 16:04:45'),
(39, 14, 16, 1, '2026-02-14 16:04:45'),
(40, 15, 14, 0, '2026-02-14 22:22:01'),
(41, 15, 15, 0, '2026-02-14 22:22:01'),
(42, 15, 16, 0, '2026-02-14 22:22:01'),
(43, 14, 17, 1, '2026-02-15 03:47:13'),
(44, 15, 17, 1, '2026-02-15 03:47:13'),
(49, 16, 14, 0, '2026-02-15 17:04:36'),
(50, 16, 17, 0, '2026-02-15 17:04:37'),
(51, 16, 16, 0, '2026-02-15 17:04:38'),
(52, 16, 15, 1, '2026-02-15 17:04:38'),
(53, 17, 14, 0, '2026-02-15 17:05:56'),
(54, 17, 15, 0, '2026-02-15 17:05:56'),
(55, 17, 16, 0, '2026-02-15 17:05:56'),
(56, 17, 17, 0, '2026-02-15 17:05:56'),
(57, 18, 14, 0, '2026-02-16 00:18:16'),
(58, 18, 15, 0, '2026-02-16 00:18:16'),
(59, 18, 16, 0, '2026-02-16 00:18:16'),
(60, 18, 17, 0, '2026-02-16 00:18:16'),
(61, 19, 14, 0, '2026-02-16 00:18:28'),
(62, 19, 15, 1, '2026-02-16 00:18:28'),
(63, 19, 16, 0, '2026-02-16 00:18:28'),
(64, 19, 17, 0, '2026-02-16 00:18:28'),
(65, 14, 19, 1, '2026-02-19 16:43:57'),
(66, 14, 18, 1, '2026-02-19 16:43:58'),
(67, 17, 18, 1, '2026-02-19 16:48:08');

-- --------------------------------------------------------
-- 6. TICKETS
-- --------------------------------------------------------
INSERT IGNORE INTO `tickets` (`id`, `ticket_number`, `user_id`, `service_id`, `auto_generated`, `user_note`, `window_id`, `status`, `staff_notes`, `queue_position`, `called_at`, `served_at`, `completed_at`, `created_at`, `updated_at`, `is_archived`, `service_time_accumulated`, `is_priority`, `office_id`) VALUES
(36, 'DPLM0219-001', 54, 15, 0, NULL, 16, 'completed', '', 1, '2026-02-19 16:49:32', '2026-02-19 16:52:18', '2026-02-19 16:52:24', '2026-02-19 16:45:55', '2026-02-19 16:52:24', 0, 6, 0, 1),
(37, 'OTR0219-001', 55, 18, 0, NULL, 17, 'completed', '', 2, '2026-02-19 16:49:46', '2026-02-19 16:50:32', '2026-02-19 16:51:00', '2026-02-19 16:45:59', '2026-02-19 16:51:00', 0, 28, 0, 1),
(38, 'OTR0219-002', 57, 18, 0, NULL, NULL, 'cancelled', NULL, 3, NULL, NULL, NULL, '2026-02-19 16:46:16', '2026-02-19 16:46:54', 0, 0, 0, 1),
(40, 'COG0219-001', 57, 14, 0, NULL, 14, 'completed', '', 4, '2026-02-19 16:48:51', '2026-02-19 16:51:39', '2026-02-19 16:52:50', '2026-02-19 16:47:06', '2026-02-19 16:52:50', 0, 71, 0, 1),
(41, 'COG0219-002', 54, 14, 0, NULL, NULL, 'cancelled', NULL, 1, NULL, NULL, NULL, '2026-02-19 16:53:03', '2026-02-19 16:53:13', 0, 0, 0, 1),
(42, 'COG0219-003', 54, 14, 0, NULL, 14, 'completed', '', 1, '2026-02-19 16:53:27', '2026-02-19 16:53:33', '2026-02-19 16:53:37', '2026-02-19 16:53:25', '2026-02-19 16:53:37', 0, 4, 0, 1),
(44, 'COG0219-004', 55, 14, 0, NULL, 14, 'completed', '', 1, '2026-02-19 17:19:41', '2026-02-19 17:19:56', '2026-02-19 17:20:04', '2026-02-19 17:16:48', '2026-02-19 17:20:04', 0, 8, 0, 1),
(45, 'GEN-INQ0219-001', 54, 17, 0, '', 14, 'completed', '', 2, '2026-02-19 17:24:24', '2026-02-19 17:26:40', '2026-02-19 17:26:45', '2026-02-19 17:17:02', '2026-02-19 17:26:45', 0, 5, 0, 1),
(46, 'COG0219-005', 57, 14, 0, NULL, 14, 'completed', '', 3, '2026-02-19 17:20:12', '2026-02-19 17:20:19', '2026-02-19 17:20:27', '2026-02-19 17:17:20', '2026-02-19 17:20:27', 0, 8, 0, 1),
(47, 'COG0219-006', 58, 14, 0, NULL, 14, 'completed', '', 4, '2026-02-19 17:20:38', '2026-02-19 17:21:13', '2026-02-19 17:22:07', '2026-02-19 17:17:50', '2026-02-19 17:22:07', 0, 54, 0, 1),
(48, 'GEN-INQ0219-002', 58, 17, 0, '', 14, 'completed', '', 2, '2026-02-19 17:24:34', '2026-02-19 17:24:46', '2026-02-19 17:24:50', '2026-02-19 17:23:47', '2026-02-19 17:24:50', 0, 4, 0, 1),
(51, 'COG0219-007', 55, 14, 0, NULL, 14, 'completed', '', 1, '2026-02-19 17:30:58', '2026-02-19 17:31:10', '2026-02-19 17:31:32', '2026-02-19 17:27:16', '2026-02-19 17:31:32', 0, 22, 0, 1),
(52, 'COG0219-008', 54, 14, 0, NULL, 14, 'completed', '', 2, '2026-02-19 17:31:53', NULL, '2026-02-19 17:32:46', '2026-02-19 17:27:17', '2026-02-19 17:32:46', 0, 0, 0, 1),
(53, 'COG0219-009', 57, 14, 0, NULL, 14, 'completed', '', 1, '2026-02-19 17:42:39', '2026-02-19 17:43:26', '2026-02-19 17:44:01', '2026-02-19 17:41:33', '2026-02-19 17:44:01', 0, 35, 0, 1),
(54, 'COG0219-010', 54, 14, 0, NULL, 14, 'cancelled', NULL, 2, '2026-03-08 16:27:17', NULL, NULL, '2026-02-19 17:42:34', '2026-03-08 16:31:57', 0, 0, 0, 1);

-- --------------------------------------------------------
-- 7. ANNOUNCEMENTS
-- --------------------------------------------------------
INSERT IGNORE INTO `announcements` (`id`, `title`, `content`, `image_path`, `created_at`, `updated_at`, `office_id`) VALUES
(8, '𝗜𝗦𝗣𝗦𝗖 𝗔𝗡𝗔𝗥𝗔𝗔𝗥 -𝗝𝗔𝗡𝗨𝗔𝗥𝗬 𝟮𝟬𝟮𝟲 𝗥𝗘𝗚𝗨𝗟𝗔𝗥 𝗜𝗦𝗦𝗨𝗘', '<p>Here are the news and updates you need to know about our college for January 2026.</p><p><br></p><p>You can read the newsletter through this link:</p><p>https://online.fliphtml5.com/.../January-2026-Anaraar-8fEK/</p><p>You may also scan the QR code in the image to access it easily on your mobile device.</p><p><br></p><p>This newsletter is brought to you by the ISPSC Office for Strategic Communication and Institutional Branding.</p><p><br></p><p>#𝗜𝗦𝗣𝗦𝗖</p><p>#𝗨𝗻𝗶𝘃𝗲𝗿𝘀𝗶𝘁𝘆𝗢𝗳𝗜𝗹𝗼𝗰𝗼𝘀𝗣𝗵𝗶𝗹𝗶𝗽𝗽𝗶𝗻𝗲𝘀</p><p>#𝗢𝗻𝘄𝗮𝗿𝗱𝘀𝗨𝗜𝗣</p>', 'uploads/announcements/6996d9a5642ac.jpg', '2026-02-19 09:36:37', '2026-02-19 09:36:37', 1);

-- --------------------------------------------------------
-- 8. FEEDBACK
-- --------------------------------------------------------
INSERT IGNORE INTO `feedback` (`id`, `ticket_id`, `user_id`, `window_id`, `rating`, `comment`, `sentiment`, `sentiment_score`, `created_at`) VALUES
(18, 36, 54, 16, NULL, 'Ulol', 'neutral', 0.0000, '2026-02-19 08:52:36'),
(19, 42, 54, 14, NULL, 'Very good', 'positive', 0.4000, '2026-02-19 08:53:54'),
(20, 40, 57, 14, NULL, 'very poor', 'negative', -0.4000, '2026-02-19 08:53:54'),
(22, 37, 55, 17, NULL, 'Service is very poor', 'negative', -0.4000, '2026-02-19 08:53:55'),
(23, 47, 58, 14, NULL, 'NICE', 'neutral', 0.0000, '2026-02-19 09:23:29'),
(24, 44, 55, 14, NULL, 'very nice', 'positive', 0.4000, '2026-02-19 09:27:04'),
(25, 45, 54, 14, NULL, 'Bad', 'negative', -0.4000, '2026-02-19 09:27:15'),
(26, 46, 57, 14, NULL, 'very good', 'positive', 0.4000, '2026-02-19 09:28:08'),
(27, 52, 54, 14, NULL, 'Jdndj', 'neutral', 0.0000, '2026-02-19 09:42:27'),
(28, 48, 58, 14, NULL, 'sheesh', 'positive', 0.4000, '2026-02-19 14:58:21');

-- --------------------------------------------------------
-- 9. NOTIFICATIONS
-- --------------------------------------------------------
INSERT IGNORE INTO `notifications` (`id`, `user_id`, `ticket_id`, `type`, `message`, `is_read`, `created_at`) VALUES
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
(117, 57, 53, 'completed', 'Transaction completed. Please provide your feedback.', 0, '2026-02-19 09:44:01');

-- --------------------------------------------------------
-- 10. AI CONTEXT
-- --------------------------------------------------------
INSERT IGNORE INTO `ai_context` (`id`, `content`, `updated_at`, `office_id`) VALUES
(1, '<h2>Certificate of Grades (COG)</h2><p>Requirements:</p><ul><li>Route Sheet,</li><li>Receipt</li><li>ID</li></ul>', '2026-02-19 08:13:01', 1);

COMMIT;

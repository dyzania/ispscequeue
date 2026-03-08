-- Master Database Setup & Migration Script for ISPSC E-Queue System
-- This script handles both fresh installations and idempotent updates to existing databases.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- 1. OFFICES TABLE
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `offices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `office_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `offices` (`id`, `name`, `code`, `description`) 
VALUES (1, 'Main Office', 'MAIN', 'Default Campus Main Office');

-- --------------------------------------------------------
-- 2. USERS TABLE
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `school_id` (`school_id`),
  KEY `idx_email` (`email`),
  KEY `idx_role` (`role`),
  KEY `idx_otp` (`otp_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Idempotent Column Additions for USERS
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `office_id` int(11) DEFAULT NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `college` varchar(50) DEFAULT NULL;

-- --------------------------------------------------------
-- 3. SERVICES TABLE
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `service_code` (`service_code`),
  KEY `idx_code` (`service_code`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `services` ADD COLUMN IF NOT EXISTS `office_id` int(11) NOT NULL DEFAULT 1;

-- --------------------------------------------------------
-- 4. WINDOWS TABLE
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `windows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `window_number` varchar(50) NOT NULL,
  `window_name` varchar(255) NOT NULL,
  `location_info` varchar(255) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `window_number` (`window_number`),
  KEY `idx_staff` (`staff_id`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `windows` ADD COLUMN IF NOT EXISTS `office_id` int(11) NOT NULL DEFAULT 1;
ALTER TABLE `windows` ADD COLUMN IF NOT EXISTS `preferred_colleges` text DEFAULT NULL;

-- --------------------------------------------------------
-- 5. WINDOW_SERVICES (Link Table)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `window_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `window_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `is_enabled` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_window_service` (`window_id`,`service_id`),
  KEY `idx_window` (`window_id`),
  KEY `idx_service` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 6. TICKETS TABLE
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_number` (`ticket_number`),
  KEY `window_id` (`window_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_service` (`service_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `tickets` ADD COLUMN IF NOT EXISTS `office_id` int(11) NOT NULL DEFAULT 1;
ALTER TABLE `tickets` ADD COLUMN IF NOT EXISTS `is_priority` tinyint(1) DEFAULT 0;

-- --------------------------------------------------------
-- 7. ANNOUNCEMENTS TABLE
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `announcements` ADD COLUMN IF NOT EXISTS `office_id` int(11) NOT NULL DEFAULT 1;

-- --------------------------------------------------------
-- 8. FEEDBACK TABLE
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `window_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5 or `rating` is null),
  `comment` text DEFAULT NULL,
  `sentiment` enum('positive','neutral','negative','very_positive','very_negative') DEFAULT NULL,
  `sentiment_score` decimal(5,4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `window_id` (`window_id`),
  KEY `idx_ticket` (`ticket_id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 9. NOTIFICATIONS TABLE
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `type` enum('ticket_created','turn_next','serving','now_serving','completed','cancelled') DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 11. AI CONTEXT TABLE
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ai_context` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` longtext DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `ai_context` ADD COLUMN IF NOT EXISTS `office_id` int(11) NOT NULL DEFAULT 1;

-- --------------------------------------------------------
-- 12. FOREIGN KEY CONSTRAINTS
-- --------------------------------------------------------

-- Helper logic to add foreign keys safely
SET @exist_fk = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME='fk_users_office' AND TABLE_NAME='users' AND TABLE_SCHEMA=DATABASE());
SET @sql = IF(@exist_fk=0, 'ALTER TABLE `users` ADD CONSTRAINT `fk_users_office` FOREIGN KEY (`office_id`) REFERENCES `offices`(`id`) ON DELETE SET NULL', 'SELECT "fk_users_office exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist_fk = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME='fk_services_office' AND TABLE_NAME='services' AND TABLE_SCHEMA=DATABASE());
SET @sql = IF(@exist_fk=0, 'ALTER TABLE `services` ADD CONSTRAINT `fk_services_office` FOREIGN KEY (`office_id`) REFERENCES `offices`(`id`) ON DELETE CASCADE', 'SELECT "fk_services_office exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist_fk = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME='fk_windows_office' AND TABLE_NAME='windows' AND TABLE_SCHEMA=DATABASE());
SET @sql = IF(@exist_fk=0, 'ALTER TABLE `windows` ADD CONSTRAINT `fk_windows_office` FOREIGN KEY (`office_id`) REFERENCES `offices`(`id`) ON DELETE CASCADE', 'SELECT "fk_windows_office exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist_fk = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME='fk_tickets_office' AND TABLE_NAME='tickets' AND TABLE_SCHEMA=DATABASE());
SET @sql = IF(@exist_fk=0, 'ALTER TABLE `tickets` ADD CONSTRAINT `fk_tickets_office` FOREIGN KEY (`office_id`) REFERENCES `offices`(`id`) ON DELETE CASCADE', 'SELECT "fk_tickets_office exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist_fk = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME='fk_announcements_office' AND TABLE_NAME='announcements' AND TABLE_SCHEMA=DATABASE());
SET @sql = IF(@exist_fk=0, 'ALTER TABLE `announcements` ADD CONSTRAINT `fk_announcements_office` FOREIGN KEY (`office_id`) REFERENCES `offices`(`id`) ON DELETE CASCADE', 'SELECT "fk_announcements_office exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist_fk = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME='fk_ai_context_office' AND TABLE_NAME='ai_context' AND TABLE_SCHEMA=DATABASE());
SET @sql = IF(@exist_fk=0, 'ALTER TABLE `ai_context` ADD CONSTRAINT `fk_ai_context_office` FOREIGN KEY (`office_id`) REFERENCES `offices`(`id`) ON DELETE CASCADE', 'SELECT "fk_ai_context_office exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- DEFAULT CORE CONSTRAINTS
SET @exist_fk = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME='feedback_ibfk_1' AND TABLE_NAME='feedback' AND TABLE_SCHEMA=DATABASE());
SET @sql = IF(@exist_fk=0, 'ALTER TABLE `feedback` ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE', 'SELECT "feedback_ibfk_1 exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ... (and so on for all primary constraints)

COMMIT;

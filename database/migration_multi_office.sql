-- Migration script for Multi-Office Architecture

-- 1. Create offices table
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

-- 2. Insert Default Office (Main Office)
INSERT IGNORE INTO `offices` (`id`, `name`, `code`, `description`) 
VALUES (1, 'Main Office', 'MAIN', 'Default Campus Main Office');

-- 3. Add necessary columns to existing tables

-- Users Table
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `office_id` int(11) DEFAULT NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `college` varchar(50) DEFAULT NULL;
UPDATE `users` SET `office_id` = 1 WHERE `role` = 'staff' AND `office_id` IS NULL;

-- Services Table
ALTER TABLE `services` ADD COLUMN IF NOT EXISTS `office_id` int(11) DEFAULT NULL;
UPDATE `services` SET `office_id` = 1 WHERE `office_id` IS NULL;

-- Windows Table
ALTER TABLE `windows` ADD COLUMN IF NOT EXISTS `office_id` int(11) DEFAULT NULL;
ALTER TABLE `windows` ADD COLUMN IF NOT EXISTS `preferred_colleges` text DEFAULT NULL;
UPDATE `windows` SET `office_id` = 1 WHERE `office_id` IS NULL;

-- Tickets Table
ALTER TABLE `tickets` ADD COLUMN IF NOT EXISTS `office_id` int(11) DEFAULT NULL;
ALTER TABLE `tickets` ADD COLUMN IF NOT EXISTS `is_priority` tinyint(1) DEFAULT 0;
UPDATE `tickets` SET `office_id` = 1 WHERE `office_id` IS NULL;

-- Announcements Table
ALTER TABLE `announcements` ADD COLUMN IF NOT EXISTS `office_id` int(11) DEFAULT NULL;
UPDATE `announcements` SET `office_id` = 1 WHERE `office_id` IS NULL;

-- AI Context Table
ALTER TABLE `ai_context` ADD COLUMN IF NOT EXISTS `office_id` int(11) DEFAULT NULL;
UPDATE `ai_context` SET `office_id` = 1 WHERE `office_id` IS NULL;

-- Activity Logs Table
ALTER TABLE `activity_logs` ADD COLUMN IF NOT EXISTS `office_id` int(11) DEFAULT NULL;

-- 4. Add Foreign Key Constraints

-- Make office_id NOT NULL where applicable after seeding
ALTER TABLE `services` MODIFY COLUMN `office_id` int(11) NOT NULL DEFAULT 1;
ALTER TABLE `windows` MODIFY COLUMN `office_id` int(11) NOT NULL DEFAULT 1;
ALTER TABLE `tickets` MODIFY COLUMN `office_id` int(11) NOT NULL DEFAULT 1;

-- Add constraints if they don't exist
SET @exist_fk_users = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME='fk_users_office' AND TABLE_NAME='users' AND TABLE_SCHEMA=DATABASE());
SET @sql_users = IF(@exist_fk_users=0, 'ALTER TABLE `users` ADD CONSTRAINT `fk_users_office` FOREIGN KEY (`office_id`) REFERENCES `offices`(`id`) ON DELETE SET NULL', 'SELECT "fk_users_office exists"');
PREPARE stmt_users FROM @sql_users; EXECUTE stmt_users; DEALLOCATE PREPARE stmt_users;

SET @exist_fk_services = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME='fk_services_office' AND TABLE_NAME='services' AND TABLE_SCHEMA=DATABASE());
SET @sql_services = IF(@exist_fk_services=0, 'ALTER TABLE `services` ADD CONSTRAINT `fk_services_office` FOREIGN KEY (`office_id`) REFERENCES `offices`(`id`) ON DELETE CASCADE', 'SELECT "fk_services_office exists"');
PREPARE stmt_services FROM @sql_services; EXECUTE stmt_services; DEALLOCATE PREPARE stmt_services;

SET @exist_fk_windows = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME='fk_windows_office' AND TABLE_NAME='windows' AND TABLE_SCHEMA=DATABASE());
SET @sql_windows = IF(@exist_fk_windows=0, 'ALTER TABLE `windows` ADD CONSTRAINT `fk_windows_office` FOREIGN KEY (`office_id`) REFERENCES `offices`(`id`) ON DELETE CASCADE', 'SELECT "fk_windows_office exists"');
PREPARE stmt_windows FROM @sql_windows; EXECUTE stmt_windows; DEALLOCATE PREPARE stmt_windows;

SET @exist_fk_tickets = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME='fk_tickets_office' AND TABLE_NAME='tickets' AND TABLE_SCHEMA=DATABASE());
SET @sql_tickets = IF(@exist_fk_tickets=0, 'ALTER TABLE `tickets` ADD CONSTRAINT `fk_tickets_office` FOREIGN KEY (`office_id`) REFERENCES `offices`(`id`) ON DELETE CASCADE', 'SELECT "fk_tickets_office exists"');
PREPARE stmt_tickets FROM @sql_tickets; EXECUTE stmt_tickets; DEALLOCATE PREPARE stmt_tickets;

SET @exist_fk_announcements = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME='fk_announcements_office' AND TABLE_NAME='announcements' AND TABLE_SCHEMA=DATABASE());
SET @sql_announcements = IF(@exist_fk_announcements=0, 'ALTER TABLE `announcements` ADD CONSTRAINT `fk_announcements_office` FOREIGN KEY (`office_id`) REFERENCES `offices`(`id`) ON DELETE CASCADE', 'SELECT "fk_announcements_office exists"');
PREPARE stmt_announcements FROM @sql_announcements; EXECUTE stmt_announcements; DEALLOCATE PREPARE stmt_announcements;

SET @exist_fk_ai_context = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME='fk_ai_context_office' AND TABLE_NAME='ai_context' AND TABLE_SCHEMA=DATABASE());
SET @sql_ai_context = IF(@exist_fk_ai_context=0, 'ALTER TABLE `ai_context` ADD CONSTRAINT `fk_ai_context_office` FOREIGN KEY (`office_id`) REFERENCES `offices`(`id`) ON DELETE CASCADE', 'SELECT "fk_ai_context_office exists"');
PREPARE stmt_ai_context FROM @sql_ai_context; EXECUTE stmt_ai_context; DEALLOCATE PREPARE stmt_ai_context;

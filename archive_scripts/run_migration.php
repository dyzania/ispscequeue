<?php
require_once __DIR__ . '/config/config.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // 1. Create offices table
    $db->exec("
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
    ");
    echo "Table 'offices' created/verified.\n";

    // 2. Insert Default Office (Main Office)
    $db->exec("
        INSERT IGNORE INTO `offices` (`id`, `name`, `code`, `description`) 
        VALUES (1, 'Main Office', 'MAIN', 'Default Campus Main Office');
    ");
    echo "Default office seeded.\n";

    // 3. Add office_id to existing tables
    $tables = ['users', 'services', 'windows', 'tickets', 'announcements', 'ai_context'];
    
    foreach ($tables as $table) {
        try {
            // Check if column exists first to avoid errors
            $stmt = $db->prepare("SHOW COLUMNS FROM `$table` LIKE 'office_id'");
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                $db->exec("ALTER TABLE `$table` ADD COLUMN `office_id` int(11) DEFAULT NULL");
                echo "Added office_id to $table.\n";
            }
            
            // Set default value for existing rows
            if ($table === 'users') {
                $db->exec("UPDATE `$table` SET `office_id` = 1 WHERE `role` = 'staff' AND `office_id` IS NULL");
            } else {
                $db->exec("UPDATE `$table` SET `office_id` = 1 WHERE `office_id` IS NULL");
            }
            
            // For required tables, make it NOT NULL after seeding
            if (in_array($table, ['services', 'windows', 'tickets'])) {
                 $db->exec("ALTER TABLE `$table` MODIFY COLUMN `office_id` int(11) NOT NULL DEFAULT 1");
            }

            // Try adding foreign key (ignore if exists)
            try {
                $action = ($table === 'users') ? 'SET NULL' : 'CASCADE';
                $db->exec("ALTER TABLE `$table` ADD CONSTRAINT `fk_{$table}_office` FOREIGN KEY (`office_id`) REFERENCES `offices`(`id`) ON DELETE $action");
                echo "Added foreign key to $table.\n";
            } catch (PDOException $e) {
                // Constraint likely already exists or data inconsistency
            }

        } catch (PDOException $e) {
            echo "Error processing $table: " . $e->getMessage() . "\n";
        }
    }

    echo "\nMigration completed successfully!\n";

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . "\n");
}

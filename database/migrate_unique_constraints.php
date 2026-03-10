<?php
require_once __DIR__ . '/../config/config.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Starting migration: Fixing multi-office unique constraints...\n";

    // 1. Fix Windows Table
    echo "Updating 'windows' table...\n";
    // Check if index exists before dropping (standard approach for safety)
    $db->exec("ALTER TABLE windows DROP INDEX window_number");
    $db->exec("ALTER TABLE windows ADD UNIQUE KEY unique_window_office (window_number, office_id)");
    echo "Successfully updated 'windows' index.\n";

    // 2. Fix Services Table
    echo "Updating 'services' table...\n";
    $db->exec("ALTER TABLE services DROP INDEX service_code");
    $db->exec("ALTER TABLE services ADD UNIQUE KEY unique_service_office (service_code, office_id)");
    echo "Successfully updated 'services' index.\n";

    echo "Migration completed successfully!\n";

} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    if (strpos($e->getMessage(), "Can't DROP") !== false) {
        echo "Note: Some indexes might have already been modified.\n";
    }
}

<?php
require_once __DIR__ . '/config/config.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Add college to users
    $db->exec("ALTER TABLE users ADD COLUMN college VARCHAR(50) DEFAULT NULL AFTER school_id");
    echo "Added 'college' column to 'users' table.\n";
    
    // Add preferred_colleges to windows
    $db->exec("ALTER TABLE windows ADD COLUMN preferred_colleges TEXT DEFAULT NULL");
    echo "Added 'preferred_colleges' column to 'windows' table.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

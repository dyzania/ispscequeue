<?php
require_once __DIR__ . '/config/config.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Start transaction
    $db->beginTransaction();

    // 1. Delete Library Admin User
    $stmt = $db->prepare("DELETE FROM users WHERE email = 'library_admin@equeue.com'");
    $stmt->execute();
    echo "Deleted user library_admin@equeue.com (if existed).\n";

    // 2. Delete Library Office (ID 3)
    // Foreign key constraints (ON DELETE CASCADE) should handle child records 
    // in services, windows, tickets, announcements, and ai_context.
    $stmt = $db->prepare("DELETE FROM offices WHERE id = 3 OR code = 'LBRY'");
    $stmt->execute();
    $count = $stmt->rowCount();
    
    if ($count > 0) {
        echo "Deleted Library office from 'offices' table.\n";
    } else {
        echo "Library office not found or already deleted.\n";
    }

    $db->commit();
    echo "Cleanup completed successfully!\n";

} catch (Exception $e) {
    if (isset($db)) $db->rollBack();
    die("Cleanup failed: " . $e->getMessage() . "\n");
}

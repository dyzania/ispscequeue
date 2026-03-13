<?php
require_once __DIR__ . '/config/config.php';

try {
    $db = Database::getInstance()->getConnection();
    
    $errors = [];

    // 1. Check Office
    $stmt = $db->query("SELECT COUNT(*) FROM offices WHERE id = 3 OR name LIKE '%Library%'");
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Office 'Library' still exists in 'offices' table.";
    }

    // 2. Check User
    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE email = 'library_admin@equeue.com'");
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "User 'library_admin@equeue.com' still exists.";
    }

    // 3. Check orphaned services
    $stmt = $db->query("SELECT COUNT(*) FROM services WHERE office_id = 3");
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Services linked to office ID 3 still exist.";
    }

    // 4. Check orphaned windows
    $stmt = $db->query("SELECT COUNT(*) FROM windows WHERE office_id = 3");
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Windows linked to office ID 3 still exist.";
    }

    if (empty($errors)) {
        echo "VERIFICATION SUCCESS: Library office and related data have been completely removed.\n";
    } else {
        echo "VERIFICATION FAILED:\n";
        foreach ($errors as $error) {
            echo "- $error\n";
        }
    }

} catch (Exception $e) {
    die("Verification script failed: " . $e->getMessage() . "\n");
}

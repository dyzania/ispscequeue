<?php
require_once __DIR__ . '/../config/config.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "--- Orphaned Staff Users Clean-up Tool ---\n";
    
    // Find users with role 'staff' and @window.local email that ARE NOT referenced in the windows table
    $query = "
        SELECT id, email, full_name, role 
        FROM users 
        WHERE role = 'staff' 
        AND email LIKE '%@window.local'
        AND id NOT IN (SELECT staff_id FROM windows WHERE staff_id IS NOT NULL)
    ";
    
    $stmt = $db->query($query);
    $orphans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($orphans)) {
        echo "No orphaned staff users found.\n";
    } else {
        echo "Found " . count($orphans) . " orphaned staff user(s):\n";
        foreach ($orphans as $user) {
            echo "ID: " . $user['id'] . " | Email: " . $user['email'] . " | Name: " . $user['full_name'] . "\n";
        }
        
        // Option to delete
        echo "\nDeleting orphans...\n";
        $ids = array_column($orphans, 'id');
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $db->prepare("DELETE FROM users WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        echo "Successfully deleted " . count($orphans) . " orphaned user(s).\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

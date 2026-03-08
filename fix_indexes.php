<?php
require 'config/config.php';
$db = Database::getInstance()->getConnection();

$stmt = $db->query("SHOW INDEX FROM windows");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

try {
    // Attempting to drop if it was named something else, like a constraint
    $db->exec("ALTER TABLE windows DROP INDEX window_number");
} catch(Exception $e) {
    echo "Drop window_number error: " . $e->getMessage() . "\n";
}

try {
    $db->exec("ALTER TABLE windows ADD UNIQUE INDEX idx_office_window (office_id, window_number)");
    echo "Added composite index success.\n";
} catch(Exception $e) {
    echo "Add composite index error: " . $e->getMessage() . "\n";
}

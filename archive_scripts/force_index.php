<?php
require 'config/config.php';
$db = Database::getInstance()->getConnection();

try {
    $db->exec("ALTER TABLE windows DROP INDEX window_number");
    echo "SUCCESS: Dropped old global window_number index.\n";
} catch(Exception $e) {
    echo "Drop error: " . $e->getMessage() . "\n";
}

try {
    // Just in case it's not there yet
    $db->exec("ALTER TABLE windows ADD UNIQUE INDEX idx_office_window (office_id, window_number)");
    echo "SUCCESS: Added new office-scoped composite index.\n";
} catch(Exception $e) {
    echo "Add error: " . $e->getMessage() . "\n";
}

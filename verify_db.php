<?php
require_once __DIR__ . '/config/config.php';
$db = Database::getInstance()->getConnection();

$stmt = $db->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

$hasOffice = false;

echo "TABLES:\n";
foreach($tables as $t) {
    if ($t === 'offices') $hasOffice = true;
    echo "- $t\n";
}

if ($hasOffice) {
    echo "FAILED: offices table still exists.\n";
} else {
    echo "SUCCESS: offices table is gone.\n";
}

// Check columns for any remaining 'office'
$remaining = 0;
foreach($tables as $t) {
    $cStmt = $db->query("SHOW COLUMNS FROM `$t`");
    $cols = $cStmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($cols as $c) {
        if (strpos($c['Field'], 'office') !== false) {
            echo "FAILED: $t still has column {$c['Field']}\n";
            $remaining++;
        }
    }
}

if ($remaining === 0) {
    echo "SUCCESS: All office columns removed.\n";
}

<?php
require_once __DIR__ . '/config/config.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SHOW COLUMNS FROM services");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "COLUMNS:\n";
foreach ($columns as $c) {
    echo $c['Field'] . "\n";
}

$stmt = $db->query("SHOW INDEX FROM services");
$indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "\nINDEXES:\n";
foreach ($indexes as $i) {
    echo $i['Key_name'] . " - " . $i['Column_name'] . "\n";
}

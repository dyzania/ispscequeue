<?php
require_once __DIR__ . '/config/config.php';
$db = Database::getInstance()->getConnection();

try {
    $db->exec("ALTER TABLE services DROP INDEX unique_service_office");
    echo "Dropped index unique_service_office\n";
} catch (Exception $e) { echo "Index unique_service_office probably doesn't exist.\n"; }

try {
    $db->exec("ALTER TABLE services DROP INDEX fk_services_office");
    echo "Dropped index fk_services_office\n";
} catch (Exception $e) { echo "Index fk_services_office probably doesn't exist.\n"; }

try {
    $db->exec("ALTER TABLE services DROP COLUMN office_id");
    echo "Dropped column office_id from services\n";
} catch (Exception $e) { echo "office_id probably doesn't exist in services.\n"; }

try {
    $db->exec("ALTER TABLE services ADD UNIQUE KEY unique_service_code (service_code)");
    echo "Added unique index unique_service_code\n";
} catch (Exception $e) { echo "Index unique_service_code couldn't be added (maybe already exists).\n"; }

$stmt = $db->query("SHOW COLUMNS FROM services");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Current services columns:\n";
foreach ($cols as $c) {
    if (strpos($c['Field'], 'office') !== false) {
        echo "FOUND OFFICE: " . $c['Field'] . "\n";
    }
}
echo "Done.\n";

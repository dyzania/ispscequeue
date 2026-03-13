<?php
require 'config/config.php';
$db = Database::getInstance()->getConnection();

$tables = [
    'offices',
    'users',
    'services',
    'windows',
    'window_services',
    'tickets',
    'announcements',
    'feedback',
    'notifications',
    'ai_context'
];

$dump = [];

foreach ($tables as $table) {
    try {
        $stmt = $db->query("SELECT * FROM `$table` ORDER BY id ASC");
        $dump[$table] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $dump[$table] = ["error" => $e->getMessage()];
    }
}

file_put_contents('full_dump.json', json_encode($dump, JSON_PRETTY_PRINT));
echo "FULL DUMP COMPLETED";

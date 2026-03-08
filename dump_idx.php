<?php
require 'config/config.php';
header('Content-Type: application/json');
$db = Database::getInstance()->getConnection();

$stmt = $db->query("SHOW INDEX FROM windows");
$indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);

file_put_contents('index_dump.json', json_encode($indexes, JSON_PRETTY_PRINT));
echo "Dumped to index_dump.json\n";

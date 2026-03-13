<?php
require 'config/config.php';
$db = Database::getInstance()->getConnection();

$stmt = $db->query("SELECT id, name, code FROM offices");
$offices = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->query("SELECT id, email, full_name, office_id, role FROM users WHERE role = 'admin'");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

file_put_contents('dump_direct.json', json_encode([
    'offices' => $offices,
    'users' => $users
], JSON_PRETTY_PRINT));
echo "DONE";

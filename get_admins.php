<?php
require 'config/config.php';
$db = Database::getInstance()->getConnection();

echo "--- OFFICES ---\n";
$stmt = $db->query("SELECT * FROM offices");
$offices = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($offices);

echo "\n--- ADMINS ---\n";
$stmt = $db->query("SELECT id, email, full_name, office_id, role FROM users WHERE role = 'admin'");
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($admins);

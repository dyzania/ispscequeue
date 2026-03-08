<?php
require 'config/config.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT email FROM users WHERE role = 'staff'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

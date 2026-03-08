<?php
require 'config/config.php';
$db = Database::getInstance()->getConnection();

$stmt = $db->prepare("
    DELETE FROM users 
    WHERE role = 'staff' 
    AND id NOT IN (SELECT staff_id FROM windows WHERE staff_id IS NOT NULL)
");
$stmt->execute();
echo "Cleaned orphaned staff users: " . $stmt->rowCount() . "\n";

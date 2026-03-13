<?php
require 'config/config.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->query('SELECT service_code, service_name FROM services');
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($services, JSON_PRETTY_PRINT);

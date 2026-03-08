<?php
require_once __DIR__ . '/config/config.php';

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT * FROM offices");
    $offices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($offices);
} catch (Exception $e) {
    echo $e->getMessage();
}

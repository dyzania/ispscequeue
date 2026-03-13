<?php
require 'config/config.php';
$db = Database::getInstance()->getConnection();

try {
    $db->beginTransaction();

    // 1. Ensure Offices exist
    $db->exec("INSERT IGNORE INTO `offices` (`id`, `name`, `code`, `description`) VALUES (1, 'Registrar Office', 'RGSTR', 'Campus Registrar Office')");
    $db->exec("UPDATE `offices` SET `name` = 'Registrar Office', `code` = 'RGSTR' WHERE `id` = 1");
    
    $db->exec("INSERT IGNORE INTO `offices` (`name`, `code`, `description`) VALUES ('Student Affair Service', 'SAS', 'Student Affair Service Office')");
    
    // Get SAS ID
    $stmt = $db->query("SELECT id FROM offices WHERE code = 'SAS'");
    $sas_id = $stmt->fetchColumn();

    // 2. Users (Amins & Staff)
    $password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // 'password'

    // Registrar Admin
    $db->exec("INSERT IGNORE INTO `users` (`email`, `full_name`, `role`, `office_id`, `password`, `is_verified`) VALUES ('registrar_admin@equeue.com', 'Registrar Admin', 'admin', 1, '$password', 1)");

    // SAS Admin
    $db->exec("INSERT IGNORE INTO `users` (`email`, `full_name`, `role`, `office_id`, `password`, `is_verified`) VALUES ('sas_admin@equeue.com', 'SAS Admin', 'admin', $sas_id, '$password', 1)");

    // Registrar Staff (Login: registrar_w01@equeue.com)
    $db->exec("INSERT IGNORE INTO `users` (`email`, `full_name`, `role`, `office_id`, `password`, `is_verified`) VALUES ('registrar_w01@equeue.com', 'Registrar Staff W01', 'staff', 1, '$password', 1)");
    $db->exec("UPDATE `users` SET `email` = 'registrar_w01@equeue.com' WHERE `full_name` = 'Registrar Staff W01'");
    $stmt = $db->query("SELECT id FROM users WHERE email = 'registrar_w01@equeue.com'");
    $reg_staff_id = $stmt->fetchColumn();

    // SAS Staff (Login: sas_w02@equeue.com)
    $db->exec("INSERT IGNORE INTO `users` (`email`, `full_name`, `role`, `office_id`, `password`, `is_verified`) VALUES ('sas_w02@equeue.com', 'SAS Staff W02', 'staff', $sas_id, '$password', 1)");
    $db->exec("UPDATE `users` SET `email` = 'sas_w02@equeue.com' WHERE `full_name` = 'SAS Staff W02'");
    $stmt = $db->query("SELECT id FROM users WHERE email = 'sas_w02@equeue.com'");
    $sas_staff_id = $stmt->fetchColumn();

    // 3. Windows
    // Registrar Window
    $db->exec("INSERT IGNORE INTO `windows` (`window_number`, `window_name`, `office_id`, `staff_id`, `is_active`) VALUES ('REG-W01', 'Registrar Window 01', 1, $reg_staff_id, 1)");
    $db->exec("UPDATE `windows` SET `window_name` = 'Registrar Window 01', `staff_id` = $reg_staff_id WHERE `window_number` = 'REG-W01'");

    // SAS Window
    $db->exec("INSERT IGNORE INTO `windows` (`window_number`, `window_name`, `office_id`, `staff_id`, `is_active`) VALUES ('SAS-W02', 'SAS Window 02', $sas_id, $sas_staff_id, 1)");
    $db->exec("UPDATE `windows` SET `window_name` = 'SAS Window 02', `staff_id` = $sas_staff_id WHERE `window_number` = 'SAS-W02'");

    $db->commit();
    echo "CHANGES APPLIED SUCCESSFULLY";
} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    echo "ERROR: " . $e->getMessage();
}

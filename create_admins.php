<?php
require 'config/config.php';
require 'models/User.php';

$userModel = new User();
$db = Database::getInstance()->getConnection();

// Default password logic mimicking your User model
$password = 'Password123!';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$otpExpiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));

$newAdmins = [
    [
        'email' => 'sas_admin@equeue.com',
        'full_name' => 'SAS Administrator',
        'office_id' => 2 // SAS
    ]
];

foreach ($newAdmins as $admin) {
    // Check if exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$admin['email']]);
    if (!$stmt->fetch()) {
        $otpCode = sprintf("%06d", mt_rand(0, 999999));
        $stmt = $db->prepare("
            INSERT INTO users (email, password, full_name, role, otp_code, otp_expiry, is_verified, office_id) 
            VALUES (?, ?, ?, 'admin', ?, ?, 1, ?)
        ");
        $stmt->execute([
            $admin['email'], 
            $hashedPassword, 
            $admin['full_name'], 
            $otpCode, 
            $otpExpiry, 
            $admin['office_id']
        ]);
        echo "Created: {$admin['email']}\n";
    } else {
        echo "Exists: {$admin['email']}\n";
    }
}

// Ensure the main admin is mapped to Registrar since we removed global access
$db->query("UPDATE users SET office_id = 1 WHERE email = 'admin@equeue.com'");
echo "Mapped admin@equeue.com to Registrar.\n";

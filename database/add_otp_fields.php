<?php
require_once __DIR__ . '/../config/config.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Starting migration: Adding OTP fields to users table...\n";
    
    // Add otp_code column
    $db->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS otp_code VARCHAR(6) DEFAULT NULL AFTER is_verified");
    echo "Added otp_code column.\n";
    
    // Add otp_expiry column
    $db->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS otp_expiry DATETIME DEFAULT NULL AFTER otp_code");
    echo "Added otp_expiry column.\n";
    
    // Add index for otp_code
    $db->exec("ALTER TABLE users ADD INDEX IF NOT EXISTS idx_otp (otp_code)");
    echo "Added index for otp_code.\n";
    
    echo "Migration completed successfully!\n";
    
} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage() . "\n");
}
?>

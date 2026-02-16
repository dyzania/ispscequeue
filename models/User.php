<?php
require_once __DIR__ . '/../config/config.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function register($email, $password, $full_name, $school_id = null, $role = 'user') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $otpCode = sprintf("%06d", mt_rand(0, 999999));
        $otpExpiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        $stmt = $this->db->prepare("
            INSERT INTO users (email, password, full_name, school_id, role, otp_code, otp_expiry, is_verified) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $isVerified = ($role !== 'user') ? 1 : 0; // Staff/Admin verified by default
        
        if ($stmt->execute([$email, $hashedPassword, $full_name, $school_id, $role, $otpCode, $otpExpiry, $isVerified])) {
            return $otpCode;
        }
        return false;
    }
    
    public function login($credential, $password) {
        // Can be email or school_id
        $stmt = $this->db->prepare("
            SELECT id, email, password, full_name, role, school_id, is_verified
            FROM users 
            WHERE email = ? OR school_id = ?
        ");
        
        $stmt->execute([$credential, $credential]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            if ($user['role'] === 'user' && !$user['is_verified']) {
                return ['unverified' => true];
            }
            return $user;
        }
        
        return false;
    }

    public function getByToken($token) {
        $stmt = $this->db->prepare("SELECT id, email, full_name FROM users WHERE verification_token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    public function verifyOTP($email, $code) {
        $stmt = $this->db->prepare("
            SELECT id, full_name, otp_expiry 
            FROM users 
            WHERE email = ? AND otp_code = ?
        ");
        $stmt->execute([$email, $code]);
        $user = $stmt->fetch();
        
        if ($user) {
            if (strtotime($user['otp_expiry']) > time()) {
                // OTP is valid and not expired
                // Clear OTP after successful use
                $update = $this->db->prepare("UPDATE users SET otp_code = NULL, otp_expiry = NULL, is_verified = 1 WHERE id = ?");
                $update->execute([$user['id']]);
                return $user;
            }
        }
        return false;
    }
    
    public function requestPasswordReset($email) {
        $stmt = $this->db->prepare("SELECT id, full_name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            $otpCode = sprintf("%06d", mt_rand(0, 999999));
            $otpExpiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            
            $update = $this->db->prepare("UPDATE users SET otp_code = ?, otp_expiry = ? WHERE id = ?");
            if ($update->execute([$otpCode, $otpExpiry, $user['id']])) {
                return ['code' => $otpCode, 'full_name' => $user['full_name']];
            }
        }
        return false;
    }

    public function resetPassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, $id]);
    }
    
    public function getUserById($id) {
        $stmt = $this->db->prepare("
            SELECT id, email, full_name, role, school_id, created_at 
            FROM users 
            WHERE id = ?
        ");
        
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getAllStaff() {
        $stmt = $this->db->prepare("
            SELECT u.id, u.email, u.full_name, w.window_number, w.window_name, w.is_active
            FROM users u
            LEFT JOIN windows w ON w.staff_id = u.id
            WHERE u.role = 'staff'
            ORDER BY u.full_name
        ");
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getAllUsers() {
        $stmt = $this->db->prepare("
            SELECT id, email, full_name, role, school_id, created_at 
            FROM users 
            WHERE role = 'user'
            ORDER BY created_at DESC
        ");
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function updateUser($id, $email, $full_name) {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET email = ?, full_name = ? 
            WHERE id = ?
        ");
        
        return $stmt->execute([$email, $full_name, $id]);
    }
    
    public function deleteUser($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function emailExists($email, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM users 
                WHERE email = ? AND id != ?
            ");
            $stmt->execute([$email, $excludeId]);
        } else {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM users 
                WHERE email = ?
            ");
            $stmt->execute([$email]);
        }
        
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    public function updateProfile($userId, $fullName, $schoolId = null, $password = null) {
        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("
                UPDATE users 
                SET full_name = ?, school_id = ?, password = ? 
                WHERE id = ?
            ");
            return $stmt->execute([$fullName, $schoolId, $hashedPassword, $userId]);
        } else {
            $stmt = $this->db->prepare("
                UPDATE users 
                SET full_name = ?, school_id = ? 
                WHERE id = ?
            ");
            return $stmt->execute([$fullName, $schoolId, $userId]);
        }
    }
}

<?php
require_once __DIR__ . '/../config/config.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function register($email, $password, $full_name, $school_id = null, $role = 'user', $token = null) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("
            INSERT INTO users (email, password, full_name, school_id, role, verification_token, is_verified) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $isVerified = ($role !== 'user') ? 1 : 0; // Staff/Admin verified by default or differently
        return $stmt->execute([$email, $hashedPassword, $full_name, $school_id, $role, $token, $isVerified]);
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

    public function verifyUser($id) {
        $stmt = $this->db->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?");
        return $stmt->execute([$id]);
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

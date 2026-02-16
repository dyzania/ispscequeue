<?php
require_once __DIR__ . '/../config/config.php';

class Window {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAllWindows() {
        $stmt = $this->db->prepare("
            SELECT w.*, u.full_name as staff_name, u.email as staff_email,
                   (SELECT ticket_number 
                    FROM tickets 
                    WHERE window_id = w.id 
                    AND status IN ('called', 'serving') 
                    AND is_archived = 0
                    ORDER BY called_at DESC LIMIT 1) as serving_ticket
            FROM windows w
            LEFT JOIN users u ON w.staff_id = u.id
            ORDER BY w.window_number
        ");
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getActiveWindows() {
        $stmt = $this->db->prepare("
            SELECT w.*, u.full_name as staff_name,
                   (SELECT ticket_number 
                    FROM tickets 
                    WHERE window_id = w.id 
                    AND status IN ('called', 'serving') 
                    AND is_archived = 0
                    ORDER BY called_at DESC LIMIT 1) as serving_ticket
            FROM windows w
            LEFT JOIN users u ON w.staff_id = u.id
            WHERE w.is_active = 1
            ORDER BY w.window_number
        ");
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getWindowById($id) {
        $stmt = $this->db->prepare("
            SELECT w.*, u.full_name as staff_name, u.email as staff_email
            FROM windows w
            LEFT JOIN users u ON w.staff_id = u.id
            WHERE w.id = ?
        ");
        
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getWindowByStaff($staffId) {
        $stmt = $this->db->prepare("
            SELECT w.*, u.full_name as staff_name
            FROM windows w
            LEFT JOIN users u ON w.staff_id = u.id
            WHERE w.staff_id = ?
        ");
        
        $stmt->execute([$staffId]);
        return $stmt->fetch();
    }
    
    public function createWindow($windowNumber, $windowName, $staffId = null) {
        $stmt = $this->db->prepare("
            INSERT INTO windows (window_number, window_name, staff_id) 
            VALUES (?, ?, ?)
        ");
        
        return $stmt->execute([$windowNumber, $windowName, $staffId]);
    }
    
    public function updateWindow($id, $windowNumber, $windowName, $staffId = null) {
        $stmt = $this->db->prepare("
            UPDATE windows 
            SET window_number = ?, window_name = ?, staff_id = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([$windowNumber, $windowName, $staffId, $id]);
    }
    
    public function toggleWindowStatus($id) {
        $stmt = $this->db->prepare("
            UPDATE windows 
            SET is_active = NOT is_active 
            WHERE id = ?
        ");
        
        return $stmt->execute([$id]);
    }
    
    public function deleteWindow($id) {
        $stmt = $this->db->prepare("DELETE FROM windows WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getWindowServices($windowId) {
        $stmt = $this->db->prepare("
            SELECT s.*, ws.is_enabled
            FROM services s
            LEFT JOIN window_services ws ON s.id = ws.service_id AND ws.window_id = ?
            ORDER BY s.service_name
        ");
        
        $stmt->execute([$windowId]);
        return $stmt->fetchAll();
    }
    
    public function toggleWindowService($windowId, $serviceId) {
        // Check if relationship exists
        $stmt = $this->db->prepare("
            SELECT id, is_enabled 
            FROM window_services 
            WHERE window_id = ? AND service_id = ?
        ");
        
        $stmt->execute([$windowId, $serviceId]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Toggle existing
            $stmt = $this->db->prepare("
                UPDATE window_services 
                SET is_enabled = NOT is_enabled 
                WHERE window_id = ? AND service_id = ?
            ");
            
            return $stmt->execute([$windowId, $serviceId]);
        } else {
            // Create new relationship
            $stmt = $this->db->prepare("
                INSERT INTO window_services (window_id, service_id, is_enabled) 
                VALUES (?, ?, 1)
            ");
            
            return $stmt->execute([$windowId, $serviceId]);
        }
    }
    
    public function getEnabledServices($windowId) {
        $stmt = $this->db->prepare("
            SELECT s.*
            FROM services s
            JOIN window_services ws ON s.id = ws.service_id
            WHERE ws.window_id = ? AND ws.is_enabled = 1
            ORDER BY s.service_name
        ");
        
        $stmt->execute([$windowId]);
        return $stmt->fetchAll();
    }
    public function setWindowStatus($id, $isActive) {
        $stmt = $this->db->prepare("
            UPDATE windows 
            SET is_active = ? 
            WHERE id = ?
        ");
        
        return $stmt->execute([$isActive ? 1 : 0, $id]);
    }

    public function enableAllServices($windowId) {
        // Get all active services
        $stmt = $this->db->query("SELECT id FROM services WHERE is_active = 1");
        $services = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($services as $serviceId) {
            // Check if relationship exists
            $stmt = $this->db->prepare("
                SELECT id 
                FROM window_services 
                WHERE window_id = ? AND service_id = ?
            ");
            $stmt->execute([$windowId, $serviceId]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                // Update existing to enabled
                $stmt = $this->db->prepare("
                    UPDATE window_services 
                    SET is_enabled = 1 
                    WHERE window_id = ? AND service_id = ?
                ");
                $stmt->execute([$windowId, $serviceId]);
            } else {
                // Create new enabled relationship
                $stmt = $this->db->prepare("
                    INSERT INTO window_services (window_id, service_id, is_enabled) 
                    VALUES (?, ?, 1)
                ");
                $stmt->execute([$windowId, $serviceId]);
            }
        }
    }
}

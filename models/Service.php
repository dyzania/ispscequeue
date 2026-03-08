<?php
require_once __DIR__ . '/../config/config.php';

class Service {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAllServices($officeId = null) {
        $officeId = $officeId ?? ($_SESSION['office_id'] ?? 1);
        $stmt = $this->db->prepare("
            SELECT s.*, 
                   (SELECT COUNT(*) 
                    FROM window_services ws 
                    WHERE ws.service_id = s.id AND ws.is_enabled = 1) as staff_enabled_count
            FROM services s 
            WHERE s.is_active = 1 AND s.office_id = ?
            ORDER BY s.service_name
        ");
        
        $stmt->execute([$officeId]);
        return $stmt->fetchAll();
    }
    
    public function getAllServicesAdmin($officeId = null) {
        $officeId = $officeId ?? ($_SESSION['office_id'] ?? 1);
        $stmt = $this->db->prepare("
            SELECT * 
            FROM services 
            WHERE office_id = ?
            ORDER BY service_name
        ");
        
        $stmt->execute([$officeId]);
        return $stmt->fetchAll();
    }
    
    public function getServiceById($id) {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM services 
            WHERE id = ?
        ");
        
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function createService($serviceName, $serviceCode, $description, $requirements, $staffNotes = null, $targetTime = 10, $officeId = 1) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO services (service_name, service_code, description, requirements, staff_notes, target_time, office_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $success = $stmt->execute([$serviceName, $serviceCode, $description, $requirements, $staffNotes, $targetTime, $officeId]);
            
            if ($success) {
                return ['success' => true, 'message' => 'Service created successfully.'];
            }
            return ['success' => false, 'message' => 'Failed to execute service creation.'];
            
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return ['success' => false, 'message' => "The service code '$serviceCode' is already in use. Please use a unique code."];
            }
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function updateService($id, $serviceName, $serviceCode, $description, $requirements, $staffNotes = null, $targetTime = 10) {
        $stmt = $this->db->prepare("
            UPDATE services 
            SET service_name = ?, service_code = ?, description = ?, requirements = ?, staff_notes = ?, target_time = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([$serviceName, $serviceCode, $description, $requirements, $staffNotes, $targetTime, $id]);
    }
    
    public function toggleServiceStatus($id) {
        $stmt = $this->db->prepare("
            UPDATE services 
            SET is_active = NOT is_active 
            WHERE id = ?
        ");
        
        return $stmt->execute([$id]);
    }
    
    public function deleteService($id) {
        $stmt = $this->db->prepare("DELETE FROM services WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

<?php
require_once __DIR__ . '/../config/config.php';

class Service {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAllServices() {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM services 
            WHERE is_active = 1
            ORDER BY service_name
        ");
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getAllServicesAdmin() {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM services 
            ORDER BY service_name
        ");
        
        $stmt->execute();
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
    
    public function createService($serviceName, $serviceCode, $description, $requirements, $estimatedTime, $staffNotes = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO services (service_name, service_code, description, requirements, estimated_time, staff_notes) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $success = $stmt->execute([$serviceName, $serviceCode, $description, $requirements, $estimatedTime, $staffNotes]);
            
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
    
    public function updateService($id, $serviceName, $serviceCode, $description, $requirements, $estimatedTime, $staffNotes = null) {
        $stmt = $this->db->prepare("
            UPDATE services 
            SET service_name = ?, service_code = ?, description = ?, requirements = ?, estimated_time = ?, staff_notes = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([$serviceName, $serviceCode, $description, $requirements, $estimatedTime, $staffNotes, $id]);
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

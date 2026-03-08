<?php
require_once __DIR__ . '/../config/config.php';

class Office {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAllOffices($includeInactive = false) {
        $query = "SELECT * FROM offices";
        if (!$includeInactive) {
            $query .= " WHERE is_active = 1";
        }
        $query .= " ORDER BY name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getOfficeById($id) {
        $stmt = $this->db->prepare("
            SELECT * FROM offices 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getOfficeByCode($code) {
        $stmt = $this->db->prepare("
            SELECT * FROM offices 
            WHERE code = ?
        ");
        $stmt->execute([$code]);
        return $stmt->fetch();
    }

    public function createOffice($data) {
        $stmt = $this->db->prepare("
            INSERT INTO offices (name, code, description, is_active) 
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['name'],
            strtoupper($data['code']), // ensure code is uppercase
            $data['description'] ?? null,
            1 // default to active
        ]);
    }

    public function updateOffice($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE offices 
            SET name = ?, code = ?, description = ? 
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['name'],
            strtoupper($data['code']),
            $data['description'] ?? null,
            $id
        ]);
    }

    public function toggleActiveStatus($id, $status) {
        $stmt = $this->db->prepare("
            UPDATE offices 
            SET is_active = ? 
            WHERE id = ?
        ");
        return $stmt->execute([$status, $id]);
    }
}

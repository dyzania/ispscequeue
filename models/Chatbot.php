<?php
require_once __DIR__ . '/../config/config.php';

class Chatbot {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAllData() {
        $stmt = $this->db->prepare("SELECT * FROM chatbot_data ORDER BY category, id");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function addData($category, $question, $answer, $keywords) {
        $stmt = $this->db->prepare("
            INSERT INTO chatbot_data (category, question, answer, keywords) 
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$category, $question, $answer, $keywords]);
    }
    
    public function updateData($id, $category, $question, $answer, $keywords) {
        $stmt = $this->db->prepare("
            UPDATE chatbot_data 
            SET category = ?, question = ?, answer = ?, keywords = ? 
            WHERE id = ?
        ");
        return $stmt->execute([$category, $question, $answer, $keywords, $id]);
    }
    
    public function deleteData($id) {
        $stmt = $this->db->prepare("DELETE FROM chatbot_data WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getResponse($query) {
        // Simple keyword matching for now
        $keywords = explode(' ', strtolower($query));
        $placeholders = str_repeat('?,', count($keywords) - 1) . '?';
        
        // This is a very basic implementation. 
        // Ideally we would use Full Text Search or similar, but LIKE %...% is simpler to setup for now
        // Or just match against the keywords column
        
        $sql = "SELECT * FROM chatbot_data WHERE is_active = 1 AND (";
        $params = [];
        $conditions = [];
        foreach($keywords as $word) {
            if(strlen($word) < 3) continue; // Skip short words
            $conditions[] = "keywords LIKE ?";
            $params[] = "%$word%";
        }
        
        if(empty($conditions)) return null;
        
        $sql .= implode(' OR ', $conditions) . ") ORDER BY usage_count DESC LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        if($result) {
            // Increment usage
            $update = $this->db->prepare("UPDATE chatbot_data SET usage_count = usage_count + 1 WHERE id = ?");
            $update->execute([$result['id']]);
        }
        
        return $result;
    }
}

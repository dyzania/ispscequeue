<?php
require_once __DIR__ . '/../config/config.php';

class Chatbot {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    //gets the AI knowledge base context..
    public function getContext() {
        $stmt = $this->db->query("SELECT content FROM ai_context WHERE id = 1");
        return $stmt->fetchColumn();
    }


    /** Update the AI knowledge base context.
     * @param string $content The new context content (HTML from editor) */
    public function updateContext($content) {
        $stmt = $this->db->prepare("UPDATE ai_context SET content = ?, updated_at = NOW() WHERE id = 1");
        return $stmt->execute([$content]);
    }
}

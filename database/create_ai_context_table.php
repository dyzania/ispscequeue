<?php
require_once __DIR__ . '/../config/config.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Create ai_context table
    $sql = "CREATE TABLE IF NOT EXISTS ai_context (
        id INT PRIMARY KEY AUTO_INCREMENT,
        content LONGTEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $db->exec($sql);
    echo "Table 'ai_context' created successfully.\n";
    
    // Check if default row exists
    $stmt = $db->query("SELECT COUNT(*) FROM ai_context WHERE id = 1");
    if ($stmt->fetchColumn() == 0) {
        $default_content = '<h2>Organization Overview</h2><p>Enter your organization details here...</p>';
        $stmt = $db->prepare("INSERT INTO ai_context (id, content) VALUES (1, ?)");
        $stmt->execute([$default_content]);
        echo "Default context inserted.\n";
    }
    
    echo "Database migration completed successfully.";
    
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

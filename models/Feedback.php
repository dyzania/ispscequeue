<?php
require_once __DIR__ . '/../config/config.php';

class Feedback {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function createFeedback($ticketId, $userId, $windowId, $comment) {
        // Perform sentiment analysis on comment
        $sentimentData = $this->analyzeSentiment($comment);
        
        $stmt = $this->db->prepare("
            INSERT INTO feedback (ticket_id, user_id, window_id, rating, comment, sentiment, sentiment_score) 
            VALUES (?, ?, ?, NULL, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $ticketId, 
            $userId, 
            $windowId, 
            $comment, 
            $sentimentData['sentiment'],
            $sentimentData['score']
        ]);
    }
    
    private function analyzeSentiment($comment) {
        // Call the Python sentiment analysis API
        $data = json_encode(["text" => $comment]);
        
        // -------------------------------------------------------------------------
        // PRODUCTION HOSTING:
        // If your Python server is on a different machine, update this URL to:
        // "https://your-python-api.com/analyze" or "http://your-server-ip:8000/analyze"
        // -------------------------------------------------------------------------
        $ch = curl_init("http://127.0.0.1:8000/analyze");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 5 second timeout
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Default values if API fails
        $sentiment = 'neutral';
        $score = 0;
        
        if ($response !== false && $httpCode === 200) {
            $result = json_decode($response, true);
            if ($result && isset($result["label"]) && isset($result["score"])) {
                // Map the label to our database enum format
                $labelMap = [
                    "Very Negative" => "very_negative",
                    "Negative" => "negative",
                    "Neutral" => "neutral",
                    "Positive" => "positive",
                    "Very Positive" => "very_positive"
                ];
                
                if (isset($labelMap[$result["label"]])) {
                    $sentiment = $labelMap[$result["label"]];
                }
                
                // Convert score to -1 to 1 range based on sentiment
                $scoreMap = [
                    "very_negative" => -0.8,
                    "negative" => -0.4,
                    "neutral" => 0,
                    "positive" => 0.4,
                    "very_positive" => 0.8
                ];
                
                $score = $scoreMap[$sentiment] ?? 0;
            }
        }
        
        return [
            'sentiment' => $sentiment,
            'score' => $score
        ];
    }
    
    public function getFeedbackByTicket($ticketId) {
        $stmt = $this->db->prepare("
            SELECT f.*, u.full_name as user_name, w.window_number
            FROM feedback f
            JOIN users u ON f.user_id = u.id
            LEFT JOIN windows w ON f.window_id = w.id
            WHERE f.ticket_id = ?
        ");
        
        $stmt->execute([$ticketId]);
        return $stmt->fetch();
    }
    
    public function getAllFeedback() {
        $stmt = $this->db->prepare("
            SELECT f.*, u.full_name as user_name, w.window_number, t.ticket_number, s.service_name
            FROM feedback f
            JOIN users u ON f.user_id = u.id
            JOIN tickets t ON f.ticket_id = t.id
            JOIN services s ON t.service_id = s.id
            LEFT JOIN windows w ON f.window_id = w.id
            ORDER BY f.created_at DESC
        ");
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getFeedbackStats() {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_feedback,
                COUNT(CASE WHEN sentiment = 'very_positive' THEN 1 END) as very_positive,
                COUNT(CASE WHEN sentiment = 'positive' THEN 1 END) as positive,
                COUNT(CASE WHEN sentiment = 'neutral' THEN 1 END) as neutral,
                COUNT(CASE WHEN sentiment = 'negative' THEN 1 END) as negative,
                COUNT(CASE WHEN sentiment = 'very_negative' THEN 1 END) as very_negative,
                AVG(sentiment_score) as avg_sentiment_score
            FROM feedback
        ");
        
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function getFeedbackByWindow($windowId) {
        $stmt = $this->db->prepare("
            SELECT f.*, u.full_name as user_name, t.ticket_number, s.service_name
            FROM feedback f
            JOIN users u ON f.user_id = u.id
            JOIN tickets t ON f.ticket_id = t.id
            JOIN services s ON t.service_id = s.id
            WHERE f.window_id = ?
            ORDER BY f.created_at DESC
        ");
        
        $stmt->execute([$windowId]);
        return $stmt->fetchAll();
    }
    
    public function getFeedbackTrends($days = 7) {
        $stmt = $this->db->prepare("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as count,
                AVG(sentiment_score) as avg_sentiment
            FROM feedback
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }
}

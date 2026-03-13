<?php
require_once __DIR__ . '/../config/config.php';

class Feedback {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function createFeedback($ticketId, $userId, $windowId, $comment, $csatData = []) {
        // Perform sentiment analysis on comment
        $sentimentData = $this->analyzeSentiment($comment);
        
        $stmt = $this->db->prepare("
            INSERT INTO feedback (
                ticket_id, user_id, window_id, rating, comment, sentiment, sentiment_score,
                client_type, client_type_others, contact_means, contact_means_others,
                cc_awareness, cc_visibility, cc_helpfulness,
                rating_responsiveness_1, rating_responsiveness_2, rating_reliability,
                rating_access, rating_communication, rating_costs, rating_integrity,
                rating_courtesy, rating_outcome
            ) 
            VALUES (?, ?, ?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        try {
            return $stmt->execute([
                $ticketId, 
                $userId, 
                $windowId, 
                $comment, 
                $sentimentData['sentiment'],
                $sentimentData['score'],
                $csatData['client_type'] ?? null,
                $csatData['client_type_others'] ?? null,
                $csatData['contact_means'] ?? null,
                $csatData['contact_means_others'] ?? null,
                $csatData['cc_awareness'] ?? null,
                $csatData['cc_visibility'] ?? null,
                $csatData['cc_helpfulness'] ?? null,
                $csatData['rating_responsiveness_1'] ?? null,
                $csatData['rating_responsiveness_2'] ?? null,
                $csatData['rating_reliability'] ?? null,
                $csatData['rating_access'] ?? null,
                $csatData['rating_communication'] ?? null,
                $csatData['rating_costs'] ?? null,
                $csatData['rating_integrity'] ?? null,
                $csatData['rating_courtesy'] ?? null,
                $csatData['rating_outcome'] ?? null
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), '1062')) {
                // Feedback already submitted for this ticket
                error_log('Feedback::createFeedback() duplicate entry for ticket_id=' . $ticketId);
                return ['duplicate' => true, 'message' => 'Feedback has already been submitted for this ticket.'];
            }
            error_log('Feedback::createFeedback() error: ' . $e->getMessage());
            return false;
        }
    }
    
    private function analyzeSentiment($comment) {
        // Call the Python sentiment analysis API
        $data = json_encode(["text" => $comment]);
        
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
                COUNT(f.id) as total_feedback,
                COUNT(CASE WHEN f.sentiment = 'very_positive' THEN 1 END) as very_positive,
                COUNT(CASE WHEN f.sentiment = 'positive' THEN 1 END) as positive,
                COUNT(CASE WHEN f.sentiment = 'neutral' THEN 1 END) as neutral,
                COUNT(CASE WHEN f.sentiment = 'negative' THEN 1 END) as negative,
                COUNT(CASE WHEN f.sentiment = 'very_negative' THEN 1 END) as very_negative,
                AVG(f.sentiment_score) as avg_sentiment_score
            FROM feedback f
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
                COUNT(id) as count,
                AVG(sentiment_score) as avg_sentiment
            FROM feedback
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY date
            ORDER BY date ASC
        ");
        
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }
}

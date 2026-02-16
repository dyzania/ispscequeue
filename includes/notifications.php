<?php
require_once __DIR__ . '/../config/config.php';

function sendNotification($userId, $ticketId, $type, $message, $staffNotes = null) {
    $db = Database::getInstance()->getConnection();
    
    // 1. Save to Database (Web Push Polling)
    $stmt = $db->prepare("
        INSERT INTO notifications (user_id, ticket_id, type, message) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$userId, $ticketId, $type, $message]);
    
    // 2. Get User & Ticket Details for Email
    // We join with tickets to get the ticket number, window info, and timestamps
    $stmt = $db->prepare("
        SELECT u.email, u.full_name, t.ticket_number, t.created_at, t.completed_at, w.window_name
        FROM users u
        LEFT JOIN tickets t ON t.id = ?
        LEFT JOIN windows w ON t.window_id = w.id
        WHERE u.id = ?
    ");
    $stmt->execute([$ticketId, $userId]);
    $data = $stmt->fetch();
    
    if ($data && !empty($data['email'])) {
        try {
            require_once __DIR__ . '/../models/MailService.php';
            $mailService = new MailService();
            
            if ($type === 'turn_next') {
                $mailService->sendTicketCalled(
                    $data['email'], 
                    $data['full_name'], 
                    $data['ticket_number'], 
                    $data['window_name'] ?? 'Assigned Counter'
                );
            } elseif ($type === 'completed') {
                // Calculate Wait Time
                $created = new DateTime($data['created_at']);
                $completed = $data['completed_at'] ? new DateTime($data['completed_at']) : new DateTime();
                $interval = $created->diff($completed);
                
                $parts = [];
                if ($interval->h > 0) $parts[] = $interval->h . ' hr' . ($interval->h > 1 ? 's' : '');
                if ($interval->i > 0) $parts[] = $interval->i . ' min' . ($interval->i > 1 ? 's' : '');
                // If less than a minute, show seconds
                if (empty($parts)) $parts[] = $interval->s . ' sec' . ($interval->s > 1 ? 's' : '');
                
                $waitTime = implode(' ', $parts);

                $mailService->sendTicketCompleted(
                    $data['email'], 
                    $data['full_name'], 
                    $data['ticket_number'],
                    $staffNotes,
                    $waitTime
                );
            } elseif ($type === 'cancelled') {
                // You might want a specific method in MailService for this, 
                // but let's just use a basic one or add it later if needed.
                // For now, at least it's saved to the database (Web Push).
            }
        } catch (Exception $e) {
            error_log("Notification System Email Error: " . $e->getMessage());
        }
    }
}

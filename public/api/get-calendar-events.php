<?php
require_once __DIR__ . '/../../config/config.php';
header('Content-Type: application/json');

try {
    $db = getDatabaseConnection();
    
    // Fetch count of tickets per day for the last 30 days
    $stmt = $db->query("
        SELECT DATE(created_at) as date, COUNT(*) as count 
        FROM tickets 
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ");
    
    $events = [];
    while ($row = $stmt->fetch()) {
        $events[] = [
            'title' => $row['count'] . ' Tickets',
            'start' => $row['date'],
            'allDay' => true,
            'extendedProps' => [
                'count' => $row['count']
            ]
        ];
    }
    
    echo json_encode($events);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>

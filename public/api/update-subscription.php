<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get the subscription status from POST
$data = json_decode(file_get_contents('php://input'), true);
$subscribed = isset($data['subscribed']) ? (int)$data['subscribed'] : 0;
$userId = getUserId();

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("UPDATE users SET announcement_subscription = ? WHERE id = ?");
    $result = $stmt->execute([$subscribed, $userId]);

    if ($result) {
        echo json_encode([
            'success' => true, 
            'message' => $subscribed ? 'You will now receive announcement notifications.' : 'You have unsubscribed from announcement notifications.',
            'status' => $subscribed
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update subscription.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal Server Error: ' . $e->getMessage()]);
}

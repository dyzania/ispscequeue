<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Ticket.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
}

// Security Checks
apiRequireRole('user');
checkRateLimit('user_cancel_ticket', 5, 300);

$data = json_decode(file_get_contents('php://input'), true);

// CSRF Check
$headers = getallheaders();
$csrfToken = $headers['X-CSRF-Token'] ?? ($data['csrf_token'] ?? '');
verifyCsrfToken($csrfToken);

if (!isset($data['ticket_id'])) {
    jsonResponse(['success' => false, 'message' => 'Missing ticket_id'], 400);
}

$ticketModel = new Ticket();
$ticket = $ticketModel->getTicketById($data['ticket_id']);

// Verify ownership
if (!$ticket || $ticket['user_id'] != getUserId()) {
    jsonResponse(['success' => false, 'message' => 'Unauthorized'], 403);
}

$success = $ticketModel->cancelTicket($data['ticket_id']);

if ($success) {
    jsonResponse(['success' => true]);
} else {
    jsonResponse(['success' => false, 'message' => 'Database error'], 500);
}

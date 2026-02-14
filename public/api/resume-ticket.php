<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Ticket.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
}

// Security Checks
apiRequireRole('staff');
checkRateLimit('resume_ticket', 20, 60);

$data = json_decode(file_get_contents('php://input'), true);

// CSRF Check
$headers = getallheaders();
$csrfToken = $headers['X-CSRF-Token'] ?? ($data['csrf_token'] ?? '');
verifyCsrfToken($csrfToken);

if (!isset($data['ticket_id'])) {
    jsonResponse(['success' => false, 'message' => 'Missing ticket_id'], 400);
}

$ticketModel = new Ticket();
$success = $ticketModel->resumeTicket($data['ticket_id']);

if ($success) {
    jsonResponse(['success' => true]);
} else {
    jsonResponse(['success' => false, 'message' => 'Database error'], 500);
}

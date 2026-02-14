<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Ticket.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
}

// Security Checks
apiRequireRole('staff');
checkRateLimit('call_ticket', 20, 60);

$data = json_decode(file_get_contents('php://input'), true);

// CSRF Check
$headers = getallheaders();
$csrfToken = $headers['X-CSRF-Token'] ?? ($data['csrf_token'] ?? '');
verifyCsrfToken($csrfToken);

if (!isset($data['window_id'])) {
    jsonResponse(['success' => false, 'message' => 'Missing window_id'], 400);
}

$ticketModel = new Ticket();
$result = $ticketModel->callNextTicket($data['window_id']);

jsonResponse($result);

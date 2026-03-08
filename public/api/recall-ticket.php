<?php
require_once __DIR__ . '/../../models/Ticket.php';
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

// Session check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$ticketModel = new Ticket();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Check
    $headers = getallheaders();
    $csrfHeader = $headers['X-CSRF-Token'] ?? $headers['x-csrf-token'] ?? '';
    
    // Also check body for CSRF
    $input = json_decode(file_get_contents('php://input'), true);
    $csrfBody = $input['csrf_token'] ?? '';
    
    if (($csrfHeader !== ($_SESSION['csrf_token'] ?? '')) && ($csrfBody !== ($_SESSION['csrf_token'] ?? ''))) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }

    $ticketId = $input['ticket_id'] ?? null;

    if ($ticketId) {
        $success = $ticketModel->recallTicket($ticketId);
        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to recall ticket or ticket not in called status']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing ticket ID']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

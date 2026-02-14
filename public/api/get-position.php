<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Ticket.php';

header('Content-Type: application/json');

$ticketId = isset($_GET['ticket_id']) ? intval($_GET['ticket_id']) : 0;

if (!$ticketId) {
    echo json_encode(['success' => false, 'message' => 'Invalid ticket ID']);
    exit;
}

$ticketModel = new Ticket();
$position = $ticketModel->getQueuePosition($ticketId);

echo json_encode([
    'success' => true,
    'position' => $position
]);

<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Ticket.php';

header('Content-Type: application/json');

$ticketModel = new Ticket();
$queue = $ticketModel->getWaitingQueue();

echo json_encode([
    'success' => true,
    'queue' => $queue
]);

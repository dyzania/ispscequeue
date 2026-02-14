<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Ticket.php';
require_once __DIR__ . '/../../models/Window.php';
require_once __DIR__ . '/../../models/Feedback.php';

// Ensure user is admin (optional for testing, but good practice)
// if (getUserRole() !== 'admin') {
//     jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
// }

$ticketModel = new Ticket();
$windowModel = new Window();

$stats = $ticketModel->getQueueStats();
$activeWindows = $windowModel->getActiveWindows();
$recentTickets = $ticketModel->getRecentTickets(10);

jsonResponse([
    'success' => true,
    'stats' => $stats,
    'activeWindows' => $activeWindows,
    'recentTickets' => $recentTickets
]);

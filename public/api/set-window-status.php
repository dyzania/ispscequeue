<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Window.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
}

// Security Checks
apiRequireRole('staff');
checkRateLimit('set_window_status', 10, 60);

$data = json_decode(file_get_contents('php://input'), true);

// CSRF Check
$headers = getallheaders();
$csrfToken = $headers['X-CSRF-Token'] ?? ($data['csrf_token'] ?? '');
verifyCsrfToken($csrfToken);

if (!isset($data['window_id']) || !isset($data['is_active'])) {
    jsonResponse(['success' => false, 'message' => 'Missing window_id or is_active'], 400);
}

$windowModel = new Window();
$success = $windowModel->setWindowStatus($data['window_id'], $data['is_active']);

if ($success) {
    jsonResponse(['success' => true]);
} else {
    jsonResponse(['success' => false, 'message' => 'Database error'], 500);
}

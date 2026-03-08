<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Window.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Custom check for CSRF to ensure JSON response
$headers = getallheaders();
$csrfToken = $headers['X-CSRF-Token'] ?? ($data['csrf_token'] ?? '');

if (!isset($_SESSION['csrf_token']) || empty($csrfToken) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
    echo json_encode(['success' => false, 'message' => 'Invalid security token. Please refresh.']);
    exit;
}

$windowId = $data['window_id'] ?? null;
$colleges = $data['colleges'] ?? [];

if (!$windowId) {
    echo json_encode(['success' => false, 'message' => 'Window ID is required']);
    exit;
}

$windowModel = new Window();
// Verify ownership
$window = $windowModel->getWindowById($windowId);
if (!$window || $window['staff_id'] != getUserId()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access to window']);
    exit;
}

if ($windowModel->updatePreferredColleges($windowId, $colleges)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update preferences']);
}

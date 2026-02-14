<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Window.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
}

// Security Checks
apiRequireRole('staff');
checkRateLimit('toggle_service', 20, 60);

$data = json_decode(file_get_contents('php://input'), true);

// CSRF Check
$headers = getallheaders();
$csrfToken = $headers['X-CSRF-Token'] ?? ($data['csrf_token'] ?? '');
verifyCsrfToken($csrfToken);

if (!isset($data['window_id']) || !isset($data['service_id'])) {
    jsonResponse(['success' => false, 'message' => 'Missing parameters'], 400);
}

$windowModel = new Window();
$success = $windowModel->toggleWindowService($data['window_id'], $data['service_id']);

if ($success) {
    // Get new status to return
    // This is a bit inefficient (querying again) but safe
    // Since we don't return the new state from toggle method, let's just check
    $services = $windowModel->getWindowServices($data['window_id']);
    $newState = false;
    foreach($services as $svc) {
        if($svc['id'] == $data['service_id']) {
            $newState = $svc['is_enabled'];
            break;
        }
    }
    
    jsonResponse([
        'success' => true,
        'is_enabled' => $newState
    ]);
} else {
    jsonResponse(['success' => false, 'message' => 'Database error'], 500);
}

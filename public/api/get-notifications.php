<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

// Ensure user is logged in
$userId = getUserId();
if (!$userId) {
    jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
}

$db = Database::getInstance()->getConnection();

// Fetch unread notifications
$stmt = $db->prepare("
    SELECT id, type, message, ticket_id, created_at
    FROM notifications
    WHERE user_id = ? AND is_read = 0
    ORDER BY created_at ASC
");
$stmt->execute([$userId]);
$notifications = $stmt->fetchAll();

if (!empty($notifications)) {
    // Mark as read immediately to avoid double-firing
    $ids = array_column($notifications, 'id');
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $updateStmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id IN ($placeholders)");
    $updateStmt->execute($ids);
}

jsonResponse([
    'success' => true,
    'notifications' => $notifications
]);

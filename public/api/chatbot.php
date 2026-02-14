<?php
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$user_message = $data['message'] ?? '';
$session_id = $data['session_id'] ?? uniqid();

if (empty($user_message)) {
    echo json_encode(['error' => 'Message is required']);
    exit();
}

// Get admin data context
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT content FROM chatbot_data WHERE id = 1");
$chatbot_data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$chatbot_data) {
    echo json_encode(['error' => 'No admin data found. Please configure admin data first.']);
    exit();
}

$chatbot_content = $chatbot_data['content'];

// Construct prompt
$prompt = "Be precise, simple, provide a direct and complete answer, avoiding vague, generic, or overly broad explanations.
Remove any unnecessary characters. If the question cannot be answered using the following context, respond only with: 'I do not have the information needed to answer this question. Please email this inquiry to admin@registrar.com or ask at the Registrar.' 

Context: {$chatbot_content}

User Question: {$user_message}

Answer:";

// Prepare request to OpenRouter
$messages = [
    [
        'role' => 'user',
        'content' => $prompt
    ]
];

$request_data = [
    'model' => AI_MODEL,
    'messages' => $messages
];

// API call to OpenRouter
$ch = curl_init(OPENROUTER_API_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . OPENROUTER_API_KEY,
    'HTTP-Referer: ' . BASE_URL,
    'X-Title: E-Queue Chatbot'
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    // Log error for debugging
    error_log("OpenRouter API Error: " . $response);
    echo json_encode(['error' => 'API request failed', 'details' => 'Service temporarily unavailable']);
    exit();
}

$api_response = json_decode($response, true);
$bot_message = $api_response['choices'][0]['message']['content'] ?? 'Sorry, I could not generate a response.';

echo json_encode([
    'success' => true,
    'response' => $bot_message,
    'session_id' => $session_id
]);

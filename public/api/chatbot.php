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
require_once __DIR__ . '/../../models/Chatbot.php';
require_once __DIR__ . '/../../models/Ticket.php';
$chatbot = new Chatbot();
$chatbot_content = $chatbot->getContext();

if (!$chatbot_content) {
    echo json_encode(['error' => 'No knowledge base found. Please configure AI context in admin settings first.']);
    exit();
}

// Fetch active ticket data for the user
$ticketModel = new Ticket();
$currentTicket = null;
$ticketContext = "";

if (isset($_SESSION['user_id'])) {
    $currentTicket = $ticketModel->getCurrentTicket($_SESSION['user_id']);
    
    if ($currentTicket) {
        $now = time();
        $ticketContext .= "\n[User's Live Ticket Data]\n";
        $ticketContext .= "- Ticket Number: " . $currentTicket['ticket_number'] . "\n";
        $ticketContext .= "- Service: " . $currentTicket['service_name'] . "\n";
        $ticketContext .= "- Office: " . $currentTicket['office_name'] . "\n";
        $ticketContext .= "- Current Status: " . strtoupper($currentTicket['status']) . "\n";
        
        if ($currentTicket['status'] === 'waiting') {
            $position = $ticketModel->getGlobalQueuePosition($currentTicket['id']);
            $ticketsAhead = $ticketModel->getTicketsAhead($currentTicket['id']);
            $estWaitSeconds = $ticketModel->getAdvancedEstimatedWaitTime($currentTicket['id'], $now);
            
            $h = floor($estWaitSeconds / 3600);
            $m = floor(($estWaitSeconds % 3600) / 60);
            $waitString = ($h > 0 ? "{$h}h " : "") . "{$m}m";
            if ($estWaitSeconds < 60) $waitString = "less than 1m";
            
            $ticketContext .= "- Queue Position: #" . $position . " (" . $ticketsAhead . " tickets ahead)\n";
            $ticketContext .= "- Estimated Wait Time: " . $waitString . "\n";
        } elseif ($currentTicket['status'] === 'called' || $currentTicket['status'] === 'serving') {
            $ticketContext .= "- Assigned Window: " . $currentTicket['window_name'] . " (" . $currentTicket['window_number'] . ")\n";
            if ($currentTicket['status'] === 'serving') {
                 $aptSeconds = $ticketModel->getPreciseAverageProcessTime($currentTicket['service_id']);
                 if ($aptSeconds && $currentTicket['served_at']) {
                     $elapsed = $now - strtotime($currentTicket['served_at']);
                     $remaining = max(0, $aptSeconds - $elapsed);
                     if ($remaining > 0) {
                        $m = floor($remaining / 60);
                        $s = $remaining % 60;
                        $ticketContext .= "- Estimated Remaining Process Time: " . ($m > 0 ? "{$m}m " : "") . "{$s}s\n";
                     } else {
                         $ticketContext .= "- Estimated Remaining Process Time: Finishing soon\n";
                     }
                 }
            }
        }
    } else {
        $ticketContext .= "\n[User's Live Ticket Data]\n- The user currently does NOT have an active ticket in the queue.\n";
    }
}

// Construct prompt
$prompt = "Be precise, simple, provide a direct and complete answer, avoiding vague, generic, or overly broad explanations.
Remove any unnecessary characters. If the question cannot be answered using the following context, 
respond only with: 'I do not have the information needed to answer this question. Please ask at the counter.' 

Context: {$chatbot_content}
{$ticketContext}

Client Question: {$user_message}";

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

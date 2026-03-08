<?php
require_once __DIR__ . '/models/Ticket.php';

// Mock session for office_id
session_start();
$_SESSION['office_id'] = 1;

try {
    $ticketModel = new Ticket();
    
    // We need a valid service_id. Looking at earlier research, COG might be available.
    // Let's find a service_id first.
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT id, service_code FROM services LIMIT 1");
    $service = $stmt->fetch();
    
    if (!$service) {
        die("No services found to test with.\n");
    }

    $serviceId = $service['id'];
    $serviceCode = $service['service_code'];
    $day = date('j');

    // Reflection to test private method
    $reflection = new ReflectionClass('Ticket');
    $method = $reflection->getMethod('generateTicketNumber');
    $method->setAccessible(true);
    
    $ticketNumber = $method->invokeArgs($ticketModel, [$serviceId]);
    
    echo "Generated Ticket Number: $ticketNumber\n";
    
    // Expected pattern: ServiceCode + Day + "-" + 3 digits
    $expectedPattern = "/^" . preg_quote($serviceCode) . $day . "-\d{3}$/";
    
    if (preg_match($expectedPattern, $ticketNumber)) {
        echo "VERIFICATION SUCCESS: Ticket number matches the pattern '$serviceCode$day-XXX'.\n";
    } else {
        echo "VERIFICATION FAILED: Ticket number '$ticketNumber' does not match pattern '$serviceCode$day-XXX'.\n";
    }

} catch (Exception $e) {
    die("Verification failed: " . $e->getMessage() . "\n");
}

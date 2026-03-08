<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Ticket.php';
require_once __DIR__ . '/models/Window.php';

// Mock session
session_start();
$_SESSION['office_id'] = 1;

try {
    $db = Database::getInstance()->getConnection();
    
    // Ensure we have a service and office
    $stmt = $db->query("SELECT id FROM offices LIMIT 1");
    $office = $stmt->fetch();
    if (!$office) {
        $db->exec("INSERT INTO offices (name, code) VALUES ('Test Office', 'TEST')");
        $officeId = $db->lastInsertId();
    } else {
        $officeId = $office['id'];
    }
    $_SESSION['office_id'] = $officeId;

    $stmt = $db->prepare("SELECT id FROM services WHERE office_id = ? LIMIT 1");
    $stmt->execute([$officeId]);
    $service = $stmt->fetch();
    if (!$service) {
        $db->exec("INSERT INTO services (service_name, service_code, office_id, is_active) VALUES ('Test Service', 'TEST', $officeId, 1)");
        $serviceId = $db->lastInsertId();
    } else {
        $serviceId = $service['id'];
    }

    // 1. Test Registration
    $userModel = new User();
    $email1 = "test_cas_" . uniqid() . "@example.com";
    $email2 = "test_scje_" . uniqid() . "@example.com";
    $password = "Password123!";
    
    $userModel->register($email1, $password, "CAS User", "ID-CAS-" . mt_rand(1000, 9999), 'user', $officeId, "CAS");
    $userModel->register($email2, $password, "SCJE User", "ID-SCJE-" . mt_rand(1000, 9999), 'user', $officeId, "SCJE");

    $stmt = $db->prepare("SELECT id, college FROM users WHERE email = ?");
    $stmt->execute([$email1]);
    $user1 = $stmt->fetch();
    $stmt->execute([$email2]);
    $user2 = $stmt->fetch();

    if ($user1['college'] === 'CAS' && $user2['college'] === 'SCJE') {
        echo "Registration Verification Success: Colleges saved.\n";
    } else {
        die("Registration Verification Failed: " . print_r([$user1, $user2], true));
    }

    // 2. Test Filtering
    $ticketModel = new Ticket();
    $windowModel = new Window();

    // Mock window ID
    $stmt = $db->prepare("SELECT id FROM windows WHERE office_id = ? LIMIT 1");
    $stmt->execute([$officeId]);
    $window = $stmt->fetch();
    if (!$window) {
        $db->exec("INSERT INTO windows (window_number, window_name, office_id, is_active) VALUES (99, 'Test Window', $officeId, 1)");
        $windowId = $db->lastInsertId();
    } else {
        $windowId = $window['id'];
    }

    // ENABLE SERVICE FOR WINDOW
    $windowModel->enableAllServices($windowId);

    // Create tickets using model
    $ticketModel->createTicket($user1['id'], $serviceId, "CAS Note");
    $ticketModel->createTicket($user2['id'], $serviceId, "SCJE Note");

    // TEST 1: No filter
    $windowModel->updatePreferredColleges($windowId, []);
    $queueAll = $ticketModel->getWaitingQueue(null, $windowId);
    $countAll = count($queueAll);
    echo "Queue count with no filter: $countAll\n";
    if ($countAll < 2) {
        echo "WARNING: Queue count lower than expected. Possible other active tickets.\n";
    }

    // TEST 2: Filter by CAS
    $windowModel->updatePreferredColleges($windowId, ['CAS']);
    $queueCAS = $ticketModel->getWaitingQueue(null, $windowId);
    
    $foundCAS = false;
    $foundSCJE = false;
    foreach ($queueCAS as $t) {
        if ($t['user_id'] == $user1['id']) {
            if ($t['college'] === 'CAS') $foundCAS = true;
        }
        if ($t['user_id'] == $user2['id']) {
            if ($t['college'] === 'SCJE') $foundSCJE = true;
        }
    }

    if ($foundCAS && !$foundSCJE) {
        echo "Filtering Verification Success: CAS filter works.\n";
    } else {
        echo "Filtering Verification Failed: CAS Filter. Found: CAS=" . ($foundCAS?'Yes':'No') . ", SCJE=" . ($foundSCJE?'Yes':'No') . "\n";
    }

    // TEST 3: Filter by SCJE
    $windowModel->updatePreferredColleges($windowId, ['SCJE']);
    $queueSCJE = $ticketModel->getWaitingQueue(null, $windowId);
    
    $foundCAS = false;
    $foundSCJE = false;
    foreach ($queueSCJE as $t) {
        if ($t['user_id'] == $user1['id']) {
            if ($t['college'] === 'CAS') $foundCAS = true;
        }
        if ($t['user_id'] == $user2['id']) {
            if ($t['college'] === 'SCJE') $foundSCJE = true;
        }
    }

    if (!$foundCAS && $foundSCJE) {
        echo "Filtering Verification Success: SCJE filter works.\n";
    } else {
        echo "Filtering Verification Failed: SCJE Filter. Found: CAS=" . ($foundCAS?'Yes':'No') . ", SCJE=" . ($foundSCJE?'Yes':'No') . "\n";
    }

    // Cleanup
    $db->prepare("DELETE FROM tickets WHERE user_id IN (?, ?)")->execute([$user1['id'], $user2['id']]);
    $db->prepare("DELETE FROM users WHERE id IN (?, ?)")->execute([$user1['id'], $user2['id']]);
    echo "Cleanup complete.\n";

} catch (Exception $e) {
    die("Error during verification: " . $e->getMessage() . "\n" . $e->getTraceAsString());
}

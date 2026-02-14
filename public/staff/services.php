<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Window.php';

requireRole('staff');

$windowModel = new Window();
$window = $windowModel->getWindowByStaff($_SESSION['user_id']);

if (!$window) {
    die("No window assigned.");
}

$services = $windowModel->getWindowServices($window['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Services - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php injectTailwindConfig(); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen">
    <?php include __DIR__ . '/../../includes/staff-navbar.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-list-check text-primary mr-2"></i> Managed Services
                    </h2>
                    <p class="text-gray-600 mt-1">Toggle the services you can handle at this window.</p>
                </div>
                
                <div class="divide-y divide-gray-100">
                    <?php if(empty($services)): ?>
                        <div class="p-8 text-center text-gray-500">
                            No services available to manage.
                        </div>
                    <?php else: ?>
                        <?php foreach($services as $service): ?>
                            <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition">
                                <div class="flex-1">
                                    <h3 class="font-bold text-lg text-gray-800">
                                        <?php echo $service['service_name']; ?>
                                    </h3>
                                    <span class="text-sm font-mono text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                        <?php echo $service['service_code']; ?>
                                    </span>
                                </div>
                                
                                <div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer" 
                                            onchange="toggleService(<?php echo $service['id']; ?>)"
                                            <?php echo $service['is_enabled'] ? 'checked' : ''; ?>>
                                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-600"></div>
                                        <span class="ml-3 text-sm font-medium text-gray-700 peer-checked:text-green-600">
                                            <span id="status-<?php echo $service['id']; ?>">
                                                <?php echo $service['is_enabled'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleService(serviceId) {
            fetch('../api/toggle-service.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    window_id: <?php echo $window['id']; ?>,
                    service_id: serviceId
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    const statusSpan = document.getElementById('status-' + serviceId);
                    if(data.is_enabled) {
                        statusSpan.textContent = 'Active';
                    } else {
                        statusSpan.textContent = 'Inactive';
                    }
                } else {
                    alert('Failed to update service status');
                    // Revert checkbox state (optional/complex to implement simply here)
                    location.reload(); 
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred');
            });
        }
    </script>
</body>
</html>

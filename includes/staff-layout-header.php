<?php
/**
 * Modern Staff Layout Header
 * Consistent with Admin and User premium aesthetics.
 */
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Staff Portal'; ?> - <?php echo APP_NAME; ?></title>
    
    <!-- Scripts & Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <?php if (function_exists('injectTailwindConfig')) injectTailwindConfig(); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="<?php echo function_exists('generateCsrfToken') ? generateCsrfToken() : ''; ?>">
    
    <script>
        const ANTIGRAVITY_BASE_URL = "<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>";
    </script>
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-heading { font-family: 'Outfit', sans-serif; }
        .glass-sidebar-staff { background: rgba(15, 23, 42, 0.98); backdrop-filter: blur(10px); }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

        /* TV/4K scaling*/
        @media (min-width: 1921px) {
            html { font-size: 18px; } 
            body { font-size: 1.125rem; }
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-900 overflow-x-hidden flex">
<?php include __DIR__ . '/staff-navbar.php'; ?>
<div id="staff-main-wrapper" class="flex-1 flex flex-col min-h-screen transition-all duration-300 lg:ml-72">
    <main class="flex-1 w-full pt-24 pb-16 px-4 md:px-10">

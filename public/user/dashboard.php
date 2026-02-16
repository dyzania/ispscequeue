<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Ticket.php';
require_once __DIR__ . '/../../models/Window.php';
require_once __DIR__ . '/../../models/Service.php';

requireLogin();
requireRole('user');

$ticketModel = new Ticket();
$windowModel = new Window();

$currentTicket = $ticketModel->getCurrentTicket(getUserId());
$pendingFeedback = $ticketModel->getPendingFeedbackTicket(getUserId());
$activeWindows = $windowModel->getActiveWindows();
$waitingTickets = $ticketModel->getWaitingQueue();

// Helper function to format duration in seconds to "Xh Ym Zs"
function formatDuration($seconds) {
    if ($seconds <= 0) return "0s";
    
    $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    $s = $seconds % 60;
    
    $parts = [];
    if ($h > 0) $parts[] = $h . "h";
    if ($m > 0) $parts[] = $m . "m";
    if ($s > 0 || empty($parts)) $parts[] = $s . "s";
    
    return implode(" ", $parts);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Dashboard - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php injectTailwindConfig(); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        const ANTIGRAVITY_BASE_URL = "<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>";
    </script>
    <script src="<?php echo BASE_URL; ?>/js/dashboard-refresh.js"></script>
</head>
<body class="min-h-screen">
    <?php include __DIR__ . '/../../includes/user-navbar.php'; ?>

    <main class="container-ultra px-4 md:px-10 py-8 4xl:py-20">
        <div id="header-sync">
            <!-- Welcome Header -->
            <div class="mb-6 md:mb-10 4xl:mb-20 flex flex-col md:flex-row md:items-center justify-between gap-4 md:gap-6">
                <div>
                    <h1 class="text-3xl md:text-4xl 3xl:text-6xl 5xl:text-8xl font-black text-gray-900 mb-1 font-heading tracking-tight leading-none">Live Dashboard</h1>
                    <p class="text-gray-500 font-medium text-xs md:text-sm 3xl:text-xl 5xl:text-3xl">Real-time status of services and windows.</p>
                </div>
                
                <?php if (!$currentTicket): ?>
                <a href="get-ticket.php" class="bg-primary-600 text-white px-6 md:px-8 py-2 md:py-4 3xl:px-12 5xl:px-20 3xl:py-6 5xl:py-10 rounded-xl md:rounded-2xl 3xl:rounded-[32px] 5xl:rounded-[48px] font-black shadow-primary-premium hover:bg-primary-700 hover:-translate-y-1 transition-all flex items-center justify-center space-x-2 md:space-x-3 group text-sm md:text-lg 3xl:text-2xl 5xl:text-4xl">
                    <i class="fas fa-plus-circle text-base md:text-xl 3xl:text-3xl 5xl:text-5xl group-hover:rotate-90 transition-transform duration-300"></i>
                    <span>Get New Ticket</span>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <div id="notification-sync">
            <!-- Notification Banner if being called -->
            <?php if ($currentTicket && ($currentTicket['status'] === 'called' || $currentTicket['status'] === 'serving')): ?>
                <!-- Notification Banner for Called/Serving -->
                <div class="mb-6 4xl:mb-20 bg-white border border-indigo-100 rounded-[1.5rem] md:rounded-[2rem] 5xl:rounded-[60px] p-3 md:p-6 3xl:p-10 5xl:p-20 shadow-premium flex flex-col md:flex-row items-center justify-between gap-4 overflow-hidden relative group">
                    <div class="flex items-center space-x-3 md:space-x-6 3xl:space-x-10 5xl:space-x-20 min-w-0 flex-1 w-full md:w-auto">
                        <div class="w-10 h-10 md:w-16 3xl:w-24 5xl:w-40 bg-indigo-50 rounded-lg md:rounded-2xl 3xl:rounded-[32px] 5xl:rounded-[48px] flex items-center justify-center animate-bounce shadow-lg shadow-indigo-100 shrink-0">
                            <i class="fas fa-bullhorn text-indigo-600 text-base md:text-2xl 3xl:text-4xl 5xl:text-7xl"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[8px] md:text-[10px] 3xl:text-sm 5xl:text-xl font-black uppercase tracking-[0.2em] text-indigo-600 mb-0.5 md:mb-1 leading-none">Being Called</p>
                            <h2 class="text-base md:text-xl 3xl:text-3xl 5xl:text-5xl font-black text-gray-900 font-heading leading-tight truncate">
                                <?php if ($currentTicket['window_number']): ?>
                                    <span class="bg-indigo-600 text-white px-2 md:px-3 5xl:px-6 py-0.5 md:py-1 5xl:py-2 rounded md:rounded-lg 5xl:rounded-2xl font-black text-[10px] md:text-base uppercase mr-2"><?php echo $currentTicket['window_number']; ?></span>
                                <?php endif; ?>
                                Ticket #<?php echo $currentTicket['ticket_number']; ?>
                            </h2>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 md:space-x-8 w-full md:w-auto justify-between md:justify-end border-t md:border-t-0 border-gray-50 pt-3 md:pt-0">
                        <a href="my-ticket.php" class="bg-indigo-600 text-white hover:bg-indigo-700 px-4 md:px-5 py-2 md:py-3 rounded md:rounded-2xl transition-all duration-300 shadow-lg shadow-indigo-200 font-black text-[10px] md:text-base uppercase whitespace-nowrap">
                            View Ticket <i class="fas fa-arrow-right ml-1 md:ml-2"></i>
                        </a>
                    </div>
                </div>
            <?php elseif ($pendingFeedback): ?>
                <!-- Notification Banner for Feedback -->
                <div class="mb-6 4xl:mb-20 bg-white border border-amber-100 rounded-[1.5rem] md:rounded-[2rem] 5xl:rounded-[60px] p-3 md:p-6 3xl:p-10 5xl:p-20 shadow-premium flex flex-col md:flex-row items-center justify-between gap-4 overflow-hidden relative group">
                    <div class="flex items-center space-x-3 md:space-x-6 3xl:space-x-10 5xl:space-x-20 min-w-0 flex-1 w-full md:w-auto">
                        <div class="w-10 h-10 md:w-16 3xl:w-24 5xl:w-40 bg-amber-50 rounded-lg md:rounded-2xl 3xl:rounded-[32px] 5xl:rounded-[48px] flex items-center justify-center animate-bounce shadow-lg shadow-amber-100 shrink-0">
                            <i class="fas fa-star text-amber-500 text-base md:text-2xl 3xl:text-4xl 5xl:text-7xl"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[8px] md:text-[10px] 3xl:text-sm 5xl:text-xl font-black uppercase tracking-[0.2em] text-amber-500 mb-0.5 md:mb-1 leading-none">Thank You!</p>
                            <h2 class="text-base md:text-xl 3xl:text-3xl 5xl:text-5xl font-black text-gray-900 font-heading leading-tight">
                                Ticket #<?php echo $pendingFeedback['ticket_number']; ?> Complete
                            </h2>
                            <?php
                            if ($pendingFeedback['created_at'] && $pendingFeedback['completed_at']) {
                                $created = new DateTime($pendingFeedback['created_at']);
                                $completed = new DateTime($pendingFeedback['completed_at']);
                                $interval = $created->diff($completed);
                                
                                $parts = [];
                                if ($interval->h > 0) $parts[] = $interval->h . 'h';
                                if ($interval->i > 0) $parts[] = $interval->i . 'm';
                                if (empty($parts)) $parts[] = $interval->s . 's';
                                
                                echo '<p class="text-[10px] md:text-xs 3xl:text-lg 5xl:text-2xl font-bold text-gray-400 mt-1">Total Time: ' . implode(' ', $parts) . '</p>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 md:space-x-8 w-full md:w-auto justify-between md:justify-end border-t md:border-t-0 border-gray-50 pt-3 md:pt-0">
                        <a href="my-ticket.php" class="bg-amber-500 text-white hover:bg-amber-600 px-4 md:px-5 py-2 md:py-3 rounded md:rounded-2xl transition-all duration-300 shadow-lg shadow-amber-200 font-black text-[10px] md:text-base uppercase whitespace-nowrap">
                            Give Feedback <i class="fas fa-comment-dots ml-1 md:ml-2"></i>
                        </a>
                    </div>
                </div>
            <?php elseif ($currentTicket): ?>
                <div class="mb-6 4xl:mb-20 bg-white border border-primary-100 rounded-[1.5rem] md:rounded-[2rem] 5xl:rounded-[60px] p-3 md:p-6 3xl:p-10 5xl:p-20 shadow-premium flex flex-col md:flex-row items-center justify-between gap-4 overflow-hidden relative group">
                    <div class="flex items-center space-x-3 md:space-x-6 3xl:space-x-10 5xl:space-x-20 min-w-0 flex-1 w-full md:w-auto">
                        <div class="w-10 h-10 md:w-16 3xl:w-24 5xl:w-40 bg-primary-50 rounded-lg md:rounded-2xl 3xl:rounded-[32px] 5xl:rounded-[48px] flex items-center justify-center group-hover:bg-primary-100 transition-colors shrink-0 shadow-lg shadow-primary-100">
                            <i class="fas fa-clock text-primary-600 text-base md:text-2xl 3xl:text-4xl 5xl:text-7xl"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[8px] md:text-[10px] 3xl:text-sm 5xl:text-xl font-black uppercase tracking-[0.2em] text-primary-600 mb-0.5 md:mb-1 leading-none">Your Active Ticket</p>
                            <h2 class="text-base md:text-xl 3xl:text-3xl 5xl:text-5xl font-black text-gray-900 font-heading leading-tight truncate">
                                <?php echo $currentTicket['ticket_number']; ?>
                            </h2>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 md:space-x-8 w-full md:w-auto justify-between md:justify-end border-t md:border-t-0 border-gray-50 pt-3 md:pt-0">
                        <div class="text-left md:text-right">
                            <p class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-0.5">Status</p>
                            <p class="text-[10px] md:text-sm font-black text-primary-600 uppercase">Waiting in Line</p>
                        </div>
                        <a href="my-ticket.php" class="bg-primary-50 text-primary-700 hover:bg-primary-600 hover:text-white px-4 md:px-5 py-2 md:py-3 rounded md:rounded-2xl transition-all duration-300 shadow-sm font-black text-[10px] md:text-base uppercase whitespace-nowrap">
                            View Position <i class="fas fa-chevron-right ml-1 md:ml-2 text-[10px] md:text-[10px] 3xl:text-sm 5xl:text-2xl"></i>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="flex flex-col lg:flex-row gap-10 5xl:gap-20">
            <!-- Windows Section -->
            <div class="w-full lg:w-[65%] space-y-8 5xl:space-y-16">
                <div class="flex items-center justify-between border-b border-gray-100 5xl:pb-10">
                    <h3 class="text-2xl 3xl:text-4xl 5xl:text-6xl font-black text-gray-900 font-heading">Service Windows</h3>
                    <div id="window-count-sync">
                        <span class="px-4 py-1.5 5xl:px-8 5xl:py-3 bg-green-100 text-green-700 rounded-full text-xs 3xl:text-sm 5xl:text-2xl font-black tracking-widest uppercase flex items-center">
                            <span class="w-1.5 h-1.5 5xl:w-4 5xl:h-4 bg-green-500 rounded-full mr-2 5xl:mr-4 animate-pulse"></span>
                            <?php echo count($activeWindows); ?> Active
                        </span>
                    </div>
                </div>

                <?php if (empty($activeWindows)): ?>
                    <div class="bg-white rounded-3xl 5xl:rounded-[64px] p-12 3xl:p-20 5xl:p-40 text-center border-2 border-dashed border-gray-100 shadow-sm">
                        <div class="w-20 3xl:w-32 5xl:w-60 h-20 3xl:h-32 5xl:h-60 bg-gray-50 rounded-3xl 3xl:rounded-[48px] 5xl:rounded-[80px] flex items-center justify-center mx-auto mb-6 5xl:mb-12">
                            <i class="fas fa-door-closed text-gray-300 text-3xl 3xl:text-5xl 5xl:text-8xl"></i>
                        </div>
                        <h4 class="text-xl 3xl:text-3xl 5xl:text-5xl font-bold text-gray-400 mb-2">Windows Closed</h4>
                        <p class="text-gray-400 max-w-xs 3xl:max-w-xl 5xl:max-w-4xl mx-auto text-sm 3xl:text-lg 5xl:text-3xl">Service windows are currently closed. Please check back during business hours.</p>
                    </div>
                <?php else: ?>
                    <!-- Hidden Sync Source for Real-time Updates -->
                    <div id="window-sync-source" class="hidden">
                        <?php foreach ($activeWindows as $window): ?>
                            <div class="sync-item" 
                                 data-id="<?php echo $window['id']; ?>" 
                                 data-active="1" 
                                 data-ticket="<?php echo $window['serving_ticket']; ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div id="window-carousel-viewport" class="relative overflow-hidden w-full cursor-grab active:cursor-grabbing py-4 -mx-4 px-4 md:mx-0 md:px-0">
                        <div id="window-carousel-track" class="flex gap-4 5xl:gap-12 w-max will-change-transform">
                            <?php foreach ($activeWindows as $window): ?>
                                <div class="window-slide min-w-[300px] md:min-w-[400px] 5xl:min-w-[700px] snap-center" data-window-id="<?php echo $window['id']; ?>">
                                    <div class="window-card bg-white rounded-[32px] 5xl:rounded-[60px] p-6 3xl:p-10 5xl:p-16 shadow-division border border-gray-50 hover:shadow-premium transition-all duration-300 group h-full">
                                        <div class="flex items-start justify-between mb-8 5xl:mb-16">
                                            <div class="flex items-center space-x-4 3xl:space-x-6 5xl:space-x-10">
                                                <div class="w-14 3xl:w-20 5xl:w-32 h-14 3xl:h-24 5xl:h-40 bg-primary-600 rounded-2xl 3xl:rounded-[32px] 5xl:rounded-[48px] flex items-center justify-center text-white shadow-lg shadow-primary-900/20 group-hover:rotate-6 transition-transform relative overflow-hidden">
                                                    <span class="text-xl 3xl:text-4xl 5xl:text-6xl font-black relative z-10"><?php echo str_replace('W-', '', $window['window_number']); ?></span>
                                                    <div class="absolute inset-0 bg-white/10 group-hover:scale-150 transition-transform duration-700"></div>
                                                </div>
                                                <div>
                                                    <h4 class="text-lg 3xl:text-3xl 5xl:text-5xl font-black text-gray-900 tracking-tight leading-none mb-1 5xl:mb-4"><?php echo $window['window_name']; ?></h4>
                                                    <p class="text-[10px] 3xl:text-sm 5xl:text-2xl font-bold text-gray-400 uppercase tracking-[0.2em]"><?php echo $window['window_number']; ?></p>
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-end">
                                                <span class="px-3 5xl:px-6 py-1 5xl:py-2 bg-green-50 text-green-600 rounded-lg 5xl:rounded-xl text-[10px] 3xl:text-xs 5xl:text-xl font-black tracking-widest uppercase mb-2">
                                                    Online
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Now Serving Display -->
                                        <?php 
                                        $isServing = !empty($window['serving_ticket']);
                                        $containerClasses = $isServing 
                                            ? 'bg-secondary-700 shadow-inner' 
                                            : 'bg-secondary-200/70';
                                        $labelClasses = $isServing ? 'text-white/50' : 'text-secondary-400';
                                        ?>
                                        <div class="serving-container <?php echo $containerClasses; ?> rounded-2xl 3xl:rounded-[24px] 5xl:rounded-[40px] p-5 3xl:p-6 5xl:p-10 mb-2 border border-transparent transition-all duration-500">
                                            <p class="serving-label text-[9px] 3xl:text-xs 5xl:text-lg font-black <?php echo $labelClasses; ?> uppercase tracking-[0.3em] mb-2 transition-colors">Now Serving</p>
                                            <div class="serving-ticket-display">
                                            <?php if ($isServing): ?>
                                                <div class="flex items-center justify-between">
                                                    <span class="text-xl 3xl:text-3xl 5xl:text-5xl font-black text-white font-heading transition-colors"><?php echo $window['serving_ticket']; ?></span>
                                                    <span class="flex h-3 w-3 3xl:h-4 3xl:w-4 5xl:h-8 5xl:w-8 relative">
                                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-40"></span>
                                                        <span class="relative inline-flex rounded-full h-3 w-3 3xl:h-4 3xl:w-4 5xl:h-8 5xl:w-8 bg-white"></span>
                                                    </span>
                                                </div>
                                            <?php else: ?>
                                                <div class="flex items-center justify-between">
                                                    <span class="text-xl 3xl:text-3xl 5xl:text-5xl font-black text-secondary-900/40 font-heading transition-colors">Counter Idle</span>
                                                    <i class="fas fa-moon text-secondary-300 text-lg 3xl:text-2xl 5xl:text-4xl transition-colors"></i>
                                                </div>
                                            <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                <?php endif; ?>
            </div>

            <!-- Queue List Section -->
            <div class="w-full lg:w-[35%] space-y-8 5xl:space-y-16" id="waitlist-sync">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4 5xl:pb-10">
                    <h3 class="text-2xl 5xl:text-5xl font-black text-gray-900 font-heading">Queue Waitlist</h3>
                    <i class="fas fa-sync-alt text-gray-300 5xl:text-4xl animate-spin-slow cursor-pointer hover:text-primary-600 transition-colors"></i>
                </div>
                <!-- Rest of waitlist unchanged -->

                <div class="bg-white rounded-[40px] 5xl:rounded-[64px] shadow-premium border border-gray-50 overflow-hidden">
                    <div id="queueTableBody" class="divide-y divide-gray-50">
                        <?php if (empty($waitingTickets)): ?>
                            <div class="p-12 5xl:p-32 text-center py-20 5xl:py-40">
                                <div class="w-16 h-16 5xl:w-32 5xl:h-32 bg-gray-50 rounded-2xl 5xl:rounded-[48px] flex items-center justify-center mx-auto mb-4 5xl:mb-10 text-gray-300">
                                    <i class="fas fa-mug-hot text-2xl 5xl:text-5xl"></i>
                                </div>
                                <p class="font-black text-gray-300 tracking-widest uppercase text-xs 5xl:text-3xl">Queue is Empty</p>
                            </div>
                        <?php else: ?>
                            <?php 
                            foreach ($waitingTickets as $ticket): 
                                $globalPos = $ticketModel->getGlobalQueuePosition($ticket['id']);
                                $estWaitSeconds = $ticketModel->getWeightedEstimatedWaitTime($ticket['id']);
                                $estWaitFormatted = formatDuration(round($estWaitSeconds));
                                $isOwnTicket = $currentTicket && $ticket['id'] === $currentTicket['id'];
                            ?>
                                <div class="p-4 md:p-6 5xl:p-14 flex items-center justify-between hover:bg-gray-50 transition-colors group <?php echo $isOwnTicket ? 'bg-primary-50/50 border-l-4 border-primary-600' : ''; ?>">
                                    <div class="flex items-center space-x-4 md:space-x-6 5xl:space-x-12 min-w-0 flex-1">
                                        <div class="w-12 md:w-14 5xl:w-24 h-12 md:h-14 5xl:h-24 shrink-0 relative">
                                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($ticket['user_name']); ?>&background=<?php echo $isOwnTicket ? '15803d' : '0f172a'; ?>&color=fff&font-size=0.35&bold=true" 
                                                 class="w-full h-full rounded-2xl 5xl:rounded-[32px] shadow-sm border border-slate-100 group-hover:scale-105 transition-transform" 
                                                 alt="">
                                            <div class="absolute -top-1 -right-1 bg-primary-600 text-white w-5 h-5 5xl:w-10 5xl:h-10 rounded-full flex items-center justify-center text-[8px] 5xl:text-lg font-black border-2 border-white shadow-sm">
                                                <?php echo $globalPos; ?>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center space-x-2">
                                                <p class="font-black text-gray-900 text-sm md:text-base 5xl:text-3xl leading-none mb-1 truncate"><?php echo $ticket['ticket_number']; ?></p>
                                                <?php if($isOwnTicket): ?>
                                                    <span class="bg-primary-600 text-white text-[8px] 5xl:text-lg font-black px-1.5 py-0.5 rounded uppercase tracking-tighter">YOU</span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-[10px] 5xl:text-xl font-bold text-gray-400 uppercase tracking-wider truncate"><?php echo $ticket['service_name']; ?></p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end shrink-0 ml-4">
                                        <span class="px-2 5xl:px-4 py-0.5 5xl:py-2 bg-primary-50 text-primary-600 rounded-lg 5xl:rounded-2xl text-[9px] md:text-[10px] 5xl:text-xl font-black uppercase tracking-wider mb-1">
                                            Waiting
                                        </span>
                                        <p class="text-[9px] 5xl:text-lg font-bold text-gray-600 tracking-tight flex items-center italic">
                                            <i class="fas fa-history mr-1 opacity-30 text-[8px]"></i>
                                            <?php echo $estWaitFormatted; ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <style>
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin-slow {
            animation: spin-slow 8s linear infinite;
        }
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .animate-gradient {
            background-size: 200% auto;
            animation: gradient 3s linear infinite;
        }
        /* Mobile Carousel Scrollbar Hide */
        .scrollbar-hide::-webkit-scrollbar {
            display: none !important;
        }
        .scrollbar-hide {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }
    </style>

    <script>
        // Unified Carousel Logic for User Dashboard
    const PIXELS_PER_SECOND = 120;
    let carouselState = {
        scrollPos: 0,
        lastTimestamp: null,
        animationId: null,
        isPaused: false,
        totalWidth: 0
    };

    function syncCarouselData() {
        const syncSource = document.getElementById('window-sync-source');
        if (!syncSource) return;

        const syncItems = syncSource.querySelectorAll('.sync-item');
        const track = document.getElementById('window-carousel-track');
        if (!track) return;

        syncItems.forEach(item => {
            const id = item.dataset.id;
            const ticket = item.dataset.ticket;
            
            const slides = track.querySelectorAll(`.window-slide[data-window-id="${id}"]`);
            
            slides.forEach(slide => {
                const display = slide.querySelector('.serving-ticket-display');
                const servingContainer = slide.querySelector('.serving-container');
                const servingLabel = slide.querySelector('.serving-label');
                const isServing = !!ticket;

                if (servingContainer) {
                    const baseContainer = "serving-container rounded-2xl 3xl:rounded-[24px] 5xl:rounded-[40px] p-5 3xl:p-6 5xl:p-10 mb-2 border border-transparent transition-all duration-500";
                    servingContainer.className = isServing 
                        ? `${baseContainer} bg-secondary-700 shadow-inner`
                        : `${baseContainer} bg-secondary-200/70`;
                }

                if (servingLabel) {
                    servingLabel.className = `serving-label text-[9px] 3xl:text-xs 5xl:text-lg font-black uppercase tracking-[0.3em] mb-2 transition-colors ${isServing ? 'text-white/50' : 'text-secondary-400'}`;
                }

                if (display) {
                    let html = '';
                    if (isServing) {
                        html = `
                            <div class="flex items-center justify-between">
                                <span class="text-xl 3xl:text-3xl 5xl:text-5xl font-black text-white font-heading transition-colors">${ticket}</span>
                                <span class="flex h-3 w-3 3xl:h-4 3xl:w-4 5xl:h-8 5xl:w-8 relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-40"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 3xl:h-4 3xl:w-4 5xl:h-8 5xl:w-8 bg-white"></span>
                                </span>
                            </div>`;
                    } else {
                        html = `
                            <div class="flex items-center justify-between">
                                <span class="text-xl 3xl:text-3xl 5xl:text-5xl font-black text-secondary-900/40 font-heading transition-colors">Counter Idle</span>
                                <i class="fas fa-moon text-secondary-300 text-lg 3xl:text-2xl 5xl:text-4xl transition-colors"></i>
                            </div>`;
                    }
                    if (display.innerHTML !== html) display.innerHTML = html;
                }
            });
        });
    }

    function calculateMetrics() {
        const track = document.getElementById('window-carousel-track');
        if (!track) return;
        const style = window.getComputedStyle(track);
        const gap = parseFloat(style.gap) || 0;
        carouselState.totalWidth = (track.scrollWidth + gap) / 3;
    }

    function initWindowCarousel() {
        const track = document.getElementById('window-carousel-track');
        const viewport = document.getElementById('window-carousel-viewport');
        if (!track || !viewport) return;

        const originalSlides = Array.from(track.querySelectorAll('.window-slide'));
        if (originalSlides.length === 0) return;
        
        const contentHtml = originalSlides.map(s => s.outerHTML).join('');
        track.innerHTML = contentHtml + contentHtml + contentHtml;
        
        // Drag scrolling variables
        let isDragging = false;
        let startX = 0;
        let lastX = 0;

        function handleDragStart(e) {
            isDragging = true;
            carouselState.isPaused = true;
            startX = (e.pageX || e.touches[0].pageX);
            lastX = startX;
            viewport.classList.add('cursor-grabbing');
            viewport.classList.remove('cursor-grab');
        }

        function handleDragEnd() {
            if (!isDragging) return;
            isDragging = false;
            carouselState.isPaused = false;
            viewport.classList.remove('cursor-grabbing');
            viewport.classList.add('cursor-grab');
            carouselState.lastTimestamp = null; // Re-sync animation timing
        }

        function handleDragMove(e) {
            if (!isDragging) return;
            const currentX = (e.pageX || e.touches?.[0]?.pageX);
            if (currentX === undefined) return;
            
            const diff = lastX - currentX;
            lastX = currentX;

            carouselState.scrollPos += diff;
            
            // Infinite loop handling
            while (carouselState.scrollPos >= carouselState.totalWidth) {
                carouselState.scrollPos -= carouselState.totalWidth;
            }
            while (carouselState.scrollPos < 0) {
                carouselState.scrollPos += carouselState.totalWidth;
            }
            
            track.style.transform = `translate3d(-${carouselState.scrollPos}px, 0, 0)`;
        }

        viewport.addEventListener('mousedown', handleDragStart);
        window.addEventListener('mouseup', handleDragEnd);
        window.addEventListener('mousemove', handleDragMove);

        viewport.addEventListener('touchstart', handleDragStart, { passive: true });
        window.addEventListener('touchend', handleDragEnd);
        window.addEventListener('touchmove', handleDragMove, { passive: false });

        viewport.addEventListener('mouseenter', () => { if (!isDragging) carouselState.isPaused = true; });
        viewport.addEventListener('mouseleave', () => { if (!isDragging) carouselState.isPaused = false; });
        
        requestAnimationFrame(() => {
            calculateMetrics();
            startAnimation();
        });

        window.addEventListener('resize', calculateMetrics);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) carouselState.lastTimestamp = null;
        });

        syncCarouselData();
    }

    function startAnimation() {
        if (carouselState.animationId) cancelAnimationFrame(carouselState.animationId);
        const track = document.getElementById('window-carousel-track');
        
        function animate(timestamp) {
            if (!carouselState.lastTimestamp) {
                carouselState.lastTimestamp = timestamp;
                carouselState.animationId = requestAnimationFrame(animate);
                return;
            }

            const delta = (timestamp - carouselState.lastTimestamp) / 1000;
            carouselState.lastTimestamp = timestamp;
            const cappedDelta = Math.min(delta, 0.1); 

            if (!carouselState.isPaused && carouselState.totalWidth > 0) {
                carouselState.scrollPos += PIXELS_PER_SECOND * cappedDelta;
                while (carouselState.scrollPos >= carouselState.totalWidth) {
                    carouselState.scrollPos -= carouselState.totalWidth;
                }
                track.style.transform = `translate3d(-${carouselState.scrollPos}px, 0, 0)`;
            }
            carouselState.animationId = requestAnimationFrame(animate);
        }
        carouselState.animationId = requestAnimationFrame(animate);
    }

    // Initialize Real-Time Auto-Refresh for specific stable segments
    new DashboardRefresh(['header-sync', 'notification-sync', 'window-count-sync', 'waitlist-sync', 'window-sync-source'], 3000);

    document.addEventListener('dashboard:updated', (e) => {
        if (e.detail.id === 'window-sync-source') {
            syncCarouselData();
        }
    });

    document.addEventListener('DOMContentLoaded', initWindowCarousel);
    </script>
    

    <?php include __DIR__ . '/../../includes/chatbot-widget.php'; ?>
    <script src="<?php echo BASE_URL; ?>/js/notifications.js"></script>
</body>
</html>

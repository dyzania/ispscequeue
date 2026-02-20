<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Ticket.php';
require_once __DIR__ . '/../../models/Window.php';

requireLogin();
requireRole('staff');

$ticketModel = new Ticket();
$windowModel = new Window();

$window = $windowModel->getWindowByStaff(getUserId());
$servingTicket = null;
$waitingTickets = [];

if ($window) {
    if (!isset($_SESSION['window_initialized'])) {
        $windowModel->setWindowStatus($window['id'], true);
        $_SESSION['window_initialized'] = true;
    }
    
    $activeTickets = $ticketModel->getActiveTicketsByWindow($window['id']);
    $archivedTickets = $ticketModel->getArchivedTicketsByWindow($window['id']);
    $waitingTickets = $ticketModel->getWaitingQueueForWindow($window['id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Counter - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php injectTailwindConfig(); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="<?php echo generateCsrfToken(); ?>">
    <script>
        const ANTIGRAVITY_BASE_URL = "<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>";
    </script>
    <script src="<?php echo BASE_URL; ?>/js/dashboard-refresh.js"></script>
</head>
<body class="min-h-screen">
    <?php include __DIR__ . '/../../includes/staff-navbar.php'; ?>

    <main class="container-ultra max-w-[2000px] mx-auto pt-2 pb-8 px-4 md:px-8">
        <?php if (!$window): ?>
            <div class="max-w-2xl mx-auto mt-20 text-center bg-white p-12 rounded-2xl shadow-2xl shadow-slate-200 border border-slate-100">
                <div class="w-24 h-24 bg-rose-50 rounded-xl flex items-center justify-center mx-auto mb-8">
                    <i class="fas fa-exclamation-triangle text-rose-500 text-4xl"></i>
                </div>
                <h2 class="text-3xl font-black text-gray-900 mb-4 font-heading">No Window Assigned</h2>
                <p class="text-gray-500 text-lg">You are not currently assigned to any service window. Please contact your administrator to get started.</p>
            </div>
        <?php else: ?>
            
            <!-- Compact Header -->
            <div class="bg-white rounded-2xl p-6 5xl:p-12 shadow-xl shadow-slate-200/50 border border-white mb-2 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
                <div class="flex items-center gap-6 5xl:gap-12 relative z-10">
                    <div class="w-16 5xl:w-32 h-16 5xl:h-32 bg-slate-900 rounded-2xl 5xl:rounded-[48px] flex items-center justify-center text-white font-black text-2xl 5xl:text-5xl shadow-lg">
                        <?php echo $window['window_number']; ?>
                    </div>
                    <div>
                        <p class="text-xs 5xl:text-xl font-bold text-gray-400 uppercase tracking-widest mb-1 5xl:mb-4">Operational Counter</p>
                        <h2 class="text-2xl 5xl:text-5xl font-black text-gray-900 leading-none"><?php echo $window['window_name']; ?></h2>
                    </div>
                </div>

                <div class="flex items-center gap-6 5xl:gap-12 relative z-10">
                    <div class="flex flex-col items-end">
                        <p class="text-[10px] 5xl:text-xl font-black text-gray-400 uppercase tracking-widest mb-2">Service Status</p>
                        <div class="flex items-center gap-4">
                            <span class="text-xs font-black uppercase tracking-wider <?php echo $window['is_active'] ? 'text-emerald-600' : 'text-rose-600'; ?>">
                                <?php echo $window['is_active'] ? 'Online' : 'On Break'; ?>
                            </span>
                            <button onclick="toggleBreakMode(<?php echo $window['id']; ?>, <?php echo $window['is_active']; ?>, this)" 
                                    class="relative inline-flex h-10 w-20 5xl:h-20 5xl:w-40 items-center rounded-full transition-all duration-300 focus:outline-none shadow-inner <?php echo $window['is_active'] ? 'bg-emerald-500' : 'bg-rose-500'; ?>">
                                <span class="sr-only">Toggle Online Status</span>
                                <span class="inline-block h-8 w-8 5xl:h-16 5xl:w-16 transform rounded-full bg-white shadow-xl transition-all duration-300 transform <?php echo $window['is_active'] ? 'translate-x-11 5xl:translate-x-22' : 'translate-x-1'; ?>"></span>
                                <div class="absolute inset-0 flex items-center justify-between px-3 5xl:px-6 pointer-events-none">
                                    <i class="fas fa-check text-[10px] 5xl:text-2xl text-white <?php echo $window['is_active'] ? 'opacity-100' : 'opacity-0'; ?> transition-opacity"></i>
                                    <i class="fas fa-coffee text-[10px] 5xl:text-2xl text-white <?php echo !$window['is_active'] ? 'opacity-100' : 'opacity-0'; ?> transition-opacity"></i>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Decorative BG -->
                <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-slate-50 to-transparent opacity-50 pointer-events-none"></div>
            </div>

            <!-- Performance Snapshot Row -->
            <div id="performance-snapshot-container">
                <?php $staffStats = $ticketModel->getStaffDailyStats(getUserId()); ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Served Today</p>
                            <h4 class="text-2xl font-black text-slate-900"><?php echo $staffStats['total_served']; ?></h4>
                        </div>
                        <div class="w-12 h-12 bg-secondary-50 rounded-xl flex items-center justify-center text-secondary-600">
                            <i class="fas fa-check-double text-xl"></i>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Avg. Speed</p>
                            <h4 class="text-2xl font-black text-slate-900">
                                <?php echo $staffStats['avg_processing_time']; ?>
                            </h4>
                        </div>
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-bolt text-xl"></i>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Services Offline</p>
                            <?php 
                                $allServices = $windowModel->getWindowServices($window['id']);
                                $offlineCount = count(array_filter($allServices, function($s) { return !$s['is_enabled']; }));
                            ?>
                            <h4 class="text-2xl font-black text-primary-600"><?php echo $offlineCount; ?></h4>
                        </div>
                        <div class="w-12 h-12 bg-primary-50 rounded-xl flex items-center justify-center text-primary-500">
                            <i class="fas fa-toggle-off text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Balanced 50/50 Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- Updated Section (Waitlist) - 50% -->
                <div class="space-y-6" id="waiting-tickets-container">
                     <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xl 5xl:text-4xl font-black text-gray-900 font-heading">Upcoming</h3>
                        <span class="px-3 5xl:px-6 py-1 5xl:py-3 bg-slate-100 text-slate-500 rounded-lg 5xl:rounded-2xl text-xs 5xl:text-2xl font-black uppercase tracking-wide"><?php echo count($waitingTickets); ?> Waiting</span>
                    </div>

                    <div class="bg-white rounded-2xl shadow-premium border border-white overflow-hidden min-h-[500px]">
                        <?php if (empty($waitingTickets)): ?>
                            <div class="flex flex-col items-center justify-center h-full p-10 5xl:p-20 text-center opacity-50">
                                <i class="fas fa-coffee text-4xl 5xl:text-8xl mb-4 5xl:mb-10 text-slate-300"></i>
                                <p class="font-bold 5xl:text-4xl text-slate-400">Queue is empty</p>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 5xl:gap-8 p-6 5xl:p-14">
                                <?php foreach (array_slice($waitingTickets, 0, 12) as $ticket): ?>
                                <div class="bg-white rounded-[24px] 5xl:rounded-[48px] p-4 5xl:p-14 flex items-center justify-between hover:bg-slate-50 border border-slate-200 shadow-division transition-all duration-300 group">
                                    <div class="flex items-center space-x-4 5xl:space-x-16 min-w-0 flex-1">
                                        <div class="w-14 h-14 5xl:w-32 5xl:h-32 bg-slate-900 rounded-2xl 5xl:rounded-[40px] flex items-center justify-center shadow-lg border border-slate-100 group-hover:scale-105 transition-transform shrink-0">
                                            <span class="text-white font-black text-xs 5xl:text-4xl"><?php echo $ticket['service_code']; ?></span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center space-x-3 mb-1 5xl:mb-4">
                                                <p class="font-black text-gray-900 text-lg 5xl:text-5xl leading-none tracking-tight"><?php echo $ticket['ticket_number']; ?></p>
                                            </div>
                                            <p class="text-[10px] 5xl:text-3xl font-bold text-slate-400 uppercase tracking-wider truncate"><?php echo $ticket['service_name']; ?></p>
                                            <p class="text-[10px] 5xl:text-2xl font-black text-slate-500 uppercase tracking-widest mt-1 truncate"><?php echo $ticket['user_name']; ?></p>
                                        </div>
                                    </div>
                                    <div class="w-2 5xl:w-5 h-2 5xl:h-5 rounded-full bg-primary-500 shadow-lg shadow-primary-100 group-hover:scale-125 transition-transform duration-200"></div>
                                </div>
                                <?php endforeach; ?>
                                <?php if(count($waitingTickets) > 12): ?>
                                    <div class="md:col-span-2 p-4 5xl:p-10 text-center text-xs 5xl:text-2xl font-bold text-gray-400 bg-slate-50 rounded-xl">
                                        + <?php echo count($waitingTickets) - 12; ?> more in queue
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Active Transactions Section - 50% -->
                <div class="space-y-6" id="active-transaction-container">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xl 5xl:text-4xl font-black text-gray-900 font-heading">Active Transaction</h3>
                        <?php if (!empty($activeTickets)): ?>
                            <span class="px-3 5xl:px-6 py-1 5xl:py-3 bg-primary-100 text-primary-700 rounded-lg 5xl:rounded-2xl text-xs 5xl:text-2xl font-black uppercase tracking-wide">Serving</span>
                        <?php else: ?>
                            <span class="px-3 5xl:px-6 py-1 5xl:py-3 bg-slate-100 text-slate-500 rounded-lg 5xl:rounded-2xl text-xs 5xl:text-2xl font-black uppercase tracking-wide">Idle</span>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php if (!empty($activeTickets)): ?>
                        <?php foreach ($activeTickets as $ticket): ?>
                        <div class="bg-white rounded-2xl overflow-hidden shadow-2xl shadow-primary-100 border border-white relative group flex flex-col">
                            <!-- Card Header -->
                            <div class="bg-slate-900 p-6 5xl:p-16 text-white relative overflow-hidden">
                                <div class="relative z-10 text-center">
                                    <p class="text-[10px] 5xl:text-xl font-black uppercase tracking-[0.3em] opacity-50 mb-2 5xl:mb-6">Current Ticket</p>
                                    <div class="text-4xl 5xl:text-8xl font-black font-heading group-hover:scale-110 transition-transform duration-300"><?php echo $ticket['ticket_number']; ?></div>
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-br from-primary-500/20 to-transparent"></div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-6 5xl:p-16 flex-1 flex flex-col">
                                <div class="text-center mb-6 5xl:mb-12">
                                    <h4 class="text-xl 5xl:text-4xl font-black text-gray-900 leading-tight mb-1"><?php echo $ticket['user_name']; ?></h4>
                                    <p class="text-xs 5xl:text-2xl font-bold text-primary-600 uppercase tracking-widest"><?php echo $ticket['service_name']; ?></p>
                                </div>

                                <?php if (!empty($ticket['user_note'])): ?>
                                    <div class="mb-6 5xl:mb-12 p-4 5xl:p-10 bg-amber-50 rounded-2xl border border-amber-100 relative overflow-hidden group/note">
                                        <div class="relative z-10">
                                            <p class="text-[10px] 5xl:text-xl font-black text-amber-600 uppercase tracking-widest mb-2 flex items-center gap-2">
                                                <i class="fas fa-comment-dots"></i> Customer Concern
                                            </p>
                                            <p class="text-sm 5xl:text-3xl font-bold text-gray-800 italic leading-relaxed">
                                                "<?php echo htmlspecialchars($ticket['user_note']); ?>"
                                            </p>
                                        </div>
                                        <div class="absolute -right-4 -bottom-4 text-6xl 5xl:text-9xl text-amber-100/30 group-hover/note:scale-110 transition-transform">
                                            <i class="fas fa-quote-right"></i>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($ticket['status'] === 'serving'): ?>
                                    <div class="mb-6 5xl:mb-12">
                                        <label for="staff-notes-<?php echo $ticket['id']; ?>" class="block text-[10px] 5xl:text-xl font-black text-gray-400 uppercase tracking-widest mb-2">Notes to User</label>
                                        <textarea id="staff-notes-<?php echo $ticket['id']; ?>" 
                                                  class="w-full px-4 5xl:px-10 py-3 5xl:py-8 bg-slate-50 border border-slate-200 rounded-xl 5xl:rounded-[32px] text-xs 5xl:text-2xl font-medium focus:ring-4 focus:ring-primary-100 focus:border-primary-400 transition-all resize-none"
                                                  placeholder="Type transaction notes here..."
                                                  rows="3"><?php echo $ticket['staff_notes']; ?></textarea>
                                    </div>
                                <?php endif; ?>

                                <div class="space-y-3 5xl:space-y-8 mt-auto">
                                    <?php if ($ticket['status'] === 'called'): ?>
                                        <button type="button" onclick="startServing(<?php echo $ticket['id']; ?>, this)" class="w-full py-3 5xl:py-8 bg-primary-600 text-white font-black rounded-xl 5xl:rounded-[32px] shadow-lg shadow-primary-200 hover:bg-primary-700 transition-all text-xs 5xl:text-2xl flex items-center justify-center gap-2">
                                            <i class="fas fa-play"></i> Start Serving
                                        </button>
                                        <button type="button" onclick="cancelTicket(<?php echo $ticket['id']; ?>, this)" class="w-full py-3 5xl:py-8 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl 5xl:rounded-[32px] hover:bg-slate-50 transition-all text-xs 5xl:text-2xl">
                                            No Show
                                        </button>
                                    <?php else: ?>
                                        <button type="button" onclick="completeTicket(<?php echo $ticket['id']; ?>, this)" class="w-full py-3 5xl:py-8 bg-primary-500 text-white font-black rounded-xl 5xl:rounded-[32px] shadow-lg shadow-primary-200 hover:bg-primary-600 transition-all text-xs 5xl:text-2xl flex items-center justify-center gap-2">
                                            <i class="fas fa-check"></i> Complete
                                        </button>
                                    <?php endif; ?>
                                    
                                    <button type="button" onclick="archiveTicket(<?php echo $ticket['id']; ?>, this)" class="w-full py-2 5xl:py-6 bg-amber-50 text-amber-600 font-bold rounded-xl hover:bg-amber-100 transition-all text-[10px] 5xl:text-xl flex items-center justify-center gap-2">
                                        <i class="fas fa-box-archive"></i> Archive
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <!-- Call Next Mini-Button -->
                        <div class="md:col-span-2 mt-2">
                            <?php if ($window['is_active']): ?>
                                <button type="button" onclick="callNext(<?php echo $window['id']; ?>, this)" class="relative z-10 w-full py-4 5xl:py-10 border-2 border-dashed border-primary-200 text-primary-500 font-bold rounded-2xl hover:bg-primary-50 hover:border-primary-300 transition-all flex items-center justify-center gap-2 5xl:gap-6 text-sm 5xl:text-3xl">
                                    <i class="fas fa-plus"></i> Call Another
                                </button>
                            <?php else: ?>
                                <button disabled class="w-full py-4 5xl:py-10 border-2 border-dashed border-slate-200 text-slate-400 font-bold rounded-2xl cursor-not-allowed flex items-center justify-center gap-2 5xl:gap-6 text-sm 5xl:text-3xl">
                                    <i class="fas fa-ban"></i> Go Online to Call
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="bg-white rounded-2xl p-8 5xl:p-32 text-center shadow-xl shadow-slate-200/50 border border-slate-100 py-20 5xl:py-40 col-span-full">
                            <div class="w-20 5xl:w-48 h-20 5xl:h-48 bg-primary-50 rounded-full flex items-center justify-center mx-auto mb-6 5xl:mb-16 animate-pulse">
                                <i class="fas fa-bell text-primary-500 text-2xl 5xl:text-7xl"></i>
                            </div>
                            <h3 class="text-xl 5xl:text-5xl font-black text-gray-900 mb-2 5xl:mb-10">Ready to Serve</h3>
                            <?php if ($window['is_active']): ?>
                                <button onclick="callNext(<?php echo $window['id']; ?>, this)" class="mt-6 5xl:mt-10 px-8 5xl:px-20 py-4 5xl:py-10 bg-primary-600 text-white font-black rounded-2xl 5xl:rounded-[40px] shadow-lg shadow-primary-200 hover:bg-primary-700 hover:-translate-y-1 transition-all text-sm 5xl:text-4xl uppercase tracking-widest">
                                    Call Next Ticket
                                </button>
                            <?php else: ?>
                                <button disabled class="mt-6 5xl:mt-10 px-8 5xl:px-20 py-4 5xl:py-10 bg-slate-200 text-slate-400 font-black rounded-2xl 5xl:rounded-[40px] cursor-not-allowed text-sm 5xl:text-4xl uppercase tracking-widest">
                                    You are Offline
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>

            </div>
        <?php endif; ?>
    </main>

    <script src="../js/notifications.js"></script>
    
    <script>
        function setLoading(btn, isLoading, showText = true) {
            // console.log('setLoading called', { btn, isLoading, showText });
            try {
                if (isLoading) {
                    btn.disabled = true;
                    btn.style.opacity = '0.7';
                    btn.style.cursor = 'not-allowed';
                    const originalText = btn.innerHTML;
                    btn.setAttribute('data-original-text', originalText);
                    if (showText) {
                        btn.innerHTML = `<i class="fas fa-circle-notch animate-spin mr-2"></i> Processing...`;
                    } else {
                        // Just add the spinner to the existing content if it's a toggle
                        btn.innerHTML = `<i class="fas fa-circle-notch animate-spin"></i>`;
                    }
                } else {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                    btn.style.cursor = 'pointer';
                    btn.innerHTML = btn.getAttribute('data-original-text');
                }
            } catch (e) {
                console.error('Error in setLoading:', e);
                // alert('Error in UI update: ' + e.message);
            }
        }

        function toggleBreakMode(windowId, currentStatus, btn) {
            console.log('toggleBreakMode called', { windowId, currentStatus });
            try {
                const newStatus = currentStatus ? 0 : 1;
                setLoading(btn, true, false);
                
                fetch('../api/set-window-status.php', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ window_id: windowId, is_active: newStatus })
                })
                .then(res => res.json())
                .then(async data => {
                    if (data.success) {
                        // Notify and delay reload
                        document.dispatchEvent(new CustomEvent('equeue:toast', { 
                            detail: { 
                                type: 'success', 
                                title: newStatus ? 'Back Online' : 'On Break',
                                message: newStatus ? 'You are now visible to customers.' : 'Window set to break mode.'
                            } 
                        }));
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        setLoading(btn, false);
                        await equeueAlert(data.message || 'Error updating status', 'Status Error');
                    }
                })
                .catch(async err => {
                    console.error('toggleBreakMode error:', err);
                    setLoading(btn, false);
                    await equeueAlert('Error: ' + err.message, 'Network Error');
                });
            } catch (e) {
                console.error('Error in toggleBreakMode:', e);
                equeueAlert('Script error: ' + e.message, 'System Error');
            }
        }

        function callNext(windowId, btn) {
            console.log('callNext called', { windowId, btn });
            try {
                setLoading(btn, true);
                fetch('../api/call-ticket.php', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ window_id: windowId })
                })
                .then(res => res.json())
                .then(async data => {
                    console.log('callNext data:', data);
                    if (data.success) {
                        window.location.reload();
                    } else {
                        setLoading(btn, false);
                        await equeueAlert(data.message, 'Action Failed');
                    }
                })
                .catch(async err => {
                    console.error('callNext error:', err);
                    setLoading(btn, false);
                    await equeueAlert('Error: ' + err.message, 'Network Error');
                });
            } catch (e) {
                console.error('Error in callNext:', e);
                equeueAlert('Script error: ' + e.message, 'System Error');
            }
        }

        function startServing(ticketId, btn) {
            console.log('startServing called', { ticketId });
            try {
                setLoading(btn, true);
                fetch('../api/start-serving.php', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ ticket_id: ticketId })
                })
                .then(res => res.json())
                .then(async data => {
                    if (data.success) {
                        // Notify and delay reload
                        document.dispatchEvent(new CustomEvent('equeue:toast', { 
                            detail: { type: 'serving', message: 'Service started' } 
                        }));
                        setTimeout(() => window.location.reload(), 3500);
                    } else {
                        setLoading(btn, false);
                        await equeueAlert(data.message || 'Error starting service', 'Action Failed');
                    }
                })
                .catch(async err => {
                    console.error('startServing error:', err);
                    setLoading(btn, false);
                    await equeueAlert('Error: ' + err.message, 'Network Error');
                });
            } catch (e) {
                console.error('Error in startServing:', e);
                equeueAlert('Script error: ' + e.message, 'System Error');
            }
        }

        async function cancelTicket(ticketId, btn) {
            console.log('cancelTicket called', { ticketId });
            try {
                if (!await equeueConfirm('Are you sure you want to cancel this ticket (No Show)?', 'Confirm No-Show')) return;
                
                setLoading(btn, true);
                fetch('../api/cancel-ticket.php', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ ticket_id: ticketId })
                })
                .then(res => res.json())
                .then(async data => {
                    if (data.success) {
                        // Notify and delay reload
                        document.dispatchEvent(new CustomEvent('equeue:toast', { 
                            detail: { type: 'cancelled', message: 'Ticket cancelled successfully' } 
                        }));
                        setTimeout(() => window.location.reload(), 3500);
                    } else {
                        setLoading(btn, false);
                        await equeueAlert(data.message || 'Error cancelling ticket', 'Action Failed');
                    }
                })
                .catch(async err => {
                    console.error('cancelTicket error:', err);
                    setLoading(btn, false);
                    await equeueAlert('Error: ' + err.message, 'Network Error');
                });
            } catch (e) {
                console.error('Error in cancelTicket:', e);
                equeueAlert('Script error: ' + e.message, 'System Error');
            }
        }

        function completeTicket(ticketId, btn) {
            console.log('completeTicket called', { ticketId, btn });
            try {
                const notesEl = document.getElementById(`staff-notes-${ticketId}`);
                const notes = notesEl ? notesEl.value : '';
                
                setLoading(btn, true);
                fetch('../api/complete-ticket.php', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ ticket_id: ticketId, notes: notes })
                })
                .then(res => {
                    console.log('completeTicket response status:', res.status);
                    return res.json();
                })
                .then(async data => {
                    console.log('completeTicket data:', data);
                    if (data.success) {
                        window.location.reload();
                    } else {
                        setLoading(btn, false);
                        await equeueAlert(data.message, 'Action Failed');
                    }
                })
                .catch(async err => {
                    console.error('completeTicket fetch error:', err);
                    setLoading(btn, false);
                    await equeueAlert('Network error: ' + err.message, 'Network Error');
                });
            } catch (e) {
                console.error('Error in completeTicket:', e);
                equeueAlert('Script error: ' + e.message, 'System Error');
            }
        }

        async function archiveTicket(ticketId, btn) {
            console.log('archiveTicket called', { ticketId, btn });
            try {
                if (!await equeueConfirm('Move this ticket to archive? Use this for long-running transactions.', 'Archive Ticket')) return;
                
                const notesEl = document.getElementById(`staff-notes-${ticketId}`);
                const notes = notesEl ? notesEl.value : '';

                setLoading(btn, true);
                fetch('../api/archive-ticket.php', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ ticket_id: ticketId, notes: notes })
                })
                .then(res => res.json())
                .then(async data => {
                    console.log('archiveTicket data:', data);
                    if (data.success) {
                        // Notify and delay reload
                        document.dispatchEvent(new CustomEvent('equeue:toast', { 
                            detail: { type: 'success', message: 'Ticket moved to archive' } 
                        }));
                        setTimeout(() => window.location.reload(), 3500);
                    } else {
                        setLoading(btn, false);
                        await equeueAlert(data.message || 'Error archiving ticket', 'Action Failed');
                    }
                })
                .catch(async err => {
                    console.error('archiveTicket error:', err);
                    setLoading(btn, false);
                    await equeueAlert('Error: ' + err.message, 'Network Error');
                });
            } catch (e) {
                console.error('Error in archiveTicket:', e);
                equeueAlert('Script error: ' + e.message, 'System Error');
            }
        }

        function resumeTicket(ticketId, btn) {
            console.log('resumeTicket called', { ticketId });
            try {
                setLoading(btn, true);
                fetch('../api/resume-ticket.php', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ ticket_id: ticketId })
                })
                .then(res => res.json())
                .then(async data => {
                    if (data.success) {
                        // Notify and delay reload
                        document.dispatchEvent(new CustomEvent('equeue:toast', { 
                            detail: { type: 'success', message: 'Ticket resumed successfully' } 
                        }));
                        setTimeout(() => window.location.reload(), 3500);
                    } else {
                        setLoading(btn, false);
                        await equeueAlert(data.message || 'Error resuming ticket', 'Action Failed');
                    }
                })
                .catch(async err => {
                    console.error('resumeTicket error:', err);
                    setLoading(btn, false);
                    await equeueAlert('Error: ' + err.message, 'Network Error');
                });
            } catch (e) {
                console.error('Error in resumeTicket:', e);
                equeueAlert('Script error: ' + e.message, 'System Error');
            }
        }

        // Live Timer for Archived Tickets
        setInterval(() => {
            document.querySelectorAll('.elapsed-timer').forEach(el => {
                let seconds = parseInt(el.getAttribute('data-seconds'));
                seconds++;
                el.setAttribute('data-seconds', seconds);
                
                // Format: Xd Xh Xm Xs
                let d = Math.floor(seconds / (3600*24));
                let h = Math.floor(seconds % (3600*24) / 3600);
                let m = Math.floor(seconds % 3600 / 60);
                let s = Math.floor(seconds % 60);
                
                let timeStr = '';
                if(d > 0) timeStr += d + 'd ';
                if(h > 0) timeStr += h + 'h ';
                if(m > 0 || h > 0 || d > 0) timeStr += m + 'm ';
                timeStr += s + 's';
                
                el.innerText = timeStr;
            });
        }, 1000);

        // Initialize Real-Time Auto-Refresh
        new DashboardRefresh([
            'performance-snapshot-container',
            'archived-tickets-container',
            'waiting-tickets-container',
            'active-transaction-container'
        ], 3000); // Refresh every 3 seconds

        // --- Staff Idle Wake-up Alert Logic ---
        let lastEmptyStartTime = null;
        const STAGNANT_THRESHOLD = 15 * 60; // 15 minutes in seconds
        
        // Notification Sound (Simple clean alert)
        // const alertSound = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
        // alertSound.volume = 0.7;

        // Browsers block audio until first interaction
        /*
        document.addEventListener('mousedown', () => {
             if (alertSound.paused && alertSound.currentTime === 0) {
                 // "Prime" the audio on first click
                 alertSound.play().catch(e => console.log('Audio autoplay prevented:', e));
             }
        }, { once: true });
        */

        function getWaitingTicketCount() {
            const container = document.getElementById('waiting-tickets-container');
            if (!container) return 0;
            // Count elements with bg-white inside the grid (but not the empty state div)
            // The empty state has 'p-10' or 'p-20' classes, actual tickets have 'p-6' or 'p-14'
            return container.querySelectorAll('.bg-white .flex.items-center').length;
        }

        // Initialize state
        if (getWaitingTicketCount() === 0) {
            lastEmptyStartTime = Date.now();
        }

        document.addEventListener('dashboard:updated', (e) => {
            if (e.detail.id === 'waiting-tickets-container') {
                const currentCount = getWaitingTicketCount();
                const now = Date.now();

                if (currentCount > 0) {
                    if (lastEmptyStartTime !== null) {
                        const secondsIdle = (now - lastEmptyStartTime) / 1000;
                        // Trigger Notification via centralized system (Sound + Toast + Native)
                        if (secondsIdle >= STAGNANT_THRESHOLD) {
                            document.dispatchEvent(new CustomEvent('equeue:toast', { 
                                detail: { 
                                    type: 'turn_next', 
                                    title: 'WAKE UP! NEW TICKET!', 
                                    message: 'A new ticket has arrived after idle period!',
                                    native: true 
                                } 
                            }));
                        }
                        lastEmptyStartTime = null; // No longer empty
                    }
                } else {
                    // Queue is currently empty
                    if (lastEmptyStartTime === null) {
                        lastEmptyStartTime = now;
                    }
                }
            }
        });
    </script>
    <script src="<?php echo BASE_URL; ?>/js/notifications.js"></script>
</body>
</html>

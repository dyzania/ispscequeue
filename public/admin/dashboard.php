<?php
$pageTitle = 'Executive Dashboard';
require_once __DIR__ . '/../../models/Ticket.php';
require_once __DIR__ . '/../../models/Service.php';
require_once __DIR__ . '/../../models/Window.php';
require_once __DIR__ . '/../../models/Feedback.php';
include __DIR__ . '/../../includes/admin-layout-header.php';

$ticketModel = new Ticket();
$serviceModel = new Service();
$windowModel = new Window();
$feedbackModel = new Feedback();

$stats = $ticketModel->getQueueStats();
$feedbackStats = $feedbackModel->getFeedbackStats();
$activeWindows = $windowModel->getActiveWindows();
?>

<div class="space-y-10" id="admin-dashboard-container">
    <!-- Dashboard Header Area -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <p class="text-[10px] 5xl:text-xl font-black uppercase tracking-[0.3em] text-primary-600 mb-2">Systems Overview</p>
            <h1 class="text-4xl 5xl:text-8xl font-black text-gray-900 font-heading tracking-tight">Overview</h1>
        </div>
        <div class="flex items-center space-x-3">
            <button class="px-6 py-3 bg-white border border-slate-200 rounded-xl font-bold text-gray-600 hover:bg-slate-50 transition-all flex items-center shadow-sm text-sm">
                <i class="fas fa-download mr-2 opacity-50"></i>Export
            </button>
            <button class="px-6 py-3 bg-slate-900 text-white rounded-xl font-bold flex items-center shadow-xl shadow-slate-200 text-sm hover:bg-black transition-all">
                <i class="fas fa-plus mr-2"></i>New Action
            </button>
        </div>
    </div>

    <!-- Stats Grid (Modern Positioning) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Clients Card -->
        <div class="bg-white rounded-2xl p-8 shadow-xl shadow-slate-200/50 border border-white hover:shadow-2xl transition-all duration-300 group">
            <div class="flex items-center justify-between mb-8">
                <div class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[10px] font-black flex items-center">
                    <i class="fas fa-caret-up mr-1 text-xs"></i> 12%
                </div>
                <div class="w-10 h-10 flex items-center justify-center text-slate-300">
                    <i class="fas fa-cog text-sm"></i>
                </div>
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <h3 class="text-gray-400 text-xs font-black uppercase tracking-widest mb-2">Total Tickets</h3>
                    <p id="stat-total" class="text-4xl font-black text-gray-900 font-heading tracking-tighter"><?php echo $stats['total'] ?? 0; ?></p>
                </div>
                <div class="w-14 h-14 bg-primary-50 rounded-xl flex items-center justify-center text-primary-600">
                    <i class="fas fa-ticket-alt text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Sales Card -->
        <div class="bg-white rounded-2xl p-8 shadow-xl shadow-slate-200/50 border border-white hover:shadow-2xl transition-all duration-300 group">
            <div class="flex items-center justify-between mb-8">
                <div class="px-3 py-1 bg-red-50 text-red-600 rounded-lg text-[10px] font-black flex items-center">
                    <i class="fas fa-caret-down mr-1 text-xs"></i> 8%
                </div>
                <div class="w-10 h-10 flex items-center justify-center text-slate-300">
                    <i class="fas fa-cog text-sm"></i>
                </div>
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <h3 class="text-gray-400 text-xs font-black uppercase tracking-widest mb-2">In Waiting</h3>
                    <p id="stat-waiting" class="text-4xl font-black text-gray-900 font-heading tracking-tighter"><?php echo $stats['waiting'] ?? 0; ?></p>
                </div>
                <div class="w-14 h-14 bg-accent-50 rounded-xl flex items-center justify-center text-accent-600">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Performance Card -->
        <div class="bg-white rounded-2xl p-8 shadow-xl shadow-slate-200/50 border border-white hover:shadow-2xl transition-all duration-300 group">
            <div class="flex items-center justify-between mb-8">
                <div class="px-3 py-1 bg-amber-50 text-amber-600 rounded-lg text-[10px] font-black flex items-center">
                    <i class="fas fa-exclamation-circle mr-1 text-xs"></i> Capacity
                </div>
                <div class="w-10 h-10 flex items-center justify-center text-slate-300">
                    <i class="fas fa-cog text-sm"></i>
                </div>
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <h3 class="text-gray-400 text-xs font-black uppercase tracking-widest mb-2">Efficiency</h3>
                    <p class="text-4xl font-black text-gray-900 font-heading tracking-tighter">94%</p>
                </div>
                <div class="w-14 h-14 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                    <i class="fas fa-bolt text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Command Center (Left) -->
        <div class="lg:col-span-8 space-y-8">
            <div class="bg-white rounded-2xl shadow-2xl shadow-slate-200/40 border border-white overflow-hidden">
                <div class="px-8 py-8 border-b border-slate-50 flex items-center justify-between bg-primary-900 text-white">
                    <div>
                        <h3 class="text-2xl font-black font-heading">Command Center</h3>
                        <p class="text-xs font-bold text-primary-300 uppercase tracking-widest mt-1">Live Window Performance</p>
                    </div>
                </div>
                <div class="p-8">
                    <div id="active-windows-container" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach ($activeWindows as $window): ?>
                            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 hover:border-primary-100 transition-all flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-white rounded-lg shadow-sm flex items-center justify-center text-primary-600 font-black border border-slate-100">
                                        <?php echo str_replace('W-', '', $window['window_number']); ?>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-gray-900 text-sm"><?php echo $window['window_name']; ?></h4>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest"><?php echo $window['staff_name']; ?></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-black text-gray-900"><?php echo $window['serving_ticket'] ?: '---'; ?></div>
                                    <div class="text-[10px] font-black text-emerald-500 uppercase">Active</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Global Live Feed -->
            <div class="bg-white rounded-2xl shadow-2xl shadow-slate-200/40 border border-white overflow-hidden">
                <div class="px-8 py-8 border-b border-slate-50 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-black text-gray-900 font-heading">System Feed</h3>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-1">Latest 5 Transactions</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="history.php" class="text-xs font-black text-primary-600 hover:text-primary-700 transition-colors uppercase tracking-widest flex items-center">
                            View History <i class="fas fa-arrow-right ml-2 text-[10px]"></i>
                        </a>
                        <div class="flex items-center space-x-2 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">
                            <span class="w-1.5 h-1.5 bg-primary-500 rounded-full animate-pulse"></span>
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Realtime</span>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <tbody id="live-queue-body" class="divide-y divide-slate-50">
                            <?php 
                            $recentTickets = $ticketModel->getRecentTickets(5);
                            foreach ($recentTickets as $ticket): 
                            ?>
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-slate-900 text-white rounded-lg flex items-center justify-center font-black text-[10px]">
                                            <?php echo $ticket['service_code']; ?>
                                        </div>
                                        <span class="font-black text-gray-900 text-sm"><?php echo $ticket['ticket_number']; ?></span>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="font-bold text-gray-700 text-sm"><?php echo $ticket['user_name']; ?></span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="px-3 py-1 bg-primary-50 text-primary-600 rounded-full text-[10px] font-black uppercase inline-block">
                                        <?php echo $ticket['status']; ?>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <span class="text-xs font-bold text-gray-400"><?php echo date('H:i', strtotime($ticket['created_at'])); ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Services & Forecast (Right) -->
        <div class="lg:col-span-4 space-y-8">
            <div class="bg-white rounded-2xl shadow-2xl shadow-slate-200/40 border border-white overflow-hidden p-8">
                <h3 class="text-xl font-black text-gray-900 font-heading mb-8">Active Services</h3>
                <div class="space-y-4">
                    <?php 
                    $allServices = $serviceModel->getAllServices();
                    foreach (array_slice($allServices, 0, 4) as $service): 
                    ?>
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-primary-600 font-black text-xs border border-slate-100">
                                <?php echo $service['service_code']; ?>
                            </div>
                            <span class="font-black text-gray-800 text-sm"><?php echo $service['service_name']; ?></span>
                        </div>
                        <div class="px-2 py-1 bg-emerald-50 text-emerald-600 rounded text-[9px] font-black">94%</div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Forecast Mini Component -->
            <div class="bg-indigo-600 rounded-2xl shadow-2xl border border-white/20 p-8 text-white relative overflow-hidden group">
                <div class="relative z-10">
                    <h3 class="text-xl font-black font-heading mb-2">Traffic Forecast</h3>
                    <p class="text-[10px] font-bold text-indigo-200 uppercase tracking-widest mb-6">Upcoming Volume</p>
                    <div class="flex items-end space-x-2 h-24">
                        <div class="flex-1 bg-white/10 rounded-t-lg h-[40%] group-hover:h-[60%] transition-all duration-500"></div>
                        <div class="flex-1 bg-white/20 rounded-t-lg h-[60%] group-hover:h-[80%] transition-all duration-500"></div>
                        <div class="flex-1 bg-amber-400 rounded-t-lg h-[90%] transition-all duration-500 shadow-lg shadow-amber-950/20"></div>
                        <div class="flex-1 bg-white/20 rounded-t-lg h-[50%] group-hover:h-[70%] transition-all duration-500"></div>
                        <div class="flex-1 bg-white/10 rounded-t-lg h-[30%] group-hover:h-[50%] transition-all duration-500"></div>
                    </div>
                    <div class="mt-6 pt-6 border-t border-white/10 flex justify-between items-center">
                        <span class="text-sm font-black text-amber-300">Peak Hour: 14:00</span>
                        <a href="analytics.php" class="text-xs font-black hover:text-amber-400 transition-colors">Details <i class="fas fa-arrow-right ml-1"></i></a>
                    </div>
                </div>
                <div class="absolute -right-8 -top-8 text-9xl font-black text-white/5 pointer-events-none tracking-tighter">AI</div>
            </div>
        </div>
    </div>
</div>

<script>
    function pollDashboardStats() {
        // Silently handled by DashboardRefresh
    }
    
    // Initialize Real-Time Auto-Refresh
    new DashboardRefresh(['admin-dashboard-container'], 10000);
</script>

<?php include __DIR__ . '/../../includes/admin-layout-footer.php'; ?>

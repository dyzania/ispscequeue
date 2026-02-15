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
$globalAvgTime = $ticketModel->getGlobalAverageProcessTime();
if ($globalAvgTime == 0) $globalAvgTime = 5; // Default to 5 mins if no data
?>

<div class="h-full flex flex-col space-y-4" id="admin-dashboard-container">
    <!-- Top Row: Windows Status -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 flex-none">
        <?php foreach ($windowModel->getAllWindows() as $window): 
            $isActive = $window['is_active'];
            $statusClass = $isActive ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-slate-100 text-slate-400 grayscale opacity-50';
        ?>
        <div class="rounded-xl h-[18.5rem] flex flex-col items-center justify-center text-center transition-all <?php echo $statusClass; ?>">
            <div class="text-xs font-bold uppercase tracking-widest mb-1"><?php echo $window['window_number']; ?></div>
            <div class="font-black text-sm"><?php echo $window['window_name']; ?></div>
            <?php if ($isActive): ?>
                <div class="mt-2 text-[10px] bg-white/20 px-2 py-0.5 rounded-full">
                    <?php echo $window['staff_name'] ?: 'No Staff'; ?>
                </div>
            <?php else: ?>
                <div class="mt-2 text-[10px] border border-slate-300 px-2 py-0.5 rounded-full">Offline</div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Main Content Layout -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-6 min-h-0">
        
        <!-- Left Column: Waiting Queue (Large) -->
        <div class="lg:col-span-8 bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-white overflow-hidden flex flex-col h-full">
            <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between bg-white sticky top-0 z-10 flex-none">
                <div class="text-center w-full"> 
                    <!-- Added w-full and text-center, referencing the request "justify and align center all" -->
                    <!-- But wait, justify-between with a badge... usually implies left/right. 
                         If I make it w-full text-center, the badge needs to be handled.
                         Let's keep the justify-between but make the TEXT centered relative to itself? 
                         Actually, let's just center the text content within its container. 
                    -->
                    <h3 class="text-2xl font-black text-gray-900 font-heading">Waiting Queue</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Real-time List</p>
                </div>
                <div class="hidden px-4 py-2 bg-amber-50 text-amber-600 rounded-full text-xs font-black uppercase tracking-wider animate-pulse">
                    <!-- Hiding the badge or moving it if we really want to CENTER everything? 
                         User said "justify and align center all the text". 
                         Let's assume they want the visual balance of centered text. 
                         I will wrap the header text in a centered div. 
                    -->
                    Live Feed
                </div>
            </div>
            
            <div class="flex-1 overflow-auto p-6">
                <!-- ... grid ... -->
                <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    <?php 
                    $waitingTickets = $ticketModel->getWaitingQueue();
                    // ... (rest of PHP logic same) ...
                    if (empty($waitingTickets)): ?>
                        <div class="col-span-full flex flex-col items-center justify-center text-slate-400 py-12 text-center">
                            <i class="fas fa-clipboard-list text-4xl mb-3 opacity-20"></i>
                            <p class="font-bold">No tickets in queue.</p>
                        </div>
                    <?php else: 
                        $position = 0;
                        foreach ($waitingTickets as $ticket): 
                            $position++;
                            $peopleInFront = $position - 1;
                            $estWaitMinutes = $peopleInFront * $globalAvgTime;
                    ?>
                    <div class="bg-white rounded-xl p-3 flex flex-col items-center justify-center text-center border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 aspect-square group relative overflow-hidden">
                        <div class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1"><?php echo $ticket['service_code']; ?></div>
                        <div class="font-black text-2xl text-gray-900 mb-2 group-hover:scale-110 transition-transform tracking-tight"><?php echo $ticket['ticket_number']; ?></div>
                        <div class="text-xs font-black text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-100 shadow-sm">
                            Est. <?php echo $estWaitMinutes; ?>m
                        </div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column: Analytics Grid (2x2) -->
        <div class="lg:col-span-4 grid grid-rows-2 gap-4 h-full">
            
            <!-- Top Half -->
            <div class="grid grid-cols-2 gap-4 h-full">
                <!-- 1. Total Tickets -->
                <div class="bg-white rounded-2xl p-6 shadow-xl shadow-slate-200/50 border border-white flex flex-col items-center justify-center h-full text-center">
                    <div class="text-slate-300 mb-2"><i class="fas fa-ticket-alt text-2xl"></i></div>
                    <div>
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Total Tickets</h3>
                        <p class="text-4xl font-black text-gray-900"><?php echo $stats['total'] ?? 0; ?></p>
                    </div>
                </div>

                <!-- 2. Waiting Count -->
                <div class="bg-white rounded-2xl p-6 shadow-xl shadow-slate-200/50 border border-white flex flex-col items-center justify-center h-full text-center">
                    <div class="text-slate-300 mb-2"><i class="fas fa-clock text-2xl"></i></div>
                    <div>
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Waiting</h3>
                        <p class="text-4xl font-black text-gray-900"><?php echo $stats['waiting'] ?? 0; ?></p>
                    </div>
                </div>
            </div>

            <!-- Bottom Half -->
            <div class="grid grid-cols-2 gap-4 h-full">
                <!-- 3. Avg Process Time -->
                <div class="bg-white rounded-2xl p-6 shadow-xl shadow-slate-200/50 border border-white flex flex-col items-center justify-center h-full text-center">
                    <div class="text-slate-300 mb-2"><i class="fas fa-stopwatch text-2xl"></i></div>
                    <div>
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Avg Process Time</h3>
                        <p class="text-3xl font-black text-gray-900"><?php echo $ticketModel->getGlobalAverageProcessTime(); ?> <span class="text-xs text-slate-400 font-bold">min</span></p>
                    </div>
                </div>

                <!-- 4. Traffic Forecast -->
                <div class="bg-white rounded-2xl p-6 shadow-xl shadow-slate-200/50 border border-white flex flex-col items-center justify-center h-full text-center">
                    <div class="text-slate-300 mb-2"><i class="fas fa-chart-line text-2xl"></i></div>
                    <div>
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Peak Hour</h3>
                        <p class="text-3xl font-black text-gray-900"><?php echo $ticketModel->getPeakHour(); ?></p>
                    </div>
                </div>
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

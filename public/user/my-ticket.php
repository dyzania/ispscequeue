<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Ticket.php';
require_once __DIR__ . '/../../models/Feedback.php';

requireLogin();
requireRole('user');

$ticketModel = new Ticket();
$feedbackModel = new Feedback();

$now = time();
$ticket = $ticketModel->getCurrentTicket(getUserId());

// Function to format duration in seconds to "Xh Ym Zs"
function formatDuration($seconds) {
    if ($seconds < 60) return "0m";
    
    $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    
    $parts = [];
    if ($h > 0) $parts[] = $h . "h";
    if ($m > 0 || (empty($parts) && $m >= 0)) $parts[] = $m . "m";
    
    return implode(" ", $parts);
}

// If no active ticket, check for tickets needing feedback
if (!$ticket) {
    $ticket = $ticketModel->getPendingFeedbackTicket(getUserId());
}

$position = $ticket ? $ticketModel->getGlobalQueuePosition($ticket['id']) : 0;
$ticketsAhead = $ticket ? $ticketModel->getTicketsAhead($ticket['id']) : 0;
$initialPosition = $ticket ? $ticketModel->getInitialQueuePosition($ticket['id']) : 0;

// Use new Advanced Wait Time calculation
$estimatedWaitSeconds = $ticket ? $ticketModel->getAdvancedEstimatedWaitTime($ticket['id'], $now) : 0;
$estimatedWait = formatDuration(round($estimatedWaitSeconds));

// Average Processing Time (APT)
$avgProcessSeconds = $ticket ? $ticketModel->getPreciseAverageProcessTime($ticket['service_id']) : null;

$isArchived = $ticket && (int)($ticket['is_archived'] ?? 0) === 1;
$isWaiting = $ticket && $ticket['status'] === 'waiting';
$isCalled = $ticket && $ticket['status'] === 'called' && !$isArchived;
$isServing = $ticket && ($ticket['status'] === 'serving' || $isArchived); // Show as SERVING if archived
$isCompleted = $ticket && $ticket['status'] === 'completed';

// Calculate remaining time for serving state in PHP to prevent reset on refresh
$servingRemainingSeconds = null;
if ($isServing && $avgProcessSeconds) {
    $startTime = $ticket['served_at'] ?: $ticket['called_at'];
    $servedAt = strtotime($startTime ?? '');
    if ($servedAt) {
        $elapsed = time() - $servedAt;
        $servingRemainingSeconds = max(0, $avgProcessSeconds - $elapsed);
    }
}

$avgProcessTimeFormatted = $avgProcessSeconds ? formatDuration($avgProcessSeconds) : "";
$servingRemainingTimeFormatted = ($servingRemainingSeconds !== null) ? formatDuration($servingRemainingSeconds) : $avgProcessTimeFormatted;

$feedbackGiven = $ticket ? $feedbackModel->getFeedbackByTicket($ticket['id']) : null;
$history = $ticketModel->getUserTicketHistory(getUserId());

// Handle Feedback Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    verifyCsrfToken($_POST['csrf_token'] ?? '');
    
    if ($ticket) {
        $comment = sanitize($_POST['comment'] ?? '');
        
        $csatData = [
            'client_type' => $_POST['client_type'] ?? null,
            'client_type_others' => sanitize($_POST['client_type_others'] ?? ''),
            'contact_means' => $_POST['contact_means'] ?? null,
            'contact_means_others' => sanitize($_POST['contact_means_others'] ?? ''),
            'cc_awareness' => $_POST['cc_awareness'] ?? null,
            'cc_visibility' => $_POST['cc_visibility'] ?? null,
            'cc_helpfulness' => $_POST['cc_helpfulness'] ?? null,
            'rating_responsiveness_1' => $_POST['rating_responsiveness_1'] ?? null,
            'rating_responsiveness_2' => $_POST['rating_responsiveness_2'] ?? null,
            'rating_reliability' => $_POST['rating_reliability'] ?? null,
            'rating_access' => $_POST['rating_access'] ?? null,
            'rating_communication' => $_POST['rating_communication'] ?? null,
            'rating_costs' => $_POST['rating_costs'] ?? null,
            'rating_integrity' => $_POST['rating_integrity'] ?? null,
            'rating_courtesy' => $_POST['rating_courtesy'] ?? null,
            'rating_outcome' => $_POST['rating_outcome'] ?? null,
        ];
        
        if ($feedbackModel->createFeedback($ticket['id'], getUserId(), $ticket['window_id'], $comment, $csatData)) {
            header('Location: my-ticket.php?success=1');
            exit;
        }
    } else {
        // Redirect or handle error if no ticket is found
        header('Location: dashboard.php');
        exit;
    }
}
$pageTitle = 'My Ticket';
require_once __DIR__ . '/../../includes/user-layout-header.php';
?>

<script src="<?php echo BASE_URL; ?>/js/dashboard-refresh.js"></script>

<div class="container-ultra mx-auto px-4 md:px-10 w-full max-w-full overflow-x-hidden flex flex-col space-y-8 md:space-y-12 5xl:space-y-24 pb-12 md:pb-20 5xl:pb-32" id="ticket-main-content">
        <?php if (!$ticket): ?>
            <div class="max-w-2xl mx-auto mt-20 text-center bg-white p-12 3xl:p-20 rounded-[40px] 3xl:rounded-[56px] shadow-2xl shadow-slate-200 border border-slate-100">
                <div class="w-24 3xl:w-32 h-24 3xl:h-32 bg-primary-50 rounded-3xl 3xl:rounded-[48px] flex items-center justify-center mx-auto mb-8 animate-float shadow-division">
                    <i class="fas fa-ticket-alt text-primary-600 text-4xl 3xl:text-6xl opacity-40"></i>
                </div>
                <h2 class="text-3xl 3xl:text-5xl font-black text-gray-900 mb-4 font-heading tracking-tight">No Active Ticket</h2>
                <p class="text-gray-500 text-lg 3xl:text-2xl mb-10 max-w-sm 3xl:max-w-xl mx-auto">You don't have any tickets in the queue right now. Ready to start?</p>
                <a href="get-ticket.php" class="inline-block bg-primary-600 text-white px-12 3xl:px-16 py-5 3xl:py-8 rounded-3xl 3xl:rounded-[40px] font-black text-xl 3xl:text-3xl shadow-2xl shadow-primary-600/20 hover:bg-primary-500 hover:-translate-y-1 transition-all active:scale-95">
                    Explore Services
                </a>
            </div>
        <?php else: ?>
            
            <!-- Ticket Status Card -->
            <div class="bg-white rounded-[32px] md:rounded-[64px] p-2 shadow-ultra border border-slate-50">
                <div class="bg-slate-900 rounded-[28px] md:rounded-[56px] p-6 md:p-20 text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex flex-col md:flex-row items-center md:items-start justify-between gap-6 md:gap-20">
                            <!-- Left: Number -->
                            <div class="text-center md:text-left">
                                <?php $isCompleted = ($ticket['status'] === 'completed'); ?>
                                <p class="text-[10px] md:text-base font-black uppercase tracking-[0.4em] <?php echo $isCompleted ? 'text-secondary-400' : 'text-primary-400'; ?> mb-2 md:mb-4">Queue Ticket</p>
                                <h2 class="text-4xl md:text-[8.5rem] font-black font-heading tracking-tighter leading-none">
                                    <?php echo $ticket['ticket_number']; ?>
                                </h2>
                                <div class="mt-4 md:mt-10 flex items-center justify-center md:justify-start space-x-3 md:space-x-6">
                                    <span class="w-2.5 h-2.5 md:w-5 md:h-5 rounded-full <?php echo $isCompleted ? 'bg-secondary-500' : 'bg-primary-500'; ?> animate-ping"></span>
                                    <span class="text-xl md:text-4xl font-black uppercase tracking-[0.2em] <?php echo $isCompleted ? 'text-secondary-300' : 'text-primary-300'; ?>">
                                        <?php 
                                            if ($isCompleted) echo "COMPLETED";
                                            elseif ($isCalled) echo "CALLING";
                                            elseif ($isServing) echo "SERVING";
                                            else echo strtoupper($ticket['status']);
                                        ?>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Middle: Divider -->
                            <div class="hidden md:block w-px h-32 md:h-72 bg-white/10 mx-6"></div>
                            
                            <!-- Right: Service Details -->
                            <div class="flex-1 w-full md:w-auto">
                                <h3 class="text-base md:text-3xl font-black font-heading mb-4 md:mb-8 leading-tight opacity-50 text-center md:text-left"><?php echo $ticket['service_name']; ?></h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:flex md:flex-col gap-4 md:gap-8">
                                    <div class="px-6 md:px-12 py-4 md:py-10 bg-white/5 rounded-[22px] md:rounded-[32px] border border-white/10 flex items-center space-x-5 md:space-x-8 backdrop-blur-xl group/box">
                                        <div class="w-12 h-12 md:w-24 md:h-24 bg-white/10 rounded-2xl flex items-center justify-center shrink-0 border border-white/10 group-hover/box:scale-110 transition-transform">
                                            <span class="text-white font-black text-xs md:text-3xl"><?php echo $ticket['service_code']; ?></span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-[8px] md:text-sm font-black uppercase tracking-widest text-white opacity-60 mb-1 md:mb-2">
                                                <?php 
                                                    if ($isWaiting) echo "Queue Position";
                                                    elseif ($isCalled) echo "Queue Status";
                                                    elseif ($isServing) echo "Queue Status";
                                                    elseif ($isCompleted) echo "Started at Position";
                                                ?>
                                            </span>
                                            <div class="flex items-baseline space-x-3">
                                                <span class="text-2xl md:text-5xl font-black <?php echo ($isWaiting) ? 'text-amber-300' : 'text-white'; ?>">
                                                    <?php 
                                                        if ($isWaiting) echo '#' . $position;
                                                        elseif ($isCalled) echo "PROCEED";
                                                        elseif ($isServing) echo "SERVING";
                                                        elseif ($isCompleted) echo '#' . $initialPosition;
                                                    ?>
                                                </span>
                                                <?php if ($isWaiting): ?>
                                                    <span class="text-[10px] md:text-lg font-bold text-amber-300">
                                                        <?php if ($position === 1): ?>
                                                            (0 ahead)
                                                        <?php else: ?>
                                                            (<?php echo $ticketsAhead; ?> ahead)
                                                        <?php endif; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="px-6 md:px-12 py-4 md:py-10 bg-white/5 rounded-[22px] md:rounded-[32px] border border-white/10 flex items-center space-x-5 md:space-x-8 backdrop-blur-xl group/box">
                                        <i class="fas fa-clock text-white text-xl md:text-5xl shrink-0 group-hover/box:scale-110 transition-transform"></i>
                                        <div class="flex flex-col w-full">
                                            <div class="flex flex-col gap-3 md:gap-5">
                                                <div>
                                                    <span class="text-[8px] md:text-sm font-black uppercase tracking-widest text-white/60 mb-1 md:mb-2 block">
                                                        <?php echo $isCompleted ? "Total Waiting Time" : "Avg. Process Time"; ?>
                                                    </span>
                                                    <?php if ($isCompleted): ?>
                                                        <?php 
                                                            $compAt = ($ticket['completed_at'] ?? null) ? strtotime($ticket['completed_at']) : time();
                                                            $creatAt = ($ticket['created_at'] ?? null) ? strtotime($ticket['created_at']) : time();
                                                            $totalWaitSeconds = $compAt - $creatAt;
                                                            echo '<span class="text-[12px] md:text-2xl font-bold text-amber-200/80 tracking-tight leading-none block">' . formatDuration($totalWaitSeconds) . '</span>';
                                                        ?>
                                                    <?php else: ?>
                                                        <span class="text-[12px] md:text-2xl font-bold text-amber-200/80 tracking-tight leading-none block">
                                                            <?php echo $avgProcessTimeFormatted ? "~$avgProcessTimeFormatted / person" : ""; ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="pt-3 md:pt-5 border-t border-white/10">
                                                    <span class="text-[8px] md:text-sm font-black uppercase tracking-widest text-white mb-1 md:mb-2 block">
                                                        <?php 
                                                            if ($isCalled) echo "Proceed to Window";
                                                            elseif ($isServing) echo "Estimated Process Time";
                                                            elseif ($isCompleted) echo "Total Service Processed";
                                                            else echo "Estimated Waiting Time";
                                                        ?>
                                                    </span>
                                                    <span id="ticket-metric-secondary" 
                                                          class="text-2xl md:text-5xl font-black text-amber-300 leading-none block"
                                                          data-live-countdown="1"
                                                          data-ticket-id="<?php echo $ticket['id']; ?>"
                                                          <?php 
                                                            $targetTimestampMs = 0;
                                                            if ($isServing && $avgProcessSeconds && ($ticket['served_at'] ?? null)) {
                                                                $servedAt = strtotime($ticket['served_at']);
                                                                if ($servedAt) {
                                                                    $targetTimestampMs = ($servedAt + $avgProcessSeconds) * 1000;
                                                                }
                                                            } elseif ($isWaiting && $estimatedWaitSeconds > 0) {
                                                                $targetTimestampMs = ($now + $estimatedWaitSeconds) * 1000;
                                                            }
                                                          ?>
                                                          <?php if ($targetTimestampMs > 0): ?>
                                                            data-target-timestamp="<?php echo (int)round($targetTimestampMs); ?>"
                                                            data-server-now="<?php echo $now * 1000; ?>"
                                                            <?php if ($isServing): ?>data-is-serving="1"<?php endif; ?>
                                                          <?php endif; ?>>
                                                        <?php 
                                                            if ($isCalled) echo "GO NOW";
                                                            elseif ($isServing) echo $servingRemainingTimeFormatted ?: "-";
                                                            elseif ($isCompleted) {
                                                                $compAt = ($ticket['completed_at'] ?? null) ? strtotime($ticket['completed_at']) : time();
                                                                $servAt = ($ticket['served_at'] ?? null) ? strtotime($ticket['served_at']) : (($ticket['called_at'] ?? null) ? strtotime($ticket['called_at']) : $compAt);
                                                                $totalServiceSeconds = $compAt - $servAt;
                                                                echo formatDuration($totalServiceSeconds);
                                                            }
                                                            else echo $estimatedWait;
                                                        ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($ticket['status'] === 'called' || $ticket['status'] === 'serving'): ?>
                            <div class="mt-6 md:mt-16 p-0.5 md:p-1 bg-white/20 rounded-[24px] md:rounded-[32px]">
                                <div class="bg-indigo-600 p-4 md:p-12 rounded-[22px] md:rounded-[30px] flex md:flex-row flex-col items-center justify-center text-center md:text-left gap-4 md:gap-12 border border-white/20">
                                    <div class="w-12 h-12 md:w-32 md:h-32 bg-white rounded-full flex items-center justify-center animate-bounce shadow-xl shrink-0">
                                        <i class="fas fa-desktop text-xl md:text-5xl text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-[8px] md:text-2xl font-black uppercase tracking-[0.5em] text-indigo-200 mb-1 md:mb-4">WINDOW</p>
                                        <h4 class="text-3xl md:text-[8rem] font-black tracking-tighter font-heading text-white leading-none"><?php echo $ticket['window_number']; ?></h4>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Action Bar for Waiting Users -->
                <?php if ($ticket['status'] === 'waiting'): ?>
                    <div class="px-4 md:px-10 py-4 bg-slate-50 border-t border-slate-100 rounded-b-[24px] md:rounded-b-[48px] flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center space-x-2 text-slate-400">
                            <i class="fas fa-info-circle text-[10px] md:text-base"></i>
                            <span class="text-[8px] md:text-sm font-bold uppercase tracking-widest">Adjust sequence or cancel</span>
                        </div>
                        <div class="flex items-center space-x-3 w-full sm:w-auto">
                            <button onclick="snoozeTicket(<?php echo $ticket['id']; ?>)" class="flex-1 sm:flex-initial px-6 py-4 bg-white border border-slate-200 text-slate-600 rounded-xl font-black text-xs md:text-lg hover:bg-slate-100 transition-all flex items-center justify-center shadow-sm">
                                <i class="fas fa-hourglass-half mr-2 text-amber-500"></i>
                                Step Back
                            </button>
                            <button onclick="confirmCancel(<?php echo $ticket['id']; ?>)" class="flex-1 sm:flex-initial px-6 py-4 bg-red-50 border border-red-200 text-red-600 rounded-xl font-black text-xs md:text-lg hover:bg-red-100 transition-all flex items-center justify-center shadow-sm">
                                <i class="fas fa-times-circle mr-2"></i>
                                Cancel
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Action Area -->
            <div class="grid grid-cols-1 lg:grid-cols-1 gap-10">
                <?php if ($ticket['status'] === 'completed' && !$feedbackGiven): ?>
                    <div class="bg-white rounded-[32px] md:rounded-[48px] p-6 md:p-10 shadow-premium border border-slate-50 relative overflow-hidden group">
                        <div class="relative z-10">
                            <h3 class="text-3xl font-black text-gray-900 font-heading tracking-tight mb-2">How was your visit?</h3>
                            <p class="text-gray-500 font-medium mb-4 max-w-sm text-lg">We value your time. Let us know how we can improve our service.</p>
                            
                            <?php 
                            if ($ticket['status'] === 'completed' && ($ticket['completed_at'] ?? null)) {
                                $end = strtotime($ticket['completed_at']);
                                $start = ($ticket['served_at'] ?? null) ? strtotime($ticket['served_at']) : (($ticket['called_at'] ?? null) ? strtotime($ticket['called_at']) : $end);
                                
                                if ($start && $end) {
                                    $diff = $end - $start;
                                    $m = floor($diff / 60);
                                    $s = $diff % 60;
                                    $procTime = ($m > 0 ? "{$m}m " : "") . "{$s}s";
                                    echo "<div class='mb-6 inline-flex items-center px-4 py-1.5 bg-primary-50 text-primary-600 rounded-full text-xs font-black uppercase tracking-widest'>Total Service Time: $procTime</div>";
                                }
                            }
                            ?>

                            <form method="POST" class="space-y-8" id="csatForm">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                
                                <!-- Client Info Section -->
                                <div class="p-5 md:p-8 bg-slate-50 border border-slate-200 rounded-3xl space-y-8">
                                    <h4 class="font-bold text-gray-800 text-lg uppercase tracking-wide">Client Information</h4>
                                    
                                    <div class="space-y-4">
                                        <p class="font-bold text-gray-700 text-sm">Type of Client:</p>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                            <?php 
                                            $clientTypes = ['Student', 'Non-Teaching', 'Faculty', 'Alumni', 'Parent/Guardian', 'Others'];
                                            foreach($clientTypes as $ct): 
                                                $id = strtolower(str_replace([' ', '/'], '_', $ct));
                                            ?>
                                            <label class="flex items-center space-x-3 p-4 bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-primary-300 hover:bg-primary-50/30 transition-all select-none group">
                                                <input type="radio" name="client_type" value="<?php echo $ct; ?>" required class="w-5 h-5 text-primary-600 focus:ring-primary-500 border-slate-300" <?php echo $ct === 'Others' ? 'id="client_others_radio"' : ''; ?>>
                                                <span class="text-sm font-bold text-slate-700 group-hover:text-primary-700"><?php echo $ct; ?></span>
                                            </label>
                                            <?php endforeach; ?>
                                        </div>
                                        <input type="text" name="client_type_others" id="client_type_others" placeholder="Please specify..." class="w-full mt-3 px-5 py-3 border border-slate-200 rounded-xl hidden focus:ring-4 focus:ring-primary-100 focus:border-primary-500 outline-none font-bold text-sm bg-white" disabled>
                                    </div>

                                    <div class="space-y-4 pt-6 border-t border-slate-200">
                                        <p class="font-bold text-gray-700 text-sm">Means of contacting the office/person concerned:</p>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <?php 
                                            $contactMeans = ['In person', 'Over the Telephone', 'University Help Desk', 'Others'];
                                            foreach($contactMeans as $cm): 
                                            ?>
                                            <label class="flex items-center space-x-3 p-4 bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-primary-300 hover:bg-primary-50/30 transition-all select-none group">
                                                <input type="radio" name="contact_means" value="<?php echo $cm; ?>" required class="w-5 h-5 text-primary-600 focus:ring-primary-500 border-slate-300" <?php echo $cm === 'Others' ? 'id="contact_others_radio"' : ''; ?>>
                                                <span class="text-sm font-bold text-slate-700 group-hover:text-primary-700"><?php echo $cm; ?></span>
                                            </label>
                                            <?php endforeach; ?>
                                        </div>
                                        <input type="text" name="contact_means_others" id="contact_means_others" placeholder="Please specify..." class="w-full mt-3 px-5 py-3 border border-slate-200 rounded-xl hidden focus:ring-4 focus:ring-primary-100 focus:border-primary-500 outline-none font-bold text-sm bg-white" disabled>
                                    </div>
                                </div>

                                <!-- CC Awareness Section -->
                                <div class="p-6 bg-[#fdf8e1] border border-[#fce99f] rounded-3xl space-y-6">
                                    <div class="space-y-4">
                                        <p class="font-bold text-gray-800">A. Which of the following most describes your awareness of a Citizens Charter (CC)?</p>
                                        <div class="space-y-2 pl-4">
                                            <label class="flex items-baseline space-x-3 cursor-pointer">
                                                <input type="radio" name="cc_awareness" value="1" required class="text-primary-600 mt-1"> 
                                                <span>1. I know what a CC is and I saw this office\'s CC</span>
                                            </label>
                                            <label class="flex items-baseline space-x-3 cursor-pointer">
                                                <input type="radio" name="cc_awareness" value="2" required class="text-primary-600 mt-1"> 
                                                <span>2. I know what a CC is but I did NOT see this office\'s CC</span>
                                            </label>
                                            <label class="flex items-baseline space-x-3 cursor-pointer">
                                                <input type="radio" name="cc_awareness" value="3" required class="text-primary-600 mt-1"> 
                                                <span>3. I learned of the CC only when I saw this office\'s CC</span>
                                            </label>
                                            <label class="flex items-baseline space-x-3 cursor-pointer">
                                                <input type="radio" name="cc_awareness" value="4" required class="text-primary-600 mt-1"> 
                                                <span>4. I do not know what a CC is and I did not see one in this office. (Answer \'N/A\' on questions 1 and 3)</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="space-y-4 pt-4 border-t border-[#fce99f]" id="section_b_container">
                                        <p class="font-bold text-gray-800">B. If aware of CC (answered 1-3 in A), would you say that the CC of this office was...?</p>
                                        <div class="flex flex-wrap gap-4 pl-4 text-sm font-medium">
                                            <label class="flex items-center space-x-2 cursor-pointer"><input type="radio" name="cc_visibility" value="1" required class="text-primary-600"> <span>Easy to see</span></label>
                                            <label class="flex items-center space-x-2 cursor-pointer"><input type="radio" name="cc_visibility" value="2" required class="text-primary-600"> <span>Somewhat easy to see</span></label>
                                            <label class="flex items-center space-x-2 cursor-pointer"><input type="radio" name="cc_visibility" value="3" required class="text-primary-600"> <span>Difficult to see</span></label>
                                            <label class="flex items-center space-x-2 cursor-pointer"><input type="radio" name="cc_visibility" value="4" required class="text-primary-600"> <span>Not visible at all</span></label>
                                            <label class="flex items-center space-x-2 cursor-pointer"><input type="radio" name="cc_visibility" value="5" required class="text-primary-600" id="cc_visibility_na"> <span>N/A</span></label>
                                        </div>
                                    </div>

                                    <div class="space-y-4 pt-4 border-t border-[#fce99f]" id="section_c_container">
                                        <p class="font-bold text-gray-800">C. If aware of CC (answered codes in 1-3 in A), How much did the CC help you in your transaction?</p>
                                        <div class="flex flex-wrap gap-4 pl-4 text-sm font-medium">
                                            <label class="flex items-center space-x-2 cursor-pointer"><input type="radio" name="cc_helpfulness" value="1" required class="text-primary-600"> <span>Helped very much</span></label>
                                            <label class="flex items-center space-x-2 cursor-pointer"><input type="radio" name="cc_helpfulness" value="2" required class="text-primary-600"> <span>Somewhat helped</span></label>
                                            <label class="flex items-center space-x-2 cursor-pointer"><input type="radio" name="cc_helpfulness" value="3" required class="text-primary-600"> <span>Did not help</span></label>
                                            <label class="flex items-center space-x-2 cursor-pointer"><input type="radio" name="cc_helpfulness" value="4" required class="text-primary-600" id="cc_helpfulness_na"> <span>N/A</span></label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Satisfaction Scale Section -->
                                <div class="bg-slate-50 rounded-[32px] border border-slate-200 overflow-hidden">
                                    <div class="p-6 md:p-8 border-b border-slate-200 bg-white">
                                        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-primary-600 mb-2">Service Quality Audit</p>
                                        <h4 class="text-xl md:text-2xl font-black text-gray-900 font-heading tracking-tight leading-none mb-2">How satisfied were you?</h4>
                                        <p class="text-gray-500 font-bold text-xs">Please select the rating that corresponds to your experience for each category below:</p>
                                    </div>

                                    <div class="p-1 md:p-0">
                                        <div class="grid grid-cols-1 divide-y divide-slate-100">
                                            <?php 
                                            $csatRows = [
                                                ['name' => 'rating_responsiveness_1', 'text' => '1. I am satisfied with the service that I availed. (Responsiveness)'],
                                                ['name' => 'rating_responsiveness_2', 'text' => '2. I spent a reasonable amount of time for my transaction. (Responsiveness)'],
                                                ['name' => 'rating_reliability', 'text' => '3. The office followed the transaction\'s requirements and steps based on the information provided. (Reliability)'],
                                                ['name' => 'rating_access', 'text' => '4. The steps (including payment) I needed to do for my transaction were easy and simple. (Accessible)'],
                                                ['name' => 'rating_communication', 'text' => '5. I easily found information about my transaction from the office or its website. (Communication)'],
                                                ['name' => 'rating_costs', 'text' => '6. I paid a reasonable amount of fees for my transactions. (Cost)'],
                                                ['name' => 'rating_integrity', 'text' => '7. I feel the office was fair to everyone or "walang palakasan", during my transaction. (Integrity)'],
                                                ['name' => 'rating_courtesy', 'text' => '8. I was treated courteously by the staff, and (if asked for help) the staff was helpful. (Courteous)'],
                                                ['name' => 'rating_outcome', 'text' => '9. I got what I needed from the government office, or (if denied) denial of request was sufficiently explained to me. (Outcome)']
                                            ];
                                            $emojis = [
                                                '5' => ['icon' => '🤩', 'label' => 'Strongly Agree', 'color' => 'green'],
                                                '4' => ['icon' => '😊', 'label' => 'Agree', 'color' => 'emerald'],
                                                '3' => ['icon' => '😐', 'label' => 'Neutral', 'color' => 'yellow'],
                                                '2' => ['icon' => '🙁', 'label' => 'Disagree', 'color' => 'orange'],
                                                '1' => ['icon' => '😡', 'label' => 'Strongly Disagree', 'color' => 'red']
                                            ];
                                            foreach($csatRows as $idx => $row): 
                                            ?>
                                            <div class="p-6 md:p-10 bg-white hover:bg-slate-50 transition-colors group">
                                                <p class="text-md md:text-xl font-black text-slate-800 mb-6 group-hover:text-primary-700 transition-colors"><?php echo $row['text']; ?></p>
                                                
                                                <div class="grid grid-cols-5 md:flex md:flex-row-reverse md:justify-center gap-2 md:gap-4">
                                                    <?php foreach($emojis as $val => $e): ?>
                                                    <label class="flex flex-col items-center cursor-pointer group/item relative">
                                                        <input type="radio" name="<?php echo $row['name']; ?>" value="<?php echo $val; ?>" required class="peer absolute inset-0 opacity-0 cursor-pointer z-10">
                                                        <div class="w-full aspect-square md:w-20 md:h-20 flex flex-col items-center justify-center rounded-2xl border-2 border-slate-100 bg-white peer-checked:bg-<?php echo $e['color']; ?>-50 peer-checked:border-<?php echo $e['color']; ?>-500 peer-checked:scale-110 md:peer-checked:scale-105 transition-all duration-300 shadow-sm peer-checked:shadow-xl peer-checked:shadow-<?php echo $e['color']; ?>-100 relative overflow-hidden">
                                                            <div class="text-2xl md:text-4xl mb-1 filter peer-checked:grayscale-0 transition-all"><?php echo $e['icon']; ?></div>
                                                            <span class="text-[8px] md:text-[10px] font-black uppercase tracking-tighter text-slate-400 peer-checked:text-<?php echo $e['color']; ?>-700 text-center leading-none hidden md:block">
                                                                <?php echo str_replace(' ', '<br>', $e['label']); ?>
                                                            </span>
                                                            <div class="absolute -bottom-2 -right-2 text-4xl md:text-6xl opacity-0 peer-checked:opacity-20 transition-opacity font-black text-<?php echo $e['color']; ?>-500"><?php echo $val; ?></div>
                                                        </div>
                                                        <span class="mt-2 text-[9px] font-black text-slate-400 peer-checked:text-<?php echo $e['color']; ?>-600 md:hidden text-center"><?php echo $val; ?></span>
                                                    </label>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Feedback Comments Section -->
                                <div class="relative group bg-white p-4 sm:p-6 rounded-[32px] border border-slate-200 overflow-hidden">
                                    <p class="font-bold text-gray-800 text-lg mb-4 break-words whitespace-normal"></p>
                                    <textarea 
                                        name="comment" 
                                        rows="4"
                                        class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-[24px] focus:outline-none focus:ring-4 focus:ring-primary-100 focus:bg-white focus:border-primary-500 transition-all font-medium text-gray-700 text-lg shadow-inner placeholder-gray-400"
                                        placeholder="Write your feedback/comments here..."
                                    ></textarea>
                                </div>

                                <button type="submit" class="w-full bg-primary-600 text-white py-5 rounded-3xl font-black text-xl shadow-2xl shadow-primary-600/20 hover:bg-primary-500 hover:-translate-y-1 transition-all active:scale-95 flex items-center justify-center space-x-4 uppercase tracking-widest mt-8">
                                    <span>Submit Survey</span>
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                                <p class="text-center text-gray-400 text-xs font-bold uppercase tracking-widest mt-4">
                                    <i class="fas fa-robot mr-2"></i>Powered by AI Sentiment Analysis
                                </p>
                            </form>
                            
                            <!-- JavaScript for Dynamic Form Logic -->
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    // Client Type "Others"
                                    const clientOthersRadio = document.getElementById('client_others_radio');
                                    const clientTypeOthers = document.getElementById('client_type_others');
                                    document.querySelectorAll('input[name="client_type"]').forEach(radio => {
                                        radio.addEventListener('change', (e) => {
                                            if (e.target.value === 'Others') {
                                                clientTypeOthers.classList.remove('hidden');
                                                clientTypeOthers.disabled = false;
                                                clientTypeOthers.required = true;
                                            } else {
                                                clientTypeOthers.classList.add('hidden');
                                                clientTypeOthers.disabled = true;
                                                clientTypeOthers.required = false;
                                            }
                                        });
                                    });

                                    // Contact Means "Others"
                                    const contactOthersRadio = document.getElementById('contact_others_radio');
                                    const contactMeansOthers = document.getElementById('contact_means_others');
                                    document.querySelectorAll('input[name="contact_means"]').forEach(radio => {
                                        radio.addEventListener('change', (e) => {
                                            if (e.target.value === 'Others') {
                                                contactMeansOthers.classList.remove('hidden');
                                                contactMeansOthers.disabled = false;
                                                contactMeansOthers.required = true;
                                            } else {
                                                contactMeansOthers.classList.add('hidden');
                                                contactMeansOthers.disabled = true;
                                                contactMeansOthers.required = false;
                                            }
                                        });
                                    });

                                    // CC Awareness Logic (Section A -> B & C)
                                    const ccVisibilityNa = document.getElementById('cc_visibility_na');
                                    const ccHelpfulnessNa = document.getElementById('cc_helpfulness_na');
                                    const secB_radios = document.querySelectorAll('input[name="cc_visibility"]');
                                    const secC_radios = document.querySelectorAll('input[name="cc_helpfulness"]');

                                    document.querySelectorAll('input[name="cc_awareness"]').forEach(radio => {
                                        radio.addEventListener('change', (e) => {
                                            if (e.target.value === '4') {
                                                // Answered 4: Automatically check N/A and visually disable the rest
                                                ccVisibilityNa.checked = true;
                                                ccHelpfulnessNa.checked = true;
                                            }
                                        });
                                    });
                                });
                            </script>
                        </div>
                        
                        <div class="absolute -right-10 bottom-0 text-[150px] text-primary-50/70 pointer-events-none opacity-50 group-hover:rotate-6 transition-transform duration-700"><i class="fas fa-quote-right"></i></div>
                    </div>
                <?php elseif ($feedbackGiven): ?>
                    <div class="bg-green-50 rounded-[48px] p-12 text-center border border-green-100 shadow-xl shadow-green-100/50">
                        <div class="w-20 h-20 bg-green-500 rounded-3xl flex items-center justify-center text-white mx-auto mb-8 shadow-lg shadow-green-200">
                            <i class="fas fa-heart text-2xl"></i>
                        <h3 class="text-3xl font-black text-green-900 font-heading tracking-tight mb-2">Thank You!</h3>
                        <p class="text-green-700 font-medium text-lg">Your feedback has been recorded and analyzed. Safe travels!</p>
                        <?php 
                        if ($ticket['status'] === 'completed' && ($ticket['completed_at'] ?? null)) {
                            $end = strtotime($ticket['completed_at']);
                            $start = ($ticket['served_at'] ?? null) ? strtotime($ticket['served_at']) : (($ticket['called_at'] ?? null) ? strtotime($ticket['called_at']) : $end);
                            
                            if ($start && $end) {
                                $diff = $end - $start;
                                $m = floor($diff / 60);
                                $s = $diff % 60;
                                $procTime = ($m > 0 ? "{$m}m " : "") . "{$s}s";
                                echo "<div class='mt-6 inline-flex items-center px-4 py-1.5 bg-green-100 text-green-700 rounded-full text-xs font-black uppercase tracking-widest'>Processed in $procTime</div>";
                            }
                        }
                        ?>
                    </div>
                <?php else: ?>
                    <!-- Service Requirements -->
                    <div class="bg-white rounded-[32px] p-6 md:p-12 shadow-premium border border-slate-50 relative overflow-hidden">
                        <div class="relative z-10">
                            <h3 class="text-xl md:text-2xl font-black text-gray-800 font-heading tracking-tight mb-2 flex items-center">
                                <i class="fas fa-clipboard-list text-primary-600 mr-3"></i>
                                Service Requirements
                            </h3>
                            <p class="text-gray-400 text-[10px] md:text-sm font-bold uppercase tracking-widest mb-6">
                                Please ensure you have these ready
                            </p>

                            <?php if (!empty($ticket['requirements'])): ?>
                                <div class="flex flex-wrap gap-3 md:gap-4">
                                    <?php 
                                    $reqs = preg_split('/[,\n\r]+/', $ticket['requirements']);
                                    foreach ($reqs as $req): 
                                        $req = trim($req);
                                        if (empty($req)) continue;
                                    ?>
                                        <div class="px-5 py-3 md:px-8 md:py-4 bg-slate-50 border border-slate-100 rounded-2xl md:rounded-[24px] flex items-center space-x-3">
                                            <i class="fas fa-check-circle text-primary-500 text-[10px] md:text-base"></i>
                                            <span class="text-xs md:text-lg font-bold text-gray-600"><?php echo htmlspecialchars($req); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="p-6 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-center">
                                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">No technical requirements</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="absolute -right-6 -bottom-6 text-[100px] text-gray-50/50 pointer-events-none z-0 rotate-12"><i class="fas fa-folder-open"></i></div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div> <!-- Closing container-ultra -->

    <script src="<?php echo BASE_URL; ?>/js/live-countdown.js"></script>
    <script>
        // Initialize silent auto-refresh moved to bottom for reliability
        function getTicketMetaData() {
            // Helper to extract current state for LiveStatus
            const ticketNum = <?php echo json_encode($ticket['ticket_number'] ?? ''); ?>;
            const serviceName = <?php echo json_encode($ticket['service_name'] ?? ''); ?>;
            const status = <?php echo json_encode($ticket['status'] ?? ''); ?>;
            const windowNum = <?php echo json_encode($ticket['window_number'] ?? ''); ?>;
            const windowName = <?php echo json_encode($ticket['window_name'] ?? ''); ?>;
            
            return {
                ticket_number: ticketNum,
                service_name: serviceName,
                status: status,
                window_number: windowNum,
                window_name: windowName
            };
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Re-initialize if dashboard refreshes - Handled by LiveCountdown class
        });

        async function snoozeTicket(ticketId) {
            if (!await equeueConfirm('Moving your ticket back by 3 spots will give you more time. Proceed?', 'Snooze Ticket')) return;
            
            try {
                const response = await fetch(`${ANTIGRAVITY_BASE_URL}/api/snooze-ticket.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?php echo generateCsrfToken(); ?>'
                    },
                    body: JSON.stringify({ ticket_id: ticketId })
                });
                
                const data = await response.json();
                if (data.success) {
                    await equeueSuccess('Ticket snoozed! You moved back 3 spots.', 'Ticket Updated');
                    window.location.reload();
                } else {
                    await equeueAlert(data.message || 'Error snoozing ticket', 'Action Failed');
                }
            } catch (error) {
                console.error(error);
                await equeueAlert('Connection error', 'Network Error');
            }
        }

        async function confirmCancel(ticketId) {
            if (!await equeueConfirm('Are you sure you want to leave the queue? This cannot be undone.', 'Cancel Ticket')) return;
            
            try {
                const response = await fetch(`${ANTIGRAVITY_BASE_URL}/api/user-cancel-ticket.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?php echo generateCsrfToken(); ?>'
                    },
                    body: JSON.stringify({ ticket_id: ticketId })
                });
                
                const data = await response.json();
                if (data.success) {
                    window.location.href = 'dashboard.php';
                } else {
                    await equeueAlert(data.message || 'Error cancelling ticket', 'Action Failed');
                }
            } catch (error) {
                console.error(error);
                await equeueAlert('Connection error', 'Network Error');
            }
        }

        // Initialize silent auto-refresh for ticket status
        if (<?php echo ($ticket && $ticket['status'] !== 'completed') ? 'true' : 'false'; ?>) {
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof DashboardRefresh !== 'undefined') {
                    const refresh = new DashboardRefresh(['ticket-main-content'], 3000);
                }
            });
        }
    </script>
<?php require_once __DIR__ . '/../../includes/user-layout-footer.php'; ?>

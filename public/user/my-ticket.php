<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Ticket.php';
require_once __DIR__ . '/../../models/Feedback.php';

requireLogin();
requireRole('user');

$ticketModel = new Ticket();
$feedbackModel = new Feedback();

$ticket = $ticketModel->getCurrentTicket(getUserId());

// If no active ticket, check for tickets needing feedback
if (!$ticket) {
    $ticket = $ticketModel->getPendingFeedbackTicket(getUserId());
}

$position = $ticket ? $ticketModel->getQueuePosition($ticket['id']) : 0;
$avgProcessTime = $ticket ? $ticketModel->getAverageProcessTime($ticket['service_id']) : 3;
$estimatedWait = $position * $avgProcessTime;
$feedbackGiven = $ticket ? $feedbackModel->getFeedbackByTicket($ticket['id']) : null;
$history = $ticketModel->getUserTicketHistory(getUserId());

// Handle Feedback Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    verifyCsrfToken($_POST['csrf_token'] ?? '');
    
    $comment = sanitize($_POST['comment']);
    
    if ($feedbackModel->createFeedback($ticket['id'], getUserId(), $ticket['window_id'], $comment)) {
        header('Location: my-ticket.php?success=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Ticket - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php injectTailwindConfig(); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="../js/dashboard-refresh.js"></script>
</head>
<body class="min-h-screen pb-20">
    <?php include __DIR__ . '/../../includes/user-navbar.php'; ?>

    <main class="container-ultra px-4 md:px-10 py-8 pb-20" id="ticket-main-content">
        <?php if (!$ticket): ?>
            <div class="max-w-2xl mx-auto mt-20 text-center bg-white p-12 3xl:p-20 rounded-[40px] 3xl:rounded-[56px] shadow-2xl shadow-slate-200 border border-slate-100">
                <div class="w-24 3xl:w-32 h-24 3xl:h-32 bg-primary-50 rounded-3xl 3xl:rounded-[48px] flex items-center justify-center mx-auto mb-8 animate-float shadow-division">
                    <i class="fas fa-ticket-alt text-primary-600 text-4xl 3xl:text-6xl opacity-40"></i>
                </div>
                <h2 class="text-3xl 3xl:text-5xl font-black text-gray-900 mb-4 font-heading tracking-tight">No Active Ticket</h2>
                <p class="text-gray-500 text-lg 3xl:text-2xl mb-10 max-w-sm 3xl:max-w-xl mx-auto">You don't have any tickets in the queue right now. Ready to start?</p>
                <a href="get-ticket.php" class="inline-block bg-slate-900 text-white px-12 3xl:px-16 py-5 3xl:py-8 rounded-3xl 3xl:rounded-[40px] font-black text-xl 3xl:text-3xl shadow-2xl shadow-slate-200 hover:bg-black hover:-translate-y-1 transition-all active:scale-95">
                    Explore Services
                </a>
            </div>
        <?php else: ?>
            
            <!-- Ticket Status Card -->
            <div class="bg-white rounded-[32px] 3xl:rounded-[48px] p-2 shadow-premium border border-slate-50 mb-8 3xl:mb-12">
                <div class="bg-slate-900 rounded-[30px] 3xl:rounded-[44px] p-6 3xl:p-10 text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-10 3xl:gap-20">
                            <!-- Left: Number -->
                            <div class="text-center md:text-left">
                                <p class="text-[9px] 3xl:text-xs font-black uppercase tracking-[0.4em] text-primary-400 mb-2">Queue Ticket</p>
                                <h2 class="text-3xl 3xl:text-5xl font-black font-heading tracking-tighter leading-none flex items-center gap-3">
                                    <?php echo $ticket['ticket_number']; ?>
                                </h2>
                                <div class="mt-4 3xl:mt-6 flex items-center justify-center md:justify-start space-x-2 3xl:space-x-4">
                                    <span class="w-3 h-3 3xl:w-6 3xl:h-6 rounded-full bg-primary-500 animate-ping"></span>
                                    <span class="text-2xl 3xl:text-4xl font-black uppercase tracking-[0.2em] text-primary-300">
                                        <?php 
                                            if ($ticket['is_archived'] == 1 && $ticket['status'] !== 'completed') {
                                                echo "NOW SERVING";
                                            } elseif ($ticket['status'] === 'called') {
                                                echo "YOU'RE BEING CALLED AND";
                                            } elseif ($ticket['status'] === 'serving') {
                                                echo "NOW SERVING";
                                            } else {
                                                echo strtoupper($ticket['status']);
                                            }
                                        ?>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Middle: Divider -->
                            <div class="hidden md:block w-px h-32 3xl:h-48 bg-white/10 mx-4"></div>
                            
                            <!-- Right: Service Details -->
                            <div class="flex-1 text-center md:text-left">
                                <h3 class="text-lg 3xl:text-xl font-black font-heading mb-4 leading-tight opacity-50"><?php echo $ticket['service_name']; ?></h3>
                                <div class="flex flex-col gap-4 3xl:gap-6 mt-2">
                                    <div class="px-6 3xl:px-10 py-3 3xl:py-6 bg-white/5 rounded-[24px] border border-white/10 flex items-center space-x-6 backdrop-blur-md shadow-2xl">
                                        <i class="fas fa-user-friends text-primary-400 text-2xl 3xl:text-4xl"></i>
                                        <div class="flex flex-col">
                                            <span class="text-[9px] 3xl:text-xs font-black uppercase tracking-widest text-primary-400 opacity-60 mb-1">Queue Position</span>
                                            <span class="text-2xl 3xl:text-4xl font-black text-white">
                                                <?php 
                                                    if ($ticket['is_archived'] == 1 && $ticket['status'] !== 'completed') {
                                                        echo "NOW SERVING";
                                                    } elseif ($ticket['status'] === 'called') {
                                                        echo "ATTEND NOW";
                                                    } elseif ($ticket['status'] === 'serving') {
                                                        echo "NOW SERVING";
                                                    } else {
                                                        echo '#' . ($position + 1);
                                                    }
                                                ?>
                                            </span>
                                            <?php if ($ticket['status'] === 'waiting'): ?>
                                                <span class="text-xs 3xl:text-sm font-bold text-primary-300 mt-1">
                                                    <?php echo $position; ?> <?php echo $position === 1 ? 'person' : 'people'; ?> ahead
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="px-6 3xl:px-10 py-3 3xl:py-6 bg-white/5 rounded-[24px] border border-white/10 flex items-center space-x-6 backdrop-blur-md shadow-2xl">
                                        <i class="fas fa-clock text-amber-400 text-2xl 3xl:text-4xl"></i>
                                        <div class="flex flex-col">
                                            <span class="text-[9px] 3xl:text-xs font-black uppercase tracking-widest text-amber-500/60 mb-1">Est. Process Time</span>
                                            <span class="text-2xl 3xl:text-4xl font-black text-amber-300">
                                                <?php 
                                                    if ($ticket['is_archived'] == 1 && $ticket['status'] !== 'completed') {
                                                        echo "In Service";
                                                    } elseif ($ticket['status'] === 'called') {
                                                        echo "Arriving";
                                                    } elseif ($ticket['status'] === 'serving') {
                                                        echo "In Service";
                                                    } else {
                                                        echo "~" . $estimatedWait . " Minutes";
                                                    }
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($ticket['status'] === 'called' || $ticket['status'] === 'serving'): ?>
                            <div class="mt-8 3xl:mt-16 p-1 bg-white/20 rounded-[32px] shadow-[0_0_50px_rgba(255,255,255,0.1)]">
                                <div class="bg-indigo-600 p-6 3xl:p-12 rounded-[30px] flex flex-col items-center justify-center text-center gap-6 border border-white/20">
                                    <div class="w-20 3xl:w-32 h-20 3xl:h-32 bg-white rounded-full flex items-center justify-center animate-bounce shadow-xl">
                                        <i class="fas fa-desktop text-3xl 3xl:text-5xl text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-lg 3xl:text-2xl font-black uppercase tracking-[0.5em] text-indigo-200 mb-4 drop-shadow-md">PROCEED TO WINDOW</p>
                                        <h4 class="text-4xl 3xl:text-[8rem] font-black tracking-tighter font-heading text-white bg-clip-text leading-none"><?php echo $ticket['window_number']; ?></h4>
                                        <p class="text-xl 3xl:text-3xl font-bold text-indigo-100 mt-4 opacity-80"><?php echo $ticket['window_name']; ?></p>
                                        <?php if (!empty($ticket['location_info'])): ?>
                                            <div class="mt-6 inline-flex items-center px-6 py-3 bg-white/10 rounded-2xl border border-white/20 text-indigo-50 backdrop-blur-sm">
                                                <i class="fas fa-map-marker-alt mr-3 text-indigo-200"></i>
                                                <span class="text-sm 3xl:text-xl font-bold"><?php echo $ticket['location_info']; ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
<?php endif; ?>
                    </div>
                    
                    <!-- Decorative background element -->
                    <div class="absolute -right-20 -top-20 text-[200px] 3xl:text-[300px] font-black text-white/5 select-none pointer-events-none tracking-tighter">TIC</div>
                </div>

                <!-- NEW: Action Bar for Waiting Users -->
                <?php if ($ticket['status'] === 'waiting'): ?>
                    <div class="px-6 3xl:px-10 py-6 bg-slate-50 border-t border-slate-100 rounded-b-[32px] 3xl:rounded-b-[48px] flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center space-x-2 text-slate-400">
                            <i class="fas fa-info-circle"></i>
                            <span class="text-xs 3xl:text-lg font-bold uppercase tracking-widest">Need more time or changed your mind?</span>
                        </div>
                        <div class="flex items-center space-x-3 w-full sm:w-auto">
                            <button onclick="snoozeTicket(<?php echo $ticket['id']; ?>)" class="flex-1 sm:flex-initial px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl 3xl:rounded-2xl font-black text-[11.7px] sm:text-sm md:text-base 3xl:text-2xl hover:bg-slate-100 transition-all flex items-center justify-center">
                                <i class="fas fa-hourglass-half mr-2 text-amber-500"></i>
                                Step Back
                            </button>
                            <button onclick="confirmCancel(<?php echo $ticket['id']; ?>)" class="flex-1 sm:flex-initial px-6 py-3 bg-red-50 text-red-600 rounded-xl 3xl:rounded-2xl font-black text-[14px] sm:text-sm md:text-base 3xl:text-2xl hover:bg-red-100 transition-all flex items-center justify-center">
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
                    <div class="bg-white rounded-[48px] p-10 shadow-premium border border-slate-50 relative overflow-hidden group">
                        <div class="relative z-10">
                            <h3 class="text-3xl font-black text-gray-900 font-heading tracking-tight mb-2">How was your visit?</h3>
                            <p class="text-gray-500 font-medium mb-4 max-w-sm text-lg">We value your time. Let us know how we can improve our service.</p>
                            
                            <?php 
                            if ($ticket['status'] === 'completed' && $ticket['served_at'] && $ticket['completed_at']) {
                                $start = strtotime($ticket['served_at']);
                                $end = strtotime($ticket['completed_at']);
                                $diff = $end - $start;
                                $m = floor($diff / 60);
                                $s = $diff % 60;
                                $procTime = ($m > 0 ? "{$m}m " : "") . "{$s}s";
                                echo "<div class='mb-6 inline-flex items-center px-4 py-1.5 bg-primary-50 text-primary-600 rounded-full text-xs font-black uppercase tracking-widest'>Total Service Time: $procTime</div>";
                            }
                            ?>

                            <form method="POST" class="space-y-6">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                <div class="relative group">
                                    <textarea 
                                        name="comment" 
                                        required
                                        rows="4"
                                        class="w-full px-8 py-6 bg-slate-50 border border-slate-100 rounded-[32px] focus:outline-none focus:ring-4 focus:ring-primary-100 focus:bg-white focus:border-primary-500 transition-all font-medium text-gray-700 text-lg shadow-inner placeholder-gray-300"
                                        placeholder="Enter your experience here (e.g. Great and fast service!)"
                                    ></textarea>
                                    <div class="absolute top-6 right-8 text-primary-200 group-focus-within:text-primary-600 transition-colors">
                                        <i class="fas fa-pen-nib text-xl"></i>
                                    </div>
                                </div>

                                <button type="submit" class="w-full bg-slate-900 text-white py-6 rounded-3xl font-black text-xl shadow-2xl shadow-slate-200 hover:bg-black hover:-translate-y-1 transition-all active:scale-95 flex items-center justify-center space-x-4">
                                    <span>Submit Feedback</span>
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                                <p class="text-center text-gray-400 text-xs font-black uppercase tracking-widest">
                                    <i class="fas fa-robot mr-2"></i>Powered by AI Sentiment Analysis
                                </p>
                            </form>
                        </div>
                        
                        <!-- Floating graphic -->
                        <div class="absolute -right-10 bottom-0 text-[150px] text-primary-50/70 pointer-events-none opacity-50 group-hover:rotate-6 transition-transform duration-700"><i class="fas fa-quote-right"></i></div>
                    </div>
                <?php elseif ($feedbackGiven): ?>
                    <div class="bg-green-50 rounded-[48px] p-12 text-center border border-green-100 shadow-xl shadow-green-100/50">
                        <div class="w-20 h-20 bg-green-500 rounded-3xl flex items-center justify-center text-white mx-auto mb-8 shadow-lg shadow-green-200">
                            <i class="fas fa-heart text-2xl"></i>
                        <h3 class="text-3xl font-black text-green-900 font-heading tracking-tight mb-2">Thank You!</h3>
                        <p class="text-green-700 font-medium text-lg">Your feedback has been recorded and analyzed. Safe travels!</p>
                        <?php 
                        if ($ticket['status'] === 'completed' && $ticket['served_at'] && $ticket['completed_at']) {
                            $start = strtotime($ticket['served_at']);
                            $end = strtotime($ticket['completed_at']);
                            $diff = $end - $start;
                            $m = floor($diff / 60);
                            $s = $diff % 60;
                            $procTime = ($m > 0 ? "{$m}m " : "") . "{$s}s";
                            echo "<div class='mt-6 inline-flex items-center px-4 py-1.5 bg-green-100 text-green-700 rounded-full text-xs font-black uppercase tracking-widest'>Processed in $procTime</div>";
                        }
                        ?>
                    </div>
                <?php else: ?>
                    <!-- Preparation Checklist -->
                    <div class="bg-white rounded-[48px] p-10 3xl:p-16 shadow-premium border border-slate-50 overflow-hidden relative">
                        <div class="flex flex-col md:flex-row items-start justify-between gap-10 relative z-10">
                            <div class="flex-1">
                                <h3 class="text-2xl 3xl:text-4xl font-black text-gray-800 font-heading tracking-tight mb-4 flex items-center">
                                    <div class="w-10 h-10 3xl:w-16 3xl:h-16 bg-primary-600 rounded-xl 3xl:rounded-2xl flex items-center justify-center text-white mr-4 shadow-lg">
                                        <i class="fas fa-clipboard-check text-lg 3xl:text-2xl"></i>
                                    </div>
                                    Preparation Checklist
                                </h3>
                                <p class="text-gray-500 font-medium mb-8 text-sm 3xl:text-xl leading-relaxed max-w-xl">
                                    Please ensure you have these requirements ready. Being prepared helps our staff serve you and others much faster!
                                </p>

                                <?php if (!empty($ticket['requirements'])): ?>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 3xl:gap-6">
                                        <?php 
                                        $reqs = preg_split('/[,\n\r]+/', $ticket['requirements']);
                                        foreach ($reqs as $req): 
                                            $req = trim($req);
                                            if (empty($req)) continue;
                                        ?>
                                            <div class="flex items-start space-x-4 p-5 3xl:p-8 bg-slate-50 rounded-3xl border border-slate-100 group hover:border-primary-200 transition-colors">
                                                <div class="shrink-0 mt-1 w-6 h-6 3xl:w-10 3xl:h-10 bg-white border-2 border-slate-200 rounded-lg flex items-center justify-center group-hover:border-primary-500 transition-colors">
                                                    <i class="fas fa-check text-[10px] 3xl:text-lg text-primary-500 scale-0 group-hover:scale-100 transition-transform"></i>
                                                </div>
                                                <span class="text-sm 3xl:text-xl font-bold text-gray-600"><?php echo $req; ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="p-8 bg-slate-50 rounded-3xl border border-dashed border-slate-200 text-center">
                                        <p class="text-gray-400 font-bold italic">No specific requirements for this service.</p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="w-full md:w-80 shrink-0">
                                <div class="bg-gradient-to-br from-slate-900 to-slate-800 p-8 3xl:p-12 rounded-[2rem] 3xl:rounded-[3rem] text-white shadow-2xl relative overflow-hidden">
                                    <h4 class="text-lg 3xl:text-2xl font-black mb-4 relative z-10">Pro Tip</h4>
                                    <p class="text-xs 3xl:text-xl text-slate-300 font-medium leading-relaxed relative z-10">
                                        Most services take around 10-15 minutes per customer. You can use this time to review your documents or speak with our AI chatbot.
                                    </p>
                                    <i class="fas fa-lightbulb absolute -right-4 -bottom-4 text-6xl 3xl:text-9xl opacity-10 -rotate-12"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            
        <?php endif; ?>
    </main>

    <script>
        // Initialize silent auto-refresh for ticket status
        if (<?php echo ($ticket && $ticket['status'] !== 'completed') ? 'true' : 'false'; ?>) {
            const refresh = new DashboardRefresh(['ticket-main-content'], 10000);
            
            // Sync with Live Status whenever dashboard updates
            document.addEventListener('dashboard:updated', () => {
                // syncLiveStatus(); Removed
            });
        }

        function getTicketMetaData() {
            // Helper to extract current state for LiveStatus
            const ticketNum = "<?php echo $ticket['ticket_number']; ?>";
            const serviceName = "<?php echo $ticket['service_name']; ?>";
            const status = "<?php echo $ticket['status']; ?>";
            const windowNum = "<?php echo $ticket['window_number']; ?>";
            const windowName = "<?php echo $ticket['window_name']; ?>";
            
            // Reach into DOM for dynamic values (position/wait)
            const posText = document.querySelector('.text-2xl.3xl\\:text-4xl.font-black.text-white')?.textContent || "0";
            const pos = parseInt(posText.replace('#', '')) - 1;
            const waitText = document.querySelector('.text-2xl.3xl\\:text-4xl.font-black.text-amber-300')?.textContent || "0";
            const wait = parseInt(waitText.replace('~', '').replace(' Minutes', ''));

            return {
                ticket_number: ticketNum,
                service_name: serviceName,
                status: status,
                window_number: windowNum,
                window_name: windowName,
                position: isNaN(pos) ? 0 : pos,
                estimated_wait: isNaN(wait) ? 0 : wait
            };
        }


        async function snoozeTicket(ticketId) {
            if (!confirm('Moving your ticket back by 3 spots will give you more time. Proceed?')) return;
            
            try {
                const response = await fetch('../api/snooze-ticket.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?php echo generateCsrfToken(); ?>'
                    },
                    body: JSON.stringify({ ticket_id: ticketId })
                });
                
                const data = await response.json();
                if (data.success) {
                    document.dispatchEvent(new CustomEvent('equeue:toast', { 
                        detail: { type: 'success', message: 'Ticket snoozed! You moved back 3 spots.' } 
                    }));
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    alert(data.message || 'Error snoozing ticket');
                }
            } catch (error) {
                console.error(error);
                alert('Connection error');
            }
        }

        async function confirmCancel(ticketId) {
            if (!confirm('Are you sure you want to leave the queue? This cannot be undone.')) return;
            
            try {
                const response = await fetch('../api/user-cancel-ticket.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?php echo generateCsrfToken(); ?>'
                    },
                    body: JSON.stringify({ ticket_id: ticketId })
                });
                
                const data = await response.json();
                if (data.success) {
                    // Manual toast removed to prevent duplicates with backend notifications
                    window.location.href = 'dashboard.php';
                } else {
                    alert(data.message || 'Error cancelling ticket');
                }
            } catch (error) {
                console.error(error);
                alert('Connection error');
            }
        }
    </script>

    <?php include __DIR__ . '/../../includes/chatbot-widget.php'; ?>
</body>
</html>

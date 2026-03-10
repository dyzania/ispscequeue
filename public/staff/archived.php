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
$archivedTickets = [];

if ($window) {
    if (!isset($_SESSION['window_initialized'])) {
        $windowModel->setWindowStatus($window['id'], true);
        $_SESSION['window_initialized'] = true;
    }
    $archivedTickets = $ticketModel->getArchivedTicketsByWindow($window['id']);
}
$pageTitle = 'Archived Tickets';
require_once __DIR__ . '/../../includes/staff-layout-header.php';
?>

<script src="<?php echo BASE_URL; ?>/js/dashboard-refresh.js"></script>
<script src="<?php echo BASE_URL; ?>/js/live-countdown.js"></script>

        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-gray-900 font-heading">Archived Tickets</h1>
                <p class="text-gray-500">Manage long-running or paused transactions at your window.</p>
            </div>
            <div id="archived-count-sync">
                <span class="px-4 py-2 bg-amber-100 text-amber-700 rounded-xl text-sm font-black uppercase tracking-wide">
                    <?php echo count($archivedTickets); ?> On Hold
                </span>
            </div>
        </div>

        <?php if (!$window): ?>
            <div class="max-w-2xl mx-auto mt-20 text-center bg-white p-12 rounded-2xl shadow-xl border border-slate-100">
                <h2 class="text-3xl font-black text-gray-900 mb-4 font-heading">No Window Assigned</h2>
                <p class="text-gray-500 text-lg">You must be assigned to a window to view archives.</p>
            </div>
        <?php else: ?>
            <div id="archived-tickets-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (empty($archivedTickets)): ?>
                    <div class="md:col-span-2 lg:col-span-3 bg-white rounded-2xl p-20 text-center border-2 border-dashed border-slate-100">
                        <i class="fas fa-box-open text-slate-200 text-6xl mb-4"></i>
                        <p class="text-xl font-bold text-gray-400">No archived tickets at your window</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($archivedTickets as $ticket): ?>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col gap-6 group hover:shadow-md transition-all">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 bg-slate-900 rounded-2xl flex items-center justify-center shadow-md border border-slate-800 shrink-0">
                                    <span class="text-white font-black text-base"><?php echo $ticket['service_code']; ?></span>
                                </div>
                                <div>
                                    <p class="font-black text-gray-900 text-lg"><?php echo $ticket['ticket_number']; ?></p>
                                    <div class="px-2 py-0.5 bg-blue-50 rounded-md inline-block -ml-2 mb-1">
                                        <p class="text-sm font-black text-blue-700 uppercase tracking-wide"><?php echo $ticket['user_name']; ?></p>
                                    </div>
                                    <p class="text-[10px] font-bold text-primary-600 uppercase tracking-widest"><?php echo $ticket['service_name']; ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="flex items-center gap-2 justify-end">
                                    <i class="fas fa-clock text-xs text-amber-500"></i>
                                    <span class="text-xs font-black text-amber-600" 
                                          data-live-countdown="1"
                                          data-countdown-mode="elapsed"
                                          data-ticket-id="<?php echo $ticket['id']; ?>"
                                          data-target-timestamp="<?php echo (time() - $ticket['elapsed_seconds']) * 1000; ?>"
                                          data-server-now="<?php echo time() * 1000; ?>">
                                        <?php 
                                            $m = floor($ticket['elapsed_seconds'] / 60);
                                            $h = floor($m / 60);
                                            $m = $m % 60;
                                            if ($h > 0) {
                                                echo "{$h}h {$m}m";
                                            } else {
                                                echo "{$m}m"; 
                                            }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Staff Notes</label>
                            <textarea id="staff-notes-<?php echo $ticket['id']; ?>" 
                                      class="w-full bg-slate-50 border-slate-100 rounded-xl p-4 text-xs font-medium focus:ring-primary-500 focus:border-primary-500 transition-all"
                                      placeholder="Internal notes..." rows="3"><?php echo htmlspecialchars($ticket['staff_notes'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <button onclick="cancelTicket(<?php echo $ticket['id']; ?>, this)" class="py-3 bg-rose-50 text-rose-600 font-bold rounded-xl hover:bg-rose-100 transition-all text-xs flex items-center justify-center gap-2">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button onclick="completeTicket(<?php echo $ticket['id']; ?>, this)" class="py-3 bg-emerald-50 text-emerald-600 font-bold rounded-xl hover:bg-emerald-100 transition-all text-xs flex items-center justify-center gap-2">
                                <i class="fas fa-check"></i> Complete
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>

    <script src="../js/notifications.js"></script>
    <script>
        // Copy relevant JS from dashboard.php
        function setLoading(btn, isLoading) {
            if (isLoading) {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                const original = btn.innerHTML;
                btn.setAttribute('data-original', original);
                btn.innerHTML = '<i class="fas fa-circle-notch animate-spin"></i>';
            } else {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                btn.innerHTML = btn.getAttribute('data-original');
            }
        }

        async function cancelTicket(ticketId, btn) {
            if (!confirm('Are you sure you want to cancel this ticket?')) return;
            setLoading(btn, true);
            try {
                const res = await fetch('../api/cancel-ticket.php', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name=\"csrf-token\"]').content
                    },
                    body: JSON.stringify({ ticket_id: ticketId })
                });
                const data = await res.json();
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            } catch (e) { console.error(e); }
            setLoading(btn, false);
        }

        async function completeTicket(ticketId, btn) {
            const notes = document.getElementById(`staff-notes-${ticketId}`).value;
            setLoading(btn, true);
            try {
                const res = await fetch('../api/complete-ticket.php', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name=\"csrf-token\"]').content
                    },
                    body: JSON.stringify({ ticket_id: ticketId, notes: notes })
                });
                const data = await res.json();
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            } catch (e) { console.error(e); }
            setLoading(btn, false);
        }
    </script>

    <script src=\"<?php echo BASE_URL; ?>/js/dashboard-refresh.js\"></script>
    <script src=\"<?php echo BASE_URL; ?>/js/live-countdown.js\"></script>
    <script>
        new DashboardRefresh(['archived-tickets-container', 'archived-count-sync'], 3000);
    </script>
<?php require_once __DIR__ . '/../../includes/staff-layout-footer.php'; ?>

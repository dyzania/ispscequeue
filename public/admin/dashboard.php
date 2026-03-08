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

$now = time();
$stats = $ticketModel->getQueueStats();
$feedbackStats = $feedbackModel->getFeedbackStats();
$activeWindows = $windowModel->getActiveWindows();

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

<div class="h-full flex flex-col space-y-4 max-w-full overflow-x-hidden" id="admin-dashboard-container">
    <!-- Top Row: Windows Status (Persistent UI) -->
    <div class="relative group flex-none mb-4">
        <div class="overflow-hidden -my-10 py-10 px-4 -mx-4" id="window-carousel-viewport">
            <div id="window-carousel-track" class="flex gap-4 select-none" style="will-change: transform;">
                <?php 
                $allWindows = $windowModel->getAllWindows();
                foreach ($allWindows as $window): 
                    $isActive = $window['is_active'];
                    $servingTicket = $window['serving_ticket'];
                    if (!$isActive) {
                        $statusClass = 'bg-white/50 border-gray-200 grayscale opacity-40';
                    } elseif ($servingTicket) {
                        $statusClass = 'bg-white shadow-premium border-indigo-300 brightness-110 opacity-100 ring-4 ring-indigo-100/30 -translate-y-1';
                    } else {
                        $statusClass = 'bg-white shadow-division border-gray-50 opacity-90 hover:opacity-100 transition-opacity';
                    }
                ?>
                <div class="window-slide flex-none w-full sm:w-[calc(50%-8px)] lg:w-[calc(33.333%-11px)]" data-window-id="<?php echo $window['id']; ?>">
                    <div class="window-card rounded-[32px] lg:rounded-[48px] 5xl:rounded-[80px] p-6 lg:p-8 3xl:p-12 5xl:p-20 border transition-all duration-300 group/card overflow-hidden flex flex-col h-full <?php echo $statusClass; ?>">
                        <div class="flex items-start justify-between mb-4 5xl:mb-8">
                            <div class="flex items-center space-x-2 lg:space-x-3 3xl:space-x-6 5xl:space-x-10">
                                <div class="w-10 h-10 lg:w-12 lg:h-12 3xl:w-20 3xl:h-24 5xl:w-32 5xl:h-40 bg-primary-600 rounded-lg lg:rounded-xl 3xl:rounded-[32px] 5xl:rounded-[48px] flex items-center justify-center text-white shadow-lg shadow-primary-900/20 group-hover/card:rotate-6 transition-transform relative overflow-hidden flex-none">
                                    <span class="text-base lg:text-lg 3xl:text-4xl 5xl:text-6xl font-black relative z-10"><?php echo str_replace('W-', '', $window['window_number']); ?></span>
                                    <div class="absolute inset-0 bg-white/10 group-hover/card:scale-150 transition-transform duration-700"></div>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-sm lg:text-base 3xl:text-3xl 5xl:text-5xl font-black text-gray-900 tracking-tight leading-none mb-1 5xl:mb-4 truncate window-name"><?php echo $window['window_name']; ?></h4>
                                    <p class="text-[11px] lg:text-[12px] 3xl:text-sm 5xl:text-2xl font-bold text-gray-400 uppercase tracking-[0.2em]"><?php echo $window['window_number']; ?></p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="window-status-badge px-3 5xl:px-6 py-1 5xl:py-2 rounded-lg 5xl:rounded-xl text-xs 3xl:text-sm 5xl:text-xl font-black tracking-widest uppercase mb-2 <?php echo $isActive ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-400'; ?>">
                                    <?php echo $isActive ? 'Online' : 'Offline'; ?>
                                </span>
                            </div>
                        </div>

                        <?php 
                        $servingStatus = $window['serving_status'];
                        $isServing = ($isActive && $servingTicket);
                        $containerClasses = $isServing 
                            ? ($servingStatus === 'called' ? 'bg-yellow-400 shadow-inner' : 'bg-secondary-700 shadow-inner') 
                            : 'bg-secondary-200/70';
                        $labelClasses = $isServing ? 'text-black/50' : 'text-secondary-900/40';
                        if ($isServing && $servingStatus === 'serving') $labelClasses = 'text-white/40';
                        
                        $statusText = 'Idle';
                        if (!$isActive) $statusText = 'Offline';
                        elseif ($servingStatus === 'called') $statusText = 'Calling';
                        elseif ($servingStatus === 'serving') $statusText = 'Processing';
                        ?>
                        <div class="serving-container <?php echo $containerClasses; ?> rounded-2xl 3xl:rounded-[32px] 5xl:rounded-[50px] p-3 lg:p-4 3xl:p-6 5xl:p-10 mb-4 5xl:mb-8 border border-transparent transition-all duration-500">
                            <p class="serving-label text-xs 3xl:text-sm 5xl:text-xl font-black <?php echo $labelClasses; ?> uppercase tracking-[0.3em] mb-2 transition-colors"><?php echo $statusText; ?></p>
                            <div class="serving-ticket-display flex items-center justify-between min-h-[3.5rem] 3xl:min-h-[5rem] 5xl:min-h-[8rem]">
                                <?php if ($isServing): ?>
                                    <span class="text-5xl font-black <?php echo $servingStatus === 'called' ? 'text-black' : 'text-white'; ?> font-heading transition-colors"><?php echo $servingTicket; ?></span>
                                    <span class="flex h-5 w-5 3xl:h-6 3xl:w-6 5xl:h-10 5xl:w-10 relative">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full <?php echo $servingStatus === 'called' ? 'bg-black' : 'bg-white'; ?> opacity-40"></span>
                                        <span class="relative inline-flex rounded-full h-5 w-5 3xl:h-6 3xl:w-6 5xl:h-10 5xl:w-10 <?php echo $servingStatus === 'called' ? 'bg-black' : 'bg-white'; ?>"></span>
                                    </span>
                                <?php else: ?>
                                    <span class="text-2xl 3xl:text-4xl 5xl:text-6xl font-black text-secondary-900/40 font-heading transition-colors"><?php echo !$isActive ? 'OFFLINE' : 'IDLE'; ?></span>
                                    <i class="fas <?php echo !$isActive ? 'fa-power-off' : 'fa-moon'; ?> <?php echo !$isActive ? 'text-secondary-300' : 'text-secondary-900/40'; ?> text-2xl 3xl:text-4xl 5xl:text-6xl transition-colors"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Hidden Sync Source for Window Data (Refreshed silently) -->
    <div id="window-sync-source" class="hidden">
        <?php foreach ($allWindows as $w): ?>
            <div class="sync-item" 
                 data-id="<?php echo $w['id']; ?>" 
                 data-active="<?php echo $w['is_active'] ? '1' : '0'; ?>"
                 data-ticket="<?php echo $w['serving_ticket'] ?: ''; ?>"
                 data-status="<?php echo $w['serving_status'] ?: ''; ?>"
                 data-is-priority="<?php echo $w['is_priority'] ?? 0; ?>"
                 data-called-at="<?php echo $w['called_at'] ?: ''; ?>">
            </div>
        <?php endforeach; ?>
    </div>


    <!-- Main Content (Refreshed silently) -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-6 min-h-0" id="main-content-sync">
        <!-- Left Column: Waiting Queue (Large) -->
        <div class="lg:col-span-8 bg-white rounded-2xl shadow-xl shadow-slate-300/50 border border-slate-300 overflow-hidden flex flex-col h-full">
            <div class="px-8 py-8 border-b border-slate-200 bg-white sticky top-0 z-10 flex-none flex items-center justify-between">
                <div>
                    <h3 class="text-2xl 3xl:text-5xl 5xl:text-8xl font-black text-gray-900 font-heading">Waiting Queue</h3>
                    <p class="text-xs 3xl:text-base 5xl:text-3xl font-bold text-slate-400 uppercase tracking-widest mt-1">Real-time List</p>
                </div>

                <!-- Relocated Voice Announcement Toggle -->
                <div class="flex items-center gap-3 bg-slate-50/80 px-4 py-2 rounded-2xl border border-slate-100 shadow-sm" id="voice-toggle-container">
                    <div class="flex flex-col items-end">
                        <span class="text-xs 3xl:text-base 5xl:text-2xl font-black uppercase tracking-widest text-slate-400 leading-none mb-1">Voice System</span>
                        <span id="voice-status-text" class="text-xs 3xl:text-base 5xl:text-2xl font-bold text-slate-300 transition-colors uppercase">Offline</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer group">
                        <input type="checkbox" id="voice-master-toggle" class="sr-only peer">
                        <div class="w-11 h-6 3xl:w-16 3xl:h-8 5xl:w-24 5xl:h-12 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 3xl:after:h-7 3xl:after:w-7 5xl:after:h-10 5xl:after:w-10 after:transition-all peer-checked:bg-emerald-500 transition-colors"></div>
                    </label>
                </div>
            </div>
            <div class="flex-1 overflow-auto p-4 md:p-6 shadow-inner bg-slate-50/30">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 5xl:gap-10">
                    <?php 
                    $waitingTickets = $ticketModel->getWaitingQueue();
                    if (empty($waitingTickets)): ?>
                        <div class="flex flex-col items-center justify-center text-slate-400 py-12 5xl:py-40 text-center">
                            <i class="fas fa-clipboard-list text-4xl 5xl:text-9xl mb-3 5xl:mb-10 opacity-20"></i>
                            <p class="font-bold 5xl:text-4xl text-slate-300 uppercase tracking-widest text-xs">No tickets in queue.</p>
                        </div>
                    <?php else: 
                        foreach ($waitingTickets as $ticket): 
                            $globalPos = $ticketModel->getGlobalQueuePosition($ticket['id']);
                            $estWaitSeconds = $ticketModel->getAdvancedEstimatedWaitTime($ticket['id'], $now);
                            $estWaitFormatted = formatDuration(round($estWaitSeconds));
                    ?>
                    <div class="bg-white rounded-[24px] 5xl:rounded-[48px] p-4 md:p-6 5xl:p-14 flex items-center justify-between hover:bg-slate-50 border <?php echo $ticket['is_priority'] ? 'border-amber-400 ring-2 ring-amber-100 ring-inset' : 'border-slate-200'; ?> shadow-division transition-all duration-300 group relative overflow-hidden">
                        <?php if ($ticket['is_priority']): ?>
                            <div class="absolute top-0 right-0 h-10 w-24 5xl:h-20 5xl:w-48 bg-amber-400 text-white flex items-center justify-center font-black text-[8px] 5xl:text-xl uppercase tracking-[0.2em] transform rotate-12 translate-x-6 -translate-y-2 shadow-lg z-0">
                                Priority
                            </div>
                        <?php endif; ?>
                        <div class="flex items-center space-x-4 md:space-x-8 5xl:space-x-16 min-w-0 flex-1">
                            <div class="w-14 h-14 md:w-20 md:h-20 5xl:w-40 5xl:h-40 bg-slate-900 rounded-2xl md:rounded-3xl 5xl:rounded-[40px] flex items-center justify-center shadow-lg border border-slate-100 group-hover:scale-105 transition-transform shrink-0 relative">
                                <span class="text-white font-black text-xs md:text-xl 5xl:text-5xl tracking-tighter"><?php echo $ticket['service_code']; ?></span>
                                <div class="absolute -top-2 -right-2 bg-primary-600 text-white w-6 h-6 md:w-8 md:h-8 5xl:w-16 5xl:h-16 rounded-full flex items-center justify-center text-xs 5xl:text-2xl font-black shadow-lg border-2 border-white">
                                    <?php echo $globalPos; ?>
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center space-x-3 mb-1 5xl:mb-4">
                                    <p class="font-black text-gray-900 text-lg md:text-2xl 5xl:text-6xl leading-none tracking-tight"><?php echo $ticket['ticket_number']; ?></p>
                                    <span class="bg-slate-100 text-slate-600 px-2 py-0.5 5xl:px-6 5xl:py-2 rounded-md 5xl:rounded-xl text-[11px] 5xl:text-xl font-black uppercase tracking-widest"><?php echo $ticket['service_code']; ?></span>
                                    <?php if ($ticket['is_priority']): ?>
                                        <i class="fas fa-star text-amber-500 text-xs md:text-sm 5xl:text-3xl animate-pulse"></i>
                                    <?php endif; ?>
                                </div>
                                <p class="text-xs md:text-sm 5xl:text-3xl font-bold text-slate-400 uppercase tracking-wider truncate"><?php echo $ticket['service_name']; ?></p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end shrink-0 ml-4 group-hover:translate-x-1 transition-transform">
                            <div class="text-xs md:text-sm 5xl:text-3xl font-black text-primary-700 bg-primary-50 px-3 py-1 5xl:px-8 5xl:py-3 rounded-full border border-primary-100 shadow-sm leading-none mb-1 md:mb-2 italic"
                                 data-live-countdown="1"
                                 data-ticket-id="<?php echo $ticket['id']; ?>"
                                 data-target-timestamp="<?php echo (int)round(($now + $estWaitSeconds) * 1000); ?>"
                                 data-server-now="<?php echo $now * 1000; ?>">
                                <?php echo $estWaitFormatted; ?>
                            </div>
                            <p class="text-[11px] 5xl:text-xl font-black text-slate-300 uppercase tracking-widest leading-none">Sequence Pos</p>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column: Analytics Grid (2x2) -->
        <div class="lg:col-span-4 grid grid-rows-2 gap-4 h-full" id="analytics-sync">
            <div class="grid grid-cols-2 gap-4 5xl:gap-12 h-full">
                <div class="bg-white rounded-2xl 5xl:rounded-[48px] p-6 5xl:p-14 shadow-xl shadow-slate-300/50 border border-slate-300 flex flex-col items-center justify-center h-full text-center overflow-hidden">
                    <div class="text-slate-300 mb-2 5xl:mb-6"><i class="fas fa-ticket-alt text-2xl 3xl:text-4xl 5xl:text-7xl"></i></div>
                    <div class="w-full">
                        <h3 class="text-xs 3xl:text-sm 5xl:text-2xl font-black uppercase tracking-widest text-slate-400 mb-1 5xl:mb-4">Total Tickets</h3>
                        <p class="text-4xl 3xl:text-5xl 5xl:text-[6rem] font-black text-gray-900 leading-none truncate"><?php echo $stats['total'] ?? 0; ?></p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl 5xl:rounded-[48px] p-6 5xl:p-14 shadow-xl shadow-slate-300/50 border border-slate-300 flex flex-col items-center justify-center h-full text-center overflow-hidden">
                    <div class="text-slate-300 mb-2 5xl:mb-6"><i class="fas fa-clock text-2xl 3xl:text-4xl 5xl:text-7xl"></i></div>
                    <div class="w-full">
                        <h3 class="text-xs 3xl:text-sm 5xl:text-2xl font-black uppercase tracking-widest text-slate-400 mb-1 5xl:mb-4">Waiting</h3>
                        <p class="text-4xl 3xl:text-5xl 5xl:text-[6rem] font-black text-gray-900 leading-none truncate"><?php echo $stats['waiting'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 5xl:gap-12 h-full">
                <div class="bg-white rounded-2xl 5xl:rounded-[48px] p-6 5xl:p-16 shadow-xl shadow-slate-300/50 border border-slate-300 flex flex-col items-center justify-center h-full text-center">
                    <div class="text-slate-300 mb-2 5xl:mb-8">
                        <i class="fas fa-desktop text-2xl 3xl:text-4xl 5xl:text-7xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xs 3xl:text-sm 5xl:text-3xl font-black uppercase tracking-widest text-slate-400 mb-1 5xl:mb-4">Active Windows</h3>
                        <p class="text-3xl 3xl:text-5xl 5xl:text-8xl font-black text-gray-900 leading-none">
                            <?php echo count($activeWindows); ?>
                        </p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl 5xl:rounded-[48px] p-6 5xl:p-16 shadow-xl shadow-slate-300/50 border border-slate-300 flex flex-col items-center justify-center h-full text-center">
                    <div class="text-slate-300 mb-2 5xl:mb-8"><i class="fas fa-chart-line text-2xl 3xl:text-4xl 5xl:text-7xl"></i></div>
                    <div>
                        <h3 class="text-xs 3xl:text-sm 5xl:text-3xl font-black uppercase tracking-widest text-slate-400 mb-1 5xl:mb-4">Peak Hour</h3>
                        <p class="text-3xl 3xl:text-5xl 5xl:text-8xl font-black text-gray-900 leading-none"><?php echo $ticketModel->getPeakHour(); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // --- Voice Announcement System ---
    class VoiceAnnouncer {
        constructor() {
            this.synth = window.speechSynthesis;
            this.lastAnnouncedTickets = new Set();
            this.isInitialized = false;
            // Robust storage retrieval
            const savedState = localStorage.getItem('voice_enabled');
            this.isEnabled = savedState !== null ? savedState === 'true' : true; 
            this.isUnlocked = false;
            
            console.log('VoiceAnnouncer: State loaded from storage', { isEnabled: this.isEnabled });
            this.init();
        }

        init() {
            try {
                const toggle = document.getElementById('voice-master-toggle');
                if (toggle) {
                    toggle.checked = this.isEnabled;
                    this.updateUI();

                    toggle.onchange = () => {
                        this.isEnabled = toggle.checked;
                        localStorage.setItem('voice_enabled', this.isEnabled);
                        console.log('VoiceAnnouncer: State saved to storage', { isEnabled: this.isEnabled });
                        this.updateUI();
                        
                        if (this.isEnabled && !this.isUnlocked) {
                            this.unlock();
                        }
                    };
                }

                // Interaction required for most browsers to play audio
                const autoUnlock = () => {
                    if (this.isEnabled && !this.isUnlocked) {
                        this.unlock();
                    }
                    document.removeEventListener('mousedown', autoUnlock);
                    document.removeEventListener('touchstart', autoUnlock);
                };
                document.addEventListener('mousedown', autoUnlock);
                document.addEventListener('touchstart', autoUnlock);
                
                this.isInitialized = true;
                
                // If browser already has voices loaded, update UI
                if (this.synth && this.synth.getVoices().length > 0) {
                    this.updateUI();
                } else if (this.synth) {
                    this.synth.onvoiceschanged = () => this.updateUI();
                }
            } catch (e) {
                console.error('VoiceAnnouncer: Initialization failed', e);
            }
        }

        unlock() {
            try {
                const utterance = new SpeechSynthesisUtterance('');
                utterance.volume = 0;
                this.synth.speak(utterance);
                this.isUnlocked = true;
                this.updateUI();
                console.log('Speech engine unlocked');
            } catch (e) {
                console.error('Speech unlock failed', e);
            }
        }

        updateUI() {
            const statusText = document.getElementById('voice-status-text');
            if (!statusText) return;

            if (!this.isEnabled) {
                statusText.innerText = 'Disabled';
                statusText.className = 'text-xs 5xl:text-2xl font-bold text-slate-300 transition-colors uppercase';
            } else if (!this.isUnlocked) {
                statusText.innerText = 'Syncing...';
                statusText.className = 'text-xs 5xl:text-2xl font-bold text-yellow-500 transition-colors uppercase animate-pulse';
            } else {
                statusText.innerText = 'Online';
                statusText.className = 'text-xs 5xl:text-2xl font-bold text-emerald-500 transition-colors uppercase';
            }
        }

        speak(ticketNumber, calledAt = '', isPriority = false) {
            if (!this.isEnabled) return;
            
            // Create a unique key for this specific call (Number + Timestamp)
            // This allows re-calling the same ticket number
            const callKey = `${ticketNumber}|${calledAt}`;
            
            console.log('Announcing ticket:', callKey, 'Priority:', isPriority);
            if (!this.synth || !this.isInitialized) return;
            
            // Avoid repeat announcements for the EXACT same call time
            if (this.lastAnnouncedTickets.has(callKey)) return;
            this.lastAnnouncedTickets.add(callKey);
            
            // Keep the set size manageable
            if (this.lastAnnouncedTickets.size > 50) {
                const firstElement = this.lastAnnouncedTickets.values().next().value;
                this.lastAnnouncedTickets.delete(firstElement);
            }

            let prefix = ticketNumber;
            let counter = '';
            
            if (ticketNumber.includes('-')) {
                const parts = ticketNumber.split('-');
                prefix = parts[0];
                counter = parts[1];
            }

            const speakPart = (text, rate = 0.7) => {
                // Explicitly use "zero" for '0' to avoid it being read as letter "O"
                const clearText = text.split(' ').map(c => c === '0' ? 'zero' : c).join(' ');
                console.log('Speaking part:', clearText);
                
                const utterance = new SpeechSynthesisUtterance(clearText);
                utterance.rate = rate;
                utterance.pitch = 1.0;
                
                const voices = this.synth.getVoices();
                if (voices.length > 0) {
                    const preferredVoice = voices.find(v => v.lang.includes('en-US')) || voices[0];
                    utterance.voice = preferredVoice;
                }
                this.synth.speak(utterance);
            };

            this.synth.resume();
            this.synth.cancel();

            // Part 1: Intro (Slower rate as requested)
            const intro = isPriority ? "Calling priority ticket number" : "Calling ticket number";
            speakPart(intro, 0.6);

            // Part 2: [Prefix] after 2.4s delay (Intro is slower, so prefix starts later)
            setTimeout(() => {
                speakPart(prefix.split('').join(' '));
            }, 2400);

            // Part 3: [Counter] after 4.0s total delay (Ensures Prefix is finished)
            if (counter) {
                setTimeout(() => {
                    speakPart(counter.split('').join(' '));
                }, 4000);
            }
        }

        scanForCalledTickets() {
            const syncSource = document.getElementById('window-sync-source');
            if (!syncSource) return;

            const syncItems = syncSource.querySelectorAll('.sync-item');
            syncItems.forEach(item => {
                const status = item.dataset.status;
                const ticket = item.dataset.ticket;
                const calledAt = item.dataset.calledAt || '';
                const isPriority = item.dataset.isPriority === '1';
                
                if (status === 'called' && ticket) {
                    this.speak(ticket, calledAt, isPriority);
                }
            });
        }
    }

    const announcer = new VoiceAnnouncer();

    // Constants for Water-like flow
    const PIXELS_PER_SECOND = 90; // Constant speed
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
            const isActive = item.dataset.active === '1';
            const ticket = item.dataset.ticket;
            const isPriority = item.dataset.isPriority === '1';
            
            // Find ALL slides for this window ID (because we triple them)
            const slides = track.querySelectorAll(`.window-slide[data-window-id="${id}"]`);
            
            slides.forEach(slide => {
                const badge = slide.querySelector('.window-status-badge');
                const display = slide.querySelector('.serving-ticket-display');
                const card = slide.querySelector('.window-card');
                
                // Update Status Badge
                if (badge) {
                    badge.className = `window-status-badge px-3 5xl:px-6 py-1 5xl:py-2 rounded-lg 5xl:rounded-xl text-xs 3xl:text-sm 5xl:text-xl font-black tracking-widest uppercase mb-2 ${isActive ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-400'}`;
                    badge.textContent = isActive ? 'Online' : 'Offline';
                }

                if (card) {
                    const baseClasses = "window-card rounded-[32px] lg:rounded-[48px] 5xl:rounded-[80px] p-6 lg:p-8 3xl:p-12 5xl:p-20 border transition-all duration-300 group/card overflow-hidden flex flex-col h-full";
                    if (!isActive) {
                        card.className = `${baseClasses} bg-white/50 border-gray-200 grayscale opacity-40`;
                    if (ticket) {
                        const priorityClasses = isPriority ? 'border-amber-400 ring-4 ring-amber-100 ring-inset brightness-110' : 'border-indigo-300 ring-4 ring-indigo-100/30';
                        card.className = `${baseClasses} bg-white shadow-premium opacity-100 -translate-y-1 ${priorityClasses}`;
                    } else {
                        card.className = `${baseClasses} bg-white shadow-division border-gray-50 opacity-90 hover:opacity-100 transition-opacity hover:shadow-premium hover:-translate-y-1`;
                    }
                }

                if (display) {
                    const servingContainer = slide.querySelector('.serving-container');
                    const servingLabel = slide.querySelector('.serving-label');
                    const status = item.dataset.status;
                    const isServing = isActive && ticket;

                    if (servingContainer) {
                        const baseContainer = "serving-container rounded-2xl 3xl:rounded-[32px] 5xl:rounded-[50px] p-3 lg:p-4 3xl:p-6 5xl:p-10 mb-4 5xl:mb-8 border border-transparent transition-all duration-500";
                        if (!isServing) {
                            servingContainer.className = `${baseContainer} bg-secondary-200/70`;
                        } else {
                            if (status === 'called') {
                                servingContainer.className = isPriority 
                                    ? `${baseContainer} bg-amber-400 shadow-inner`
                                    : `${baseContainer} bg-yellow-400 shadow-inner`;
                            } else {
                                servingContainer.className = `${baseContainer} bg-secondary-700 shadow-inner`;
                            }
                        }
                    }

                    if (servingLabel) {
                        let labelColor = isServing ? 'text-black/50' : 'text-secondary-900/40';
                        if (isServing && status === 'serving') labelColor = 'text-white/40';
                        servingLabel.className = `serving-label text-xs 3xl:text-sm 5xl:text-xl font-black uppercase tracking-[0.3em] mb-2 transition-colors ${labelColor}`;
                        
                        let statusText = 'Idle';
                        if (!isActive) statusText = 'Offline';
                        else if (status === 'called') statusText = 'Calling';
                        else if (status === 'serving') statusText = 'Processing';
                        servingLabel.textContent = statusText;
                    }

                    let html = '';
                    if (isServing) {
                        const textColor = status === 'called' ? 'text-black' : 'text-white';
                        const dotColor = status === 'called' ? 'bg-black' : 'bg-white';
                        html = `
                            <span class="text-5xl font-black ${textColor} font-heading transition-colors">${ticket}</span>
                            <span class="flex h-5 w-5 3xl:h-6 3xl:w-6 5xl:h-10 5xl:w-10 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full ${dotColor} opacity-40"></span>
                                <span class="relative inline-flex rounded-full h-5 w-5 3xl:h-6 3xl:w-6 5xl:h-10 5xl:w-10 ${dotColor}"></span>
                            </span>`;
                    } else {
                        html = `
                            <span class="text-2xl 3xl:text-4xl 5xl:text-6xl font-black text-secondary-900/40 font-heading transition-colors">${!isActive ? 'OFFLINE' : 'IDLE'}</span>
                            <i class="fas ${!isActive ? 'fa-power-off' : 'fa-moon'} ${!isActive ? 'text-secondary-300' : 'text-secondary-900/40'} text-2xl 3xl:text-4xl 5xl:text-6xl transition-colors"></i>`;
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
        // Mathematics: (Total Track scrollable width + 1 gap) / 3 sets = perfectly repeating loop distance
        carouselState.totalWidth = (track.scrollWidth + gap) / 3;
    }

    function initWindowCarousel() {
        const track = document.getElementById('window-carousel-track');
        const viewport = document.getElementById('window-carousel-viewport');
        if (!track || !viewport) return;

        // Add visual cues for interactivity
        viewport.classList.add('cursor-grab', 'active:cursor-grabbing');

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
        
        // Wait for next frame for DOM layout to settle
        requestAnimationFrame(() => {
            calculateMetrics();
            startAnimation();
        });

        window.addEventListener('resize', calculateMetrics);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) carouselState.lastTimestamp = null; // Prevent jump on tab return
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

            // Cap delta to 100ms to prevent massive jumps on browser throttle/stutter
            const cappedDelta = Math.min(delta, 0.1); 

            if (!carouselState.isPaused && carouselState.totalWidth > 0) {
                carouselState.scrollPos += PIXELS_PER_SECOND * cappedDelta;
                
                // Use a loop for reset to handle extreme cases (though cappedDelta prevents them)
                while (carouselState.scrollPos >= carouselState.totalWidth) {
                    carouselState.scrollPos -= carouselState.totalWidth;
                }
                
                track.style.transform = `translate3d(-${carouselState.scrollPos}px, 0, 0)`;
            }
            
            carouselState.animationId = requestAnimationFrame(animate);
        }
        
        carouselState.animationId = requestAnimationFrame(animate);
    }

    new DashboardRefresh(['main-content-sync', 'analytics-sync', 'window-sync-source'], 3000);

    document.addEventListener('dashboard:updated', (e) => {
        if (e.detail.id === 'window-sync-source') {
            syncCarouselData();
            announcer.scanForCalledTickets();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        initWindowCarousel();
        // Initial scan
        announcer.scanForCalledTickets();
    });
</script>
<script src="<?php echo BASE_URL; ?>/js/live-countdown.js"></script>

<?php include __DIR__ . '/../../includes/admin-layout-footer.php'; ?>

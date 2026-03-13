<!-- Modern User Sidebar Navigation -->
<?php 
$isDashboard = str_contains($_SERVER['PHP_SELF'], 'dashboard.php');
$isGetTicket = str_contains($_SERVER['PHP_SELF'], 'get-ticket.php');
$isMyTicket = str_contains($_SERVER['PHP_SELF'], 'my-ticket.php');
$isAnnouncements = str_contains($_SERVER['PHP_SELF'], 'announcements.php');
$isHistory = str_contains($_SERVER['PHP_SELF'], 'history.php');
$isProfile = str_contains($_SERVER['PHP_SELF'], 'profile.php');

// Get unread announcements count
// Get unread announcements count
$unreadAnnouncements = 0;
if (isset($_SESSION['user_id'])) {
    if (!isset($announcementModel)) {
        require_once __DIR__ . '/../models/Announcement.php';
        $announcementModel = new Announcement();
    }
    $unreadAnnouncements = $announcementModel->getUnreadCount($_SESSION['user_id']);
}
?>

<style>
    .user-sidebar-active { background: rgba(16, 185, 129, 0.1); border-left: 4px solid #10b981; }
    .glass-sidebar-user { background: rgba(15, 23, 42, 0.98); backdrop-filter: blur(10px); }
</style>

<!-- Mobile Overlay -->
<div id="user-sidebar-overlay" onclick="toggleUserSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden transition-opacity lg:hidden"></div>

<!-- Sidebar -->
<aside id="user-sidebar" class="fixed inset-y-0 left-0 w-72 glass-sidebar-user text-white z-50 flex flex-col transition-all duration-300 -translate-x-full lg:translate-x-0 shadow-2xl border-r border-white/5">
    <div class="p-8 border-b border-white/10">
        <a href="dashboard.php" class="flex items-center space-x-3 group text-left">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-lg group-hover:rotate-6 transition-all duration-300 p-1.5 flex-shrink-0">
                <img src="<?php echo BASE_URL; ?>/img/logo.png" alt="Logo" class="w-full h-full object-contain">
            </div>
            <div class="min-w-0">
                <h1 class="text-xl font-black font-heading tracking-tighter leading-none truncate"><?php echo APP_NAME; ?></h1>
                <p class="text-[10px] font-black uppercase tracking-widest text-primary-400 mt-1">User Dashboard</p>
            </div>
        </a>
    </div>

    <nav class="flex-1 px-4 py-8 space-y-2 overflow-y-auto">
        <div class="px-6 mb-4">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-500">Navigation</p>
        </div>

        <a href="dashboard.php" class="flex items-center px-6 py-4 rounded-xl text-sm font-bold tracking-tight transition-all duration-300 <?php echo $isDashboard ? 'bg-primary-600/20 text-white border-l-4 border-primary-500 pl-5' : 'text-gray-400 hover:bg-white/5 hover:text-white' ?>">
            <i class="fas fa-desktop mr-4 text-lg <?php echo $isDashboard ? 'text-primary-500' : 'opacity-70'; ?>"></i>Live Queue
        </a>
        
        <a href="get-ticket.php" class="flex items-center px-6 py-4 rounded-xl text-sm font-bold tracking-tight transition-all duration-300 <?php echo $isGetTicket ? 'bg-primary-600/20 text-white border-l-4 border-primary-500 pl-5' : 'text-gray-400 hover:bg-white/5 hover:text-white' ?>">
            <i class="fas fa-ticket-alt mr-4 text-lg <?php echo $isGetTicket ? 'text-primary-500' : 'opacity-70'; ?>"></i>Get Ticket
        </a>

        <a href="my-ticket.php" class="flex items-center px-6 py-4 rounded-xl text-sm font-bold tracking-tight transition-all duration-300 <?php echo $isMyTicket ? 'bg-primary-600/20 text-white border-l-4 border-primary-500 pl-5' : 'text-gray-400 hover:bg-white/5 hover:text-white' ?>">
            <i class="fas fa-user-tag mr-4 text-lg <?php echo $isMyTicket ? 'text-primary-500' : 'opacity-70'; ?>"></i>My Ticket
        </a>

        <div class="px-6 mb-4 pt-6">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-500">General</p>
        </div>

        <a href="announcements.php" class="flex items-center px-6 py-4 rounded-xl text-sm font-bold tracking-tight transition-all duration-300 <?php echo $isAnnouncements ? 'bg-primary-600/20 text-white border-l-4 border-primary-500 pl-5' : 'text-gray-400 hover:bg-white/5 hover:text-white' ?>">
            <div class="flex items-center flex-1">
                <i class="fas fa-newspaper mr-4 text-lg <?php echo $isAnnouncements ? 'text-primary-500' : 'opacity-70'; ?>"></i>
                <span>Announcements</span>
            </div>
            <?php if ($unreadAnnouncements > 0): ?>
                <span class="px-2 py-0.5 bg-red-500 text-white text-[10px] font-black rounded-lg ring-2 ring-white/10 animate-pulse">NEW</span>
            <?php endif; ?>
        </a>

        <a href="history.php" class="flex items-center px-6 py-4 rounded-xl text-sm font-bold tracking-tight transition-all duration-300 <?php echo $isHistory ? 'bg-primary-600/20 text-white border-l-4 border-primary-500 pl-5' : 'text-gray-400 hover:bg-white/5 hover:text-white' ?>">
            <i class="fas fa-history mr-4 text-lg <?php echo $isHistory ? 'text-primary-500' : 'opacity-70'; ?>"></i>History
        </a>

        <div class="pt-8 px-6 mb-4">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-500">Account</p>
        </div>

        <a href="profile.php" class="flex items-center px-6 py-4 rounded-xl text-sm font-bold tracking-tight transition-all duration-300 <?php echo $isProfile ? 'bg-primary-600/20 text-white border-l-4 border-primary-500 pl-5' : 'text-gray-400 hover:bg-white/5 hover:text-white' ?>">
            <i class="fas fa-user-circle mr-4 text-lg <?php echo $isProfile ? 'text-primary-500' : 'opacity-70'; ?>"></i>Profile Settings
        </a>

        <a href="../logout.php" class="flex items-center px-6 py-4 rounded-xl text-sm font-black text-rose-400 hover:bg-rose-500/10 transition-all">
            <i class="fas fa-power-off mr-4 text-lg"></i>Logout
        </a>
    </nav>

</aside>


<!-- Top Header for User -->
<header id="user-header" class="fixed top-0 right-0 left-0 lg:left-72 h-20 bg-white/80 backdrop-blur-xl z-40 border-b border-gray-100 px-4 md:px-8 flex items-center justify-between shadow-sm transition-all duration-300">
    <div class="flex items-center">
        <button onclick="toggleUserSidebar()" class="w-12 h-12 rounded-2xl bg-primary-50 flex items-center justify-center text-primary-600 hover:bg-primary-100 transition-all mr-6 shadow-sm border border-primary-100/50 group">
            <i class="fas fa-bars text-xl group-hover:scale-110 transition-transform"></i>
        </button>
        <div class="hidden md:block">
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-primary-600 leading-none mb-1">E-Queue System</p>
            <h2 class="text-lg font-black text-gray-900 tracking-tight">
                <?php 
                if ($isDashboard) echo 'Live Window Queue';
                elseif ($isGetTicket) echo 'Get New Ticket';
                elseif ($isMyTicket) echo 'My Active Ticket';
                elseif ($isAnnouncements) echo 'Campus Announcements';
                elseif ($isHistory) echo 'Transaction History';
                else echo 'Welcome';
                ?>
            </h2>
        </div>
    </div>

    <!-- Profile & Quick Actions -->
    <div class="flex items-center space-x-4 md:space-x-6">
        <div class="hidden sm:flex flex-col text-right">
            <span class="text-xs font-black text-gray-900 leading-none"><?php echo explode(' ', $_SESSION['full_name'] ?? 'User')[0]; ?></span>
            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">Visitor</span>
        </div>
        <div class="relative group">
            <img class="w-10 h-10 rounded-xl shadow-lg border-2 border-white ring-1 ring-gray-100 group-hover:scale-105 transition-transform cursor-pointer" 
                 src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['full_name'] ?? 'User'); ?>&background=10b981&color=fff" 
                 alt="user photo">
        </div>
    </div>
</header>

<script>
    function toggleUserSidebar() {
        const sidebar = document.getElementById('user-sidebar');
        const header = document.getElementById('user-header');
        const overlay = document.getElementById('user-sidebar-overlay');
        const mainWrapper = document.getElementById('user-main-wrapper');
        
        if (window.innerWidth < 1024) {
            // Mobile Toggle
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        } else {
            // Desktop Toggle
            const isOpen = sidebar.classList.contains('lg:translate-x-0');
            if (isOpen) {
                sidebar.classList.remove('lg:translate-x-0');
                sidebar.classList.add('-translate-x-full');
                if (header) header.classList.remove('lg:left-72');
                if (mainWrapper) mainWrapper.classList.remove('lg:ml-72');
            } else {
                sidebar.classList.add('lg:translate-x-0');
                sidebar.classList.remove('-translate-x-full');
                header.classList.add('lg:left-72');
                if (mainWrapper) mainWrapper.classList.add('lg:ml-72');
            }
        }
    }

    // Auto-collapse on desktop if needed
    document.addEventListener('DOMContentLoaded', () => {
        // Default sidebar behavior
    });
</script>

<!-- Modern Staff Sidebar Navigation -->
<?php 
$isDashboard = str_contains($_SERVER['PHP_SELF'], 'dashboard.php');
$isServices = str_contains($_SERVER['PHP_SELF'], 'services.php');
$isArchived = str_contains($_SERVER['PHP_SELF'], 'archived.php');

// Get archived count for the active window
$archivedCount = 0;
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'staff') {
    if (!isset($ticketModel)) {
        require_once __DIR__ . '/../models/Ticket.php';
        $ticketModel = new Ticket();
    }
    if (!isset($windowModel)) {
        require_once __DIR__ . '/../models/Window.php';
        $windowModel = new Window();
    }
    $activeStaffWindow = $windowModel->getWindowByStaff($_SESSION['user_id']);
    if ($activeStaffWindow) {
        $archivedCount = count($ticketModel->getArchivedTicketsByWindow($activeStaffWindow['id']));
    }
}
?>

<style>
    .staff-sidebar-active { background: rgba(225, 29, 72, 0.1); border-left: 4px solid #e11d48; }
    .glass-sidebar-staff { background: rgba(15, 23, 42, 0.98); backdrop-filter: blur(10px); }
</style>

<!-- Mobile Overlay -->
<div id="sidebar-overlay" onclick="toggleStaffSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden transition-opacity lg:hidden"></div>

<!-- Sidebar -->
<aside id="staff-sidebar" class="fixed inset-y-0 left-0 w-72 glass-sidebar-staff text-white z-50 flex flex-col transition-all duration-300 -translate-x-full lg:translate-x-0 shadow-2xl border-r border-white/5">
    <div class="p-8 border-b border-white/10">
        <a href="dashboard.php" class="flex items-center space-x-3 group">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-lg group-hover:rotate-6 transition-all duration-300 p-1.5 flex-shrink-0">
                <img src="<?php echo BASE_URL; ?>/img/logo.png" alt="Logo" class="w-full h-full object-contain">
            </div>
            <div class="min-w-0">
                <h1 class="text-xl font-black font-heading tracking-tighter leading-none truncate"><?php echo APP_NAME; ?></h1>
                <p class="text-[10px] font-black uppercase tracking-widest text-secondary-400 mt-1">Staff Counter</p>
            </div>
        </a>
    </div>

    <nav class="flex-1 px-4 py-8 space-y-2 overflow-y-auto">
        <div class="px-6 mb-4">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-500">Operation</p>
        </div>

        <a href="dashboard.php" class="flex items-center px-6 py-4 rounded-xl text-sm font-bold tracking-tight transition-all duration-300 <?php echo $isDashboard ? 'bg-secondary-600/20 text-white border-l-4 border-secondary-500 pl-5' : 'text-gray-400 hover:bg-white/5 hover:text-white' ?>">
            <i class="fas fa-desktop mr-4 text-lg <?php echo $isDashboard ? 'text-secondary-500' : 'opacity-70'; ?>"></i>Live Counter
        </a>
        
        <a href="services.php" class="flex items-center px-6 py-4 rounded-xl text-sm font-bold tracking-tight transition-all duration-300 <?php echo $isServices ? 'bg-secondary-600/20 text-white border-l-4 border-secondary-500 pl-5' : 'text-gray-400 hover:bg-white/5 hover:text-white' ?>">
            <i class="fas fa-clipboard-list mr-4 text-lg <?php echo $isServices ? 'text-secondary-500' : 'opacity-70'; ?>"></i>Service Control
        </a>

        <a href="archived.php" class="flex items-center px-6 py-4 rounded-xl text-sm font-bold tracking-tight transition-all duration-300 <?php echo $isArchived ? 'bg-secondary-600/20 text-white border-l-4 border-secondary-500 pl-5' : 'text-gray-400 hover:bg-white/5 hover:text-white' ?>">
            <div class="flex items-center flex-1">
                <i class="fas fa-archive mr-4 text-lg <?php echo $isArchived ? 'text-secondary-500' : 'opacity-70'; ?>"></i>
                <span>Archives</span>
            </div>
            <?php if ($archivedCount > 0): ?>
                <span class="px-2 py-0.5 bg-secondary-500 text-white text-[10px] font-black rounded-lg ring-2 ring-white/10 animate-pulse"><?php echo $archivedCount; ?></span>
            <?php endif; ?>
        </a>

        <div class="pt-8 px-6 mb-4">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-500">Account</p>
        </div>

        <a href="../logout.php" class="flex items-center px-6 py-4 rounded-xl text-sm font-black text-rose-400 hover:bg-rose-500/10 transition-all">
            <i class="fas fa-power-off mr-4 text-lg"></i>Logout
        </a>
    </nav>


</aside>

<!-- Top Header for Staff -->
<header id="staff-header" class="fixed top-0 right-0 left-0 lg:left-72 h-20 bg-white/80 backdrop-blur-xl z-40 border-b border-gray-100 px-4 md:px-8 flex items-center justify-between shadow-sm transition-all duration-300">
    <div class="flex items-center">
        <button onclick="toggleStaffSidebar()" class="w-12 h-12 rounded-2xl bg-secondary-50 flex items-center justify-center text-secondary-600 hover:bg-secondary-100 transition-all mr-6 shadow-sm border border-secondary-100/50 group">
            <i class="fas fa-bars text-xl group-hover:scale-110 transition-transform"></i>
        </button>
        <div class="hidden md:block">
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-secondary-600 leading-none mb-1">Staff Operations</p>
            <h2 class="text-lg font-black text-gray-900 tracking-tight"><?php echo $isDashboard ? 'Live Dashboard' : ($isServices ? 'Service Control' : 'Archived Tickets'); ?></h2>
        </div>
    </div>

    <!-- Profile & Quick Actions -->
    <div class="flex items-center space-x-4 md:space-x-6">
        <div class="hidden sm:flex flex-col text-right">
            <span class="text-xs font-black text-gray-900 leading-none"><?php echo $_SESSION['full_name'] ?? 'Staff Member'; ?></span>
            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">Counter Personnel</span>
        </div>
        <div class="relative group">
            <img class="w-10 h-10 rounded-xl shadow-lg border-2 border-white ring-1 ring-gray-100 group-hover:scale-105 transition-transform cursor-pointer" 
                 src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['full_name'] ?? 'Staff'); ?>&background=e11d48&color=fff" 
                 alt="staff photo">
        </div>
    </div>
</header>

<script>
    function toggleStaffSidebar() {
        const sidebar = document.getElementById('staff-sidebar');
        const header = document.getElementById('staff-header');
        const overlay = document.getElementById('sidebar-overlay');
        const mainWrapper = document.getElementById('staff-main-wrapper');
        
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
</script>

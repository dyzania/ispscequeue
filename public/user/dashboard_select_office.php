<?php
require_once __DIR__ . '/../../models/Office.php';

$officeModel = new Office();
$offices = $officeModel->getAllOffices();

$db = Database::getInstance()->getConnection();
foreach ($offices as &$office) {
    // get active windows
    $stmt = $db->prepare("SELECT COUNT(*) FROM windows WHERE office_id = ? AND is_active = 1");
    $stmt->execute([$office['id']]);
    $office['active_windows'] = $stmt->fetchColumn();
    
    // get waiting tickets
    $stmt = $db->prepare("SELECT COUNT(*) FROM tickets WHERE office_id = ? AND status = 'waiting' AND DATE(created_at) = CURDATE()");
    $stmt->execute([$office['id']]);
    $office['waiting'] = $stmt->fetchColumn();
}
unset($office); // break reference

$pageTitle = 'Select Office';
require_once __DIR__ . '/../../includes/user-layout-header.php';
?>

<style>
    .grid-bg {
        background-color: #0f172a;
        background-image: 
            radial-gradient(at 40% 20%, hsla(110,100%,74%,0.15) 0px, transparent 50%),
            radial-gradient(at 80% 0%, hsla(189,100%,56%,0.15) 0px, transparent 50%),
            radial-gradient(at 0% 50%, hsla(355,100%,93%,0.1) 0px, transparent 50%);
    }
    .office-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .office-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(34, 197, 94, 0.1), 0 10px 10px -5px rgba(34, 197, 94, 0.04);
        border-color: rgba(34, 197, 94, 0.5);
    }
    /* Override body for this page */
    body { background-color: #0f172a !important; }
</style>

<div class="grid-bg min-h-[calc(100vh-8rem)] flex flex-col items-center justify-center p-6 lg:p-12 transition-all duration-300 rounded-[32px] mx-4 md:mx-10 overflow-hidden relative shadow-2xl border border-white/5">
        <div class="max-w-5xl w-full relative z-10">
            <!-- Header -->
            <div class="text-center mb-16">
                <!--
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-primary-900/50 backdrop-blur-xl border border-primary-500/30 mb-8 shadow-2xl">
                    <img src="../img/logo.png" alt="ISPSC Logo" class="w-12 h-12 object-contain">
                </div>
                -->
                <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight">Select Destination Office</h1>
                <p class="text-xl text-primary-200/80 font-medium">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>. Where would you like to queue today?</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-500/10 border border-red-500/50 text-red-400 p-4 rounded-xl mb-8 flex items-center shadow-lg backdrop-blur-sm">
                    <i class="fas fa-exclamation-circle text-xl mr-3"></i>
                    <p class="font-medium"><?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <!-- Office Selection Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($offices as $index => $office): ?>
                    <form method="POST" action="dashboard.php" class="h-full">
                        <input type="hidden" name="office_id" value="<?php echo $office['id']; ?>">
                        <button type="submit" class="w-full h-full text-left office-card bg-primary-900/40 backdrop-blur-xl border border-primary-500/20 rounded-3xl p-8 relative overflow-hidden group">
                            
                            <!-- Decorative background element -->
                            <div class="absolute -right-12 -top-12 w-40 h-40 bg-gradient-to-br from-primary-500/20 to-transparent rounded-full blur-2xl group-hover:bg-primary-500/30 transition-all duration-500"></div>
                            
                            <div class="relative z-10 flex flex-col h-full">
                                <div class="w-14 h-14 rounded-2xl bg-primary-800 border border-primary-500/30 flex items-center justify-center mb-6 text-2xl text-primary-400 group-hover:text-white group-hover:bg-primary-500 transition-colors">
                                    <?php 
                                        // Assign an icon based on the office name (fallback to building)
                                        $icon = 'fa-building';
                                        if (stripos($office['name'], 'registrar') !== false) $icon = 'fa-file-signature';
                                        if (stripos($office['name'], 'accounting') !== false || stripos($office['name'], 'cashier') !== false) $icon = 'fa-coins';
                                        if (stripos($office['name'], 'admission') !== false) $icon = 'fa-user-graduate';
                                        if (stripos($office['name'], 'clinic') !== false) $icon = 'fa-notes-medical';
                                    ?>
                                    <i class="fas <?php echo $icon; ?>"></i>
                                </div>
                                
                                <h3 class="text-2xl font-bold text-white mb-3 tracking-tight"><?php echo htmlspecialchars($office['name']); ?></h3>
                                
                                <p class="text-primary-200/60 leading-relaxed mb-6 flex-grow">
                                    <?php echo htmlspecialchars($office['description'] ?? 'Queue for ' . $office['name'] . ' services.'); ?>
                                </p>
                                
                                <div class="flex gap-4 mb-6">
                                    <div class="flex flex-col bg-white/5 p-3 rounded-xl flex-1 border border-white/5">
                                        <span class="text-[10px] text-primary-300/70 font-bold uppercase tracking-widest mb-1">Open Windows</span>
                                        <span class="text-lg font-black text-white"><?php echo $office['active_windows']; ?></span>
                                    </div>
                                    <div class="flex flex-col bg-white/5 p-3 rounded-xl flex-1 border border-white/5">
                                        <span class="text-[10px] text-primary-300/70 font-bold uppercase tracking-widest mb-1">Queue Waitlist</span>
                                        <span class="text-lg font-black text-white"><?php echo $office['waiting']; ?></span>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </form>
                <?php endforeach; ?>
            </div>
            
        </div>
    </div>
<?php require_once __DIR__ . '/../../includes/user-layout-footer.php'; ?>

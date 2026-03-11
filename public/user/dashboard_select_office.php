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
    .office-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .office-card:hover {
        transform: translateY(-5px);
    }
</style>

<div class="min-h-[calc(100vh-8rem)] flex flex-col items-center justify-center p-6 lg:p-12 transition-all duration-300 mx-4 md:mx-10 overflow-hidden relative">
        <div class="max-w-5xl w-full relative z-10">
            <!-- Header -->
            <div class="text-center mb-16">
                <!--
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-primary-50 backdrop-blur-xl border border-primary-100 mb-8 shadow-sm">
                    <img src="../img/logo.png" alt="ISPSC Logo" class="w-12 h-12 object-contain">
                </div>
                -->
                <h1 class="text-4xl md:text-5xl font-black text-gray-900 mb-4 tracking-tight font-heading">Select Destination Office</h1>
                <p class="text-xl text-gray-500 font-medium">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>. Where would you like to queue today?</p>
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
                        <button type="submit" class="w-full h-full text-left office-card bg-white rounded-[32px] md:rounded-[40px] p-8 shadow-premium border border-slate-50 hover:border-primary-100 hover:shadow-primary-premium relative overflow-hidden group">
                            
                            <!-- Decorative background element -->
                            <div class="absolute -right-12 -top-12 w-40 h-40 bg-gradient-to-br from-primary-50 to-transparent rounded-full blur-2xl group-hover:from-primary-100 transition-all duration-500"></div>
                            
                            <div class="relative z-10 flex flex-col h-full">
                                <div class="w-14 h-14 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center mb-6 text-2xl group-hover:text-white group-hover:bg-primary-500 transition-colors shadow-sm">
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
                                
                                <h3 class="text-2xl font-black text-gray-900 font-heading mb-3 tracking-tight"><?php echo htmlspecialchars($office['name']); ?></h3>
                                
                                <p class="text-gray-500 font-medium leading-relaxed mb-6 flex-grow">
                                    <?php echo htmlspecialchars($office['description'] ?? 'Queue for ' . $office['name'] . ' services.'); ?>
                                </p>
                                
                                <div class="flex gap-4 mb-6">
                                    <div class="flex flex-col bg-slate-50 p-3 rounded-xl flex-1 border border-slate-100">
                                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Open Windows</span>
                                        <span class="text-lg font-black text-gray-900"><?php echo $office['active_windows']; ?></span>
                                    </div>
                                    <div class="flex flex-col bg-slate-50 p-3 rounded-xl flex-1 border border-slate-100">
                                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Queue Waitlist</span>
                                        <span class="text-lg font-black text-gray-900"><?php echo $office['waiting']; ?></span>
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

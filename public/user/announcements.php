<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

$pageTitle = 'Campus Announcements';
require_once __DIR__ . '/../../models/Announcement.php';
require_once __DIR__ . '/../../models/User.php';

$announcementModel = new Announcement();
$userModel = new User();
$announcements = $announcementModel->getAll();

$isSubscribed = false;
// Mark as read for the current user
if (isset($_SESSION['user_id'])) {
    $announcementModel->markAsRead($_SESSION['user_id']);
    $userData = $userModel->getUserById($_SESSION['user_id']);
    $isSubscribed = $userData['announcement_subscription'] ?? false;
}

// Premium header inclusion
require_once __DIR__ . '/../../includes/user-layout-header.php';
?>

<style>
    /* Force no top margin for the first paragraph in the announcement content */
    .announcement-description p:first-child { margin-top: 0 !important; }
    .announcement-description p { margin-bottom: 0.5rem; }
    .announcement-description p:last-child { margin-bottom: 0 !important; }
    
    .glass-morphism {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
</style>

<div class="max-w-4xl mx-auto px-4 w-full">
    <div class="mb-10 md:mb-16 text-center">
        <h1 class="text-3xl md:text-5xl font-black text-slate-900 font-heading tracking-tight mb-4">Campus News Feed</h1>
        <p class="text-slate-500 text-sm md:text-base font-medium max-w-md mx-auto">Stay updated with the latest events and news from the ISPSC E-Queue System.</p>
    </div>

    <div class="space-y-6 md:space-y-12 pb-12">
        <?php if (empty($announcements)): ?>
            <div class="glass-morphism p-10 md:p-16 rounded-[2rem] text-center">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-newspaper text-slate-300 text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900 mb-2">The feed is empty</h3>
                <p class="text-slate-500 font-medium">Check back later for new updates!</p>
            </div>
        <?php else: ?>
            <?php foreach ($announcements as $a): ?>
                <article class="glass-morphism rounded-[2.5rem] overflow-hidden shadow-premium group hover:shadow-ultra transition-all duration-700">
                    <!-- Post Header -->
                    <div class="p-6 md:p-8 flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-2xl bg-primary-600 flex items-center justify-center text-white shadow-lg shrink-0 group-hover:rotate-6 transition-transform">
                            <i class="fas fa-university text-xl"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-lg font-black text-slate-900 group-hover:text-primary-600 transition-colors truncate leading-tight">Official Announcement</h4>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 leading-none mt-1">
                                <i class="far fa-clock mr-1"></i>
                                <?php echo date('F d, Y \a\t h:i A', strtotime($a['created_at'])); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Post Content -->
                    <div class="px-6 md:px-8 pb-6">
                        <h2 class="text-2xl md:text-3xl font-black text-slate-900 font-heading mb-3 leading-tight"><?php echo $a['title']; ?></h2>
                        <div class="announcement-description text-base text-slate-600 font-medium leading-relaxed">
                            <?php echo $a['content']; ?>
                        </div>
                    </div>

                    <!-- Post Image -->
                    <?php if ($a['image_path']): ?>
                        <div class="overflow-hidden">
                            <img src="<?php echo BASE_URL . '/' . $a['image_path']; ?>" 
                                 class="w-full h-auto object-cover max-h-[600px] group-hover:scale-105 transition-transform duration-1000" 
                                 alt="Announcement Image">
                        </div>
                    <?php endif; ?>

                    <!-- Post Footer -->
                    <div class="p-6 md:p-8 flex items-center justify-between bg-slate-50/30">
                        <div class="flex items-center space-x-4 opacity-50">
                            <span class="flex items-center text-xs font-black text-slate-400 uppercase tracking-widest">
                                <i class="far fa-check-circle mr-2"></i> Verified Post
                            </span>
                        </div>
                        <div class="px-4 py-2 bg-white rounded-full border border-slate-100 shadow-sm">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-primary-600">ISPSC News Feed</span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/user-layout-footer.php'; ?>

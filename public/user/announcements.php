<?php
session_start();
$pageTitle = 'Campus Announcements';
require_once __DIR__ . '/../../models/Announcement.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../config/config.php';

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php injectTailwindConfig(); ?>
    <style>
        /* Force no top margin for the first paragraph in the announcement content */
        .announcement-description p:first-child { margin-top: 0 !important; }
        .announcement-description p { margin-bottom: 0.5rem; }
        .announcement-description p:last-child { margin-bottom: 0 !important; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="mesh-gradient-container">
        <div class="mesh-gradient-item mesh-1"></div>
        <div class="mesh-gradient-item mesh-2"></div>
    </div>

    <?php include __DIR__ . '/../../includes/user-navbar.php'; ?>

    <main class="max-w-2xl mx-auto px-4 py-8 md:py-20">
        <div class="mb-10 md:mb-16 text-center">
            <h1 class="text-3xl md:text-5xl font-black text-gray-900 font-heading tracking-tight mb-4">Campus News Feed</h1>
            <p class="text-gray-500 text-sm md:text-base font-medium max-w-md mx-auto mb-8">Stay updated with the latest events, announcements, and news from the ISPSC Registrar.</p>
            
        </div>

        <div class="space-y-6 md:space-y-12">
            <?php if (empty($announcements)): ?>
                <div class="glass-morphism p-10 md:p-16 rounded-[2rem] md:rounded-[2.5rem] text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-newspaper text-slate-300 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 mb-2">The feed is empty</h3>
                    <p class="text-slate-500 font-medium">Check back later for new updates!</p>
                </div>
            <?php else: ?>
                <?php foreach ($announcements as $a): ?>
                    <article class="glass-morphism rounded-[2rem] md:rounded-[2.5rem] overflow-hidden shadow-premium group hover:shadow-ultra transition-all duration-700">
                        <!-- Post Header -->
                        <div class="p-5 md:p-8 pb-1 md:pb-2 flex items-center space-x-4">
                            <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl md:rounded-2xl bg-primary-600 flex items-center justify-center text-white shadow-lg shrink-0">
                                <i class="fas fa-university text-lg md:text-xl"></i>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-base md:text-lg font-black text-slate-900 group-hover:text-primary-600 transition-colors truncate leading-tight">ISPSC Registrar</h4>
                                <p class="text-[9px] md:text-[10px] font-black uppercase tracking-widest text-slate-400 leading-none">
                                    <i class="far fa-clock mr-1"></i>
                                    <?php 
                                        $time = strtotime($a['created_at']);
                                        echo date('F d, Y \a\t h:i A', $time); 
                                    ?>
                                </p>
                            </div>
                        </div>

                        <!-- Post Content -->
                        <div class="px-5 md:px-8 pb-4 md:pb-6">
                            <h2 class="text-xl md:text-2xl font-black text-slate-900 font-heading mt-3 mb-1 md:mb-2 leading-tight text-left"><?php echo $a['title']; ?></h2>
                            <div class="announcement-description text-sm md:text-base text-gray-700 font-medium leading-relaxed mt-0 text-left">
                                <?php echo $a['content']; ?>
                            </div>
                        </div>

                        <!-- Post Image -->
                        <?php if ($a['image_path']): ?>
                            <div class="mt-2 md:mt-4 overflow-hidden">
                                <img src="<?php echo BASE_URL . '/' . $a['image_path']; ?>" 
                                    class="w-full h-auto object-cover max-h-[500px] md:max-h-[600px] hover:scale-[1.02] transition-transform duration-1000" 
                                    alt="Announcement Image">
                            </div>
                        <?php endif; ?>

                        <!-- Post Footer -->
                        <div class="p-5 md:p-8 pt-4 md:pt-6 border-t border-slate-100/50 flex items-center justify-between">
                            <div class="flex items-center space-x-4 opacity-50">
                                <span class="flex items-center text-[9px] md:text-xs font-black text-slate-400 uppercase tracking-widest">
                                    <i class="far fa-eye mr-2"></i> Official Post
                                </span>
                            </div>
                            <div class="px-3 md:px-4 py-1.5 md:py-2 bg-slate-50/50 rounded-full border border-slate-100">
                                <span class="text-[8px] md:text-[10px] font-black uppercase tracking-[0.2em] text-primary-600">ISPSC News Feed</span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/../../includes/admin-layout-footer.php'; ?>

    <script>
    </script>
</body>
</html>

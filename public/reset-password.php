<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';

// Ensure user is authorized to reset password (via OTP)
if (!isset($_SESSION['reset_user_id'])) {
    header('Location: login.php');
    exit;
}

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        $userModel = new User();
        // Reset password
        if ($userModel->resetPassword($_SESSION['reset_user_id'], $password)) {
            // Clear session variable
            unset($_SESSION['reset_user_id']);
            
            // Redirect with success message
            header('Location: login.php?reset=success');
            exit;
        } else {
            $error = "Failed to reset password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php injectTailwindConfig(); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen flex items-center justify-center p-4 font-sans text-gray-900 bg-gray-50">
    <!-- Mesh Gradient Background -->
    <div class="mesh-gradient-container">
        <div class="mesh-gradient-item mesh-1"></div>
        <div class="mesh-gradient-item mesh-2"></div>
    </div>

    <div class="max-w-md w-full glass-morphism p-8 md:p-10 rounded-[30px] shadow-2xl border border-white/20 text-center relative overflow-hidden">
        
        <div class="mb-8">
            <div class="w-20 h-20 bg-primary-100 text-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg transform rotate-6">
                <i class="fas fa-lock-open text-4xl"></i>
            </div>
            <h1 class="text-3xl font-black mb-2 font-heading">New Access Key</h1>
            <p class="text-gray-600 font-medium">
                Set a new password for your account.
            </p>
        </div>

        <?php if ($error): ?>
            <div class="p-4 mb-6 text-sm text-secondary-600 bg-secondary-100 rounded-xl border border-secondary-200 flex items-center justify-center animate-shake">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span class="font-bold"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-6">
            <div class="space-y-2 text-left">
                <label class="block text-gray-500 text-[10px] font-black uppercase tracking-[0.3em] ml-1" for="password">New Password</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <i class="fas fa-key text-gray-400 group-focus-within:text-primary-500 transition-colors"></i>
                    </div>
                    <input type="password" id="password" name="password" required class="w-full pl-12 pr-4 py-4 bg-white/50 border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all font-bold text-gray-800 placeholder-gray-400 shadow-sm" placeholder="••••••••">
                </div>
            </div>

            <div class="space-y-2 text-left">
                <label class="block text-gray-500 text-[10px] font-black uppercase tracking-[0.3em] ml-1" for="confirm_password">Confirm Password</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <i class="fas fa-check-double text-gray-400 group-focus-within:text-primary-500 transition-colors"></i>
                    </div>
                    <input type="password" id="confirm_password" name="confirm_password" required class="w-full pl-12 pr-4 py-4 bg-white/50 border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all font-bold text-gray-800 placeholder-gray-400 shadow-sm" placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="w-full bg-primary-600 text-white font-black py-4 rounded-xl shadow-xl shadow-primary-600/20 hover:bg-primary-700 hover:shadow-2xl hover:-translate-y-1 transition-all active:scale-95 text-lg tracking-wide uppercase flex items-center justify-center space-x-2">
                <span>Update Password</span>
                <i class="fas fa-save text-sm opacity-50"></i>
            </button>
        </form>
    </div>
</body>
</html>

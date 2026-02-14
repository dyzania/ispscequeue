<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/MailService.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST['csrf_token'] ?? '');

    $fullName = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $schoolId = !empty($_POST['school_id']) ? sanitize($_POST['school_id']) : null;
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if ($password !== $confirmPassword) {
        $error = "Passwords do not match";
    } else {
        $userModel = new User();
        $mailService = new MailService();
        
        if ($userModel->emailExists($email)) {
            $error = "Email already registered";
        } else {
            $token = bin2hex(random_bytes(32));
            if ($userModel->register($email, $password, $fullName, $schoolId, 'user', $token)) {
                $mailService->sendVerification($email, $fullName, $token);
                $success = "Registration successful! Please check your email ($email) to verify your account before logging in.";
            } else {
                $error = "Registration failed. School ID might be already in use.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php injectTailwindConfig(); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <!-- Modern Mesh Gradient Background -->
    <div class="mesh-gradient-container">
        <div class="mesh-gradient-item mesh-1"></div>
        <div class="mesh-gradient-item mesh-2"></div>
        <div class="mesh-gradient-item mesh-3"></div>
    </div>

    <div class="max-w-5xl w-full grid grid-cols-1 md:grid-cols-2 bg-white rounded-3xl shadow-2xl overflow-hidden glass-morphism border border-white/20">
        <!-- Brand Side -->
        <div class="hidden md:flex flex-col justify-between p-12 bg-primary-600 text-white relative overflow-hidden">
            <div class="z-10">
                <div class="flex items-center space-x-3 mb-12">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-user-plus text-primary-600 text-xl"></i>
                    </div>
                    <span class="text-2xl font-black tracking-tight font-heading"><?php echo APP_NAME; ?></span>
                </div>
                
                <h1 class="text-5xl font-black leading-tight mb-6 font-heading">
                    Join Us and <br>
                    <span class="text-primary-200">Skip the Line</span>
                </h1>
                <p class="text-primary-100 text-lg max-w-md font-light leading-relaxed">
                    Create your account today and experience the most advanced queueing system that puts your time first.
                </p>
            </div>

            <div class="z-10 bg-white/10 p-6 rounded-2xl backdrop-blur-md border border-white/10">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-12 h-12 bg-primary-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-double text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="font-black text-white">Fast Setup</p>
                        <p class="text-primary-100 text-sm">Less than 2 minutes</p>
                    </div>
                </div>
                <div class="w-full bg-primary-700/50 h-2 rounded-full overflow-hidden">
                    <div class="bg-white w-2/3 h-full rounded-full"></div>
                </div>
            </div>

            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-primary-950/20 rounded-full -ml-32 -mb-32 blur-3xl"></div>
        </div>

        <!-- Form Side -->
        <div class="p-8 md:p-12 lg:p-16 flex flex-col justify-center bg-white/40 backdrop-blur-3xl overflow-y-auto">
            <div class="mb-10 text-center md:text-left">
                <h2 class="text-3xl font-black text-gray-900 mb-2 font-heading">Create Account</h2>
                <p class="text-gray-500 font-medium text-sm">Join thousands of users managing their time better</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="p-4 mb-6 text-sm text-secondary-700 bg-secondary-50 rounded-2xl border border-secondary-100 flex items-center animate-shake" role="alert">
                    <i class="fas fa-exclamation-circle mr-3 text-lg"></i>
                    <span class="font-bold"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="p-4 mb-6 text-sm text-primary-700 bg-primary-50 rounded-2xl border border-primary-100 flex items-center" role="alert">
                    <i class="fas fa-check-circle mr-3 text-lg"></i>
                    <span class="font-bold"><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                <div>
                    <label class="block text-gray-700 text-xs font-black uppercase tracking-widest mb-2 ml-1" for="full_name">Full Name</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-id-card text-gray-400 group-focus-within:text-primary-500 transition-colors"></i>
                        </div>
                        <input 
                            type="text" 
                            id="full_name" 
                            name="full_name" 
                            required
                            class="w-full pl-12 pr-4 py-3.5 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition-all font-medium text-gray-700 placeholder-gray-400"
                            placeholder="John Doe"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 text-xs font-black uppercase tracking-widest mb-2 ml-1" for="email">Email Address</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400 group-focus-within:text-primary-500 transition-colors"></i>
                        </div>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            class="w-full pl-12 pr-4 py-3.5 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition-all font-medium text-gray-700 placeholder-gray-400"
                            placeholder="john@example.com"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 text-xs font-black uppercase tracking-widest mb-2 ml-1" for="school_id">School ID (Optional)</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-id-badge text-gray-400 group-focus-within:text-primary-500 transition-colors"></i>
                        </div>
                        <input 
                            type="text" 
                            id="school_id" 
                            name="school_id" 
                            class="w-full pl-12 pr-4 py-3.5 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition-all font-medium text-gray-700 placeholder-gray-400"
                            placeholder="e.g. 2024-0001"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 text-xs font-black uppercase tracking-widest mb-2 ml-1" for="password">Password</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 group-focus-within:text-primary-500 transition-colors"></i>
                            </div>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                class="w-full pl-12 pr-4 py-3.5 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition-all font-medium text-gray-700 placeholder-gray-400"
                                placeholder="••••••••"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-xs font-black uppercase tracking-widest mb-2 ml-1" for="confirm_password">Confirm</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-shield-alt text-gray-400 group-focus-within:text-primary-500 transition-colors"></i>
                            </div>
                            <input 
                                type="password" 
                                id="confirm_password" 
                                name="confirm_password" 
                                required
                                class="w-full pl-12 pr-4 py-3.5 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition-all font-medium text-gray-700 placeholder-gray-400"
                                placeholder="••••••••"
                            >
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="w-full bg-primary-600 text-white font-black py-4 rounded-2xl shadow-xl shadow-primary-200 hover:bg-primary-700 hover:shadow-primary-300 hover:-translate-y-1 transition-all active:scale-95 flex items-center justify-center space-x-2 text-lg"
                    >
                        <span>Create Account</span>
                        <i class="fas fa-magic ml-2 text-sm"></i>
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <p class="text-gray-600 font-medium text-sm">
                    Already have an account? 
                    <a href="index.php" class="text-primary-600 font-black hover:text-primary-700 transition-colors">Sign In</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Custom Animations -->
    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .animate-shake {
            animation: shake 0.2s ease-in-out 0s 2;
        }
    </style>
</body>
</html>

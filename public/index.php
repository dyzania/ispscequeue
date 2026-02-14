<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST['csrf_token'] ?? '');
    
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    // Support code-based login for window staff (e.g., "w01" becomes "w01@window.local")
    if (!str_contains($email, '@')) {
        $email = strtolower($email) . '@window.local';
    }
    
    $userModel = new User();
    $user = $userModel->login($email, $password);
    
    if ($user && !isset($user['unverified'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['school_id'] = $user['school_id'];
        
        // Redirect based on role
        switch ($user['role']) {
            case 'admin':
                header('Location: admin/dashboard.php');
                break;
            case 'staff':
                header('Location: staff/dashboard.php');
                break;
            default:
                header('Location: user/dashboard.php');
                break;
        }
        exit;
    } elseif ($user && isset($user['unverified'])) {
        $error = "Please verify your email address before logging in. Check your inbox ($email).";
    } else {
        $error = "Invalid credentials or password";
    }
}

if (isset($_GET['verified'])) {
    $success = "Email verified successfully! You can now sign in.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
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
                        <i class="fas fa-layer-group text-primary-600 text-xl"></i>
                    </div>
                    <span class="text-2xl font-black tracking-tight font-heading"><?php echo APP_NAME; ?></span>
                </div>
                
                <h1 class="text-5xl font-black leading-tight mb-6 font-heading">
                    Welcome to the <br>
                    <span class="text-primary-200">Future of Queueing</span>
                </h1>
                <p class="text-primary-100 text-lg max-w-md font-light leading-relaxed">
                    Experience seamless, efficient, and modern customer service management. Say goodbye to long waits and hello to smart queueing.
                </p>
            </div>

            <div class="relative z-10 bottom-0">
                <div class="flex items-center space-x-4">
                    <div class="flex -space-x-2">
                        <img src="https://ui-avatars.com/api/?name=JD&background=fff&color=16a34a" class="w-8 h-8 rounded-full border-2 border-primary-600 shadow-sm" alt="">
                        <img src="https://ui-avatars.com/api/?name=SM&background=fff&color=16a34a" class="w-8 h-8 rounded-full border-2 border-primary-600 shadow-sm" alt="">
                        <img src="https://ui-avatars.com/api/?name=AK&background=fff&color=16a34a" class="w-8 h-8 rounded-full border-2 border-primary-600 shadow-sm" alt="">
                    </div>
                    <p class="text-sm text-primary-100 font-medium">Joined by 2,000+ users today</p>
                </div>
            </div>

            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-primary-950/20 rounded-full -ml-32 -mb-32 blur-3xl"></div>
        </div>

        <!-- Form Side -->
        <div class="p-8 md:p-16 flex flex-col justify-center bg-white/40 backdrop-blur-3xl">
            <div class="mb-10 text-center md:text-left">
                <h2 class="text-3xl font-black text-gray-900 mb-2 font-heading">Sign In</h2>
                <p class="text-gray-500 font-medium">Please enter your credentials to access the system</p>
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

            <form method="POST" action="" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2 ml-1" for="email">Email or School ID</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-user-circle text-gray-400 group-focus-within:text-primary-500 transition-colors text-lg"></i>
                        </div>
                        <input 
                            type="text" 
                            id="email" 
                            name="email" 
                            required
                            class="w-full pl-12 pr-4 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition-all font-medium text-gray-700 placeholder-gray-400"
                            placeholder="e.g. name@company.com or 2024-0001"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2 ml-1" for="password">Password</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400 group-focus-within:text-primary-500 transition-colors text-lg"></i>
                        </div>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="w-full pl-12 pr-4 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition-all font-medium text-gray-700 placeholder-gray-400"
                            placeholder="••••••••"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" class="w-5 h-5 rounded-lg border-gray-300 text-primary-600 focus:ring-primary-500 cursor-pointer transition-all">
                        <span class="ml-3 text-sm font-semibold text-gray-600 group-hover:text-gray-900 transition-colors">Remember me</span>
                    </label>
                    <a href="#" class="text-sm font-bold text-primary-600 hover:text-primary-700 transition-colors">Forgot password?</a>
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-primary-600 text-white font-black py-4 rounded-2xl shadow-xl shadow-primary-200 hover:bg-primary-700 hover:shadow-primary-300 hover:-translate-y-1 transition-all active:scale-95 flex items-center justify-center space-x-2 text-lg"
                >
                    <span>Sign In to Your Account</span>
                    <i class="fas fa-arrow-right ml-2 text-sm"></i>
                </button>
            </form>

            <div class="mt-10 pt-8 border-t border-gray-100">
                <p class="text-center text-gray-600 font-medium">
                    Don't have an account? 
                    <a href="register.php" class="text-primary-600 font-black hover:text-primary-700 transition-colors hover:underline underline-offset-4">Register here</a>
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

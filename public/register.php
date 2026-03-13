<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/MailService.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST['csrf_token'] ?? '');

    $fullName = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $college = sanitize($_POST['college']);
    $schoolId = !empty($_POST['school_id']) ? sanitize($_POST['school_id']) : null;
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Consent fields
    $termsAgree = isset($_POST['terms_agree']);
    $privacyAgree = isset($_POST['privacy_agree']);

    if (empty($college)) {
        $error = "College selection is mandatory.";
    } elseif (!$termsAgree) {
        $error = "You must agree to the Terms and Conditions.";
    } elseif (!$privacyAgree) {
        $error = "You must agree to the Privacy Notice.";
    } elseif ($password !== $confirmPassword) {
        $error = "Access keys do not match.";
    } else {
        $passwordErrors = User::validatePassword($password);
        if (!empty($passwordErrors)) {
            $error = $passwordErrors[0];
        } else {
            $userModel = new User();
            $mailService = new MailService();
            
            if ($userModel->emailExists($email)) {
                $error = "Identity already registered in the grid.";
            } else {
                // Register and get OTP
                $otpCode = $userModel->register($email, $password, $fullName, $schoolId, 'user', $college, 0);
                
                if ($otpCode) {
                    // Send OTP Email
                    if ($mailService->sendOTPEmail($email, $fullName, $otpCode, 'verification')) {
                        // Redirect to OTP verification page
                        header("Location: verify-otp.php?email=" . urlencode($email) . "&context=verification");
                        exit;
                    } else {
                        $error = "Registration successful, but failed to send OTP. Please contact support.";
                    }
                } else {
                    $error = "Protocol failure. Identity or School ID already synchronized.";
                }
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
    <title>Register Identity - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php injectTailwindConfig(); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .register-bg {
            background-image: linear-gradient(135deg, rgba(5, 34, 2, 0.95) 0%, rgba(2, 16, 1, 0.8) 100%), url('img/drone.png');
            background-size: cover;
            background-position: center;
        }
        .animate-tilt {
            animation: tilt 10s infinite linear;
        }
        @keyframes tilt {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(0.5deg); }
            75% { transform: rotate(-0.5deg); }
        }
        /* Custom checkbox styling */
        .custom-checkbox {
            appearance: none;
            -webkit-appearance: none;
            @apply w-5 h-5 border border-white/20 rounded-md bg-white/5 cursor-pointer transition-all relative flex-shrink-0;
        }
        .custom-checkbox:checked {
            @apply bg-primary-600 border-primary-500;
        }
        .custom-checkbox:checked::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            @apply absolute inset-0 flex items-center justify-center text-[10px] text-white;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 register-bg font-sans selection:bg-primary-500/30">

    <div class="max-w-[1200px] w-full grid grid-cols-1 lg:grid-cols-2 bg-primary-950/40 backdrop-blur-3xl rounded-[40px] shadow-2xl overflow-hidden border border-primary-500/20 animate-tilt">
        
        <!-- Form Side -->
        <div class="p-6 md:p-10 lg:p-12 flex flex-col justify-center bg-primary-950/10 order-2 lg:order-1 relative">
            <!-- Close/Back Button -->
            <a href="index.php" class="absolute top-6 left-6 w-10 h-10 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 text-gray-400 hover:text-white hover:bg-white/10 hover:border-white/20 transition-all active:scale-90 group z-20" title="Go Back">
                <i class="fas fa-times text-lg group-hover:rotate-90 transition-transform duration-300"></i>
            </a>

            <div class="mb-6 text-center lg:text-left">
                <h2 class="text-3xl font-black text-white mb-2 font-heading tracking-tight">Register</h2>
                <p class="text-gray-500 text-sm font-medium">Create your account to join the queueing system.</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="p-4 mb-6 text-sm text-primary-400 bg-primary-500/10 rounded-2xl border border-primary-500/20 flex items-center animate-shake z-10">
                    <i class="fas fa-shield-virus mr-3 text-lg"></i>
                    <span class="font-bold uppercase tracking-widest text-[9px]"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="p-4 mb-6 text-sm text-secondary-400 bg-secondary-500/10 rounded-2xl border border-secondary-500/20 flex items-center z-10">
                    <i class="fas fa-check-double mr-3 text-lg"></i>
                    <span class="font-bold uppercase tracking-widest text-[9px]"><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-3 md:space-y-4 relative z-10">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                    <div class="space-y-2 text-left">
                        <label class="block text-gray-400 text-[10px] font-black uppercase tracking-[0.3em] ml-1" for="full_name">Full Name</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                <i class="fas fa-id-card text-gray-600 group-focus-within:text-white transition-colors"></i>
                            </div>
                            <input type="text" id="full_name" name="full_name" required class="w-full pl-12 pr-4 py-4 bg-white/5 border border-white/10 rounded-2xl focus:outline-none focus:ring-4 focus:ring-white/10 focus:border-white transition-all font-medium text-white placeholder-gray-600" placeholder="">
                        </div>
                    </div>
                    <div class="space-y-2 text-left">
                        <label class="block text-gray-400 text-[10px] font-black uppercase tracking-[0.3em] ml-1" for="school_id">School ID</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                <i class="fas fa-id-badge text-gray-600 group-focus-within:text-white transition-colors"></i>
                            </div>
                            <input type="text" id="school_id" name="school_id" class="w-full pl-12 pr-4 py-3.5 bg-white/5 border border-white/10 rounded-2xl focus:outline-none focus:ring-4 focus:ring-white/10 focus:border-white transition-all font-medium text-white placeholder-gray-600 text-sm" placeholder="e.g. NLP-**-*****">
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5 text-left">
                    <label class="block text-gray-400 text-[9px] font-black uppercase tracking-[0.3em] ml-1" for="college">College <span class="text-primary-500">*</span></label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                            <i class="fas fa-university text-gray-600 group-focus-within:text-white transition-colors"></i>
                        </div>
                        <select id="college" name="college" required class="w-full pl-12 pr-4 py-3.5 bg-white/5 border border-white/10 rounded-2xl focus:outline-none focus:ring-4 focus:ring-white/10 focus:border-white transition-all font-medium text-white appearance-none cursor-pointer text-sm">
                            <option value="" disabled selected class="bg-primary-950">Select your College</option>
                            <option value="CAS" class="bg-primary-950">CAS - College of Arts and Sciences</option>
                            <option value="SCJE" class="bg-primary-950">SCJE - School of Criminal Justice Education</option>
                            <option value="CTE" class="bg-primary-950">CTE - College of Teacher Education</option>
                            <option value="CBME" class="bg-primary-950">CBME - College of Business Management and Entrepreneurship</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-600 group-focus-within:text-white transition-colors"></i>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5 text-left">
                    <label class="block text-gray-400 text-[9px] font-black uppercase tracking-[0.3em] ml-1" for="email">Email</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                            <i class="fas fa-envelope-open-text text-gray-600 group-focus-within:text-white transition-colors"></i>
                        </div>
                        <input type="email" id="email" name="email" required class="w-full pl-12 pr-4 py-3 bg-white/5 border border-white/10 rounded-2xl focus:outline-none focus:ring-4 focus:ring-white/10 focus:border-white transition-all font-medium text-white placeholder-gray-600 text-sm" placeholder="your_email@example.com">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                    <div class="space-y-1.5 text-left">
                        <label class="block text-gray-400 text-[9px] font-black uppercase tracking-[0.3em] ml-1" for="password">Password</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                <i class="fas fa-key text-gray-600 group-focus-within:text-white transition-colors"></i>
                            </div>
                            <input type="password" id="password" name="password" required class="w-full pl-12 pr-12 py-3 bg-white/5 border border-white/10 rounded-2xl focus:outline-none focus:ring-4 focus:ring-white/10 focus:border-white transition-all font-medium text-white placeholder-gray-600 text-sm" placeholder="••••••••">
                            <button type="button" onclick="togglePassword('password', this)" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-600 hover:text-white transition-colors">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>
                    <div class="space-y-1.5 text-left">
                        <label class="block text-gray-400 text-[9px] font-black uppercase tracking-[0.3em] ml-1" for="confirm_password">Confirm</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                <i class="fas fa-shield-check text-gray-600 group-focus-within:text-white transition-colors"></i>
                            </div>
                            <input type="password" id="confirm_password" name="confirm_password" required class="w-full pl-12 pr-12 py-3 bg-white/5 border border-white/10 rounded-2xl focus:outline-none focus:ring-4 focus:ring-white/10 focus:border-white transition-all font-medium text-white placeholder-gray-600 text-sm" placeholder="••••••••">
                            <button type="button" onclick="togglePassword('confirm_password', this)" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-600 hover:text-white transition-colors">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="space-y-3 pt-3 border-t border-white/5">
                    <div class="flex items-start space-x-3">
                        <input type="checkbox" id="terms_agree" name="terms_agree" required class="custom-checkbox">
                        <label for="terms_agree" class="text-xs text-gray-400 font-medium cursor-pointer">
                            I have read and agree to the <button type="button" data-modal-target="tc-modal" data-modal-toggle="tc-modal" class="text-primary-400 hover:text-primary-300 underline font-bold transition-colors">Terms and Conditions</button>.
                        </label>
                    </div>
                    <div class="flex items-start space-x-3">
                        <input type="checkbox" id="privacy_agree" name="privacy_agree" required class="custom-checkbox">
                        <label for="privacy_agree" class="text-xs text-gray-400 font-medium cursor-pointer leading-relaxed">
                            I have read the <button type="button" data-modal-target="pn-modal" data-modal-toggle="pn-modal" class="text-primary-400 hover:text-primary-300 underline font-bold transition-colors">Privacy Notice</button> and consent to the processing of my personal data for account creation, account management, and delivery of the services of <?php echo APP_NAME; ?>.
                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full bg-primary-600 text-white font-black py-4 rounded-2xl shadow-2xl shadow-primary-900/20 hover:bg-primary-500 hover:shadow-primary-500/40 hover:-translate-y-1 transition-all active:scale-95 flex items-center justify-center space-x-4 text-base mt-2">
                    <span>REGISTER</span>
                    <i class="fas fa-magic text-xs opacity-50"></i>
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-primary-500/10 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-gray-500 font-medium text-[10px]">Already have an account?</p>
                <a href="login.php" class="bg-primary-500/5 hover:bg-primary-500/10 text-white px-6 py-2.5 rounded-xl font-black text-[9px] uppercase tracking-[0.2em] border border-white/30 transition-all active:scale-95">Login</a>
            </div>
        </div>

        <!-- Brand Side -->
        <div class="hidden lg:flex flex-col justify-between p-12 bg-gradient-to-bl from-primary-600/20 to-transparent relative overflow-hidden order-1 lg:order-2">
            <div class="z-10">
                <a href="index.php" class="flex items-center space-x-4 mb-12 group justify-end">
                    <div class="flex flex-col text-right">
                        <span class="text-xl font-black tracking-tighter font-heading text-white leading-none">ISPSC MAIN</span>
                        <span class="text-xs font-bold tracking-[0.3em] text-primary-400 mt-1 uppercase"><?php echo APP_NAME; ?></span>
                    </div>
                    <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-2xl group-hover:rotate-6 transition-all duration-500 p-2">
                        <img src="img/logo.png" alt="ISPSC Logo" class="w-full h-full object-contain">
                    </div>
                </a>
                
                <h1 class="text-5xl font-black leading-tight mb-6 font-heading text-white text-right">
                    JOIN THE <br>
                    <span class="text-primary-400 font-black"><?php echo strtoupper(APP_NAME); ?>.</span>
                </h1>
                <p class="text-gray-400 text-lg max-w-[280px] ml-auto text-right font-medium leading-relaxed">
                    Efficiency starts with an identity. Register now to experience the next-gen e-queueing grid.
                </p>
            </div>

            <div class="relative z-10 text-right">
                <div class="flex justify-end space-x-2">
                    <div class="w-2 h-2 rounded-full bg-primary-500"></div>
                    <div class="w-2 h-2 rounded-full bg-primary-500/40"></div>
                    <div class="w-2 h-2 rounded-full bg-primary-500/10"></div>
                </div>
            </div>

            <div class="absolute -top-20 -right-20 w-80 h-80 bg-primary-500/10 rounded-full blur-[100px]"></div>
            <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-secondary-500/10 rounded-full blur-[100px]"></div>
        </div>
    </div>

    <!-- Modals -->
    <div id="tc-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-primary-950 border border-primary-500/20 rounded-[32px] shadow-2xl backdrop-blur-2xl">
                <div class="flex items-center justify-between p-6 md:p-8 border-b border-white/10">
                    <h3 class="text-2xl font-black text-white font-heading">Terms and Conditions</h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-white/5 hover:text-white rounded-2xl text-sm w-12 h-12 inline-flex justify-center items-center transition-all" data-modal-hide="tc-modal">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-8 md:p-10 space-y-6 overflow-y-auto max-h-[60vh] custom-scrollbar text-sm leading-relaxed text-gray-400">
                    <div class="space-y-4">
                        <p class="font-bold text-white uppercase tracking-widest text-xs">Effective Date: <?php echo date('F d, Y'); ?></p>
                        <p>Welcome to <?php echo APP_NAME; ?> (“Platform,” “Website,” “System,” “we,” “us,” or “our”). These Terms and Conditions govern your access to and use of our website, web application, mobile application, and related services.</p>
                        <p>By creating an account, clicking “I Agree,” or using the Platform, you acknowledge that you have read, understood, and agreed to be bound by these Terms and Conditions, our Privacy Notice, and other policies posted on the Platform.</p>
                        
                        <h4 class="text-white font-bold text-base mt-8">1. Eligibility</h4>
                        <p>You may register and use the Platform only if:<br>a. you are at least eighteen (18) years old; or<br>b. if you are below eighteen (18), you have obtained the consent of your parent, guardian, or authorized representative, when required by applicable law or institutional policy.</p>

                        <h4 class="text-white font-bold text-base mt-8">2. Account Registration</h4>
                        <p>To use certain features of the Platform, you may be required to create an account. You agree to:<br>a. provide accurate, current, and complete information;<br>b. keep your login credentials confidential;<br>c. update your account information when necessary; and<br>d. accept responsibility for all activities conducted through your account.</p>

                        <h4 class="text-white font-bold text-base mt-8">3. Use of the Platform</h4>
                        <p>You agree to use the Platform only for lawful purposes and in accordance with these Terms.</p>

                        <h4 class="text-white font-bold text-base mt-8">4. Privacy and Personal Data</h4>
                        <p>We process personal data in accordance with the Data Privacy Act of 2012 (Republic Act No. 10173) and other applicable Philippine laws, rules, and regulations.</p>

                        <h4 class="text-white font-bold text-base mt-8">5. Intellectual Property</h4>
                        <p>All content, software, code, design elements, text, graphics, logos, icons, interface features, and other materials on the Platform are owned by or licensed to ISPSC and are protected by applicable intellectual property laws.</p>

                        <h4 class="text-white font-bold text-base mt-8">Institutional Use</h4>
                        <p>The Platform is intended only for authorized students, staff, faculty, and approved users of ISPSC. Access may be limited, monitored, or revoked in accordance with institutional policies.</p>

                        <h4 class="text-white font-bold text-base mt-8">Queueing and Request Services</h4>
                        <p>Submission of a queue request, appointment, or service form through the Platform does not automatically guarantee approval. All requests remain subject to verification and office policies.</p>
                    </div>
                </div>
                <div class="p-6 md:p-8 border-t border-white/10 flex justify-end">
                    <button data-modal-hide="tc-modal" type="button" class="bg-primary-600 hover:bg-primary-500 text-white font-black px-10 py-4 rounded-2xl transition-all active:scale-95 shadow-xl shadow-primary-950/20">I UNDERSTAND</button>
                </div>
            </div>
        </div>
    </div>

    <div id="pn-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-primary-950 border border-primary-500/20 rounded-[32px] shadow-2xl backdrop-blur-2xl">
                <div class="flex items-center justify-between p-6 md:p-8 border-b border-white/10">
                    <h3 class="text-2xl font-black text-white font-heading">Privacy Notice</h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-white/5 hover:text-white rounded-2xl text-sm w-12 h-12 inline-flex justify-center items-center transition-all" data-modal-hide="pn-modal">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-8 md:p-10 space-y-6 overflow-y-auto max-h-[60vh] custom-scrollbar text-sm leading-relaxed text-gray-400">
                    <div class="space-y-4">
                        <p class="font-bold text-white uppercase tracking-widest text-xs">Last Updated: <?php echo date('F d, Y'); ?></p>
                        <p>At <?php echo APP_NAME; ?>, we are committed to protecting your privacy and ensuring that your personal data is handled in a lawful and secure manner in accordance with the Data Privacy Act of 2012.</p>
                        
                        <h4 class="text-white font-bold text-base mt-8">1. Information We Collect</h4>
                        <p>We collect personal information that you provide during registration, including your name, email address, school ID, and college affiliation.</p>

                        <h4 class="text-white font-bold text-base mt-8">2. Purpose of Processing</h4>
                        <p>Your data is processed for the following purposes:<br>• Account creation and management<br>• Verification of student/staff identity<br>• Management of queueing requests<br>• Sending notifications regarding your tickets<br>• Service improvement and analytics</p>

                        <h4 class="text-white font-bold text-base mt-8">3. Data Security</h4>
                        <p>We implement technical and organizational security measures to protect your data from unauthorized access, alteration, or disclosure.</p>

                        <h4 class="text-white font-bold text-base mt-8">4. Data Subject Rights</h4>
                        <p>You have the right to access, correct, or request the erasure of your personal data, subject to legitimate service requirements and school policies.</p>

                        <h4 class="text-white font-bold text-base mt-8">5. Consent</h4>
                        <p>By registering, you provide your explicit consent to the processing of your data as described in this notice. You may withdraw your consent at any time, but this may result in the termination of your access to the platform.</p>
                    </div>
                </div>
                <div class="p-6 md:p-8 border-t border-white/10 flex justify-end">
                    <button data-modal-hide="pn-modal" type="button" class="bg-primary-600 hover:bg-primary-500 text-white font-black px-10 py-4 rounded-2xl transition-all active:scale-95 shadow-xl shadow-primary-950/20">I AGREE</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>

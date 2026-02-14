<nav class="sticky top-0 z-[100] w-full">
    <div class="w-full">
        <div class="glass-morphism shadow-xl px-4 md:px-12 py-4 md:py-6 border-b border-white/50 flex items-center justify-between">
            <!-- Logo -->
            <a href="dashboard.php" class="flex items-center space-x-3 group shrink-0">
                <div class="w-10 h-10 bg-secondary-600 rounded-xl flex items-center justify-center shadow-lg group-hover:bg-secondary-700 transition-all duration-300 transform group-hover:rotate-6">
                    <i class="fas fa-user-tie text-white text-xl"></i>
                </div>
                <div class="hidden sm:block">
                    <span class="text-xl font-black tracking-tighter font-heading text-gray-900 leading-none"><?php echo APP_NAME; ?></span>
                    <div class="text-[10px] font-black uppercase tracking-[0.2em] text-secondary-600">Staff Portal</div>
                </div>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-1 lg:space-x-4">
                <a href="dashboard.php" class="px-6 py-3 rounded-xl text-lg font-black tracking-tight <?php echo str_contains($_SERVER['PHP_SELF'], 'dashboard.php') ? 'bg-secondary-600 text-white shadow-lg' : 'text-gray-600 hover:bg-secondary-50 hover:text-secondary-600' ?> transition-all duration-300 whitespace-nowrap">
                    <i class="fas fa-desktop mr-2 opacity-70"></i>Live Counter
                </a>
                <a href="services.php" class="px-6 py-3 rounded-xl text-lg font-black tracking-tight <?php echo str_contains($_SERVER['PHP_SELF'], 'services.php') ? 'bg-secondary-600 text-white shadow-lg' : 'text-gray-600 hover:bg-secondary-50 hover:text-secondary-600' ?> transition-all duration-300 whitespace-nowrap">
                    <i class="fas fa-clipboard-list mr-2 opacity-70"></i>Service Control
                </a>
                
                <div class="h-6 w-px bg-gray-200 mx-2"></div>

                <!-- Profile Dropdown -->
                <button id="staffDropdownButton" data-dropdown-toggle="staffDropdown" class="flex items-center space-x-2 p-1 pl-3 bg-gray-50/50 rounded-xl hover:bg-gray-100 transition-all border border-gray-100">
                    <span class="text-xs font-black text-gray-700"><?php echo explode(' ', $_SESSION['full_name'] ?? 'Staff')[0]; ?></span>
                    <img class="w-8 h-8 rounded-lg" src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['full_name'] ?? 'Staff'); ?>&background=dc2626&color=fff" alt="staff photo">
                </button>
                
                <div id="staffDropdown" class="z-[110] hidden bg-white/95 divide-y divide-gray-100 rounded-2xl shadow-2xl w-48 border border-gray-100 backdrop-blur-xl overflow-hidden">
                    <ul class="py-2 text-sm text-gray-700">
                        <li>
                            <a href="../logout.php" class="flex items-center px-4 py-3 font-bold text-red-600 hover:bg-red-50 transition-colors">
                                <i class="fas fa-sign-out-alt mr-2 opacity-60"></i>Sign out
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Mobile menu toggle & Profile -->
            <div class="flex items-center space-x-3 md:hidden">
                <button id="staffDropdownButtonMobile" data-dropdown-toggle="staffDropdownMobile" class="flex items-center bg-gray-50/50 rounded-xl p-1 border border-gray-100">
                    <img class="w-8 h-8 rounded-lg" src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['full_name'] ?? 'Staff'); ?>&background=dc2626&color=fff" alt="staff photo">
                </button>
                <button data-collapse-toggle="navbar-staff" type="button" class="inline-flex items-center p-2.5 w-12 h-12 justify-center text-gray-600 rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all bg-gray-50 border border-gray-100">
                    <span class="sr-only">Open main menu</span>
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>

            <!-- Mobile Dropdowns -->
            <div id="staffDropdownMobile" class="z-[110] hidden bg-white divide-y divide-gray-100 rounded-2xl shadow-2xl w-64 border border-gray-100 backdrop-blur-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Profile</p>
                    <p class="font-black text-gray-900 mt-1"><?php echo $_SESSION['full_name'] ?? 'Staff'; ?></p>
                </div>
                <ul class="py-2 text-sm text-gray-700">
                    <li><a href="../logout.php" class="block px-6 py-3 font-bold text-red-600">Logout</a></li>
                </ul>
            </div>

            <!-- Mobile Navigation -->
            <div class="hidden w-full md:hidden mt-6" id="navbar-staff">
                <ul class="flex flex-col font-black space-y-2 p-2 bg-gray-50/50 rounded-[2rem] border border-gray-100">
                    <li><a href="dashboard.php" class="flex items-center py-4 px-6 rounded-2xl <?php echo str_contains($_SERVER['PHP_SELF'], 'dashboard.php') ? 'bg-secondary-600 text-white shadow-lg' : 'text-gray-600 hover:bg-white' ?>"><i class="fas fa-desktop mr-4 text-lg"></i>Live Counter</a></li>
                    <li><a href="services.php" class="flex items-center py-4 px-6 rounded-2xl <?php echo str_contains($_SERVER['PHP_SELF'], 'services.php') ? 'bg-secondary-600 text-white shadow-lg' : 'text-gray-600 hover:bg-white' ?>"><i class="fas fa-clipboard-list mr-4 text-lg"></i>Service Control</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

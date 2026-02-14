<?php
$pageTitle = 'AI Context Settings';
require_once __DIR__ . '/../../models/Chatbot.php';
include __DIR__ . '/../../includes/admin-layout-header.php';

$db = Database::getInstance()->getConnection();
$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = trim($_POST['content']);
    
    if (empty($content)) {
        $error = 'Context content cannot be empty.';
    } else {
        $stmt = $db->prepare("UPDATE chatbot_data SET content = ?, updated_at = NOW() WHERE id = 1");
        if ($stmt->execute([$content])) {
            $message = 'Chatbot knowledge base updated successfully!';
        } else {
            $error = 'Failed to update database.';
        }
    }
}

$chatbot = new Chatbot();

// Get current chatbot data
$stmt = $db->query("SELECT * FROM chatbot_data WHERE id = 1");
$chatbot_data = $stmt->fetch(PDO::FETCH_ASSOC);

// If no data exists, insert default
if (!$chatbot_data) {
    $default_content = 'Enter your organization details here...';
    $db->exec("INSERT INTO chatbot_data (content) VALUES ('$default_content')");
    $chatbot_data = ['content' => $default_content];
}
?>
<?php
if ($message): ?>
    <div class="p-6 mb-8 text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-100 flex items-center shadow-lg shadow-emerald-100/50 animate-float" role="alert">
        <i class="fas fa-check-circle mr-4 text-2xl"></i>
        <span class="font-bold text-lg"><?php echo $message; ?></span>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="p-6 mb-8 text-red-800 bg-red-50 rounded-xl border border-red-100 flex items-center shadow-lg shadow-red-100/50" role="alert">
        <i class="fas fa-exclamation-circle mr-4 text-2xl"></i>
        <span class="font-bold text-lg"><?php echo $error; ?></span>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
    <!-- Settings Form -->
    <div class="lg:col-span-8">
        <div class="bg-white rounded-2xl shadow-2xl shadow-slate-200/50 border border-white overflow-hidden">
            <div class="bg-slate-900 px-8 py-6 flex items-center justify-between">
                <h3 class="text-xl font-black text-white font-heading tracking-tight">
                    <i class="fas fa-robot mr-3 text-primary-400"></i>AI Context Data
                </h3>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active System</span>
            </div>
            
            <div class="p-10">
                <div class="mb-8 p-6 bg-primary-50 rounded-lg border border-primary-100 text-sm text-primary-900">
                    <h4 class="font-black uppercase tracking-widest text-[10px] mb-2 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>Executive Guidance
                    </h4>
                    <p class="font-medium">The data below forms the AI's "Brain". Be precise with business hours, physical location, service requirements, and organizational policies to ensure accurate responses.</p>
                </div>

                <form method="POST">
                    <div class="mb-8">
                        <label for="content" class="block text-gray-700 text-xs font-black uppercase tracking-[0.2em] mb-4 ml-2">Knowledge Base Content</label>
                        <textarea
                            id="content"
                            name="content"
                            rows="15"
                            class="w-full px-8 py-6 bg-slate-50 border border-slate-100 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary-100 focus:bg-white focus:border-primary-500 transition-all font-medium text-gray-700 context-editor shadow-inner"
                            placeholder="Enter company info, FAQs, and policies here..."
                            required
                        ><?php echo htmlspecialchars($chatbot_data['content']); ?></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-slate-900 hover:bg-black text-white font-black py-4 px-10 rounded-lg shadow-xl shadow-slate-200 transition transform hover:-translate-y-1 active:scale-95 flex items-center">
                            <i class="fas fa-save mr-3"></i>Deploy Intelligence
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="lg:col-span-4 space-y-8">
        <div class="bg-white rounded-2xl p-10 shadow-2xl shadow-slate-200/50 border border-white">
            <h3 class="text-xl font-black text-gray-900 font-heading mb-6 tracking-tight">System Status</h3>
            <div class="space-y-6">
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Model</span>
                    <span class="text-xs font-bold text-gray-700">Step-3.5-Flash</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Response Time</span>
                    <span class="text-xs font-bold text-emerald-600">~1.2s</span>
                </div>
            </div>
        </div>

        <div class="bg-indigo-600 rounded-2xl p-10 shadow-2xl shadow-indigo-200 relative overflow-hidden group">
            <div class="relative z-10 text-white">
                <h3 class="text-xl font-black font-heading mb-4 tracking-tight text-white">Live Testing</h3>
                <p class="text-indigo-100 text-sm font-medium mb-8 leading-relaxed">Instantly verify knowledge base updates using the integrated chat widget.</p>
                <button onclick="document.getElementById('chatToggleBtn').click()" class="w-full bg-white text-indigo-700 font-black py-4 rounded-lg shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all flex items-center justify-center space-x-2">
                    <i class="fas fa-comment-dots text-lg"></i>
                    <span>Test Updates</span>
                </button>
            </div>
            <!-- Decorative Icon -->
            <div class="absolute -right-6 -bottom-6 text-9xl text-white/10 group-hover:rotate-12 transition-transform duration-700">
                <i class="fas fa-robot"></i>
            </div>
        </div>
    </div>
</div>

<?php 
include __DIR__ . '/../../includes/chatbot-widget.php';
include __DIR__ . '/../../includes/admin-layout-footer.php'; 
?>

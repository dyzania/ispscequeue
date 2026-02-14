<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'user'): ?>
<!-- Chatbot Widget -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/chatbot.css?v=1.6">

<div class="chat-wrapper" id="chatWrapper">
    <!-- Chat Launcher -->
    <button class="chat-launcher" id="chatToggleBtn" aria-label="Open chat">
        <div class="launcher-icon">
            <i class="fas fa-comment-dots"></i>
        </div>
    </button>

    <!-- Chat Window -->
    <div class="chat-window shadow-ultra" id="chatContainer">
        <!-- Header -->
        <div class="chat-window-header">
            <div class="header-info">
                <div class="bot-avatar-pulse">
                    <i class="fas fa-robot"></i>
                </div>
                <div>
                    <h3><?php echo APP_NAME; ?> Assistant</h3>
                    <div class="status-indicator">
                        <span class="status-dot"></span>
                        <span>Always Online</span>
                    </div>
                </div>
            </div>
            <button class="chat-window-close" id="chatCloseBtn">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="chat-window-body">
            <div class="chat-messages-container" id="chatMessages">
                <div class="welcome-card">
                    <div class="welcome-icon">👋</div>
                    <h2>Hello!</h2>
                    <p>I'm your E-Queue assistant. How can I help you today?</p>
                </div>
            </div>
            <!-- Typing Indicator -->
            <div id="typingIndicator" class="typing-indicator-wrapper hidden">
                <div class="typing-bubble">
                    <span></span><span></span><span></span>
                </div>
            </div>
        </div>

        <!-- Footer / Input -->
        <div class="chat-window-footer">
            <form class="chat-input-wrapper" id="chatForm">
                <input type="text" id="messageInput" placeholder="Write a message..." autocomplete="off" required>
                <button type="submit" id="sendButton" class="send-btn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
            <div class="footer-meta">
                Powered by AI • <?php echo date('H:i'); ?>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>/js/chatbot.js" defer></script>
<?php endif; ?>

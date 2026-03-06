## 🚀 Core Features

### 1. Queue Management & Algorithm
Implemented in [Ticket.php](file:///c:/xampp/htdocs/ispscequeue/models/Ticket.php).
- **Sequential Ticket Numbering**: Generates unique codes per service (e.g., REG-001, ACC-005).
- **Constraint-Aware Scheduling**: A sophisticated algorithm that calculates wait times by analyzing window status, service-to-window mappings, and historical processing speeds.
- **Queue State Persistence**: Tracks "Waiting", "Serving", "Completed", and "Snoozed" states to manage customer flow accurately.

### 2. AI Chatbot (OpenRouter Integration)
Implemented in [Chatbot.php](file:///c:/xampp/htdocs/ispscequeue/models/Chatbot.php).
- **Context-Aware Support**: Uses a curated `ai_context` database table to provide project-specific answers to students/clients.
- **RESTful Integration**: Communicates with the OpenRouter API to leverage advanced LLM capabilities while maintaining local control over the "knowledge base".

### 3. Sentiment Analysis Microservice
Implemented in [sentiment_analysis/app.py](file:///c:/xampp/htdocs/ispscequeue/sentiment_analysis/app.py).
- **NLP Processing**: Offloads heavy text analysis to Python, which is better suited for machine learning.
- **PHP Integration**: The backend communicates with the Python service via internal `curl` calls, ensuring a seamless experience for the end-user.

---

## 🛠️ Services Implementation

### 1. Service Management
Implemented in [Service.php](file:///c:/xampp/htdocs/ispscequeue/models/Service.php).
- **Service-Window Mapping**: Allows administrators to dynamically toggle which windows serve which services.
- **Performance Tracking**: Captures "Target Times" for each service type to measure efficiency against real-world data.

### 2. Mail Service (Backend Notifications)
Implemented in [MailService.php](file:///c:/xampp/htdocs/ispscequeue/models/MailService.php).
- **Transactional Emails**: Uses **PHPMailer** to send high-priority alerts:
    - **OTP Verification**: For secure login and password resets.
    - **Ticket Call Notifications**: Alerting users via email when it is their turn.
    - **Transaction Receipts**: Formal notice of service completion with staff remarks.
    - **Security Alerts**: Immediate notification of account lockouts after failed attempts.

---

## 🔔 Notification System

The notification system uses a hybrid approach to ensure users never miss their turn:

### 1. Real-Time Frontend Polling
Implemented in [notifications.js](file:///c:/xampp/htdocs/ispscequeue/public/js/notifications.js).
- **AJAX Polling**: Continuously checks the server every 10 seconds for state changes.
- **Native Browser Alerts**: Leverages the browser's Notification API to show alerts even when the tab is not in focus.

### 2. Modern UX Components
- **Custom Toasts & Modals**: Built a bespoke UI system for elegant, non-intrusive feedback (replacing dated browser alerts).
- **Audio Cues**: Context-aware alert sounds (muted for staff, active for users) to provide accessibility and immediate feedback.
- **Email Redundancy**: If a user is not looking at the screen, the [MailService](file:///c:/xampp/htdocs/ispscequeue/models/MailService.php#9-202) ensures they receive an alert on their mobile device or computer.

---

## 🛡️ Anti-Spam & Security Measures

The system includes several layers of protection to prevent "ticket-cancel spam" and other malicious activities:

### 1. Multi-Stage "Speed Bumps"
- **Service Checklist**: Users must manually check off all requirements in a frontend modal before the "Get Ticket" button is enabled.
- **Mandatory Feedback**: A strict "lock" prevents users from generating a new ticket until they have provided feedback for their previous completed transaction.

### 2. Rate Limiting & Flow Control
- **Cancellation Rate Limit**: Specifically for ticket cancellations, the system restricts users to **5 cancellations per 5 minutes** using a session-based rate limiter ([checkRateLimit](file:///c:/xampp/htdocs/ispscequeue/config/config.php#185-213)).
- **One-Ticket Policy**: A user is programmatically prevented from having more than one active ticket ('waiting', 'called', or 'serving') at any given time.
- **Staff-Only Availability**: Tickets can only be generated for services that have at least one active/enabled staff window.

### 3. Backend Hardening
- **CSRF Protection**: Every cancellation or ticket generation request requires a unique cryptographic token stored in the session.
- **Ownership Validation**: The backend verifies that the `user_id` on the ticket matches the currently logged-in user before allowing any status changes.
- **Security Logs**: All rate limit violations and unauthorized attempts are logged to [logs/security.log](file:///c:/xampp/htdocs/ispscequeue/logs/security.log) for administrative review.

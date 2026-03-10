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

---

## 🏛️ Layout & Aesthetic Refactoring

The system has undergone a major layout consolidation to achieve a "Premium" and consistent UX across all portals:

### 1. Unified Layout Fragments
- **Consolidated Headers/Footers**: Replaced ad-hoc HTML in `public/user` and `public/staff` with unified fragments ([user-layout-header.php](file:///c:/xampp/htdocs/ispscequeue/includes/user-layout-header.php), [staff-layout-header.php](file:///c:/xampp/htdocs/ispscequeue/includes/staff-layout-header.php)).
- **Standardized Dependencies**: Centralized Tailwind CSS, Font Awesome, and custom counting scripts to ensure identical behavior and styling across the entire platform.

### 2. Admin Dashboard Enhancements
- **Premium Window Cards**: Enlarged the window carousel cards in the Admin portal to provide a high-visibility, "executive" feel.
- **40/60 Information Split**: Implemented a vertical layout split for window cards:
    - **Top 40% (Upcoming)**: Shows the "Next in Line" ticket number and service code for immediate clarity.
    - **Bottom 60% (Active)**: A larger, high-contrast block for the "Now Serving" ticket, complete with live status animations.
- **Real-Time Synchronization**: Updated the dynamic dashboard sync logic to update both the Upcoming and Active ticket data without page flickers.

### 3. User Dashboard Optimization
- **Compact Resilience**: Reverted the window cards in the user portal to a more efficient, compact layout, ensuring they remain legible without over-enlargement, while maintaining the premium `glass-morphism` effects.
- **Improved Hierarchy**: Streamlined the layout to focus on the user's active ticket and current position.

### 4. Structural & Functional Integrity
- **Get Ticket Restored**: Repaired the `get-ticket.php` page by removing redundant tags and duplicate script inclusions that were breaking the JavaScript-driven requirement checklist.
- **Staff Layout Gaps**: Restored the modern "side gaps" in the Staff portal by centralizing padding in the `staff-layout-header.php` fragment.
- **DOM Desynchronization Fix**: Removed trailing `</div>` and extra `</main>` tags across multiple files to ensure perfect nesting with the new unified layout system.
- **College Filter Refinement**: Significantly enlarged the campus watch filters (CAS, SCJE, etc.) and updated their border-radius to match the dashboard's premium containers, using more vibrant colors for active states.
- **Queue Focus Refactor**: Transitioned the 'Upcoming Tickets' section to a single-column layout for better readability.
- **Extreme Client Highlighting**: Implemented maximum visual emphasis for client names using high-contrast dark badges (`bg-slate-900`), vibrant primary accents, and a radiated glow effect for the active transaction to ensure absolute visibility.
- **Compact Staff "High-Density" Layout**: Significantly reduced the vertical height of staff metrics (Served Today, Avg. Speed, etc.) and operational headers by compacting padding and scaling down icons. This is paired with a further refined "Medium-Small" font size for active client names to optimize space efficiency.
- **Mega-Centered Admin Tickets**: Centered and significantly enlarged Admin ticket numbers (up to `text-[15rem]` on 5XL) for absolute visibility, complemented by a reduced and more balanced font size for client names in the Staff portal.
- **Fine-Tuned "Medium-Plus" Admin Layout**: Incrementally increased the admin dashboard height (min-h: 360px - 750px) and scaled up UI elements (icons, typography) to find the perfect balance between compactness and prominence, satisfying the request for a "lil bit taller" profile.

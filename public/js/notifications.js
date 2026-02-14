class EQueueNotifications {
  constructor() {
    // Defensive check to avoid double initialization
    if (window.equeueNotifsInit) return;
    window.equeueNotifsInit = true;

    this.pollInterval = 10000; // 10 seconds
    this.isSupported = "Notification" in window;
    this.hasPermission = false;
    this.toastContainer = null;

    console.log(
      "%c🔔 E-Queue Notifications starting...",
      "color: #059669; font-weight: bold; font-size: 12px;",
    );

    if (typeof EQUEUE_BASE_URL === "undefined") {
      console.error(
        "E-Queue Error: EQUEUE_BASE_URL is missing. Notifications disabled.",
      );
      return;
    }

    this.injectStyles();
    this.listenForManualToasts();
    this.init();
  }

  injectStyles() {
    if (document.getElementById("equeue-notif-styles")) return;
    const style = document.createElement("style");
    style.id = "equeue-notif-styles";
    style.textContent = `
      #equeue-toast-container {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 2147483647; /* Use maximum possible z-index */
        display: flex;
        flex-direction: column;
        gap: 12px;
        pointer-events: none;
      }
      .equeue-toast {
        min-width: 320px;
        max-width: 450px;
        background: white;
        border-radius: 16px;
        padding: 16px 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        border: 1px solid rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        gap: 16px;
        pointer-events: auto;
        animation: toast-slide-in 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        cursor: pointer;
        overflow: hidden;
        position: relative;
        backdrop-filter: blur(8px);
      }
      .equeue-toast::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        height: 4px;
        background: currentColor;
        width: 100%;
        animation: toast-progress 3s linear forwards;
        opacity: 0.3;
      }
      .equeue-toast-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
      }
      .equeue-toast-content {
        flex: 1;
      }
      .equeue-toast-title {
        font-weight: 800;
        color: #111827;
        font-size: 13px;
        margin-bottom: 2px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
      }
      .equeue-toast-message {
        color: #4b5563;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.4;
      }
      @keyframes toast-slide-in {
        from { transform: translateX(120%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
      }
      @keyframes toast-progress {
        from { width: 100%; }
        to { width: 0%; }
      }
      .equeue-toast.fade-out {
        animation: toast-fade-out 0.3s ease-in forwards;
      }
      @keyframes toast-fade-out {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(20px); opacity: 0; }
      }
    `;
    document.head.appendChild(style);
  }

  async init() {
    this.createToastContainer();

    if (this.isSupported) {
      if (Notification.permission === "granted") {
        this.hasPermission = true;
      } else if (Notification.permission !== "denied") {
        this.requestPermission();
      }
    }

    this.startPolling();
    console.log("E-Queue: Notifications active. Endpoint: " + EQUEUE_BASE_URL);
  }

  createToastContainer() {
    if (document.getElementById("equeue-toast-container")) {
      this.toastContainer = document.getElementById("equeue-toast-container");
      return;
    }
    this.toastContainer = document.createElement("div");
    this.toastContainer.id = "equeue-toast-container";
    document.body.appendChild(this.toastContainer);
  }

  async requestPermission() {
    try {
      const permission = await Notification.requestPermission();
      if (permission === "granted") {
        this.hasPermission = true;
        console.log("E-Queue: Native notifications granted.");
      }
    } catch (error) {
      console.warn("E-Queue: Native notification request failed.");
    }
  }

  startPolling() {
    this.checkNotifications();
    setInterval(() => this.checkNotifications(), this.pollInterval);
  }

  async checkNotifications() {
    try {
      const apiUrl = `${EQUEUE_BASE_URL}/api/get-notifications.php`;
      const response = await fetch(apiUrl);

      if (!response.ok) {
        if (response.status === 401) return; // User logged out
        throw new Error("API " + response.status);
      }

      const data = await response.json();

      if (data.success && data.notifications && data.notifications.length > 0) {
        console.log("E-Queue: " + data.notifications.length + " new alerts.");
        data.notifications.forEach((notif) => {
          this.processNotification(notif);
        });
      }
    } catch (error) {
      // Periodic server errors or networking issues are ignored silently to avoid console spam
    }
  }

  processNotification(notif) {
    this.showToast(notif);

    if (this.hasPermission) {
      this.showBrowserNotification(notif);
    }

    this.playAlertSound();
    this.handleAutoReload(notif);
  }

  // Allow other scripts to trigger a toast manually
  listenForManualToasts() {
    document.addEventListener("equeue:toast", (e) => {
      this.showToast({
        type: e.detail.type || "system",
        message: e.detail.message,
        title: e.detail.title,
      });
    });
  }

  showToast(notif) {
    const toast = document.createElement("div");
    toast.className = "equeue-toast";

    let title = notif.title || "System Alert";
    let iconClass = "fa-bell";
    let colorClass = "text-indigo-600";
    let bgClass = "bg-indigo-50";

    if (notif.type === "turn_next") {
      title = notif.title || "IT IS YOUR TURN!";
      iconClass = "fa-bullhorn";
      colorClass = "text-emerald-600";
      bgClass = "bg-emerald-50";
    } else if (notif.type === "completed" || notif.type === "success") {
      title = notif.title || "Success";
      iconClass = "fa-check-circle";
      colorClass = "text-blue-600";
      bgClass = "bg-blue-50";
    } else if (notif.type === "now_serving" || notif.type === "serving") {
      title = notif.title || "Now Serving";
      iconClass = "fa-play-circle";
      colorClass = "text-amber-600";
      bgClass = "bg-amber-50";
    } else if (notif.type === "cancelled") {
      title = notif.title || "Ticket Cancelled";
      iconClass = "fa-times-circle";
      colorClass = "text-red-600";
      bgClass = "bg-red-50";
    }

    toast.style.color = colorClass.includes("text-emerald")
      ? "#059669"
      : colorClass.includes("text-blue")
        ? "#2563eb"
        : colorClass.includes("text-amber")
          ? "#d97706"
          : colorClass.includes("text-red")
            ? "#dc2626"
            : "#4f46e5";

    toast.innerHTML = `
      <div class="equeue-toast-icon ${bgClass} ${colorClass}">
        <i class="fas ${iconClass}"></i>
      </div>
      <div class="equeue-toast-content">
        <div class="equeue-toast-title">${title}</div>
        <div class="equeue-toast-message">${notif.message}</div>
      </div>
    `;

    toast.onclick = () => {
      window.focus();
      if (!window.location.href.includes("my-ticket.php")) {
        window.location.href = `${EQUEUE_BASE_URL}/user/my-ticket.php`;
      }
    };

    if (this.toastContainer) {
      this.toastContainer.appendChild(toast);
    }

    setTimeout(() => {
      toast.classList.add("fade-out");
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }

  showBrowserNotification(notif) {
    let title = "E-Queue Alert";
    if (notif.type === "turn_next") title = "IT IS YOUR TURN!";
    else if (notif.type === "completed") title = "Service Completed";

    try {
      const n = new Notification(title, {
        body: notif.message,
        tag: "ticket-" + (notif.ticket_id || "system"),
        renotify: true,
      });
      n.onclick = function () {
        window.focus();
        if (!window.location.href.includes("my-ticket.php")) {
          window.location.href = `${EQUEUE_BASE_URL}/user/my-ticket.php`;
        }
        this.close();
      };
    } catch (e) {}
  }

  handleAutoReload(notif) {
    if (
      [
        "completed",
        "turn_next",
        "now_serving",
        "serving",
        "cancelled",
      ].includes(notif.type)
    ) {
      const currentPath = window.location.pathname;
      if (
        currentPath.includes("dashboard.php") ||
        currentPath.includes("my-ticket.php") ||
        currentPath.includes("history.php")
      ) {
        // Wait for the 3s toast + 300ms fade-out before reloading
        const delay = 3500;
        setTimeout(() => window.location.reload(), delay);
      }
    }
  }

  playAlertSound() {
    try {
      const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
      const oscillator = audioCtx.createOscillator();
      const gainNode = audioCtx.createGain();
      oscillator.type = "sine";
      oscillator.frequency.setValueAtTime(880, audioCtx.currentTime);
      gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
      oscillator.connect(gainNode);
      gainNode.connect(audioCtx.destination);
      oscillator.start();
      oscillator.stop(audioCtx.currentTime + 0.2);
    } catch (e) {}
  }
}

// Initial boot
if (document.readyState === "loading") {
  document.addEventListener(
    "DOMContentLoaded",
    () => new EQueueNotifications(),
  );
} else {
  new EQueueNotifications();
}

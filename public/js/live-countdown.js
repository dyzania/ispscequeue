/**
 * Antigravity Live Countdown Utility - Flicker-Free Surgical Version
 * Ensures that timers survive DOM swaps and take over instantly.
 */

class LiveCountdown {
  constructor() {
    this.timers = new Map(); // Store by ticketId
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", () => this.init());
    } else {
      this.init();
    }
  }

  init() {
    // 1. Initial scan
    this.refresh();

    // 2. High-performance MutationObserver
    this.observer = new MutationObserver((mutations) => {
      let containsCountdown = false;
      for (const mutation of mutations) {
        if (mutation.type === "childList") {
          for (const node of mutation.addedNodes) {
            if (node.nodeType === Node.ELEMENT_NODE) {
              if (
                node.hasAttribute("data-live-countdown") ||
                node.querySelector("[data-live-countdown]")
              ) {
                containsCountdown = true;
                break;
              }
            }
          }
        }
        if (containsCountdown) break;
      }

      if (containsCountdown) {
        // Run synchronously for absolute zero-latency takeover
        this.refresh();
      }
    });

    this.observer.observe(document.body, {
      childList: true,
      subtree: true,
    });

    // 3. Sync with DashboardRefresh updates
    document.addEventListener("dashboard:updated", () => {
      this.refresh();
    });
  }

  formatDuration(seconds) {
    if (seconds < 60) return "0m";
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    let parts = [];
    if (h > 0) parts.push(h + "h");
    if (m > 0 || (h > 0 && m === 0)) parts.push(m + "m");
    if (parts.length === 0) parts.push("0m");
    return parts.join(" ");
  }

  refresh() {
    const elements = document.querySelectorAll("[data-live-countdown]");

    elements.forEach((el) => {
      const ticketId = el.dataset.ticketId;
      if (!ticketId) return;

      const serverTarget = parseInt(el.dataset.targetTimestamp);
      const serverNowAttr = el.dataset.serverNow;
      const serverNow = serverNowAttr ? parseInt(serverNowAttr) : Date.now();
      const clientOffset = Date.now() - serverNow;
      const isServing = el.dataset.isServing === "1";

      if (isNaN(serverTarget)) return;

      // --- Stable Anchor Logic ---
      const storageKey = `ticket_${ticketId}_stable_target`;
      let targetTimestamp = serverTarget;
      const storedTarget = sessionStorage.getItem(storageKey);
      const mode = el.dataset.countdownMode || "remaining"; // "remaining" or "elapsed"

      if (storedTarget) {
        const diff = Math.abs(parseInt(storedTarget) - serverTarget);
        if (diff < 60000) {
          targetTimestamp = parseInt(storedTarget);
        } else {
          sessionStorage.setItem(storageKey, serverTarget);
        }
      } else {
        sessionStorage.setItem(storageKey, serverTarget);
      }

      // --- Surgical Takeover ---
      if (this.timers.has(ticketId)) {
        clearInterval(this.timers.get(ticketId));
      }

      const updateText = () => {
        if (!document.contains(el)) return false;

        const now = Date.now() - clientOffset;

        if (mode === "elapsed") {
          const elapsed = Math.max(0, Math.floor((now - targetTimestamp) / 1000));
          el.textContent = this.formatDuration(elapsed);
          return true;
        } else {
          const remaining = Math.max(0, Math.floor((targetTimestamp - now) / 1000));
          if (remaining <= 0 && isServing) {
            el.textContent = "Almost done...";
            el.classList.add("animate-pulse");
            return false;
          } else {
            el.textContent = this.formatDuration(remaining);
            if (remaining <= 0) {
              sessionStorage.removeItem(storageKey);
              return false;
            }
          }
        }
        return true;
      };

      // RUN IMMEDIATELY to prevent seeing the static HTML before JS runs
      const shouldKeepTicking = updateText();

      if (shouldKeepTicking) {
        const timer = setInterval(() => {
          if (!updateText()) {
            clearInterval(timer);
            this.timers.delete(ticketId);
          }
        }, 1000);
        this.timers.set(ticketId, timer);
      }
    });
  }
}

// Global instance - Single entry point
if (!window.liveCountdown) {
  window.liveCountdown = new LiveCountdown();
}

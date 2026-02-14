
 // dashboard refresh utility
 // silently refreshes specific dashboard segments by fetching the current page

class DashboardRefresh {
  constructor(containers = [], interval = 10000) {
    this.containers = containers;
    this.interval = interval;
    this.isRunning = false;
    this.timer = null;
    this.isPaused = false;
    this.lastRefresh = 0;

    this.init();
  }

  init() {
    // 1. start the refresh loop
    this.start();

    // 2. typing/focus detection
    // we pause refresh if the user is interacting with form fields
    const handleFocus = (e) => {
      const tag = e.target.tagName;
      if (tag === "INPUT" || tag === "TEXTAREA" || tag === "SELECT") {
        this.isPaused = true;
      }
    };

    const handleBlur = (e) => {
      const tag = e.target.tagName;
      if (tag === "INPUT" || tag === "TEXTAREA" || tag === "SELECT") {
        // short delay to allow focus to actually land on another element
        setTimeout(() => {
          const focused = document.activeElement;
          if (
            !focused ||
            (focused.tagName !== "INPUT" &&
              focused.tagName !== "TEXTAREA" &&
              focused.tagName !== "SELECT")
          ) {
            this.isPaused = false;
          }
        }, 150);
      }
    };

    document.addEventListener("focusin", handleFocus);
    document.addEventListener("focusout", handleBlur);

    // 3. Visibility API - CRITICAL for Mobile
    // Mobile browsers pause JavaScript entirely in background.
    // We MUST catch up immediately when the user returns.
    document.addEventListener("visibilitychange", () => {
      if (document.hidden) {
        this.stop();
      } else {
        this.start();
        // Force an immediate refresh if it's been a while
        this.refresh();
      }
    });

    // 4. Network Status
    window.addEventListener("online", () => {
      this.start();
      this.refresh();
    });
    window.addEventListener("offline", () => this.stop());

    // 5. Mobile Manual Interaction Fallback
    // Allow double-tap on containers to force a manual refresh
    this.containers.forEach((id) => {
      const el = document.getElementById(id);
      if (el) {
        el.addEventListener("dblclick", () => this.refresh(true));
      }
    });
  }

  start() {
    if (this.isRunning) return;
    this.isRunning = true;
    this.scheduleNext();
  }

  stop() {
    this.isRunning = false;
    if (this.timer) {
      clearTimeout(this.timer);
      this.timer = null;
    }
  }

  scheduleNext() {
    if (!this.isRunning) return;
    if (this.timer) clearTimeout(this.timer);

    // Recursive timeout is much better than setInterval on mobile
    this.timer = setTimeout(() => {
      this.refresh().finally(() => this.scheduleNext());
    }, this.interval);
  }

  async refresh(force = false) {
    if (
      !force &&
      (this.isPaused || !this.isRunning || document.hidden || !navigator.onLine)
    ) {
      return;
    }

    try {
      const url = new URL(window.location.href);
      url.searchParams.set("t_refresh", Date.now()); // Strict cache buster

      const response = await fetch(url.toString(), {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "Cache-Control": "no-cache, no-store, must-revalidate",
          Pragma: "no-cache",
          Expires: "0",
        },
      });

      if (!response.ok) throw new Error(`HTTP ${response.status}`);

      const html = await response.text();
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, "text/html");

      this.containers.forEach((id) => {
        const newContent = doc.getElementById(id);
        const currentContent = document.getElementById(id);

        if (newContent && currentContent) {
          // Robust focus check
          if (currentContent.contains(document.activeElement)) return;

          // Only update if content changed
          if (newContent.innerHTML !== currentContent.innerHTML) {
            currentContent.innerHTML = newContent.innerHTML;

            const event = new CustomEvent("dashboard:updated", {
              detail: { id: id },
            });
            document.dispatchEvent(event);
          }
        }
      });

      this.lastRefresh = Date.now();
    } catch (error) {
      console.warn("Silent refresh stalled:", error.message);
    }
  }
}

window.DashboardRefresh = DashboardRefresh;

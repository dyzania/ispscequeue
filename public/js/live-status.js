/**
 * LiveQueueStatus Utility
 * Uses Media Session API + Silent Audio to provide persistent Dynamic Island/Lock Screen status.
 */
class LiveQueueStatus {
  constructor() {
    this.audio = null;
    this.isActive = false;
    this.currentMeta = null;
    // Standard silent base64 (approx 1s of silence)
    this.silentBase64 =
      "data:audio/wav;base64,UklGRigAAABXQVZFZm10IBAAAAABAAEARKwAAIhYAQACABAAZGF0YQQAAAAAAAABAAEAAQAB";
  }

  async start(ticketMeta) {
    if (this.isActive) {
      this.update(ticketMeta);
      return;
    }

    try {
      // Create audio if it doesn't exist
      if (!this.audio) {
        this.audio = new Audio(this.silentBase64);
        this.audio.loop = true;
        this.audio.id = "live-status-audio";
        // Append to body (hidden) to prevent cleanup
        this.audio.style.display = "none";
        document.body.appendChild(this.audio);
      }

      if ("mediaSession" in navigator) {
        this.setupControls();
        this.update(ticketMeta);
      }

      await this.audio.play();
      this.isActive = true;

      if ("mediaSession" in navigator) {
        navigator.mediaSession.playbackState = "playing";
      }

      console.log("Live Status Activated (Audio Playing)");
      return true;
    } catch (err) {
      console.error("Live Status failed to start:", err);
      return false;
    }
  }

  setupControls() {
    if (!("mediaSession" in navigator)) return;

    const handlers = {
      play: () => this.audio.play(),
      pause: () => this.audio.pause(),
      stop: () => this.stop(),
    };

    for (const [action, handler] of Object.entries(handlers)) {
      try {
        navigator.mediaSession.setActionHandler(action, handler);
      } catch (error) {
        console.warn(`MediaSession action "${action}" not supported.`);
      }
    }
  }

  update(meta) {
    if (!("mediaSession" in navigator) || !meta) return;

    this.currentMeta = meta;

    let title = `Ticket: ${meta.ticket_number}`;
    let artist = "E-Queue Live Status";

    if (meta.status === "called") {
      title = `📢 GO TO WINDOW ${meta.window_number}!`;
      artist = `Proceed to ${meta.window_name}`;
    } else if (meta.status === "serving") {
      title = `✨ Being Served at W${meta.window_number}`;
      artist = "Processing your request...";
    } else {
      const posDisplay = meta.position + 1 || "?";
      title = `📍 Position: #${posDisplay} (~${meta.estimated_wait}m)`;
      artist = `${meta.service_name}`;
    }

    // Attempt to use a global icon if possible, otherwise skip artwork
    const artwork = [];
    const iconPath = window.EQUEUE_BASE_URL
      ? `${window.EQUEUE_BASE_URL}/assets/img/icon-512.png`
      : null;
    if (iconPath) {
      artwork.push({ src: iconPath, sizes: "512x512", type: "image/png" });
    }

    navigator.mediaSession.metadata = new MediaMetadata({
      title: title,
      artist: artist,
      album: "E-Queue LIVE",
      artwork: artwork,
    });

    // Keep state active
    navigator.mediaSession.playbackState = "playing";
  }

  stop() {
    if (this.audio) {
      this.audio.pause();
      if ("mediaSession" in navigator) {
        navigator.mediaSession.playbackState = "paused";
      }
    }
    this.isActive = false;
  }
}

// Global instance
window.LiveStatus = new LiveQueueStatus();

export function initIdleLogout() {
    const idleMinutes = Number(document.querySelector('meta[name="idle-logout-minutes"]')?.getAttribute('content') || 0);
    if (!Number.isFinite(idleMinutes) || idleMinutes <= 0) return;

    const idleMs = idleMinutes * 60 * 1000;
    const syncKey = 'monitoring:last-activity-at';
    const forceLogoutKey = 'monitoring:force-logout-at';
    const loginUrl = document.querySelector('meta[name="monitoring-login-url"]')?.getAttribute('content') || '/login';
    const logoutUrl = document.querySelector('meta[name="monitoring-logout-url"]')?.getAttribute('content') || '/logout';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    let lastActivityAt = Date.now();
    let hiddenSince = document.hidden ? Date.now() : null;
    let isLoggingOut = false;
    let lastSyncedAt = 0;

    const syncActivity = (ts) => {
        if (ts - lastSyncedAt < 5000) return;
        lastSyncedAt = ts;
        try {
            localStorage.setItem(syncKey, String(ts));
        } catch (_) {
            // no-op
        }
    };

    const markActivity = () => {
        if (isLoggingOut) return;
        const now = Date.now();
        lastActivityAt = now;
        syncActivity(now);
    };

    const pullSharedActivity = () => {
        try {
            const value = localStorage.getItem(syncKey);
            const parsed = Number(value);
            if (Number.isFinite(parsed) && parsed > lastActivityAt) {
                lastActivityAt = parsed;
            }
        } catch (_) {
            // no-op
        }
    };

    const logoutDueToIdle = async () => {
        if (isLoggingOut) return;
        isLoggingOut = true;

        try {
            localStorage.setItem(forceLogoutKey, String(Date.now()));
        } catch (_) {
            // no-op
        }

        try {
            await fetch(logoutUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });
        } catch (_) {
            // no-op
        }

        window.location.href = loginUrl;
    };

    const checkIdleTimeout = () => {
        if (isLoggingOut) return;
        pullSharedActivity();

        const now = Date.now();
        if (hiddenSince !== null && (now - hiddenSince) >= idleMs) {
            logoutDueToIdle();
            return;
        }

        if ((now - lastActivityAt) >= idleMs) {
            logoutDueToIdle();
        }
    };

    ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart', 'click'].forEach((eventName) => {
        window.addEventListener(eventName, markActivity, { passive: true });
    });

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            hiddenSince = Date.now();
            return;
        }

        const now = Date.now();
        if (hiddenSince !== null && (now - hiddenSince) >= idleMs) {
            logoutDueToIdle();
            return;
        }

        hiddenSince = null;
        markActivity();
    });

    window.addEventListener('storage', (event) => {
        if (event.key === syncKey && event.newValue) {
            const parsed = Number(event.newValue);
            if (Number.isFinite(parsed) && parsed > lastActivityAt) {
                lastActivityAt = parsed;
            }
            return;
        }

        if (event.key === forceLogoutKey && event.newValue) {
            window.location.href = loginUrl;
        }
    });

    markActivity();
    pullSharedActivity();

    const idleCheckIntervalMs = Math.min(15000, Math.max(3000, Math.floor(idleMs / 6)));
    const idleTimer = setInterval(checkIdleTimeout, idleCheckIntervalMs);
    window.addEventListener('beforeunload', () => clearInterval(idleTimer));
}

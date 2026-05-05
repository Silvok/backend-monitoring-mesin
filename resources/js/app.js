import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

window.ensureEcho = async function ensureEcho() {
    if (window.Echo) {
        return window.Echo;
    }

    const module = await import('./echo');
    return module.initEcho();
};

window.ensureChartJs = function ensureChartJs() {
    if (window.Chart) return Promise.resolve(window.Chart);
    if (!window.__chartJsPromise) {
        window.__chartJsPromise = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js';
            script.defer = true;
            script.onload = () => resolve(window.Chart);
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }
    return window.__chartJsPromise;
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('header[data-fixed-realtime="1"]') || window.location.pathname.replace(/\/+$/, '') === '/monitoring-mesin') {
        import('./features/layout-runtime').then((module) => {
            module.initFixedRealtimeHeaderSpacer();
            module.initMonitoringHeaderClock();
        });
    }

    const idleMinutes = Number(document.querySelector('meta[name="idle-logout-minutes"]')?.getAttribute('content') || 0);
    if (Number.isFinite(idleMinutes) && idleMinutes > 0) {
        import('./features/idle-logout').then((module) => {
            module.initIdleLogout();
        });
    }
});

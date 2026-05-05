export function initFixedRealtimeHeaderSpacer() {
    const fixedHeader = document.querySelector('header[data-fixed-realtime="1"]');
    const spacer = document.getElementById('fixedHeaderSpacer');
    if (!fixedHeader || !spacer) return;

    const syncFixedHeaderSpacer = () => {
        spacer.style.height = `${fixedHeader.offsetHeight}px`;
    };

    syncFixedHeaderSpacer();
    window.addEventListener('resize', syncFixedHeaderSpacer);
    setTimeout(syncFixedHeaderSpacer, 120);
}

export function initMonitoringHeaderClock() {
    const path = window.location.pathname.replace(/\/+$/, '');
    if (path !== '/monitoring-mesin') return;

    const headerSlot = document.querySelector('header > div > div');
    if (!headerSlot) return;

    const titleEl = headerSlot.querySelector('h2');
    const timeEl = headerSlot.querySelector('#currentTime');
    if (!titleEl || !timeEl) return;

    const liveText = ['Terhubung', 'Live', 'Connected'];
    headerSlot.querySelectorAll('span').forEach((span) => {
        if (!liveText.includes((span.textContent || '').trim())) return;
        const chip = span.closest('div');
        if (chip) chip.remove();
    });

    headerSlot.className = 'w-full min-w-0 flex items-center justify-between gap-2';

    const leftWrap = titleEl.closest('div');
    if (leftWrap) leftWrap.className = 'min-w-0 flex-1';
    titleEl.className = 'font-bold text-base sm:text-xl text-emerald-900 truncate';

    const timeBox = timeEl.parentElement;
    const rightWrap = timeBox && timeBox.parentElement ? timeBox.parentElement : null;
    if (rightWrap) rightWrap.className = 'flex-shrink-0';
    if (timeBox) {
        timeBox.className = 'inline-flex items-center text-[10px] sm:text-sm text-gray-600 bg-gray-50 px-2 py-1.5 rounded-lg border border-gray-200';
    }
    timeEl.className = 'font-semibold whitespace-nowrap tabular-nums';

    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const updateMonitoringHeaderClock = () => {
        const now = new Date();
        timeEl.textContent = `${String(now.getDate()).padStart(2, '0')} ${months[now.getMonth()]} ${now.getFullYear()}, ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
    };

    updateMonitoringHeaderClock();
    setInterval(updateMonitoringHeaderClock, 60000);
}

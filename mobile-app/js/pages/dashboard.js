const SVG = {
    logout: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>',
    home: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>',
    calendar: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>',
    wallet: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>',
    user: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>',
    check: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>',
    download: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>',
    megaphone: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46"/></svg>',
    share: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z"/></svg>',
    news: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"/></svg>',
    book: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>',
    bus: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 00-.879-2.121l-3.992-3.992A2.25 2.25 0 0015.621 7.5H12M2.25 14.25V6.375c0-.621.504-1.125 1.125-1.125h7.5c.621 0 1.125.504 1.125 1.125v7.875"/></svg>',
    clock: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    flag: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5"/></svg>',
    moon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>',
    sun: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>',
    menu: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>',
    chevDown: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>',
    homework: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>',
};

const statusColors = { present: '#059669', absent: '#dc2626', late: '#d97706', sick: '#3b82f6', excused: '#9ca3af' };

function fmt(n) { return new Intl.NumberFormat().format(n); }
function fmtK(n) { return 'K ' + new Intl.NumberFormat().format(n); }
function initial(name) { return (name || '?')[0].toUpperCase(); }

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const raw = atob(base64);
    const arr = new Uint8Array(raw.length);
    for (let i = 0; i < raw.length; i++) arr[i] = raw.charCodeAt(i);
    return arr;
}

export async function renderDashboard(container, api, settings) {
    const user = JSON.parse(localStorage.getItem('user_data') || '{}');
    const children = JSON.parse(localStorage.getItem('children_data') || '[]');

    // Shell first, load data async
    container.innerHTML = `
        <div class="app-shell">
            <div class="app-header">
                <div class="app-header-left">
                    <button class="app-header-btn" id="menu-btn" aria-label="Menu">${SVG.menu}</button>
                    <div class="app-header-avatar">${initial(user.name)}</div>
                    <div>
                        <div class="app-header-name">${user.name || 'Parent'}</div>
                        <div class="app-header-role">${user.relationship ? user.relationship.charAt(0).toUpperCase() + user.relationship.slice(1) : 'Parent'} &middot; ${children.length} child${children.length !== 1 ? 'ren' : ''}</div>
                    </div>
                </div>
                <button class="app-header-btn" id="logout-btn">${SVG.logout}</button>
            </div>

            <!-- Hamburger Drawer -->
            <div class="drawer-overlay" id="drawer-overlay"></div>
            <aside class="drawer" id="drawer">
                <div class="drawer-header">
                    <div class="drawer-avatar">${initial(user.name)}</div>
                    <div>
                        <div class="drawer-name">${user.name || 'Parent'}</div>
                        <div class="drawer-role">${user.email || ''}</div>
                    </div>
                </div>
                <nav class="drawer-nav">
                    <div class="drawer-section-label">Main</div>
                    <a href="#/dashboard" class="drawer-link">${SVG.home}<span>Home</span></a>
                    <a href="#/dashboard/notices" class="drawer-link">${SVG.megaphone}<span>Notices</span></a>
                    <a href="#/dashboard/news" class="drawer-link">${SVG.news}<span>News</span></a>
                    <a href="#/dashboard/events" class="drawer-link">${SVG.calendar}<span>Events</span></a>
                    <a href="#/dashboard/payments" class="drawer-link">${SVG.wallet}<span>Payments</span></a>
                    <a href="#/dashboard/homework" class="drawer-link">${SVG.homework}<span>Homework</span></a>
                    <a href="#/dashboard/library" class="drawer-link">${SVG.book}<span>Library</span></a>
                    <a href="#/dashboard/timetable" class="drawer-link">${SVG.clock}<span>Timetable</span></a>
                    <a href="#/dashboard/report-cards" class="drawer-link">${SVG.download}<span>Report Cards</span></a>
                    <div class="drawer-section-label">My Children</div>
                    ${children.map(c => `<div class="drawer-child">${c.profile_photo ? `<img src="${c.profile_photo}" class="drawer-child-avatar" style="object-fit:cover">` : `<div class="drawer-child-avatar">${initial(c.name)}</div>`}<div><div class="text-sm bold">${c.name}</div><div class="text-xs text-gray">${c.grade || ''} ${c.class ? '- ' + c.class : ''}</div></div></div>`).join('')}
                    <div class="drawer-section-label">Account</div>
                    <a href="#/dashboard/profile" class="drawer-link">${SVG.user}<span>Profile</span></a>
                    <a href="#/dashboard/settings" class="drawer-link">${SVG.sun}<span>Settings</span></a>
                    <button class="drawer-link drawer-logout" id="drawer-logout">${SVG.logout}<span>Sign Out</span></button>
                </nav>
            </aside>

            <div class="app-content" id="main-content">
                <div class="dash-scroll">
                    <div class="kpi-strip"><div class="skeleton skeleton-kpi"></div><div class="skeleton skeleton-kpi"></div><div class="skeleton skeleton-kpi"></div></div>
                    <div class="skeleton skeleton-card"></div>
                    <div class="skeleton skeleton-card" style="height:140px"></div>
                </div>
            </div>
            <nav class="tab-bar">
                <a href="#/dashboard" class="tab-item active">${SVG.home}<span>Home</span></a>
                <a href="#/dashboard/homework" class="tab-item">${SVG.homework}<span>Homework</span></a>
                <a href="#/dashboard/notices" class="tab-item" id="tab-notices">${SVG.megaphone}<span>Notices</span></a>
                <a href="#/dashboard/payments" class="tab-item">${SVG.wallet}<span>Payments</span></a>
                <a href="#/dashboard/profile" class="tab-item">${SVG.user}<span>Profile</span></a>
            </nav>
        </div>
    `;

    // Hamburger menu open/close
    const drawer = document.getElementById('drawer');
    const overlay = document.getElementById('drawer-overlay');
    const openDrawer = () => { drawer.classList.add('open'); overlay.classList.add('open'); };
    const closeDrawer = () => { drawer.classList.remove('open'); overlay.classList.remove('open'); };
    document.getElementById('menu-btn').addEventListener('click', openDrawer);
    overlay.addEventListener('click', closeDrawer);
    // Close drawer on any nav link click
    drawer.querySelectorAll('.drawer-link').forEach(link => {
        link.addEventListener('click', closeDrawer);
    });

    // Logout handlers
    const doLogout = async () => {
        try { await api.logout(); } catch {}
        api.setToken(null);
        localStorage.removeItem('user_data');
        localStorage.removeItem('children_data');
        window.location.hash = '#/login';
    };
    document.getElementById('logout-btn').addEventListener('click', doLogout);
    document.getElementById('drawer-logout').addEventListener('click', doLogout);

    // Route sub-pages
    const hash = window.location.hash;
    const content = document.getElementById('main-content');

    if (hash.includes('/notices')) await renderNotices(content, api);
    else if (hash.includes('/news')) await renderNews(content, api);
    else if (hash.includes('/events')) await renderEvents(content, api, children);
    else if (hash.includes('/payments')) await renderPayments(content, api);
    else if (hash.includes('/homework')) await renderHomeworkPage(content, api, children);
    else if (hash.includes('/library')) await renderLibraryPage(content, api, children);
    else if (hash.includes('/timetable')) await renderTimetablePage(content, api, children);
    else if (hash.includes('/report-cards')) await renderReportCardsPage(content, api, children);
    else if (hash.includes('/settings')) await renderSettings(content, user, api);
    else if (hash.includes('/profile')) await renderProfile(content, user, children, api);
    else await renderHome(content, api, children);

    // Update active tab + drawer link
    document.querySelectorAll('.tab-item').forEach(t => {
        t.classList.toggle('active', hash.startsWith(t.getAttribute('href')));
    });
    document.querySelectorAll('.drawer-link[href]').forEach(l => {
        l.classList.toggle('active', hash.startsWith(l.getAttribute('href')));
    });

    // Badge counts on tabs
    api.getDashboard().then(stats => {
        const noticeTab = document.getElementById('tab-notices');
        if (noticeTab && stats.unread_notices > 0) {
            const badge = document.createElement('span');
            badge.className = 'tab-badge';
            badge.textContent = stats.unread_notices > 9 ? '9+' : stats.unread_notices;
            noticeTab.appendChild(badge);
        }
    }).catch(() => {});
}

async function renderHome(el, api, children) {
    try {
        const [stats, ...childData] = await Promise.all([
            api.getDashboard(),
            ...children.map(c => Promise.all([
                api.getAttendance(c.id),
                api.getFees(c.id),
                api.getResults(c.id),
                api.getHomework(c.id),
                api.getBookLoans(c.id).catch(() => ({ loans: [], active_count: 0, overdue_count: 0 })),
                api.getBusPayments(c.id).catch(() => ({ payments: [], total_balance: 0 })),
            ]).then(([att, fees, results, hw, bookLoans, busPayments]) => ({ child: c, att, fees, results, hw, bookLoans, busPayments })))
        ]);

        let html = '<div class="dash-scroll" id="home-scroll">';
        html += '<div class="ptr-indicator" id="ptr-indicator"><span class="ptr-spinner"></span>Pull down to refresh</div>';

        // KPI Strip
        const attColor = stats.attendance_rate >= 80 ? 'kpi-green' : (stats.attendance_rate >= 60 ? 'kpi-amber' : 'kpi-red');
        const balColor = stats.total_balance > 0 ? 'kpi-red' : 'kpi-green';
        html += `<div class="kpi-strip">
            <div class="kpi"><div class="kpi-val ${attColor}">${stats.attendance_rate}%</div><div class="kpi-lbl">Attendance</div></div>
            <div class="kpi"><div class="kpi-val ${balColor}">${fmtK(stats.total_balance)}</div><div class="kpi-lbl">Balance</div></div>
            <div class="kpi"><div class="kpi-val kpi-navy">${stats.pending_homework + stats.overdue_homework}</div><div class="kpi-lbl">Homework</div></div>
        </div>`;

        // Per-child sections
        for (const cd of childData) {
            html += renderChildCard(cd, api);
        }

        html += '</div>';
        el.innerHTML = html;

        // Bind homework submit buttons
        el.querySelectorAll('.btn-submit-hw').forEach(btn => {
            btn.addEventListener('click', async () => {
                const childId = btn.dataset.child;
                const hwId = btn.dataset.hw;
                const title = btn.dataset.title;

                // Show inline form
                const card = btn.closest('.hw-card');
                if (card.querySelector('.hw-submit-form')) return; // already open

                const form = document.createElement('div');
                form.className = 'hw-submit-form';
                form.style.cssText = 'margin-top:8px;border-top:1px solid var(--border);padding-top:8px';
                form.innerHTML = `
                    <textarea placeholder="Add a comment (optional)" rows="2" style="width:100%;padding:8px;border:1px solid var(--border);border-radius:6px;font-size:0.82rem;font-family:inherit;resize:vertical"></textarea>
                    <div style="background:rgba(30,58,95,0.05);border-radius:6px;padding:8px 10px;margin:6px 0;border-left:3px solid var(--navy)">
                        <div class="text-xs text-gray">Save file as: <strong style="color:var(--text)">ChildName_Subject_HW${hwId}.pdf</strong></div>
                    </div>
                    <div class="file-upload-area" id="hw-file-area">
                        <input type="file" id="hw-file-input" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                        <div class="file-upload-label"><strong>Tap to attach</strong> a photo or file</div>
                    </div>
                    <div id="hw-file-preview" style="display:none"></div>
                    <div style="display:flex;gap:8px;margin-top:6px">
                        <button class="hw-confirm" style="flex:1;background:var(--green);color:#fff;border:none;padding:8px;border-radius:6px;font-size:0.78rem;font-weight:600;cursor:pointer">Submit Homework</button>
                        <button class="hw-cancel" style="background:transparent;color:var(--text3);border:1px solid var(--border);padding:8px 12px;border-radius:6px;font-size:0.78rem;cursor:pointer">Cancel</button>
                    </div>
                `;
                // File upload handling
                const fileArea = form.querySelector('#hw-file-area');
                const fileInput = form.querySelector('#hw-file-input');
                const filePreview = form.querySelector('#hw-file-preview');
                fileArea.addEventListener('click', () => fileInput.click());
                fileInput.addEventListener('change', () => {
                    const file = fileInput.files[0];
                    if (file) {
                        fileArea.style.display = 'none';
                        filePreview.style.display = '';
                        filePreview.innerHTML = `<div class="file-upload-preview"><span class="file-name">${file.name}</span><span class="text-xs text-gray">${(file.size / 1024).toFixed(0)} KB</span><button class="file-upload-remove" type="button">&times;</button></div>`;
                        filePreview.querySelector('.file-upload-remove').onclick = () => {
                            fileInput.value = '';
                            filePreview.style.display = 'none';
                            fileArea.style.display = '';
                        };
                    }
                });
                card.appendChild(form);
                btn.style.display = 'none';

                form.querySelector('.hw-cancel').onclick = () => { form.remove(); btn.style.display = ''; };
                form.querySelector('.hw-confirm').onclick = async () => {
                    const content = form.querySelector('textarea').value;
                    const file = form.querySelector('#hw-file-input')?.files[0] || null;
                    const confirmBtn = form.querySelector('.hw-confirm');
                    confirmBtn.textContent = 'Submitting...';
                    confirmBtn.disabled = true;
                    try {
                        await api.submitHomework(childId, hwId, content, file);
                        card.innerHTML = '<div style="padding:8px;text-align:center;color:var(--green);font-weight:600;font-size:0.82rem">Submitted successfully!</div>';
                    } catch (err) {
                        confirmBtn.textContent = err.message;
                        confirmBtn.style.background = 'var(--red)';
                        setTimeout(() => { confirmBtn.textContent = 'Submit Homework'; confirmBtn.style.background = 'var(--green)'; confirmBtn.disabled = false; }, 2000);
                    }
                };
            });
        });
        // Share report card copy buttons
        el.querySelectorAll('.btn-share-rc').forEach(btn => {
            btn.addEventListener('click', () => {
                const text = btn.dataset.text + '\n' + window.location.origin + btn.dataset.url;
                navigator.clipboard.writeText(text).then(() => {
                    btn.textContent = 'Copied!';
                    setTimeout(() => { btn.textContent = 'Copy'; }, 2000);
                }).catch(() => {});
            });
        });

        // Accordion toggles (attendance calendar etc.)
        el.querySelectorAll('.accordion-trigger').forEach(trigger => {
            trigger.addEventListener('click', () => {
                const panel = document.getElementById(trigger.dataset.target);
                if (panel) { panel.classList.toggle('accordion-panel-open'); trigger.classList.toggle('accordion-open'); }
            });
        });

        // Pay Now buttons
        el.querySelectorAll('.btn-pay-now').forEach(btn => {
            btn.addEventListener('click', () => {
                alert('Coming Soon!\n\nMobile money payments will be available shortly. Please pay via the school office for now.');
            });
        });

        // Pull-to-refresh
        const scrollEl = document.querySelector('.app-content');
        const ptrEl = document.getElementById('ptr-indicator');
        let ptrStart = 0, ptrPulling = false;
        if (scrollEl && ptrEl) {
            scrollEl.addEventListener('touchstart', (e) => {
                if (scrollEl.scrollTop <= 0) { ptrStart = e.touches[0].clientY; ptrPulling = true; }
            }, { passive: true });
            scrollEl.addEventListener('touchmove', (e) => {
                if (!ptrPulling) return;
                const diff = e.touches[0].clientY - ptrStart;
                if (diff > 60 && scrollEl.scrollTop <= 0) { ptrEl.classList.add('active'); ptrEl.textContent = 'Release to refresh'; }
            }, { passive: true });
            scrollEl.addEventListener('touchend', () => {
                if (ptrEl.classList.contains('active')) {
                    ptrEl.innerHTML = '<span class="ptr-spinner" style="display:inline-block"></span> Refreshing...';
                    ptrEl.classList.add('refreshing');
                    setTimeout(() => { window.location.hash = '#/dashboard'; window.location.reload(); }, 300);
                }
                ptrPulling = false;
                if (!ptrEl.classList.contains('refreshing')) { ptrEl.classList.remove('active'); ptrEl.textContent = 'Pull down to refresh'; }
            });
        }
    } catch (err) {
        el.innerHTML = `<div class="dash-scroll"><div class="card-empty">${err.message}</div></div>`;
    }
}

function renderChildCard({ child, att, fees, results, hw, bookLoans, busPayments }, api) {
    const balColor = fees.total_balance > 0 ? 'style="color:#fca5a5"' : 'style="color:#6ee7b7"';

    const photoEl = child.profile_photo
        ? `<img src="${child.profile_photo}" alt="${child.name}" class="child-photo">`
        : `<div class="child-avatar">${initial(child.name)}</div>`;

    let html = `<div class="child-card">
        <div class="child-header">
            <div style="display:flex;align-items:center;gap:12px">
                ${photoEl}
                <div><div class="child-name">${child.name}</div><div class="child-meta">${child.grade || ''} ${child.class ? '- ' + child.class : ''}</div></div>
            </div>
            <div class="child-stats">
                ${att.rate > 0 ? `<div class="child-stat"><div class="child-stat-val">${att.rate}%</div><div class="child-stat-lbl">Attend.</div></div>` : ''}
                ${results.average ? `<div class="child-stat"><div class="child-stat-val">${results.average}%</div><div class="child-stat-lbl">Average</div></div>` : ''}
                <div class="child-stat"><div class="child-stat-val" ${balColor}>${fmtK(fees.total_balance)}</div><div class="child-stat-lbl">Balance</div></div>
            </div>
        </div>
        <div class="child-body">`;

    // Attendance
    html += `<div class="section">
        <div class="section-title"><span class="section-dot" style="background:var(--green)"></span>Attendance</div>`;
    if (att.total > 0) {
        const barClass = att.rate >= 80 ? 'bg-green' : (att.rate >= 60 ? 'bg-amber' : 'bg-red');
        html += `<div class="flex-between text-xs mb-2"><span>${att.present + att.late}/${att.total} days</span><span class="bold ${att.rate >= 80 ? 'text-green' : 'text-red'}">${att.rate}%</span></div>
            <div class="progress"><div class="progress-bar ${barClass}" style="width:${Math.min(att.rate, 100)}%"></div></div>
            <div class="stat-grid mt-2">
                <div class="stat-row"><span class="stat-dot" style="background:var(--green)"></span>Present: ${att.present}</div>
                <div class="stat-row"><span class="stat-dot" style="background:var(--red)"></span>Absent: ${att.absent}</div>
                <div class="stat-row"><span class="stat-dot" style="background:var(--amber)"></span>Late: ${att.late}</div>
                <div class="stat-row"><span class="stat-dot" style="background:var(--blue)"></span>Sick: ${att.sick}</div>
            </div>`;
        if (att.current_week && att.current_week.length) {
            const weekLabel = att.school_week !== null && att.school_week !== undefined ? `Week ${att.school_week}` : 'This Week';
            html += `<div class="text-xs text-gray bold mt-3" style="text-transform:uppercase;letter-spacing:0.04em;margin-bottom:6px;display:flex;justify-content:space-between"><span>${weekLabel}</span><span>Mon — Fri</span></div><div class="day-grid" style="display:grid;grid-template-columns:repeat(5,1fr);gap:6px">`;
            for (const d of att.current_week) {
                const bgColor = d.status && d.status !== 'no_record' ? (statusColors[d.status] || '#d1d5db') : '#e5e7eb';
                const symbolText = d.status && d.status !== 'no_record' ? d.symbol : '-';
                const opacity = d.status === null ? 'opacity:0.4' : '';
                html += `<div class="day-cell" style="text-align:center;${opacity}"><span class="day-label">${d.day}</span><span class="day-box" style="background:${bgColor}">${symbolText}</span><span class="day-label" style="margin-top:2px">${d.date}</span></div>`;
            }
            html += '</div>';
        }
        // Monthly calendar view (collapsible)
        if (att.month_calendar && att.month_calendar.length) {
            const calId = 'att-cal-' + (child?.id || Math.random().toString(36).slice(2));
            html += `<button class="accordion-trigger mt-3" data-target="${calId}" style="padding:8px 0;gap:6px">
                <div class="accordion-trigger-left" style="gap:6px"><span class="text-xs text-gray bold" style="text-transform:uppercase;letter-spacing:0.04em">${att.month_name || 'This Month'}</span></div>
                <span class="accordion-chevron">${SVG.chevDown}</span>
            </button>`;
            html += `<div class="accordion-panel" id="${calId}"><div class="month-cal">`;
            const dayHeaders = ['M','T','W','T','F','S','S'];
            for (const dh of dayHeaders) html += `<div class="month-cal-header">${dh}</div>`;
            const firstDow = att.month_calendar[0].dow;
            for (let p = 1; p < firstDow; p++) html += '<div class="month-cal-day empty"></div>';
            for (const d of att.month_calendar) {
                let cls = 'month-cal-day';
                if (d.is_today) cls += ' today';
                if (d.is_weekend) cls += ' weekend';
                else if (d.is_future) cls += ' future';
                else if (d.status) cls += ` ${d.status}`;
                html += `<div class="${cls}">${d.day}</div>`;
            }
            html += '</div></div>';
        }
        if (att.download_url) html += `<a href="${api.downloadUrl(att.download_url)}" target="_blank" class="btn btn-outline mt-3" style="font-size:0.78rem;padding:10px">${SVG.download} Download Attendance PDF</a>`;
    } else {
        html += '<div class="text-sm text-gray">No attendance records this term.</div>';
    }
    html += '</div>';

    // Fees
    html += `<div class="section">
        <div class="section-title"><span class="section-dot" style="background:var(--amber)"></span>Tuition Fees</div>
        <div class="fee-terms">`;
    for (const t of fees.terms || []) {
        const amtColor = t.status === 'paid' ? 'text-green' : (t.status === 'unpaid' ? 'text-red' : (t.status === 'partial' ? 'text-amber' : 'text-gray'));
        const statusBadge = t.status === 'no_fee' ? '<span class="badge badge-gray">N/A</span>'
            : t.status === 'paid' ? '<span class="badge badge-green">Paid</span>'
            : t.status === 'partial' ? '<span class="badge badge-amber">Partial</span>'
            : '<span class="badge badge-red">Unpaid</span>';
        html += `<div class="fee-term">
            <div class="fee-term-name">${t.term}</div>
            <div class="fee-term-amount ${amtColor}">${t.tuition_fee > 0 ? fmtK(t.tuition_fee) : '-'}</div>
            <div class="mt-2">${statusBadge}</div>
        </div>`;
    }
    html += '</div>';
    if (fees.statement_url) html += `<a href="${api.downloadUrl(fees.statement_url)}" target="_blank" class="btn btn-outline mt-3" style="font-size:0.78rem;padding:10px">${SVG.download} Download Fee Statement</a>`;
    if (fees.total_balance > 0) {
        html += `<button class="btn btn-primary mt-3 btn-pay-now" data-child="${child.id}" data-name="${child.name}" data-balance="${fees.total_balance}" style="font-size:0.85rem;padding:12px;border-radius:var(--radius-sm)">
            ${SVG.wallet} Pay Now &middot; ${fmtK(fees.total_balance)} Due
        </button>`;
    }
    html += '</div>';

    // Results - full view (locked if fees not fully paid)
    const resultsLocked = fees.total_balance > 0;
    if (results.total_subjects > 0) {
        html += `<div class="section" style="${resultsLocked ? 'position:relative;overflow:hidden' : ''}">
            <div class="section-title"><span class="section-dot" style="background:var(--purple)"></span>Academic Results ${resultsLocked ? '<span class="badge badge-red" style="margin-left:6px">🔒 Locked</span>' : ''}</div>
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:12px">
                <div style="text-align:center"><div class="mono bold" style="font-size:1.6rem;${(results.average >= 50) ? 'color:var(--green)' : 'color:var(--red)'}">${results.average}%</div><div class="text-xs text-gray">Average</div></div>
                <div class="text-xs text-gray"><div>Highest: <strong>${results.highest}%</strong></div><div>Lowest: <strong>${results.lowest}%</strong></div><div>${results.total_subjects} subjects</div></div>
            </div>`;
        // Grade distribution
        if (results.grade_distribution && Object.keys(results.grade_distribution).length) {
            html += '<div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:12px">';
            for (const [grade, count] of Object.entries(results.grade_distribution)) {
                const gc = ['A+','A'].includes(grade) ? 'badge-green' : ['B+','B'].includes(grade) ? 'badge-blue' : ['C+','C'].includes(grade) ? 'badge-amber' : 'badge-red';
                html += `<span class="badge ${gc}">${grade}: ${count}</span>`;
            }
            html += '</div>';
        }
        // Subject-by-subject table
        if (results.subjects && results.subjects.length) {
            html += '<div style="border:1px solid var(--border);border-radius:8px;overflow:hidden">';
            html += '<div style="display:grid;grid-template-columns:1fr auto auto;background:var(--navy);color:#fff;font-size:0.68rem;font-weight:600;text-transform:uppercase;letter-spacing:0.03em">';
            html += '<div style="padding:8px 10px">Subject</div><div style="padding:8px 10px;text-align:center">Marks</div><div style="padding:8px 10px;text-align:center">Grade</div></div>';
            for (let i = 0; i < results.subjects.length; i++) {
                const s = results.subjects[i];
                const bg = i % 2 === 0 ? '' : 'background:#f9fafb';
                const mc = s.marks >= 50 ? 'color:var(--green)' : 'color:var(--red)';
                const gc = ['A+','A'].includes(s.grade) ? 'badge-green' : ['B+','B'].includes(s.grade) ? 'badge-blue' : ['C+','C'].includes(s.grade) ? 'badge-amber' : 'badge-red';
                html += `<div style="display:grid;grid-template-columns:1fr auto auto;font-size:0.82rem;${bg}">
                    <div style="padding:8px 10px;font-weight:500">${s.subject || 'N/A'}</div>
                    <div style="padding:8px 10px;text-align:center;font-weight:700;${mc}" class="mono">${s.marks}%</div>
                    <div style="padding:8px 10px;text-align:center"><span class="badge ${gc}">${s.grade}</span></div>
                </div>`;
            }
            html += '</div>';
        }
        if (resultsLocked) {
            html += `<div style="position:absolute;inset:0;top:30px;display:flex;flex-direction:column;align-items:center;justify-content:center;background:rgba(255,255,255,0.85);backdrop-filter:blur(4px);border-radius:8px">
                <div style="font-size:1.5rem;margin-bottom:6px">🔒</div>
                <div class="text-sm bold text-red">Fees Outstanding</div>
                <div class="text-xs text-gray" style="margin-top:4px;text-align:center">Clear your fee balance to view results</div>
            </div>`;
        }
        html += '</div>';
    } else {
        html += '<div class="section"><div class="section-title"><span class="section-dot" style="background:var(--purple)"></span>Academic Results</div><div class="text-sm text-gray">No results recorded yet.</div></div>';
    }

    // Book Loans (P2)
    if (bookLoans && bookLoans.loans && bookLoans.loans.length) {
        html += `<div class="section">
            <div class="section-title"><span class="section-dot" style="background:var(--blue)"></span>Library Books <span class="text-xs text-gray" style="font-weight:400">(${bookLoans.active_count} active${bookLoans.overdue_count > 0 ? ', ' + bookLoans.overdue_count + ' overdue' : ''})</span></div>
            <div class="hw-list">`;
        for (const loan of bookLoans.loans.slice(0, 5)) {
            const badge = loan.status === 'returned' ? '<span class="badge badge-green">Returned</span>'
                : loan.is_overdue ? `<span class="badge badge-red">Overdue ${loan.days_overdue}d</span>`
                : `<span class="badge badge-blue">${loan.days_left >= 0 ? loan.days_left + 'd left' : 'Active'}</span>`;
            const fineInfo = loan.fine_amount > 0 ? `<div class="text-xs ${loan.fine_paid ? 'text-green' : 'text-red'}" style="margin-top:2px">Fine: K ${loan.fine_amount}${loan.fine_paid ? ' (paid)' : ''}</div>` : '';
            html += `<div class="hw-card" style="flex-direction:column;align-items:stretch">
                <div style="display:flex;justify-content:space-between;align-items:flex-start">
                    <div><div class="hw-title">${loan.book_title}</div><div class="hw-sub">Borrowed ${loan.lent_date} &middot; Due ${loan.due_date}</div>${fineInfo}</div>
                    <div>${badge}</div>
                </div>
            </div>`;
        }
        html += '</div></div>';
    }

    // Bus Payments (P3)
    if (busPayments && busPayments.payments && busPayments.payments.length) {
        html += `<div class="section">
            <div class="section-title"><span class="section-dot" style="background:var(--amber)"></span>Bus/Transport Fees</div>
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
                <div class="text-xs text-gray">Total: <strong class="text-navy">${fmtK(busPayments.total_amount)}</strong></div>
                <div class="text-xs text-gray">Paid: <strong class="text-green">${fmtK(busPayments.total_paid)}</strong></div>
                <div class="text-xs text-gray">Balance: <strong class="${busPayments.total_balance > 0 ? 'text-red' : 'text-green'}">${fmtK(busPayments.total_balance)}</strong></div>
            </div>
            <div class="hw-list">`;
        for (const bp of busPayments.payments) {
            const badge = bp.status === 'paid' ? '<span class="badge badge-green">Paid</span>'
                : bp.status === 'partial' ? '<span class="badge badge-amber">Partial</span>'
                : '<span class="badge badge-red">Unpaid</span>';
            html += `<div class="hw-card">
                <div><div class="hw-title">${bp.month} ${bp.year}</div><div class="hw-sub">${fmtK(bp.amount_paid)} / ${fmtK(bp.amount)}</div></div>
                <div>${badge}</div>
            </div>`;
        }
        html += '</div></div>';
    }

    // Homework with submit + feedback (P8)
    if (hw.homework && hw.homework.length) {
        html += `<div class="section">
            <div class="section-title"><span class="section-dot" style="background:var(--amber)"></span>Homework <span class="text-xs text-gray" style="font-weight:400">(${hw.total_submitted}/${hw.total_assigned})</span></div>
            <div class="hw-list">`;
        for (const h of hw.homework.slice(0, 3)) {
            const badge = h.submitted ? `<span class="badge badge-green">Done${h.marks !== null ? ' &middot; ' + h.marks : ''}</span>`
                : h.is_overdue ? '<span class="badge badge-red">Overdue</span>'
                : h.is_due_soon ? '<span class="badge badge-amber">Due Soon</span>'
                : '<span class="badge badge-blue">Pending</span>';
            const submitBtn = !h.submitted
                ? `<button class="btn-submit-hw" data-child="${child.id}" data-hw="${h.id}" data-title="${h.title}" style="background:${h.is_overdue ? 'var(--amber)' : 'var(--navy)'};color:#fff;border:none;padding:6px 12px;border-radius:6px;font-size:0.7rem;font-weight:600;cursor:pointer;margin-top:6px">${h.is_overdue ? 'Submit Late' : 'Submit'}</button>`
                : '';
            const dlBtn = h.has_file ? `<a href="${api.downloadUrl(h.download_url)}" target="_blank" style="color:var(--blue);font-size:0.7rem;font-weight:600;text-decoration:none">Download</a>` : '';
            // P8: Show teacher feedback if graded
            let feedbackHtml = '';
            if (h.submitted && h.graded_at) {
                feedbackHtml += '<div class="hw-feedback">';
                feedbackHtml += `<div class="hw-feedback-header">Graded ${h.graded_at}${h.marks !== null ? ' &middot; Score: ' + h.marks : ''}</div>`;
                if (h.feedback) feedbackHtml += `<div class="hw-feedback-text">${h.feedback}</div>`;
                if (h.teacher_notes) feedbackHtml += `<div class="hw-feedback-notes">Note: ${h.teacher_notes}</div>`;
                feedbackHtml += '</div>';
            }
            html += `<div class="hw-card" style="flex-direction:column;align-items:stretch">
                <div style="display:flex;justify-content:space-between;align-items:flex-start">
                    <div><div class="hw-title">${h.title}</div><div class="hw-sub">${h.subject} &middot; Due ${h.due_date}</div></div>
                    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px">${badge}${dlBtn}</div>
                </div>
                ${feedbackHtml}
                ${submitBtn}
            </div>`;
        }
        if (hw.homework.length > 3) {
            html += `<a href="#/dashboard/homework" style="display:block;text-align:center;padding:10px;font-size:0.78rem;font-weight:600;color:var(--navy);text-decoration:none;border-top:1px solid var(--border);margin-top:8px">View All Homework (${hw.total_assigned}) &rarr;</a>`;
        }
        html += '</div></div>';
    }


    html += '</div></div>';
    return html;
}

async function renderNotices(el, api) {
    try {
        const allNotices = await api.getNotices();

        let html = '<div class="dash-scroll">';

        // Filter bar
        html += `<div style="display:flex;gap:6px;overflow-x:auto;padding-bottom:10px;margin-bottom:12px;-webkit-overflow-scrolling:touch">
            <button class="notice-filter active" data-filter="all" style="flex-shrink:0;padding:6px 14px;border-radius:20px;border:1px solid var(--border);background:var(--navy);color:#fff;font-size:0.72rem;font-weight:600;cursor:pointer;font-family:inherit">All</button>
            <button class="notice-filter" data-filter="urgent" style="flex-shrink:0;padding:6px 14px;border-radius:20px;border:1px solid var(--border);background:var(--card);color:var(--text2);font-size:0.72rem;font-weight:600;cursor:pointer;font-family:inherit">Urgent</button>
            <button class="notice-filter" data-filter="important" style="flex-shrink:0;padding:6px 14px;border-radius:20px;border:1px solid var(--border);background:var(--card);color:var(--text2);font-size:0.72rem;font-weight:600;cursor:pointer;font-family:inherit">Important</button>
            <button class="notice-filter" data-filter="student" style="flex-shrink:0;padding:6px 14px;border-radius:20px;border:1px solid var(--border);background:var(--card);color:var(--text2);font-size:0.72rem;font-weight:600;cursor:pointer;font-family:inherit">My Child</button>
            <button class="notice-filter" data-filter="school" style="flex-shrink:0;padding:6px 14px;border-radius:20px;border:1px solid var(--border);background:var(--card);color:var(--text2);font-size:0.72rem;font-weight:600;cursor:pointer;font-family:inherit">School-wide</button>
        </div>`;

        html += '<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--red)"></span>School Notices <span class="text-xs text-gray" style="font-weight:400" id="notice-count">(${allNotices.length})</span></div></div><div class="card-body" id="notices-list">';
        html += '</div></div></div>';

        el.innerHTML = html;

        function renderNoticeList(notices) {
            const listEl = document.getElementById('notices-list');
            const countEl = document.getElementById('notice-count');
            if (countEl) countEl.textContent = `(${notices.length})`;

            if (notices.length === 0) {
                listEl.innerHTML = '<div class="card-empty">No notices match the filter.</div>';
                return;
            }

            let items = '';
            for (const n of notices) {
                const priorityBadge = n.priority === 'urgent' ? '<span class="badge badge-red">Urgent</span>'
                    : n.priority === 'important' ? '<span class="badge badge-amber">Important</span>'
                    : '<span class="badge badge-gray">Normal</span>';
                const targetBadge = `<span class="badge badge-blue">${n.target}</span>`;

                items += `<div class="list-item" style="flex-direction:column;align-items:stretch;gap:8px">
                    <div style="display:flex;align-items:center;gap:8px">
                        <div style="flex:1;min-width:0">
                            <div class="list-title">${n.title}</div>
                            <div class="list-sub">${n.posted_by} &middot; ${n.date} &middot; ${n.relative}</div>
                        </div>
                        <div style="display:flex;gap:4px;flex-shrink:0">${priorityBadge} ${targetBadge}</div>
                    </div>
                    <div class="text-sm" style="color:var(--text2);line-height:1.6">${n.body}</div>
                    <div style="display:flex;gap:8px;align-items:center">
                        ${n.has_attachment ? `<a href="${n.attachment_url}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;background:rgba(59,130,246,0.1);color:var(--blue);font-size:0.7rem;font-weight:600;text-decoration:none">${SVG.download} File</a>` : ''}
                        <button class="share-notice" data-title="${n.title.replace(/"/g, '&quot;')}" data-body="${n.body.substring(0, 150).replace(/"/g, '&quot;')}" style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;background:rgba(5,150,105,0.1);color:var(--green);font-size:0.7rem;font-weight:600;border:none;cursor:pointer;font-family:inherit">${SVG.share} Share</button>
                    </div>
                </div>`;
            }
            listEl.innerHTML = items;

            // Bind share buttons
            listEl.querySelectorAll('.share-notice').forEach(btn => {
                btn.addEventListener('click', () => {
                    const title = btn.dataset.title;
                    const body = btn.dataset.body;
                    const text = `${title}\n\n${body}\n\n— St. Francis of Assisi School`;
                    if (navigator.share) {
                        navigator.share({ title, text }).catch(() => {});
                    } else {
                        navigator.clipboard.writeText(text).then(() => {
                            btn.innerHTML = '${SVG.check} Copied!';
                            setTimeout(() => { btn.innerHTML = '${SVG.share} Share'; }, 2000);
                        });
                    }
                });
            });
        }

        // Initial render
        renderNoticeList(allNotices);

        // Filter buttons
        el.querySelectorAll('.notice-filter').forEach(btn => {
            btn.addEventListener('click', () => {
                el.querySelectorAll('.notice-filter').forEach(b => {
                    b.style.background = 'var(--card)';
                    b.style.color = 'var(--text2)';
                    b.classList.remove('active');
                });
                btn.style.background = 'var(--navy)';
                btn.style.color = '#fff';
                btn.classList.add('active');

                const filter = btn.dataset.filter;
                if (filter === 'all') renderNoticeList(allNotices);
                else if (filter === 'urgent') renderNoticeList(allNotices.filter(n => n.priority === 'urgent'));
                else if (filter === 'important') renderNoticeList(allNotices.filter(n => n.priority === 'important'));
                else if (filter === 'student') renderNoticeList(allNotices.filter(n => n.target_type === 'student'));
                else if (filter === 'school') renderNoticeList(allNotices.filter(n => n.target_type === 'school'));
            });
        });
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

async function renderEvents(el, api, children) {
    try {
        const [events, calendar] = await Promise.all([
            api.getEvents(),
            api.getSchoolCalendar().catch(() => ({ terms: [], year: null })),
        ]);

        let html = '<div class="dash-scroll">';

        // Academic Calendar — collapsible terms
        if (calendar.terms && calendar.terms.length) {
            html += `<div class="card">
                <div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--navy)"></span>Academic Calendar ${calendar.year || ''}</div></div>
                <div class="card-body" style="padding:0">`;
            for (let ti = 0; ti < calendar.terms.length; ti++) {
                const t = calendar.terms[ti];
                const isActive = t.is_active;
                const termId = 'term-' + ti;
                html += `<div class="accordion-section${ti < calendar.terms.length - 1 ? ' accordion-bordered' : ''}">
                    <button class="accordion-trigger${isActive ? ' accordion-open' : ''}" data-target="${termId}">
                        <div class="accordion-trigger-left">
                            <div class="accordion-trigger-icon" style="background:${isActive ? 'var(--green)' : 'var(--navy)'}">
                                ${t.name.replace(/Term\s*/i, 'T')}
                            </div>
                            <div>
                                <div class="accordion-trigger-title">${t.name}${isActive ? '<span class="badge badge-green" style="margin-left:8px">Current</span>' : ''}</div>
                                <div class="accordion-trigger-sub">${t.start_date || '?'} — ${t.end_date || '?'}</div>
                            </div>
                        </div>
                        <div class="accordion-trigger-right">
                            ${t.total_weeks ? `<span class="badge badge-blue">${t.total_weeks} wks</span>` : ''}
                            <span class="accordion-chevron">${SVG.chevDown}</span>
                        </div>
                    </button>
                    <div class="accordion-panel${isActive ? ' accordion-panel-open' : ''}" id="${termId}">`;
                if (t.weeks && t.weeks.length) {
                    for (const w of t.weeks) {
                        const isCurrent = w.is_current;
                        html += `<div class="cal-week-row${isCurrent ? ' cal-week-current' : ''}">
                            <div class="cal-week-num">${w.label}</div>
                            <div class="cal-week-dates">${w.start} — ${w.end}</div>
                            ${isCurrent ? '<span class="badge badge-green">Now</span>' : ''}
                        </div>`;
                    }
                } else {
                    html += '<div class="card-empty" style="padding:16px">No week data available.</div>';
                }
                html += '</div></div>';
            }
            html += '</div></div>';
        }

        // Upcoming Events — collapsible
        html += `<div class="card">
            <button class="accordion-trigger accordion-open accordion-card-head" data-target="events-panel">
                <div class="accordion-trigger-left">
                    <span class="section-dot" style="background:var(--purple)"></span>
                    <span class="card-title" style="margin:0">Upcoming Events</span>
                    <span class="badge badge-blue" style="margin-left:6px">${events.length}</span>
                </div>
                <span class="accordion-chevron">${SVG.chevDown}</span>
            </button>
            <div class="accordion-panel accordion-panel-open" id="events-panel">`;
        if (events.length === 0) { html += '<div class="card-empty">No upcoming events.</div>'; }
        else {
            for (const e of events) {
                html += `<div class="event-card">
                    <div class="event-card-date"><div class="event-day">${e.day}</div><div class="event-month">${e.month}</div></div>
                    <div class="event-card-body">
                        <div class="event-card-title">${e.title}</div>
                        <div class="event-card-meta">${e.date} &middot; ${e.time} &middot; ${e.relative}</div>
                        ${e.description ? `<div class="event-card-desc">${e.description}</div>` : ''}
                    </div>
                </div>`;
            }
        }
        html += '</div></div>';

        html += '</div>';
        el.innerHTML = html;

        // Bind accordion toggles
        el.querySelectorAll('.accordion-trigger').forEach(trigger => {
            trigger.addEventListener('click', () => {
                const targetId = trigger.dataset.target;
                const panel = document.getElementById(targetId);
                if (!panel) return;
                const isOpen = panel.classList.contains('accordion-panel-open');
                panel.classList.toggle('accordion-panel-open');
                trigger.classList.toggle('accordion-open');
            });
        });
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// P4: News Feed
async function renderNews(el, api) {
    try {
        const news = await api.getNews();
        let html = '<div class="dash-scroll"><div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--purple)"></span>School News</div></div><div class="card-body">';
        if (news.length === 0) { html += '<div class="card-empty">No news articles yet.</div>'; }
        else {
            for (const n of news) {
                html += `<div class="list-item" style="flex-direction:column;align-items:stretch;gap:8px">
                    ${n.image ? `<img src="${n.image}" alt="" style="width:100%;border-radius:var(--radius-sm);max-height:180px;object-fit:cover">` : ''}
                    <div>
                        <div style="display:flex;justify-content:space-between;align-items:flex-start">
                            <div class="list-title" style="flex:1">${n.title}</div>
                            ${n.category ? `<span class="badge badge-blue">${n.category}</span>` : ''}
                        </div>
                        <div class="list-sub">${n.author} &middot; ${n.date} &middot; ${n.relative}</div>
                        <div class="text-sm mt-2" style="color:var(--text2);line-height:1.6">${n.content.length > 300 ? n.content.substring(0, 300) + '...' : n.content}</div>
                    </div>
                </div>`;
            }
        }
        html += '</div></div></div>';
        el.innerHTML = html;
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

async function renderPayments(el, api) {
    try {
        const data = await api.getPayments();
        const children = data.children || [];
        const transactions = data.transactions || [];

        let html = '<div class="dash-scroll">';

        // Overall summary card
        html += `<div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border-radius:var(--radius);padding:20px;color:#fff;margin-bottom:16px;box-shadow:var(--shadow-md)">
            <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;opacity:0.7;margin-bottom:12px">Fee Summary</div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;text-align:center">
                <div><div class="mono" style="font-size:1.2rem;font-weight:700">${fmtK(data.total_fees || 0)}</div><div style="font-size:0.62rem;opacity:0.6;margin-top:2px">Total Fees</div></div>
                <div><div class="mono" style="font-size:1.2rem;font-weight:700;color:#6ee7b7">${fmtK(data.total_paid || 0)}</div><div style="font-size:0.62rem;opacity:0.6;margin-top:2px">Paid</div></div>
                <div><div class="mono" style="font-size:1.2rem;font-weight:700;color:#fca5a5">${fmtK(data.total_balance || 0)}</div><div style="font-size:0.62rem;opacity:0.6;margin-top:2px">Balance</div></div>
            </div>
            ${data.total_fees > 0 ? `<div class="progress mt-3" style="height:8px;background:rgba(255,255,255,0.2)"><div class="progress-bar bg-green" style="width:${Math.min(Math.round((data.total_paid / data.total_fees) * 100), 100)}%;background:#6ee7b7"></div></div>
            <div style="text-align:center;font-size:0.68rem;opacity:0.7;margin-top:6px">${Math.round((data.total_paid / data.total_fees) * 100)}% collected</div>` : ''}
        </div>`;

        // Per-child fee cards with actions
        if (children.length > 0) {
            html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--amber)"></span>Fee Balances</div></div><div class="card-body" style="padding:0">`;
            for (let ci = 0; ci < children.length; ci++) {
                const c = children[ci];
                const statusBadge = c.status === 'paid' ? '<span class="badge badge-green">Paid</span>'
                    : c.status === 'partial' ? '<span class="badge badge-amber">Partial</span>'
                    : '<span class="badge badge-red">Unpaid</span>';
                const border = ci < children.length - 1 ? 'border-bottom:1px solid var(--border);' : '';
                html += `<div style="padding:14px 16px;${border}">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                        <div><div class="list-title">${c.name}</div><div class="list-sub">${c.grade || ''}</div></div>
                        ${statusBadge}
                    </div>
                    <div style="display:flex;gap:16px;margin-bottom:8px">
                        <div class="text-xs text-gray">Fees: <strong class="text-navy mono">${fmtK(c.total_fee)}</strong></div>
                        <div class="text-xs text-gray">Paid: <strong class="text-green mono">${fmtK(c.total_paid)}</strong></div>
                        <div class="text-xs text-gray">Due: <strong class="${c.balance > 0 ? 'text-red' : 'text-green'} mono">${fmtK(c.balance)}</strong></div>
                    </div>
                    ${c.total_fee > 0 ? `<div class="progress" style="margin-bottom:10px"><div class="progress-bar ${c.progress >= 100 ? 'bg-green' : c.progress > 0 ? 'bg-amber' : 'bg-red'}" style="width:${Math.min(c.progress, 100)}%"></div></div>` : ''}
                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                        ${c.balance > 0 ? `<button class="btn-primary btn-pay-child" data-child="${c.id}" data-name="${c.name}" data-balance="${c.balance}" style="flex:1;padding:10px;border:none;border-radius:6px;font-size:0.78rem;font-weight:600;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:6px;background:var(--navy);color:#fff"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg> Pay K ${fmt(c.balance)}</button>` : ''}
                        <a href="${api.downloadUrl(c.statement_url)}" target="_blank" class="btn-outline" style="flex:${c.balance > 0 ? '0' : '1'};padding:10px;border-radius:6px;font-size:0.78rem;font-weight:600;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:6px;border:1px solid var(--border);color:var(--text2);background:var(--card)"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg> Statement</a>
                    </div>
                </div>`;
            }
            html += '</div></div>';
        }

        // Transaction history
        html += `<div class="card">
            <div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--green)"></span>Transaction History</div><span class="badge badge-gray">${transactions.length}</span></div>
            <div class="card-body">`;
        if (transactions.length === 0) {
            html += '<div class="card-empty">No transactions recorded yet.</div>';
        } else {
            for (const t of transactions) {
                const isPayment = t.type === 'payment';
                const iconColor = isPayment ? 'var(--green)' : (t.type === 'refund' ? 'var(--red)' : 'var(--blue)');
                const iconBg = isPayment ? 'rgba(5,150,105,0.1)' : (t.type === 'refund' ? 'rgba(220,38,38,0.1)' : 'rgba(59,130,246,0.1)');
                const amtColor = isPayment ? 'text-green' : (t.type === 'refund' ? 'text-red' : 'text-blue');
                const typeLabel = t.type === 'payment' ? 'Payment' : t.type === 'refund' ? 'Refund' : t.type === 'balance_forward' ? 'Balance Forward' : t.type.charAt(0).toUpperCase() + t.type.slice(1);
                const statusIcon = t.status === 'completed' ? SVG.check : '';

                html += `<div class="list-item" style="gap:10px">
                    <div style="width:32px;height:32px;border-radius:8px;background:${iconBg};color:${iconColor};display:flex;align-items:center;justify-content:center;flex-shrink:0"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${isPayment ? 'M4.5 12.75l6 6 9-13.5' : 'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33'}"/></svg></div>
                    <div style="flex:1;min-width:0">
                        <div class="list-title">${t.student || 'N/A'}</div>
                        <div class="list-sub">${t.date}${t.time ? ' ' + t.time : ''} &middot; ${t.method || typeLabel}${t.reference ? ' &middot; ' + t.reference : ''}</div>
                    </div>
                    <div style="text-align:right;flex-shrink:0">
                        <div class="mono bold ${amtColor}" style="font-size:0.82rem">${isPayment ? '+' : ''}${fmtK(t.amount)}</div>
                        <div style="display:flex;gap:4px;margin-top:4px;justify-content:flex-end">
                            ${t.receipt_url ? `<a href="${api.downloadUrl(t.receipt_url)}" target="_blank" class="share-btn share-copy" style="padding:2px 6px;font-size:0.58rem">Receipt</a>` : ''}
                            ${t.transaction_receipt_url ? `<a href="${api.downloadUrl(t.transaction_receipt_url)}" target="_blank" class="share-btn share-copy" style="padding:2px 6px;font-size:0.58rem">Slip</a>` : ''}
                        </div>
                    </div>
                </div>`;
            }
        }
        html += '</div></div></div>';
        el.innerHTML = html;

        // Bind pay buttons
        el.querySelectorAll('.btn-pay-child').forEach(btn => {
            btn.addEventListener('click', () => {
                alert('Coming Soon!\n\nMobile money payments will be available shortly. Please pay via the school office for now.');
            });
        });

    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

async function renderHomeworkPage(el, api, children) {
    try {
        const allHwData = await Promise.all(
            children.map(c => api.getHomework(c.id).then(hw => ({ child: c, ...hw })).catch(() => ({ child: c, homework: [], total_assigned: 0, total_submitted: 0, subjects: [], weeks: [], compliance: [], current_week: 0, policy_minimum: 2 })))
        );

        // We render per-child; for simplicity use first child's data (most parents have 1-2 children)
        let html = '<div class="dash-scroll">';

        for (const data of allHwData) {
            const c = data.child;
            const hwList = data.homework || [];
            const subjects = data.subjects || [];
            const weeks = data.weeks || [];
            const compliance = data.compliance || [];
            const currentWeek = data.current_week;
            const policyMin = data.policy_minimum || 2;
            const childId = c.id;
            const dataId = `hw-data-${childId}`;

            // Stats
            const totalPending = hwList.filter(h => !h.submitted).length;
            const totalOverdue = hwList.filter(h => h.is_overdue).length;
            const totalGraded = hwList.filter(h => h.graded_at).length;

            // KPI header
            html += `<div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border-radius:var(--radius);padding:16px;color:#fff;margin-bottom:12px;box-shadow:var(--shadow-md)">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
                    <div><div style="font-size:1rem;font-weight:700">${c.name}</div><div style="font-size:0.72rem;opacity:0.65">${c.grade || ''} &middot; Week ${currentWeek ?? '?'}</div></div>
                    <div style="font-size:0.68rem;opacity:0.7;text-transform:uppercase;letter-spacing:0.05em">Policy: ${policyMin} HW/subject/week</div>
                </div>
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;text-align:center">
                    <div><div class="mono" style="font-size:1.2rem;font-weight:700">${data.total_assigned}</div><div style="font-size:0.58rem;opacity:0.6">Assigned</div></div>
                    <div><div class="mono" style="font-size:1.2rem;font-weight:700;color:#6ee7b7">${data.total_submitted}</div><div style="font-size:0.58rem;opacity:0.6">Submitted</div></div>
                    <div><div class="mono" style="font-size:1.2rem;font-weight:700;color:#fca5a5">${totalOverdue}</div><div style="font-size:0.58rem;opacity:0.6">Overdue</div></div>
                    <div><div class="mono" style="font-size:1.2rem;font-weight:700;color:#93c5fd">${totalGraded}</div><div style="font-size:0.58rem;opacity:0.6">Graded</div></div>
                </div>
            </div>`;

            // Weekly Compliance Tracker
            if (compliance.length > 0) {
                // Group by week
                const weeklyComp = {};
                for (const cr of compliance) {
                    if (!weeklyComp[cr.week]) weeklyComp[cr.week] = [];
                    weeklyComp[cr.week].push(cr);
                }

                html += `<div class="card">
                    <button class="accordion-trigger accordion-open accordion-card-head" data-target="compliance-${childId}">
                        <div class="accordion-trigger-left"><span class="section-dot" style="background:var(--amber)"></span><span class="card-title" style="margin:0">Weekly Compliance</span></div>
                        <span class="accordion-chevron">${SVG.chevDown}</span>
                    </button>
                    <div class="accordion-panel accordion-panel-open" id="compliance-${childId}" style="overflow-x:auto">
                        <table style="width:100%;font-size:0.72rem;border-collapse:collapse">
                            <thead><tr style="background:var(--navy);color:#fff">
                                <th style="padding:8px 12px;text-align:left;font-weight:600;white-space:nowrap">Subject</th>`;
                const weekNums = Object.keys(weeklyComp).map(Number).sort((a, b) => a - b);
                for (const wn of weekNums) {
                    const isCurrent = wn === currentWeek;
                    html += `<th style="padding:8px 6px;text-align:center;font-weight:600;white-space:nowrap;${isCurrent ? 'background:rgba(255,255,255,0.15)' : ''}">Wk ${wn}${isCurrent ? ' *' : ''}</th>`;
                }
                html += '</tr></thead><tbody>';

                // Build matrix: subjects as rows, weeks as columns
                const allSubjects = [...new Set(compliance.map(c => c.subject))].sort();
                for (const subj of allSubjects) {
                    html += `<tr style="border-bottom:1px solid var(--border)"><td style="padding:8px 12px;font-weight:600;color:var(--text);white-space:nowrap">${subj}</td>`;
                    for (const wn of weekNums) {
                        const entry = compliance.find(c => c.week === wn && c.subject === subj);
                        const count = entry ? entry.count : 0;
                        const submitted = entry ? entry.submitted : 0;
                        const meets = count >= policyMin;
                        const isCurrent = wn === currentWeek;
                        const cellBg = meets ? 'rgba(5,150,105,0.1)' : (count > 0 ? 'rgba(217,119,6,0.1)' : 'rgba(220,38,38,0.06)');
                        const cellColor = meets ? 'var(--green)' : (count > 0 ? 'var(--amber)' : 'var(--red)');
                        html += `<td style="padding:6px;text-align:center;background:${cellBg};${isCurrent ? 'outline:2px solid var(--navy);outline-offset:-2px' : ''}">
                            <div class="mono" style="font-weight:700;font-size:0.75rem;color:${cellColor}">${count}</div>
                            <div style="font-size:0.55rem;color:var(--text3)">${submitted}/${count}</div>
                        </td>`;
                    }
                    html += '</tr>';
                }
                html += '</tbody></table></div></div>';
            }

            // Filters
            html += `<div class="card" style="overflow:visible">
                <div style="padding:12px 16px;display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                    <select class="form-input hw-filter-week" data-child="${childId}" style="padding:8px 10px;font-size:0.75rem;width:auto;flex:1;min-width:100px">
                        <option value="all">All Weeks</option>
                        ${weeks.map(w => `<option value="${w}"${w === currentWeek ? ' selected' : ''}>Week ${w}${w === currentWeek ? ' (Current)' : ''}</option>`).join('')}
                    </select>
                    <select class="form-input hw-filter-subject" data-child="${childId}" style="padding:8px 10px;font-size:0.75rem;width:auto;flex:1;min-width:100px">
                        <option value="all">All Subjects</option>
                        ${subjects.map(s => `<option value="${s}">${s}</option>`).join('')}
                    </select>
                    <select class="form-input hw-filter-status" data-child="${childId}" style="padding:8px 10px;font-size:0.75rem;width:auto;flex:1;min-width:100px">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="overdue">Overdue</option>
                        <option value="submitted">Submitted</option>
                        <option value="graded">Graded</option>
                    </select>
                </div>
            </div>`;

            // Homework list container
            html += `<div id="hw-list-${childId}"></div>`;

            // Store homework data for JS filtering
            html += `<script type="application/json" id="${dataId}">${JSON.stringify(hwList)}</script>`;
        }

        html += '</div>';
        el.innerHTML = html;

        // Render homework lists and bind filters for each child
        for (const data of allHwData) {
            const childId = data.child.id;
            renderFilteredHomework(el, api, childId, children);

            el.querySelectorAll(`.hw-filter-week[data-child="${childId}"],.hw-filter-subject[data-child="${childId}"],.hw-filter-status[data-child="${childId}"]`).forEach(sel => {
                sel.addEventListener('change', () => renderFilteredHomework(el, api, childId, children));
            });
        }

        // Bind accordion toggles
        el.querySelectorAll('.accordion-trigger').forEach(trigger => {
            trigger.addEventListener('click', () => {
                const panel = document.getElementById(trigger.dataset.target);
                if (panel) { panel.classList.toggle('accordion-panel-open'); trigger.classList.toggle('accordion-open'); }
            });
        });

    } catch (err) {
        el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`;
    }
}

function renderFilteredHomework(el, api, childId, children) {
    const dataEl = document.getElementById(`hw-data-${childId}`);
    const listEl = document.getElementById(`hw-list-${childId}`);
    if (!dataEl || !listEl) return;

    const hwList = JSON.parse(dataEl.textContent);
    const weekFilter = el.querySelector(`.hw-filter-week[data-child="${childId}"]`)?.value || 'all';
    const subjectFilter = el.querySelector(`.hw-filter-subject[data-child="${childId}"]`)?.value || 'all';
    const statusFilter = el.querySelector(`.hw-filter-status[data-child="${childId}"]`)?.value || 'all';

    let filtered = hwList;
    if (weekFilter !== 'all') filtered = filtered.filter(h => String(h.week_number) === weekFilter);
    if (subjectFilter !== 'all') filtered = filtered.filter(h => h.subject === subjectFilter);
    if (statusFilter !== 'all') {
        if (statusFilter === 'pending') filtered = filtered.filter(h => !h.submitted && !h.is_overdue);
        else if (statusFilter === 'overdue') filtered = filtered.filter(h => h.is_overdue);
        else if (statusFilter === 'submitted') filtered = filtered.filter(h => h.submitted && !h.graded_at);
        else if (statusFilter === 'graded') filtered = filtered.filter(h => h.graded_at);
    }

    if (filtered.length === 0) {
        listEl.innerHTML = '<div class="card"><div class="card-empty">No homework matches the selected filters.</div></div>';
        return;
    }

    // Group by week
    const byWeek = {};
    for (const h of filtered) {
        const wk = h.week_number ?? 'Other';
        if (!byWeek[wk]) byWeek[wk] = [];
        byWeek[wk].push(h);
    }

    let html = '';
    const sortedWeeks = Object.keys(byWeek).sort((a, b) => Number(b) - Number(a));

    for (const wk of sortedWeeks) {
        const items = byWeek[wk];
        const submittedCount = items.filter(h => h.submitted).length;

        html += `<div class="card">
            <div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--navy)"></span>Week ${wk}</div><span class="badge ${submittedCount === items.length ? 'badge-green' : 'badge-amber'}">${submittedCount}/${items.length} done</span></div>
            <div class="card-body" style="padding:0">`;

        for (const h of items) {
            const badge = h.submitted
                ? `<span class="badge badge-green">Submitted${h.marks !== null ? ' &middot; ' + h.marks : ''}</span>`
                : h.is_overdue ? '<span class="badge badge-red">Overdue</span>'
                : h.is_due_soon ? '<span class="badge badge-amber">Due Soon</span>'
                : '<span class="badge badge-blue">Pending</span>';

            html += `<div class="list-item" style="flex-direction:column;align-items:stretch;gap:8px;padding:14px 16px">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
                    <div style="flex:1;min-width:0">
                        <div class="list-title">${h.title}</div>
                        <div class="list-sub">${h.subject || ''} &middot; ${h.teacher || ''} &middot; Due ${h.due_date}</div>
                    </div>
                    <div style="flex-shrink:0">${badge}</div>
                </div>`;

            if (h.has_file) {
                html += `<a href="${api.downloadUrl(h.download_url)}" target="_blank" class="share-btn share-copy" style="align-self:flex-start;padding:4px 10px">${SVG.download} Download Assignment</a>`;
            }

            if (h.submitted) {
                html += `<div style="background:rgba(59,130,246,0.05);border:1px solid rgba(59,130,246,0.15);border-radius:var(--radius-sm);padding:10px 12px">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                        <div class="text-xs bold" style="color:var(--blue)">Your Submission</div>
                        <div class="text-xs text-gray">${h.submitted_at || ''}${h.is_late_submission ? ' <span class="badge badge-amber" style="margin-left:4px">Late</span>' : ''}</div>
                    </div>
                    ${h.submission_content ? `<div class="text-sm" style="color:var(--text);margin-top:4px;line-height:1.5">${h.submission_content}</div>` : ''}
                    ${h.submission_file ? `<a href="${h.submission_file}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;margin-top:6px;padding:4px 10px;border-radius:6px;background:rgba(59,130,246,0.1);color:var(--blue);font-size:0.72rem;font-weight:600;text-decoration:none">${SVG.download} ${h.submission_file_name || 'File'}</a>` : ''}
                </div>`;
            }

            if (h.submitted && h.graded_at) {
                html += `<div class="hw-feedback">
                    <div class="hw-feedback-header">Graded ${h.graded_at}${h.marks !== null ? ' &middot; Score: ' + h.marks : ''}</div>
                    ${h.feedback ? `<div class="hw-feedback-text">${h.feedback}</div>` : ''}
                    ${h.teacher_notes ? `<div class="hw-feedback-notes">Note: ${h.teacher_notes}</div>` : ''}
                </div>`;
            } else if (h.submitted && !h.graded_at) {
                html += `<div style="padding:6px 10px;background:rgba(217,119,6,0.06);border-radius:6px;border-left:3px solid var(--amber)"><div class="text-xs bold" style="color:var(--amber)">Awaiting grading</div></div>`;
            }

            if (!h.submitted) {
                html += `<button class="btn btn-primary hw-page-submit" data-child="${childId}" data-hw="${h.id}" data-title="${h.title}" style="padding:10px;font-size:0.82rem;border-radius:6px">${SVG.homework} Submit${h.is_overdue ? ' Late' : ''}</button>`;
            }

            html += '</div>';
        }
        html += '</div></div>';
    }

    listEl.innerHTML = html;

    // Bind submit buttons
    listEl.querySelectorAll('.hw-page-submit').forEach(btn => {
        btn.addEventListener('click', () => {
            showHomeworkSubmitModal(api, el, btn.dataset.child, btn.dataset.hw, btn.dataset.title, children);
        });
    });
}

async function renderLibraryPage(el, api, children) {
    try {
        const allData = await Promise.all(
            children.map(c => api.getBookLoans(c.id).then(d => ({ child: c, ...d })).catch(() => ({ child: c, loans: [], active_count: 0, overdue_count: 0, returned_count: 0, total_borrowed: 0, total_fines: 0, unpaid_fines: 0, categories_read: {} })))
        );

        let html = '<div class="dash-scroll">';

        for (const data of allData) {
            const c = data.child;
            const loans = data.loans || [];
            const active = loans.filter(l => l.status === 'active');
            const overdue = loans.filter(l => l.status === 'overdue');
            const returned = loans.filter(l => l.status === 'returned');
            const categories = data.categories_read || {};

            // KPI Header
            html += `<div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border-radius:var(--radius);padding:16px;color:#fff;margin-bottom:12px;box-shadow:var(--shadow-md)">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
                    <div><div style="font-size:1rem;font-weight:700">${c.name}</div><div style="font-size:0.72rem;opacity:0.65">${c.grade || ''} &middot; Library</div></div>
                    ${data.unpaid_fines > 0 ? `<span class="badge badge-red">K${data.unpaid_fines.toFixed(2)} fines</span>` : '<span class="badge" style="background:rgba(255,255,255,0.15);color:#fff">No fines</span>'}
                </div>
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;text-align:center">
                    <div><div class="mono" style="font-size:1.2rem;font-weight:700">${data.total_borrowed}</div><div style="font-size:0.58rem;opacity:0.6">Total</div></div>
                    <div><div class="mono" style="font-size:1.2rem;font-weight:700;color:#93c5fd">${data.active_count}</div><div style="font-size:0.58rem;opacity:0.6">Active</div></div>
                    <div><div class="mono" style="font-size:1.2rem;font-weight:700;color:#fca5a5">${data.overdue_count}</div><div style="font-size:0.58rem;opacity:0.6">Overdue</div></div>
                    <div><div class="mono" style="font-size:1.2rem;font-weight:700;color:#6ee7b7">${data.returned_count}</div><div style="font-size:0.58rem;opacity:0.6">Returned</div></div>
                </div>
            </div>`;

            // Reading categories breakdown
            if (Object.keys(categories).length > 0) {
                html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--purple)"></span>Reading Categories</div></div>
                    <div style="padding:12px 16px;display:flex;flex-wrap:wrap;gap:6px">`;
                for (const [cat, count] of Object.entries(categories)) {
                    html += `<span class="badge badge-blue" style="padding:4px 10px;font-size:0.68rem">${cat}: ${count}</span>`;
                }
                html += '</div></div>';
            }

            // Overdue alert
            if (overdue.length > 0) {
                html += `<div class="card" style="border-left:4px solid var(--red)">
                    <div class="card-head" style="background:rgba(220,38,38,0.04)"><div class="card-title" style="color:var(--red)"><span class="section-dot" style="background:var(--red)"></span>Overdue Books (${overdue.length})</div></div>
                    <div class="card-body" style="padding:0">`;
                for (const l of overdue) {
                    html += renderLibraryCard(l);
                }
                html += '</div></div>';
            }

            // Currently borrowed
            if (active.length > 0) {
                html += `<div class="card">
                    <div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--blue)"></span>Currently Borrowed (${active.length})</div></div>
                    <div class="card-body" style="padding:0">`;
                for (const l of active) {
                    html += renderLibraryCard(l);
                }
                html += '</div></div>';
            }

            // Return history
            if (returned.length > 0) {
                html += `<div class="card">
                    <button class="accordion-trigger accordion-card-head" data-target="lib-returned-${c.id}">
                        <div class="accordion-trigger-left"><span class="section-dot" style="background:var(--green)"></span><span class="card-title" style="margin:0">Return History (${returned.length})</span></div>
                        <span class="accordion-chevron">${SVG.chevDown}</span>
                    </button>
                    <div class="accordion-panel" id="lib-returned-${c.id}">`;
                for (const l of returned) {
                    html += renderLibraryCard(l);
                }
                html += '</div></div>';
            }

            // Fines summary
            if (data.total_fines > 0) {
                const paidFines = loans.filter(l => l.fine_amount > 0 && l.fine_paid);
                const unpaidFines = loans.filter(l => l.fine_amount > 0 && !l.fine_paid);

                html += `<div class="card">
                    <div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--amber)"></span>Fines</div><span class="badge ${data.unpaid_fines > 0 ? 'badge-red' : 'badge-green'}">K${data.total_fines.toFixed(2)} total</span></div>
                    <div class="card-body" style="padding:0">`;
                for (const l of [...unpaidFines, ...paidFines]) {
                    html += `<div class="list-item" style="gap:10px">
                        <div style="width:32px;height:32px;border-radius:8px;background:${l.fine_paid ? 'rgba(5,150,105,0.1)' : 'rgba(220,38,38,0.1)'};color:${l.fine_paid ? 'var(--green)' : 'var(--red)'};display:flex;align-items:center;justify-content:center;flex-shrink:0"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${l.fine_paid ? 'M4.5 12.75l6 6 9-13.5' : 'M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z'}"/></svg></div>
                        <div style="flex:1;min-width:0">
                            <div class="list-title">${l.book_title}</div>
                            <div class="list-sub">${l.fine_paid ? 'Paid' : 'Unpaid'} &middot; Due ${l.due_date}</div>
                        </div>
                        <div class="mono bold ${l.fine_paid ? 'text-green' : 'text-red'}" style="font-size:0.85rem">K${l.fine_amount.toFixed(2)}</div>
                    </div>`;
                }
                html += '</div></div>';
            }

            // Empty state
            if (loans.length === 0) {
                html += `<div class="card"><div class="card-empty" style="padding:40px 16px">
                    <div style="font-size:2rem;margin-bottom:8px">${SVG.book}</div>
                    <div class="list-title">No Library Activity</div>
                    <div class="text-sm text-gray" style="margin-top:4px">${c.name} hasn't borrowed any books yet.</div>
                </div></div>`;
            }
        }

        html += '</div>';
        el.innerHTML = html;

        // Bind accordion toggles
        el.querySelectorAll('.accordion-trigger').forEach(trigger => {
            trigger.addEventListener('click', () => {
                const panel = document.getElementById(trigger.dataset.target);
                if (panel) { panel.classList.toggle('accordion-panel-open'); trigger.classList.toggle('accordion-open'); }
            });
        });

    } catch (err) {
        el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`;
    }
}

function renderLibraryCard(l) {
    const statusColor = l.status === 'overdue' ? 'var(--red)' : l.status === 'active' ? 'var(--blue)' : 'var(--green)';
    const statusBg = l.status === 'overdue' ? 'rgba(220,38,38,0.08)' : l.status === 'active' ? 'rgba(59,130,246,0.08)' : 'rgba(5,150,105,0.08)';
    const statusLabel = l.status === 'overdue' ? `Overdue ${l.days_overdue}d` : l.status === 'active' ? (l.days_left >= 0 ? `${l.days_left}d left` : 'Active') : 'Returned';

    // Due date urgency bar for active loans
    let urgencyBar = '';
    if (l.status === 'active' && l.days_left !== null) {
        const urgencyColor = l.days_left <= 2 ? 'var(--red)' : l.days_left <= 5 ? 'var(--amber)' : 'var(--green)';
        urgencyBar = `<div style="margin-top:6px;display:flex;align-items:center;gap:6px">
            <div style="flex:1;height:4px;background:var(--border);border-radius:99px;overflow:hidden"><div style="height:100%;width:${Math.max(10, Math.min(100, (14 - l.days_left) / 14 * 100))}%;background:${urgencyColor};border-radius:99px"></div></div>
            <span class="text-xs bold" style="color:${urgencyColor}">${l.days_left}d</span>
        </div>`;
    }

    return `<div class="list-item" style="flex-direction:column;align-items:stretch;gap:6px;padding:14px 16px">
        <div style="display:flex;gap:12px;align-items:flex-start">
            <div style="width:44px;height:56px;border-radius:6px;background:${statusBg};display:flex;align-items:center;justify-content:center;flex-shrink:0;color:${statusColor}">${SVG.book}</div>
            <div style="flex:1;min-width:0">
                <div class="list-title">${l.book_title}</div>
                <div class="list-sub">${l.book_author}${l.book_category ? ' &middot; ' + l.book_category : ''}</div>
                <div style="display:flex;gap:8px;margin-top:6px;flex-wrap:wrap">
                    <span class="text-xs text-gray">Borrowed: <strong>${l.lent_date}</strong></span>
                    <span class="text-xs text-gray">Due: <strong style="color:${l.status === 'overdue' ? 'var(--red)' : 'var(--text)'}">${l.due_date}</strong></span>
                    ${l.returned_at ? `<span class="text-xs text-gray">Returned: <strong class="text-green">${l.returned_at}</strong></span>` : ''}
                </div>
                ${urgencyBar}
            </div>
            <div style="flex-shrink:0">
                <span class="badge" style="background:${statusBg};color:${statusColor}">${statusLabel}</span>
                ${l.fine_amount > 0 ? `<div class="text-xs mono bold ${l.fine_paid ? 'text-green' : 'text-red'}" style="text-align:center;margin-top:4px">K${l.fine_amount.toFixed(2)}</div>` : ''}
            </div>
        </div>
        ${l.book_isbn ? `<div class="text-xs text-gray" style="margin-top:2px">ISBN: ${l.book_isbn}${l.book_shelf ? ' &middot; Shelf: ' + l.book_shelf : ''}</div>` : ''}
        ${l.condition_on_loan ? `<div class="text-xs text-gray">Condition at loan: <em>${l.condition_on_loan}</em>${l.condition_on_return ? ' &rarr; Return: <em>' + l.condition_on_return + '</em>' : ''}</div>` : ''}
        ${l.notes ? `<div class="text-xs text-gray" style="font-style:italic">${l.notes}</div>` : ''}
    </div>`;
}

function showHomeworkSubmitModal(api, pageEl, childId, hwId, title, children) {
    if (document.getElementById('hw-submit-modal')) return;
    const childName = (children.find(c => String(c.id) === String(childId))?.name || 'Child').trim();
    const modal = document.createElement('div');
    modal.id = 'hw-submit-modal';
    modal.className = 'complaint-modal-overlay';
    modal.innerHTML = `
        <div class="complaint-modal">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                <div class="list-title" style="font-size:1rem">Submit Homework</div>
                <button id="close-hw-modal" style="background:none;border:none;font-size:1.3rem;cursor:pointer;color:var(--text3)">&times;</button>
            </div>
            <div class="text-xs text-gray" style="margin-bottom:16px">${title}</div>

            <div id="hw-modal-form">
                <div class="form-group">
                    <label class="form-label">Comment (optional)</label>
                    <textarea id="hw-modal-content" class="form-input" rows="3" placeholder="Add a comment about your submission..." style="padding:10px 12px;resize:vertical;font-family:inherit"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Attachment (optional)</label>
                    <div style="background:rgba(30,58,95,0.05);border-radius:6px;padding:10px 12px;margin-bottom:8px;border-left:3px solid var(--navy)">
                        <div class="text-xs bold" style="color:var(--navy);margin-bottom:2px">File Naming Guide</div>
                        <div class="text-xs text-gray">Save your file as: <strong style="color:var(--text)">ChildName_Subject_Homework1</strong></div>
                        <div class="text-xs text-gray">Example: <strong style="color:var(--text)">${childName.split(' ')[0]}_${title.replace(/\s+/g, '')}_HW1.pdf</strong></div>
                    </div>
                    <div class="file-upload-area" id="hw-modal-file-area">
                        <input type="file" id="hw-modal-file-input" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                        <div class="file-upload-label"><strong>Tap to attach</strong> a photo or file<br><span class="text-xs text-gray">JPG, PNG, PDF, DOC up to 10MB</span></div>
                    </div>
                    <div id="hw-modal-file-preview" style="display:none"></div>
                </div>
                <div id="hw-modal-error" class="form-error" style="display:none;margin-bottom:12px"></div>
                <button id="hw-modal-submit" class="btn btn-primary" style="padding:12px;font-size:0.9rem">${SVG.check} Submit</button>
            </div>

            <div id="hw-modal-success" style="display:none;text-align:center;padding:24px 0">
                <div style="width:56px;height:56px;border-radius:50%;background:rgba(5,150,105,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;color:var(--green)">${SVG.check}</div>
                <div class="list-title" style="color:var(--green)">Homework Submitted!</div>
                <div class="text-sm text-gray" style="margin-top:6px">${title}</div>
                <button id="hw-modal-done" class="btn btn-primary mt-3">Done</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    document.getElementById('close-hw-modal').onclick = () => modal.remove();
    modal.addEventListener('click', (e) => { if (e.target === modal) modal.remove(); });

    // File upload handling
    const fileArea = modal.querySelector('#hw-modal-file-area');
    const fileInput = modal.querySelector('#hw-modal-file-input');
    const filePreview = modal.querySelector('#hw-modal-file-preview');
    fileArea.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        if (file) {
            fileArea.style.display = 'none';
            filePreview.style.display = '';
            filePreview.innerHTML = `<div class="file-upload-preview"><span class="file-name">${file.name}</span><span class="text-xs text-gray">${(file.size / 1024).toFixed(0)} KB</span><button class="file-upload-remove" type="button">&times;</button></div>`;
            filePreview.querySelector('.file-upload-remove').onclick = () => {
                fileInput.value = '';
                filePreview.style.display = 'none';
                fileArea.style.display = '';
            };
        }
    });

    // Submit
    document.getElementById('hw-modal-submit').addEventListener('click', async () => {
        const content = document.getElementById('hw-modal-content').value;
        const file = fileInput.files[0] || null;
        const errEl = document.getElementById('hw-modal-error');
        const submitBtn = document.getElementById('hw-modal-submit');

        if (!content && !file) {
            errEl.textContent = 'Please add a comment or attach a file.';
            errEl.style.display = '';
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<div class="btn-spinner"></div> Submitting...';
        errEl.style.display = 'none';

        try {
            await api.submitHomework(childId, hwId, content, file);
            document.getElementById('hw-modal-form').style.display = 'none';
            document.getElementById('hw-modal-success').style.display = '';
        } catch (err) {
            errEl.textContent = err.message;
            errEl.style.display = '';
            submitBtn.disabled = false;
            submitBtn.innerHTML = `${SVG.check} Submit`;
        }
    });

    // Done — refresh homework page
    document.getElementById('hw-modal-done')?.addEventListener('click', () => {
        modal.remove();
        renderHomeworkPage(pageEl, api, children);
    });
}

async function renderReportCardsPage(el, api, children) {
    try {
        const allData = await Promise.all(
            children.map(c => api.getReportCards(c.id).then(rc => ({ child: c, ...rc })).catch(() => ({ child: c, terms: [] })))
        );

        let html = '<div class="dash-scroll">';

        html += `<div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border-radius:var(--radius);padding:16px;color:#fff;margin-bottom:12px;box-shadow:var(--shadow-md)">
            <div style="font-size:1rem;font-weight:700">Report Cards</div>
            <div style="font-size:0.72rem;opacity:0.65">Download and share your child's report cards</div>
        </div>`;

        for (const data of allData) {
            const c = data.child;
            const terms = data.terms || [];
            const readyCount = terms.filter(t => t.is_generated).length;
            const isFullyPaid = data.is_fully_paid;
            const feeStatus = data.fee_status;

            // Fee warning banner for unpaid/partial
            if (!isFullyPaid && feeStatus !== 'no_fee') {
                html += `<div style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.15);border-radius:var(--radius);margin-bottom:12px">
                    <div style="width:36px;height:36px;border-radius:10px;background:rgba(220,38,38,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.2rem">🔒</div>
                    <div><div class="text-sm bold text-red">Report Cards Locked</div><div class="text-xs text-gray" style="margin-top:2px">Please clear the outstanding tuition fee balance to access report cards and results for ${c.name}.</div></div>
                </div>`;
            }

            html += `<div class="card">
                <div class="card-head" style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border:none">
                    <div class="card-title" style="color:#fff"><span class="section-dot" style="background:#fff"></span>${c.name}</div>
                    <div style="display:flex;gap:6px">
                        ${!isFullyPaid && feeStatus !== 'no_fee' ? '<span class="badge badge-red">Locked</span>' : ''}
                        <span class="badge" style="background:rgba(255,255,255,0.2);color:#fff">${readyCount}/${terms.length} ready</span>
                    </div>
                </div>
                <div class="card-body" style="padding:0">`;

            if (terms.length === 0) {
                html += '<div class="card-empty">No report cards available.</div>';
            } else {
                for (let i = 0; i < terms.length; i++) {
                    const t = terms[i];
                    const border = i < terms.length - 1 ? 'border-bottom:1px solid var(--border);' : '';
                    const shareText = `${c.name} - ${t.term} Report Card - St. Francis of Assisi School`;
                    const locked = t.is_locked;

                    if (t.is_generated && !locked) {
                        // Fully paid — full access
                        html += `<div style="padding:16px;${border}">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
                                <div>
                                    <div class="list-title">${t.term}</div>
                                    <div class="text-xs text-green bold" style="margin-top:2px">Ready for download</div>
                                </div>
                                <span class="badge badge-green">Ready</span>
                            </div>
                            <div style="display:flex;gap:8px;flex-wrap:wrap">
                                <a href="${api.downloadUrl(t.download_url)}" target="_blank" class="share-btn" style="flex:1;justify-content:center;background:var(--navy);color:#fff;padding:10px 12px;font-size:0.78rem"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg> Download PDF</a>
                                <a href="https://wa.me/?text=${encodeURIComponent(shareText + ' ' + window.location.origin + api.downloadUrl(t.download_url))}" target="_blank" class="share-btn share-whatsapp" style="padding:10px 12px;font-size:0.78rem">WhatsApp</a>
                                <button class="share-btn share-copy btn-share-rc" data-url="${api.downloadUrl(t.download_url)}" data-text="${shareText}" style="padding:10px 12px;font-size:0.78rem">${SVG.share} Copy</button>
                            </div>
                        </div>`;
                    } else if (t.is_generated && locked) {
                        // Partial/unpaid — blurred/locked
                        html += `<div style="padding:16px;${border}position:relative;overflow:hidden">
                            <div style="filter:blur(6px);pointer-events:none;user-select:none">
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
                                    <div><div class="list-title">${t.term}</div><div class="text-xs text-green bold" style="margin-top:2px">Ready for download</div></div>
                                    <span class="badge badge-green">Ready</span>
                                </div>
                                <div style="display:flex;gap:8px"><div class="share-btn" style="flex:1;justify-content:center;background:var(--navy);color:#fff;padding:10px 12px;font-size:0.78rem">Download PDF</div></div>
                            </div>
                            <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;background:rgba(255,255,255,0.7);backdrop-filter:blur(2px)">
                                <div style="font-size:1.5rem;margin-bottom:6px">🔒</div>
                                <div class="text-sm bold" style="color:var(--red)">Fees Outstanding</div>
                                <div class="text-xs text-gray" style="margin-top:4px;text-align:center;max-width:200px">Clear your fee balance to unlock this report card</div>
                            </div>
                        </div>`;
                    } else {
                        html += `<div style="padding:16px;${border}opacity:0.6">
                            <div style="display:flex;justify-content:space-between;align-items:center">
                                <div>
                                    <div class="list-title">${t.term}</div>
                                    <div class="text-xs text-gray" style="margin-top:2px">Not yet generated</div>
                                </div>
                                <span class="badge badge-gray">Pending</span>
                            </div>
                        </div>`;
                    }
                }
            }

            html += '</div></div>';
        }

        html += '</div>';
        el.innerHTML = html;

        // Share copy buttons
        el.querySelectorAll('.btn-share-rc').forEach(btn => {
            btn.addEventListener('click', () => {
                const text = btn.dataset.text + '\n' + window.location.origin + btn.dataset.url;
                navigator.clipboard.writeText(text).then(() => {
                    btn.textContent = 'Copied!';
                    setTimeout(() => { btn.innerHTML = `${SVG.share} Copy`; }, 2000);
                }).catch(() => {});
            });
        });

    } catch (err) {
        el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`;
    }
}

async function renderTimetablePage(el, api, children) {
    try {
        const allData = await Promise.all(
            children.map(c => api.getTimetable(c.id).then(tt => ({ child: c, ...tt })).catch(() => ({ child: c, periods: [], days: [] })))
        );

        let html = '<div class="dash-scroll">';
        const dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        const todayName = dayNames[new Date().getDay() - 1] || 'Monday';

        for (const data of allData) {
            const c = data.child;
            const periods = data.periods || [];
            const days = data.days || dayNames;

            html += `<div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border-radius:var(--radius);padding:16px;color:#fff;margin-bottom:12px;box-shadow:var(--shadow-md)">
                <div style="font-size:1rem;font-weight:700">${c.name}</div>
                <div style="font-size:0.72rem;opacity:0.65">${c.grade || ''} ${c.class ? '- ' + c.class : ''} &middot; Class Timetable</div>
            </div>`;

            if (periods.length === 0) {
                html += '<div class="card"><div class="card-empty">No timetable available for this class.</div></div>';
                continue;
            }

            // Day selector tabs
            html += `<div class="card" style="overflow:visible"><div style="display:flex;gap:0;border-bottom:1px solid var(--border)" id="day-tabs-${c.id}">`;
            for (const day of days) {
                const isToday = day === todayName;
                const short = day.substring(0, 3);
                html += `<button class="tt-day-tab${isToday ? ' tt-day-active' : ''}" data-child="${c.id}" data-day="${day}" style="flex:1;padding:10px 4px;border:none;background:none;font-family:inherit;font-size:0.72rem;font-weight:600;cursor:pointer;color:${isToday ? 'var(--navy)' : 'var(--text3)'};border-bottom:2px solid ${isToday ? 'var(--navy)' : 'transparent'};transition:all 0.2s">
                    <div>${short}</div>
                    ${isToday ? '<div style="width:4px;height:4px;border-radius:50%;background:var(--navy);margin:3px auto 0"></div>' : ''}
                </button>`;
            }
            html += `</div><div id="day-schedule-${c.id}" style="padding:0"></div></div>`;

            // Full week grid (collapsible)
            html += `<div class="card">
                <button class="accordion-trigger accordion-card-head" data-target="full-tt-${c.id}">
                    <div class="accordion-trigger-left"><span class="section-dot" style="background:var(--blue)"></span><span class="card-title" style="margin:0">Full Week View</span></div>
                    <span class="accordion-chevron">${SVG.chevDown}</span>
                </button>
                <div class="accordion-panel" id="full-tt-${c.id}" style="padding:0">
                    ${renderTimetableHtml(data)}
                </div>
            </div>`;

            // Store data for JS day switching
            html += `<script type="application/json" id="tt-data-${c.id}">${JSON.stringify({ periods, days })}</script>`;
        }

        html += '</div>';
        el.innerHTML = html;

        // Render today's schedule for each child and bind day tabs
        for (const data of allData) {
            const cid = data.child.id;
            renderDaySchedule(cid, todayName);

            el.querySelectorAll(`.tt-day-tab[data-child="${cid}"]`).forEach(tab => {
                tab.addEventListener('click', () => {
                    el.querySelectorAll(`.tt-day-tab[data-child="${cid}"]`).forEach(t => {
                        t.style.color = 'var(--text3)';
                        t.style.borderBottomColor = 'transparent';
                        t.classList.remove('tt-day-active');
                    });
                    tab.style.color = 'var(--navy)';
                    tab.style.borderBottomColor = 'var(--navy)';
                    tab.classList.add('tt-day-active');
                    renderDaySchedule(cid, tab.dataset.day);
                });
            });
        }

        // Bind accordion toggles
        el.querySelectorAll('.accordion-trigger').forEach(trigger => {
            trigger.addEventListener('click', () => {
                const panel = document.getElementById(trigger.dataset.target);
                if (panel) { panel.classList.toggle('accordion-panel-open'); trigger.classList.toggle('accordion-open'); }
            });
        });

    } catch (err) {
        el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`;
    }
}

function renderDaySchedule(childId, dayName) {
    const dataEl = document.getElementById(`tt-data-${childId}`);
    const scheduleEl = document.getElementById(`day-schedule-${childId}`);
    if (!dataEl || !scheduleEl) return;

    const { periods } = JSON.parse(dataEl.textContent);
    let html = '';
    let hasEntries = false;

    for (const p of periods) {
        const entry = p.days[dayName];
        const isBreak = p.type === 'break';

        if (isBreak) {
            html += `<div style="display:flex;align-items:center;gap:12px;padding:8px 16px;background:rgba(217,119,6,0.06)">
                <div style="min-width:50px;text-align:right;font-size:0.68rem;color:var(--amber);font-weight:600">${p.start_time || ''}</div>
                <div style="flex:1;height:1px;background:var(--amber);opacity:0.3"></div>
                <div style="font-size:0.72rem;font-weight:600;color:var(--amber)">${p.short_name || 'Break'}</div>
                <div style="flex:1;height:1px;background:var(--amber);opacity:0.3"></div>
                <div style="min-width:50px;font-size:0.68rem;color:var(--amber)">${p.end_time || ''}</div>
            </div>`;
            continue;
        }

        if (entry) {
            hasEntries = true;
            html += `<div style="display:flex;gap:12px;padding:12px 16px;border-bottom:1px solid var(--border)">
                <div style="min-width:50px;text-align:right">
                    <div style="font-size:0.75rem;font-weight:700;color:var(--navy)">${p.start_time || ''}</div>
                    <div style="font-size:0.62rem;color:var(--text3)">${p.end_time || ''}</div>
                </div>
                <div style="width:3px;border-radius:99px;background:var(--blue);flex-shrink:0"></div>
                <div style="flex:1">
                    <div style="font-size:0.88rem;font-weight:600;color:var(--text)">${entry.subject || 'Free Period'}</div>
                    <div style="font-size:0.72rem;color:var(--text2);margin-top:2px">${entry.teacher || ''}${entry.room ? ' &middot; Room ' + entry.room : ''}</div>
                    <div style="font-size:0.62rem;color:var(--text3);margin-top:2px">${p.short_name || p.period}</div>
                </div>
            </div>`;
        } else {
            html += `<div style="display:flex;gap:12px;padding:10px 16px;border-bottom:1px solid var(--border);opacity:0.5">
                <div style="min-width:50px;text-align:right"><div style="font-size:0.75rem;font-weight:700;color:var(--text3)">${p.start_time || ''}</div><div style="font-size:0.62rem;color:var(--text3)">${p.end_time || ''}</div></div>
                <div style="width:3px;border-radius:99px;background:var(--border);flex-shrink:0"></div>
                <div style="flex:1;font-size:0.82rem;color:var(--text3);display:flex;align-items:center">Free Period</div>
            </div>`;
        }
    }

    if (!hasEntries && periods.length > 0) {
        html = '<div class="card-empty" style="padding:24px">No classes scheduled for this day.</div>';
    } else if (periods.length === 0) {
        html = '<div class="card-empty" style="padding:24px">No timetable data available.</div>';
    }

    scheduleEl.innerHTML = html;
}

async function renderSettings(el, user, api) {
    const isDark = document.documentElement.classList.contains('dark-mode');
    const pushSupported = 'serviceWorker' in navigator && 'PushManager' in window;
    const pushPermission = typeof Notification !== 'undefined' ? Notification.permission : 'unsupported';
    const children = JSON.parse(localStorage.getItem('children_data') || '[]');

    // Fetch school info and calendar in parallel
    const [schoolInfo, calendar] = await Promise.all([
        api.getSchoolSettings().catch(() => ({})),
        api.getSchoolCalendar().catch(() => ({ terms: [], year: null })),
    ]);

    const activeTerm = (calendar.terms || []).find(t => t.is_active);
    const cacheSize = localStorage.length;

    let html = '<div class="dash-scroll">';

    // Header
    html += `<div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border-radius:var(--radius);padding:20px;color:#fff;margin-bottom:12px;box-shadow:var(--shadow-md);text-align:center">
        <div style="font-size:1.1rem;font-weight:700">Settings</div>
        <div style="font-size:0.72rem;opacity:0.6;margin-top:4px">${user.name || 'Parent'} &middot; ${schoolInfo.name || 'St. Francis of Assisi'}</div>
    </div>`;

    // ── Appearance ──
    html += `<div class="card"><div class="card-head"><div class="card-title">${SVG.sun} Appearance</div></div>
        <div style="padding:0">
            <div class="list-item" style="padding:14px 16px;border-bottom:1px solid var(--border)">
                <div><div class="text-sm bold">Dark Mode</div><div class="text-xs text-gray">Reduce eye strain at night</div></div>
                <label class="toggle-switch"><input type="checkbox" id="dark-mode-toggle" ${isDark ? 'checked' : ''}><span class="toggle-slider"></span></label>
            </div>
            <div class="list-item" style="padding:14px 16px">
                <div><div class="text-sm bold">Text Size</div><div class="text-xs text-gray">Adjust reading comfort</div></div>
                <div style="display:flex;gap:4px">
                    <button class="text-size-btn" data-size="small" style="width:32px;height:32px;border-radius:6px;border:1px solid var(--border);background:var(--card);cursor:pointer;font-size:0.68rem;font-weight:700;color:var(--text2);font-family:inherit">A</button>
                    <button class="text-size-btn" data-size="normal" style="width:32px;height:32px;border-radius:6px;border:2px solid var(--navy);background:var(--card);cursor:pointer;font-size:0.82rem;font-weight:700;color:var(--navy);font-family:inherit">A</button>
                    <button class="text-size-btn" data-size="large" style="width:32px;height:32px;border-radius:6px;border:1px solid var(--border);background:var(--card);cursor:pointer;font-size:1rem;font-weight:700;color:var(--text2);font-family:inherit">A</button>
                </div>
            </div>
        </div>
    </div>`;

    // ── Notifications ──
    html += `<div class="card"><div class="card-head"><div class="card-title">${SVG.megaphone} Notifications</div></div>
        <div style="padding:0">
            ${pushSupported ? `<div class="list-item" style="padding:14px 16px;border-bottom:1px solid var(--border)">
                <div><div class="text-sm bold">Push Notifications</div><div class="text-xs text-gray" id="push-status">${pushPermission === 'granted' ? 'Enabled — receiving alerts' : pushPermission === 'denied' ? 'Blocked in browser settings' : 'Get alerts for notices, homework & payments'}</div></div>
                ${pushPermission === 'denied' ? '<span class="badge badge-red">Blocked</span>' : `<label class="toggle-switch"><input type="checkbox" id="push-toggle" ${pushPermission === 'granted' ? 'checked' : ''}><span class="toggle-slider"></span></label>`}
            </div>` : ''}
            <div class="list-item" style="padding:14px 16px;border-bottom:1px solid var(--border)">
                <div><div class="text-sm bold">SMS Alerts</div><div class="text-xs text-gray">Payment confirmations via SMS to ${user.phone || 'your phone'}</div></div>
                <span class="badge badge-green">Active</span>
            </div>
            <div class="list-item" style="padding:14px 16px">
                <div><div class="text-sm bold">Email Notifications</div><div class="text-xs text-gray">${user.email || 'Not set'}</div></div>
                <span class="badge badge-green">Active</span>
            </div>
        </div>
    </div>`;

    // ── Account ──
    html += `<div class="card"><div class="card-head"><div class="card-title">${SVG.user} Account</div></div>
        <div style="padding:0">
            <div class="list-item" style="padding:12px 16px;border-bottom:1px solid var(--border)">
                <div style="display:flex;align-items:center;gap:12px">
                    <div class="child-avatar" style="width:40px;height:40px;font-size:1rem;border-radius:10px;background:var(--navy);color:#fff;border:none">${initial(user.name)}</div>
                    <div><div class="text-sm bold">${user.name || '-'}</div><div class="text-xs text-gray">${user.relationship ? user.relationship.charAt(0).toUpperCase() + user.relationship.slice(1) : 'Parent'}</div></div>
                </div>
            </div>
            <div class="list-item" style="padding:10px 16px;border-bottom:1px solid var(--border)"><span class="text-xs text-gray" style="width:70px">Email</span><span class="text-sm">${user.email || '-'}</span></div>
            <div class="list-item" style="padding:10px 16px;border-bottom:1px solid var(--border)"><span class="text-xs text-gray" style="width:70px">Phone</span><span class="text-sm">${user.phone || '-'}</span></div>
            <div class="list-item" style="padding:10px 16px"><span class="text-xs text-gray" style="width:70px">Children</span><span class="text-sm">${children.map(c => c.name).join(', ') || '-'}</span></div>
        </div>
    </div>`;

    // ── Security ──
    html += `<div class="card"><div class="card-head"><div class="card-title">${SVG.flag} Security</div></div>
        <div style="padding:0">
            <button class="list-item" id="btn-change-pw" style="padding:14px 16px;border:none;background:none;width:100%;cursor:pointer;font-family:inherit;border-bottom:1px solid var(--border)">
                <div style="text-align:left"><div class="text-sm bold">Change Password</div><div class="text-xs text-gray">Update your login password</div></div>
                <span class="accordion-chevron" style="transform:rotate(-90deg)">${SVG.chevDown}</span>
            </button>
            <div id="change-pw-form" style="display:none;padding:16px;border-bottom:1px solid var(--border)">
                <div class="form-group"><label class="form-label">Current Password</label><input type="password" id="pw-current" class="form-input" style="padding:10px 12px"></div>
                <div class="form-group"><label class="form-label">New Password</label><input type="password" id="pw-new" class="form-input" style="padding:10px 12px"></div>
                <div class="form-group"><label class="form-label">Confirm New Password</label><input type="password" id="pw-confirm" class="form-input" style="padding:10px 12px"></div>
                <div id="pw-error" class="form-error" style="display:none;margin-bottom:8px"></div>
                <div id="pw-success" class="form-msg success" style="display:none;margin-bottom:8px">Password updated successfully!</div>
                <button id="pw-submit" class="btn btn-primary" style="padding:10px">Update Password</button>
            </div>
            <div class="list-item" style="padding:14px 16px">
                <div><div class="text-sm bold">Login Sessions</div><div class="text-xs text-gray">Manage active sessions</div></div>
                <button id="btn-logout-all" class="badge badge-red" style="cursor:pointer;border:none;padding:4px 10px;font-family:inherit">Logout All</button>
            </div>
        </div>
    </div>`;

    // ── School Information ──
    html += `<div class="card"><div class="card-head"><div class="card-title">${SVG.home} School Information</div></div>
        <div style="padding:0">
            <div class="list-item" style="padding:10px 16px;border-bottom:1px solid var(--border)"><span class="text-xs text-gray" style="width:80px">School</span><span class="text-sm bold">${schoolInfo.name || 'St. Francis of Assisi'}</span></div>
            <div class="list-item" style="padding:10px 16px;border-bottom:1px solid var(--border)"><span class="text-xs text-gray" style="width:80px">Motto</span><span class="text-sm" style="font-style:italic">${schoolInfo.motto || '-'}</span></div>
            <div class="list-item" style="padding:10px 16px;border-bottom:1px solid var(--border)"><span class="text-xs text-gray" style="width:80px">Phone</span><a href="tel:${schoolInfo.phone || ''}" class="text-sm text-blue" style="text-decoration:none">${schoolInfo.phone || '-'}</a></div>
            <div class="list-item" style="padding:10px 16px;border-bottom:1px solid var(--border)"><span class="text-xs text-gray" style="width:80px">Email</span><a href="mailto:${schoolInfo.email || ''}" class="text-sm text-blue" style="text-decoration:none">${schoolInfo.email || '-'}</a></div>
            ${calendar.year ? `<div class="list-item" style="padding:10px 16px;border-bottom:1px solid var(--border)"><span class="text-xs text-gray" style="width:80px">Year</span><span class="text-sm bold">${calendar.year}</span></div>` : ''}
            ${activeTerm ? `<div class="list-item" style="padding:10px 16px"><span class="text-xs text-gray" style="width:80px">Term</span><span class="text-sm"><strong>${activeTerm.name}</strong> <span class="text-xs text-gray">${activeTerm.start_date} — ${activeTerm.end_date}</span></span></div>` : ''}
        </div>
    </div>`;

    // ── Data & Storage ──
    html += `<div class="card"><div class="card-head"><div class="card-title">${SVG.download} Data & Storage</div></div>
        <div style="padding:0">
            <div class="list-item" style="padding:14px 16px;border-bottom:1px solid var(--border)">
                <div><div class="text-sm bold">Cached Data</div><div class="text-xs text-gray">${cacheSize} items stored locally</div></div>
                <button id="btn-clear-cache" class="badge badge-amber" style="cursor:pointer;border:none;padding:4px 10px;font-family:inherit">Clear</button>
            </div>
            <div class="list-item" style="padding:14px 16px">
                <div><div class="text-sm bold">Offline Mode</div><div class="text-xs text-gray">View cached data when offline</div></div>
                <span class="badge badge-green">Enabled</span>
            </div>
        </div>
    </div>`;

    // ── Quick Links ──
    html += `<div class="card"><div class="card-head"><div class="card-title">${SVG.menu} Quick Links</div></div>
        <div style="padding:0">
            <a href="#/dashboard/homework" class="list-item" style="padding:14px 16px;border-bottom:1px solid var(--border);text-decoration:none;color:var(--text)">
                <div style="display:flex;align-items:center;gap:10px">${SVG.homework}<span class="text-sm bold">Homework</span></div>
                <span class="accordion-chevron" style="transform:rotate(-90deg)">${SVG.chevDown}</span>
            </a>
            <a href="#/dashboard/library" class="list-item" style="padding:14px 16px;border-bottom:1px solid var(--border);text-decoration:none;color:var(--text)">
                <div style="display:flex;align-items:center;gap:10px">${SVG.book}<span class="text-sm bold">Library</span></div>
                <span class="accordion-chevron" style="transform:rotate(-90deg)">${SVG.chevDown}</span>
            </a>
            <a href="#/dashboard/timetable" class="list-item" style="padding:14px 16px;border-bottom:1px solid var(--border);text-decoration:none;color:var(--text)">
                <div style="display:flex;align-items:center;gap:10px">${SVG.clock}<span class="text-sm bold">Timetable</span></div>
                <span class="accordion-chevron" style="transform:rotate(-90deg)">${SVG.chevDown}</span>
            </a>
            <a href="#/dashboard/events" class="list-item" style="padding:14px 16px;text-decoration:none;color:var(--text)">
                <div style="display:flex;align-items:center;gap:10px">${SVG.calendar}<span class="text-sm bold">Events & Calendar</span></div>
                <span class="accordion-chevron" style="transform:rotate(-90deg)">${SVG.chevDown}</span>
            </a>
        </div>
    </div>`;

    // ── About ──
    html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--navy)"></span>About</div></div>
        <div style="padding:0">
            <div class="list-item" style="padding:10px 16px;border-bottom:1px solid var(--border)"><span class="text-xs text-gray" style="width:80px">App</span><span class="text-sm">SFA Parent Portal v1.0.0</span></div>
            <div class="list-item" style="padding:10px 16px;border-bottom:1px solid var(--border)"><span class="text-xs text-gray" style="width:80px">Platform</span><span class="text-sm">${navigator.userAgent.includes('SFAParentApp') ? 'Android App' : 'Web Browser'}</span></div>
            <div class="list-item" style="padding:10px 16px"><span class="text-xs text-gray" style="width:80px">Support</span><a href="tel:${schoolInfo.phone || ''}" class="text-sm text-blue" style="text-decoration:none">${schoolInfo.phone || 'Contact school'}</a></div>
        </div>
    </div>`;

    // Sign out
    html += `<button class="btn btn-outline" id="settings-logout" style="color:var(--red);border-color:var(--red);margin-top:4px">${SVG.logout} Sign Out</button>`;

    html += '</div>';
    el.innerHTML = html;

    // ── Event Handlers ──

    // Dark mode
    document.getElementById('dark-mode-toggle')?.addEventListener('change', (e) => {
        if (e.target.checked) { document.documentElement.classList.add('dark-mode'); localStorage.setItem('dark_mode', '1'); }
        else { document.documentElement.classList.remove('dark-mode'); localStorage.setItem('dark_mode', '0'); }
    });

    // Text size
    const currentSize = localStorage.getItem('text_size') || 'normal';
    el.querySelectorAll('.text-size-btn').forEach(btn => {
        if (btn.dataset.size === currentSize) { btn.style.borderColor = 'var(--navy)'; btn.style.color = 'var(--navy)'; btn.style.borderWidth = '2px'; }
        btn.addEventListener('click', () => {
            const size = btn.dataset.size;
            const sizes = { small: '13px', normal: '14px', large: '16px' };
            document.body.style.fontSize = sizes[size];
            localStorage.setItem('text_size', size);
            el.querySelectorAll('.text-size-btn').forEach(b => { b.style.borderColor = 'var(--border)'; b.style.color = 'var(--text2)'; b.style.borderWidth = '1px'; });
            btn.style.borderColor = 'var(--navy)'; btn.style.color = 'var(--navy)'; btn.style.borderWidth = '2px';
        });
    });

    // Push toggle
    document.getElementById('push-toggle')?.addEventListener('change', async (e) => {
        const toggle = e.target;
        const statusEl = document.getElementById('push-status');
        if (toggle.checked) {
            try {
                const permission = await Notification.requestPermission();
                if (permission === 'granted') {
                    const reg = await navigator.serviceWorker.ready;
                    const { key } = await api.getVapidKey();
                    const sub = await reg.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: urlBase64ToUint8Array(key) });
                    const subJson = sub.toJSON();
                    await api.pushSubscribe({ endpoint: subJson.endpoint, keys: subJson.keys });
                    if (statusEl) statusEl.textContent = 'Enabled — receiving alerts';
                } else { toggle.checked = false; if (statusEl) statusEl.textContent = 'Permission denied'; }
            } catch (err) { toggle.checked = false; if (statusEl) statusEl.textContent = 'Error: ' + err.message; }
        } else {
            try {
                const reg = await navigator.serviceWorker.ready;
                const sub = await reg.pushManager.getSubscription();
                if (sub) { await api.pushUnsubscribe(sub.endpoint); await sub.unsubscribe(); }
                if (statusEl) statusEl.textContent = 'Disabled';
            } catch {}
        }
    });

    // Change password toggle
    document.getElementById('btn-change-pw')?.addEventListener('click', () => {
        const form = document.getElementById('change-pw-form');
        form.style.display = form.style.display === 'none' ? '' : 'none';
    });

    // Change password submit
    document.getElementById('pw-submit')?.addEventListener('click', async () => {
        const current = document.getElementById('pw-current').value;
        const newPw = document.getElementById('pw-new').value;
        const confirm = document.getElementById('pw-confirm').value;
        const errEl = document.getElementById('pw-error');
        const successEl = document.getElementById('pw-success');
        errEl.style.display = 'none'; successEl.style.display = 'none';

        if (!current || !newPw || !confirm) { errEl.textContent = 'All fields are required.'; errEl.style.display = ''; return; }
        if (newPw.length < 8) { errEl.textContent = 'New password must be at least 8 characters.'; errEl.style.display = ''; return; }
        if (newPw !== confirm) { errEl.textContent = 'New passwords do not match.'; errEl.style.display = ''; return; }

        const btn = document.getElementById('pw-submit');
        btn.disabled = true; btn.textContent = 'Updating...';
        try {
            await api.post('/change-password', { current_password: current, new_password: newPw, new_password_confirmation: confirm });
            successEl.style.display = '';
            document.getElementById('pw-current').value = '';
            document.getElementById('pw-new').value = '';
            document.getElementById('pw-confirm').value = '';
        } catch (err) { errEl.textContent = err.message; errEl.style.display = ''; }
        btn.disabled = false; btn.textContent = 'Update Password';
    });

    // Logout all sessions
    document.getElementById('btn-logout-all')?.addEventListener('click', async () => {
        if (!window.confirm('This will sign you out from all devices. Continue?')) return;
        try { await api.logout(); } catch {}
        api.setToken(null);
        localStorage.removeItem('user_data');
        localStorage.removeItem('children_data');
        window.location.hash = '#/login';
    });

    // Clear cache
    document.getElementById('btn-clear-cache')?.addEventListener('click', () => {
        if (!window.confirm('Clear cached data? You will need to reload.')) return;
        caches.keys().then(ks => ks.forEach(k => { if (k.includes('sfa-api')) caches.delete(k); }));
        const btn = document.getElementById('btn-clear-cache');
        btn.textContent = 'Cleared!'; btn.style.background = 'rgba(5,150,105,0.1)'; btn.style.color = 'var(--green)';
        setTimeout(() => { btn.textContent = 'Clear'; btn.style.background = ''; btn.style.color = ''; }, 2000);
    });

    // Logout
    document.getElementById('settings-logout')?.addEventListener('click', async () => {
        try { await api.logout(); } catch {}
        api.setToken(null);
        localStorage.removeItem('user_data');
        localStorage.removeItem('children_data');
        window.location.hash = '#/login';
    });
}

function renderTimetableHtml(timetable) {
    if (!timetable || !timetable.periods || !timetable.periods.length) return '';
    let html = `<div class="timetable-wrap">
        <div class="timetable-grid">
            <div class="tt-header"><div class="tt-cell tt-period-cell">Period</div>`;
    for (const day of timetable.days) {
        html += `<div class="tt-cell">${day.substring(0, 3)}</div>`;
    }
    html += '</div>';
    for (const period of timetable.periods) {
        if (period.type === 'break') {
            html += `<div class="tt-row tt-break"><div class="tt-cell tt-period-cell">${period.short_name || period.period}</div>`;
            for (let i = 0; i < timetable.days.length; i++) html += '<div class="tt-cell tt-break-cell">Break</div>';
            html += '</div>';
            continue;
        }
        html += `<div class="tt-row"><div class="tt-cell tt-period-cell"><div class="tt-period-name">${period.short_name || period.period}</div><div class="tt-period-time">${period.start_time || ''}</div></div>`;
        for (const day of timetable.days) {
            const entry = period.days[day];
            html += entry
                ? `<div class="tt-cell tt-subject"><div class="tt-subj-name">${entry.subject || '-'}</div><div class="tt-subj-teacher">${entry.teacher ? entry.teacher.split(' ').pop() : ''}</div></div>`
                : '<div class="tt-cell tt-empty">-</div>';
        }
        html += '</div>';
    }
    html += '</div></div>';
    return html;
}

async function renderProfile(el, user, children, api) {
    const isDark = document.documentElement.classList.contains('dark-mode');
    let html = '<div class="dash-scroll">';
    html += `<div class="card">
        <div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));padding:24px;text-align:center;color:#fff">
            <div class="child-avatar" style="width:64px;height:64px;font-size:1.6rem;margin:0 auto 12px;border-radius:16px">${initial(user.name)}</div>
            <div style="font-size:1.1rem;font-weight:700">${user.name || ''}</div>
            <div style="font-size:0.82rem;opacity:0.65;margin-top:4px">${user.relationship ? user.relationship.charAt(0).toUpperCase() + user.relationship.slice(1) : 'Parent'}</div>
        </div>
        <div style="padding:16px">
            <div class="list-item" style="border-bottom:1px solid var(--border)"><div class="text-xs text-gray bold" style="width:80px">Email</div><div class="text-sm">${user.email || '-'}</div></div>
            <div class="list-item" style="border-bottom:1px solid var(--border)"><div class="text-xs text-gray bold" style="width:80px">Phone</div><div class="text-sm">${user.phone || '-'}</div></div>
            <div class="list-item"><div class="text-xs text-gray bold" style="width:80px">Children</div><div class="text-sm">${children.map(c => c.name + ' (' + c.grade + ')').join(', ') || '-'}</div></div>
        </div>
    </div>`;

    // Quick link to settings
    html += `<a href="#/dashboard/settings" class="btn btn-outline" style="margin-bottom:12px;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:8px">${SVG.sun} App Settings</a>`;

    // Children cards
    for (const c of children) {
        html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--blue)"></span>${c.name}</div><span class="badge badge-green">Active</span></div>
            <div style="padding:12px 16px">
                <div class="list-item" style="padding:6px 0"><span class="text-xs text-gray" style="width:80px">Grade</span><span class="text-sm bold">${c.grade || '-'}</span></div>
                <div class="list-item" style="padding:6px 0"><span class="text-xs text-gray" style="width:80px">Class</span><span class="text-sm bold">${c.class || '-'}</span></div>
                <div class="list-item" style="padding:6px 0"><span class="text-xs text-gray" style="width:80px">Teacher</span><span class="text-sm bold">${c.class_teacher || '-'}</span></div>
            </div>
            <div style="padding:0 16px 12px"><button class="btn btn-outline btn-report-issue" data-child="${c.id}" data-name="${c.name}" style="font-size:0.78rem;padding:10px;margin-top:0">${SVG.flag} Report an Issue</button></div>
        </div>`;
    }

    // P5: Complaints list
    html += `<div class="card" id="complaints-card" style="display:none"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--red)"></span>My Complaints</div></div><div class="card-body" id="complaints-list"></div></div>`;

    html += `<button class="btn btn-outline" id="profile-logout" style="color:var(--red);border-color:var(--red);margin-top:8px">${SVG.logout} Sign Out</button>`;
    html += '</div>';
    el.innerHTML = html;

    // P5: Load complaints
    api.getComplaints().then(complaints => {
        if (complaints.length > 0) {
            const card = document.getElementById('complaints-card');
            const list = document.getElementById('complaints-list');
            if (card) card.style.display = '';
            let items = '';
            for (const c of complaints) {
                const statusBadge = c.status === 'resolved' ? '<span class="badge badge-green">Resolved</span>'
                    : c.status === 'in_progress' ? '<span class="badge badge-amber">In Progress</span>'
                    : '<span class="badge badge-blue">Open</span>';
                items += `<div class="list-item" style="flex-direction:column;align-items:stretch;gap:6px">
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <div class="list-title">${c.subject}</div>${statusBadge}
                    </div>
                    <div class="list-sub">${c.type} &middot; ${c.priority} priority &middot; ${c.date}${c.student ? ' &middot; ' + c.student : ''}</div>
                    <div class="text-xs" style="color:var(--text2)">${c.description.length > 150 ? c.description.substring(0, 150) + '...' : c.description}</div>
                    ${c.resolution ? `<div class="text-xs text-green" style="margin-top:4px">Resolution: ${c.resolution}</div>` : ''}
                </div>`;
            }
            if (list) list.innerHTML = items;
        }
    }).catch(() => {});

    // P5: Report issue buttons
    el.querySelectorAll('.btn-report-issue').forEach(btn => {
        btn.addEventListener('click', () => {
            const childId = btn.dataset.child;
            const childName = btn.dataset.name;
            showComplaintForm(el, api, childId, childName);
        });
    });

    document.getElementById('profile-logout')?.addEventListener('click', async () => {
        try { await api.logout(); } catch {}
        api.setToken(null);
        localStorage.removeItem('user_data');
        localStorage.removeItem('children_data');
        window.location.hash = '#/login';
    });
}

// P5: Complaint form modal
function showComplaintForm(el, api, childId, childName) {
    if (document.getElementById('complaint-modal')) return;
    const modal = document.createElement('div');
    modal.id = 'complaint-modal';
    modal.className = 'complaint-modal-overlay';
    modal.innerHTML = `
        <div class="complaint-modal">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                <div class="list-title">Report Issue for ${childName}</div>
                <button id="close-complaint" style="background:none;border:none;font-size:1.3rem;cursor:pointer;color:var(--text3)">&times;</button>
            </div>
            <div class="form-group">
                <label class="form-label">Type</label>
                <select id="complaint-type" class="form-input" style="padding:10px 12px">
                    <option value="">Select type...</option>
                    <option value="academic">Academic</option>
                    <option value="behavioral">Behavioral</option>
                    <option value="facility">Facility Issue</option>
                    <option value="staff">Staff</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Subject</label>
                <input type="text" id="complaint-subject" class="form-input" placeholder="Brief subject" style="padding:10px 12px">
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea id="complaint-desc" class="form-input" rows="4" placeholder="Describe the issue..." style="padding:10px 12px;resize:vertical;font-family:inherit"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Priority</label>
                <select id="complaint-priority" class="form-input" style="padding:10px 12px">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            <div id="complaint-error" class="form-error" style="display:none;margin-bottom:12px"></div>
            <button id="submit-complaint" class="btn btn-primary" style="padding:12px">Submit Report</button>
        </div>
    `;
    document.body.appendChild(modal);

    document.getElementById('close-complaint').onclick = () => modal.remove();
    modal.addEventListener('click', (e) => { if (e.target === modal) modal.remove(); });

    document.getElementById('submit-complaint').addEventListener('click', async () => {
        const type = document.getElementById('complaint-type').value;
        const subject = document.getElementById('complaint-subject').value;
        const description = document.getElementById('complaint-desc').value;
        const priority = document.getElementById('complaint-priority').value;
        const errEl = document.getElementById('complaint-error');
        const submitBtn = document.getElementById('submit-complaint');

        if (!type || !subject || !description) {
            errEl.textContent = 'Please fill in all required fields.';
            errEl.style.display = '';
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<div class="btn-spinner"></div> Submitting...';
        errEl.style.display = 'none';

        try {
            await api.createComplaint(childId, { complaint_type: type, subject, description, priority });
            modal.remove();
            // Refresh profile to show new complaint
            const user = JSON.parse(localStorage.getItem('user_data') || '{}');
            const children = JSON.parse(localStorage.getItem('children_data') || '[]');
            renderProfile(el, user, children, api);
        } catch (err) {
            errEl.textContent = err.message;
            errEl.style.display = '';
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Submit Report';
        }
    });
}

// Payment Modal
function showPaymentModal(api, childId, childName, balance) {
    if (document.getElementById('payment-modal')) return;
    const user = JSON.parse(localStorage.getItem('user_data') || '{}');
    const modal = document.createElement('div');
    modal.id = 'payment-modal';
    modal.className = 'complaint-modal-overlay';
    modal.innerHTML = `
        <div class="complaint-modal">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                <div class="list-title" style="font-size:1rem">Pay Tuition Fees</div>
                <button id="close-payment" style="background:none;border:none;font-size:1.3rem;cursor:pointer;color:var(--text3)">&times;</button>
            </div>
            <div class="text-xs text-gray" style="margin-bottom:16px">${childName}</div>

            <div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border-radius:var(--radius-sm);padding:16px;color:#fff;margin-bottom:16px;text-align:center">
                <div style="font-size:0.7rem;opacity:0.7;text-transform:uppercase;letter-spacing:0.05em">Outstanding Balance</div>
                <div class="mono" style="font-size:1.8rem;font-weight:700;margin-top:4px">K ${new Intl.NumberFormat().format(balance)}</div>
            </div>

            <div id="pay-form">
                <div class="form-group">
                    <label class="form-label">Amount to Pay (ZMW)</label>
                    <input type="number" id="pay-amount" class="form-input" value="${balance}" min="1" max="${balance}" step="0.01" style="padding:12px;font-size:1.1rem;font-weight:700;text-align:center" inputmode="decimal">
                </div>
                <div style="display:flex;gap:6px;margin-bottom:16px;flex-wrap:wrap">
                    <button class="pay-preset badge badge-blue" data-amount="${balance}" style="padding:6px 12px;cursor:pointer;border:none;font-family:inherit;font-size:0.72rem">Full: K ${new Intl.NumberFormat().format(balance)}</button>
                    ${balance > 500 ? `<button class="pay-preset badge badge-blue" data-amount="${Math.ceil(balance/2)}" style="padding:6px 12px;cursor:pointer;border:none;font-family:inherit;font-size:0.72rem">Half: K ${new Intl.NumberFormat().format(Math.ceil(balance/2))}</button>` : ''}
                    ${balance > 1000 ? `<button class="pay-preset badge badge-blue" data-amount="${Math.ceil(balance/3)}" style="padding:6px 12px;cursor:pointer;border:none;font-family:inherit;font-size:0.72rem">1/3: K ${new Intl.NumberFormat().format(Math.ceil(balance/3))}</button>` : ''}
                </div>
                <div class="form-group">
                    <label class="form-label">Mobile Money Number</label>
                    <input type="tel" id="pay-mobile" class="form-input" placeholder="e.g. 0971234567" value="${user.phone || ''}" style="padding:12px" inputmode="tel">
                    <div class="text-xs text-gray" style="margin-top:4px">Airtel Money, MTN MoMo, or Zamtel Kwacha</div>
                </div>
                <div id="pay-error" class="form-error" style="display:none;margin-bottom:12px"></div>
                <button id="pay-submit" class="btn btn-primary" style="padding:14px;font-size:0.95rem">
                    ${SVG.wallet} Pay Now
                </button>
            </div>

            <div id="pay-processing" style="display:none;text-align:center;padding:24px 0">
                <div class="btn-spinner" style="width:40px;height:40px;margin:0 auto 16px;border-color:var(--border);border-top-color:var(--navy)"></div>
                <div class="list-title">Processing Payment</div>
                <div class="text-sm text-gray" style="margin-top:6px">Check your phone to approve the transaction</div>
                <div class="text-xs text-gray" style="margin-top:12px">Reference: <span id="pay-ref" class="mono bold">—</span></div>
            </div>

            <div id="pay-success" style="display:none;text-align:center;padding:24px 0">
                <div style="width:56px;height:56px;border-radius:50%;background:rgba(5,150,105,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;color:var(--green)">${SVG.check}</div>
                <div class="list-title" style="color:var(--green)">Payment Successful!</div>
                <div class="text-sm text-gray" style="margin-top:6px">K <span id="pay-success-amount">0</span> received</div>
                <div class="text-xs text-gray" style="margin-top:4px">Ref: <span id="pay-success-ref" class="mono">—</span></div>
                <a id="pay-receipt-link" href="#" target="_blank" class="btn btn-outline mt-3" style="display:none">${SVG.download} Download Receipt</a>
                <button id="pay-done" class="btn btn-primary mt-3">Done</button>
            </div>

            <div id="pay-failed" style="display:none;text-align:center;padding:24px 0">
                <div style="width:56px;height:56px;border-radius:50%;background:rgba(220,38,38,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;color:var(--red);font-size:1.5rem;font-weight:700">&times;</div>
                <div class="list-title" style="color:var(--red)">Payment Failed</div>
                <div class="text-sm text-gray" style="margin-top:6px" id="pay-fail-msg">Please try again.</div>
                <button id="pay-retry" class="btn btn-outline mt-3">Try Again</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    const formEl = document.getElementById('pay-form');
    const processingEl = document.getElementById('pay-processing');
    const successEl = document.getElementById('pay-success');
    const failedEl = document.getElementById('pay-failed');

    let paymentInProgress = false;
    const closeModal = () => {
        if (paymentInProgress) {
            if (!confirm('Payment is being processed. Are you sure you want to close? The payment will still be processed if approved.')) return;
        }
        paymentInProgress = false;
        modal.remove();
    };
    document.getElementById('close-payment').onclick = closeModal;
    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

    // Preset amount buttons
    modal.querySelectorAll('.pay-preset').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('pay-amount').value = btn.dataset.amount;
        });
    });

    // Submit payment
    document.getElementById('pay-submit').addEventListener('click', async () => {
        const amount = parseFloat(document.getElementById('pay-amount').value);
        const mobile = document.getElementById('pay-mobile').value.trim();
        const errEl = document.getElementById('pay-error');

        if (!amount || amount < 1) { errEl.textContent = 'Please enter a valid amount.'; errEl.style.display = ''; return; }
        if (amount > balance) { errEl.textContent = `Amount cannot exceed K ${balance}.`; errEl.style.display = ''; return; }
        if (!mobile || mobile.length < 10) { errEl.textContent = 'Please enter a valid mobile number.'; errEl.style.display = ''; return; }
        errEl.style.display = 'none';

        // Disable button to prevent double-tap
        const payBtn = document.getElementById('pay-submit');
        payBtn.disabled = true;
        payBtn.innerHTML = '<div class="btn-spinner"></div> Processing...';

        formEl.style.display = 'none';
        processingEl.style.display = '';
        paymentInProgress = true;

        const showSuccess = (ref, receiptUrl) => {
            paymentInProgress = false;
            processingEl.style.display = 'none';
            successEl.style.display = '';
            document.getElementById('pay-success-amount').textContent = new Intl.NumberFormat().format(amount);
            document.getElementById('pay-success-ref').textContent = ref;
            if (receiptUrl) {
                const link = document.getElementById('pay-receipt-link');
                link.href = api.downloadUrl(receiptUrl);
                link.style.display = '';
            }
        };

        const showFailed = (msg) => {
            paymentInProgress = false;
            processingEl.style.display = 'none';
            failedEl.style.display = '';
            document.getElementById('pay-fail-msg').textContent = msg || 'Payment was not completed. Please try again.';
        };

        try {
            const result = await api.initiatePayment(childId, amount, mobile);
            document.getElementById('pay-ref').textContent = result.payment_reference;

            // If delayed (timeout), show a helpful message but still poll
            if (result.is_delayed) {
                document.querySelector('#pay-processing .list-title').textContent = 'Payment Request Sent';
                document.querySelector('#pay-processing .text-sm').textContent = 'Confirmation delayed — check your phone for the payment prompt. If you approve it, we will detect it automatically.';
            }

            // Poll for status — only queryCustomerPayment confirms completion
            const paymentId = result.payment_id;
            let attempts = 0;
            let networkErrors = 0;
            const maxAttempts = 60; // 5 minutes (every 5 seconds)

            const pollStatus = async () => {
                // Stop if modal was closed
                if (!document.getElementById('payment-modal')) return;

                attempts++;
                try {
                    const status = await api.checkPaymentStatus(paymentId);
                    networkErrors = 0; // reset on success

                    if (status.status === 'completed') {
                        showSuccess(status.payment_reference || result.payment_reference, status.receipt_url);
                        return;
                    }

                    if (status.status === 'failed' || status.status === 'expired') {
                        showFailed(status.message);
                        return;
                    }

                    if (attempts < maxAttempts) {
                        setTimeout(pollStatus, 5000);
                    } else {
                        // Don't show as failed — show as pending with reference
                        processingEl.style.display = 'none';
                        failedEl.style.display = '';
                        document.getElementById('pay-fail-msg').innerHTML = `Confirmation is taking longer than expected.<br><br>
                            <strong>Ref: ${result.payment_reference}</strong><br><br>
                            If you approved the payment on your phone, it will be processed and your balance will update automatically. Check back later.`;
                        document.getElementById('pay-retry').textContent = 'Close';
                    }
                } catch (pollErr) {
                    networkErrors++;
                    if (networkErrors >= 5) {
                        showFailed('Connection lost. If you approved on your phone, the payment will still be processed. Check your balance later.');
                    } else if (attempts < maxAttempts) {
                        setTimeout(pollStatus, 5000);
                    }
                }
            };

            setTimeout(pollStatus, 5000);

        } catch (err) {
            showFailed(err.message);
        }
    });

    // Done — refresh dashboard to show updated balances
    document.getElementById('pay-done')?.addEventListener('click', () => {
        paymentInProgress = false;
        modal.remove();
        window.location.reload();
    });

    // Retry — reset to form
    document.getElementById('pay-retry')?.addEventListener('click', () => {
        failedEl.style.display = 'none';
        formEl.style.display = '';
        document.getElementById('pay-error').style.display = 'none';
        document.getElementById('pay-submit').disabled = false;
        document.getElementById('pay-submit').innerHTML = `${SVG.wallet} Pay Now`;
    });
}

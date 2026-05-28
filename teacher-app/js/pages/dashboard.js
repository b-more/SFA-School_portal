const SVG = {
    home: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>',
    users: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>',
    clipboard: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>',
    check: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>',
    clock: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    chart: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>',
    megaphone: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46"/></svg>',
    user: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>',
    logout: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>',
    menu: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>',
    chevDown: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>',
    plus: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>',
    calendar: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>',
    chat: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/></svg>',
    send: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>',
    paperclip: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"/></svg>',
};

function initial(name) { return (name || '?')[0].toUpperCase(); }

export async function renderDashboard(container, api, settings) {
    const user = JSON.parse(localStorage.getItem('teacher_data') || '{}');

    container.innerHTML = `
        <div class="app-shell">
            <div class="app-header">
                <div class="app-header-left">
                    <button class="app-header-btn" id="menu-btn">${SVG.menu}</button>
                    <div class="app-header-avatar">${initial(user.name)}</div>
                    <div>
                        <div class="app-header-name">${user.name || 'Teacher'}</div>
                        <div class="app-header-role">${user.grade || ''} ${user.class_section ? user.class_section : ''} ${user.is_class_teacher ? '· Class Teacher' : ''}</div>
                    </div>
                </div>
                <button class="app-header-btn" id="logout-btn">${SVG.logout}</button>
            </div>

            <div class="drawer-overlay" id="drawer-overlay"></div>
            <aside class="drawer" id="drawer">
                <div class="drawer-header">
                    <div class="drawer-avatar">${initial(user.name)}</div>
                    <div><div class="drawer-name">${user.name || 'Teacher'}</div><div class="drawer-role">${user.email || ''}</div></div>
                </div>
                <nav class="drawer-nav">
                    <div class="drawer-section-label">Main</div>
                    <a href="#/dashboard" class="drawer-link">${SVG.home}<span>Dashboard</span></a>
                    ${!(user.is_head_teacher || user.is_deputy_head) ? `
                    <a href="#/dashboard/classes" class="drawer-link">${SVG.users}<span>My Classes</span></a>
                    <a href="#/dashboard/attendance" class="drawer-link">${SVG.check}<span>Attendance</span></a>
                    <a href="#/dashboard/homework" class="drawer-link">${SVG.clipboard}<span>Homework</span></a>
                    <a href="#/dashboard/quiz" class="drawer-link">${SVG.check}<span>Quizzes</span></a>
                    <a href="#/dashboard/question-bank" class="drawer-link">${SVG.clipboard}<span>Question Bank</span></a>
                    <a href="#/dashboard/results" class="drawer-link">${SVG.chart}<span>Results</span></a>
                    <a href="#/dashboard/timetable" class="drawer-link">${SVG.clock}<span>Timetable</span></a>` : ''}
                    <a href="#/dashboard/notices" class="drawer-link">${SVG.megaphone}<span>Notices</span></a>
                    ${(user.is_head_teacher || user.is_deputy_head) ? `
                    <div class="drawer-section-label">Management</div>
                    <a href="#/dashboard/staff" class="drawer-link">${SVG.users}<span>Staff Directory</span></a>
                    <a href="#/dashboard/send-notice" class="drawer-link">${SVG.megaphone}<span>Send Notice</span></a>
                    <a href="#/dashboard/att-analytics" class="drawer-link">${SVG.check}<span>Attendance Reports</span></a>
                    <a href="#/dashboard/hw-analytics" class="drawer-link">${SVG.chart}<span>Homework Reports</span></a>
                    <a href="#/dashboard/cpd-approvals" class="drawer-link">${SVG.check}<span>CPD Approvals</span></a>
                    <a href="#/dashboard/cpd-school-report" class="drawer-link">${SVG.chart}<span>CPD School Report</span></a>` : `
                    <a href="#/dashboard/students" class="drawer-link">${SVG.users}<span>My Students</span></a>`}
                    <a href="#/dashboard/parent-contacts" class="drawer-link">${SVG.user}<span>Parent Contacts</span></a>
                    <a href="#/dashboard/messages" class="drawer-link">${SVG.chat}<span>Messages</span></a>
                    <div class="drawer-section-label">Professional Development</div>
                    <a href="#/dashboard/cpd" class="drawer-link">${SVG.chart}<span>CPD Dashboard</span></a>
                    <a href="#/dashboard/cpd-activities" class="drawer-link">${SVG.clipboard}<span>CPD Activities</span></a>
                    <a href="#/dashboard/cpd-resources" class="drawer-link">${SVG.clipboard}<span>Resource Library</span></a>
                    <a href="#/dashboard/cpd-observations" class="drawer-link">${SVG.users}<span>Observations</span></a>
                    <a href="#/dashboard/cpd-templates" class="drawer-link">${SVG.plus}<span>Quick Log</span></a>
                    <a href="#/dashboard/cpd-certs" class="drawer-link">${SVG.chart}<span>Certificates</span></a>
                    <a href="#/dashboard/cpd-approvals" class="drawer-link">${SVG.check}<span>Approvals</span></a>
                    <a href="#/dashboard/cpd-school-report" class="drawer-link">${SVG.users}<span>School CPD Report</span></a>
                    <div class="drawer-section-label">Analytics</div>
                    <a href="#/dashboard/hw-analytics" class="drawer-link">${SVG.chart}<span>Homework Analytics</span></a>
                    <a href="#/dashboard/att-analytics" class="drawer-link">${SVG.check}<span>Attendance Analytics</span></a>
                    <div class="drawer-section-label">School</div>
                    <a href="#/dashboard/leave" class="drawer-link">${SVG.calendar}<span>Leave</span></a>
                    <a href="#/dashboard/report-comments" class="drawer-link">${SVG.clipboard}<span>Report Comments</span></a>
                    <a href="#/dashboard/staff" class="drawer-link">${SVG.users}<span>Staff Directory</span></a>
                    <a href="#/dashboard/calendar" class="drawer-link">${SVG.calendar}<span>School Calendar</span></a>
                    <a href="#/dashboard/send-notice" class="drawer-link">${SVG.megaphone}<span>Send Notice</span></a>
                    <div class="drawer-section-label">Account</div>
                    <a href="#/dashboard/profile" class="drawer-link">${SVG.user}<span>Profile</span></a>
                    <a href="#/dashboard/payslips" class="drawer-link">${SVG.chart}<span>Payslips</span></a>
                    <button class="drawer-link drawer-logout" id="drawer-logout">${SVG.logout}<span>Sign Out</span></button>
                </nav>
            </aside>

            <div class="app-content" id="main-content">
                <div class="dash-scroll"><div class="kpi-strip"><div class="skeleton skeleton-kpi"></div><div class="skeleton skeleton-kpi"></div></div><div class="skeleton skeleton-card"></div></div>
            </div>
            <nav class="tab-bar">
                <a href="#/dashboard" class="tab-item active">${SVG.home}<span>Home</span></a>
                <a href="#/dashboard/attendance" class="tab-item">${SVG.check}<span>Attend.</span></a>
                <a href="#/dashboard/homework" class="tab-item">${SVG.clipboard}<span>H/Work</span></a>
                <a href="#/dashboard/messages" class="tab-item" id="tab-messages">${SVG.chat}<span>Messages</span></a>
                <a href="#/dashboard/profile" class="tab-item">${SVG.user}<span>Profile</span></a>
            </nav>
        </div>
    `;

    // Drawer
    const drawer = document.getElementById('drawer');
    const overlay = document.getElementById('drawer-overlay');
    const openDrawer = () => { drawer.classList.add('open'); overlay.classList.add('open'); };
    const closeDrawer = () => { drawer.classList.remove('open'); overlay.classList.remove('open'); };
    document.getElementById('menu-btn').addEventListener('click', openDrawer);
    overlay.addEventListener('click', closeDrawer);
    drawer.querySelectorAll('.drawer-link').forEach(l => l.addEventListener('click', closeDrawer));

    const doLogout = async () => { try { await api.logout(); } catch {} api.setToken(null); localStorage.removeItem('teacher_data'); localStorage.removeItem('teacher_remember'); window.location.hash = '#/login'; };
    document.getElementById('logout-btn').addEventListener('click', doLogout);
    document.getElementById('drawer-logout').addEventListener('click', doLogout);

    // Route
    const hash = window.location.hash;
    const content = document.getElementById('main-content');

    if (hash.includes('/classes')) await renderClasses(content, api);
    else if (hash.includes('/attendance')) await renderAttendance(content, api);
    else if (hash.includes('/cpd-templates')) await renderCpdQuickLog(content, api);
    else if (hash.includes('/cpd-certs')) await renderCpdCerts(content, api);
    else if (hash.includes('/cpd-approvals')) await renderCpdApprovals(content, api);
    else if (hash.includes('/cpd-school-report')) await renderSchoolCpdReport(content, api);
    else if (hash.includes('/cpd-activities')) await renderCpdActivities(content, api);
    else if (hash.includes('/cpd-resources')) await renderCpdResources(content, api);
    else if (hash.includes('/cpd-observations')) await renderCpdObservations(content, api);
    else if (hash.includes('/cpd')) await renderCpdDashboard(content, api);
    else if (hash.includes('/hw-analytics')) await renderHwAnalytics(content, api);
    else if (hash.includes('/question-bank')) await renderQuestionBank(content, api);
    else if (hash.includes('/quiz')) await renderQuiz(content, api);
    else if (hash.includes('/homework')) await renderHomework(content, api);
    else if (hash.includes('/results')) await renderResults(content, api);
    else if (hash.includes('/timetable')) await renderTimetable(content, api);
    else if (hash.includes('/notices')) await renderNotices(content, api);
    else if (hash.includes('/student-perf')) await renderStudentPerf(content, api);
    else if (hash.includes('/att-analytics')) await renderAttAnalytics(content, api);
    else if (hash.includes('/students')) await renderStudents(content, api);
    else if (hash.includes('/parent-contacts')) await renderParentContacts(content, api);
    else if (hash.includes('/leave')) await renderLeave(content, api);
    else if (hash.includes('/report-comments')) await renderReportComments(content, api);
    else if (hash.includes('/staff')) await renderStaffDirectory(content, api);
    else if (hash.includes('/calendar')) await renderCalendar(content, api);
    else if (hash.includes('/send-notice')) await renderSendNotice(content, api);
    else if (hash.includes('/messages')) await renderMessages(content, api);
    else if (hash.includes('/payslips')) await renderPayslips(content, api);
    else if (hash.includes('/profile')) await renderProfile(content, api, user);
    else if (user.is_head_teacher || user.is_deputy_head || user.is_admin) await renderHeadDashboard(content, api, user);
    else await renderHome(content, api, user);

    // Active states
    document.querySelectorAll('.tab-item').forEach(t => t.classList.toggle('active', hash.startsWith(t.getAttribute('href'))));
    document.querySelectorAll('.drawer-link[href]').forEach(l => l.classList.toggle('active', hash.startsWith(l.getAttribute('href'))));
}

// ─── HOME ───
async function renderHome(el, api, user) {
    try {
        const data = await api.getDashboard();
        let html = '<div class="dash-scroll">';

        // Welcome
        html += `<div style="margin-bottom:14px"><div style="font-size:1.1rem;font-weight:700">Good ${new Date().getHours() < 12 ? 'Morning' : new Date().getHours() < 17 ? 'Afternoon' : 'Evening'}, ${(user.name || 'Teacher').split(' ')[0]}</div>
            <div class="text-xs text-gray">${new Date().toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })}</div></div>`;

        // KPIs
        html += `<div class="kpi-strip">
            <div class="kpi"><div class="kpi-val kpi-blue">${data.total_students || 0}</div><div class="kpi-lbl">Students</div></div>
            <div class="kpi"><div class="kpi-val kpi-green">${data.total_classes || 0}</div><div class="kpi-lbl">Classes</div></div>
            <div class="kpi"><div class="kpi-val kpi-amber">${data.total_subjects || 0}</div><div class="kpi-lbl">Subjects</div></div>
            <div class="kpi"><div class="kpi-val kpi-red">${data.pending_grading || 0}</div><div class="kpi-lbl">To Grade</div></div>
        </div>`;

        // Quick actions
        html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--primary)"></span>Quick Actions</div></div>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;padding:12px">
                <a href="#/dashboard/attendance" class="btn btn-outline" style="text-decoration:none;font-size:0.75rem">${SVG.check} Mark Attendance</a>
                <a href="#/dashboard/homework" class="btn btn-outline" style="text-decoration:none;font-size:0.75rem">${SVG.clipboard} Give Homework</a>
                <a href="#/dashboard/results" class="btn btn-outline" style="text-decoration:none;font-size:0.75rem">${SVG.chart} Enter Results</a>
                <a href="#/dashboard/timetable" class="btn btn-outline" style="text-decoration:none;font-size:0.75rem">${SVG.clock} My Timetable</a>
            </div>
        </div>`;

        // Today's classes
        if (data.today_classes && data.today_classes.length > 0) {
            html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--green)"></span>Today's Schedule</div></div><div class="card-body">`;
            for (const c of data.today_classes) {
                html += `<div class="list-item">
                    <div style="min-width:44px;text-align:right"><div class="mono bold text-blue" style="font-size:0.75rem">${c.start_time || ''}</div><div class="text-xs text-gray">${c.end_time || ''}</div></div>
                    <div style="width:3px;border-radius:99px;background:var(--primary);align-self:stretch;flex-shrink:0"></div>
                    <div><div class="list-title">${c.subject || ''}</div><div class="list-sub">${c.class_name || ''} ${c.room ? '· Room ' + c.room : ''}</div></div>
                </div>`;
            }
            html += '</div></div>';
        }

        // Class teacher info
        if (data.class_teacher_info) {
            const ct = data.class_teacher_info;
            html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--amber)"></span>My Class: ${ct.class_name || ''}</div><span class="badge badge-blue">${ct.student_count || 0} students</span></div>
                <div style="padding:12px 14px">
                    <div class="flex-between text-xs mb-2"><span>Attendance Today</span><span class="bold ${(ct.attendance_rate || 0) >= 80 ? 'text-green' : 'text-red'}">${ct.attendance_rate || 0}%</span></div>
                    <div class="progress"><div class="progress-bar ${(ct.attendance_rate || 0) >= 80 ? 'bg-green' : 'bg-red'}" style="width:${Math.min(ct.attendance_rate || 0, 100)}%"></div></div>
                </div>
            </div>`;
        }

        html += '</div>';
        el.innerHTML = html;
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── HEAD TEACHER DASHBOARD ───
async function renderHeadDashboard(el, api, user) {
    try {
        const d = await api.getHeadDashboard();
        const fmtK = n => 'K ' + new Intl.NumberFormat().format(n || 0);
        let html = '<div class="dash-scroll">';

        // Welcome
        html += `<div style="margin-bottom:14px"><div style="font-size:1.1rem;font-weight:700">Welcome, ${(user.name || 'Head Teacher').split(' ')[0]}</div>
            <div class="text-xs text-gray">${user.role_name || 'Head Teacher'} · ${d.section_label || ''} · ${d.term || ''} ${d.year || ''}</div></div>`;

        // Section overview KPIs
        html += `<div style="background:linear-gradient(135deg,var(--navy),#1e3a8a);border-radius:var(--radius);padding:16px;color:#fff;margin-bottom:12px;box-shadow:var(--shadow-md)">
            <div style="font-size:0.68rem;opacity:0.6;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:10px">${d.section_label || d.school_name || 'School Overview'}</div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;text-align:center">
                <div><div class="mono" style="font-size:1.4rem;font-weight:700">${d.total_students}</div><div style="font-size:0.58rem;opacity:0.6">Students</div></div>
                <div><div class="mono" style="font-size:1.4rem;font-weight:700">${d.total_teachers}</div><div style="font-size:0.58rem;opacity:0.6">Teachers</div></div>
                <div><div class="mono" style="font-size:1.4rem;font-weight:700">${d.total_classes}</div><div style="font-size:0.58rem;opacity:0.6">Classes</div></div>
            </div>
        </div>`;

        // Today's attendance
        html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--green)"></span>Today's Attendance</div>
            <div class="mono bold ${d.att_rate >= 80 ? 'text-green' : d.att_rate >= 60 ? 'text-amber' : 'text-red'}">${d.att_rate}%</div></div>
            <div style="padding:12px 14px">
                <div class="progress" style="margin-bottom:8px"><div class="progress-bar ${d.att_rate >= 80 ? 'bg-green' : d.att_rate >= 60 ? 'bg-amber' : 'bg-red'}" style="width:${d.att_rate}%"></div></div>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;text-align:center;margin-bottom:8px">
                    <div><div class="mono bold text-green">${d.att_present}</div><div class="text-xs text-gray">Present</div></div>
                    <div><div class="mono bold text-red">${d.att_absent}</div><div class="text-xs text-gray">Absent</div></div>
                    <div><div class="mono bold text-amber">${d.unmarked_classes}</div><div class="text-xs text-gray">Unmarked</div></div>
                </div>`;

        // Grade breakdown
        if (d.grade_attendance && d.grade_attendance.length > 0) {
            html += '<div style="border-top:1px solid var(--border);padding-top:8px">';
            for (const g of d.grade_attendance) {
                const color = g.rate >= 80 ? 'var(--green)' : g.rate >= 60 ? 'var(--amber)' : 'var(--red)';
                html += `<div class="flex-between" style="padding:4px 0"><div class="text-xs">${g.grade} <span class="text-gray">(${g.students})</span></div>
                    <div style="display:flex;align-items:center;gap:6px;width:50%"><div class="progress" style="flex:1"><div class="progress-bar" style="width:${g.rate}%;background:${color}"></div></div><span class="mono bold text-xs" style="color:${color};min-width:30px;text-align:right">${g.rate}%</span></div></div>`;
            }
            html += '</div>';
        }
        html += '</div></div>';

        // Fee collection
        const uncollectedRate = 100 - d.collection_rate;
        html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--blue)"></span>Fee Collection</div></div>
            <div style="padding:12px 14px">
                <div class="progress" style="height:8px;margin-bottom:10px"><div class="progress-bar bg-green" style="width:${d.collection_rate}%"></div></div>
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;text-align:center">
                    <div><div class="mono bold text-green" style="font-size:1.3rem">${d.collection_rate}%</div><div class="text-xs text-gray">Collected</div></div>
                    <div><div class="mono bold text-red" style="font-size:1.3rem">${uncollectedRate}%</div><div class="text-xs text-gray">Outstanding</div></div>
                </div>
            </div>
        </div>`;

        // Action items
        html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--red)"></span>Action Items</div></div><div class="card-body" style="padding:0">`;
        const actions = [
            { label: 'Pending Grading', count: d.pending_grading, color: 'var(--amber)', link: '#/dashboard/homework' },
            { label: 'Pending Leave', count: d.pending_leave, color: 'var(--blue)', link: '#/dashboard/leave' },
            { label: 'Open Complaints', count: d.open_complaints, color: 'var(--red)', link: '#/dashboard/notices' },
            { label: 'Unmarked Classes', count: d.unmarked_classes, color: 'var(--amber)', link: '#/dashboard/attendance' },
        ];
        for (const a of actions) {
            if (a.count > 0) {
                html += `<a href="${a.link}" class="list-item" style="text-decoration:none;color:var(--text)"><div style="flex:1"><div class="list-title">${a.label}</div></div><div class="mono bold" style="color:${a.color}">${a.count}</div></a>`;
            }
        }
        if (actions.every(a => a.count === 0)) html += '<div class="card-empty">No action items.</div>';
        html += '</div></div>';

        // CPD compliance
        html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:#7c3aed"></span>CPD Compliance</div>
            <div class="mono bold" style="color:#7c3aed">${d.cpd_rate}%</div></div>
            <div style="padding:12px 14px">
                <div class="progress" style="margin-bottom:6px"><div class="progress-bar" style="width:${d.cpd_rate}%;background:#7c3aed"></div></div>
                <div class="text-xs text-gray">${d.cpd_compliant}/${d.cpd_total} teachers have met 40h target</div>
                <a href="#/dashboard/cpd-school-report" class="btn btn-outline mt-2" style="text-decoration:none;font-size:0.72rem">View Full Report</a>
            </div>
        </div>`;

        // Quick actions
        html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--primary)"></span>Quick Actions</div></div>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;padding:12px">
                <a href="#/dashboard/staff" class="btn btn-outline" style="text-decoration:none;font-size:0.72rem">${SVG.users} Staff Directory</a>
                <a href="#/dashboard/send-notice" class="btn btn-outline" style="text-decoration:none;font-size:0.72rem">${SVG.megaphone} Send Notice</a>
                <a href="#/dashboard/cpd-approvals" class="btn btn-outline" style="text-decoration:none;font-size:0.72rem">${SVG.check} CPD Approvals</a>
                <a href="#/dashboard/calendar" class="btn btn-outline" style="text-decoration:none;font-size:0.72rem">${SVG.calendar} School Calendar</a>
            </div>
        </div>`;

        // My Teachers
        if (d.teachers && d.teachers.length > 0) {
            html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--green)"></span>My Teachers</div><span class="badge badge-blue">${d.teachers.length}</span></div><div class="card-body" style="padding:0">`;
            for (const t of d.teachers) {
                html += `<div class="list-item" style="gap:8px">
                    <div class="att-avatar">${t.name[0]}</div>
                    <div style="flex:1;min-width:0"><div class="att-name-text">${t.name}${t.is_class_teacher ? ' <span class="badge badge-blue" style="margin-left:4px;font-size:0.5rem">CT</span>' : ''}</div><div class="att-name-sub">${t.grade || ''} ${t.class || ''}</div></div>
                    ${t.phone ? `<a href="tel:${t.phone}" class="badge badge-green" style="text-decoration:none;padding:3px 8px;font-size:0.55rem">Call</a>` : ''}
                </div>`;
            }
            html += '</div></div>';
        }

        // Recent notices
        if (d.recent_notices && d.recent_notices.length > 0) {
            html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--amber)"></span>Recent Notices</div></div><div class="card-body" style="padding:0">`;
            for (const n of d.recent_notices) {
                const badge = n.priority === 'urgent' ? '<span class="badge badge-red">Urgent</span>' : n.priority === 'important' ? '<span class="badge badge-amber">Important</span>' : '';
                html += `<div class="list-item"><div style="flex:1"><div class="list-title">${n.title}</div></div><div style="display:flex;align-items:center;gap:4px">${badge}<span class="text-xs text-gray">${n.date}</span></div></div>`;
            }
            html += '</div></div>';
        }

        html += '</div>';
        el.innerHTML = html;
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── MY CLASSES ───
async function renderClasses(el, api) {
    try {
        const classes = await api.getMyClasses();
        let html = '<div class="dash-scroll">';
        html += `<div style="font-size:1rem;font-weight:700;margin-bottom:12px">My Classes</div>`;

        if (!classes || classes.length === 0) {
            html += '<div class="card"><div class="card-empty">No class assignments found.</div></div>';
        } else {
            for (const c of classes) {
                html += `<div class="card">
                    <div class="card-head" style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border:none">
                        <div class="card-title" style="color:#fff">${c.grade || ''} ${c.class_section || ''}</div>
                        <span class="badge" style="background:rgba(255,255,255,0.2);color:#fff">${c.student_count || 0} students</span>
                    </div>
                    <div class="card-body" style="padding:12px 14px">
                        <div class="text-sm bold">${c.subject || ''}</div>
                        <div style="display:flex;gap:8px;margin-top:8px">
                            <a href="#/dashboard/attendance?class=${c.class_section_id}" class="btn btn-outline" style="flex:1;text-decoration:none;font-size:0.72rem;padding:8px">${SVG.check} Attendance</a>
                            <a href="#/dashboard/results?class=${c.class_section_id}&subject=${c.subject_id}" class="btn btn-outline" style="flex:1;text-decoration:none;font-size:0.72rem;padding:8px">${SVG.chart} Results</a>
                        </div>
                    </div>
                </div>`;
            }
        }

        html += '</div>';
        el.innerHTML = html;
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── ATTENDANCE ───
async function renderAttendance(el, api) {
    try {
        const classes = await api.getMyClasses();
        const params = new URLSearchParams(window.location.hash.split('?')[1] || '');
        const selectedClass = params.get('class') || (classes.length > 0 ? classes[0].class_section_id : null);
        const today = new Date().toISOString().split('T')[0];
        const selectedDate = params.get('date') || today;

        let html = '<div class="dash-scroll">';
        html += '<div style="font-size:1rem;font-weight:700;margin-bottom:12px">Mark Attendance</div>';

        // Class + Date selectors
        html += `<div class="card" style="overflow:visible"><div style="padding:12px 14px;display:flex;gap:8px">
            <select id="att-class" class="form-input" style="flex:1;padding:8px 10px;font-size:0.75rem">
                ${classes.map(c => `<option value="${c.class_section_id}" ${String(c.class_section_id) === String(selectedClass) ? 'selected' : ''}>${c.grade} ${c.class_section} - ${c.subject}</option>`).join('')}
            </select>
            <input type="date" id="att-date" class="form-input" value="${selectedDate}" style="width:auto;padding:8px 10px;font-size:0.75rem">
        </div></div>`;

        html += '<div id="att-list"><div class="card"><div class="card-empty">Loading students...</div></div></div>';
        html += '</div>';
        el.innerHTML = html;

        // Load attendance
        async function loadAttendance() {
            const classId = document.getElementById('att-class').value;
            const date = document.getElementById('att-date').value;
            const listEl = document.getElementById('att-list');

            try {
                const data = await api.getAttendance(classId, date);
                const students = data.students || [];
                let h = '';

                if (students.length === 0) {
                    h = '<div class="card"><div class="card-empty">No students in this class.</div></div>';
                } else {
                    // Mark all buttons
                    h += `<div style="display:flex;gap:6px;margin-bottom:10px;flex-wrap:wrap">
                        <button class="btn btn-outline mark-all-btn" data-status="present" style="flex:1;padding:8px;font-size:0.7rem;color:var(--green);border-color:var(--green)">All Present</button>
                        <button class="btn btn-outline mark-all-btn" data-status="absent" style="flex:1;padding:8px;font-size:0.7rem;color:var(--red);border-color:var(--red)">All Absent</button>
                    </div>`;

                    h += '<div class="card"><div class="att-grid">';
                    for (const s of students) {
                        const status = s.status || '';
                        h += `<div class="att-row" data-student="${s.id}">
                            <div class="att-avatar">${initial(s.name)}</div>
                            <div class="att-name"><div class="att-name-text">${s.name}</div><div class="att-name-sub">${s.student_id_number || ''}</div></div>
                            <div class="att-status-group">
                                <button class="att-btn ${status === 'present' ? 'selected s-present' : ''}" data-status="present" title="Present">P</button>
                                <button class="att-btn ${status === 'absent' ? 'selected s-absent' : ''}" data-status="absent" title="Absent">A</button>
                                <button class="att-btn ${status === 'late' ? 'selected s-late' : ''}" data-status="late" title="Late">L</button>
                                <button class="att-btn ${status === 'sick' ? 'selected s-sick' : ''}" data-status="sick" title="Sick">S</button>
                            </div>
                        </div>`;
                    }
                    h += '</div></div>';

                    // Save button
                    h += `<button id="save-att" class="btn btn-primary" style="margin-top:10px;padding:12px">${SVG.check} Save Attendance</button>`;
                }

                listEl.innerHTML = h;

                // Bind status buttons
                listEl.querySelectorAll('.att-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const row = btn.closest('.att-row');
                        row.querySelectorAll('.att-btn').forEach(b => { b.classList.remove('selected', 's-present', 's-absent', 's-late', 's-sick', 's-excused'); });
                        btn.classList.add('selected', 's-' + btn.dataset.status);
                    });
                });

                // Mark all buttons
                listEl.querySelectorAll('.mark-all-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const status = btn.dataset.status;
                        listEl.querySelectorAll('.att-row').forEach(row => {
                            row.querySelectorAll('.att-btn').forEach(b => b.classList.remove('selected', 's-present', 's-absent', 's-late', 's-sick', 's-excused'));
                            const target = row.querySelector(`.att-btn[data-status="${status}"]`);
                            if (target) target.classList.add('selected', 's-' + status);
                        });
                    });
                });

                // Save
                document.getElementById('save-att')?.addEventListener('click', async () => {
                    const btn = document.getElementById('save-att');
                    const attendance = [];
                    listEl.querySelectorAll('.att-row').forEach(row => {
                        const studentId = row.dataset.student;
                        const selected = row.querySelector('.att-btn.selected');
                        if (selected) attendance.push({ student_id: parseInt(studentId), status: selected.dataset.status });
                    });

                    if (attendance.length === 0) { alert('Please mark at least one student.'); return; }

                    btn.disabled = true; btn.innerHTML = '<div class="btn-spinner"></div> Saving...';
                    try {
                        await api.markAttendance({ class_section_id: parseInt(classId), date, attendance });
                        btn.innerHTML = `${SVG.check} Saved!`; btn.style.background = 'var(--green)';
                        setTimeout(() => { btn.innerHTML = `${SVG.check} Save Attendance`; btn.style.background = ''; btn.disabled = false; }, 2000);
                    } catch (err) {
                        btn.innerHTML = err.message; btn.style.background = 'var(--red)';
                        setTimeout(() => { btn.innerHTML = `${SVG.check} Save Attendance`; btn.style.background = ''; btn.disabled = false; }, 3000);
                    }
                });

            } catch (err) { listEl.innerHTML = `<div class="card"><div class="card-empty">${err.message}</div></div>`; }
        }

        loadAttendance();
        document.getElementById('att-class').addEventListener('change', loadAttendance);
        document.getElementById('att-date').addEventListener('change', loadAttendance);

    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── HOMEWORK ───
async function renderHomework(el, api) {
    try {
        const [hwData, classes] = await Promise.all([api.getMyHomework(), api.getMyClasses()]);
        const homework = hwData.homework || hwData || [];

        let html = '<div class="dash-scroll">';
        html += `<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <div style="font-size:1rem;font-weight:700">Homework</div>
            <button id="btn-new-hw" class="btn btn-primary" style="width:auto;padding:8px 14px;font-size:0.72rem">${SVG.plus} New</button>
        </div>`;

        if (homework.length === 0) {
            html += '<div class="card"><div class="card-empty">No homework created yet.</div></div>';
        } else {
            for (const hw of homework) {
                const subCount = hw.submission_count || 0;
                const gradedCount = hw.graded_count || 0;
                html += `<div class="card">
                    <div class="card-head"><div class="card-title">${hw.title}</div><span class="badge ${hw.status === 'active' ? 'badge-green' : 'badge-gray'}">${hw.status}</span></div>
                    <div style="padding:10px 14px">
                        <div class="list-sub">${hw.subject || ''} · ${hw.grade || ''} · Due ${hw.due_date || ''}</div>
                        <div style="display:flex;gap:12px;margin-top:8px">
                            <div class="text-xs text-gray">Submissions: <strong class="text-blue">${subCount}</strong></div>
                            <div class="text-xs text-gray">Graded: <strong class="text-green">${gradedCount}</strong></div>
                        </div>
                        ${subCount > 0 ? `<a href="#/dashboard/homework?view=${hw.id}" class="btn btn-outline mt-2" style="text-decoration:none;font-size:0.72rem;padding:8px">View Submissions & Grade</a>` : ''}
                    </div>
                </div>`;
            }
        }

        html += '</div>';
        el.innerHTML = html;

        // New homework modal
        document.getElementById('btn-new-hw')?.addEventListener('click', () => {
            showNewHomeworkModal(api, el, classes);
        });

        // Check if viewing submissions
        const viewId = new URLSearchParams(window.location.hash.split('?')[1] || '').get('view');
        if (viewId) showSubmissions(api, el, viewId);

    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

function showNewHomeworkModal(api, pageEl, classes) {
    if (document.getElementById('hw-modal')) return;
    const modal = document.createElement('div');
    modal.id = 'hw-modal'; modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
                <div class="list-title" style="font-size:0.95rem">Create Homework</div>
                <button id="close-hw" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--text3)">&times;</button>
            </div>
            <div class="form-group"><label class="form-label">Title</label><input type="text" id="hw-title" class="form-input" placeholder="Homework title" style="padding:10px 12px"></div>
            <div class="form-group"><label class="form-label">Class & Subject</label>
                <select id="hw-class" class="form-input" style="padding:10px 12px">
                    ${classes.map(c => `<option value="${c.class_section_id}|${c.subject_id}|${c.grade_id}">${c.grade} ${c.class_section} - ${c.subject}</option>`).join('')}
                </select>
            </div>
            <div class="form-group"><label class="form-label">Description</label><textarea id="hw-desc" class="form-input" rows="3" placeholder="Instructions..." style="padding:10px 12px;resize:vertical;font-family:inherit"></textarea></div>
            <div class="form-group"><label class="form-label">Due Date</label><input type="date" id="hw-due" class="form-input" style="padding:10px 12px"></div>
            <div class="form-group"><label class="form-label">Max Score</label><input type="number" id="hw-max" class="form-input" value="100" style="padding:10px 12px"></div>
            <div class="form-group">
                <label class="form-label">Attachment (optional)</label>
                <div style="display:flex;gap:6px">
                    <div class="file-upload-area" id="hw-file-area" style="flex:1;padding:10px">
                        <input type="file" id="hw-file-input" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                        <div style="font-size:0.72rem;color:var(--text2)"><strong style="color:var(--primary)">Tap to attach</strong> a file</div>
                    </div>
                    <div class="file-upload-area" id="hw-camera-area" style="width:60px;padding:10px;display:flex;align-items:center;justify-content:center">
                        <input type="file" id="hw-camera-input" accept="image/*" capture="environment" style="display:none">
                        <svg fill="none" stroke="var(--primary)" viewBox="0 0 24 24" width="22" height="22"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/></svg>
                    </div>
                </div>
                <div id="hw-file-preview" style="display:none"></div>
            </div>
            <div id="hw-err" class="form-error" style="display:none;margin-bottom:8px"></div>
            <button id="hw-submit" class="btn btn-primary" style="padding:11px">Create Homework</button>
        </div>
    `;
    document.body.appendChild(modal);
    document.getElementById('close-hw').onclick = () => modal.remove();
    modal.addEventListener('click', (e) => { if (e.target === modal) modal.remove(); });

    // File upload handling
    const fileArea = modal.querySelector('#hw-file-area');
    const fileInput = modal.querySelector('#hw-file-input');
    const cameraArea = modal.querySelector('#hw-camera-area');
    const cameraInput = modal.querySelector('#hw-camera-input');
    const filePreview = modal.querySelector('#hw-file-preview');
    let selectedFile = null;

    fileArea.addEventListener('click', () => fileInput.click());
    cameraArea.addEventListener('click', () => cameraInput.click());

    const showFilePreview = (file) => {
        selectedFile = file;
        fileArea.style.display = 'none';
        cameraArea.style.display = 'none';
        filePreview.style.display = '';
        filePreview.innerHTML = `<div style="display:flex;align-items:center;gap:8px;padding:8px;background:rgba(59,130,246,0.06);border-radius:6px;margin-top:4px">
            <span style="flex:1;font-size:0.75rem;font-weight:600;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${file.name}</span>
            <span class="text-xs text-gray">${(file.size/1024).toFixed(0)} KB</span>
            <button id="hw-file-remove" style="background:none;border:none;color:var(--red);cursor:pointer;font-size:1rem">&times;</button>
        </div>`;
        document.getElementById('hw-file-remove').onclick = () => {
            selectedFile = null; fileInput.value = ''; cameraInput.value = '';
            filePreview.style.display = 'none'; fileArea.style.display = ''; cameraArea.style.display = 'flex';
        };
    };
    fileInput.addEventListener('change', () => { if (fileInput.files[0]) showFilePreview(fileInput.files[0]); });
    cameraInput.addEventListener('change', () => { if (cameraInput.files[0]) showFilePreview(cameraInput.files[0]); });

    document.getElementById('hw-submit').addEventListener('click', async () => {
        const title = document.getElementById('hw-title').value.trim();
        const [classSectionId, subjectId, gradeId] = (document.getElementById('hw-class').value || '').split('|');
        const desc = document.getElementById('hw-desc').value;
        const dueDate = document.getElementById('hw-due').value;
        const maxScore = document.getElementById('hw-max').value;
        const errEl = document.getElementById('hw-err');
        const btn = document.getElementById('hw-submit');

        if (!title || !dueDate) { errEl.textContent = 'Title and due date are required.'; errEl.style.display = ''; return; }
        errEl.style.display = 'none';
        btn.disabled = true; btn.innerHTML = '<div class="btn-spinner"></div> Creating...';

        try {
            const data = { title, description: desc, subject_id: subjectId, grade_id: gradeId, due_date: dueDate, max_score: maxScore };
            if (selectedFile) data.file = selectedFile;
            await api.createHomework(data);
            modal.remove();
            renderHomework(pageEl, api);
        } catch (err) { errEl.textContent = err.message; errEl.style.display = ''; btn.disabled = false; btn.innerHTML = 'Create Homework'; }
    });
}

async function showSubmissions(api, pageEl, homeworkId) {
    try {
        const data = await api.getHomeworkSubmissions(homeworkId);
        const subs = data.submissions || [];

        let html = '<div class="dash-scroll">';
        html += `<div style="font-size:1rem;font-weight:700;margin-bottom:4px">${data.homework_title || 'Submissions'}</div>`;
        html += `<div class="text-xs text-gray" style="margin-bottom:12px">${data.subject || ''} · ${subs.length} submissions</div>`;

        for (const s of subs) {
            const badge = s.status === 'graded' ? `<span class="badge badge-green">${s.marks}</span>` : '<span class="badge badge-amber">Ungraded</span>';
            html += `<div class="card">
                <div style="padding:12px 14px">
                    <div class="flex-between"><div class="list-title">${s.student_name || ''}</div>${badge}</div>
                    <div class="list-sub mt-2">${s.submitted_at || ''} ${s.is_late ? '· <span class="text-red bold">Late</span>' : ''}</div>
                    ${s.content ? `<div class="text-sm mt-2" style="background:#f8fafc;padding:8px;border-radius:6px;color:var(--text2)">${s.content}</div>` : ''}
                    ${s.file_url ? `<a href="${s.file_url}" target="_blank" class="btn btn-outline mt-2" style="text-decoration:none;font-size:0.7rem;padding:6px 10px">Download File</a>` : ''}
                    ${s.status !== 'graded' ? `
                        <div style="margin-top:8px;display:flex;flex-direction:column;gap:6px">
                            <div style="display:flex;gap:8px"><div style="flex:1"><label class="form-label">Score</label><input type="number" class="form-input grade-marks" data-sub="${s.id}" value="" min="0" placeholder="0" style="padding:8px;font-size:0.82rem"></div><div style="flex:1"><label class="form-label">Max: ${data.max_score || 100}</label></div></div>
                            <div><label class="form-label">Feedback to Student</label><textarea class="form-input grade-feedback" data-sub="${s.id}" rows="2" placeholder="Well done! / Needs improvement in..." style="padding:8px;font-size:0.78rem;resize:vertical;font-family:inherit"></textarea></div>
                            <div><label class="form-label">Teacher Notes (internal)</label><textarea class="form-input grade-notes" data-sub="${s.id}" rows="1" placeholder="Internal notes..." style="padding:8px;font-size:0.78rem;resize:vertical;font-family:inherit"></textarea></div>
                            <button class="btn btn-primary btn-grade" data-sub="${s.id}" style="width:auto;padding:8px 14px;font-size:0.72rem">${SVG.check} Grade</button>
                        </div>
                    ` : `<div style="margin-top:6px;padding:8px 10px;background:rgba(5,150,105,0.05);border-radius:6px;border-left:3px solid var(--green)"><div class="text-xs bold text-green">Score: ${s.marks} · Feedback: ${s.feedback || 'None'}</div></div>`}
                </div>
            </div>`;
        }

        if (subs.length === 0) html += '<div class="card"><div class="card-empty">No submissions yet.</div></div>';
        html += `<a href="#/dashboard/homework" class="btn btn-outline" style="text-decoration:none">Back to Homework</a></div>`;

        pageEl.innerHTML = html;

        // Bind grade buttons
        pageEl.querySelectorAll('.btn-grade').forEach(btn => {
            btn.addEventListener('click', async () => {
                const subId = btn.dataset.sub;
                const marks = pageEl.querySelector(`.grade-marks[data-sub="${subId}"]`)?.value;
                const feedback = pageEl.querySelector(`.grade-feedback[data-sub="${subId}"]`)?.value || '';
                const notes = pageEl.querySelector(`.grade-notes[data-sub="${subId}"]`)?.value || '';
                if (!marks) { alert('Enter a score.'); return; }
                btn.disabled = true; btn.textContent = 'Saving...';
                try {
                    await api.gradeSubmission(subId, { marks: parseFloat(marks), feedback, teacher_notes: notes });
                    btn.textContent = 'Graded!'; btn.style.background = 'var(--green)';
                    setTimeout(() => showSubmissions(api, pageEl, new URLSearchParams(window.location.hash.split('?')[1] || '').get('view')), 1500);
                } catch (err) { btn.textContent = err.message; btn.style.background = 'var(--red)'; setTimeout(() => { btn.innerHTML = `${SVG.check} Grade`; btn.style.background = ''; btn.disabled = false; }, 2000); }
            });
        });
    } catch (err) { pageEl.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── RESULTS ───
async function renderResults(el, api) {
    try {
        const classes = await api.getMyClasses();
        const params = new URLSearchParams(window.location.hash.split('?')[1] || '');
        const selectedClass = params.get('class') || '';
        const selectedSubject = params.get('subject') || '';

        let html = '<div class="dash-scroll">';
        html += '<div style="font-size:1rem;font-weight:700;margin-bottom:12px">Enter Results</div>';

        html += `<div class="card" style="overflow:visible"><div style="padding:12px 14px;display:flex;gap:8px;flex-wrap:wrap">
            <select id="res-class" class="form-input" style="flex:1;padding:8px 10px;font-size:0.75rem">
                <option value="">Select Class & Subject</option>
                ${classes.map(c => `<option value="${c.class_section_id}|${c.subject_id}" ${String(c.class_section_id) === selectedClass && String(c.subject_id) === selectedSubject ? 'selected' : ''}>${c.grade} ${c.class_section} - ${c.subject}</option>`).join('')}
            </select>
            <select id="res-type" class="form-input" style="width:auto;padding:8px 10px;font-size:0.75rem">
                <option value="test">Test</option>
                <option value="exam">Exam</option>
                <option value="assignment">Assignment</option>
                <option value="quiz">Quiz</option>
            </select>
        </div></div>`;

        html += '<div id="res-list"><div class="card"><div class="card-empty">Select a class and subject above.</div></div></div>';
        html += '</div>';
        el.innerHTML = html;

        document.getElementById('res-class').addEventListener('change', async () => {
            const val = document.getElementById('res-class').value;
            if (!val) return;
            const [classId, subjectId] = val.split('|');
            const listEl = document.getElementById('res-list');

            try {
                const data = await api.getClassStudents(classId);
                const students = data.students || data || [];
                let h = '<div class="card"><div class="att-grid">';

                for (const s of students) {
                    h += `<div class="att-row" data-student="${s.id}">
                        <div class="att-avatar">${initial(s.name)}</div>
                        <div class="att-name"><div class="att-name-text">${s.name}</div></div>
                        <input type="number" class="form-input res-mark" data-student="${s.id}" min="0" max="100" placeholder="0" style="width:60px;padding:6px 8px;font-size:0.82rem;text-align:center;font-weight:700">
                    </div>`;
                }

                h += '</div></div>';
                h += `<button id="save-res" class="btn btn-primary" style="margin-top:10px;padding:12px">${SVG.chart} Save Results</button>`;
                listEl.innerHTML = h;

                document.getElementById('save-res')?.addEventListener('click', async () => {
                    const examType = document.getElementById('res-type').value;
                    const results = [];
                    listEl.querySelectorAll('.res-mark').forEach(inp => {
                        if (inp.value) results.push({ student_id: parseInt(inp.dataset.student), marks: parseFloat(inp.value) });
                    });
                    if (results.length === 0) { alert('Enter at least one mark.'); return; }

                    const btn = document.getElementById('save-res');
                    btn.disabled = true; btn.innerHTML = '<div class="btn-spinner"></div> Saving...';
                    try {
                        await api.enterResults({ subject_id: parseInt(subjectId), class_section_id: parseInt(classId), exam_type: examType, results });
                        btn.innerHTML = `${SVG.check} Saved!`; btn.style.background = 'var(--green)';
                    } catch (err) { btn.innerHTML = err.message; btn.style.background = 'var(--red)'; setTimeout(() => { btn.innerHTML = `${SVG.chart} Save Results`; btn.style.background = ''; btn.disabled = false; }, 3000); }
                });
            } catch (err) { listEl.innerHTML = `<div class="card"><div class="card-empty">${err.message}</div></div>`; }
        });

        // Auto-load if pre-selected
        if (selectedClass && selectedSubject) document.getElementById('res-class').dispatchEvent(new Event('change'));

    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── TIMETABLE ───
async function renderTimetable(el, api) {
    try {
        const data = await api.getMyTimetable();
        const periods = data.periods || [];
        const days = data.days || ['Monday','Tuesday','Wednesday','Thursday','Friday'];
        const todayName = ['','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'][new Date().getDay()] || 'Monday';

        let html = '<div class="dash-scroll">';
        html += '<div style="font-size:1rem;font-weight:700;margin-bottom:12px">My Timetable</div>';

        // Day tabs
        html += '<div class="card"><div style="display:flex;border-bottom:1px solid var(--border)">';
        for (const day of days) {
            const isToday = day === todayName;
            html += `<button class="tt-day-tab${isToday ? ' active' : ''}" data-day="${day}" style="flex:1;padding:10px 4px;border:none;background:none;font-family:inherit;font-size:0.68rem;font-weight:600;cursor:pointer;color:${isToday ? 'var(--primary)' : 'var(--text3)'};border-bottom:2px solid ${isToday ? 'var(--primary)' : 'transparent'}">${day.substring(0,3)}</button>`;
        }
        html += '</div><div id="tt-schedule"></div></div>';

        html += '</div>';
        el.innerHTML = html;

        function renderDay(dayName) {
            const scheduleEl = document.getElementById('tt-schedule');
            let h = '';
            for (const p of periods) {
                const entry = p.days?.[dayName];
                if (p.type === 'break') {
                    h += `<div style="display:flex;align-items:center;gap:8px;padding:6px 14px;background:rgba(217,119,6,0.05)"><div style="flex:1;height:1px;background:var(--amber);opacity:0.3"></div><span class="text-xs bold text-amber">${p.short_name || 'Break'}</span><div style="flex:1;height:1px;background:var(--amber);opacity:0.3"></div></div>`;
                } else if (entry) {
                    h += `<div class="list-item"><div style="min-width:44px;text-align:right"><div class="mono bold text-blue" style="font-size:0.72rem">${p.start_time || ''}</div><div class="text-xs text-gray">${p.end_time || ''}</div></div><div style="width:3px;border-radius:99px;background:var(--primary);align-self:stretch;flex-shrink:0"></div><div><div class="list-title">${entry.subject || ''}</div><div class="list-sub">${entry.class_name || ''}${entry.room ? ' · ' + entry.room : ''}</div></div></div>`;
                } else {
                    h += `<div class="list-item" style="opacity:0.4"><div style="min-width:44px;text-align:right;font-size:0.72rem;color:var(--text3)">${p.start_time || ''}</div><div style="width:3px;border-radius:99px;background:var(--border);align-self:stretch;flex-shrink:0"></div><div class="text-sm text-gray">Free Period</div></div>`;
                }
            }
            if (!h) h = '<div class="card-empty">No schedule data.</div>';
            scheduleEl.innerHTML = h;
        }

        renderDay(todayName);
        el.querySelectorAll('.tt-day-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                el.querySelectorAll('.tt-day-tab').forEach(t => { t.style.color = 'var(--text3)'; t.style.borderBottomColor = 'transparent'; });
                tab.style.color = 'var(--primary)'; tab.style.borderBottomColor = 'var(--primary)';
                renderDay(tab.dataset.day);
            });
        });
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── NOTICES ───
async function renderNotices(el, api) {
    try {
        const notices = await api.getNotices();
        let html = '<div class="dash-scroll"><div style="font-size:1rem;font-weight:700;margin-bottom:12px">Notices</div>';

        if (!notices || notices.length === 0) {
            html += '<div class="card"><div class="card-empty">No notices.</div></div>';
        } else {
            for (const n of notices) {
                const badge = n.priority === 'urgent' ? '<span class="badge badge-red">Urgent</span>' : n.priority === 'important' ? '<span class="badge badge-amber">Important</span>' : '';
                html += `<div class="card"><div style="padding:12px 14px">
                    <div class="flex-between"><div class="list-title">${n.title}</div>${badge}</div>
                    <div class="list-sub">${n.posted_by || ''} · ${n.date || ''}</div>
                    <div class="text-sm mt-2" style="color:var(--text2);line-height:1.5">${n.body || ''}</div>
                </div></div>`;
            }
        }

        html += '</div>';
        el.innerHTML = html;
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── CPD DASHBOARD ───
async function renderCpdDashboard(el, api) {
    try {
        const [d, termData] = await Promise.all([api.getCpdDashboard(), api.getCpdTermBreakdown().catch(() => null)]);
        let html = '<div class="dash-scroll">';

        // Header
        html += `<div style="background:linear-gradient(135deg,#7c3aed,#4f46e5);border-radius:var(--radius);padding:16px;color:#fff;margin-bottom:12px;box-shadow:var(--shadow-md)">
            <div style="font-size:1rem;font-weight:700">Professional Development</div>
            <div style="font-size:0.72rem;opacity:0.65">${d.year} · ${d.total_hours}/${d.target_hours} CPD hours</div>
            <div class="progress mt-2" style="height:8px;background:rgba(255,255,255,0.2)"><div class="progress-bar" style="width:${d.hours_progress}%;background:#6ee7b7"></div></div>
            <div style="text-align:center;font-size:0.68rem;opacity:0.7;margin-top:4px">${d.hours_progress}% of annual target</div>
        </div>`;

        // KPIs
        html += `<div class="kpi-strip" style="grid-template-columns:repeat(4,1fr)">
            <div class="kpi"><div class="kpi-val kpi-blue">${d.completed_count}</div><div class="kpi-lbl">Completed</div></div>
            <div class="kpi"><div class="kpi-val kpi-amber">${d.planned_count}</div><div class="kpi-lbl">Planned</div></div>
            <div class="kpi"><div class="kpi-val kpi-green">${d.certificates_count}</div><div class="kpi-lbl">Certs</div></div>
            <div class="kpi"><div class="kpi-val" style="color:#7c3aed">${d.goals_achieved}/${d.goals_total}</div><div class="kpi-lbl">Goals</div></div>
        </div>`;

        // Term-by-term tracking
        if (termData && termData.terms) {
            html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:#7c3aed"></span>Term Breakdown</div><span class="badge badge-blue">${termData.total_points || 0} pts</span></div><div class="card-body" style="padding:0">`;
            termData.terms.forEach((t, i) => {
                const target = termData.term_targets?.[i] || 13;
                const pct = target > 0 ? Math.min(100, Math.round((t.hours / target) * 100)) : 0;
                const color = pct >= 100 ? 'var(--green)' : pct >= 50 ? 'var(--amber)' : 'var(--red)';
                html += `<div class="list-item" style="flex-direction:column;align-items:stretch;gap:4px;padding:12px 14px">
                    <div class="flex-between"><div class="list-title">${t.term}</div><div class="mono bold" style="color:${color}">${t.hours}/${target}h</div></div>
                    <div class="progress"><div class="progress-bar" style="width:${pct}%;background:${color}"></div></div>
                    <div class="text-xs text-gray">${t.count} activities · ${t.mandatory} mandatory · ${t.voluntary} voluntary · ${t.points} pts</div>
                </div>`;
            });
            html += '</div></div>';
        }

        // Hours by type
        if (d.by_type && Object.keys(d.by_type).length > 0) {
            html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:#7c3aed"></span>Hours by Type</div></div><div class="card-body" style="padding:0">`;
            for (const [type, data] of Object.entries(d.by_type)) {
                html += `<div class="list-item"><div style="flex:1"><div class="list-title">${type.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}</div><div class="list-sub">${data.count} activit${data.count !== 1 ? 'ies' : 'y'}</div></div><div class="mono bold text-blue">${data.hours}h</div></div>`;
            }
            html += '</div></div>';
        }

        // Goals
        if (d.goals && d.goals.length > 0) {
            html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--green)"></span>Goals</div></div><div class="card-body" style="padding:0">`;
            for (const g of d.goals) {
                const badge = g.status === 'achieved' ? '<span class="badge badge-green">Achieved</span>' : g.status === 'in_progress' ? '<span class="badge badge-amber">In Progress</span>' : '<span class="badge badge-gray">Not Started</span>';
                html += `<div class="list-item"><div style="flex:1"><div class="list-title">${g.title}</div><div class="list-sub">${g.target_date ? 'Target: ' + g.target_date : ''}</div></div>${badge}</div>`;
            }
            html += '</div></div>';
        }

        // Recent observations
        if (d.recent_observations && d.recent_observations.length > 0) {
            html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--amber)"></span>Recent Observations</div></div><div class="card-body" style="padding:0">`;
            for (const o of d.recent_observations) {
                html += `<div class="list-item"><div style="flex:1"><div class="list-title">${o.subject || 'Lesson Observation'}</div><div class="list-sub">${o.date} · ${o.observer}</div></div>${o.rating ? `<div class="mono bold text-blue">${o.rating}/5</div>` : ''}</div>`;
            }
            html += '</div></div>';
        }

        // Quick actions
        html += `<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px">
            <a href="#/dashboard/cpd-activities" class="btn btn-outline" style="text-decoration:none;font-size:0.72rem">${SVG.clipboard} Activities</a>
            <a href="#/dashboard/cpd-resources" class="btn btn-outline" style="text-decoration:none;font-size:0.72rem">${SVG.users} Resources</a>
        </div>
        <button id="btn-cpd-export" class="btn btn-outline mt-2" style="font-size:0.72rem">${SVG.chart} Export CPD Report</button>`;

        html += '</div>';
        el.innerHTML = html;

        // CPD Export
        document.getElementById('btn-cpd-export')?.addEventListener('click', () => {
            window.open(api.downloadUrl('/teacher-api/cpd/export/download'), '_blank');
        });
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── CPD QUICK LOG ───
async function renderCpdQuickLog(el, api) {
    try {
        const templates = await api.getCpdTemplates();
        let html = '<div class="dash-scroll">';
        html += `<div style="background:linear-gradient(135deg,#7c3aed,#4f46e5);border-radius:var(--radius);padding:16px;color:#fff;margin-bottom:12px;box-shadow:var(--shadow-md)">
            <div style="font-size:1rem;font-weight:700">Quick Log CPD</div>
            <div style="font-size:0.72rem;opacity:0.65">One-tap logging for common activities</div>
        </div>`;

        html += `<div class="form-group" style="margin-bottom:12px"><label class="form-label">Date</label><input type="date" id="ql-date" class="form-input" value="${new Date().toISOString().split('T')[0]}" style="padding:10px"></div>`;

        if (templates.length === 0) {
            html += '<div class="card"><div class="card-empty">No templates available.</div></div>';
        } else {
            // Mandatory
            const mandatory = templates.filter(t => t.is_mandatory);
            const voluntary = templates.filter(t => !t.is_mandatory);

            if (mandatory.length > 0) {
                html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--red)"></span>Mandatory</div></div><div class="card-body" style="padding:0">`;
                for (const t of mandatory) {
                    html += `<div class="list-item" style="gap:8px"><div style="flex:1"><div class="list-title">${t.title}</div><div class="list-sub">${t.type.replace(/_/g,' ')} · ${t.provider || 'School'} · ${t.hours}h · ${t.points}pts</div></div>
                        <button class="btn btn-primary btn-quick-log" data-id="${t.id}" style="width:auto;padding:8px 12px;font-size:0.7rem">${SVG.plus} Log</button></div>`;
                }
                html += '</div></div>';
            }
            if (voluntary.length > 0) {
                html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--blue)"></span>Voluntary</div></div><div class="card-body" style="padding:0">`;
                for (const t of voluntary) {
                    html += `<div class="list-item" style="gap:8px"><div style="flex:1"><div class="list-title">${t.title}</div><div class="list-sub">${t.type.replace(/_/g,' ')} · ${t.hours}h · ${t.points}pts</div></div>
                        <button class="btn btn-primary btn-quick-log" data-id="${t.id}" style="width:auto;padding:8px 12px;font-size:0.7rem">${SVG.plus} Log</button></div>`;
                }
                html += '</div></div>';
            }
        }

        html += '</div>';
        el.innerHTML = html;

        el.querySelectorAll('.btn-quick-log').forEach(btn => {
            btn.addEventListener('click', async () => {
                const date = document.getElementById('ql-date').value;
                if (!date) { alert('Select a date.'); return; }
                btn.disabled = true; btn.textContent = '...';
                try {
                    const res = await api.quickLogCpd(parseInt(btn.dataset.id), date);
                    btn.innerHTML = `${SVG.check} +${res.hours}h`;
                    btn.style.background = 'var(--green)';
                } catch (err) { alert(err.message); btn.textContent = 'Log'; btn.style.background = ''; btn.disabled = false; }
            });
        });
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── CPD CERTIFICATES ───
async function renderCpdCerts(el, api) {
    try {
        const certs = await api.getCpdCertificates();
        let html = '<div class="dash-scroll">';
        html += `<div style="background:linear-gradient(135deg,#7c3aed,#4f46e5);border-radius:var(--radius);padding:16px;color:#fff;margin-bottom:12px;box-shadow:var(--shadow-md)">
            <div style="font-size:1rem;font-weight:700">Certificate Gallery</div>
            <div style="font-size:0.72rem;opacity:0.65">${certs.length} certificate${certs.length !== 1 ? 's' : ''}</div>
        </div>`;

        if (certs.length === 0) {
            html += '<div class="card"><div class="card-empty">No certificates uploaded yet. Upload certificates when logging CPD activities.</div></div>';
        } else {
            html += '<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px">';
            for (const c of certs) {
                const isImg = c.certificate_url.match(/\.(jpg|jpeg|png)$/i);
                html += `<a href="${c.certificate_url}" target="_blank" class="card" style="text-decoration:none;margin:0">
                    <div style="height:80px;background:${isImg ? `url(${c.certificate_url}) center/cover` : 'linear-gradient(135deg,rgba(124,58,237,0.1),rgba(59,130,246,0.1))'};display:flex;align-items:center;justify-content:center;border-radius:var(--radius) var(--radius) 0 0">
                        ${!isImg ? '<div style="font-size:1.5rem">📄</div>' : ''}
                    </div>
                    <div style="padding:8px 10px">
                        <div class="list-title" style="font-size:0.72rem;line-height:1.3">${c.title}</div>
                        <div class="text-xs text-gray">${c.date} · ${c.hours}h</div>
                        <div class="text-xs text-gray">${c.provider || ''}</div>
                    </div>
                </a>`;
            }
            html += '</div>';
        }
        html += '</div>';
        el.innerHTML = html;
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── CPD APPROVALS ───
async function renderCpdApprovals(el, api) {
    try {
        const pending = await api.getPendingApprovals();
        let html = '<div class="dash-scroll">';
        html += `<div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border-radius:var(--radius);padding:16px;color:#fff;margin-bottom:12px;box-shadow:var(--shadow-md)">
            <div style="font-size:1rem;font-weight:700">CPD Approvals</div>
            <div style="font-size:0.72rem;opacity:0.65">${pending.length} pending review</div>
        </div>`;

        if (pending.length === 0) {
            html += '<div class="card"><div class="card-empty">No activities pending approval.</div></div>';
        } else {
            for (const a of pending) {
                html += `<div class="card"><div style="padding:12px 14px">
                    <div class="flex-between" style="margin-bottom:6px"><div class="list-title">${a.teacher}</div>${a.is_mandatory ? '<span class="badge badge-red">Mandatory</span>' : '<span class="badge badge-blue">Voluntary</span>'}</div>
                    <div class="list-sub">${a.title} · ${a.type} · ${a.date} · ${a.hours}h · ${a.points}pts</div>
                    <div style="display:flex;gap:6px;margin-top:8px">
                        <button class="btn btn-primary btn-approve" data-id="${a.id}" style="flex:1;padding:8px;font-size:0.72rem">${SVG.check} Approve</button>
                        <button class="btn btn-outline btn-reject" data-id="${a.id}" style="flex:1;padding:8px;font-size:0.72rem;color:var(--red);border-color:var(--red)">Reject</button>
                    </div>
                </div></div>`;
            }
        }
        html += '</div>';
        el.innerHTML = html;

        el.querySelectorAll('.btn-approve').forEach(btn => {
            btn.addEventListener('click', async () => {
                btn.disabled = true; btn.textContent = '...';
                try { await api.approveCpdActivity(btn.dataset.id, 'approved', ''); btn.closest('.card').style.opacity = '0.4'; btn.textContent = 'Approved'; btn.style.background = 'var(--green)'; }
                catch (err) { alert(err.message); btn.disabled = false; btn.textContent = 'Approve'; }
            });
        });
        el.querySelectorAll('.btn-reject').forEach(btn => {
            btn.addEventListener('click', async () => {
                const reason = prompt('Reason for rejection:');
                if (reason === null) return;
                btn.disabled = true; btn.textContent = '...';
                try { await api.approveCpdActivity(btn.dataset.id, 'rejected', reason); btn.closest('.card').style.opacity = '0.4'; btn.textContent = 'Rejected'; btn.style.background = 'var(--red)'; btn.style.color = '#fff'; }
                catch (err) { alert(err.message); btn.disabled = false; btn.textContent = 'Reject'; }
            });
        });
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── SCHOOL-WIDE CPD REPORT ───
async function renderSchoolCpdReport(el, api) {
    try {
        const data = await api.getSchoolWideCpdReport();
        let html = '<div class="dash-scroll">';

        // Header
        html += `<div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border-radius:var(--radius);padding:16px;color:#fff;margin-bottom:12px;box-shadow:var(--shadow-md)">
            <div style="font-size:1rem;font-weight:700">School CPD Report ${data.year}</div>
            <div style="font-size:0.72rem;opacity:0.65">${data.total_teachers} teachers · ${data.school_total_hours}h total</div>
        </div>`;

        // Compliance KPIs
        html += `<div class="kpi-strip" style="grid-template-columns:repeat(4,1fr)">
            <div class="kpi"><div class="kpi-val kpi-green">${data.compliant}</div><div class="kpi-lbl">Compliant</div></div>
            <div class="kpi"><div class="kpi-val kpi-amber">${data.on_track}</div><div class="kpi-lbl">On Track</div></div>
            <div class="kpi"><div class="kpi-val" style="color:var(--red)">${data.behind}</div><div class="kpi-lbl">Behind</div></div>
            <div class="kpi"><div class="kpi-val kpi-blue">${data.compliance_rate}%</div><div class="kpi-lbl">Rate</div></div>
        </div>`;

        // Teacher list
        html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--primary)"></span>All Teachers</div></div><div class="card-body" style="padding:0">`;
        for (const t of data.teachers || []) {
            const statusColor = t.status === 'compliant' ? 'var(--green)' : t.status === 'on_track' ? 'var(--amber)' : 'var(--red)';
            const statusLabel = t.status === 'compliant' ? 'Compliant' : t.status === 'on_track' ? 'On Track' : 'Behind';
            html += `<div class="list-item" style="flex-direction:column;align-items:stretch;gap:4px;padding:10px 14px">
                <div class="flex-between"><div class="list-title">${t.name}${t.is_class_teacher ? ' <span class="badge badge-blue" style="margin-left:4px">CT</span>' : ''}</div><span class="badge" style="background:${statusColor}20;color:${statusColor}">${statusLabel}</span></div>
                <div class="progress"><div class="progress-bar" style="width:${t.progress}%;background:${statusColor}"></div></div>
                <div class="text-xs text-gray">${t.hours}/${t.target}h · ${t.activities} activities · ${t.certificates} certs · ${t.points} pts</div>
            </div>`;
        }
        html += '</div></div>';

        // Export button
        html += `<button id="btn-export-school" class="btn btn-outline" style="font-size:0.72rem">${SVG.chart} Export School Report</button>`;
        html += '</div>';
        el.innerHTML = html;

        document.getElementById('btn-export-school')?.addEventListener('click', () => {
            window.open(api.downloadUrl('/teacher-api/cpd/school-report/csv'), '_blank');
        });
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── CPD ACTIVITIES ───
async function renderCpdActivities(el, api) {
    try {
        const activities = await api.getCpdActivities();
        let html = '<div class="dash-scroll">';
        html += `<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <div style="font-size:1rem;font-weight:700">CPD Activities</div>
            <button id="btn-add-cpd" class="btn btn-primary" style="width:auto;padding:8px 14px;font-size:0.72rem">${SVG.plus} Log Activity</button>
        </div>`;

        if (activities.length === 0) {
            html += '<div class="card"><div class="card-empty">No CPD activities logged yet. Tap "Log Activity" to record your first one.</div></div>';
        } else {
            for (const a of activities) {
                const statusBadge = a.status === 'completed' ? '<span class="badge badge-green">Done</span>' : a.status === 'in_progress' ? '<span class="badge badge-amber">Active</span>' : '<span class="badge badge-blue">Planned</span>';
                html += `<div class="card"><div style="padding:12px 14px">
                    <div class="flex-between" style="margin-bottom:6px"><div class="list-title">${a.title}</div>${statusBadge}</div>
                    <div class="list-sub">${a.type_label} · ${a.start_date}${a.end_date && a.end_date !== a.start_date ? ' — ' + a.end_date : ''} · <strong class="text-blue">${a.hours}h</strong></div>
                    ${a.provider ? `<div class="text-xs text-gray mt-2">Provider: ${a.provider}</div>` : ''}
                    ${a.description ? `<div class="text-sm mt-2" style="color:var(--text2);line-height:1.5">${a.description}</div>` : ''}
                    ${a.reflection ? `<div style="margin-top:6px;padding:8px 10px;background:rgba(124,58,237,0.05);border-radius:6px;border-left:3px solid #7c3aed"><div class="text-xs bold" style="color:#7c3aed;margin-bottom:2px">Reflection</div><div class="text-xs" style="color:var(--text2);line-height:1.5">${a.reflection}</div></div>` : ''}
                    ${a.key_learnings ? `<div style="margin-top:6px;padding:8px 10px;background:rgba(5,150,105,0.05);border-radius:6px;border-left:3px solid var(--green)"><div class="text-xs bold text-green" style="margin-bottom:2px">Key Learnings</div><div class="text-xs" style="color:var(--text2);line-height:1.5">${a.key_learnings}</div></div>` : ''}
                    <div style="display:flex;gap:6px;margin-top:8px;flex-wrap:wrap">
                        ${a.certificate_url ? `<a href="${a.certificate_url}" target="_blank" class="btn btn-outline" style="text-decoration:none;font-size:0.7rem;padding:6px 10px">View Certificate</a>` : ''}
                        <button class="btn btn-outline btn-del-cpd" data-id="${a.id}" style="font-size:0.7rem;padding:6px 10px;color:var(--red);border-color:var(--red);width:auto">Delete</button>
                    </div>
                </div></div>`;
            }
        }

        html += '</div>';
        el.innerHTML = html;

        document.getElementById('btn-add-cpd')?.addEventListener('click', () => showCpdActivityModal(api, el));

        // Delete activity buttons
        el.querySelectorAll('.btn-del-cpd').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirm('Delete this CPD activity?')) return;
                btn.disabled = true; btn.textContent = '...';
                try { await api.deleteCpdActivity(btn.dataset.id); renderCpdActivities(el, api); }
                catch (err) { alert(err.message); btn.disabled = false; btn.textContent = 'Delete'; }
            });
        });
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

function showCpdActivityModal(api, pageEl) {
    if (document.getElementById('cpd-modal')) return;
    const modal = document.createElement('div');
    modal.id = 'cpd-modal'; modal.className = 'modal-overlay';
    modal.innerHTML = `<div class="modal">
        <div class="flex-between" style="margin-bottom:12px"><div class="list-title" style="font-size:0.95rem">Log CPD Activity</div><button id="close-cpd" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--text3)">&times;</button></div>
        <div class="form-group"><label class="form-label">Title</label><input type="text" id="cpd-title" class="form-input" placeholder="e.g. ICT Integration Workshop" style="padding:10px 12px"></div>
        <div class="form-group"><label class="form-label">Type</label><select id="cpd-type" class="form-input" style="padding:10px 12px">
            <option value="workshop">Workshop</option><option value="course">Course</option><option value="conference">Conference</option><option value="seminar">Seminar</option>
            <option value="peer_observation">Peer Observation</option><option value="self_study">Self Study</option><option value="mentoring">Mentoring</option>
            <option value="online_training">Online Training</option><option value="research">Research</option><option value="other">Other</option>
        </select></div>
        <div class="form-group"><label class="form-label">Provider / Organiser</label><input type="text" id="cpd-provider" class="form-input" placeholder="e.g. DEBS, ZEST, school" style="padding:10px 12px"></div>
        <div style="display:flex;gap:8px"><div class="form-group" style="flex:1"><label class="form-label">Start Date</label><input type="date" id="cpd-start" class="form-input" style="padding:10px 12px"></div><div class="form-group" style="flex:1"><label class="form-label">End Date</label><input type="date" id="cpd-end" class="form-input" style="padding:10px 12px"></div></div>
        <div class="form-group"><label class="form-label">Hours</label><input type="number" id="cpd-hours" class="form-input" placeholder="e.g. 8" min="0.5" step="0.5" style="padding:10px 12px"></div>
        <div class="form-group"><label class="form-label">Description</label><textarea id="cpd-desc" class="form-input" rows="2" placeholder="What was covered..." style="padding:10px 12px;resize:vertical;font-family:inherit"></textarea></div>
        <div class="form-group"><label class="form-label">Reflection</label><textarea id="cpd-reflection" class="form-input" rows="2" placeholder="What did you learn? How will it impact your teaching?" style="padding:10px 12px;resize:vertical;font-family:inherit"></textarea></div>
        <div class="form-group"><label class="form-label">Key Learnings</label><textarea id="cpd-learnings" class="form-input" rows="2" placeholder="Key takeaways..." style="padding:10px 12px;resize:vertical;font-family:inherit"></textarea></div>
        <div class="form-group"><label class="form-label">Status</label><select id="cpd-status" class="form-input" style="padding:10px 12px"><option value="completed">Completed</option><option value="in_progress">In Progress</option><option value="planned">Planned</option></select></div>
        <div class="form-group"><label class="form-label">Certificate (optional)</label><div class="file-upload-area" id="cpd-cert-area" style="padding:10px"><input type="file" id="cpd-cert-file" accept=".jpg,.jpeg,.png,.pdf" style="display:none"><div style="font-size:0.72rem;color:var(--text2)"><strong style="color:var(--primary)">Tap to attach</strong> certificate</div></div><div id="cpd-cert-preview" style="display:none"></div></div>
        <div id="cpd-err" class="form-error" style="display:none;margin-bottom:8px"></div>
        <button id="cpd-submit" class="btn btn-primary" style="padding:11px">Save Activity</button>
    </div>`;
    document.body.appendChild(modal);
    document.getElementById('close-cpd').onclick = () => modal.remove();
    modal.addEventListener('click', (e) => { if (e.target === modal) modal.remove(); });

    let certFile = null;
    document.getElementById('cpd-cert-area').addEventListener('click', () => document.getElementById('cpd-cert-file').click());
    document.getElementById('cpd-cert-file').addEventListener('change', (e) => {
        if (e.target.files[0]) { certFile = e.target.files[0]; document.getElementById('cpd-cert-area').style.display = 'none'; const p = document.getElementById('cpd-cert-preview'); p.style.display = 'block'; p.innerHTML = `<div style="display:flex;align-items:center;gap:6px;padding:6px 10px;background:rgba(59,130,246,0.06);border-radius:6px;font-size:0.72rem">📄 ${certFile.name} <button id="rm-cert" style="background:none;border:none;color:var(--red);cursor:pointer">&times;</button></div>`; document.getElementById('rm-cert').onclick = () => { certFile = null; p.style.display = 'none'; document.getElementById('cpd-cert-area').style.display = ''; }; }
    });

    document.getElementById('cpd-submit').addEventListener('click', async () => {
        const title = document.getElementById('cpd-title').value.trim();
        const hours = document.getElementById('cpd-hours').value;
        const startDate = document.getElementById('cpd-start').value;
        const errEl = document.getElementById('cpd-err');
        const btn = document.getElementById('cpd-submit');
        if (!title || !hours || !startDate) { errEl.textContent = 'Title, date and hours are required.'; errEl.style.display = ''; return; }
        errEl.style.display = 'none'; btn.disabled = true; btn.innerHTML = '<div class="btn-spinner"></div> Saving...';
        try {
            await api.createCpdActivity({
                title, type: document.getElementById('cpd-type').value, provider: document.getElementById('cpd-provider').value,
                start_date: startDate, end_date: document.getElementById('cpd-end').value || null, hours: parseFloat(hours),
                description: document.getElementById('cpd-desc').value, reflection: document.getElementById('cpd-reflection').value,
                key_learnings: document.getElementById('cpd-learnings').value, status: document.getElementById('cpd-status').value,
                certificate: certFile,
            });
            modal.remove(); renderCpdActivities(pageEl, api);
        } catch (err) { errEl.textContent = err.message; errEl.style.display = ''; btn.disabled = false; btn.innerHTML = 'Save Activity'; }
    });
}

// ─── CPD RESOURCES ───
async function renderCpdResources(el, api) {
    try {
        const resources = await api.getCpdResources();
        let html = '<div class="dash-scroll">';
        html += `<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <div style="font-size:1rem;font-weight:700">Resource Library</div>
            <button id="btn-share-res" class="btn btn-primary" style="width:auto;padding:8px 14px;font-size:0.72rem">${SVG.plus} Share</button>
        </div>`;

        if (resources.length === 0) {
            html += '<div class="card"><div class="card-empty">No resources shared yet. Be the first to share!</div></div>';
        } else {
            for (const r of resources) {
                html += `<div class="card"><div style="padding:12px 14px">
                    <div class="flex-between"><div class="list-title">${r.title}</div><span class="badge badge-blue">${r.type_label}</span></div>
                    <div class="list-sub">${r.shared_by} · ${r.date}${r.subject ? ' · ' + r.subject : ''}${r.grade ? ' · ' + r.grade : ''}</div>
                    ${r.description ? `<div class="text-sm mt-2" style="color:var(--text2)">${r.description}</div>` : ''}
                    <div style="display:flex;gap:6px;margin-top:8px">
                        ${r.file_url ? `<a href="${r.file_url}" target="_blank" class="btn btn-outline" style="text-decoration:none;font-size:0.7rem;padding:6px 10px;flex:1">${SVG.clipboard} Download${r.file_name ? ' · ' + r.file_name : ''}</a>` : ''}
                        ${r.external_url ? `<a href="${r.external_url}" target="_blank" class="btn btn-outline" style="text-decoration:none;font-size:0.7rem;padding:6px 10px;flex:1">Open Link</a>` : ''}
                    </div>
                </div></div>`;
            }
        }
        html += '</div>';
        el.innerHTML = html;

        document.getElementById('btn-share-res')?.addEventListener('click', () => {
            if (document.getElementById('res-modal')) return;
            const modal = document.createElement('div');
            modal.id = 'res-modal'; modal.className = 'modal-overlay';
            modal.innerHTML = `<div class="modal">
                <div class="flex-between" style="margin-bottom:12px"><div class="list-title" style="font-size:0.95rem">Share Resource</div><button id="close-res" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--text3)">&times;</button></div>
                <div class="form-group"><label class="form-label">Title</label><input type="text" id="res-title" class="form-input" placeholder="Resource title" style="padding:10px 12px"></div>
                <div class="form-group"><label class="form-label">Type</label><select id="res-type" class="form-input" style="padding:10px 12px"><option value="lesson_plan">Lesson Plan</option><option value="worksheet">Worksheet</option><option value="past_paper">Past Paper</option><option value="presentation">Presentation</option><option value="video_link">Video Link</option><option value="article">Article</option><option value="template">Template</option><option value="other">Other</option></select></div>
                <div style="display:flex;gap:8px"><div class="form-group" style="flex:1"><label class="form-label">Subject</label><input type="text" id="res-subject" class="form-input" placeholder="e.g. Math" style="padding:10px 12px"></div><div class="form-group" style="flex:1"><label class="form-label">Grade</label><input type="text" id="res-grade" class="form-input" placeholder="e.g. Form 1" style="padding:10px 12px"></div></div>
                <div class="form-group"><label class="form-label">Description</label><textarea id="res-desc" class="form-input" rows="2" style="padding:10px 12px;resize:vertical;font-family:inherit" placeholder="Brief description..."></textarea></div>
                <div class="form-group"><label class="form-label">File</label><input type="file" id="res-file" class="form-input" style="padding:8px"></div>
                <div class="form-group"><label class="form-label">Or External Link</label><input type="url" id="res-url" class="form-input" placeholder="https://..." style="padding:10px 12px"></div>
                <div id="res-err" class="form-error" style="display:none;margin-bottom:8px"></div>
                <button id="res-submit" class="btn btn-primary" style="padding:11px">Share Resource</button>
            </div>`;
            document.body.appendChild(modal);
            document.getElementById('close-res').onclick = () => modal.remove();
            modal.addEventListener('click', (e) => { if (e.target === modal) modal.remove(); });

            document.getElementById('res-submit').addEventListener('click', async () => {
                const title = document.getElementById('res-title').value.trim();
                const errEl = document.getElementById('res-err');
                const btn = document.getElementById('res-submit');
                if (!title) { errEl.textContent = 'Title is required.'; errEl.style.display = ''; return; }
                errEl.style.display = 'none'; btn.disabled = true; btn.innerHTML = '<div class="btn-spinner"></div> Sharing...';
                try {
                    const fileInput = document.getElementById('res-file');
                    await api.shareCpdResource({
                        title, type: document.getElementById('res-type').value, subject: document.getElementById('res-subject').value,
                        grade: document.getElementById('res-grade').value, description: document.getElementById('res-desc').value,
                        file: fileInput.files[0] || null, external_url: document.getElementById('res-url').value || null,
                    });
                    modal.remove(); renderCpdResources(el, api);
                } catch (err) { errEl.textContent = err.message; errEl.style.display = ''; btn.disabled = false; btn.innerHTML = 'Share Resource'; }
            });
        });
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── CPD OBSERVATIONS ───
async function renderCpdObservations(el, api) {
    try {
        const [observations, staff] = await Promise.all([api.getCpdObservations(), api.getStaffDirectory()]);
        let html = '<div class="dash-scroll">';
        html += `<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <div style="font-size:1rem;font-weight:700">Teaching Observations</div>
            <button id="btn-new-obs" class="btn btn-primary" style="width:auto;padding:8px 14px;font-size:0.72rem">${SVG.plus} Observe</button>
        </div>`;

        if (observations.length === 0) {
            html += '<div class="card"><div class="card-empty">No observations recorded yet.</div></div>';
        } else {
            for (const o of observations) {
                html += `<div class="card"><div style="padding:14px">
                    <div class="flex-between" style="margin-bottom:8px"><div class="list-title">${o.subject || 'Lesson Observation'}${o.topic ? ' — ' + o.topic : ''}</div>${o.rating ? `<div class="mono bold text-blue" style="font-size:1rem">${o.rating}/5</div>` : ''}</div>
                    <div class="list-sub">${o.date} · ${o.observer} · ${o.class_observed || ''}</div>
                    ${o.strengths ? `<div style="margin-top:8px;padding:8px 10px;background:rgba(5,150,105,0.05);border-radius:6px;border-left:3px solid var(--green)"><div class="text-xs bold text-green" style="margin-bottom:2px">Strengths</div><div class="text-xs" style="color:var(--text2);line-height:1.5">${o.strengths}</div></div>` : ''}
                    ${o.areas_for_improvement ? `<div style="margin-top:6px;padding:8px 10px;background:rgba(217,119,6,0.05);border-radius:6px;border-left:3px solid var(--amber)"><div class="text-xs bold text-amber" style="margin-bottom:2px">Areas for Improvement</div><div class="text-xs" style="color:var(--text2);line-height:1.5">${o.areas_for_improvement}</div></div>` : ''}
                    ${o.recommendations ? `<div style="margin-top:6px;padding:8px 10px;background:rgba(59,130,246,0.05);border-radius:6px;border-left:3px solid var(--blue)"><div class="text-xs bold text-blue" style="margin-bottom:2px">Recommendations</div><div class="text-xs" style="color:var(--text2);line-height:1.5">${o.recommendations}</div></div>` : ''}
                    ${o.teacher_reflection ? `<div style="margin-top:6px;padding:8px 10px;background:rgba(124,58,237,0.05);border-radius:6px;border-left:3px solid #7c3aed"><div class="text-xs bold" style="color:#7c3aed;margin-bottom:2px">My Reflection</div><div class="text-xs" style="color:var(--text2);line-height:1.5">${o.teacher_reflection}</div></div>` : `
                        <div style="margin-top:8px"><textarea class="form-input obs-reflection" data-obs="${o.id}" rows="2" placeholder="Add your reflection on this observation..." style="padding:8px;font-size:0.78rem;resize:vertical;font-family:inherit"></textarea>
                        <button class="btn btn-primary btn-save-obs mt-2" data-obs="${o.id}" style="width:auto;padding:6px 14px;font-size:0.7rem">${SVG.check} Save Reflection</button></div>
                    `}
                </div></div>`;
            }
        }
        html += '</div>';
        el.innerHTML = html;

        el.querySelectorAll('.btn-save-obs').forEach(btn => {
            btn.addEventListener('click', async () => {
                const obsId = btn.dataset.obs;
                const reflection = el.querySelector(`.obs-reflection[data-obs="${obsId}"]`).value.trim();
                if (!reflection) return;
                btn.disabled = true; btn.textContent = 'Saving...';
                try {
                    await api.saveObservationReflection(obsId, reflection);
                    btn.innerHTML = `${SVG.check} Saved!`; btn.style.background = 'var(--green)';
                } catch (err) { btn.textContent = err.message; btn.style.background = 'var(--red)'; setTimeout(() => { btn.innerHTML = `${SVG.check} Save`; btn.style.background = ''; btn.disabled = false; }, 2000); }
            });
        });

        // New observation modal
        document.getElementById('btn-new-obs')?.addEventListener('click', () => {
            if (document.getElementById('obs-modal')) return;
            const modal = document.createElement('div');
            modal.id = 'obs-modal'; modal.className = 'modal-overlay';
            modal.innerHTML = `<div class="modal">
                <div class="flex-between" style="margin-bottom:12px"><div class="list-title" style="font-size:0.95rem">Record Observation</div><button id="close-obs" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--text3)">&times;</button></div>
                <div class="form-group"><label class="form-label">Teacher Observed</label><select id="obs-teacher" class="form-input" style="padding:10px"><option value="">Select teacher...</option>${staff.map(t => `<option value="${t.user_id}">${t.name} - ${t.grade || ''} ${t.class_section || ''}</option>`).join('')}</select></div>
                <div class="form-group"><label class="form-label">Date</label><input type="date" id="obs-date" class="form-input" value="${new Date().toISOString().split('T')[0]}" style="padding:10px"></div>
                <div style="display:flex;gap:8px"><div class="form-group" style="flex:1"><label class="form-label">Subject</label><input type="text" id="obs-subject" class="form-input" style="padding:10px" placeholder="e.g. Mathematics"></div><div class="form-group" style="flex:1"><label class="form-label">Class</label><input type="text" id="obs-class" class="form-input" style="padding:10px" placeholder="e.g. Form 1 A"></div></div>
                <div class="form-group"><label class="form-label">Topic</label><input type="text" id="obs-topic" class="form-input" style="padding:10px" placeholder="Lesson topic"></div>
                <div class="form-group"><label class="form-label">Rating (1-5)</label><select id="obs-rating" class="form-input" style="padding:10px"><option value="">Select...</option><option value="1">1 - Poor</option><option value="2">2 - Below Average</option><option value="3">3 - Average</option><option value="4">4 - Good</option><option value="5">5 - Excellent</option></select></div>
                <div class="form-group"><label class="form-label">Strengths</label><textarea id="obs-strengths" class="form-input" rows="2" style="padding:10px;resize:vertical;font-family:inherit" placeholder="What went well..."></textarea></div>
                <div class="form-group"><label class="form-label">Areas for Improvement</label><textarea id="obs-improve" class="form-input" rows="2" style="padding:10px;resize:vertical;font-family:inherit" placeholder="What could be better..."></textarea></div>
                <div class="form-group"><label class="form-label">Recommendations</label><textarea id="obs-recs" class="form-input" rows="2" style="padding:10px;resize:vertical;font-family:inherit" placeholder="Suggestions..."></textarea></div>
                <div id="obs-err" class="form-error" style="display:none;margin-bottom:8px"></div>
                <button id="obs-submit" class="btn btn-primary" style="padding:11px">Save Observation</button>
            </div>`;
            document.body.appendChild(modal);
            document.getElementById('close-obs').onclick = () => modal.remove();
            modal.addEventListener('click', (e) => { if (e.target === modal) modal.remove(); });

            document.getElementById('obs-submit').addEventListener('click', async () => {
                const teacherId = document.getElementById('obs-teacher').value;
                const date = document.getElementById('obs-date').value;
                const errEl = document.getElementById('obs-err');
                const btn = document.getElementById('obs-submit');
                if (!teacherId || !date) { errEl.textContent = 'Teacher and date required.'; errEl.style.display = ''; return; }
                errEl.style.display = 'none'; btn.disabled = true; btn.innerHTML = '<div class="btn-spinner"></div> Saving...';
                try {
                    await api.createObservation({
                        teacher_user_id: parseInt(teacherId), observation_date: date,
                        subject: document.getElementById('obs-subject').value, class_observed: document.getElementById('obs-class').value,
                        topic: document.getElementById('obs-topic').value, rating: document.getElementById('obs-rating').value ? parseInt(document.getElementById('obs-rating').value) : null,
                        strengths: document.getElementById('obs-strengths').value, areas_for_improvement: document.getElementById('obs-improve').value,
                        recommendations: document.getElementById('obs-recs').value,
                    });
                    modal.remove(); renderCpdObservations(el, api);
                } catch (err) { errEl.textContent = err.message; errEl.style.display = ''; btn.disabled = false; btn.innerHTML = 'Save Observation'; }
            });
        });
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── HOMEWORK ANALYTICS ───
async function renderHwAnalytics(el, api) {
    try {
        const data = await api.getHomeworkAnalytics();
        let html = '<div class="dash-scroll">';
        html += `<div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border-radius:var(--radius);padding:16px;color:#fff;margin-bottom:12px;box-shadow:var(--shadow-md)">
            <div style="font-size:1rem;font-weight:700">Homework Analytics</div>
            <div style="font-size:0.72rem;opacity:0.65">Performance overview across all classes</div>
        </div>`;

        html += `<div class="kpi-strip" style="grid-template-columns:repeat(3,1fr)">
            <div class="kpi"><div class="kpi-val kpi-blue">${data.total_homework}</div><div class="kpi-lbl">Total HW</div></div>
            <div class="kpi"><div class="kpi-val kpi-green">${data.total_graded}</div><div class="kpi-lbl">Graded</div></div>
            <div class="kpi"><div class="kpi-val kpi-red">${data.pending_grading}</div><div class="kpi-lbl">Pending</div></div>
        </div>`;

        html += `<div class="kpi-strip" style="grid-template-columns:repeat(3,1fr)">
            <div class="kpi"><div class="kpi-val kpi-blue">${data.total_submissions}</div><div class="kpi-lbl">Submissions</div></div>
            <div class="kpi"><div class="kpi-val kpi-amber">${data.late_submissions}</div><div class="kpi-lbl">Late</div></div>
            <div class="kpi"><div class="kpi-val kpi-green">${data.average_score ?? '-'}</div><div class="kpi-lbl">Avg Score</div></div>
        </div>`;

        // Per subject
        if (data.by_subject && data.by_subject.length) {
            html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--primary)"></span>By Subject</div></div><div class="card-body" style="padding:0">`;
            for (const s of data.by_subject) {
                html += `<div class="list-item"><div style="flex:1"><div class="list-title">${s.subject}</div><div class="list-sub">${s.homework_count} homework · ${s.submissions} submissions · ${s.graded} graded</div></div><div class="mono bold ${s.avg_score >= 50 ? 'text-green' : 'text-red'}">${s.avg_score ?? '-'}%</div></div>`;
            }
            html += '</div></div>';
        }

        html += '</div>';
        el.innerHTML = html;
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── PARENT CONTACTS ───
async function renderParentContacts(el, api) {
    try {
        const contacts = await api.getParentContacts();
        let html = '<div class="dash-scroll">';
        html += `<div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border-radius:var(--radius);padding:16px;color:#fff;margin-bottom:12px;box-shadow:var(--shadow-md)">
            <div style="font-size:1rem;font-weight:700">Parent Contacts</div>
            <div style="font-size:0.72rem;opacity:0.65">${contacts.length} students</div>
        </div>`;

        if (contacts.length === 0) { html += '<div class="card"><div class="card-empty">No contacts available.</div></div>'; }
        else {
            html += '<div class="card"><div class="card-body" style="padding:0">';
            for (const c of contacts) {
                html += `<div class="list-item" style="gap:8px">
                    <div class="att-avatar">${c.student_name[0]}</div>
                    <div style="flex:1;min-width:0">
                        <div class="list-title">${c.student_name}</div>
                        <div class="list-sub">${c.parent_name} · ${c.relationship}</div>
                    </div>
                    <div style="display:flex;gap:4px;flex-shrink:0">
                        ${c.parent_phone !== '-' ? `<a href="tel:${c.parent_phone}" class="badge badge-green" style="text-decoration:none;padding:4px 8px">Call</a>` : ''}
                        ${c.parent_phone !== '-' ? `<a href="https://wa.me/${c.parent_phone.replace(/^0/, '260')}" target="_blank" class="badge badge-green" style="text-decoration:none;padding:4px 8px;background:#25D366;color:#fff">WA</a>` : ''}
                    </div>
                </div>`;
            }
            html += '</div></div>';
        }
        html += '</div>';
        el.innerHTML = html;
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── LEAVE ───
async function renderLeave(el, api) {
    try {
        const [balances, applications] = await Promise.all([api.getLeaveBalances(), api.getLeaveApplications()]);
        let html = '<div class="dash-scroll">';
        html += `<div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border-radius:var(--radius);padding:16px;color:#fff;margin-bottom:12px;box-shadow:var(--shadow-md)">
            <div style="display:flex;justify-content:space-between;align-items:center">
                <div><div style="font-size:1rem;font-weight:700">Leave Management</div><div style="font-size:0.72rem;opacity:0.65">${new Date().getFullYear()}</div></div>
                <button id="btn-apply-leave" class="badge" style="background:rgba(255,255,255,0.2);color:#fff;padding:6px 12px;border:none;cursor:pointer;font-family:inherit;font-size:0.72rem">${SVG.plus} Apply</button>
            </div>
        </div>`;

        // Balances
        if (balances.length > 0) {
            html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--green)"></span>Leave Balance</div></div><div class="card-body" style="padding:0">`;
            for (const b of balances) {
                const pct = b.allocated > 0 ? Math.round((b.used / b.allocated) * 100) : 0;
                html += `<div class="list-item" style="flex-direction:column;align-items:stretch;gap:6px;padding:12px 14px">
                    <div class="flex-between"><div class="list-title">${b.type}</div><span class="badge ${b.remaining > 0 ? 'badge-green' : 'badge-red'}">${b.remaining} days left</span></div>
                    <div class="progress"><div class="progress-bar ${pct > 80 ? 'bg-red' : pct > 50 ? 'bg-amber' : 'bg-green'}" style="width:${Math.min(pct,100)}%"></div></div>
                    <div class="text-xs text-gray">${b.used}/${b.allocated} used${b.carried_forward > 0 ? ' · ' + b.carried_forward + ' carried forward' : ''}</div>
                </div>`;
            }
            html += '</div></div>';
        }

        // Applications
        html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--blue)"></span>Applications</div><span class="badge badge-gray">${applications.length}</span></div><div class="card-body" style="padding:0">`;
        if (applications.length === 0) { html += '<div class="card-empty">No leave applications.</div>'; }
        else {
            for (const a of applications) {
                const statusBadge = a.status === 'approved' ? '<span class="badge badge-green">Approved</span>' : a.status === 'rejected' ? '<span class="badge badge-red">Rejected</span>' : a.status === 'pending' ? '<span class="badge badge-amber">Pending</span>' : `<span class="badge badge-blue">${a.status_label}</span>`;
                html += `<div class="list-item" style="flex-direction:column;align-items:stretch;gap:4px;padding:12px 14px">
                    <div class="flex-between"><div class="list-title">${a.type} · ${a.days} day${a.days > 1 ? 's' : ''}</div>${statusBadge}</div>
                    <div class="list-sub">${a.start_date} — ${a.end_date} · ${a.reference}</div>
                    <div class="text-xs text-gray">${a.reason}</div>
                </div>`;
            }
        }
        html += '</div></div></div>';
        el.innerHTML = html;

        // Apply leave modal
        document.getElementById('btn-apply-leave')?.addEventListener('click', () => showLeaveModal(api, el));
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

function showLeaveModal(api, pageEl) {
    if (document.getElementById('leave-modal')) return;
    const modal = document.createElement('div');
    modal.id = 'leave-modal'; modal.className = 'modal-overlay';
    modal.innerHTML = `<div class="modal">
        <div class="flex-between" style="margin-bottom:14px"><div class="list-title" style="font-size:0.95rem">Apply for Leave</div><button id="close-leave" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--text3)">&times;</button></div>
        <div class="form-group"><label class="form-label">Leave Type</label><select id="leave-type" class="form-input" style="padding:10px"><option value="">Loading...</option></select></div>
        <div class="form-group"><label class="form-label">Start Date</label><input type="date" id="leave-start" class="form-input" style="padding:10px"></div>
        <div class="form-group"><label class="form-label">End Date</label><input type="date" id="leave-end" class="form-input" style="padding:10px"></div>
        <div class="form-group"><label class="form-label">Reason</label><textarea id="leave-reason" class="form-input" rows="3" style="padding:10px;resize:vertical;font-family:inherit" placeholder="Reason for leave..."></textarea></div>
        <div class="form-group"><label class="form-label">Contact During Leave</label><input type="tel" id="leave-contact" class="form-input" style="padding:10px" placeholder="Phone number"></div>
        <div id="leave-err" class="form-error" style="display:none;margin-bottom:8px"></div>
        <button id="leave-submit" class="btn btn-primary" style="padding:11px">Submit Application</button>
    </div>`;
    document.body.appendChild(modal);
    document.getElementById('close-leave').onclick = () => modal.remove();
    modal.addEventListener('click', (e) => { if (e.target === modal) modal.remove(); });

    // Load leave types
    api.getLeaveBalances().then(balances => {
        const sel = document.getElementById('leave-type');
        sel.innerHTML = '<option value="">Select type...</option>' + balances.map(b => `<option value="${b.id}">${b.type} (${b.remaining} days left)</option>`).join('');
    }).catch(() => {});

    document.getElementById('leave-submit').addEventListener('click', async () => {
        const typeId = document.getElementById('leave-type').value;
        const startDate = document.getElementById('leave-start').value;
        const endDate = document.getElementById('leave-end').value;
        const reason = document.getElementById('leave-reason').value;
        const contact = document.getElementById('leave-contact').value;
        const errEl = document.getElementById('leave-err');
        const btn = document.getElementById('leave-submit');

        if (!typeId || !startDate || !endDate || !reason) { errEl.textContent = 'All fields are required.'; errEl.style.display = ''; return; }
        errEl.style.display = 'none';
        btn.disabled = true; btn.innerHTML = '<div class="btn-spinner"></div> Submitting...';
        try {
            const result = await api.applyLeave({ leave_type_id: parseInt(typeId), start_date: startDate, end_date: endDate, reason, contact_during_leave: contact });
            modal.remove();
            renderLeave(pageEl, api);
        } catch (err) { errEl.textContent = err.message; errEl.style.display = ''; btn.disabled = false; btn.innerHTML = 'Submit Application'; }
    });
}

// ─── REPORT CARD COMMENTS ───
async function renderReportComments(el, api) {
    try {
        const data = await api.getReportCardStudents();
        let html = '<div class="dash-scroll">';
        html += `<div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border-radius:var(--radius);padding:16px;color:#fff;margin-bottom:12px;box-shadow:var(--shadow-md)">
            <div style="font-size:1rem;font-weight:700">Report Card Comments</div>
            <div style="font-size:0.72rem;opacity:0.65">${data.term || ''} · ${data.year || ''}</div>
        </div>`;

        const students = data.students || [];
        const commented = students.filter(s => s.comment).length;
        html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--green)"></span>Progress</div><span class="badge ${commented === students.length ? 'badge-green' : 'badge-amber'}">${commented}/${students.length}</span></div>
            <div style="padding:10px 14px"><div class="progress"><div class="progress-bar ${commented === students.length ? 'bg-green' : 'bg-amber'}" style="width:${students.length > 0 ? Math.round((commented/students.length)*100) : 0}%"></div></div></div>
        </div>`;

        html += '<div class="card"><div class="card-body" style="padding:0">';
        for (const s of students) {
            html += `<div class="list-item" style="flex-direction:column;align-items:stretch;gap:6px;padding:12px 14px">
                <div class="flex-between"><div class="list-title">${s.name}</div>${s.comment ? '<span class="badge badge-green">Done</span>' : '<span class="badge badge-amber">Pending</span>'}</div>
                <textarea class="form-input rc-comment" data-student="${s.id}" rows="2" placeholder="Enter class teacher comment..." style="padding:8px;font-size:0.78rem;resize:vertical;font-family:inherit">${s.comment || ''}</textarea>
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <div class="text-xs text-gray">${s.commented_at ? 'Saved ' + s.commented_at : ''}</div>
                    <button class="btn btn-primary btn-save-rc" data-student="${s.id}" style="width:auto;padding:6px 14px;font-size:0.7rem">${SVG.check} Save</button>
                </div>
            </div>`;
        }
        html += '</div></div></div>';
        el.innerHTML = html;

        // Save buttons
        el.querySelectorAll('.btn-save-rc').forEach(btn => {
            btn.addEventListener('click', async () => {
                const studentId = btn.dataset.student;
                const comment = el.querySelector(`.rc-comment[data-student="${studentId}"]`).value.trim();
                if (!comment) return;
                btn.disabled = true; btn.textContent = 'Saving...';
                try {
                    await api.saveReportCardComment({ student_id: parseInt(studentId), comment });
                    btn.innerHTML = `${SVG.check} Saved!`; btn.style.background = 'var(--green)';
                    setTimeout(() => { btn.innerHTML = `${SVG.check} Save`; btn.style.background = ''; btn.disabled = false; }, 2000);
                } catch (err) { btn.textContent = err.message; btn.style.background = 'var(--red)'; setTimeout(() => { btn.innerHTML = `${SVG.check} Save`; btn.style.background = ''; btn.disabled = false; }, 2000); }
            });
        });
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── STAFF DIRECTORY ───
async function renderStaffDirectory(el, api) {
    try {
        const staff = await api.getStaffDirectory();
        let html = '<div class="dash-scroll">';
        html += `<div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border-radius:var(--radius);padding:16px;color:#fff;margin-bottom:12px;box-shadow:var(--shadow-md)">
            <div style="font-size:1rem;font-weight:700">Staff Directory</div>
            <div style="font-size:0.72rem;opacity:0.65">${staff.length} teachers</div>
        </div>`;
        html += '<div class="card"><div class="card-body" style="padding:0">';
        for (const t of staff) {
            html += `<div class="list-item" style="gap:10px">
                <div class="att-avatar">${t.profile_photo ? `<img src="${t.profile_photo}" style="width:32px;height:32px;border-radius:8px;object-fit:cover">` : t.name[0]}</div>
                <div style="flex:1;min-width:0">
                    <div class="list-title">${t.name}${t.is_class_teacher ? ' <span class="badge badge-blue" style="margin-left:4px">CT</span>' : ''}</div>
                    <div class="list-sub">${t.specialization || ''} · ${t.grade || ''} ${t.class_section || ''}</div>
                </div>
                <div style="display:flex;gap:4px;flex-shrink:0">
                    ${t.phone ? `<a href="tel:${t.phone}" class="badge badge-green" style="text-decoration:none;padding:4px 8px">Call</a>` : ''}
                </div>
            </div>`;
        }
        if (staff.length === 0) html += '<div class="card-empty">No staff found.</div>';
        html += '</div></div></div>';
        el.innerHTML = html;
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── SCHOOL CALENDAR ───
async function renderCalendar(el, api) {
    try {
        const data = await api.getSchoolCalendar();
        let html = '<div class="dash-scroll">';
        html += `<div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border-radius:var(--radius);padding:16px;color:#fff;margin-bottom:12px;box-shadow:var(--shadow-md)">
            <div style="font-size:1rem;font-weight:700">School Calendar</div>
            <div style="font-size:0.72rem;opacity:0.65">${data.year || new Date().getFullYear()}</div>
        </div>`;

        // Terms
        for (const t of (data.terms || [])) {
            html += `<div class="card" style="border-left:4px solid ${t.is_active ? 'var(--green)' : 'var(--border)'}">
                <div style="padding:14px">
                    <div class="flex-between"><div class="list-title">${t.name}</div>${t.is_active ? '<span class="badge badge-green">Active</span>' : ''}</div>
                    <div class="list-sub mt-2">${t.start_date || '?'} — ${t.end_date || '?'}</div>
                </div>
            </div>`;
        }

        // Events
        if (data.events && data.events.length > 0) {
            html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--purple)"></span>Upcoming Events</div></div><div class="card-body" style="padding:0">`;
            for (const e of data.events) {
                html += `<div class="list-item"><div style="min-width:40px;text-align:center;flex-shrink:0"><div class="mono bold text-blue" style="font-size:0.82rem">${e.date?.split(' ')[0] || ''}</div><div class="text-xs text-gray">${e.date?.split(' ').slice(1).join(' ') || ''}</div></div><div><div class="list-title">${e.title}</div>${e.description ? `<div class="list-sub">${e.description}</div>` : ''}</div></div>`;
            }
            html += '</div></div>';
        }

        // Daily quote
        const quotes = ['Teaching is the one profession that creates all other professions.','The art of teaching is the art of assisting discovery.','Education is not the filling of a pail, but the lighting of a fire.','A good teacher can inspire hope, ignite the imagination, and instill a love of learning.','Teachers affect eternity; no one can tell where their influence stops.'];
        const todayQuote = quotes[new Date().getDay() % quotes.length];
        html += `<div class="card" style="background:linear-gradient(135deg,rgba(124,58,237,0.06),rgba(59,130,246,0.06))"><div style="padding:16px;text-align:center">
            <div class="text-xs text-gray bold" style="text-transform:uppercase;letter-spacing:0.06em;margin-bottom:8px">Daily Inspiration</div>
            <div class="text-sm" style="color:var(--text);font-style:italic;line-height:1.6">"${todayQuote}"</div>
        </div></div>`;

        html += '</div>';
        el.innerHTML = html;
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── SEND NOTICE ───
async function renderSendNotice(el, api) {
    let html = '<div class="dash-scroll">';
    html += `<div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border-radius:var(--radius);padding:16px;color:#fff;margin-bottom:12px;box-shadow:var(--shadow-md)">
        <div style="font-size:1rem;font-weight:700">Send Notice to Parents</div>
        <div style="font-size:0.72rem;opacity:0.65">Notify parents in your class</div>
    </div>`;
    html += `<div class="card"><div style="padding:14px">
        <div class="form-group"><label class="form-label">Title</label><input type="text" id="notice-title" class="form-input" placeholder="Notice title" style="padding:10px 12px"></div>
        <div class="form-group"><label class="form-label">Message</label><textarea id="notice-body" class="form-input" rows="5" placeholder="Write your message to parents..." style="padding:10px 12px;resize:vertical;font-family:inherit"></textarea></div>
        <div class="form-group"><label class="form-label">Priority</label><select id="notice-priority" class="form-input" style="padding:10px 12px"><option value="normal">Normal</option><option value="important">Important</option><option value="urgent">Urgent</option></select></div>
        <div id="notice-err" class="form-error" style="display:none;margin-bottom:8px"></div>
        <div id="notice-ok" class="form-msg success" style="display:none;margin-bottom:8px">Notice sent successfully!</div>
        <button id="notice-send" class="btn btn-primary" style="padding:12px">${SVG.megaphone} Send Notice</button>
    </div></div>`;
    html += '</div>';
    el.innerHTML = html;

    document.getElementById('notice-send').addEventListener('click', async () => {
        const title = document.getElementById('notice-title').value.trim();
        const body = document.getElementById('notice-body').value.trim();
        const priority = document.getElementById('notice-priority').value;
        const errEl = document.getElementById('notice-err');
        const okEl = document.getElementById('notice-ok');
        const btn = document.getElementById('notice-send');

        if (!title || !body) { errEl.textContent = 'Title and message are required.'; errEl.style.display = ''; return; }
        errEl.style.display = 'none'; okEl.style.display = 'none';
        btn.disabled = true; btn.innerHTML = '<div class="btn-spinner"></div> Sending...';
        try {
            await api.sendClassNotice({ title, body, priority });
            okEl.style.display = '';
            document.getElementById('notice-title').value = '';
            document.getElementById('notice-body').value = '';
        } catch (err) { errEl.textContent = err.message; errEl.style.display = ''; }
        btn.disabled = false; btn.innerHTML = `${SVG.megaphone} Send Notice`;
    });
}

// ─── MESSAGES ───
async function renderMessages(el, api) {
    const params = new URLSearchParams(window.location.hash.split('?')[1] || '');
    const chatWith = params.get('chat');

    if (chatWith) {
        await renderChat(el, api, chatWith);
        return;
    }

    try {
        const [data, staff] = await Promise.all([api.getConversations(), api.getStaffDirectory()]);
        const conversations = data.conversations || [];

        let html = '<div class="dash-scroll">';
        html += `<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <div style="font-size:1rem;font-weight:700">Messages ${data.total_unread > 0 ? `<span class="badge badge-red" style="margin-left:6px">${data.total_unread}</span>` : ''}</div>
            <button id="btn-new-chat" class="btn btn-primary" style="width:auto;padding:8px 14px;font-size:0.72rem">${SVG.plus} New</button>
        </div>`;

        if (conversations.length === 0) {
            html += '<div class="card"><div class="card-empty">No conversations yet. Tap "New" to start messaging a colleague.</div></div>';
        } else {
            html += '<div class="card"><div class="card-body" style="padding:0">';
            for (const c of conversations) {
                html += `<a href="#/dashboard/messages?chat=${c.user_id}" class="list-item" style="text-decoration:none;color:var(--text);gap:10px;cursor:pointer${c.unread > 0 ? ';background:rgba(30,64,175,0.04)' : ''}">
                    <div class="att-avatar" style="${c.unread > 0 ? 'background:var(--primary);color:#fff' : ''}">${c.profile_photo ? `<img src="${c.profile_photo}" style="width:32px;height:32px;border-radius:8px;object-fit:cover">` : c.name[0]}</div>
                    <div style="flex:1;min-width:0">
                        <div class="flex-between"><div class="list-title">${c.name}</div><div class="text-xs text-gray">${c.last_time || ''}</div></div>
                        <div class="list-sub" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${c.is_mine ? 'You: ' : ''}${c.last_message || ''}</div>
                    </div>
                    ${c.unread > 0 ? `<span class="badge badge-red" style="padding:3px 7px">${c.unread}</span>` : ''}
                </a>`;
            }
            html += '</div></div>';
        }

        html += '</div>';
        el.innerHTML = html;

        // New chat modal
        document.getElementById('btn-new-chat')?.addEventListener('click', () => {
            if (document.getElementById('newchat-modal')) return;
            const modal = document.createElement('div');
            modal.id = 'newchat-modal'; modal.className = 'modal-overlay';
            let staffHtml = '';
            for (const t of staff) {
                staffHtml += `<a href="#/dashboard/messages?chat=${t.user_id || ''}" class="list-item" style="text-decoration:none;color:var(--text);cursor:pointer" data-uid="${t.user_id || ''}">
                    <div class="att-avatar">${t.name[0]}</div>
                    <div><div class="list-title">${t.name}</div><div class="list-sub">${t.specialization || ''} · ${t.grade || ''}</div></div>
                </a>`;
            }
            modal.innerHTML = `<div class="modal"><div class="flex-between" style="margin-bottom:12px"><div class="list-title" style="font-size:0.95rem">Start Conversation</div><button id="close-newchat" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--text3)">&times;</button></div>
                <input type="text" id="search-staff" class="form-input" placeholder="Search teacher..." style="padding:10px 12px;margin-bottom:8px">
                <div id="staff-list" style="max-height:50vh;overflow-y:auto">${staffHtml}</div>
            </div>`;
            document.body.appendChild(modal);
            document.getElementById('close-newchat').onclick = () => modal.remove();
            modal.addEventListener('click', (e) => { if (e.target === modal) modal.remove(); });

            // Click on staff to start chat
            modal.querySelectorAll('.list-item[data-uid]').forEach(item => {
                item.addEventListener('click', () => modal.remove());
            });

            // Search filter
            document.getElementById('search-staff').addEventListener('input', (e) => {
                const q = e.target.value.toLowerCase();
                modal.querySelectorAll('.list-item[data-uid]').forEach(item => {
                    item.style.display = item.textContent.toLowerCase().includes(q) ? '' : 'none';
                });
            });
        });

        // Unread badge on tab
        if (data.total_unread > 0) {
            const tab = document.getElementById('tab-messages');
            if (tab && !tab.querySelector('.tab-badge')) {
                const badge = document.createElement('span');
                badge.className = 'tab-badge';
                badge.textContent = data.total_unread > 9 ? '9+' : data.total_unread;
                tab.appendChild(badge);
            }
        }

    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

async function renderChat(el, api, partnerId) {
    try {
        const data = await api.getChatMessages(partnerId);
        const partner = data.partner || {};
        const messages = data.messages || [];

        el.innerHTML = `
            <div style="display:flex;flex-direction:column;height:100%">
                <div style="padding:10px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;flex-shrink:0;background:var(--card)">
                    <a href="#/dashboard/messages" style="color:var(--text3);text-decoration:none;font-size:1.2rem;padding:4px">&larr;</a>
                    <div class="att-avatar">${partner.profile_photo ? `<img src="${partner.profile_photo}" style="width:32px;height:32px;border-radius:8px;object-fit:cover">` : (partner.name || '?')[0]}</div>
                    <div><div class="list-title">${partner.name || 'Unknown'}</div><div class="list-sub">${partner.grade || 'Teacher'}</div></div>
                </div>
                <div id="chat-messages" style="flex:1;overflow-y:auto;padding:12px;display:flex;flex-direction:column;gap:6px">
                </div>
                <div style="padding:8px 12px;border-top:1px solid var(--border);display:flex;gap:8px;align-items:flex-end;background:var(--card);flex-shrink:0">
                    <button id="chat-attach" style="background:none;border:none;color:var(--text2);cursor:pointer;padding:6px;flex-shrink:0;width:36px;height:36px;display:flex;align-items:center;justify-content:center">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"/></svg>
                    </button>
                    <input type="file" id="chat-file" style="display:none" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip">
                    <div style="flex:1">
                        <div id="chat-file-preview" style="display:none;padding:6px 10px;background:rgba(59,130,246,0.08);border-radius:6px;margin-bottom:4px;font-size:0.72rem;color:var(--primary)"></div>
                        <textarea id="chat-input" rows="1" placeholder="Type a message..." style="width:100%;padding:8px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:0.82rem;font-family:inherit;resize:none;outline:none;max-height:80px;background:var(--bg)"></textarea>
                    </div>
                    <button id="chat-send" style="background:var(--primary);color:#fff;border:none;border-radius:8px;width:36px;height:36px;cursor:pointer;flex-shrink:0;display:flex;align-items:center;justify-content:center">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                    </button>
                </div>
            </div>`;

        const msgContainer = document.getElementById('chat-messages');
        let lastDate = '';

        for (const m of messages) {
            if (m.date !== lastDate) {
                msgContainer.innerHTML += `<div style="text-align:center;padding:8px 0"><span class="text-xs text-gray" style="background:var(--bg);padding:2px 10px;border-radius:10px">${m.date}</span></div>`;
                lastDate = m.date;
            }
            const align = m.is_mine ? 'flex-end' : 'flex-start';
            const bg = m.is_mine ? 'var(--primary)' : 'var(--card)';
            const color = m.is_mine ? '#fff' : 'var(--text)';
            const border = m.is_mine ? '' : 'border:1px solid var(--border);';

            let content = '';
            if (m.file_path) {
                const isImage = m.file_type?.startsWith('image/');
                content += isImage
                    ? `<a href="${m.file_path}" target="_blank"><img src="${m.file_path}" style="max-width:200px;border-radius:6px;margin-bottom:4px"></a>`
                    : `<a href="${m.file_path}" target="_blank" style="display:flex;align-items:center;gap:6px;padding:6px 10px;background:rgba(255,255,255,0.15);border-radius:6px;margin-bottom:4px;text-decoration:none;color:inherit;font-size:0.72rem;font-weight:600">${SVG.paperclip} ${m.file_name || 'File'}<span style="opacity:0.6;font-size:0.62rem">${m.file_size ? Math.round(m.file_size/1024) + 'KB' : ''}</span></a>`;
            }
            if (m.message) content += `<div style="font-size:0.82rem;line-height:1.5;word-break:break-word">${m.message}</div>`;

            msgContainer.innerHTML += `<div style="display:flex;justify-content:${align}">
                <div style="max-width:80%;padding:8px 12px;border-radius:12px;background:${bg};color:${color};${border}box-shadow:0 1px 2px rgba(0,0,0,0.05)">
                    ${content}
                    <div style="font-size:0.58rem;opacity:0.6;text-align:right;margin-top:2px">${m.time}${m.is_mine && m.read_at ? ' ✓✓' : ''}</div>
                </div>
            </div>`;
        }

        // Scroll to bottom
        msgContainer.scrollTop = msgContainer.scrollHeight;

        // File attach
        let selectedFile = null;
        const filePreview = document.getElementById('chat-file-preview');
        document.getElementById('chat-attach').addEventListener('click', () => document.getElementById('chat-file').click());
        document.getElementById('chat-file').addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                selectedFile = file;
                filePreview.style.display = 'block';
                filePreview.innerHTML = `<div style="display:flex;align-items:center;gap:6px">
                    <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">📎 ${file.name}</span>
                    <span style="opacity:0.6;font-size:0.62rem;flex-shrink:0">${(file.size/1024).toFixed(0)} KB</span>
                    <button id="remove-file" style="background:none;border:none;color:var(--red);cursor:pointer;font-size:1rem;flex-shrink:0;padding:0 2px">&times;</button>
                </div>`;
                document.getElementById('remove-file').onclick = () => { selectedFile = null; filePreview.style.display = 'none'; document.getElementById('chat-file').value = ''; };
            }
        });

        // Send
        const sendMsg = async () => {
            const input = document.getElementById('chat-input');
            const message = input.value.trim();
            if (!message && !selectedFile) return;

            const sendBtn = document.getElementById('chat-send');
            sendBtn.disabled = true;

            try {
                await api.sendMessage(partnerId, message || null, selectedFile);
                input.value = '';
                selectedFile = null;
                filePreview.style.display = 'none';
                document.getElementById('chat-file').value = '';
                // Reload chat
                renderChat(el, api, partnerId);
            } catch (err) {
                alert(err.message);
            }
            sendBtn.disabled = false;
        };

        document.getElementById('chat-send').addEventListener('click', sendMsg);
        document.getElementById('chat-input').addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMsg(); }
        });

        // Auto-resize textarea
        document.getElementById('chat-input').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 80) + 'px';
        });

        // Poll for new messages every 5 seconds
        const pollInterval = setInterval(async () => {
            if (!document.getElementById('chat-messages')) { clearInterval(pollInterval); return; }
            try {
                const fresh = await api.getChatMessages(partnerId);
                if ((fresh.messages || []).length !== messages.length) {
                    renderChat(el, api, partnerId);
                    clearInterval(pollInterval);
                }
            } catch {}
        }, 5000);

    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── STUDENT PERFORMANCE ───
async function renderStudentPerf(el, api) {
    const params = new URLSearchParams(window.location.hash.split('?')[1] || '');
    const studentId = params.get('id');
    if (!studentId) { el.innerHTML = '<div class="dash-scroll card-empty">No student selected.</div>'; return; }
    try {
        const data = await api.getStudentPerformance(studentId);
        let html = '<div class="dash-scroll">';
        html += `<div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border-radius:var(--radius);padding:16px;color:#fff;margin-bottom:12px;box-shadow:var(--shadow-md)">
            <div style="font-size:1rem;font-weight:700">${data.student?.name || 'Student'}</div>
            <div style="font-size:0.72rem;opacity:0.65">${data.student?.grade || ''} ${data.student?.class || ''} · Overall: ${data.overall_average ?? '-'}%</div>
        </div>`;

        if (data.by_subject && data.by_subject.length) {
            for (const s of data.by_subject) {
                const trendIcon = s.trend === 'improving' ? '↑' : s.trend === 'declining' ? '↓' : '→';
                const trendColor = s.trend === 'improving' ? 'var(--green)' : s.trend === 'declining' ? 'var(--red)' : 'var(--text3)';
                html += `<div class="card"><div style="padding:12px 14px">
                    <div class="flex-between" style="margin-bottom:8px"><div class="list-title">${s.subject}</div><span style="font-size:1rem;color:${trendColor}">${trendIcon}</span></div>
                    <div style="display:flex;gap:12px;margin-bottom:8px">
                        <div class="text-xs text-gray">Avg: <strong class="mono ${s.average >= 50 ? 'text-green' : 'text-red'}">${s.average}%</strong></div>
                        <div class="text-xs text-gray">High: <strong class="mono">${s.highest}%</strong></div>
                        <div class="text-xs text-gray">Low: <strong class="mono">${s.lowest}%</strong></div>
                        <div class="text-xs text-gray">Trend: <strong style="color:${trendColor}">${s.trend}</strong></div>
                    </div>
                    <div style="display:flex;gap:4px;flex-wrap:wrap">
                        ${s.results.map(r => `<div style="padding:4px 8px;border-radius:4px;font-size:0.65rem;font-weight:600;background:${r.marks >= 50 ? 'rgba(5,150,105,0.1)' : 'rgba(220,38,38,0.1)'};color:${r.marks >= 50 ? 'var(--green)' : 'var(--red)'}">${r.exam_type}: ${r.marks}% (${r.grade})</div>`).join('')}
                    </div>
                </div></div>`;
            }
        } else { html += '<div class="card"><div class="card-empty">No results found for this student.</div></div>'; }
        html += `<a href="#/dashboard/students" class="btn btn-outline" style="text-decoration:none">Back to Students</a></div>`;
        el.innerHTML = html;
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── ATTENDANCE ANALYTICS ───
async function renderAttAnalytics(el, api) {
    try {
        const classes = await api.getMyClasses();
        const params = new URLSearchParams(window.location.hash.split('?')[1] || '');
        const selectedClass = params.get('class') || (classes.length > 0 ? classes[0].class_section_id : null);

        let html = '<div class="dash-scroll">';
        html += `<div style="font-size:1rem;font-weight:700;margin-bottom:12px">Attendance Analytics</div>`;
        html += `<select id="ana-class" class="form-input" style="padding:8px 10px;font-size:0.75rem;margin-bottom:12px">
            ${classes.map(c => `<option value="${c.class_section_id}" ${String(c.class_section_id) === String(selectedClass) ? 'selected' : ''}>${c.grade} ${c.class_section} - ${c.subject}</option>`).join('')}
        </select>`;
        html += '<div id="ana-content"><div class="card"><div class="card-empty">Loading analytics...</div></div></div></div>';
        el.innerHTML = html;

        async function loadAnalytics() {
            const classId = document.getElementById('ana-class').value;
            const contentEl = document.getElementById('ana-content');
            try {
                const data = await api.getAttendanceAnalytics(classId);
                let h = '';

                // Overall rate
                h += `<div class="card" style="background:linear-gradient(135deg,var(--navy),var(--navy-light));color:#fff"><div style="padding:16px;text-align:center">
                    <div class="mono" style="font-size:2rem;font-weight:700">${data.overall_rate}%</div>
                    <div style="font-size:0.72rem;opacity:0.65">Overall Attendance Rate · ${data.student_count} students</div>
                </div></div>`;

                // Status breakdown
                if (data.by_status) {
                    h += `<div class="kpi-strip" style="grid-template-columns:repeat(4,1fr)">`;
                    const statuses = [['present','P','kpi-green'],['absent','A','kpi-red'],['late','L','kpi-amber'],['sick','S','kpi-blue']];
                    for (const [s,l,c] of statuses) h += `<div class="kpi"><div class="kpi-val ${c}">${data.by_status[s] || 0}</div><div class="kpi-lbl">${l}</div></div>`;
                    h += '</div>';
                }

                // Weekly trend
                if (data.weekly_trend && data.weekly_trend.length) {
                    h += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--primary)"></span>Weekly Trend</div></div><div class="card-body" style="padding:12px 14px">`;
                    h += '<div style="display:flex;align-items:flex-end;gap:4px;height:80px">';
                    for (const w of data.weekly_trend) {
                        const barH = Math.max(4, (w.rate / 100) * 70);
                        const color = w.rate >= 80 ? 'var(--green)' : w.rate >= 60 ? 'var(--amber)' : 'var(--red)';
                        h += `<div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px">
                            <div class="text-xs mono bold" style="color:${color};font-size:0.55rem">${w.rate}%</div>
                            <div style="width:100%;height:${barH}px;background:${color};border-radius:3px 3px 0 0"></div>
                            <div class="text-xs text-gray" style="font-size:0.5rem">${w.week}</div>
                        </div>`;
                    }
                    h += '</div></div></div>';
                }

                // At-risk students
                if (data.at_risk_students && data.at_risk_students.length) {
                    h += `<div class="card" style="border-left:4px solid var(--red)"><div class="card-head"><div class="card-title" style="color:var(--red)"><span class="section-dot" style="background:var(--red)"></span>At-Risk Students (&gt;20% absent)</div></div><div class="card-body" style="padding:0">`;
                    for (const s of data.at_risk_students) {
                        h += `<div class="list-item"><div style="flex:1"><div class="list-title">${s.name}</div><div class="list-sub">${s.absent}/${s.total} absent</div></div><div class="mono bold text-red">${s.rate}%</div></div>`;
                    }
                    h += '</div></div>';
                }

                contentEl.innerHTML = h;
            } catch (err) { contentEl.innerHTML = `<div class="card"><div class="card-empty">${err.message}</div></div>`; }
        }

        loadAnalytics();
        document.getElementById('ana-class').addEventListener('change', loadAnalytics);
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── MY STUDENTS ───
async function renderStudents(el, api) {
    try {
        const classes = await api.getMyStudents();
        let html = '<div class="dash-scroll">';
        html += `<div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border-radius:var(--radius);padding:16px;color:#fff;margin-bottom:12px;box-shadow:var(--shadow-md)">
            <div style="font-size:1rem;font-weight:700">My Students</div>
            <div style="font-size:0.72rem;opacity:0.65">${classes.reduce((s,c) => s + c.student_count, 0)} students across ${classes.length} classes</div>
        </div>`;

        for (const c of classes) {
            html += `<div class="card">
                <button class="accordion-trigger accordion-open accordion-card-head" data-target="stu-${c.class_section_id}">
                    <div class="accordion-trigger-left">
                        <span class="section-dot" style="background:var(--primary)"></span>
                        <div><div class="card-title" style="margin:0">${c.class_name}</div><div class="text-xs text-gray">${c.subjects.join(', ')}</div></div>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px"><span class="badge badge-blue">${c.student_count}</span><span class="accordion-chevron">${SVG.chevDown}</span></div>
                </button>
                <div class="accordion-panel accordion-panel-open" id="stu-${c.class_section_id}">`;

            for (const s of c.students) {
                html += `<div class="att-row">
                    <div class="att-avatar">${s.profile_photo ? `<img src="${s.profile_photo}" style="width:32px;height:32px;border-radius:8px;object-fit:cover">` : initial(s.name)}</div>
                    <div class="att-name"><div class="att-name-text">${s.name}</div><div class="att-name-sub">${s.student_id_number || ''} · ${s.gender || ''}</div></div>
                    <a href="#/dashboard/student-perf?id=${s.id}" class="badge badge-blue" style="text-decoration:none;padding:4px 8px;font-size:0.6rem">Performance</a>
                </div>`;
            }

            html += '</div></div>';
        }

        if (classes.length === 0) html += '<div class="card"><div class="card-empty">No students found.</div></div>';
        html += '</div>';
        el.innerHTML = html;

        el.querySelectorAll('.accordion-trigger').forEach(trigger => {
            trigger.addEventListener('click', () => {
                const panel = document.getElementById(trigger.dataset.target);
                if (panel) { panel.classList.toggle('accordion-panel-open'); trigger.classList.toggle('accordion-open'); }
            });
        });
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── PAYSLIPS ───
async function renderPayslips(el, api) {
    try {
        const payslips = await api.getPayslips();
        let html = '<div class="dash-scroll">';
        html += `<div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border-radius:var(--radius);padding:16px;color:#fff;margin-bottom:12px;box-shadow:var(--shadow-md)">
            <div style="font-size:1rem;font-weight:700">My Payslips</div>
            <div style="font-size:0.72rem;opacity:0.65">${payslips.length} payslip${payslips.length !== 1 ? 's' : ''} available</div>
        </div>`;

        if (payslips.length === 0) {
            html += '<div class="card"><div class="card-empty">No payslips available yet.</div></div>';
        } else {
            for (const p of payslips) {
                const statusBadge = p.status === 'paid' ? '<span class="badge badge-green">Paid</span>' : '<span class="badge badge-amber">Pending</span>';
                html += `<div class="card">
                    <div style="padding:14px">
                        <div class="flex-between" style="margin-bottom:10px">
                            <div><div class="list-title">${p.month} ${p.year}</div><div class="list-sub">${p.payment_date || 'Payment pending'}</div></div>
                            ${statusBadge}
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:10px">
                            <div><div class="text-xs text-gray">Basic</div><div class="mono bold text-sm">K${(p.basic_salary || 0).toLocaleString()}</div></div>
                            <div><div class="text-xs text-gray">Gross</div><div class="mono bold text-sm text-blue">K${(p.gross_salary || 0).toLocaleString()}</div></div>
                            <div><div class="text-xs text-gray">Net Pay</div><div class="mono bold text-sm text-green">K${(p.net_salary || 0).toLocaleString()}</div></div>
                        </div>`;

                // Allowances
                if (p.allowances && typeof p.allowances === 'object' && Object.keys(p.allowances).length > 0) {
                    html += '<div style="margin-bottom:6px"><div class="text-xs text-gray bold" style="margin-bottom:4px">Allowances</div>';
                    for (const [key, val] of Object.entries(p.allowances)) {
                        if (val && val > 0) html += `<div class="flex-between text-xs" style="padding:2px 0"><span style="color:var(--text2)">${key}</span><span class="mono text-green">+K${Number(val).toLocaleString()}</span></div>`;
                    }
                    html += '</div>';
                }

                // Deductions
                if (p.deductions && typeof p.deductions === 'object' && Object.keys(p.deductions).length > 0) {
                    html += '<div style="margin-bottom:8px"><div class="text-xs text-gray bold" style="margin-bottom:4px">Deductions</div>';
                    for (const [key, val] of Object.entries(p.deductions)) {
                        if (val && val > 0) html += `<div class="flex-between text-xs" style="padding:2px 0"><span style="color:var(--text2)">${key}</span><span class="mono text-red">-K${Number(val).toLocaleString()}</span></div>`;
                    }
                    html += '</div>';
                }

                html += `<a href="${api.downloadUrl(p.download_url)}" target="_blank" class="btn btn-outline" style="text-decoration:none;font-size:0.75rem;padding:8px">${SVG.chart} Download Payslip</a>
                    </div>
                </div>`;
            }
        }

        html += '</div>';
        el.innerHTML = html;
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── PROFILE ───
async function renderProfile(el, api, user) {
    try {
        const profile = await api.getProfile();
        let html = '<div class="dash-scroll">';

        // Header card
        const avatarInner = profile.profile_photo
            ? `<img id="avatar-img" src="${profile.profile_photo}" style="width:64px;height:64px;border-radius:16px;object-fit:cover;border:3px solid rgba(255,255,255,0.3);display:block">`
            : `<div id="avatar-img" style="width:64px;height:64px;border-radius:16px;background:rgba(255,255,255,0.12);border:2px solid rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700">${initial(profile.name)}</div>`;
        html += `<div class="card">
            <div style="background:linear-gradient(135deg,var(--navy),var(--primary-dark));padding:24px;text-align:center;color:#fff">
                <div id="avatar-tap" style="position:relative;width:64px;height:64px;margin:0 auto 10px;cursor:pointer" title="Change photo">
                    ${avatarInner}
                    <div style="position:absolute;right:-4px;bottom:-4px;width:24px;height:24px;border-radius:50%;background:var(--primary);border:2px solid #fff;display:flex;align-items:center;justify-content:center">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    </div>
                    <input type="file" id="avatar-input" accept="image/*" style="display:none">
                </div>
                <div id="avatar-msg" style="font-size:0.66rem;opacity:0.85;height:14px;margin-bottom:4px"></div>
                <div style="font-size:1.1rem;font-weight:700">${profile.name || ''}</div>
                <div style="font-size:0.75rem;opacity:0.6;margin-top:4px">${profile.position || 'Teacher'} · ${profile.department || ''}</div>
                ${profile.employee_number ? `<div style="font-size:0.68rem;opacity:0.5;margin-top:2px">${profile.employee_number}</div>` : ''}
            </div>
        </div>`;

        // Teaching Info
        html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--primary)"></span>Teaching</div></div><div style="padding:0">`;
        const teachFields = [
            ['Role', profile.is_class_teacher ? 'Class Teacher' : 'Subject Teacher'],
            ['Grade', profile.grade || '-'],
            ['Class', profile.class_section || '-'],
            ['Qualification', profile.qualification || '-'],
            ['Specialization', profile.specialization || '-'],
            ['Joined', profile.join_date || '-'],
            ['Service', profile.years_of_service ? profile.years_of_service + ' years' : '-'],
        ];
        for (const [label, value] of teachFields) {
            html += `<div class="list-item" style="padding:8px 14px;border-bottom:1px solid var(--border)"><span class="text-xs text-gray" style="width:90px">${label}</span><span class="text-sm bold">${value}</span></div>`;
        }
        html += '</div></div>';

        // Personal Info
        html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--green)"></span>Personal</div></div><div style="padding:0">`;
        const personalFields = [
            ['Email', profile.email || '-'],
            ['Phone', profile.phone || '-'],
            ['Gender', profile.gender ? profile.gender.charAt(0).toUpperCase() + profile.gender.slice(1) : '-'],
            ['DOB', profile.date_of_birth || '-'],
            ['Address', [profile.address, profile.city, profile.province].filter(Boolean).join(', ') || '-'],
            ['NRC', profile.nrc_number || '-'],
        ];
        for (const [label, value] of personalFields) {
            html += `<div class="list-item" style="padding:8px 14px;border-bottom:1px solid var(--border)"><span class="text-xs text-gray" style="width:90px">${label}</span><span class="text-sm">${value}</span></div>`;
        }
        html += '</div></div>';

        // Employment & Bank
        html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--amber)"></span>Employment</div></div><div style="padding:0">`;
        const empFields = [
            ['Emp. Number', profile.employee_number || '-'],
            ['Emp. Type', profile.employment_type ? profile.employment_type.charAt(0).toUpperCase() + profile.employment_type.slice(1) : '-'],
            ['NAPSA', profile.napsa_number || '-'],
            ['TPIN', profile.tpin_number || '-'],
            ['Bank', profile.bank_name || '-'],
            ['Branch', profile.bank_branch || '-'],
            ['Account', profile.bank_account_number || '-'],
        ];
        for (const [label, value] of empFields) {
            html += `<div class="list-item" style="padding:8px 14px;border-bottom:1px solid var(--border)"><span class="text-xs text-gray" style="width:90px">${label}</span><span class="text-sm">${value}</span></div>`;
        }
        html += '</div></div>';

        // Emergency contacts
        if (profile.emergency_contact_name || profile.next_of_kin_name) {
            html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--red)"></span>Emergency</div></div><div style="padding:0">`;
            if (profile.emergency_contact_name) {
                html += `<div class="list-item" style="padding:10px 14px;border-bottom:1px solid var(--border)"><div><div class="list-title">${profile.emergency_contact_name}</div><div class="list-sub">${profile.emergency_contact_relationship || ''} · <a href="tel:${profile.emergency_contact_phone}" class="text-blue" style="text-decoration:none">${profile.emergency_contact_phone || ''}</a></div></div></div>`;
            }
            if (profile.next_of_kin_name) {
                html += `<div class="list-item" style="padding:10px 14px"><div><div class="list-title">${profile.next_of_kin_name}</div><div class="list-sub">Next of Kin · <a href="tel:${profile.next_of_kin_phone}" class="text-blue" style="text-decoration:none">${profile.next_of_kin_phone || ''}</a></div></div></div>`;
            }
            html += '</div></div>';
        }

        // Security - Change Password
        html += `<div class="card"><div class="card-head"><div class="card-title"><span class="section-dot" style="background:var(--red)"></span>Security</div></div>
            <div id="pw-section" style="padding:14px;display:none">
                <div class="form-group"><label class="form-label">Current Password</label><input type="password" id="pw-current" class="form-input" style="padding:10px 12px"></div>
                <div class="form-group"><label class="form-label">New Password</label><input type="password" id="pw-new" class="form-input" style="padding:10px 12px"></div>
                <div class="form-group"><label class="form-label">Confirm Password</label><input type="password" id="pw-confirm" class="form-input" style="padding:10px 12px"></div>
                <div id="pw-err" class="form-error" style="display:none;margin-bottom:8px"></div>
                <div id="pw-ok" class="form-msg success" style="display:none;margin-bottom:8px">Password changed!</div>
                <button id="pw-submit" class="btn btn-primary" style="padding:10px">Update Password</button>
            </div>
            <button id="pw-toggle" class="list-item" style="padding:12px 14px;border:none;background:none;width:100%;cursor:pointer;font-family:inherit">
                <div class="text-sm bold">Change Password</div><span class="accordion-chevron">${SVG.chevDown}</span>
            </button>
        </div>`;

        // Quick links
        html += `<a href="#/dashboard/payslips" class="btn btn-outline" style="text-decoration:none;margin-bottom:8px">${SVG.chart} View Payslips</a>`;
        html += `<button class="btn btn-outline" id="profile-logout" style="color:var(--red);border-color:var(--red)">${SVG.logout} Sign Out</button>`;
        html += '</div>';
        el.innerHTML = html;

        // Profile photo upload
        const avatarTap = document.getElementById('avatar-tap');
        const avatarInput = document.getElementById('avatar-input');
        avatarTap?.addEventListener('click', () => avatarInput?.click());
        avatarInput?.addEventListener('change', async () => {
            const file = avatarInput.files && avatarInput.files[0];
            const msg = document.getElementById('avatar-msg');
            if (!file) return;
            if (!file.type.startsWith('image/')) { msg.textContent = 'Please choose an image file.'; avatarInput.value = ''; return; }
            if (file.size > 5 * 1024 * 1024) { msg.textContent = 'Image must be under 5MB.'; avatarInput.value = ''; return; }
            msg.textContent = 'Uploading…';
            try {
                const res = await api.updateProfilePhoto(file);
                const newImg = document.createElement('img');
                newImg.id = 'avatar-img';
                newImg.src = (res.photo_url || '') + '?t=' + Date.now();
                newImg.style.cssText = 'width:64px;height:64px;border-radius:16px;object-fit:cover;border:3px solid rgba(255,255,255,0.3);display:block';
                document.getElementById('avatar-img')?.replaceWith(newImg);
                msg.textContent = 'Photo updated';
                setTimeout(() => { if (msg.textContent === 'Photo updated') msg.textContent = ''; }, 2500);
            } catch (err) {
                msg.textContent = err.message || 'Upload failed.';
            }
            avatarInput.value = '';
        });

        // Password toggle
        document.getElementById('pw-toggle')?.addEventListener('click', () => {
            const sec = document.getElementById('pw-section');
            sec.style.display = sec.style.display === 'none' ? '' : 'none';
        });

        // Password submit
        document.getElementById('pw-submit')?.addEventListener('click', async () => {
            const current = document.getElementById('pw-current').value;
            const newPw = document.getElementById('pw-new').value;
            const confirm = document.getElementById('pw-confirm').value;
            const errEl = document.getElementById('pw-err');
            const okEl = document.getElementById('pw-ok');
            errEl.style.display = 'none'; okEl.style.display = 'none';
            if (!current || !newPw || !confirm) { errEl.textContent = 'All fields required.'; errEl.style.display = ''; return; }
            if (newPw.length < 8) { errEl.textContent = 'Min 8 characters.'; errEl.style.display = ''; return; }
            if (newPw !== confirm) { errEl.textContent = 'Passwords don\'t match.'; errEl.style.display = ''; return; }
            const btn = document.getElementById('pw-submit');
            btn.disabled = true; btn.textContent = 'Updating...';
            try {
                await api.changePassword({ current_password: current, new_password: newPw, new_password_confirmation: confirm });
                okEl.style.display = ''; document.getElementById('pw-current').value = ''; document.getElementById('pw-new').value = ''; document.getElementById('pw-confirm').value = '';
            } catch (err) { errEl.textContent = err.message; errEl.style.display = ''; }
            btn.disabled = false; btn.textContent = 'Update Password';
        });

        document.getElementById('profile-logout')?.addEventListener('click', async () => {
            try { await api.logout(); } catch {} api.setToken(null); localStorage.removeItem('teacher_data'); window.location.hash = '#/login';
        });
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── QUIZZES (teacher) ───
function quizEsc(s) { return (s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
function quizEscAttr(s) { return (s || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }

async function renderQuiz(el, api) {
    try {
        const [quizzes, classes] = await Promise.all([api.getMyQuizzes(), api.getMyClasses()]);
        let html = '<div class="dash-scroll">';
        html += `<div class="flex-between" style="margin-bottom:12px">
            <div style="font-size:1rem;font-weight:700">Quizzes</div>
            <button id="btn-new-quiz" class="btn btn-primary" style="width:auto;padding:8px 14px;font-size:0.72rem">${SVG.plus} New</button></div>`;
        if (!quizzes.length) {
            html += '<div class="card"><div class="card-empty">No quizzes yet. Tap “New” to create one.</div></div>';
        } else {
            for (const q of quizzes) {
                const timed = q.time_limit_minutes ? `${q.time_limit_minutes} min` : 'Untimed';
                const statusBadge = q.status === 'closed' ? '<span class="badge badge-red">Closed</span>' : '<span class="badge badge-green">Open</span>';
                html += `<div class="card"><div style="padding:12px 14px">
                    <div class="flex-between"><div class="list-title">${quizEsc(q.title)}</div>${statusBadge}</div>
                    <div class="list-sub mt-2">${q.class_section || ''}${q.subject ? ' · ' + q.subject : ''} · ${q.num_questions} Q · ${timed}</div>
                    <div class="text-xs text-gray mt-2">${q.students_attempted} attempted${q.average_percentage !== null ? ' · avg ' + q.average_percentage + '%' : ''}</div>
                    <div style="display:flex;gap:6px;margin-top:8px;flex-wrap:wrap">
                        <button class="btn btn-outline btn-quiz-results" data-id="${q.id}" style="width:auto;padding:6px 12px;font-size:0.7rem">${SVG.chart} Results</button>
                        ${q.status !== 'closed' ? `<button class="btn btn-outline btn-quiz-close" data-id="${q.id}" style="width:auto;padding:6px 12px;font-size:0.7rem">Close</button>` : ''}
                        <button class="btn btn-outline btn-quiz-del" data-id="${q.id}" style="width:auto;padding:6px 12px;font-size:0.7rem;color:var(--red);border-color:var(--red)">Delete</button>
                    </div></div></div>`;
            }
        }
        html += '</div>';
        el.innerHTML = html;

        document.getElementById('btn-new-quiz')?.addEventListener('click', () => showNewQuizModal(api, el, classes));
        el.querySelectorAll('.btn-quiz-results').forEach(b => b.addEventListener('click', () => showQuizResults(api, el, b.dataset.id)));
        el.querySelectorAll('.btn-quiz-close').forEach(b => b.addEventListener('click', async () => {
            if (!confirm('Close this quiz? Pupils will no longer be able to take it.')) return;
            b.disabled = true;
            try { await api.closeQuiz(b.dataset.id); renderQuiz(el, api); } catch (e) { alert(e.message); b.disabled = false; }
        }));
        el.querySelectorAll('.btn-quiz-del').forEach(b => b.addEventListener('click', async () => {
            if (!confirm('Delete this quiz and all its attempts? This cannot be undone.')) return;
            b.disabled = true;
            try { await api.deleteQuiz(b.dataset.id); renderQuiz(el, api); } catch (e) { alert(e.message); b.disabled = false; }
        }));
    } catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

function showNewQuizModal(api, pageEl, classes) {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    document.body.appendChild(modal);
    const close = () => modal.remove();
    modal.addEventListener('click', (e) => { if (e.target === modal) close(); });

    const newQuestion = (type) => type === 'true_false'
        ? { text: '', type: 'true_false', points: 1, options: [{ text: 'True', correct: true }, { text: 'False', correct: false }] }
        : { text: '', type: 'mcq', points: 1, options: [{ text: '', correct: true }, { text: '', correct: false }] };
    let questions = [newQuestion('mcq')];

    modal.innerHTML = `<div class="modal" style="max-height:88vh;overflow-y:auto">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
            <div class="list-title" style="font-size:0.95rem">Create Quiz</div>
            <button id="close-quiz" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--text3)">&times;</button>
        </div>
        <div class="form-group"><label class="form-label">Title</label><input type="text" id="quiz-title" class="form-input" placeholder="e.g. Week 3 Maths Quiz" style="padding:10px 12px"></div>
        <div class="form-group"><label class="form-label">Class & Subject</label>
            <select id="quiz-class" class="form-input" style="padding:10px 12px">
                ${classes.map(c => `<option value="${c.class_section_id}|${c.subject_id}|${c.grade_id}">${c.grade} ${c.class_section} - ${c.subject}</option>`).join('')}
            </select></div>
        <div class="form-group"><label class="form-label">Instructions (optional)</label><textarea id="quiz-desc" class="form-input" rows="2" placeholder="Any instructions for pupils..." style="padding:10px 12px;resize:vertical;font-family:inherit"></textarea></div>
        <div class="form-group">
            <label class="checkbox-wrap" style="display:flex;align-items:center;gap:8px"><input type="checkbox" id="quiz-timed"><span class="form-label" style="margin:0">Timed quiz</span></label>
            <div id="quiz-time-wrap" style="display:none;margin-top:6px"><input type="number" id="quiz-minutes" class="form-input" min="1" value="10" placeholder="Minutes" style="padding:10px 12px"><div class="text-xs text-gray mt-2">Auto-submits when the time runs out.</div></div>
        </div>
        <div class="form-group"><label class="form-label">Due date (optional)</label><input type="date" id="quiz-due" class="form-input" style="padding:10px 12px"></div>
        <div style="font-weight:700;font-size:0.82rem;margin:10px 0 6px">Questions</div>
        <div id="quiz-questions"></div>
        <div style="display:flex;gap:6px;flex-wrap:wrap">
            <button id="add-question" class="btn btn-outline" style="margin-top:6px;width:auto;padding:7px 12px;font-size:0.72rem">${SVG.plus} Add Question</button>
            <button id="add-from-bank" class="btn btn-outline" style="margin-top:6px;width:auto;padding:7px 12px;font-size:0.72rem">${SVG.clipboard} Add from bank</button>
        </div>
        <div id="quiz-err" class="form-error" style="display:none;margin:10px 0 8px"></div>
        <button id="quiz-submit" class="btn btn-primary" style="padding:11px;margin-top:10px">Create Quiz</button>
    </div>`;

    modal.querySelector('#close-quiz').onclick = close;
    const timedCb = modal.querySelector('#quiz-timed');
    const timeWrap = modal.querySelector('#quiz-time-wrap');
    timedCb.addEventListener('change', () => { timeWrap.style.display = timedCb.checked ? '' : 'none'; });
    const qContainer = modal.querySelector('#quiz-questions');

    function questionHtml(q, qi) {
        const opts = q.options.map((o, oi) => {
            const radio = `<input type="radio" name="correct-${qi}" data-qi="${qi}" data-oi="${oi}" class="q-correct" ${o.correct ? 'checked' : ''}>`;
            if (q.type === 'true_false') {
                return `<label style="display:flex;align-items:center;gap:8px;padding:3px 0">${radio}<span style="font-size:0.82rem">${o.text}</span></label>`;
            }
            return `<div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">${radio}
                <input type="text" class="form-input q-opt" data-qi="${qi}" data-oi="${oi}" value="${quizEscAttr(o.text)}" placeholder="Option ${oi + 1}" style="padding:7px 9px;font-size:0.8rem;flex:1">
                ${q.options.length > 2 ? `<button class="q-opt-del" data-qi="${qi}" data-oi="${oi}" style="background:none;border:none;color:var(--red);cursor:pointer;font-size:1rem">&times;</button>` : ''}</div>`;
        }).join('');
        return `<div class="card" style="padding:12px;margin-bottom:8px;background:#f8fafc">
            <div class="flex-between" style="margin-bottom:6px"><span class="text-xs bold">Question ${qi + 1}</span>
                ${questions.length > 1 ? `<button class="q-del" data-qi="${qi}" style="background:none;border:none;color:var(--red);cursor:pointer;font-size:0.72rem">Remove</button>` : ''}</div>
            <textarea class="form-input q-text" data-qi="${qi}" rows="2" placeholder="Enter the question..." style="padding:8px;font-size:0.82rem;resize:vertical;font-family:inherit;margin-bottom:6px">${quizEsc(q.text)}</textarea>
            <div style="display:flex;gap:8px;margin-bottom:6px">
                <select class="form-input q-type" data-qi="${qi}" style="padding:7px 9px;font-size:0.8rem;flex:1">
                    <option value="mcq" ${q.type === 'mcq' ? 'selected' : ''}>Multiple choice</option>
                    <option value="true_false" ${q.type === 'true_false' ? 'selected' : ''}>True / False</option>
                </select>
                <input type="number" class="form-input q-points" data-qi="${qi}" value="${q.points}" min="1" title="Points" style="padding:7px 9px;font-size:0.8rem;width:64px">
            </div>
            <div class="text-xs text-gray" style="margin-bottom:4px">Select the correct answer:</div>
            ${opts}
            ${q.type === 'mcq' && q.options.length < 6 ? `<button class="q-opt-add btn btn-outline" data-qi="${qi}" style="margin-top:4px;width:auto;padding:5px 10px;font-size:0.7rem">+ Option</button>` : ''}
        </div>`;
    }

    function syncFromDom() {
        questions.forEach((q, qi) => {
            const t = qContainer.querySelector(`.q-text[data-qi="${qi}"]`); if (t) q.text = t.value;
            const p = qContainer.querySelector(`.q-points[data-qi="${qi}"]`); if (p) q.points = parseInt(p.value) || 1;
            if (q.type !== 'true_false') {
                qContainer.querySelectorAll(`.q-opt[data-qi="${qi}"]`).forEach(inp => { q.options[+inp.dataset.oi].text = inp.value; });
            }
            qContainer.querySelectorAll(`.q-correct[data-qi="${qi}"]`).forEach(r => { q.options[+r.dataset.oi].correct = r.checked; });
        });
    }

    function renderQuestions() {
        qContainer.innerHTML = questions.map((q, qi) => questionHtml(q, qi)).join('');
        qContainer.querySelectorAll('.q-type').forEach(s => s.addEventListener('change', () => {
            syncFromDom(); const qi = +s.dataset.qi;
            questions[qi] = { ...questions[qi], type: s.value, options: newQuestion(s.value).options };
            renderQuestions();
        }));
        qContainer.querySelectorAll('.q-opt-add').forEach(b => b.addEventListener('click', () => {
            syncFromDom(); questions[+b.dataset.qi].options.push({ text: '', correct: false }); renderQuestions();
        }));
        qContainer.querySelectorAll('.q-opt-del').forEach(b => b.addEventListener('click', () => {
            syncFromDom(); const qi = +b.dataset.qi; questions[qi].options.splice(+b.dataset.oi, 1);
            if (!questions[qi].options.some(o => o.correct)) questions[qi].options[0].correct = true;
            renderQuestions();
        }));
        qContainer.querySelectorAll('.q-del').forEach(b => b.addEventListener('click', () => {
            syncFromDom(); questions.splice(+b.dataset.qi, 1); renderQuestions();
        }));
    }

    modal.querySelector('#add-question').addEventListener('click', () => { syncFromDom(); questions.push(newQuestion('mcq')); renderQuestions(); });
    modal.querySelector('#add-from-bank').addEventListener('click', () => {
        syncFromDom();
        const subjectId = (modal.querySelector('#quiz-class').value || '').split('|')[1];
        if (!subjectId || subjectId === 'null') { alert('Select a class first.'); return; }
        showQuizBankPicker(api, subjectId, (added) => { added.forEach(q => questions.push(q)); renderQuestions(); });
    });

    modal.querySelector('#quiz-submit').addEventListener('click', async () => {
        syncFromDom();
        const errEl = modal.querySelector('#quiz-err');
        const showErr = (m) => { errEl.textContent = m; errEl.style.display = ''; };
        const title = modal.querySelector('#quiz-title').value.trim();
        const [classSectionId, subjectId, gradeId] = (modal.querySelector('#quiz-class').value || '').split('|');
        const desc = modal.querySelector('#quiz-desc').value.trim();
        const timed = modal.querySelector('#quiz-timed').checked;
        const minutes = parseInt(modal.querySelector('#quiz-minutes').value) || null;
        const due = modal.querySelector('#quiz-due').value;
        if (!title) return showErr('Enter a quiz title.');
        if (!classSectionId) return showErr('Select a class.');
        if (!questions.length) return showErr('Add at least one question.');
        for (let i = 0; i < questions.length; i++) {
            const q = questions[i];
            if (!q.text.trim()) return showErr(`Question ${i + 1}: enter the question text.`);
            if (q.options.some(o => !o.text.trim())) return showErr(`Question ${i + 1}: fill in all options.`);
            if (q.options.filter(o => o.correct).length !== 1) return showErr(`Question ${i + 1}: mark exactly one correct answer.`);
        }
        if (timed && (!minutes || minutes < 1)) return showErr('Enter a valid time limit.');
        errEl.style.display = 'none';
        const payload = {
            title, description: desc || null,
            class_section_id: parseInt(classSectionId),
            subject_id: subjectId && subjectId !== 'null' ? parseInt(subjectId) : null,
            grade_id: gradeId && gradeId !== 'null' ? parseInt(gradeId) : null,
            time_limit_minutes: timed ? minutes : null,
            due_at: due || null,
            questions: questions.map(q => ({
                question_text: q.text.trim(), type: q.type, points: q.points,
                options: q.options.map(o => ({ option_text: o.text.trim(), is_correct: !!o.correct })),
            })),
        };
        const btn = modal.querySelector('#quiz-submit');
        btn.disabled = true; btn.innerHTML = '<div class="btn-spinner"></div> Creating...';
        try { await api.createQuiz(payload); close(); renderQuiz(pageEl, api); }
        catch (e) { showErr(e.message); btn.disabled = false; btn.textContent = 'Create Quiz'; }
    });

    renderQuestions();
}

async function showQuizResults(api, pageEl, quizId) {
    try {
        const data = await api.getQuizResults(quizId);
        let html = '<div class="dash-scroll">';
        html += `<button id="quiz-back" class="btn btn-outline" style="width:auto;padding:6px 12px;font-size:0.7rem;margin-bottom:10px">← Back</button>`;
        html += `<div style="font-size:1rem;font-weight:700">${quizEsc(data.title)}</div>`;
        html += `<div class="text-xs text-gray" style="margin-bottom:12px">${data.students_attempted}/${data.class_size} attempted${data.average_percentage !== null ? ' · class avg ' + data.average_percentage + '%' : ''}</div>`;
        for (const r of data.results) {
            const badge = r.best_percentage !== null
                ? `<span class="badge ${r.best_percentage >= 50 ? 'badge-green' : 'badge-red'}">${r.best_percentage}%</span>`
                : '<span class="badge badge-amber">Not attempted</span>';
            html += `<div class="card"><div style="padding:10px 14px" class="flex-between">
                <div><div class="list-title">${quizEsc(r.name)}</div>${r.attempts ? `<div class="list-sub">${r.best_score}/${data.total_points} · ${r.attempts} attempt${r.attempts > 1 ? 's' : ''} · ${r.last_attempt || ''}</div>` : ''}</div>
                ${badge}</div></div>`;
        }
        html += '</div>';
        pageEl.innerHTML = html;
        document.getElementById('quiz-back').addEventListener('click', () => renderQuiz(pageEl, api));
    } catch (err) { pageEl.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; }
}

// ─── QUESTION BANK (teacher) ───
function qbTypeLabel(t) { return ({ mcq: 'MCQ', true_false: 'True/False', structured: 'Structured', scenario: 'Scenario' })[t] || t; }

async function renderQuestionBank(el, api) {
    let meta;
    try { meta = await api.getQuestionBankMeta(); }
    catch (err) { el.innerHTML = `<div class="dash-scroll card-empty">${err.message}</div>`; return; }
    el.innerHTML = `<div class="dash-scroll">
        <div class="flex-between" style="margin-bottom:10px">
            <div style="font-size:1rem;font-weight:700">Question Bank</div>
            <button id="qb-new" class="btn btn-primary" style="width:auto;padding:8px 14px;font-size:0.72rem">${SVG.plus} New</button>
        </div>
        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:8px">
            <select id="qb-f-subject" class="form-input" style="flex:1;min-width:110px;padding:7px 9px;font-size:0.78rem"><option value="">All subjects</option>${meta.subjects.map(s => `<option value="${s.id}">${s.name || ''}</option>`).join('')}</select>
            <select id="qb-f-curr" class="form-input" style="width:auto;padding:7px 9px;font-size:0.78rem"><option value="">Any curriculum</option><option value="ordinary">Ordinary</option><option value="cbc">New (CBC)</option></select>
            <select id="qb-f-type" class="form-input" style="width:auto;padding:7px 9px;font-size:0.78rem"><option value="">Any type</option><option value="mcq">MCQ</option><option value="true_false">True/False</option><option value="structured">Structured</option><option value="scenario">Scenario</option></select>
        </div>
        <input id="qb-f-q" class="form-input" placeholder="Search question text or topic..." style="padding:8px 10px;font-size:0.8rem;margin-bottom:10px">
        <div id="qb-list"><div class="card"><div class="card-empty">Loading…</div></div></div>
    </div>`;
    const listEl = el.querySelector('#qb-list');
    async function load() {
        listEl.innerHTML = '<div class="card"><div class="card-empty">Loading…</div></div>';
        try {
            const items = await api.getQuestionBank({
                subject_id: el.querySelector('#qb-f-subject').value,
                curriculum: el.querySelector('#qb-f-curr').value,
                type: el.querySelector('#qb-f-type').value,
                q: el.querySelector('#qb-f-q').value,
            });
            if (!items.length) { listEl.innerHTML = '<div class="card"><div class="card-empty">No questions found.</div></div>'; return; }
            listEl.innerHTML = items.map(i => {
                const tags = [i.subject, i.grade, i.topic, i.curriculum === 'cbc' ? 'CBC' : 'Ordinary', i.component, qbTypeLabel(i.type), i.max_marks + ' mk'].filter(Boolean).join(' · ');
                const snippet = quizEsc((i.question_text || '').slice(0, 140));
                return `<div class="card"><div style="padding:10px 14px">
                    <div style="font-size:0.84rem;font-weight:600">${snippet}${(i.question_text || '').length > 140 ? '…' : ''}</div>
                    <div class="text-xs text-gray mt-2">${tags}${i.is_shared ? ' · shared' : ''}${i.mine ? '' : ' · (colleague)'}</div>
                    ${i.mine ? `<div style="display:flex;gap:6px;margin-top:8px">
                        <button class="btn btn-outline qb-edit" data-id="${i.id}" style="width:auto;padding:5px 11px;font-size:0.7rem">Edit</button>
                        <button class="btn btn-outline qb-del" data-id="${i.id}" style="width:auto;padding:5px 11px;font-size:0.7rem;color:var(--red);border-color:var(--red)">Delete</button>
                    </div>` : ''}
                </div></div>`;
            }).join('');
            listEl.querySelectorAll('.qb-edit').forEach(b => b.addEventListener('click', async () => {
                try { const item = await api.getBankItem(b.dataset.id); showBankItemEditor(api, el, meta, item); }
                catch (e) { alert(e.message); }
            }));
            listEl.querySelectorAll('.qb-del').forEach(b => b.addEventListener('click', async () => {
                if (!confirm('Delete this question from the bank?')) return;
                b.disabled = true;
                try { await api.deleteBankItem(b.dataset.id); load(); } catch (e) { alert(e.message); b.disabled = false; }
            }));
        } catch (err) { listEl.innerHTML = `<div class="card"><div class="card-empty">${err.message}</div></div>`; }
    }
    el.querySelector('#qb-new').addEventListener('click', () => showBankItemEditor(api, el, meta, null));
    ['qb-f-subject', 'qb-f-curr', 'qb-f-type'].forEach(id => el.querySelector('#' + id).addEventListener('change', load));
    let t; el.querySelector('#qb-f-q').addEventListener('input', () => { clearTimeout(t); t = setTimeout(load, 350); });
    load();
}

function showBankItemEditor(api, pageEl, meta, existing) {
    const modal = document.createElement('div'); modal.className = 'modal-overlay'; document.body.appendChild(modal);
    const close = () => modal.remove();
    modal.addEventListener('click', e => { if (e.target === modal) close(); });
    const editing = !!existing;

    let type = existing?.type || 'mcq';
    let curriculum = existing?.curriculum || 'ordinary';
    let options = existing?.options?.length ? existing.options.map(o => ({ text: o.option_text, correct: o.is_correct })) : [{ text: '', correct: true }, { text: '', correct: false }];
    let rubric = existing?.rubric?.length ? existing.rubric.map(r => ({ criterion: r.criterion, marks: r.max_marks })) : [{ criterion: '', marks: 1 }];

    const subjOpts = meta.subjects.map(s => `<option value="${s.id}" ${existing && existing.subject_id == s.id ? 'selected' : ''}>${s.name || ''}</option>`).join('');
    const gradeOpts = `<option value="">— grade —</option>` + meta.grades.map(g => `<option value="${g.id}" ${existing && existing.grade_id == g.id ? 'selected' : ''}>${g.name || ''}</option>`).join('');

    modal.innerHTML = `<div class="modal" style="max-height:88vh;overflow-y:auto">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <div class="list-title" style="font-size:0.95rem">${editing ? 'Edit Question' : 'New Question'}</div>
            <button id="qb-close" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--text3)">&times;</button>
        </div>
        <div style="display:flex;gap:6px;margin-bottom:8px">
            <div class="form-group" style="flex:1;margin:0"><label class="form-label">Curriculum</label>
                <select id="qb-curr" class="form-input" style="padding:8px"><option value="ordinary" ${curriculum === 'ordinary' ? 'selected' : ''}>Ordinary</option><option value="cbc" ${curriculum === 'cbc' ? 'selected' : ''}>New (CBC)</option></select></div>
            <div class="form-group" style="flex:1;margin:0"><label class="form-label">Type</label>
                <select id="qb-type" class="form-input" style="padding:8px">
                    <option value="mcq" ${type === 'mcq' ? 'selected' : ''}>Multiple choice</option>
                    <option value="true_false" ${type === 'true_false' ? 'selected' : ''}>True / False</option>
                    <option value="structured" ${type === 'structured' ? 'selected' : ''}>Structured</option>
                    <option value="scenario" ${type === 'scenario' ? 'selected' : ''}>Scenario</option>
                </select></div>
        </div>
        <div style="display:flex;gap:6px;margin-bottom:8px">
            <div class="form-group" style="flex:1;margin:0"><label class="form-label">Subject</label><select id="qb-subject" class="form-input" style="padding:8px"><option value="">— subject —</option>${subjOpts}</select></div>
            <div class="form-group" style="flex:1;margin:0"><label class="form-label">Grade/Form</label><select id="qb-grade" class="form-input" style="padding:8px">${gradeOpts}</select></div>
        </div>
        <div style="display:flex;gap:6px;margin-bottom:8px">
            <div class="form-group" style="flex:1;margin:0"><label class="form-label">Topic</label><input id="qb-topic" class="form-input" style="padding:8px" value="${quizEscAttr(existing?.topic || '')}" placeholder="e.g. Migration"></div>
            <div class="form-group" style="margin:0"><label class="form-label">Component</label><select id="qb-component" class="form-input" style="padding:8px"><option value="">—</option><option value="theory" ${existing?.component === 'theory' ? 'selected' : ''}>Theory</option><option value="sba" ${existing?.component === 'sba' ? 'selected' : ''}>SBA</option></select></div>
            <div class="form-group" style="margin:0"><label class="form-label">Difficulty</label><select id="qb-diff" class="form-input" style="padding:8px"><option value="">—</option><option value="easy" ${existing?.difficulty === 'easy' ? 'selected' : ''}>Easy</option><option value="medium" ${existing?.difficulty === 'medium' ? 'selected' : ''}>Medium</option><option value="hard" ${existing?.difficulty === 'hard' ? 'selected' : ''}>Hard</option></select></div>
        </div>
        <div class="form-group"><label class="form-label">Question / Scenario</label><textarea id="qb-text" class="form-input" rows="4" style="padding:8px;resize:vertical;font-family:inherit" placeholder="Type the question, or paste a real-life scenario...">${quizEsc(existing?.question_text || '')}</textarea></div>
        <div id="qb-objective-mk" class="form-group" style="display:none"><label class="form-label">Marks</label><input id="qb-marks" type="number" min="1" class="form-input" style="padding:8px;width:90px" value="${(existing && (existing.type === 'mcq' || existing.type === 'true_false')) ? existing.max_marks : 1}"></div>
        <div id="qb-dynamic"></div>
        <div id="qb-model" class="form-group" style="display:none"><label class="form-label">Model answer / marking notes (optional)</label><textarea id="qb-model-a" class="form-input" rows="2" style="padding:8px;resize:vertical;font-family:inherit">${quizEsc(existing?.model_answer || '')}</textarea></div>
        <label class="checkbox-wrap" style="display:flex;align-items:center;gap:8px;margin:6px 0"><input type="checkbox" id="qb-shared" ${existing?.is_shared ? 'checked' : ''}><span class="text-sm">Share with other teachers of this subject</span></label>
        <div id="qb-err" class="form-error" style="display:none;margin:8px 0"></div>
        <button id="qb-save" class="btn btn-primary" style="padding:11px">${editing ? 'Save Changes' : 'Add to Bank'}</button>
    </div>`;

    modal.querySelector('#qb-close').onclick = close;
    const dyn = modal.querySelector('#qb-dynamic');
    const currSel = modal.querySelector('#qb-curr');
    const typeSel = modal.querySelector('#qb-type');
    const isObjective = () => type === 'mcq' || type === 'true_false';

    function syncDynamic() {
        if (isObjective()) {
            if (type !== 'true_false') dyn.querySelectorAll('.qb-opt').forEach(inp => { options[+inp.dataset.oi].text = inp.value; });
            dyn.querySelectorAll('.qb-correct').forEach(r => { options[+r.dataset.oi].correct = r.checked; });
        } else {
            dyn.querySelectorAll('.qb-crit').forEach(inp => { rubric[+inp.dataset.ri].criterion = inp.value; });
            dyn.querySelectorAll('.qb-crit-mk').forEach(inp => { rubric[+inp.dataset.ri].marks = parseInt(inp.value) || 1; });
        }
    }
    function renderDynamic() {
        modal.querySelector('#qb-objective-mk').style.display = isObjective() ? '' : 'none';
        modal.querySelector('#qb-model').style.display = isObjective() ? 'none' : '';
        if (isObjective()) {
            dyn.innerHTML = `<div class="text-xs text-gray" style="margin-bottom:4px">Options (select the correct one):</div>` + options.map((o, oi) => {
                const radio = `<input type="radio" name="qb-correct" class="qb-correct" data-oi="${oi}" ${o.correct ? 'checked' : ''}>`;
                if (type === 'true_false') return `<label style="display:flex;align-items:center;gap:8px;padding:3px 0">${radio}<span class="text-sm">${o.text}</span></label>`;
                return `<div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">${radio}<input type="text" class="form-input qb-opt" data-oi="${oi}" value="${quizEscAttr(o.text)}" placeholder="Option ${oi + 1}" style="padding:7px 9px;font-size:0.8rem;flex:1">${options.length > 2 ? `<button class="qb-opt-del" data-oi="${oi}" style="background:none;border:none;color:var(--red);cursor:pointer;font-size:1rem">&times;</button>` : ''}</div>`;
            }).join('') + (type === 'mcq' && options.length < 6 ? `<button class="qb-opt-add btn btn-outline" style="margin-top:4px;width:auto;padding:5px 10px;font-size:0.7rem">+ Option</button>` : '');
            dyn.querySelectorAll('.qb-opt-add').forEach(b => b.addEventListener('click', () => { syncDynamic(); options.push({ text: '', correct: false }); renderDynamic(); }));
            dyn.querySelectorAll('.qb-opt-del').forEach(b => b.addEventListener('click', () => { syncDynamic(); options.splice(+b.dataset.oi, 1); if (!options.some(o => o.correct)) options[0].correct = true; renderDynamic(); }));
        } else {
            dyn.innerHTML = `<div class="text-xs text-gray" style="margin-bottom:4px">Marking rubric (criterion + marks):</div>` + rubric.map((c, ri) => `<div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                <input type="text" class="form-input qb-crit" data-ri="${ri}" value="${quizEscAttr(c.criterion)}" placeholder="e.g. Gives three valid reasons" style="padding:7px 9px;font-size:0.8rem;flex:1">
                <input type="number" min="1" class="form-input qb-crit-mk" data-ri="${ri}" value="${c.marks}" title="Marks" style="padding:7px 9px;font-size:0.8rem;width:58px">
                ${rubric.length > 1 ? `<button class="qb-crit-del" data-ri="${ri}" style="background:none;border:none;color:var(--red);cursor:pointer;font-size:1rem">&times;</button>` : ''}</div>`).join('')
                + `<button class="qb-crit-add btn btn-outline" style="margin-top:4px;width:auto;padding:5px 10px;font-size:0.7rem">+ Criterion</button>`;
            dyn.querySelectorAll('.qb-crit-add').forEach(b => b.addEventListener('click', () => { syncDynamic(); rubric.push({ criterion: '', marks: 1 }); renderDynamic(); }));
            dyn.querySelectorAll('.qb-crit-del').forEach(b => b.addEventListener('click', () => { syncDynamic(); rubric.splice(+b.dataset.ri, 1); renderDynamic(); }));
        }
    }
    function applyCurriculumLock() {
        if (curriculum === 'cbc') { type = 'scenario'; typeSel.value = 'scenario'; typeSel.disabled = true; }
        else { typeSel.disabled = false; }
    }
    currSel.addEventListener('change', () => { syncDynamic(); curriculum = currSel.value; applyCurriculumLock(); type = typeSel.value; renderDynamic(); });
    typeSel.addEventListener('change', () => {
        syncDynamic(); type = typeSel.value;
        if (type === 'true_false') options = [{ text: 'True', correct: true }, { text: 'False', correct: false }];
        else if (isObjective() && options.every(o => o.text === 'True' || o.text === 'False')) options = [{ text: '', correct: true }, { text: '', correct: false }];
        renderDynamic();
    });

    modal.querySelector('#qb-save').addEventListener('click', async () => {
        syncDynamic();
        const errEl = modal.querySelector('#qb-err'); const showErr = m => { errEl.textContent = m; errEl.style.display = ''; };
        const question_text = modal.querySelector('#qb-text').value.trim();
        if (!question_text) return showErr('Enter the question or scenario.');
        const payload = {
            curriculum, type,
            subject_id: modal.querySelector('#qb-subject').value || null,
            grade_id: modal.querySelector('#qb-grade').value || null,
            topic: modal.querySelector('#qb-topic').value.trim() || null,
            component: modal.querySelector('#qb-component').value || null,
            difficulty: modal.querySelector('#qb-diff').value || null,
            question_text,
            model_answer: isObjective() ? null : (modal.querySelector('#qb-model-a').value.trim() || null),
            is_shared: modal.querySelector('#qb-shared').checked,
        };
        if (isObjective()) {
            if (options.some(o => !o.text.trim())) return showErr('Fill in all options.');
            if (options.filter(o => o.correct).length !== 1) return showErr('Mark exactly one correct answer.');
            payload.max_marks = parseInt(modal.querySelector('#qb-marks').value) || 1;
            payload.options = options.map(o => ({ option_text: o.text.trim(), is_correct: !!o.correct }));
        } else {
            if (rubric.some(c => !c.criterion.trim())) return showErr('Fill in all rubric criteria.');
            payload.rubric = rubric.map(c => ({ criterion: c.criterion.trim(), max_marks: c.marks }));
        }
        const btn = modal.querySelector('#qb-save'); btn.disabled = true; btn.innerHTML = '<div class="btn-spinner"></div> Saving...';
        try {
            if (editing) await api.updateBankItem(existing.id, payload); else await api.createBankItem(payload);
            close(); renderQuestionBank(pageEl, api);
        } catch (e) { showErr(e.message); btn.disabled = false; btn.textContent = editing ? 'Save Changes' : 'Add to Bank'; }
    });

    applyCurriculumLock();
    renderDynamic();
}

// Picker: copy objective bank questions into the quiz builder (snapshot).
async function showQuizBankPicker(api, subjectId, onConfirm) {
    const modal = document.createElement('div'); modal.className = 'modal-overlay'; document.body.appendChild(modal);
    const close = () => modal.remove();
    modal.addEventListener('click', e => { if (e.target === modal) close(); });
    modal.innerHTML = `<div class="modal" style="max-height:88vh;overflow-y:auto">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <div class="list-title" style="font-size:0.95rem">Add from Question Bank</div>
            <button id="bp-close" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--text3)">&times;</button>
        </div>
        <div class="text-xs text-gray" style="margin-bottom:8px">Auto-gradable questions (multiple-choice / true-false) for this subject.</div>
        <div id="bp-list"><div class="card"><div class="card-empty">Loading…</div></div></div>
        <button id="bp-add" class="btn btn-primary" style="margin-top:10px;padding:10px" disabled>Add selected</button>
    </div>`;
    modal.querySelector('#bp-close').onclick = close;
    const listEl = modal.querySelector('#bp-list');
    const addBtn = modal.querySelector('#bp-add');
    let items = [];
    try {
        const all = await api.getQuestionBank({ subject_id: subjectId });
        items = all.filter(i => i.type === 'mcq' || i.type === 'true_false');
    } catch (e) { listEl.innerHTML = `<div class="card"><div class="card-empty">${e.message}</div></div>`; return; }
    if (!items.length) { listEl.innerHTML = '<div class="card"><div class="card-empty">No auto-gradable questions in the bank for this subject yet.</div></div>'; return; }
    listEl.innerHTML = items.map(i => `<label class="card" style="display:block;cursor:pointer"><div style="padding:10px 14px;display:flex;gap:8px;align-items:flex-start">
        <input type="checkbox" class="bp-check" data-id="${i.id}" style="margin-top:3px">
        <div><div style="font-size:0.82rem;font-weight:600">${quizEsc((i.question_text || '').slice(0, 120))}${(i.question_text || '').length > 120 ? '…' : ''}</div>
        <div class="text-xs text-gray mt-2">${qbTypeLabel(i.type)} · ${i.max_marks} mk${i.topic ? ' · ' + quizEsc(i.topic) : ''}</div></div>
    </div></label>`).join('');
    const refresh = () => { addBtn.disabled = !listEl.querySelectorAll('.bp-check:checked').length; };
    listEl.querySelectorAll('.bp-check').forEach(c => c.addEventListener('change', refresh));
    addBtn.addEventListener('click', async () => {
        const ids = Array.from(listEl.querySelectorAll('.bp-check:checked')).map(c => c.dataset.id);
        if (!ids.length) return;
        addBtn.disabled = true; addBtn.innerHTML = '<div class="btn-spinner"></div> Adding...';
        try {
            const details = await Promise.all(ids.map(id => api.getBankItem(id)));
            const mapped = details.map(d => ({
                text: d.question_text, type: d.type, points: d.max_marks || 1,
                options: (d.options || []).map(o => ({ text: o.option_text, correct: !!o.is_correct })),
            }));
            onConfirm(mapped); close();
        } catch (e) { addBtn.disabled = false; addBtn.textContent = 'Add selected'; alert(e.message); }
    });
}

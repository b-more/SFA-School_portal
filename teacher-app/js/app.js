import { api, SERVER_BASE } from './api.js?v=1';
import { renderSplash } from './pages/splash.js?v=1';
import { renderLogin } from './pages/login.js?v=1';
import { renderDashboard } from './pages/dashboard.js?v=1';

const app = document.getElementById('app');
let schoolSettings = null;

if (localStorage.getItem('teacher_dark_mode') === '1') {
    document.documentElement.classList.add('dark-mode');
}

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(() => {});
}

function route() {
    const hash = window.location.hash || '#/splash';
    const path = hash.replace('#', '');
    if (path === '/splash') renderSplash(app, schoolSettings, onSplashDone);
    else if (path === '/login') renderLogin(app, schoolSettings, api);
    else if (path.startsWith('/dashboard')) renderDashboard(app, api, schoolSettings);
    else { window.location.hash = api.isAuthenticated() ? '#/dashboard' : '#/login'; }
}

async function onSplashDone() {
    if (api.isAuthenticated()) {
        // If remembered, go straight to dashboard without server check
        if (localStorage.getItem('teacher_remember') === 'true' && localStorage.getItem('teacher_data')) {
            window.location.hash = '#/dashboard';
            return;
        }
        // Otherwise verify token is still valid
        try { await api.getUser(); window.location.hash = '#/dashboard'; }
        catch {
            // Token expired — try auto-login with saved credentials
            if (await tryAutoLogin()) {
                window.location.hash = '#/dashboard';
            } else {
                window.location.hash = '#/login';
            }
        }
    } else {
        // No token — try auto-login with saved credentials
        if (await tryAutoLogin()) {
            window.location.hash = '#/dashboard';
        } else {
            window.location.hash = '#/login';
        }
    }
}

async function tryAutoLogin() {
    const savedEmail = localStorage.getItem('teacher_saved_email');
    const savedPass = localStorage.getItem('teacher_saved_pass');
    if (!savedEmail || !savedPass || localStorage.getItem('teacher_remember') !== 'true') return false;

    try {
        const data = await api.login(savedEmail, savedPass);
        api.setToken(data.token);
        localStorage.setItem('teacher_data', JSON.stringify(data.user));
        return true;
    } catch {
        return false;
    }
}

async function init() {
    try {
        schoolSettings = await api.getSchoolSettings();
        if (schoolSettings.logo_path) schoolSettings.logo = `${SERVER_BASE}/${schoolSettings.logo_path}`;
    } catch { schoolSettings = { name: 'St. Francis of Assisi', motto: 'Excellence in Education', logo: null }; }

    if (localStorage.getItem('teacher_remember') === 'true' && (api.isAuthenticated() || localStorage.getItem('teacher_saved_email'))) {
        window.location.hash = '#/splash';
    } else if (!window.location.hash || window.location.hash === '#/') {
        window.location.hash = '#/splash';
    }
    route();
}

window.addEventListener('hashchange', route);
init();

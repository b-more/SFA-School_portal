import { api, SERVER_BASE } from './api.js?v=8';
import { renderSplash } from './pages/splash.js?v=8';
import { renderLogin } from './pages/login.js?v=8';
import { renderDashboard } from './pages/dashboard.js?v=8';

const app = document.getElementById('app');
let schoolSettings = null;

// P9: Restore dark mode preference
if (localStorage.getItem('dark_mode') === '1') {
    document.documentElement.classList.add('dark-mode');
}

// Service Worker
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(() => {});
}

// PWA Install Prompt (P15)
window._pwaInstallPrompt = null;
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    window._pwaInstallPrompt = e;
    // Show banner if on dashboard and not dismissed recently
    if (window.location.hash.startsWith('#/dashboard') && !localStorage.getItem('pwa_dismissed')) {
        showPWABanner();
    }
});

function showPWABanner() {
    if (document.getElementById('pwa-banner') || !window._pwaInstallPrompt) return;
    const banner = document.createElement('div');
    banner.id = 'pwa-banner';
    banner.className = 'pwa-banner';
    banner.innerHTML = `
        <div class="pwa-banner-text">
            <div class="pwa-banner-title">Install Parent Portal</div>
            <div class="pwa-banner-sub">Add to home screen for quick access</div>
        </div>
        <button class="pwa-banner-install" id="pwa-install">Install</button>
        <button class="pwa-banner-close" id="pwa-close">&times;</button>
    `;
    document.body.appendChild(banner);
    document.getElementById('pwa-install').addEventListener('click', async () => {
        if (window._pwaInstallPrompt) {
            window._pwaInstallPrompt.prompt();
            const result = await window._pwaInstallPrompt.userChoice;
            window._pwaInstallPrompt = null;
        }
        banner.remove();
    });
    document.getElementById('pwa-close').addEventListener('click', () => {
        banner.remove();
        localStorage.setItem('pwa_dismissed', Date.now());
    });
}

// Router
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
        if (localStorage.getItem('remember_login') === 'true' && localStorage.getItem('user_data')) {
            window.location.hash = '#/dashboard';
            return;
        }
        try {
            await api.getUser();
            window.location.hash = '#/dashboard';
        } catch {
            if (await tryAutoLogin()) { window.location.hash = '#/dashboard'; }
            else { window.location.hash = '#/login'; }
        }
    } else {
        if (await tryAutoLogin()) { window.location.hash = '#/dashboard'; }
        else { window.location.hash = '#/login'; }
    }
}

async function tryAutoLogin() {
    const savedLogin = localStorage.getItem('saved_login');
    const savedPass = localStorage.getItem('saved_pass');
    if (!savedLogin || !savedPass || localStorage.getItem('remember_login') !== 'true') return false;
    try {
        const data = await api.login(savedLogin, savedPass, true);
        api.setToken(data.token);
        localStorage.setItem('user_data', JSON.stringify(data.user));
        localStorage.setItem('children_data', JSON.stringify(data.children));
        return true;
    } catch { return false; }
}

// Init
async function init() {
    try {
        schoolSettings = await api.getSchoolSettings();
        // Resolve logo URL relative to the server, not APP_URL
        if (schoolSettings.logo_path) {
            schoolSettings.logo = `${SERVER_BASE}/${schoolSettings.logo_path}`;
        }
    } catch {
        schoolSettings = { name: 'St. Francis of Assisi', motto: 'Excellence in Education', logo: null };
    }

    // Check for remembered login
    if (localStorage.getItem('remember_login') === 'true' && (api.isAuthenticated() || localStorage.getItem('saved_login'))) {
        window.location.hash = '#/splash';
    } else if (!window.location.hash || window.location.hash === '#/') {
        window.location.hash = '#/splash';
    }

    route();
}

window.addEventListener('hashchange', route);
init();

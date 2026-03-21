const CACHE_NAME = 'sfa-parent-v8';
const API_CACHE = 'sfa-api-cache-v1';
const ASSETS = ['/', '/css/app.css', '/js/app.js', '/js/api.js', '/js/pages/splash.js', '/js/pages/login.js', '/js/pages/dashboard.js'];

self.addEventListener('install', e => { e.waitUntil(caches.open(CACHE_NAME).then(c => c.addAll(ASSETS))); self.skipWaiting(); });
self.addEventListener('activate', e => { e.waitUntil(caches.keys().then(ks => Promise.all(ks.filter(k => k !== CACHE_NAME && k !== API_CACHE).map(k => caches.delete(k))))); });

// P10: Offline support — cache API responses, serve cached when offline
self.addEventListener('fetch', e => {
    if (e.request.url.includes('/api/')) {
        // Network-first for API calls, fallback to cache
        e.respondWith(
            fetch(e.request).then(res => {
                if (res.ok && e.request.method === 'GET') {
                    const clone = res.clone();
                    caches.open(API_CACHE).then(c => c.put(e.request, clone));
                }
                return res;
            }).catch(() => caches.match(e.request).then(r => {
                if (r) {
                    // Wrap cached response with offline indicator header
                    const headers = new Headers(r.headers);
                    headers.set('X-From-Cache', 'true');
                    return new Response(r.body, { status: r.status, statusText: r.statusText, headers });
                }
                return new Response(JSON.stringify({ message: 'You are offline.' }), {
                    status: 503, headers: { 'Content-Type': 'application/json' }
                });
            }))
        );
        return;
    }
    e.respondWith(caches.match(e.request).then(r => r || fetch(e.request)));
});

// P7: Push notifications
self.addEventListener('push', e => {
    const data = e.data ? e.data.json() : {};
    const title = data.title || 'St. Francis of Assisi';
    const options = {
        body: data.body || 'You have a new notification.',
        icon: data.icon || '/images/logo.png',
        badge: '/images/badge.png',
        tag: data.tag || 'sfa-notification',
        data: { url: data.url || '/' },
    };
    e.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', e => {
    e.notification.close();
    const url = e.notification.data?.url || '/';
    e.waitUntil(clients.matchAll({ type: 'window' }).then(cs => {
        for (const c of cs) { if (c.url.includes(url) && 'focus' in c) return c.focus(); }
        return clients.openWindow(url);
    }));
});

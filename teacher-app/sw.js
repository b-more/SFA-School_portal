const CACHE_NAME = 'sfa-teacher-v4';
const API_CACHE = 'sfa-teacher-api-v1';
const ASSETS = ['/', '/css/app.css', '/js/app.js', '/js/api.js', '/js/pages/splash.js', '/js/pages/login.js', '/js/pages/dashboard.js'];

self.addEventListener('install', e => { e.waitUntil(caches.open(CACHE_NAME).then(c => c.addAll(ASSETS))); self.skipWaiting(); });
self.addEventListener('activate', e => { e.waitUntil(caches.keys().then(ks => Promise.all(ks.filter(k => k !== CACHE_NAME && k !== API_CACHE).map(k => caches.delete(k))))); });

self.addEventListener('fetch', e => {
    if (e.request.url.includes('/teacher-api/')) {
        e.respondWith(
            fetch(e.request).then(res => {
                if (res.ok && e.request.method === 'GET') {
                    const clone = res.clone();
                    caches.open(API_CACHE).then(c => c.put(e.request, clone));
                }
                return res;
            }).catch(() => caches.match(e.request).then(r => r || new Response(JSON.stringify({ message: 'You are offline.' }), { status: 503, headers: { 'Content-Type': 'application/json' } })))
        );
        return;
    }
    e.respondWith(caches.match(e.request).then(r => r || fetch(e.request)));
});

self.addEventListener('push', e => {
    const data = e.data ? e.data.json() : {};
    e.waitUntil(self.registration.showNotification(data.title || 'SFA Teacher', {
        body: data.body || 'You have a new notification.',
        icon: '/icons/icon-192.png',
        tag: data.tag || 'sfa-teacher',
        data: { url: data.url || '/' },
    }));
});

self.addEventListener('notificationclick', e => {
    e.notification.close();
    e.waitUntil(clients.matchAll({ type: 'window' }).then(cs => {
        for (const c of cs) if ('focus' in c) return c.focus();
        return clients.openWindow(e.notification.data?.url || '/');
    }));
});

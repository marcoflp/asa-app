const CACHE = 'asa-v1';

const PRECACHE = [
    '/dashboard',
    '/offline',
    '/manifest.json',
];

// Instala e faz precache das rotas essenciais
self.addEventListener('install', e => {
    e.waitUntil(
        caches.open(CACHE).then(c => c.addAll(PRECACHE)).then(() => self.skipWaiting())
    );
});

// Remove caches antigos
self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k)))
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', e => {
    const { request } = e;
    const url = new URL(request.url);

    // Ignora requests que não são GET ou são de outros domínios
    if (request.method !== 'GET' || url.origin !== location.origin) return;

    // Assets (js, css, imagens, fontes) — cache first
    if (/\.(js|css|png|jpg|jpeg|svg|ico|woff2?)(\?.*)?$/.test(url.pathname)) {
        e.respondWith(
            caches.match(request).then(cached => cached ?? fetch(request).then(res => {
                const clone = res.clone();
                caches.open(CACHE).then(c => c.put(request, clone));
                return res;
            }))
        );
        return;
    }

    // Páginas — network first, fallback para cache, depois /offline
    e.respondWith(
        fetch(request)
            .then(res => {
                const clone = res.clone();
                caches.open(CACHE).then(c => c.put(request, clone));
                return res;
            })
            .catch(() => caches.match(request).then(cached => cached ?? caches.match('/offline')))
    );
});

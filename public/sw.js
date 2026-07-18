/* EdoBest PWA Service Worker */
const CACHE_VERSION = 'edobest-v2';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const RUNTIME_CACHE = `${CACHE_VERSION}-runtime`;

const PRECACHE_URLS = [
  '/offline.html',
  '/manifest.webmanifest',
  '/favicon.png',
  '/favicon.ico',
  '/logo.png',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png',
  '/icons/apple-touch-icon.png',
];

const CDN_HOSTS = [
  'cdn.jsdelivr.net',
  'code.jquery.com',
  'fonts.googleapis.com',
  'fonts.gstatic.com',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then((cache) => cache.addAll(PRECACHE_URLS))
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((key) => key.startsWith('edobest-') && key !== STATIC_CACHE && key !== RUNTIME_CACHE)
          .map((key) => caches.delete(key))
      )
    ).then(() => self.clients.claim())
  );
});

function isSameOrigin(url) {
  return url.origin === self.location.origin;
}

function isCdnRequest(url) {
  return CDN_HOSTS.includes(url.hostname);
}

function isNavigationRequest(request) {
  return request.mode === 'navigate' ||
    (request.method === 'GET' && request.headers.get('accept')?.includes('text/html'));
}

async function networkFirst(request) {
  try {
    const response = await fetch(request);
    if (response && response.ok && request.method === 'GET') {
      const cache = await caches.open(RUNTIME_CACHE);
      cache.put(request, response.clone());
    }
    return response;
  } catch (error) {
    const cached = await caches.match(request);
    if (cached) {
      return cached;
    }
    if (isNavigationRequest(request)) {
      const offline = await caches.match('/offline.html');
      if (offline) {
        return offline;
      }
    }
    throw error;
  }
}

async function staleWhileRevalidate(request) {
  const cache = await caches.open(RUNTIME_CACHE);
  const cached = await cache.match(request);

  const networkPromise = fetch(request)
    .then((response) => {
      if (response && response.ok) {
        cache.put(request, response.clone());
      }
      return response;
    })
    .catch(() => cached);

  return cached || networkPromise;
}

self.addEventListener('fetch', (event) => {
  const { request } = event;

  if (request.method !== 'GET') {
    return;
  }

  const url = new URL(request.url);

  // Jangan cache request non-http(s) (chrome-extension, dll.)
  if (!url.protocol.startsWith('http')) {
    return;
  }

  // Manifest & icon: cache-first via stale-while-revalidate
  if (isSameOrigin(url) && (
    url.pathname === '/manifest.webmanifest' ||
    url.pathname.startsWith('/icons/') ||
    url.pathname === '/favicon.png' ||
    url.pathname === '/favicon.ico' ||
    url.pathname === '/logo.png' ||
    url.pathname === '/offline.html'
  )) {
    event.respondWith(staleWhileRevalidate(request));
    return;
  }

  // CDN assets (Bootstrap, jQuery, fonts): stale-while-revalidate
  if (isCdnRequest(url)) {
    event.respondWith(staleWhileRevalidate(request));
    return;
  }

  // Halaman HTML Laravel: network-first (data harus fresh), fallback offline
  if (isSameOrigin(url) && isNavigationRequest(request)) {
    event.respondWith(networkFirst(request));
    return;
  }

  // Asset same-origin lainnya
  if (isSameOrigin(url)) {
    event.respondWith(staleWhileRevalidate(request));
  }
});

self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});

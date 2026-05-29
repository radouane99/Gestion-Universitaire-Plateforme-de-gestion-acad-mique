const CACHE_NAME = 'upf-portail-v1';
const API_CACHE_NAME = 'upf-portail-api-v1';
const ASSET_CACHE_NAME = 'upf-portail-assets-v1';

// Static assets to cache on install
const STATIC_ASSETS = [
  '/',
  '/offline.html',
  '/manifest.json'
];

// Install event: cache static assets
self.addEventListener('install', event => {
  console.log('[SW] Installing service worker...');
  event.waitUntil(
    Promise.all([
      caches.open(CACHE_NAME).then(cache => {
        console.log('[SW] Caching static assets');
        return cache.addAll(STATIC_ASSETS).catch(err => {
          console.warn('[SW] Cache addAll failed for static assets:', err);
        });
      })
    ]).then(() => self.skipWaiting())
  );
});

// Activate event: cleanup old caches
self.addEventListener('activate', event => {
  console.log('[SW] Activating service worker...');
  const cacheWhitelist = [CACHE_NAME, API_CACHE_NAME, ASSET_CACHE_NAME];
  
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (!cacheWhitelist.includes(cacheName)) {
            console.log('[SW] Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch event: implement caching strategies
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);
  
  // Skip non-GET requests
  if (request.method !== 'GET') return;
  
  // Skip chrome extensions and other non-http(s) requests
  if (!url.protocol.startsWith('http')) return;
  
  // Strategy 1: API calls (network-first with fallback)
  if (url.pathname.startsWith('/api/')) {
    return event.respondWith(networkFirstStrategy(request, API_CACHE_NAME));
  }
  
  // Strategy 2: Assets (cache-first with network fallback)
  if (isAsset(url.pathname)) {
    return event.respondWith(cacheFirstStrategy(request, ASSET_CACHE_NAME));
  }
  
  // Strategy 3: HTML pages (network-first with fallback)
  event.respondWith(networkFirstStrategy(request, CACHE_NAME));
});

/**
 * Network-first strategy: Try network first, fall back to cache, then offline page
 */
function networkFirstStrategy(request, cacheName) {
  return fetch(request)
    .then(response => {
      // Only cache successful responses
      if (!response || response.status !== 200 || response.type === 'error') {
        return response;
      }
      
      // Clone and cache the response
      const responseClone = response.clone();
      caches.open(cacheName).then(cache => {
        cache.put(request, responseClone);
      });
      
      return response;
    })
    .catch(() => {
      // Network failed, try cache
      return caches.match(request).then(response => {
        if (response) return response;
        
        // If it's a navigation request, return offline page
        if (request.mode === 'navigate') {
          return caches.match('/offline.html');
        }
        
        // Return a generic offline response
        return new Response('Offline - Resource not available', {
          status: 503,
          statusText: 'Service Unavailable',
          headers: new Headers({
            'Content-Type': 'text/plain'
          })
        });
      });
    });
}

/**
 * Cache-first strategy: Check cache first, fall back to network
 */
function cacheFirstStrategy(request, cacheName) {
  return caches.match(request).then(response => {
    if (response) {
      return response;
    }
    
    return fetch(request).then(response => {
      // Only cache successful responses
      if (!response || response.status !== 200) {
        return response;
      }
      
      // Clone and cache the response
      const responseClone = response.clone();
      caches.open(cacheName).then(cache => {
        cache.put(request, responseClone);
      });
      
      return response;
    }).catch(() => {
      // Network failed and no cache, return offline response
      return new Response('Offline - Asset not available', {
        status: 503,
        statusText: 'Service Unavailable',
        headers: new Headers({
          'Content-Type': 'text/plain'
        })
      });
    });
  });
}

/**
 * Determine if a URL is an asset (CSS, JS, images, fonts, etc.)
 */
function isAsset(pathname) {
  return /\.(js|css|png|jpg|jpeg|gif|svg|woff|woff2|ttf|eot|ico)$/.test(pathname) ||
         pathname.startsWith('/build/') ||
         pathname.startsWith('/icons/') ||
         pathname.startsWith('/images/') ||
         pathname.startsWith('/css/') ||
         pathname.startsWith('/js/');
}

// Handle messages from clients
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
  
  if (event.data && event.data.type === 'CLEAR_CACHE') {
    caches.keys().then(cacheNames => {
      cacheNames.forEach(cacheName => {
        caches.delete(cacheName);
      });
    });
  }
});


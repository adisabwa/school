import { precacheAndRoute } from 'workbox-precaching';
import { clientsClaim } from 'workbox-core';
import { registerRoute } from 'workbox-routing';
import { NetworkFirst, CacheFirst } from 'workbox-strategies';

// 1. Precache file statis (Vite akan menyuntikkan manifes di sini)
precacheAndRoute(self.__WB_MANIFEST);

self.skipWaiting();
clientsClaim();

// 2. Runtime Caching
// Contoh: API Cache
registerRoute(
  /^https:\/\/api\.example\.com\//,
  new NetworkFirst({ cacheName: 'api-cache' })
);

// Contoh: Image Cache
registerRoute(
  /\.(?:png|jpg|jpeg|svg)$/,
  new CacheFirst({ cacheName: 'image-cache' })
);

// 3. LOGIKA PUSH API
self.addEventListener('push', (event) => {
  let data = { title: 'Pemberitahuan', body: 'Ada pesan baru' };
  
  if (event.data) {
    try {
      data = event.data.json();
    } catch (e) {
      data.body = event.data.text();
    }
  }

  const options = {
    body: data.body,
    icon: '/assets/images/icons/android-chrome-192x192.png',
    badge: '/assets/images/icons/android-chrome-192x192.png',
    data: { 
      url: data.url || '/' 
    }
  };

  event.waitUntil(
    self.registration.showNotification(data.title, options)
  );
});

// Menangani klik pada notifikasi
self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  event.waitUntil(
    clients.openWindow(event.notification.data.url)
  );
});
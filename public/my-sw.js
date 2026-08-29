// Memuat Workbox secara lokal/offline-ready via CDN
importScripts('https://storage.googleapis.com/workbox-cdn/releases/7.0.0/workbox-sw.js');

if (workbox) {
  console.log('Workbox Berhasil Dimuat');

  // 1. Konfigurasi Dasar
  workbox.core.skipWaiting();
  workbox.core.clientsClaim();

  // 2. Precache file statis
  // Vite akan menyuntikkan manifes (array file) ke sini
  workbox.precaching.precacheAndRoute(self.__WB_MANIFEST || []);

  // 3. Runtime Caching
  // API Cache (Network First)
  // 3. Runtime Caching
  // API Cache (Network First)
  workbox.routing.registerRoute(
    ({ url }) => url.origin === 'https://api.example.com',
    new workbox.strategies.NetworkFirst({
      cacheName: 'api-cache'
    })
  );

  // Image Cache (Cache First)
  workbox.routing.registerRoute(
    /\.(?:png|jpg|jpeg|svg)$/,
    new workbox.strategies.CacheFirst({
      cacheName: 'image-cache',
      plugins: [
        new workbox.expiration.ExpirationPlugin({
          maxEntries: 50,
          maxAgeSeconds: 30 * 24 * 60 * 60, // 30 Hari
        }),
      ],
    })
  );

  // 4. LOGIKA PUSH API
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
      icon: '/ppmda/assets/images/icons/android-chrome-192x192.png',
      badge: '/ppmda/assets/images/icons/android-chrome-72x72.png',
      data: { url: data.url || '/' },
      // GETARAN LAMA (Pola: Getar 500ms, Diam 100ms, Getar 500ms, dst)
      // Ini akan terasa jauh lebih kuat daripada getaran standar
      vibrate: [1000, 110, 1000, 110, 1000, 110, 1000], 
      tag: 'urgent-alert-' + (data.id || ''),
      
      // RENOΤIFY: Paksa HP bergetar lagi meskipun notifikasi dengan tag yang sama sudah ada
      renotify: true,
      
      // REQUIRE INTERACTION: Notifikasi tidak akan hilang sampai user klik/swipe
      // Ini kunci agar terlihat seperti "Alarm"
      requireInteraction: true,
      priority: 'high',
      actions: [
        { action: 'open', title: 'LIHAT SEKARANG' },
        { action: 'close', title: 'ABAIKAN' },
        { action: 'coba', title: 'PERCOBAAN' },
      ],

    };

    event.waitUntil(
      self.registration.showNotification(data.title, options)
    );
  });

    // Klik Notifikasi
    self.addEventListener('notificationclick', (event) => {
    const notification = event.notification;
    const action = event.action; // Mengambil id 'open' atau 'close'

    // 1. Selalu tutup notifikasi setelah diklik
    notification.close();

    if (action === 'close') {
      // Jika tombol 'ABAIKAN' diklik, berhenti di sini (jangan buka link)
      console.log('User memilih untuk mengabaikan.');
      return;
    }

    // 2. Logika untuk tombol 'open' atau klik pada badan notifikasi
    const targetUrl = notification.data?.url || '/';

    event.waitUntil(
      clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
        // Jika tab aplikasi sudah terbuka, fokuskan saja
        for (let client of windowClients) {
          if (client.url.includes(targetUrl) && 'focus' in client) {
            return client.focus();
          }
        }
        // Jika belum terbuka, buka tab baru
        if (clients.openWindow) {
          return clients.openWindow(targetUrl);
        }
      })
    );
  });
} else {
  console.log('Workbox Gagal Dimuat');
}
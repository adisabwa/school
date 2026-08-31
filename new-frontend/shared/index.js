import '@2/shared/config/styles/tailwind.css'
import '@2/shared/config/styles/app.scss'
import elementPlugin from '@2/shared/config/plugins/element-ui-global'
import numberFuncPlugin from '@2/shared/config/plugins/number-functions'
import dataFuncPlugin from '@2/shared/config/plugins/data-functions'
import dateFuncPlugin from '@2/shared/config/plugins/date-functions'
import uiFuncPlugin from '@2/shared/config/plugins/ui-functions'
import directives from '@2/shared/config/plugins/directives' // import your plugin
import jsonToFormData from 'json-form-data'
import JQuery from 'jquery'
import { baseUrl, siteUrl, defaultRoute} from '@2/shared/config/url';
import API from '@2/shared/config/api'
import vue3GoogleLogin from 'vue3-google-login'
// console.log(import.meta.env)
let clientId = import.meta.env.VITE_GOOGLE_CLIENT_ID
// import AddToHomescreen from '@owliehq/vue-addtohomescreen';
const vapidKey = import.meta.env.VITE_PUBLIC_VAPID_KEY;
// Variable lokal untuk menyimpan router
export let router = null;
// vue-apps/shared/index.js
import pinia from '@2/shared/config/stores/index' // Import instance yang SAMA

export default {
  install(app, options) {
    // 1. Inisialisasi Pinia jika belum ada
    // 1. Simpan router yang dikirim dari main.js ke variable export
    if (options && options.router) {
        router = options.router;
        pinia.use(({ store }) => {
            store.router = markRaw(router);
        });
    }

    app.use(pinia);
    // (Opsional) Tambahkan ke global properties agar bisa diakses di template via $sharedRouter
    app.config.globalProperties.$sharedRouter = router;
    
    app.use(directives)
    app.use(elementPlugin)
    app.use(numberFuncPlugin)
    app.use(dataFuncPlugin)
    app.use(dateFuncPlugin)
    app.use(uiFuncPlugin)

    
    window.jsonToFormData = jsonToFormData

    
    window.jquery = JQuery
    //Variables
    
    app.config.globalProperties.$baseUrl = baseUrl; 
    app.config.globalProperties.$siteUrl = siteUrl; 
    app.config.globalProperties.defaultRoute = defaultRoute; 

    
    app.config.globalProperties.$http = API

    
    
    app.use(vue3GoogleLogin, {
        clientId: clientId
    })

    app.config.globalProperties.defaultPage = function() {
      window.location.href = siteUrl;
    }
    
    // app.use(AddToHomescreen, {
    //   buttonColor: 'blue',
    // });
    async function cleanAndRegister() {
      if (!('serviceWorker' in navigator)) return;

      // 1. Bersihkan semua SW yang ada (Opsional, gunakan hanya jika ganti logic SW)
      const registrations = await navigator.serviceWorker.getRegistrations();
      for (const registration of registrations) {
        await registration.unregister();
        console.log('🗑️ Old SW Unregistered');
      }

      const swUrl = baseUrl + 'sw.js';

      try {
        // 2. Registrasi
        const reg = await navigator.serviceWorker.register(swUrl);
        console.log('✅ SW Registered. Waiting for activation...');

        // 3. TUNGGU sampai SW benar-benar Aktif sebelum Subscribe
        // Kita cek jika SW sudah aktif, jika belum kita tunggu event statechange
        let serviceWorker = reg.installing || reg.waiting || reg.active;
        
        if (serviceWorker) {
          if (serviceWorker.state === 'activated') {
            console.log('🚀 SW already active');
            app.config.globalProperties.$registration = reg;
            subscribeUser(reg);
          } else {
            serviceWorker.addEventListener('statechange', (e) => {
              if (e.target.state === 'activated') {
                console.log('🚀 SW activated now');
                app.config.globalProperties.$registration = reg;
                subscribeUser(reg);
              }
            });
          }
        }
      } catch (error) {
        console.error('❌ Registration Failed:', error);
      }
    }

    function urlBase64ToUint8Array(base64String) {
      console.log(base64String)
      const padding = '='.repeat((4 - base64String.length % 4) % 4);
      const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
      const rawData = window.atob(base64);
      return Uint8Array.from([...rawData].map((char) => char.charCodeAt(0)));
    }

    async function subscribeUser(registration) {
      
      // 1. Request Permission
      const permission = await Notification.requestPermission();
      console.log('subscribe user')
      if (permission !== 'granted') {
        console.error('Izin notifikasi ditolak');
        return;
      }

      try {
        // 2. TUNGGU sampai Service Worker benar-benar siap (PENTING!)
      // Tunggu sejenak untuk memastikan status active tidak null
        await navigator.serviceWorker.ready; 
        
        if (!registration.active) {
          console.error("SW is registered but not active yet.");
          return;
        }
        // 3. Cek subskripsi lama
        console.log('check subscription')
        const existingSubscription = await registration.pushManager.getSubscription();

        if (existingSubscription) {
          // Unsubscribe hanya jika perlu, atau langsung return jika key-nya sama
          await existingSubscription.unsubscribe();
          console.log('Subskripsi lama dihapus.');
        }

        // 4. Konversi VAPID Key
        
        if (!vapidKey) throw new Error("VITE_PUBLIC_VAPID_KEY tidak ditemukan di .env");

        // 5. Daftar ulang dengan kunci baru
        const newSubscription = await registration.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: urlBase64ToUint8Array(vapidKey)
        });

        // 6. Kirim ke Backend CI4
        // Tambahkan await agar Anda tahu jika request ke server gagal
        await API.post('/notification/save-subscription', newSubscription);
        
        console.log('Subskripsi baru berhasil didaftarkan ke server!');
      } catch (error) {
        console.error('Gagal subscribe:', error);
      }
    }

    cleanAndRegister();
  }
};
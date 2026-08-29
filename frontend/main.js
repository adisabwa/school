import { createApp, ref} from 'vue'
import App from "./App.vue"
let app = createApp(App)

//check eksport to which school
const school = import.meta.env.VITE_SCHOOL

const schoolEls = await import(`@/config/schools/${school}.js`)

Object.keys(schoolEls).forEach(key => {
  app.config.globalProperties[('$' + key)] = schoolEls[key]
  // console.log(`Global property set: $${key} =`, schoolEls[key])
})

import router from '@/config/router'
//Modules
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)
// Add the router to Pinia so all stores can access it via 'this.router'
pinia.use(({ store }) => {
  store.router = markRaw(router)
})

app.use(router)
app.use(pinia)

//Styling
import '@/config/styles/tailwind.css'
import '@/config/styles/app.scss'

//Plugins
import elementPlugin from '@/config/plugins/element-ui-global'
import directives from '@/config/plugins/directives' // import your plugin

app.use(directives)
app.use(elementPlugin)

import jsonToFormData from 'json-form-data'
window.jsonToFormData = jsonToFormData

// import JQuery from 'jquery'
// window.jquery = JQuery
//Variables
import { baseUrl, siteUrl, defaultRoute} from '@/config/url';
app.config.globalProperties.$baseUrl = baseUrl; 
app.config.globalProperties.$siteUrl = siteUrl; 
app.config.globalProperties.defaultRoute = defaultRoute; 

import API from '@/config/api'
app.config.globalProperties.$http = API

import vue3GoogleLogin from 'vue3-google-login'
// console.log(import.meta.env)
app.use(vue3GoogleLogin, {
    clientId: import.meta.env.VITE_GOOGLE_CLIENT_ID
})


const { windowWidth, windowHeight } = useWindow()
const { x, y } = useScroll()
app.config.globalProperties.$windowWidth = windowWidth
app.config.globalProperties.$windowHeight = windowHeight
app.config.globalProperties.$scrollX = x
app.config.globalProperties.$scrollY = y

// import AddToHomescreen from '@owliehq/vue-addtohomescreen';
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
  console.log('Registering SW from:', swUrl);
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
    const vapidKey = import.meta.env.VITE_PUBLIC_VAPID_KEY;
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

router.isReady().then(() => {
  app.mount('#app')
})

import { createApp, ref} from 'vue'
import App from "@2/shared/App.vue"
import SharedModule from '@2/shared/index';
let app = createApp(App)

import router from './router'

app.use(router)
app.use(SharedModule, { router }) // Kirim router sebagai opsi ke plugin SharedComponents;

router.isReady().then(() => {
  app.mount('#app')
})

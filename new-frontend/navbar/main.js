import { createApp, ref} from 'vue'
import App from "@navbar/NavBar.vue"
import pinia from '@2/shared/config/stores/index' // Import instance yang SAMA
import { baseUrl, siteUrl, defaultRoute} from '@2/shared/config/url';
import elementPlugin from '@2/shared/config/plugins/element-ui-global'

let app = createApp(App)

app.config.globalProperties.$baseUrl = baseUrl; 
app.config.globalProperties.$siteUrl = siteUrl; 
app.config.globalProperties.defaultRoute = defaultRoute; 
app.use(elementPlugin)
app.use(pinia)
 
app.mount('#navbar-app')

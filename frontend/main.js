import { createApp, ref} from 'vue'
import App from "./App.vue"
let app = createApp(App)

//Modules
import { createPinia } from 'pinia'
const pinia = createPinia()
app.use(pinia)

import router from '@/config/routes/router'
app.use(router)


//Styling
import '@/config/styles/tailwind.css'
import '@/config/styles/app.scss'

//Plugins
import elementPlugin from '@/config/plugins/element-ui-global'
import funcPlugin from '@/config/plugins/functions'
import numberFuncPlugin from '@/config/plugins/number-functions'
import dataFuncPlugin from '@/config/plugins/data-functions'
import dateFuncPlugin from '@/config/plugins/date-functions'
import uiFuncPlugin from '@/config/plugins/ui-functions'
import directives from '@/config/plugins/directives' // import your plugin

app.use(directives)
app.use(elementPlugin)
app.use(funcPlugin)
app.use(numberFuncPlugin)
app.use(dataFuncPlugin)
app.use(dateFuncPlugin)
app.use(uiFuncPlugin)

import jsonToFormData from 'json-form-data'
window.jsonToFormData = jsonToFormData

import JQuery from 'jquery'
window.jquery = JQuery
//Variables
import { baseUrl, siteUrl, defaultRoute} from '@/config/url';
app.config.globalProperties.$baseUrl = baseUrl; 
app.config.globalProperties.$siteUrl = siteUrl; 
app.config.globalProperties.defaultRoute = defaultRoute; 

import API from '@/config/api'
app.config.globalProperties.$http = API


import AddToHomescreen from '@owliehq/vue-addtohomescreen';
app.use(AddToHomescreen, {
  buttonColor: 'blue',
});

if ('serviceWorker' in navigator) {
// Wait until the page is loaded
    window.addEventListener('load', () => {
        // Register the service worker
        navigator.serviceWorker
        .register(baseUrl + 'assets/vue/sw.js')  // This path should point to your service worker
        .then((registration) => {
            console.log('Service Worker registered with scope: ', registration.scope);
        })
        .catch((error) => {
            console.log('Service Worker registration failed: ', error);
        });
    });
} else {
    console.warn('Service workers are not supported in this browser');
  }

app.mount('#app')

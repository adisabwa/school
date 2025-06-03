import PublicLayout from '@/layouts/PublicLayout.vue'
import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const baseUrl = config.baseUrl
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'persyarikatan'
    },
    children: [
      {
        path: siteUrl + 'p',
        children: [
          {
            path: 'persyarikatan',
            component: MainLayout,
            meta: {
                requiresAuth: true,
                enterFromClass : "scale-0 opacity-50",
                enterToClass : "opacity-100",
                leaveFromClass : "opacity-100",
                leaveToClass : "scale-0 opacity-50",
            },
            children: [ 
              {
                path: '',
                name: 'persyarikatan', 
                component: () => import('@/pages/persyarikatan/Index.vue'),
                meta: {
                    pageTitle: "<b>Daftar Kegiatan</b>",
                }
              },
            ]
          },
        ],
      },
    ],
  }
]

export default routes;
import PublicLayout from '@/layouts/PublicLayout.vue'
import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const baseUrl = config.baseUrl
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'kajian'
    },
    children: [
      {
        path: siteUrl + 'p',
        children: [
          {
            path: 'kajian',
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
                name: 'kajian', 
                component: () => import('@/pages/kajian/Index.vue'),
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
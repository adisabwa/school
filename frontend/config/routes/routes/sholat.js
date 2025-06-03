import PublicLayout from '@/layouts/PublicLayout.vue'
import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const baseUrl = config.baseUrl
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'sholat'
    },
    children: [
      {
        path: siteUrl + 'p/sholat',
        children: [
          {
            path: 'sunnah',
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
                name: 'sholat-sunnah', 
                component: () => import('@/pages/sholat/sunnah/Index.vue'),
                meta: {
                    pageTitle: "<b>Daftar Hafalan Qur'an</b>",
                }
              },
            ]
          },
          {
            path: 'wajib',
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
                name: 'sholat-wajib', 
                component: () => import('@/pages/sholat/wajib/Index.vue'),
                meta: {
                    pageTitle: "<b>Daftar Sholat Wajib</b>",
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
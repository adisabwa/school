import PublicLayout from '@/layouts/PublicLayout.vue'
import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const baseUrl = config.baseUrl
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'facility'
    },
    children: [
      {
        path: siteUrl + 'p/facility',
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
            path: 'sholat',
            name: 'facility-sholat', 
            component: () => import('@/pages/facility/sholat/Index.vue'),
            meta: {
                pageTitle: "<b>Daftar Bacaan Sholat</b>",
            }
          },
          {
            path: 'dzikir-sholat',
            name: 'facility-dzikir-sholat', 
            component: () => import('@/pages/facility/dzikir-sholat/Index.vue'),
            meta: {
                pageTitle: "<b>Daftar Bacaan Doa Harian</b>",
            }
          },
          {
            path: 'doa',
            name: 'facility-doa', 
            component: () => import('@/pages/facility/doa/Index.vue'),
            meta: {
                pageTitle: "<b>Daftar Bacaan Doa Harian</b>",
            }
          },
          // {
          //   path: 'wajib',
          //   component: MainLayout,
          //   meta: {
          //       requiresAuth: true,
          //       enterFromClass : "scale-0 opacity-50",
          //       enterToClass : "opacity-100",
          //       leaveFromClass : "opacity-100",
          //       leaveToClass : "scale-0 opacity-50",
          //   },
          //   children: [ 
          //     {
          //       path: '',
          //       name: 'facility-wajib', 
          //       component: () => import('@/pages/facility/wajib/Index.vue'),
          //       meta: {
          //           pageTitle: "<b>Daftar Sholat Wajib</b>",
          //       }
          //     },
          //   ]
          // },
        ],
      },
    ],
  }
]

export default routes;
import PublicLayout from '@/layouts/PublicLayout.vue'
import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const baseUrl = config.baseUrl
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'infaq'
    },
    children: [
      {
        path: siteUrl + 'p/infaq',
        children: [
          {
            path: 'shadaqah',
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
                name: 'shadaqah', 
                component: () => import('@/pages/infaq/shadaqah/Index.vue'),
                meta: {
                    pageTitle: "<b>Daftar Hafalan Qur'an</b>",
                }
              },
            ]
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
          //       name: 'infaq-wajib', 
          //       component: () => import('@/pages/infaq/wajib/Index.vue'),
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
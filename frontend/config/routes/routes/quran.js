import PublicLayout from '@/layouts/PublicLayout.vue'
import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const baseUrl = config.baseUrl
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'quran'
    },
    children: [
      {
        path: siteUrl + 'p/quran',
        children: [
          {
            path: 'baca',
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
                name: 'quran-baca', 
                component: () => import('@/pages/quran/baca/Index.vue'),
                meta: {
                    pageTitle: "<b>Daftar Baca Qur'an</b>",
                }
              },
            ]
          },
          {
            path: 'hafal',
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
                name: 'quran-hafal', 
                component: () => import('@/pages/quran/hafal/Index.vue'),
                meta: {
                    pageTitle: "<b>Daftar Hafalan Qur'an</b>",
                }
              },
            ]
          },
          {
            path: 'tarjamah',
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
                name: 'quran-tarjamah', 
                component: () => import('@/pages/quran/tarjamah/Index.vue'),
                meta: {
                    pageTitle: "<b>Daftar Tarjamah Qur'an</b>",
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
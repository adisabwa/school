import MainLayout from '@/layouts/MainLayout.vue'

import { siteUrl } from '@/config/url'

let routes = [
  {
    meta:{
      app: 'data'
    },
    children: [
      {
        path: siteUrl + 'p/data',
        children: [
          {
            path: 'pengguna',
            component: MainLayout,
            meta: {
                requiresAuth: true,
                enterFromClass : "scale-y-0 opacity-50",
                enterToClass : "opacity-100",
                leaveFromClass : "opacity-100",
                leaveToClass : "scale-y-0 opacity-50",
            },
            children: [ 
              {
                path: '',
                name: 'pengguna-list', 
                component: () => import('@/pages/data/PenggunaList.vue'),
                meta: {
                    pageTitle: "<b>Daftar Pengguna</b>",
                    allowedRoles: ['admin','super-admin','admin-bidang'],
                    redirect:'dashboard',
                }
              },
            ]
          },
          {
            path: 'unit',
            component: MainLayout,
            meta: {
                requiresAuth: true,
                enterFromClass : "scale-y-0 opacity-50",
                enterToClass : "opacity-100",
                leaveFromClass : "opacity-100",
                leaveToClass : "scale-y-0 opacity-50",
            },
            children: [ 
              {
                path: '',
                name: 'unit-list', 
                component: () => import('@/pages/data/UnitList.vue'),
                meta: {
                    pageTitle: "<b>Daftar Unit</b>",
                    allowedRoles: ['super-admin','admin-bidang'],
                    redirect:'dashboard',
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
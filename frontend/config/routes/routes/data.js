import MainLayout from '@/layouts/MainLayout.vue'

import { siteUrl } from '@/config/url'

let routes = [
  {
    component: MainLayout,
    meta:{
      enterFromClass : "scale-y-0 opacity-50",
      enterToClass : "opacity-100",
      leaveFromClass : "opacity-100",
      leaveToClass : "scale-y-0 opacity-50",
      requiresAuth: true,
    },
    children: [
      {
        path: siteUrl + 'p/data',
        children: [
          {
            path: 'pengguna',
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
            children: [ 
              {
                path: '',
                name: 'unit-list', 
                component: () => import('@/pages/data/UnitList.vue'),
                meta: {
                    pageTitle: "<b>Daftar Unit</b>",
                    allowedRoles: ['super-admin','admin-bidang','admin'],
                    redirect:'dashboard',
                }
              },
            ]
          },
          {
            path: 'penghasilan',
            children: [ 
              {
                path: '',
                name: 'penghasilan-list', 
                component: () => import('@/pages/data/PenghasilanList.vue'),
                meta: {
                    pageTitle: "<b>Daftar Unit</b>",
                    allowedRoles: ['super-admin','admin-bidang','admin'],
                    redirect:'dashboard',
                }
              },
            ]
          },
          {
            path: 'santri',
            children: [ 
              {
                path: '',
                name: 'santri-list', 
                component: () => import('@/pages/data/SantriList.vue'),
                meta: {
                    pageTitle: "<b>Data Santri</b>",
                    allowedRoles: ['super-admin','admin-bidang','admin'],
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
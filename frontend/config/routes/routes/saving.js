import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'saving',
    },
    children: [
      {
        path: siteUrl + 'p/saving/',
        children: [
            {
                path: 'admin',
                component: () => import('@/layouts/MainLayout.vue'),
                meta: {
                    requiresAuth: true,
                    enterFromClass : "scale-0 opacity-50",
                    enterToClass : "opacity-100",
                    leaveFromClass : "opacity-100",
                    leaveToClass : "scale-0 opacity-50",
                    allowedRoles: ['admin'],
                },
                children: [
                  {
                    path: 'dashboard',
                    name: 'saving-dashboard',
                    component: () => import('@/pages/saving/Dashboard.vue'),
                  },   
                  {
                    path: '',
                    name: 'admin-saving', 
                    component: () => import('@/pages/saving/admin/Index.vue'),
                    meta: {
                        pageTitle: '<b>Daftar Tabungans Santri</b>',
                    }
                  },
                  {
                    path: 'rekapitulasi',
                    name: 'rekap-saving', 
                    component: () => import('@/pages/saving/admin/Rekapitulasi.vue'),
                    meta: {
                        pageTitle: '<b>Rekapitulasi Tabungans</b>',
                    }
                  },
                  {
                    path: 'kas',
                    name: 'kas-saving', 
                    component: () => import('@/pages/saving/admin/Kas.vue'),
                    meta: {
                        pageTitle: '<b>Data Kas</b>',
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
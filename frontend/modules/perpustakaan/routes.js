import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'perpustakaan',
    },
    children: [
      {
        path: siteUrl + 'p/perpustakaan/',
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
                path: '',
                component: () => import('@/layouts/MainLayout.vue'),
                children: [
                  {
                    path: 'dashboard',
                    name: 'perpustakaan-dashboard',
                    component: () => import('./pages/Dashboard.vue'),
                  },  
                ],
            },
            {
                path: 'admin',
                component: () => import('@/layouts/MainLayout.vue'),
                children: [
                  {
                    path: 'buku',
                    name: 'buku-list', 
                    component: () => import('./pages/admin/Buku.vue'),
                    meta: {
                        pageTitle: '<b>Data Buku</b>',
                    }
                  },
                  {
                    path: 'peminjaman',
                    name: 'peminjaman-list', 
                    component: () => import('./pages/admin/Peminjaman.vue'),
                    meta: {
                        pageTitle: '<b>Data Peminjaman</b>',
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
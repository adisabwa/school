import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'mapel',
    },
    children: [
      {
        path: siteUrl + 'p/mapel/',
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
                    path: '',
                    name: 'admin-mapel', 
                    component: () => import('./pages/admin/Index.vue'),
                    meta: {
                        pageTitle: '<b>Daftar Pelanggaran Santri</b>',
                    }
                  },
                  {
                    path: 'pembagian',
                    name: 'pembagian-mapel', 
                    component: () => import('./pages/admin/Pembagian.vue'),
                    meta: {
                        pageTitle: '<b>Pembagian Pengampu</b>',
                    }
                  },
                  {
                    path: 'penjadwalan',
                    name: 'penjadwalan-mapel', 
                    component: () => import('./pages/admin/Penjadwalan.vue'),
                    meta: {
                        pageTitle: '<b>Penjadwalan Pembelajaran</b>',
                    }
                  },
                ]
            },
            {
                path: 'rpp',
                component: () => import('@/layouts/MainLayout.vue'),
                meta: {
                    requiresAuth: true,
                    enterFromClass : "-translate-x-full opacity-50",
                    enterToClass : "opacity-100",
                    leaveFromClass : "opacity-100",
                    leaveToClass : "translate-x-full opacity-50",
                },
                children: [
                  {
                    path: '',
                    name: 'mapel-dashboard-guru', 
                    component: () => import('./pages/guru/Dashboard.vue'),
                    meta: {
                    }
                  },
                  {
                    path: 'materi',
                    name: 'mapel-materi', 
                    component: () => import('./pages/guru/Materi.vue'),
                    meta: {
                    }
                  },
                  {
                    path: 'prota',
                    name: 'mapel-prota', 
                    component: () => import('./pages/guru/ProtaPromesView.vue'),
                    meta: {
                    }
                  },
                  {
                    path: 'generate',
                    name: 'mapel-rpp', 
                    component: () => import('./pages/guru/RppGenerator.vue'),
                    meta: {
                    }
                  },
                  {
                    path: 'history',
                    name: 'mapel-rpp-history', 
                    component: () => import('./pages/guru/SavedRpp.vue'),
                    meta: {
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
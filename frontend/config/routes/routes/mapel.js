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
                component: MainLayout,
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
                    component: () => import('@/pages/mapel/admin/Index.vue'),
                    meta: {
                        pageTitle: '<b>Daftar Pelanggaran Santri</b>',
                    }
                  },
                  {
                    path: 'pembagian',
                    name: 'pembagian-mapel', 
                    component: () => import('@/pages/mapel/admin/Pembagian.vue'),
                    meta: {
                        pageTitle: '<b>Pembagian Pengampu</b>',
                    }
                  },
                  {
                    path: 'penjadwalan',
                    name: 'penjadwalan-mapel', 
                    component: () => import('@/pages/mapel/admin/Penjadwalan.vue'),
                    meta: {
                        pageTitle: '<b>Penjadwalan Pembelajaran</b>',
                    }
                  },
                ]
            },
            {
              path: '',
              component: MainLayout,
              meta: {
                  requiresAuth: true,
                  enterFromClass : "translate-x-full opacity-50",
                  enterToClass : "translate-x-0",
                  leaveFromClass : "translate-x-0",
                  leaveToClass : "translate-x-full opacity-50",
              },
              children: [
                {
                  path: 'dashboard',
                  name: 'mapel-dashboard',
                  component: () => import('@/pages/mapel/Dashboard.vue'),
                },   
                {
                  path: 'nilai-mapel',
                  name: 'nilai-mapel',
                  component: () => import('@/pages/mapel/Nilai.vue'),
                },  
                {
                  path: 'rekap-nilai',
                  name: 'rekap-nilai',
                  component: () => import('@/pages/mapel/RekapNilai.vue'),
                },  
                {
                  path: 'dashboard-nilai',
                  name: 'dashboard-nilai',
                  component: () => import('@/pages/mapel/DashboardNilai.vue'),
                },  
              ]
            }
        ],
      },
    ],
  }
]

export default routes;
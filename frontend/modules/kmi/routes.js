import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'kmi',
    },
    children: [
      {
        path: siteUrl + 'p/kmi/',
        children: [
            {
              path: '',
              component: () => import('@/layouts/MainLayout.vue'),
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
                  name: 'kmi-dashboard',
                  component: () => import('./pages/Dashboard.vue'),
                },   
                {
                  path: 'kaldik',
                  name: 'kalender', 
                  component: () => import('./pages/Kalender.vue'),
                  meta: {
                      pageTitle: "<b>Kalender Akademik</b>",
                      allowedRoles: ['admin','wamar','guru','walas'],
                  }
                },
                {
                  path: 'kelas',
                  name: 'kmi-kelas-list', 
                  component: () => import('@/modules/data/pages/KelasAjarList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Kelas</b>",
                      allowedRoles: ['admin'],
                  }
                },
                {
                  path: 'santri-kelas',
                  name: 'santri-kelas-list', 
                  component: () => import('./pages/SantriKelas.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Santri</b>",
                      allowedRoles: ['admin', 'walas'],
                  }
                },
              ]
            }
        ],
      },
    ],
  }
]

export default routes;
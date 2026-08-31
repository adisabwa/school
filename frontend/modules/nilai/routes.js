import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'nilai',
    },
    children: [
      {
        path: siteUrl + 'p/nilai/',
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
                  name: 'mapel-dashboard',
                  component: () => import('./pages/Dashboard.vue'),
                },   
                {
                  path: 'nilai-mapel',
                  name: 'nilai-mapel',
                  component: () => import('./pages/Nilai.vue'),
                },  
                {
                  path: 'dashboard-nilai',
                  name: 'dashboard-nilai',
                  component: () => import('./pages/DashboardNilai.vue'),
                },  
              ]
            }
        ],
      },
    ],
  }
]

export default routes;
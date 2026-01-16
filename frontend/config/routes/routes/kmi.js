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
                  component: () => import('@/pages/kmi/Dashboard.vue'),
                },   
                {
                  path: 'kaldik',
                  name: 'kalender', 
                  component: () => import('@/pages/kmi/Kalender.vue'),
                  meta: {
                      pageTitle: "<b>Kalender Akademik</b>",
                      allowedRoles: ['admin','wamar','guru','walas'],
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
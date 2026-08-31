import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'profil',
    },
    children: [
      {
        path: siteUrl + 'p/profil/',
        children: [
            {
                path: '',
                component: () => import('@/layouts/MainLayout.vue'),
                meta: {
                    requiresAuth: true,
                    enterFromClass : "scale-0 opacity-50",
                    enterToClass : "opacity-100",
                    leaveFromClass : "opacity-100",
                    leaveToClass : "scale-0 opacity-50",
                    allowedRoles: ['ortu', 'admin'],
                },
                children: [
                  {
                    path: 'anak-orang-tua',
                    name: 'profil-anak-ortu',
                    component: () => import('./pages/ProfilAnakOrangTua.vue'),
                  },   
                ]
            },
        ],
      },
    ],
  }
]

export default routes;
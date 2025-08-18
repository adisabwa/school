import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'presensi',
    },
    children: [
      {
        path: siteUrl + 'p/presensi/',
        children: [
            {
                path: 'kmi',
                component: MainLayout,
                meta: {
                    requiresAuth: true,
                    enterFromClass : "scale-0 opacity-50",
                    enterToClass : "opacity-100",
                    leaveFromClass : "opacity-100",
                    leaveToClass : "scale-0 opacity-50",
                    allowedRoles: ['admin','kmi'],
                },
                children: [
                  {
                    path: 'dashboard',
                    name: 'presensi-dashboard',
                    component: () => import('@/pages/presensi/Dashboard.vue'),
                  },   
                  {
                    path: '',
                    name: 'kmi-presensi', 
                    component: () => import('@/pages/presensi/kmi/Index.vue'),
                    meta: {
                        pageTitle: '<b>Daftar Presensi Guru</b>',
                    }
                  },
                  // {
                  //   path: 'rekapitulasi',
                  //   name: 'rekap-presensi', 
                  //   component: () => import('@/pages/presensi/kmi/Rekapitulasi.vue'),
                  //   meta: {
                  //       pageTitle: '<b>Rekapitulasi Presensi</b>',
                  //   }
                  // },
                ]
            },
        ],
      },
    ],
  }
]

export default routes;
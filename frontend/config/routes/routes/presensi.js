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
                path: 'admin',
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
                    path: 'kelas',
                    name: 'kelas-presensi', 
                    component: () => import('@/pages/presensi/admin/Kelas.vue'),
                    meta: {
                        pageTitle: '<b>Daftar Presensi Mengajar</b>',
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
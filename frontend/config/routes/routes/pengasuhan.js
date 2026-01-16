import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'pengasuhan',
    },
    children: [
      {
        path: siteUrl + 'p/pengasuhan/',
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
                    allowedRoles: ['admin','wamar'],
                },
                children: [
                  {
                    path: 'teacher',
                    name: 'teacher-check-pengasuhan', 
                    component: () => import('@/pages/data/GuruList.vue'),
                    meta: {
                        pageTitle: "<b>Verifikasi Data Guru</b>",
                        allowedRoles: ['admin','wamar','guru','walas'],
                    }
                  },
                  {
                    path: '',
                    name: 'admin-pengasuhan', 
                    component: () => import('@/pages/pengasuhan/Index.vue'),
                    meta: {
                        pageTitle: '<b>Nilai Pengasuhan</b>',
                    }
                  },
                  {
                    path: 'santri',
                    name: 'santri-kamar-list', 
                    component: () => import('@/pages/pengasuhan/SantriKamar.vue'),
                    meta: {
                        pageTitle: '<b>Data Penghuni Kamar</b>',
                    }
                  },
                  // {
                  //   path: 'rekapitulasi',
                  //   name: 'rekap-pengasuhan', 
                  //   component: () => import('@/pages/pengasuhan/kmi/Rekapitulasi.vue'),
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
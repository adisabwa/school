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
                component: () => import('@/layouts/MainLayout.vue'),
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
            {
                path: '',
                component: () => import('@/layouts/MainLayout.vue'),
                meta: {
                    requiresAuth: true,
                    enterFromClass : "scale-0 opacity-50",
                    enterToClass : "opacity-100",
                    leaveFromClass : "opacity-100",
                    leaveToClass : "scale-0 opacity-50",
                },
                children: [
                  {
                    path: 'dashboard',
                    name: 'presensi-dashboard', 
                    component: () => import('@/pages/presensi/Dashboard.vue'),
                    meta: {
                        pageTitle: '<b>Dashboard Presensi</b>',
                    }
                  },
                  {
                    path: 'scanner',
                    name: 'presensi-scanner', 
                    component: () => import('@/pages/presensi/Scanner.vue'),
                    meta: {
                        pageTitle: '<b>Scanner Presensi</b>',
                    }
                  },
                  {
                    path: 'form',
                    name: 'presensi-form', 
                    component: () => import('@/pages/presensi/FormKehadiran.vue'),
                    meta: {
                        pageTitle: '<b>Input Presensi</b>',
                    }
                  },
                  {
                    path: 'list',
                    name: 'presensi-list', 
                    component: () => import('@/pages/presensi/ListKehadiran.vue'),
                    meta: {
                        pageTitle: '<b>Input Presensi</b>',
                    }
                  },
                  {
                    path: 'finish',
                    name: 'presensi-finish', 
                    component: () => import('@/pages/presensi/Finish.vue'),
                    meta: {
                        pageTitle: '<b>Presensi Selesai</b>',
                    }
                  },
                  {
                    path: 'report',
                    name: 'presensi-report', 
                    component: () => import('@/pages/presensi/Report.vue'),
                    meta: {
                        pageTitle: '<b>Laporan Presensi</b>',
                    }
                  },
                  {
                    path: 'report-walas',
                    name: 'presensi-report-walas', 
                    component: () => import('@/pages/presensi/ReportWalas.vue'),
                    meta: {
                        pageTitle: '<b>Laporan Presensi</b>',
                    }
                  },
                ],
            }
        ],
      },
    ],
  }
]

export default routes;
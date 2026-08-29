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
                    component: () => import('./pages/admin/Kelas.vue'),
                    meta: {
                        pageTitle: '<b>Daftar Presensi Mengajar</b>',
                    }
                  },
                  {
                    path: '',
                    name: 'presensi-admin-dashboard', 
                    component: () => import('./pages/admin/Dashboard.vue'),
                    meta: {
                        pageTitle: '<b>Dashboard Presensi</b>',
                    }
                  },
                  {
                    path: 'sumamary',
                    name: 'presensi-summary', 
                    component: () => import('./pages/admin/Summary.vue'),
                    meta: {
                        pageTitle: '<b>Kehadiaran Guru</b>',
                    }
                  },
                  {
                    path: 'report',
                    name: 'presensi-report-admin', 
                    component: () => import('./pages/admin/Report.vue'),
                    meta: {
                        pageTitle: '<b>Laporan Kehadiran</b>',
                    }
                  },
                  {
                    path: 'sumamary-kedatangan',
                    name: 'presensi-summary-kedatangan', 
                    component: () => import('./pages/admin/SummaryKedatangan.vue'),
                    meta: {
                        pageTitle: '<b>Kehadiaran Guru</b>',
                    }
                  },
                  // {
                  //   path: 'rekapitulasi',
                  //   name: 'rekap-presensi', 
                  //   component: () => import('./pages/kmi/Rekapitulasi.vue'),
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
                    component: () => import('./pages/Dashboard.vue'),
                    meta: {
                        pageTitle: '<b>Dashboard Presensi</b>',
                    }
                  },
                  {
                    path: 'scanner',
                    name: 'presensi-scanner', 
                    component: () => import('./pages/Scanner.vue'),
                    meta: {
                        pageTitle: '<b>Scanner Presensi</b>',
                    }
                  },
                  {
                    path: 'form',
                    name: 'presensi-form', 
                    component: () => import('./pages/FormKehadiran.vue'),
                    meta: {
                        pageTitle: '<b>Input Presensi</b>',
                    }
                  },
                  {
                    path: 'list',
                    name: 'presensi-list', 
                    component: () => import('./pages/ListKehadiran.vue'),
                    meta: {
                        pageTitle: '<b>Input Presensi</b>',
                    }
                  },
                  {
                    path: 'finish',
                    name: 'presensi-finish', 
                    component: () => import('./pages/Finish.vue'),
                    meta: {
                        pageTitle: '<b>Presensi Selesai</b>',
                    }
                  },
                  {
                    path: 'report',
                    name: 'presensi-report', 
                    component: () => import('./pages/Report.vue'),
                    meta: {
                        pageTitle: '<b>Laporan Presensi</b>',
                    }
                  },
                  {
                    path: 'izin',
                    name: 'presensi-izin', 
                    component: () => import('./pages/Izin.vue'),
                    meta: {
                        pageTitle: '<b>Perizinan</b>',
                    }
                  },
                  {
                    path: 'report-walas',
                    name: 'presensi-report-walas', 
                    component: () => import('./pages/ReportWalas.vue'),
                    meta: {
                        pageTitle: '<b>Laporan Presensi</b>',
                    }
                  },

                  /// Datang / Pulang
                  {
                    path: 'teacher-scanner',
                    name: 'presensi-teacher-scanner', 
                    component: () => import('./pages/TeacherScanner.vue'),
                    meta: {
                        pageTitle: '<b>Scanner Presensi</b>',
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
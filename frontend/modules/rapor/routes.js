import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'rapor',
    },
    children: [
      {
        path: siteUrl + 'p/rapor/',
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
                  path: 'rapor-dashboard',
                  name: 'rapor-dashboard',
                  component: () => import('@/modules/rapor/pages/Dashboard.vue'),
                },   
                {
                  path: 'teacher',
                  name: 'teacher-check-raport', 
                  component: () => import('@/modules/data/pages/GuruList.vue'),
                  meta: {
                      pageTitle: "<b>Verifikasi Data Guru</b>",
                      allowedRoles: ['admin','wamar','guru','walas'],
                  }
                },
                {
                  path: 'rekap-nilai-uts',
                  name: 'rekap-nilai-uts',
                  component: () => import('./pages/RekapNilai.vue'),
                },  
                {
                  path: 'rekap-nilai-akhir',
                  name: 'rekap-nilai-akhir',
                  component: () => import('./pages/RekapNilaiAkhir.vue'),
                },  
                {
                  path: 'rekap-nilai-pengasuhan',
                  name: 'rekap-nilai-pengasuhan',
                  component: () => import('./pages/RekapNilaiPengasuhan.vue'),
                },  
                {
                  path: 'rekap-santri',
                  name: 'rekap-santri',
                  component: () => import('./pages/RekapSantri.vue'),
                },  
                {
                  path: 'catatan-nilai-akhir',
                  name: 'catatan-nilai-akhir',
                  component: () => import('./pages/CatatanWalas.vue'),
                },  
                {
                  path: 'download-nilai-akhir',
                  name: 'download-nilai-akhir',
                  component: () => import('./pages/DownloadRapor.vue'),
                },  
              ]
            }
        ],
      },
    ],
  }
]

export default routes;
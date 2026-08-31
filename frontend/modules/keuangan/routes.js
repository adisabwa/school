
import config from '@/config/url'
const baseUrl = config.baseUrl
const siteUrl = config.siteUrl

let routes = [
  {
    path: siteUrl + 'p/keauangan/',
    children: [
      {
          path: 'admin',
          component: () => import('@/layouts/MainLayout.vue'),
          meta: {
            app: 'keuangan',
            requiresAuth: true,
            allowedRoles:['admin'],
            enterFromClass : "scale-0 opacity-50",
            enterToClass : "opacity-100",
            leaveFromClass : "opacity-100",
            leaveToClass : "scale-0 opacity-50",
          },
          children: [
            {
              path: '',
              name: 'keuangan-dashboard', 
              component: () => import('./pages/admin/Dashboard.vue'),
              meta: {
                pageTitle: '<b>Dashboard</b>',
              }
            },
            {
              path: 'iuran',
              name: 'keuangan-iuran', 
              component: () => import('./pages/admin/Iuran.vue'),
              meta: {
                pageTitle: '<b>Iuran</b>',
              }
            },
            {
              path: 'transaksi',
              name: 'keuangan-transaksi', 
              component: () => import('./pages/admin/Transaksi.vue'),
              meta: {
                pageTitle: '<b>Transaksi</b>',
              }
            },
            {
              path: 'laporan',
              name: 'keuangan-laporan', 
              component: () => import('./pages/admin/reports/Index.vue'),
              meta: {
                pageTitle: '<b>Laporan Keuangan</b>',
              }
            },
            {
              path: 'iuran',
              children: [
                {
                  path: 'detail-tagihan',
                  name: 'keuangan-iuran-detail-tagihan', 
                  component: () => import('./pages/admin/iuran/DetailTagihan.vue'),
                  meta: {
                    pageTitle: '<b>Detail Tagihan</b>',
                  }
                },
                {
                  path: 'pembayaran-tagihan',
                  name: 'keuangan-iuran-pembayaran-tagihan', 
                  component: () => import('./pages/admin/iuran/PembayaranTagihan.vue'),
                  meta: {
                    pageTitle: '<b>Pembayaran Tagihan</b>',
                  }
                },
                {
                  path: 'saldo',
                  name: 'keuangan-iuran-saldo', 
                  component: () => import('./pages/admin/iuran/Saldo.vue'),
                  meta: {
                    pageTitle: '<b>Saldo</b>',
                  }
                },
                {
                  path: 'tagihan',
                  name: 'keuangan-iuran-tagihan', 
                  component: () => import('./pages/admin/iuran/Tagihan.vue'),
                  meta: {
                    pageTitle: '<b>Tagihan</b>',
                  }
                },
              ],
            },
            {
              path: 'data',
              children: [
                {
                  path: 'kas',
                  name: 'keuangan-data-kas', 
                  component: () => import('./pages/admin/data/Kas.vue'),
                  meta: {
                    pageTitle: '<b>Data Kas</b>',
                  }
                },
                {
                  path: 'iuran',
                  name: 'keuangan-data-iuran', 
                  component: () => import('./pages/admin/data/Iuran.vue'),
                  meta: {
                    pageTitle: '<b>Data Iuran</b>',
                  }
                },
                {
                  path: 'kategori',
                  name: 'keuangan-data-kategori', 
                  component: () => import('./pages/admin/data/Kategori.vue'),
                  meta: {
                    pageTitle: '<b>Data Kategori</b>',
                  }
                },
                {
                  path: 'metode',
                  name: 'keuangan-data-metode', 
                  component: () => import('./pages/admin/data/Metode.vue'),
                  meta: {
                    pageTitle: '<b>Data Metode</b>',
                  }
                },
                {
                  path: 'pos',
                  name: 'keuangan-data-pos', 
                  component: () => import('./pages/admin/data/Pos.vue'),
                  meta: {
                    pageTitle: '<b>Data Pos</b>',
                  }
                },
              ],
            },
          ]
      },
    ],
  }
]

export default routes;

import config from '@/config/url'
const baseUrl = config.baseUrl
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'psb'
    },
    children: [
      {
        path: siteUrl + 'p/psb/',
        children: [
          {
              path: '',
              component: () => import('@/layouts/PublicLayout.vue'),
              meta: {
                enterFromClass : "translate-x-full opacity-0",
                enterToClass : "opacity-50",
                leaveFromClass : "opacity-50",
                leaveToClass : "-translate-x-full opacity-0",
              },
              children: [
                {
                  path: '',
                  name: 'psb-start', 
                  component: () => import('./pages/Start.vue'),
                  meta: {
                    pageTitle: '<b>Pendaftaran Santri Baru</b>',
                  }
                },
                {
                  path: 'create',
                  name: 'psb-create', 
                  component: () => import('./pages/Create.vue'),
                  meta: {
                    pageTitle: '<b>Pendaftaran Santri Baru</b>',
                  }
                },
                {
                  path: 'view',
                  name: 'psb-view', 
                  component: () => import('./pages/View.vue'),
                  props:true,
                  meta: {
                    pageTitle: '<b>Pendaftaran Santri Baru</b>',
                  }
                },
                {
                  path: 'finish',
                  name: 'psb-finish', 
                  component: () => import('./pages/Finish.vue'),
                  meta: {
                    pageTitle: '<b>Pendaftaran Santri Baru</b>',
                  }
                },
                {
                  path: 'info',
                  name: 'psb-info', 
                  component: () => import('./pages/Info.vue'),
                  meta: {
                    pageTitle: '<b>Pendaftaran Santri Baru</b>',
                  }
                }
              ],
          },
          {
              path: 'admin',
              component: () => import('@/layouts/MainLayout.vue'),
              meta: {
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
                  name: 'admin-psb', 
                  component: () => import('./pages/admin/Index.vue'),
                  meta: {
                    pageTitle: '<b>Daftar Calon Santri</b>',
                  }
                },
                {
                  path: 'dashboard',
                  name: 'psb-dashboard',
                  component: () => import('./pages/Dashboard.vue'),
                },
              ]
          },
        ],
      },
    ],
  }
]

export default routes;
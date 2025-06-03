import PublicLayout from '@/layouts/PublicLayout.vue'
import MainLayout from '@/layouts/MainLayout.vue'

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
              component: PublicLayout,
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
                  component: () => import('@/pages/psb/Start.vue'),
                  meta: {
                    pageTitle: '<b>Pendaftaran Santri Baru</b>',
                  }
                },
                {
                  path: 'create',
                  name: 'psb-create', 
                  component: () => import('@/pages/psb/Create.vue'),
                  meta: {
                    pageTitle: '<b>Pendaftaran Santri Baru</b>',
                  }
                },
                {
                  path: 'view',
                  name: 'psb-view', 
                  component: () => import('@/pages/psb/View.vue'),
                  props:true,
                  meta: {
                    pageTitle: '<b>Pendaftaran Santri Baru</b>',
                  }
                },
                {
                  path: 'finish',
                  name: 'psb-finish', 
                  component: () => import('@/pages/psb/Finish.vue'),
                  meta: {
                    pageTitle: '<b>Pendaftaran Santri Baru</b>',
                  }
                },
                {
                  path: 'info',
                  name: 'psb-info', 
                  component: () => import('@/pages/psb/Info.vue'),
                  meta: {
                    pageTitle: '<b>Pendaftaran Santri Baru</b>',
                  }
                }
              ],
          },
          {
              path: 'admin',
              component: MainLayout,
              meta: {
                requiresAuth: true,
                enterFromClass : "scale-0 opacity-50",
                enterToClass : "opacity-100",
                leaveFromClass : "opacity-100",
                leaveToClass : "scale-0 opacity-50",
              },
              children: [
                {
                  path: '',
                  name: 'admin-psb', 
                  component: () => import('@/pages/psb/admin/Index.vue'),
                  meta: {
                    pageTitle: '<b>Daftar Calon Santri</b>',
                  }
                },
                {
                  path: 'dashboard',
                  name: 'psb-dashboard',
                  component: () => import('@/pages/psb/Dashboard.vue'),
                },
              ]
          },
        ],
      },
    ],
  }
]

export default routes;
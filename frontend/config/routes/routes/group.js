import PublicLayout from '@/layouts/PublicLayout.vue'
import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const baseUrl = config.baseUrl
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'group'
    },
    children: [
      {
        path: siteUrl + 'p/group',
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
            },
            children: [ 
              {
                path: '',
                name: 'group-admin', 
                component: () => import('@/pages/group/admin/Index.vue'),
                meta: {
                    pageTitle: "<b>Daftar Group</b>",
                    allowedRoles: ['admin','super-admin'],
                    redirect:'group-user',
                }
              },
            ]
          },
          {
            path:'',
            component: MainLayout,
            meta: {
                requiresAuth: true,
                enterFromClass : "-translate-x-100 opacity-50",
                enterToClass : "opacity-100",
                leaveFromClass : "opacity-100",
                leaveToClass : "translate-x-100 opacity-50",
            },
            children: [ 
              {
                path: '',
                name: 'group-user', 
                component: () => import('@/pages/group/Index.vue'),
                meta: {
                    pageTitle: "<b>Daftar Group</b>",
                    // redirect:'dashboard',
                }
              },
            ]
          },
        ],
      },
    ],
  }
]

export default routes;
import PublicLayout from '@/layouts/PublicLayout.vue'
import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const baseUrl = config.baseUrl
const siteUrl = config.siteUrl

let routes = [ 
  {
      path: siteUrl + 'p/',
      component: PublicLayout,
      meta: {
        enterFromClass : "translate-y-full",
        enterToClass : "translate-y-0",
        leaveFromClass : "translate-y-0",
        leaveToClass : "-translate-y-full",
      },
      children: [
        {
          path: 'login',
          name: 'login', 
          component: () => import('@/pages/auths/Auth.vue'),
          meta: {
            pageTitle: '<b>Halaman Masuk</b>',
          }
        },
        {
          path: 'users',
          alias: '',
          name: 'users', 
          component: () => import('@/pages/auths/PenggunaList.vue'),
          meta: {
            app:'user',
            requiresAuth: true,
            pageTitle: '<b>Pengaturan Pengguna</b>',
          }
        },
      ]
  },
  {
      path: siteUrl + 'p/',
      component: PublicLayout,
      meta: {
        enterFromClass : "translate-x-full",
        enterToClass : "translate-x-0",
        leaveFromClass : "translate-x-0",
        leaveToClass : "-translate-x-full",
      },
      children: [
        {
          path: 'register',
          name: 'register', 
          component: () => import('@/pages/auths/Register.vue'),
          meta: {
            pageTitle: '<b>Pendaftaran Akun Baru</b>',
          }
        },
      ],
    },
    {
        path: siteUrl + 'p/',
        component: MainLayout,
        meta: {
          enterFromClass : "translate-x-full",
          enterToClass : "translate-x-0",
          leaveFromClass : "translate-x-0",
          leaveToClass : "-translate-x-full",
        },
        children:[
          {
            path: 'account',
            name: 'account', 
            component: () => import('@/pages/auths/Account.vue'),
            meta: {
              requiresAuth: true,
              pageTitle: '<b>Pengaturan Account</b>',
            }
          },
          {
            path: 'unauthorized',
            alias: '',
            name: 'unauthorized', 
            component: () => import('@/pages/auths/Unauthorized.vue'),
            meta: {
            }
          },
        ]
      
    },
]

export default routes;
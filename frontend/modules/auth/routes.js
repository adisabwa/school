import config from '@/config/url'
const baseUrl = config.baseUrl
const siteUrl = config.siteUrl

let routes = [ 
  {
      path: siteUrl + 'p/',
      component: () => import('@/layouts/PublicLayout.vue'),
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
          component: () => import('./pages/Auth.vue'),
          meta: {
            pageTitle: '<b>Halaman Masuk</b>',
          }
        },
      ]
  },
  {
      path: siteUrl + 'p/',
      component: () => import('@/layouts/PublicLayout.vue'),
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
          props: true,
          component: () => import('./pages/Register.vue'),
          meta: {
            pageTitle: '<b>Pendaftaran Akun Baru</b>',
          }
        },
        {
          path: 'unauthorized',
          alias: '',
          name: 'unauthorized', 
          component: () => import('./pages/Unauthorized.vue'),
          meta: {
          }
        },
      ],
    },
    {
        path: siteUrl + 'p/',
        component: () => import('@/layouts/MainLayout.vue'),
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
            component: () => import('./pages/Account.vue'),
            meta: {
              requiresAuth: true,
              pageTitle: '<b>Pengaturan Account</b>',
            }
          },
        ]
      
    },
]

export default routes;
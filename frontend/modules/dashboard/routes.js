
import config from '@/config/url'
const baseUrl = config.baseUrl
const siteUrl = config.siteUrl

export default [
  {
    children: [
      {
        path: baseUrl,
        component: () => import('@/layouts/MainLayout.vue'),
        children: [
          {
            path: '',
            alias: ['index.php','index.php/p'],
            name: 'dashboard', 
            component: () => import('./pages/Dashboard.vue'),
            meta: {
              app:'admin',
              enterFromClass : "-translate-y-full opacity-0",
              enterToClass : "opacity-50",
              leaveFromClass : "opacity-50",
              leaveToClass : "translate-y-full opacity-0",
              pageTitle: '<b>Darul Arqom Patean Boarding School</b>',
            }
          }
        ]
      },
      {
        path: siteUrl + 'p/',
        component: () => import('@/layouts/PublicLayout.vue'),
        children: [
          {
            path: 'default',
            name: 'default', 
            component: () => import('./pages/Default.vue'),
            meta: {
              enterFromClass : "-translate-y-full opacity-0",
              enterToClass : "opacity-50",
              leaveFromClass : "opacity-50",
              leaveToClass : "translate-y-full opacity-0",
            }
          },
        ]
      },
    ],
  }
]
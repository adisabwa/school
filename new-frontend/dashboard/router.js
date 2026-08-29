// vue-apps/admin/router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import { setupGuards } from '@2/shared/router/guards'; // Import guard
import { baseUrl, siteUrl } from '@2/shared/config/url'; // Import URL config

let routes = [
  {
    children: [
      {
        path: baseUrl,
        meta:{
          template:'main',
        },
        children: [
          {
            path: '',
            alias: ['index.php','index.php/p'],
            name: 'dashboard', 
            component: () => import('@dashboard/pages/Dashboard.vue'),
            meta: {
              requiresAuth: true,
              app:'admin',
              enterFromClass : "-translate-y-full opacity-0",
              enterToClass : "opacity-50",
              leaveFromClass : "opacity-50",
              leaveToClass : "translate-y-full opacity-0",
              pageTitle: '<b>Darul Arqom Patean Boarding School</b>',
            }
          },
        ]
      },
      {
        path: siteUrl + 'p/',
        meta:{
          template:'public',
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
            component: () => import('@/pages/auths/Register.vue'),
            meta: {
              pageTitle: '<b>Pendaftaran Akun Baru</b>',
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
        ],
      },
      {
        path: siteUrl + 'p/',
        meta:{
          template:'public',
        },
        children: [
          {
            path: 'default',
            name: 'default', 
            component: () => import('@dashboard/pages/Default.vue'),
            meta: {
              enterFromClass : "-translate-y-full opacity-0",
              enterToClass : "opacity-50",
              leaveFromClass : "opacity-50",
              leaveToClass : "translate-y-full opacity-0",
            }
          },
          {
            path: 'login',
            name: 'login', 
            component: () => import('@/pages/auths/Auth.vue'),
            meta: {
              enterFromClass : "translate-y-full",
              enterToClass : "translate-y-0",
              leaveFromClass : "translate-y-0",
              leaveToClass : "-translate-y-full",
              pageTitle: '<b>Halaman Masuk</b>',
            }
          },
        ]
      },
    ],
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Pasang guard global ke router ini
setupGuards(router);

export default router;
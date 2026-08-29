
  
import { createRouter, createWebHistory } from 'vue-router';
import { setupGuards } from '@2/shared/router/guards'; // Import guard
import { siteUrl } from '@2/shared/config/url'

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
              meta: {
                template:'public',
                enterFromClass : "translate-x-full opacity-0",
                enterToClass : "opacity-50",
                leaveFromClass : "opacity-50",
                leaveToClass : "-translate-x-full opacity-0",
              },
              children: [
                {
                  path: '',
                  name: 'psb-start', 
                  component: () => import('@psb/pages/Start.vue'),
                  meta: {
                    pageTitle: '<b>Pendaftaran Santri Baru</b>',
                  }
                },
                {
                  path: 'create',
                  name: 'psb-create', 
                  component: () => import('@psb/pages/Create.vue'),
                  meta: {
                    pageTitle: '<b>Pendaftaran Santri Baru</b>',
                  }
                },
                {
                  path: 'view',
                  name: 'psb-view', 
                  component: () => import('@psb/pages/View.vue'),
                  props:true,
                  meta: {
                    pageTitle: '<b>Pendaftaran Santri Baru</b>',
                  }
                },
                {
                  path: 'finish',
                  name: 'psb-finish', 
                  component: () => import('@psb/pages/Finish.vue'),
                  meta: {
                    pageTitle: '<b>Pendaftaran Santri Baru</b>',
                  }
                },
                {
                  path: 'info',
                  name: 'psb-info', 
                  component: () => import('@psb/pages/Info.vue'),
                  meta: {
                    pageTitle: '<b>Pendaftaran Santri Baru</b>',
                  }
                }
              ],
          },
          {
              path: 'admin',
              meta: {
                template:'main',
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
                  component: () => import('@psb/pages/admin/Index.vue'),
                  meta: {
                    pageTitle: '<b>Daftar Calon Santri</b>',
                  }
                },
                {
                  path: 'dashboard',
                  name: 'psb-dashboard',
                  component: () => import('@psb/pages/Dashboard.vue'),
                },
              ]
          },
        ],
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

import config from '@/config/url'
const baseUrl = config.baseUrl
const siteUrl = config.siteUrl

export default [
  {
    path: siteUrl + 'p/lowongan',
    component: () => import('@/layouts/MainLayout.vue'),
    meta: {
      enterFromClass : "-translate-y-full opacity-0",
      enterToClass : "opacity-50",
      leaveFromClass : "opacity-50",
      leaveToClass : "translate-y-full opacity-0",
      app:'lowongan',
    },
    children: [
      {
        path: 'dashboard',
        name: 'lowongan-dashboard', 
        component: () => import('./pages/Dashboard.vue'),
      },
      {
        path: 'list',
        name: 'lowongan-list', 
        component: () => import('./pages/LowonganList.vue'),
      },
      {
        path: 'form',
        name: 'lowongan-form', 
        component: () => import('./pages/LowonganForm.vue'),
      },
      {
        path: 'detail',
        name: 'lowongan-detail', 
        component: () => import('./pages/LowonganDetail.vue'),
      },
    ]
  },
]

import config from '@/config/url'
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'ekstra',
    },
    children: [
      {
        path: siteUrl + 'p/ekstra/',
        children: [
            {
              path: 'tsdac',
              children: [
                  {
                    path: '',
                    meta: {
                        enterFromClass : "translate-x-full opacity-50",
                        enterToClass : "translate-x-0",
                        leaveFromClass : "translate-x-0",
                        leaveToClass : "translate-x-full opacity-50",
                    },
                    children: [
                      {
                        path: '',
                        component: () => import('@/layouts/PublicLayout.vue'),
                        children: [
                          {
                            path: '',
                            name: 'tsdac-dashboard',
                            component: () => import('./pages/tsdac/Dashboard.vue'),
                            meta: {
                            },
                          }, 
                        ],
                      },  
                      {
                        path: '',
                        component: () => import('@/layouts/BlankLayout.vue'),
                        children: [
                          {
                            path: 'nilai',
                            name: 'tsdac-nilai', 
                            component: () => import('./pages/tsdac/Nilai.vue'),
                            meta: {
                                pageTitle: "<b>Penilaian</b>",
                            }
                          }, 
                          {
                            path: 'setting',
                            name: 'tsdac-setting', 
                            component: () => import('./pages/tsdac/MatchSetting.vue'),
                            meta: {
                                pageTitle: "<b>Penilaian</b>",
                            }
                          }, 
                          {
                            path: 'sekretaris',
                            name: 'tsdac-sekretaris', 
                            component: () => import('./pages/tsdac/Sekretaris.vue'),
                            meta: {
                                pageTitle: "<b>Sekretaris</b>",
                            }
                          }, 
                        ],
                      },  
                    ]
                  }
              ],
            }
        ],
      },
    ],
  }
]

export default routes;
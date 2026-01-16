
  import { siteUrl } from '@/config/url'

  let routes = [
    {
      component: () => import('@/layouts/MainLayout.vue'),
      meta:{
        enterFromClass : "scale-y-0 opacity-50",
        enterToClass : "opacity-100",
        leaveFromClass : "opacity-100",
        leaveToClass : "scale-y-0 opacity-50",
        requiresAuth: true,
      },
      children: [
        {
          path: siteUrl + 'p/data',
          children: [
            {
              path:'',
              meta: {
                app:'data',
                allowedRoles: ['admin'],
              },
              children: [ 
                {
                  path: 'teacher',
                  name: 'teacher-list', 
                  component: () => import('@/pages/data/GuruList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Guru</b>",
                      allowedRoles: ['admin','wamar','guru','walas'],
                  }
                },
                {
                  path: 'santri',
                  name: 'santri-list', 
                  component: () => import('@/pages/data/SantriList.vue'),
                  meta: {
                      pageTitle: "<b>Data Santri</b>",
                      allowedRoles: ['admin','walas'],
                  }
                },
                {
                  path: 'pengguna',
                  name: 'user-management', 
                  component: () => import('@/pages/data/PenggunaList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Pengguna</b>",
                  }
                },
                {
                  path: 'unit',
                  name: 'unit-list', 
                  component: () => import('@/pages/data/UnitList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Unit</b>",
                  }
                },
                {
                  path: 'jurusan',
                  name: 'jurusan-list', 
                  component: () => import('@/pages/data/JurusanList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Jurusan</b>",
                  }
                },
                {
                  path: 'kelas',
                  name: 'kelas-list', 
                  component: () => import('@/pages/data/KelasList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Kelas</b>",
                  }
                },
                {
                  path: 'kamar',
                  name: 'kamar-list', 
                  component: () => import('@/pages/data/KamarList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Kamar</b>",
                  }
                },
              ],
            },
            {
              path: 'penghasilan',
              name: 'penghasilan-list', 
              component: () => import('@/pages/data/PenghasilanList.vue'),
              meta: {
                  pageTitle: "<b>Daftar Unit</b>",
                  allowedRoles: ['admin'],
              }
            },
            {
              path: 'semester',
              name: 'semester-list', 
              component: () => import('@/pages/data/SemesterList.vue'),
              meta: {
                  pageTitle: "<b>Daftar Semester</b>",
                  allowedRoles: ['admin'],
              }
            },
            {
              path: 'sesi',
              name: 'sesi-list', 
              component: () => import('@/pages/data/SesiList.vue'),
              meta: {
                  pageTitle: "<b>Daftar Sesi</b>",
                  allowedRoles: ['admin'],
              }
            },
          ],
        },
      ],
    }
  ]

  export default routes;
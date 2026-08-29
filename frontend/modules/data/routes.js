
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
                  component: () => import('./pages/GuruList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Guru</b>",
                      allowedRoles: ['admin','wamar','guru','walas'],
                  }
                },
                {
                  path: 'santri',
                  name: 'santri-list', 
                  component: () => import('./pages/SantriList.vue'),
                  meta: {
                      pageTitle: "<b>Data Santri</b>",
                      allowedRoles: ['admin','walas'],
                  }
                },
                {
                  path: 'pengguna',
                  name: 'user-management', 
                  component: () => import('./pages/PenggunaList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Pengguna</b>",
                  }
                },
                {
                  path: 'unit',
                  name: 'unit-list', 
                  component: () => import('./pages/UnitList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Unit</b>",
                  }
                },
                {
                  path: 'jurusan',
                  name: 'jurusan-list', 
                  component: () => import('./pages/JurusanList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Jurusan</b>",
                  }
                },
                {
                  path: 'kelas',
                  name: 'kelas-list', 
                  component: () => import('./pages/KelasList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Kelas</b>",
                  }
                },
                {
                  path: 'kelas-ajar',
                  name: 'kelas-ajar-list', 
                  component: () => import('./pages/KelasAjarList.vue'),
                  meta: {
                      pageTitle: "<b>Kelas / Tahun Ajaran</b>",
                  }
                },
                {
                  path: 'kamar',
                  name: 'kamar-list', 
                  component: () => import('./pages/KamarList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Kamar</b>",
                  }
                },
                {
                  path: 'jabatan',
                  name: 'jabatan-list', 
                  component: () => import('./pages/JabatanList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Jabatan</b>",
                  }
                },
                {
                  path: 'jabatan-guru',
                  name: 'jabatan-guru-list', 
                  component: () => import('./pages/PejabatList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Pejabatan</b>",
                  }
                },
              ],
            },
            {
              path: 'penghasilan',
              name: 'penghasilan-list', 
              component: () => import('./pages/PenghasilanList.vue'),
              meta: {
                  pageTitle: "<b>Daftar Unit</b>",
                  allowedRoles: ['admin'],
              }
            },
            {
              path: 'semester',
              name: 'semester-list', 
              component: () => import('./pages/SemesterList.vue'),
              meta: {
                  pageTitle: "<b>Daftar Semester</b>",
                  allowedRoles: ['admin'],
              }
            },
            {
              path: 'sesi',
              name: 'sesi-list', 
              component: () => import('./pages/SesiList.vue'),
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
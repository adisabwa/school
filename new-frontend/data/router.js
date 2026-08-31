
  
  import { createRouter, createWebHistory } from 'vue-router';
  import { setupGuards } from '@2/shared/router/guards'; // Import guard
  import { siteUrl } from '@2/shared/config/url'

  let routes = [
    {
      meta:{
        enterFromClass : "scale-y-0 opacity-50",
        enterToClass : "opacity-100",
        leaveFromClass : "opacity-100",
        leaveToClass : "scale-y-0 opacity-50",
        requiresAuth: true,
        app:'data',
      },
      children: [
        {
          path: siteUrl + 'p/data',
          children: [
            {
              path:'',
              meta: {
                allowedRoles: ['admin'],
              },
              children: [ 
                {
                  path: 'teacher',
                  name: 'teacher-list', 
                  component: () => import('@data/pages/GuruList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Guru</b>",
                      allowedRoles: ['admin','wamar','guru','walas'],
                  }
                },
                {
                  path: 'santri',
                  name: 'santri-list', 
                  component: () => import('@data/pages/SantriList.vue'),
                  meta: {
                      pageTitle: "<b>Data Santri</b>",
                      allowedRoles: ['admin','walas'],
                  }
                },
                {
                  path: 'pengguna',
                  name: 'user-management', 
                  component: () => import('@data/pages/PenggunaList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Pengguna</b>",
                  }
                },
                {
                  path: 'unit',
                  name: 'unit-list', 
                  component: () => import('@data/pages/UnitList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Unit</b>",
                  }
                },
                {
                  path: 'jurusan',
                  name: 'jurusan-list', 
                  component: () => import('@data/pages/JurusanList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Jurusan</b>",
                  }
                },
                {
                  path: 'kelas',
                  name: 'kelas-list', 
                  component: () => import('@data/pages/KelasList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Kelas</b>",
                  }
                },
                {
                  path: 'kamar',
                  name: 'kamar-list', 
                  component: () => import('@data/pages/KamarList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Kamar</b>",
                  }
                },
                {
                  path: 'jabatan',
                  name: 'jabatan-list', 
                  component: () => import('@data/pages/JabatanList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Jabatan</b>",
                  }
                },
                {
                  path: 'jabatan-guru',
                  name: 'jabatan-guru-list', 
                  component: () => import('@data/pages/PejabatList.vue'),
                  meta: {
                      pageTitle: "<b>Daftar Pejabatan</b>",
                  }
                },
              ],
            },
            {
              path: 'penghasilan',
              name: 'penghasilan-list', 
              component: () => import('@data/pages/PenghasilanList.vue'),
              meta: {
                  pageTitle: "<b>Daftar Unit</b>",
                  allowedRoles: ['admin'],
              }
            },
            {
              path: 'semester',
              name: 'semester-list', 
              component: () => import('@data/pages/SemesterList.vue'),
              meta: {
                  pageTitle: "<b>Daftar Semester</b>",
                  allowedRoles: ['admin'],
              }
            },
            {
              path: 'sesi',
              name: 'sesi-list', 
              component: () => import('@data/pages/SesiList.vue'),
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

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Pasang guard global ke router ini
setupGuards(router);

export default router;
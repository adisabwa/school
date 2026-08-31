// vue-apps/shared/routes/manifest.js
export const APP_ROUTES = {
  DASHBOARD: {
    DASHBOARD: { name: 'dashboard', path: ''},
    DEFAULT: { name: 'default', path: 'default'},
    LOGIN: { name: 'login', path: 'login'},
    REGISTER: { name: 'register', path: 'register'},
    UNAUTHORIZED: { name: 'unauthorized', path: 'unauthorized'},
  },
  DATA:{
    PENGGUNA: { name: 'user-management', path: 'data/pengguna'},
    GURU: { name: 'teacher-list', path: 'data/guru'},
    SANTRI: { name: 'santri-list', path: 'data/santri'},
    JURUSAN: { name: 'jurusan-list', path: 'data/jurusan'},
    KELAS: { name: 'kelas-list', path: 'data/kelas'},
    KAMAR: { name: 'kamar-list', path: 'data/kamar'},
    JABATAN: { name: 'jabatan-list', path: 'data/jabatan'},
    JABATAN_GURU: { name: 'jabatan-guru-list', path: 'data/jabatan-guru'},
    UNIT: { name: 'unit-list', path: 'data/unit'},
    PENGHASILAN: { name: 'penghasilan-list', path: 'data/penghasilan'},
    SEMESTER: { name: 'semester-list', path: 'data/semester'},
    SESI: { name: 'sesi-list', path: 'data/sesi'},
    PENGHASILAN: { name: 'penghasilan-list', path: 'data/penghasilan'},
  },
  PSB:{
    START: { name: 'psb-start', path: 'psb/start'},
    CREATE: { name: 'psb-create', path: 'psb/create'},
    VIEW: { name: 'psb-view', path: 'psb/view'},  
    FINISH: { name: 'psb-finish', path: 'psb/finish'},
    INFO: { name: 'psb-info', path: 'psb/info'},
    ADMIN:{
      DASHBOARD: { name: 'psb-dashboard', path: 'psb/admin/dashboard'},
      INDEX: { name: 'admin-psb', path: 'psb/admin'},
    },
  }
};
let admin = [
  {
    index:'dashboard',
    route:'dashboard',
    function:'',
    icon:'mdi:home',
    label:'Beranda',
  },
  {
    index:'data-list',
    type:'submenu',
    function:'',
    icon:'bxs:data',
    label:'Pengolahan Data',
    roles:['admin','super-admin','admin-bidang'],
    children:[
      {
        index:'unit-list',
        route:'unit-list',
        function:'',
        icon:'ph:building-office-fill',
        label:'Data Unit Kerja',
        roles:['super-admin','admin-bidang'],
      },
      {
        index:'pengguna-list',
        route:'pengguna-list',
        function:'',
        icon:'garden:user-list-fill-16',
        label:'Data Pengguna',
      },
    ]
  },
  {
    index:'group-admin',
    route:'group-admin',
    function:'',
    icon:'mingcute:group-3-fill',
    label:'Data Kelompok',
  },
  {
    index:'account',
    route:'account',
    function:'',
    icon:'mdi:account',
    label:'Data Profil',
  },
  // {
  //   route:'',
  //   function:'doLogout',
  //   icon:'mdi:logout',
  //   label:'Keluar',
  // },
]

export default admin
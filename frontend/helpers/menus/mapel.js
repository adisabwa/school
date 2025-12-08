let mapel = [
  {
    index: 'admin-mapel',
    route: 'admin-mapel',
    icon:'ph:notebook-fill',
    label:'Data Mata Pelajaran',
    type:'menu',
    roles:['admin'],
  },
  {
    index: 'pembagian-mapel',
    route: 'pembagian-mapel',
    icon:'ic:baseline-data-thresholding',
    label:'Pembagian Pengampu',
    type:'menu',
    roles:['admin'],
  },
  {
    index: 'penjadwalan-mapel',
    route: 'penjadwalan-mapel',
    icon:'dashicons:schedule',
    label:'Jadwal Pelajaran',
    type:'menu',
    roles:['admin'],
  },
  {
    index: 'dashboard-nilai',
    route: 'dashboard-nilai',
    icon:'material-symbols:dashboard',
    label:'Dashboard Nilai',
    type:'menu',
    roles:['admin','walas','guru'],
  },
  {
    index: 'nilai-mapel',
    route: 'nilai-mapel',
    icon:'streamline-ultimate:paper-write-bold',
    label:'Nilai Santri',
    type:'menu',
  },
]
export default mapel
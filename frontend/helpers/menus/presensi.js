let saving = [
  {
    index: 'presensi-dashboard',
    route: 'presensi-dashboard',
    icon:'material-symbols:dashboard',
    label:'Dashboard',
    type:'menu',
  },
  {
    index: 'presensi-izin',
    route: 'presensi-izin',
    icon:'material-symbols:dashboard',
    label:'Dashboard',
    type:'menu',
  },
  {
    index: 'presensi-report',
    route: 'presensi-report',
    icon:'carbon:report',
    label:'Laporan',
    type:'menu',
    roles: ['guru'],
  },
  {
    index: 'presensi-report-walas',
    route: 'presensi-report-walas',
    icon:'carbon:report',
    label:'Laporan',
    type:'menu',
    roles: ['walas'],
  },
]
export default saving
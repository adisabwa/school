let rapor = [
  {
    index: 'rapor-dashboard',
    route: 'rapor-dashboard',
    icon:'material-symbols:dashboard',
    label:'Dashboard Nilai',
    type:'menu',
    roles:['admin','walas','guru'],
  },
  {
    index: 'teacher-check-raport',
    route: 'teacher-check-raport',
    icon:'mdi:teacher',
    label:'Verifikasi Wali Kelas',
    type:'menu',
  },
  {
    index:'rekap-santri',
    route: 'rekap-santri',
    label:'Kelengkapan Data Santri',
    type:'menu',
    icon:'mdi:account-box-outline',
    roles:['admin','walas'],
  },
  {
    index:'rekap-nilai-uts',
    route: 'rekap-nilai-uts',
    label:'Raport MID',
    type:'menu',
    icon:'icon-park-solid:table-report',
    roles:['admin','walas'],
  },
  {
    index:'nilai-akhir',
    label:'Raport Akhir',
    type:'submenu',
    icon:'icon-park-solid:table-report',
    roles:['admin','walas'],
    children: [
      {
        icon:'solar:list-check-bold',
        index:'rekap-nilai-akhir',
        route: 'rekap-nilai-akhir',
        label:'Data Nilai Akhir',
      },
      {
        icon:'solar:list-check-bold',
        index:'rekap-nilai-pengasuhan',
        route: 'rekap-nilai-pengasuhan',
        label:'Data Nilai Pengasuhan',
      },
      {
        icon:'mdi:note-check-outline',
        index:'catatan-nilai-akhir',
        route: 'catatan-nilai-akhir',
        label:'Catatan',
      },
      {
        icon:'carbon:document-download',
        index:'download-nilai-akhir',
        route: 'download-nilai-akhir',
        label:'Unduh Rapor',
      },
    ]
  },
]
export default rapor
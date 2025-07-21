let saving = [
  {
    index:'data',
    icon:'fluent:clipboard-data-bar-24-filled',
    label:'Pengolahan Data',
    type:'submenu',
    children: [
      {
        index:'santri-list',
        route: 'santri-list',
        label:'Data Santri',
      },
    ]
  },
  {
    index: 'admin-saving',
    route: 'admin-saving',
    icon:'ph:notebook-fill',
    label:'Tabungan Santri',
    type:'menu',
  },
  {
    index: 'rekap-saving',
    route: 'rekap-saving',
    icon:'ic:baseline-data-thresholding',
    label:'Rekapitulasi Keuangan',
    type:'menu',
  },
]
export default saving
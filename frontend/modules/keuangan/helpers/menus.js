let saving = [
  {
    index: 'keuangan-dashboard',
    route: 'keuangan-dashboard',
    icon:'material-symbols:dashboard',
    label:'Dashboard',
    type:'menu',
    roles: ['admin'],
  },
  {
    index:'keuangan-iuran-tagihan',
    route: 'keuangan-iuran-tagihan',
    label:'Iuran Santri',
    type:'menu',
    icon:'hugeicons:payment-01',
    roles:['admin'],
  },
  // {
  //   index:'keuangan-iuran',
  //   label:'Iuran Santri',
  //   type:'submenu',
  //   icon:'hugeicons:payment-01',
  //   roles:['admin'],
  //   children: [
  //     {
  //       icon:'hugeicons:invoice-03',
  //       index:'keuangan-iuran-tagihan',
  //       route: 'keuangan-iuran-tagihan',
  //       label:'Tagihan Santri',
  //     },
  //     {
  //       icon:'icon-park-twotone:bank-transfer',
  //       index:'keuangan-iuran-saldo',
  //       route: 'keuangan-iuran-saldo',
  //       label:'Saldo Pembayaran',
  //     },
  //   ],
  // },
  {
    icon:'fluent:receipt-money-24-filled',
    index:'keuangan-transaksi',
    route: 'keuangan-transaksi',
    label:'Transaksi Umum',
    roles: ['admin'],
  },
  {
    index:'keuangan-laporan',
    label:'Laporan',
    type:'menu',
    route:'keuangan-laporan',
    icon:'icon-park-solid:table-report',
    roles:['admin'],
  },
  {
    index:'data-kas',
    label:'Master Data',
    type:'submenu',
    icon:'icon-park-solid:table-report',
    roles:['admin'],
    children: [
      {
        icon:'streamline-sharp:safe-vault-solid',
        index:'keuangan-data-kas',
        route: 'keuangan-data-kas',
        label:'Daftar Kas',
      },
      {
        icon:'hugeicons:payment-02',
        index:'keuangan-data-metode',
        route: 'keuangan-data-metode',
        label:'Metode Pembayaran',
      },
      {
        icon:'picon:atm',
        index:'keuangan-data-pos',
        route: 'keuangan-data-pos',
        label:'Pos Anggaran',
      },
      {
        icon:'material-symbols:category-search-rounded',
        index:'keuangan-data-kategori',
        route: 'keuangan-data-kategori',
        label:'Kategori Transaksi',
      },
      {
        icon:'ph:user-list-bold',
        index:'keuangan-data-iuran',
        route: 'keuangan-data-iuran',
        label:'Iuran Santri',
      },
    ]
  },
]
export default saving
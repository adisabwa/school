let psb = [
  {
    index: 'dashboard',
    route: 'PSB.ADMIN.DASHBOARD',
    icon:'mdi:view-dashboard',
    label:'Dashboard',
    type:'menu',
  },
  {
    index:'data',
    icon:'fluent:clipboard-data-bar-24-filled',
    label:'Pengolahan Data',
    type:'submenu',
    children: [
      {
        index:'penghasilan-list',
        route: 'DATA.PENGHASILAN',
        label:'Data Penghasilan',
      },
    ]
  },
  {
    index: 'admin-psb',
    route: 'PSB.ADMIN.INDEX',
    icon:'ph:notebook-fill',
    label:'Data Calon Santri',
    type:'menu',
  },
]
export default psb
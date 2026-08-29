let psb = [
  {
    index: 'dashboard',
    route: 'psb-dashboard',
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
        route: 'penghasilan-list',
        label:'Data Penghasilan',
      },
    ]
  },
  {
    index: 'admin-psb',
    route: 'admin-psb',
    icon:'ph:notebook-fill',
    label:'Data Calon Santri',
    type:'menu',
  },
]
export default psb
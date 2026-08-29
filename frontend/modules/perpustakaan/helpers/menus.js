let perpustakaan = [
  {
    index: 'perpustakaan-dashboard',
    route: 'perpustakaan-dashboard',
    icon:'material-symbols:dashboard',
    label:'Dashboard',
    type:'menu',
  },
  {
    index:'buku-list',
    route: 'buku-list',
    label:'Data Buku',
    type:'menu',
    icon:'mdi:book',
    roles:['admin'],
  },
  {
    index:'peminjaman-list',
    route: 'peminjaman-list',
    label:'Peminjaman Buku',
    type:'menu',
    icon:'ph:note-fill',
    roles:['admin'],
  },
]
export default perpustakaan
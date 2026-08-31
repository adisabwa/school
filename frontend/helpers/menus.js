
import { baseUrl } from "@/config/url";

let topMenu = [
  {
    app:'profil',
    label:"Profil",
    route:{
      ortu:'profil-anak-ortu',
    },
    color:'amber-100',
    darkColor:'amber-500',
    textColor:'sky-700',
    image:baseUrl + 'assets/images/icons/profil.png',
  },
  {
    app:'presensi',
    label:"Presensi",
    route:{
      guru:'presensi-dashboard',
      walas:'presensi-dashboard',
      admin:'presensi-admin-dashboard',
    },
    color:'orange-100',
    darkColor:'orange-500',
    textColor:'cyan-700',
    image:baseUrl + 'assets/images/icons/attendance.png',
    defaultRole:'guru',
  },
  {
    app:'mapel',
    label:"Pembelajaran",
    route:{
      admin:'pembagian-mapel',
      guru:'mapel-dashboard-guru',
    },
    color:'emerald-100',
    darkColor:'emerald-500',
    textColor:'amber-700',
    image:baseUrl + 'assets/images/icons/subject.png',
    defaultRole:'guru',
  },
  {
    app:'nilai',
    label:"Penilaian",
    route:{
      all:'dashboard-nilai',
    },
    color:'red-100',
    darkColor:'red-500',
    textColor:'indigo-700',
    image:baseUrl + 'assets/images/icons/score.png',
  },
  {
    app:'pengasuhan',
    label:"Pengasuhan",
    route:{
      wamar:'dashboard-nilai-pengasuhan',
      admin:'dashboard-nilai-pengasuhan',
    },
    color:'sky-100',
    darkColor:'sky-500',
    textColor:'emerald-700',
    image:baseUrl + 'assets/images/icons/pengasuhan.png',
  },
  {
    app:'rapor',
    label:"Rapor Santri",
    route:{
      walas:'rapor-dashboard',
      admin:'rapor-dashboard',
    },
    color:'red-100',
    darkColor:'red-500',
    textColor:'indigo-700',
    image:baseUrl + 'assets/images/icons/report.png',
  },
  {
    app:'kmi',
    label:"KMI",
    route:{
      admin:'kmi-dashboard',
    },
    color:'green-100',
    darkColor:'green-500',
    textColor:'orange-700',
    image:baseUrl + 'assets/images/icons/online-learning.png',
  },
  {
    app:'keuangan',
    label:"Keuangan",
    route:{
      admin:'keuangan-dashboard',
    },
    color:'amber-100',
    darkColor:'amber-500',
    textColor:'sky-700',
    image:baseUrl + 'assets/images/icons/finance.png',
  },
  {
    app:'perpustakaan',
    label:"Perpustakaan",
    route:{
      admin:'perpustakaan-dashboard',
      guru:'perpustakaan-dashboard',
    },
    color:'rose-100',
    darkColor:'rose-500',
    textColor:'purple-700',
    image:baseUrl + 'assets/images/icons/library.png',
  },
  {
    app:'saving',
    label:"Tabungan Santri",
    route:{
      admin:'admin-saving',
    },
    color:'indigo-100',
    darkColor:'indigo-500',
    textColor:'cyan-700',
    image:baseUrl + 'assets/images/icons/saving.png',
  },
  {
    app:'bkk',
    label:"Kontak BKK",
    link:'https://wa.me/628562955558',
    color:'orange-100',
    darkColor:'orange-500',
    textColor:'cyan-700',
    image:baseUrl + 'assets/images/icons/bkk.png',
    guest:true,
  },
  // {
  //   app:'lowongan',
  //   label:"Lowongan Kerja",
  //   route:{
  //     all:'lowongan-list',
  //   },
  //   color:'emerald-100',
  //   darkColor:'emerald-500',
  //   textColor:'amber-700',
  //   image:baseUrl + 'assets/images/icons/employment.png',
  //   guest:true,
  // },
  {
    app:'psb',
    label:"PPDB",
    route:{
      admin:'admin-psb',
    },
    color:'sky-100',
    darkColor:'sky-500',
    textColor:'[var(--color-main-700)]',
    image:baseUrl + 'assets/images/icons/psb.png',
  },
  {
    app:'data',
    label:"Manajemen Data",
    route:{
      admin:'user-management',
      walas:'santri-list',
      guru:'teacher-list',
      wamar:'teacher-list',
    },
    color:'rose-100',
    darkColor:'rose-500',
    textColor:'fuchsia-700',
    image:baseUrl + 'assets/images/icons/data.png',
  },
]


const school = import.meta.env.VITE_SCHOOL
const {
  listApps
} = await import(`@/config/schools/${school}.js`)

topMenu = topMenu.filter(menu => {
  if (menu.app && listApps && !listApps.includes(menu.app)) {
    return false;
  }
  return true;
});

export { topMenu }
export default {
  topMenu
}
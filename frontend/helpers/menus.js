
import { baseUrl } from "@/config/url";

let topMenu = [
  {
    app:'mapel',
    label:"Pembelajaran & Penilaian",
    route:{
      admin:'pembagian-mapel',
      walas:'dashboard-nilai',
      guru:'dashboard-nilai',
    },
    color:'emerald-100',
    darkColor:'emerald-500',
    textColor:'amber-700',
    image:baseUrl + 'assets/images/icons/subject.png',
  },
  {
    app:'presensi',
    label:"Presensi Guru",
    route:{
      admin:'kelas-presensi',
    },
    color:'orange-100',
    darkColor:'orange-500',
    textColor:'cyan-700',
    image:baseUrl + 'assets/images/icons/attendance.png',
  },
  {
    app:'kmi',
    label:"KMI",
    route:{
      admin:'admin-kmi',
    },
    color:'green-100',
    darkColor:'green-500',
    textColor:'orange-700',
    image:baseUrl + 'assets/images/icons/online-learning.png',
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
    app:'psb',
    label:"PPDB",
    route:{
      admin:'admin-psb',
    },
    color:'sky-100',
    darkColor:'sky-500',
    textColor:'teal-700',
    image:baseUrl + 'assets/images/icons/psb.png',
  },
  {
    app:'data',
    label:"Manajemen Data",
    route:{
      admin:'user-management',
      walas:'santri-list',
    },
    color:'rose-100',
    darkColor:'rose-500',
    textColor:'fuchsia-700',
    image:baseUrl + 'assets/images/icons/data.png',
  },
  // sholatWajib: {
  //   url:'sholat/wajib',
  //   label:"Sholat Wajib",
  //   route:'sholat-wajib',
  //   color:'purple-100',
  //   darkColor:'purple-500',
  //   textColor:'orange-700',
  //   image:baseUrl + 'assets/images/icons/mosque.png',
  // },
  // sholatSunnah: {
  //   url:'sholat/sunnah',
  //   label:"Sholat Sunnah",
  //   route:'sholat-sunnah',
  //   color:'rose-100',
  //   darkColor:'rose-500',
  //   textColor:'orange-700',
  //   image:baseUrl + 'assets/images/icons/prayer-rug.png',
  // },
  // infaqShadaqah: {
  //   url:'infaq/shadaqah',
  //   label:"Infaq / Shadaqah",
  //   route:'shadaqah',
  //   color:'teal-100',
  //   darkColor:'teal-500',
  //   textColor:'orange-700',
  //   image:baseUrl + 'assets/images/icons/infaq.png',
  // },
]

let organizationMenu = {
  group: {
    url:'group',
    label:"Data Kelompok",
    route:'group-admin',
    color:'orange-100',
    darkColor:'orange-500',
    textColor:'orange-700',
    image:baseUrl + 'assets/images/icons/group.png',
  },
  kajian: {
    url:'kajian',
    label:"Kajian / Halaqah",
    route:'kajian',
    color:'cyan-100',
    darkColor:'cyan-500',
    textColor:'orange-700',
    image:baseUrl + 'assets/images/icons/mimbar.png',
  },
  persyarikatan: {
    url:'persyarikatan',
    label:"Kegiatan Persyarikatan",
    route:'persyarikatan',
    color:'indigo-100',
    darkColor:'indigo-500',
    textColor:'orange-700',
    image:baseUrl + 'assets/images/icons/meeting.png',
  },
}

let facilityMenu = {
  bacaanSholat: {
    url:'facility/sholat',
    label:"Bacaan Sholat",
    route:'facility-sholat',
    color:'fuchsia-100',
    darkColor:'fuchsia-500',
    textColor:'orange-700',
    image:baseUrl + 'assets/images/icons/praying.png',
  },
  bacaanDzikirSholat: {
    url:'facility/dzikir-sholat',
    label:"Dzikir Setelah Sholat",
    route:'facility-dzikir-sholat',
    color:'emerald-100',
    darkColor:'emerald-500',
    textColor:'orange-700',
    image:baseUrl + 'assets/images/icons/after-praying.png',
  },
  bacaanDoa: {
    url:'facility/doa',
    label:"Doa Harian",
    route:'facility-doa',
    color:'amber-100',
    darkColor:'amber-500',
    textColor:'orange-700',
    image:baseUrl + 'assets/images/icons/pray.png',
  },
}

let adminMenu = {
  account: {
    url:'data/pengguna',
    label:"Daftar Pengguna",
    route:'account-list',
    color:'emerald-100',
    darkColor:'emerald-500',
    textColor:'orange-700',
    image:baseUrl + 'assets/images/icons/account.png',
  },
}

export { adminMenu }
export { facilityMenu }
export { topMenu }
export { organizationMenu }
export default {
  topMenu,
  adminMenu,
  facilityMenu,
  organizationMenu,
}
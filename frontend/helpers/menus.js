
import { baseUrl } from "@/config/url";

let topMenu = {
  quranBaca: {
    url:'quran/baca',
    label:"Baca Qur'an",
    route:'quran-baca',
    color:'bg-lime-200',
    shadowColor:'shadow-lime-600',
    textColor:'text-orange-500',
    image:baseUrl + 'assets/images/icons/quran.png',
  },
  quranHafal: {
    url:'quran/hafal',
    label:"Hafal Qur'an",
    route:'quran-hafal',
    color:'bg-yellow-200',
    shadowColor:'shadow-yellow-600',
    textColor:'text-orange-500',
    image:baseUrl + 'assets/images/icons/memorization.png',
  },
  quranTarjamah: {
    url:'quran/tarjamah',
    label:"Tarjamahan Qur'an",
    route:'quran-tarjamah',
    color:'bg-sky-200',
    shadowColor:'shadow-sky-600',
    textColor:'text-orange-500',
    image:baseUrl + 'assets/images/icons/kajian.png',
  },
  sholatWajib: {
    url:'sholat/wajib',
    label:"Sholat Wajib",
    route:'sholat-wajib',
    color:'bg-purple-200',
    shadowColor:'shadow-purple-600',
    textColor:'text-orange-500',
    image:baseUrl + 'assets/images/icons/mosque.png',
  },
  sholatSunnah: {
    url:'sholat/sunnah',
    label:"Sholat Sunnah",
    route:'sholat-sunnah',
    color:'bg-rose-200',
    shadowColor:'shadow-rose-600',
    textColor:'text-orange-500',
    image:baseUrl + 'assets/images/icons/prayer-rug.png',
  },
  infaqShadaqah: {
    url:'infaq/shadaqah',
    label:"Infaq / Shadaqah",
    route:'shadaqah',
    color:'bg-teal-200',
    shadowColor:'shadow-teal-600',
    textColor:'text-orange-500',
    image:baseUrl + 'assets/images/icons/infaq.png',
  },
}

let organizationMenu = {
  group: {
    url:'group',
    label:"Data Kelompok",
    route:'group-admin',
    color:'bg-orange-200',
    shadowColor:'shadow-orange-600',
    textColor:'text-orange-500',
    image:baseUrl + 'assets/images/icons/group.png',
  },
  kajian: {
    url:'kajian',
    label:"Kajian / Halaqah",
    route:'kajian',
    color:'bg-cyan-200',
    shadowColor:'shadow-cyan-600',
    textColor:'text-orange-500',
    image:baseUrl + 'assets/images/icons/mimbar.png',
  },
  persyarikatan: {
    url:'persyarikatan',
    label:"Kegiatan Persyarikatan",
    route:'persyarikatan',
    color:'bg-indigo-200',
    shadowColor:'shadow-indigo-600',
    textColor:'text-orange-500',
    image:baseUrl + 'assets/images/icons/meeting.png',
  },
}

let facilityMenu = {
  bacaanSholat: {
    url:'facility/sholat',
    label:"Bacaan Sholat",
    route:'facility-sholat',
    color:'bg-fuchsia-200',
    shadowColor:'shadow-fuchsia-600',
    textColor:'text-orange-500',
    image:baseUrl + 'assets/images/icons/praying.png',
  },
  bacaanDzikirSholat: {
    url:'facility/dzikir-sholat',
    label:"Dzikir Setelah Sholat",
    route:'facility-dzikir-sholat',
    color:'bg-emerald-200',
    shadowColor:'shadow-emerald-600',
    textColor:'text-orange-500',
    image:baseUrl + 'assets/images/icons/after-praying.png',
  },
  bacaanDoa: {
    url:'facility/doa',
    label:"Doa Harian",
    route:'facility-doa',
    color:'bg-amber-200',
    shadowColor:'shadow-amber-600',
    textColor:'text-orange-500',
    image:baseUrl + 'assets/images/icons/pray.png',
  },
}

let adminMenu = {
  account: {
    url:'data/pengguna',
    label:"Daftar Pengguna",
    route:'account-list',
    color:'bg-emerald-200',
    shadowColor:'shadow-emerald-600',
    textColor:'text-orange-500',
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
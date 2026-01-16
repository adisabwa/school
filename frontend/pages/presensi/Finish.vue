<template>
  <div class=" bg-white/[0.8] px-2 md:px-10 h-full text-center py-6 lg:py-10 animate-in zoom-in-95 duration-700 max-w-md mx-auto">
    <div class="w-16 h-16 lg:w-24 lg:h-24 bg-emerald-100 text-emerald-600 rounded-2xl lg:rounded-[2rem] flex items-center justify-center mx-auto mb-5 lg:mb-6 shadow-inner ring-[6px] lg:ring-[8px] ring-emerald-50">
      <icons icon="material-symbols:check-circle-outline" class="text-emerald-700 text-[40px] lg:text-[56px]" />
    </div>

    <h2 class="text-xl lg:text-3xl font-black text-slate-900 mb-3 lg:mb-4 leading-tight">Alhamdulillah!<br />Laporan Terkirim</h2>

    <p class="text-slate-500 mb-6 lg:mb-8 text-sm lg:text-base leading-relaxed">Sistem telah mencatat kehadiran hari ini. Semoga berkah untuk kita semua.</p>

    <el-button class="w-full bg-emerald-600 text-white py-3 lg:py-4 rounded-xl lg:rounded-2xl font-black text-sm lg:text-base shadow-md hover:bg-emerald-500 transition-all h-fit"
        @click="$router.replace({name:'presensi-dashboard'})">Kembali ke Dashboard</el-button>
  </div>
</template>

<template>
  <div class="space-y-3 py-12 text-center bg-white/[0.7] px-2 md:px-5" >
    <!-- Camera Mock -->
    <div
      class="max-w-[600px] min-w-[300px] mx-auto relative flex flex-col items-center justify-center rounded-[2rem] overflow-hidden"
    >
      <QRScanner @detected="detectedCode" 
        class="aspect-square "/>

      <!-- Scanner Frame -->
        <div
          class="absolute left-0 right-0 h-0.5 bg-emerald-400 shadow-[0_0_15px_rgba(52,211,153,0.8)] animate-scan"
        ></div>

        <div class="absolute z-[4] w-2/3 h-2/3 rounded-[2rem] border-[300px] border-solid border-slate-950/[0.4]" />
        <!-- Hint -->
        <div class="absolute z-10 bottom-7 mt-8 flex flex-col items-center text-white">
          <div class="text-sm font-medium mb-1 opacity-80">
            Arahkan kamera ke QR Code Kelas
          </div>
          <div class="text-xs opacity-60">Pastikan pencahayaan cukup</div>
        </div>
    </div>

    <!-- Divider -->
    <div class="relative py-3 text-center">
      <div class="absolute inset-0 flex items-center">
        <div class="w-full border-t border-slate-200"></div>
      </div>
      <span
        class="relative bg-slate-50 px-4 text-xs font-bold text-slate-400 uppercase tracking-widest"
      >
        Atau Masukkan Manual
      </span>
    </div>

    <!-- Manual Input -->
    <div
      class="bg-white max-w-[600px] mx-auto p-4 rounded-2xl border border-slate-100 flex gap-3 shadow-sm
      items-center justify-center"
    >
      <el-input
        size="large"
        v-model="manualCode"
        @input="manualCode = manualCode.toUpperCase()"
        placeholder="CONTOH: 7A-2024"
        class="flex-1 bg-slate-50 border-0 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 placeholder:text-slate-300 focus:ring-2 focus:ring-emerald-500 transition-all outline-none"
      />
      <el-button
        @click="submitManual"
        class="bg-emerald-600 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-md hover:bg-emerald-700 transition-all h-full"
      >
        Masuk
      </el-button>
    </div>
  </div>
</template>

<script>
import QRScanner from '@/components/QRScanner.vue';
import { list } from 'postcss';
export default {
  name: 'Scanner',
  components:{
    QRScanner,
  },
  props: {
  },

  data() {
    return {
      code:'',
      manualCode: ''
    }
  },

  methods: {
    submitManual() {
      if (!this.manualCode) return
      this.onScanSuccess(this.manualCode)
    },

    detectedCode(code){
      this.onScanSuccess(code)
    },
    onScanSuccess(code){
      let codes = code.split('-')
      if (codes[0] == 'SI/KMI')
        this.$router.replace({name:'presensi-form', query:{id_kelas:codes[1]}})
      else
        this.$alert('Kode yang Anda Masukkan Keliru', 'Error', {
          // if you want to disable its autofocus
          // autofocus: false,
          type:'error',
          confirmButtonText: 'OK',
        })
    }
  }
}
</script>

<style>
@keyframes scan {
  0%, 100% {
    top: 0;
  }
  50% {
    top: 100%;
  }
}

.animate-scan {
  animation: scan 3s ease-in-out infinite;
}
</style>

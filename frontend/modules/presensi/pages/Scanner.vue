<template>
  <div class="space-y-1 py-12 text-center bg-white/[0.7] px-2 md:px-5" >
    <!-- Camera Mock -->
    <template v-if="!isOutside">
      <div 
        class="max-w-[600px] min-w-[300px] mx-auto relative flex flex-col items-center justify-center rounded-[2rem] overflow-hidden"
      >
        <QRScanner @detected="detectedCode" 
          class="aspect-square "/>
            <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2
              text-center  font-bold text-white z-[9999] space-y-2"
              v-if="isScanned">
              <div class="border-0 border-b border-solid border-white px-3 pb-1">Scan berhasil!</div>
              <div class="text-sm fles items-center">
                <icons icon="mdi:loading" class="text-sm animate-spin"/>
                {{ checkLocation ? 'Cek Lokasi' : 'Loading Data Kelas' }}
              </div>
            </div>
        <!-- Scanner Frame -->
          <div
            class="absolute left-0 right-0 h-0.5 bg-[var(--color-main-400)] shadow-[0_0_15px_rgba(52,211,153,0.8)] animate-scan"
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
          placeholder="Masukan Kode QR Code"
          class="flex-1 bg-slate-50 border-0 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 placeholder:text-slate-300 focus:ring-2 focus:ring-[var(--color-main-500)] transition-all outline-none"
        />
        <el-button
          @click="submitManual"
          class="bg-[var(--color-main-600)] text-white px-6 py-3 rounded-xl font-bold text-sm shadow-md hover:bg-[var(--color-main-700)] transition-all h-full"
        >
          Masuk
        </el-button>
      </div>
    </template>
    <div class="text-center mb-3" v-else>
      <el-button class="bg-[var(--color-main-700)] text-white"
        @click="isOutside = false; isScanned = false">
        Scan Ulang
      </el-button>
      <el-button class="bg-[var(--color-main-700)] text-white"
        @click="isOutside = false; isScanned = false">
        Upload Bukti
      </el-button>
    </div>

    <div>
      <Camera />
      <div v-if="isOutside" class="mx-auto font-bold text-center w-full">- Anda melakukan presensi dari Luar Lokasi Sekolah -</div>
      <div v-if="isOutside" class="mx-auto text-center w-full text-sm">Pastikan anda melakukan absen dari lokasi sekolah / Nyalakan deteksi lokasi pada HP Anda</div>
      <div ref="mapContainer" class="map-view max-w-[500px] h-[400px] shrink-0 mx-auto mt-2"></div>
    </div>
  </div>
</template>

<script>
import QRScanner from '@/components/QRScanner.vue';
import Camera from '@/components/Camera.vue';
import { th } from 'element-plus/es/locale/index.mjs';

import { list } from 'postcss';
export default {
  name: 'Scanner',
  components:{
    QRScanner,
    Camera,
  },
  props: {
  },

  setup(){
    const { userCoords, loading, error, getCurrentLocation, isInsidePolygon, isWithinRadius, openInGoogleMaps, renderMap,  } = useGeofence()
    return {
      userCoords, loading, error, getCurrentLocation, isInsidePolygon, isWithinRadius, openInGoogleMaps, renderMap, 
    }
  },
  data() {
    return {
      code:'',
      manualCode: '',
      isScanned: false,
      checkLocation:false,
      isOutside: true,
      polygonCoord: this.$coordinate,
      position:[],
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
      this.isScanned = true
      this.code = code
      let codes = code.split('-')
      if (codes[0] == 'SI/KMI') {
        this.checkLocationAndRenderMap()
        
      } else
        this.$alert('Kode yang Anda Masukkan Keliru', 'Error', {
          // if you want to disable its autofocus
          // autofocus: false,
          type:'error',
          confirmButtonText: 'OK',
        })
    },
    async checkLocationAndRenderMap() {
      this.checkLocation = true
      // console.log(this.polygonCoord)
      // return
      try {
        const coords = await this.getCurrentLocation();
        this.userCoords = coords;

        // Cek apakah di luar area
        let inside = false
        // ✅ Gunakan for...of loop alih-alih .forEach
        for (const coordPlace of this.polygonCoord) {
          console.log('coord', coordPlace);
          
          // Sekarang await valid digunakan di sini
          const position = await this.isInsidePolygon(coords, coordPlace);
              
          console.log(position, !position);
          this.position.push(position);
          inside = inside || position;
        }
        this.isOutside = !inside
        
        // return
        // Set isOutside menjadi true jika di luar area
        this.checkLocation = false
        // this.isOutside =

        if (this.isOutside) {
          // Tunggu DOM update setelah isOutside=true
          await this.$nextTick();
          const container = this.$refs.mapContainer;
          // Render peta jika container sudah ada
          if (container) {
            await this.renderMap(container, this.polygonCoord, true);
          }
        } else
          this.savePresensi()
        // }
      } catch (err) {
        console.log(err)
        this.error = err.message;
      } finally {
        this.loading = false;
      }
    },
    savePresensi()
    {
      let codes = this.code.split('-')
      this.$http.get('presensi/mengajar',{
          params:{
            id_kelas:codes[1],
          }
        }).then(res => {
          this.$router.push({name:'presensi-form', query:{id_kelas:codes[1]}})
        }).catch(err => {
          var res = err.response;
          var code = res.status;
          this.$notify.error({
            title:'Error',
            message:res.data.messages.error,
            position:'bottom-right',
          })
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

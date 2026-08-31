<template>
  <div>
      <!-- Header -->
      <div v-if="!isLocated" class="p-6 lg:p-8 bg-slate-900 text-white flex flex-col gap-5
        items-center justify-between relative overflow-hidden
        ">
        <div class="w-full relative">
          <div class="relative z-10 flex items-center gap-3">
            <div class="w-12 h-12 bg-[var(--color-main-500)] rounded-2xl flex items-center justify-center text-white shadow-lg shadow-[var(--color-main-500)]">
              <icons icon="boxicons:qr-filled" class="w-6 h-6 m-0" />
            </div>
            <div>
              <h3 class="text-xl font-black tracking-tight">Presensi Guru Barcode/QR</h3>
              <p class="text-xs text-slate-400 font-medium">Scan kartu ID atau konfirmasi presensi kedatangan & kepulangan</p>
            </div>
          </div>
          <icons icon="mdi:sparkles" class="absolute -right-8 -bottom-8 opacity-10 text-[var(--color-main-400)] w-40 h-40" />
        </div>
        <!-- <el-input @change="detectedCode" v-model="initCode"/> -->
        <div
          class="flex-1 max-w-[600px] min-w-[300px] aspect-square mx-auto relative flex flex-col items-center justify-center rounded-[2rem] overflow-hidden"
        >
          <QRScanner @detected="detectedCode" 
            class="aspect-square "/>
            <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2
              text-center  font-bold text-white z-[9999] space-y-2"
              v-if="isScanned">
              <div class="border-0 border-b border-solid border-white px-3 pb-1">Scan berhasil!</div>
              <div class="text-sm fles items-center">
                <icons icon="mdi:loading" class="text-sm animate-spin"/>
                Cek Lokasi
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
                Arahkan kamera ke QR Code Presensi Kehadiran
              </div>
              <div class="text-xs opacity-60">Pastikan pencahayaan cukup</div>
            </div>
        </div>
        <div>
          <div class="text-slate-400 text-xs text-center">Atau silahkan masukkan kode</div>
          <el-input v-model="code" @keyup.enter="onScanSuccess(code)" placeholder="Masukkan kode presensi..." class="w-60 mt-2" 
            size="small"/>
        </div>
      </div>

      <!-- Content Body -->
      <div v-if="isLocated" class="p-6 lg:p-8 overflow-y-auto space-y-6 flex-1 bg-white/[0.8]">
        
        <!-- Lokasi Scanner -->
        
        <div class="space-y-2" v-if="!isSaved">
          <div>
            <label class="block text-[12px] font-black text-slate-400 uppercase tracking-widest mb-2">
              Lokasi Anda
            </label>
          </div>
          <div class="flex flex-col md:flex-row gap-4" v-if="isOutside">
            <div ref="mapContainer" class="map-view min-w-[400px] max-w-[500px] h-[400px] shrink-0"></div>
            <!-- LATE / EARLY WARNING & REASON REQUIREMENT -->
            <div 
              :class="[
                'p-5 rounded-3xl border transition-all bg-cyan-50/80 border-cyan-200 text-cyan-900'
              ]"
            >
              <div class="flex items-center gap-3 mb-3">
                <div :class="[
                  'p-2.5  rounded-2xl shrink-0 leading-none bg-cyan-600 text-white'
                ]">
                  <icons icon="mdi:alert" class="m-0 w-5" />
                </div>
                <div>
                  <h4 class="font-black text-sm uppercase tracking-wide leading-[1.3]">
                   PERINGATAN: <br/>
                   ANDA PRESENSI DARI LUAR LOKASI SEKOLAH
                  </h4>
                </div>
              </div>

              <!-- Mandatory Reason Input -->
              <div class="space-y-3 pt-2">
                <label class="block text-[11px] font-black uppercase tracking-wider text-slate-800">
                  Alasan / Keterangan <span class="text-red-500 font-bold">*Wajib Diisi</span>
                </label>

                <!-- Quick Reason Chips -->
                <el-radio-group v-model="reasonOutside" class="flex flex-wrap gap-2
                  [&_*]:text-[13px] [&_*]:font-bold
                  [&_.el-radio-button\_\_inner]:px-4  [&_.el-radio-button\_\_inner]:rounded-xl" 
                  fill="#0091b1">
                  <el-radio-button
                    v-for="(chip, idx) in quickReasonOutside"
                    :key="idx"
                    :label="chip"
                    :value="chip"
                    :class="[
                      ' transition-all border',
                    ]"
                  >
                  </el-radio-button>
                  <el-radio-button label="Lainnya" value="Lainnya" />
                </el-radio-group>

                <el-input
                  type="textarea"
                  :rows="3"
                  v-model="otherReasonOutisde"
                  :placeholder="'Tuliskan secara lengkap alasan presensi dari luar lokasi...'"
                  class="w-full bg-white border border-slate-200 rounded-2xl p-4 text-xs font-medium text-slate-800 outline-none focus:ring-2 focus:ring-cyan-500 transition-all placeholder:text-slate-400 placeholder:text-lg"
                  required
                />
              </div>

              <!-- Mandatory Photo Upload -->
              <div v-if="isOutside" class="space-y-3 pt-2">
                <label class="block text-[11px] font-black uppercase tracking-wider text-slate-800">
                  Bukti Foto <span class="text-red-500 font-bold">*Wajib Diisi</span>
                </label>
                <input 
                  type="file" 
                  @change="handlePhotoUpload" 
                  accept="image/*" 
                  class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100 file:border-cyan-200" 
                />
              </div>

            </div>
          </div>


        </div>
        <!-- FORM & SCANNER VIEW -->
        <div class="space-y-2" v-if="!isSaved">
          
          <!-- Mode Switch -->
          <div class="space-y-4">

            <!-- Scan Mode Toggle -->
            <div>
              <label class="block text-[12px] font-black text-slate-400 uppercase tracking-widest mb-2">
                Jenis Presensi Scan
              </label>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div
                  :class="[
                    'py-3 h-full rounded-2xl font-black text-md flex items-center justify-center gap-2 border transition-all',
                    scanMode === 'DATANG' 
                      ? 'bg-[var(--color-main-600)] text-white border-[var(--color-main-600)] shadow-md' 
                      : 'bg-slate-50 text-slate-500 border-slate-200 hover:bg-slate-100'
                  ]"
                >
                  <icons icon="mdi:login" class="w-4.5 h-4.5" /> Datang (Check-In)
                </div>
                <div
                  :class="[
                    'py-3 h-full rounded-2xl font-black text-md flex items-center justify-center gap-2 border transition-all',
                    scanMode === 'PULANG' 
                      ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' 
                      : 'bg-slate-50 text-slate-500 border-slate-200 hover:bg-slate-100'
                  ]"
                >
                  <icons icon="mdi:logout" class="w-4.5 h-4.5" /> Pulang (Check-Out)
                </div>
              </div>
            </div>
          </div>

          <!-- LATE / EARLY WARNING & REASON REQUIREMENT -->
          <div 
            v-if="requiresReason" 
            :class="[
              'p-5 rounded-3xl border transition-all',
              isLateCheckIn 
                ? 'bg-amber-50/80 border-amber-200 text-amber-900' 
                : 'bg-purple-50/80 border-purple-200 text-purple-900'
            ]"
          >
            <div class="flex items-start gap-3 mb-3">
              <div :class="[
                'p-2.5  rounded-2xl shrink-0 leading-none',
                isLateCheckIn ? 'bg-amber-500 text-white' : 'bg-purple-600 text-white'
              ]">
                <icons icon="mdi:alert" class="m-0 w-5" />
              </div>
              <div>
                <h4 class="font-black text-sm uppercase tracking-wide">
                  {{ isLateCheckIn ? 'PERINGATAN: TELAT DATANG' : 'PERINGATAN: PULANG LEBIH CEPAT' }}
                </h4>
                <p class="text-xs opacity-80 mt-0.5 font-medium">
                  {{ isLateCheckIn 
                    ? `Jam kedatangan (${effectiveTime}) melebihi batas jam masuk (${thresholds.CHECK_IN_DEADLINE}).`
                    : `Jam kepulangan (${effectiveTime}) lebih awal dari standar jam pulang (${thresholds.CHECK_OUT_MINIMUM}).` }}
                </p>
              </div>
            </div>

            <!-- Mandatory Reason Input -->
            <div class="space-y-3 pt-2">
              <label class="block text-[11px] font-black uppercase tracking-wider text-slate-800">
                Alasan / Keterangan <span class="text-red-500 font-bold">*Wajib Diisi</span>
              </label>

              <!-- Quick Reason Chips -->
              <el-radio-group v-model="reason" class="flex flex-wrap gap-2
                [&_*]:text-[13px] [&_*]:font-bold
                [&_.el-radio-button\_\_inner]:px-4  [&_.el-radio-button\_\_inner]:rounded-xl" 
                :fill=" scanMode == 'DATANG' ? 'oklch(0.77 0.16 67.26)' : 'oklch(38.1% 0.176 304.987)'">
                <el-radio-button
                  v-for="(chip, idx) in activeChips"
                  :key="idx"
                  :label="chip"
                  :value="chip"
                  :class="[
                    ' transition-all border',
                  ]"
                >
                </el-radio-button>
                <el-radio-button label="Lainnya" value="Lainnya" />
              </el-radio-group>

              <el-input
                type="textarea"
                :rows="3"
                v-model="otherReason"
                :placeholder="isLateCheckIn 
                  ? 'Tuliskan secara lengkap alasan keterlambatan...' 
                  : 'Tuliskan secara lengkap alasan kepulangan lebih awal...'"
                class="w-full bg-white border border-slate-200 rounded-2xl p-4 text-xs font-medium text-slate-800 outline-none focus:ring-2 focus:ring-amber-500 transition-all placeholder:text-slate-400 placeholder:text-lg"
                required
              />
            </div>           
            
          </div>

          <!-- Status Indicator -->
          <div v-else class="bg-[var(--color-main-50)] border border-[var(--color-main-200)] p-4 rounded-2xl flex items-center gap-3 text-[var(--color-main-800)]">
            <icons icon="mdi:check-circle" class="w-5 h-5 text-[var(--color-main-600)] shrink-0" />
            <p class="text-xs font-semibold">
              Waktu presensi tepat waktu. Tidak memerlukan keterangan khusus.
            </p>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col md:flex-row gap-3 pt-2">
            <el-button
              type="button"
              @click="$router.replace({name:'presensi-dashboard'})"
              class="m-0 h-full flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-black text-xs uppercase tracking-widest transition-all"
            >
              Batal
            </el-button>
            <el-button
              @click="savePresensi"
              :disabled="disabledButton"
              :class="[
                'm-0 h-full flex-1 py-3 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl transition-all flex items-center justify-center gap-2',
                disabledButton
                  ? 'bg-slate-300 text-slate-500 cursor-not-allowed shadow-none'
                  : scanMode === 'DATANG' 
                    ? 'bg-[var(--color-main-600)] hover:bg-[var(--color-main-500)] text-white shadow-[var(--color-main-600)]' 
                    : 'bg-indigo-600 hover:bg-indigo-500 text-white shadow-indigo-600/20'
              ]"
            >
              <icons icon="mdi:qrcode-scan" class="w-4.5 h-4.5" /> Simpan Presensi {{ scanMode }}
            </el-button>
          </div>

        </div>

        <!-- SUCCESS VIEW -->
        <div class="text-center space-y-6 py-4" v-else>
          <div class="w-20 h-20 bg-[var(--color-main-100)] text-[var(--color-main-600)] rounded-3xl flex items-center justify-center mx-auto shadow-inner">
            <icons icon="mdi:check-bold" class="w-10 h-10" />
          </div>
          <div>
            <span class="bg-[var(--color-main-50)] text-[var(--color-main-700)] text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full border border-[var(--color-main-200)]">
              Presensi Berhasil Dicatat
            </span>
            <h4 class="text-2xl font-black text-slate-900 mt-2">{{ submittedRecord?.nama_lengkap }}</h4>
            <p class="text-sm font-semibold text-slate-500 mt-1">
              Jenis Presensi: <span class="text-slate-900 font-bold">{{ scanMode === 'DATANG' ? 'Datang (Check-In)' : 'Pulang (Check-Out)' }}</span>
            </p>
          </div>

          <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 space-y-3 text-left">
            <div class="flex justify-between items-center text-xs">
              <span class="text-slate-400 font-bold uppercase tracking-wider">Waktu Record</span>
              <span class="font-black text-slate-800 text-sm">{{ scanMode === 'DATANG' ? submittedRecord?.waktu_datang : submittedRecord?.waktu_pulang }} WIB</span>
            </div>
            <div class="flex justify-between items-center text-xs">
              <span class="text-slate-400 font-bold uppercase tracking-wider">Status Ketepatan</span>
              <span :class="[
                'font-black text-xs px-3 py-1 rounded-lg',
                isLateCheckIn || isEarlyCheckOut
                  ? 'bg-amber-100 text-amber-800'
                  : 'bg-[var(--color-main-100)] text-[var(--color-main-800)]'
              ]">
                {{ isLateCheckIn ? 'Telat Presensi' : (isEarlyCheckOut ? 'Presensi Terlalu Awal' : 'Tepat Waktu') }}
              </span>
            </div>

            <div v-if="reason" class="pt-3 border-t border-slate-200/60">
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                Keterangan / Alasan
              </p>
              <p class="text-sm font-medium text-slate-700 bg-white p-3 rounded-xl border border-slate-200 italic">
                <b>{{ reason }}</b>
                <br/>{{ otherReason }}
              </p>
            </div>
          </div>

          <div class="flex gap-3">
            <el-button
              @click="$router.replace({name:'presensi-dashboard'})"
              class="flex-1 py-4 bg-[var(--color-main-600)] hover:bg-[var(--color-main-700)] text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-[var(--color-main-600)] transition-all"
            >
              Selesai
            </el-button>
          </div>
        </div>
      </div>
  </div>
</template>

<script>
// import { MOCK_TEACHERS, TEACHER_SCHEDULE_THRESHOLDS } from '../constants';
import QRScanner from '@/components/QRScanner.vue';
import { mapState } from 'pinia'
import coord from '@/modules/presensi/helpers/kedatanganCoord'
// console.log(coord)
export default {
  name: 'TeacherQRScannerModal',
  components: {
    QRScanner,
  },
  setup(){
    const { userCoords, loading, error, getCurrentLocation, isInsidePolygon, isWithinRadius, openInGoogleMaps, renderMap } = useGeofence()
    return {
      userCoords, loading, error, getCurrentLocation, isInsidePolygon, isWithinRadius, openInGoogleMaps, renderMap
    }
  },
  props: {
  },
  emits: ['close', 'savePresence'],
  data() {
    return {
      dataId:'',
      initCode:'',
      isScanned:false,
      isLocated:false,
      isSaved:false,
      isOutside:false,
      thresholds: {
        CHECK_IN_DEADLINE:'07:00',
        // INPUT_IN_MAXIMUM:'11:00',
        CHECK_OUT_MINIMUM:'16:00',
      },
      useCustomTime: false,
      scanMode: this.initialMode || 'DATANG',
      polygonCoord: this.$coordinate,
      quickReasonChipsDatang: [
        'Macet Lalu Lintas',
        'Kendaraan Mogok/Bocor',
        'Kondisi Kesehatan / Berobat',
        'Urusan Keluarga Mendesak',
        'Cuaca Buruk / Hujan Deras',
      ],
      quickReasonChipsPulang: [
        'Izin Dinas Luar / Tugas Pesantren',
        'Urusan Keluarga Urgent',
        'Kondisi Kesehatan Kurang Fit',
        'Mengantar Santri ke Faskes',
        'Izin Pimpinan Pesantren',
      ],
      quickReasonOutside:[
        'Izin Dinas Luar',
        'Mengantar Santri ke Faskes',
        'Urusan Keluarga Mendesak',
        'Kondisi Kesehatan',
        'Tugas Luar Sekolah',
        'Rapat di Luar',
        'Kegiatan Ekstrakurikuler di Luar',
      ],
      code:'',
      reason:'',
      otherReason:'',
      reasonOutside:'',
      otherReasonOutside:'',
      submittedRecord: {},
      photo: null,
      // user:{
      //   id_guru:'4  ',
      // }
    };
  },
  computed: {
    ...mapState(useAuthStore,{
      user: 'loggedUser'
    }),
    effectiveTime() {
      // return '18:50';
      return timeNow();
    },
    isLateCheckIn() {
      return (
        this.scanMode === 'DATANG' &&
        this.effectiveTime > this.thresholds.CHECK_IN_DEADLINE
      );
    },
    isEarlyCheckOut() {
      return (
        this.scanMode === 'PULANG' &&
        this.effectiveTime < this.thresholds.CHECK_OUT_MINIMUM
      );
    },
    requiresReason() {
      return this.isLateCheckIn || this.isEarlyCheckOut;
    },
    activeChips() {
      return this.scanMode === 'DATANG'
        ? this.quickReasonChipsDatang
        : this.quickReasonChipsPulang
    },
    disabledButton() {
      let emptyOutside, emptyPresence = false
      if (this.isOutside) {
        emptyOutside = !(this.reasonOutside || this.otherReasonOutside) || !this.photo;
      } 
      if (this.requiresReason) {
        emptyPresence = !(this.reason || this.otherReason);
      }
      console.log('disabledButton', emptyOutside, emptyPresence)
      return emptyOutside || emptyPresence;
    },
  },
  watch: {
    
  },
  mounted() {
    this.initFormState();
    // setTimeout(()=> {
    //   this.detectedCode('PresensiHarian')
    // }, 1000)
  },
  methods: {
    handlePhotoUpload(event) {
      this.photo = event.target.files[0];
    },
    detectedCode(code){
      this.onScanSuccess(code)
    },
    onScanSuccess(code){
      let codes = code.split('-')
      if (codes[0] == 'PresensiHarian') {
        this.isScanned = true
        this.checkLocationAndRenderMap()
        // this.$http.get('presensi/mengajar',{
        //   params:{
        //     id_kelas:codes[1],
        //   }
        // }).then(res => {
        //   this.$router.replace({name:'presensi-form', query:{id_kelas:codes[1]}})
        // }).catch(err => {
        //   var res = err.response;
        //   var code = res.status;
        //   this.$notify.error({
        //     title:'Error',
        //     message:res.data.messages.error,
        //     position:'bottom-right',
        //   })
        // })
      } else {
        this.$alert('Kode yang Anda Masukkan Keliru', 'Error', {
          // if you want to disable its autofocus
          // autofocus: false,
          type:'error',
          confirmButtonText: 'OK',
        })
      }
    },
    async checkLocationAndRenderMap() {
      try {
        const coords = await this.getCurrentLocation();
        this.userCoords = coords;
        this.isLocated = true
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

        if (this.isOutside) {
          // Tunggu DOM update setelah isOutside=true
          await this.$nextTick();
          const container = this.$refs.mapContainer;
          // Render peta jika container sudah ada
          if (container) {
            await this.renderMap(container, this.polygonCoord, true);
          }
        }
        
        if (!this.isLateCheckIn && !this.isEarlyCheckOut && !this.isOutside) {
          this.savePresensi()
        }
      } catch (err) {
        console.log(err)
        this.error = err.message;
      } finally {
        this.loading = false;
      }
    },
    initFormState() {

      this.$http.get('presensi/kedatangan',{
        params:{
          where:{
            id_guru:this.user?.id_guru,
            tanggal: dateNow()
          }
        }
      })
      .then(res => {  
        let data = res.data
        if (data?.length > 0) {
          this.dataId = data[0].id
          this.scanMode = 'PULANG'
        } else {
          if (this.effectiveTime > this.thresholds.INPUT_IN_MAXIMUM) {
            this.saveAlfa();
          } else {
            this.dataId = -1
            this.scanMode = 'DATANG'
          }
        }
      })
    },
    saveAlfa(){
      let form = window.jsonToFormData({
        id:-1,
        id_guru: this.user.id_guru,
        tanggal: dateNow(),
      })
      this.$http.post('presensi/kedatangan/store',form)
        .then(res => {
          this.initFormState()
        })
    },
    savePresensi() {
      let form = {
        id: this.dataId,
        id_guru: this.user.id_guru,
        tanggal: dateNow(),
      }
      console.log(this.userCoords)
      if (this.scanMode == 'DATANG') 
        form = {...form,...{
          waktu_datang: this.effectiveTime,
          telat_datang: this.isLateCheckIn ? '1' : '0',
          jenis_telat_datang: this.reason,
          alasan_datang:this.otherReason,
          latitude_datang: this.userCoords?.lat || null,
          longitude_datang: this.userCoords?.lng || null,
          is_luar_datang: this.isOutside ? '1' : '0',
          jenis_luar_datang: this.isOutside ? this.reasonOutside : null,
          alasan_luar_datang: this.isOutside ? this.otherReasonOutside : null,
          bukti_foto_datang: this.isOutside ? this.photo : null,
        }}
      else 
        form = {...form,...{
          waktu_pulang: this.effectiveTime,
          telat_pulang: this.isEarlyCheckOut ? '1' : '0',
          jenis_telat_pulang: this.reason,
          alasan_pulang:this.otherReason,
          latitude_pulang: this.userCoords?.lat || null,
          longitude_pulang: this.userCoords?.lng || null,
          is_luar_pulang: this.isOutside ? '1' : '0',
          jenis_luar_pulang: this.isOutside ? this.reasonOutside : null,
          alasan_luar_pulang: this.isOutside ? this.otherReasonOutside : null,
          bukti_foto_pulang: this.isOutside ? this.photo : null,
        }}
      console.log(form)

      form = window.jsonToFormData(form)
      
      // return

      this.$http.post('presensi/kedatangan/store',form)
        .then(res => {
          this.isSaved = true
          this.submittedRecord = res.data
        })
        .catch(err => {
          this.isSaved = false
          this.$notify.error({
            title:'Gagal presensi',
            message:'Anda tidak dapat menyimpan presensi saat ini',
            position:'bottom-right',
          })
        })
    }
  },
};
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
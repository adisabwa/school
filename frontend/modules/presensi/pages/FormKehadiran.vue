<template>
  <div class="py-6 md:px-10 text-center bg-white/[0.8] relative" >
    <div class="max-w-[700px] shadow-xl p-3 py-5 md:p-5 mx-auto rounded-3xl">
      <div class="text-left">
        <div class="">
          <div class="text-[var(--color-main-600)] font-bold flex justify-between items-center *:leading-[1] mb-3
            ">
            <span>{{ dateIndo(dataKelas?.tanggal) }} </span> 
            <span class="text-right">Sesi {{ dataKelas?.sesi_awal }} - {{ dataKelas?.sesi_akhir }}<br/>
              <small>( {{ dataKelas?.waktu_mulai?.slice(0, 5) }} - {{ dataKelas?.waktu_selesai?.slice(0, 5) }} )</small>
            </span>
          </div>
          <div class="flex items-start lg:items-center gap-2 lg:gap-7 mb-3 lg:mb-5 text-center md:text-left border-b border-slate-50">
            <div class="flex flex-col items-center">
              <div class="w-20 h-20 lg:w-28 lg:h-28 bg-[var(--color-main-100)] text-[var(--color-main-600)] rounded-xl lg:rounded-3xl flex items-center justify-center font-black text-3xl lg:text-5xl shadow-inner">
                {{ dataKelas?.kelas }}
              </div>
            </div>
            <div class="">
              <div class="text-left text-sm lg:text-xl text-slate-500 grid grid-cols-1 lg:grid-cols-[130px,_1fr] items-center justify-center md:justify-start gap-x-2 gap-y-1 md:gap-y-2">
                <span class="bg-slate-100 px-2 py-0.5 rounded text-[10px] lg:text-sm font-bold text-slate-400 uppercase">Mata Pelajaran</span>
                <span class="text-[var(--color-main-600)] font-bold flex items-center">
                  <icons icon="line-md:edit-twotone" class="mr-2 text-[20px] cursor-pointer" @click="editMapel = true"/>
                  <span v-if="editMapel">
                    <floating-select v-model:value="dataKelas.id_mapel" placeholder="Pilih Mapel Baru" 
                      :filterable="true" :clearable="true" size="large"
                      @change="editMapel = false"
                      class="max-w-full"
                      :options="optionsMapel">
                    </floating-select>
                  </span>
                  <span v-else>{{ runFunction({data:dataKelas.id_mapel, options: optionsMapel}) }}</span>
                </span>
                <span class="bg-slate-100 px-2 py-0.5 rounded text-[10px] lg:text-sm font-bold text-slate-400 uppercase">Pengajar</span>
                <span class="text-[var(--color-main-600)] font-bold flex items-center">
                  <icons icon="line-md:edit-twotone" class="mr-2 text-[20px] cursor-pointer" @click="editGuru = true"/>
                  <span>{{ dataKelas?.nama_guru_lengkap }}</span>
                </span>
                <template v-if="editGuru || dataKelas.id_pengganti > 0">
                  <span class="bg-slate-100 px-2 py-0.5 rounded text-[10px] lg:text-sm font-bold text-slate-400 uppercase">Diganti oleh</span>
                  <span class="text-[var(--color-main-600)] font-bold flex items-center">
                    <icons v-if="!editGuru" icon="line-md:edit-twotone" class="mr-2 text-[20px] cursor-pointer" @click="editGuru = true"/>
                    <icons v-else icon="mdi:check" class="mr-2 text-[20px] cursor-pointer" @click="editGuru = false"/>
                    <span v-if="editGuru">
                      <floating-select v-model:value="dataKelas.id_pengganti" placeholder="Pilih Guru Pengganti" 
                        :filterable="true" :clearable="true" size="large"
                        class="max-w-full"
                        :options="optionsGuru">
                      </floating-select>
                    </span>
                    <span v-else>{{ dataKelas.nama_guru_pengganti ?? runFunction({data:dataKelas.id_pengganti, options: optionsGuru}) }}</span>
                  </span>
                </template>
              </div>
            </div>
          </div>
          <!-- Topik -->  
          <div class="relative">
            <label
              class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 ml-1"
            >
              Topik Pembelajaran
            </label>

            <div class="relative">
              <el-input
                v-model="dataKelas.topik"
                type="textarea"
                :rows="4"
                placeholder="Contoh: Bab Thaharah - Adab Buang Hajat"
                class=""
              />
            </div>
          </div>

          <!-- Pengecekan -->
          <div class="bg-white rounded-3xl py-7 p-5 border border-slate-100 shadow-sm space-y-5">
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">
              Pengecekan Kesiapan
            </label>

            <!-- Seragam -->
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div
                  :class="[
                    'w-10 h-10 rounded-xl flex items-center justify-center',
                    dataKelas.is_seragam ? 'bg-[var(--color-main-50)] text-[var(--color-main-600)]' : 'bg-slate-50 text-slate-400'
                  ]"
                >
                  <icons icon="ri:shirt-line" />
                </div>
                <div>
                  <div class="text-sm font-bold text-slate-700">Berpakaian Rapi</div>
                  <div class="text-[11px] text-slate-400">
                    Guru memakai seragam lengkap
                  </div>
                </div>
              </div>

              <el-switch active-value="1" inactive-value="0" v-model="dataKelas.is_seragam" />
            </div>

            <!-- Perangkat -->
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div
                  :class="[
                    'w-10 h-10 rounded-xl flex items-center justify-center',
                    dataKelas.is_perangkat ? 'bg-[var(--color-main-50)] text-[var(--color-main-600)]' : 'bg-slate-50 text-slate-400'
                  ]"
                >
                  <icons icon="bxs:wrench" />
                </div>
                <div>
                  <div class="text-sm font-bold text-slate-700">Perangkat Mengajar</div>
                  <div class="text-[11px] text-slate-400">
                    Guru sudah menyiapkan perangkat mengajar
                  </div>
                </div>
              </div>

              <el-switch active-value="1" inactive-value="0" v-model="dataKelas.is_perangkat" />
            </div>

            <!-- Keterlambatan -->
            <div class="flex items-center justify-between"
              v-if="dataKelas.is_telat == '1'">
              <div class="flex items-start gap-3 w-full">
                <div
                  :class="[
                    'w-10 h-10 rounded-xl flex items-center justify-center',
                    dataKelas.is_telat ? 'bg-[var(--color-main-50)] text-[var(--color-main-600)]' : 'bg-slate-50 text-slate-400'
                  ]"
                >
                  <icons icon="material-symbols:assignment-late" />
                </div>
                <div class="w-full">
                  <div class="text-sm font-bold text-slate-700">Keterlambatan</div>
                  <div class="text-[11px] text-slate-400">
                    Anda terlambat selama 
                    <el-input class="w-full" v-if="role == 'admin'"
                      v-model="timeMinute" @input="dataKelas.waktu_telat = timeMinute * 60; timeMinute = toNumber(timeMinute)">
                      <template #append>
                        Menit
                      </template>
                    </el-input>
                    <b v-else class="italic">{{ dataKelas.waktu_telat / 60 }} menit</b>
                  </div>
                  <el-input
                    v-model="dataKelas.alasan_telat"
                    type="textarea"
                    :rows="2"
                    placeholder="Masukkan alasan keterlambatan Anda"
                    class="w-full mt-1"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Submit -->
        <el-button
          type="success"
          size="large"
          class="w-full !h-auto py-4 rounded-2xl font-bold shadow-lg"
          @click="handleSubmit"
        >
          Lanjut ke Daftar Absensi
          <ChevronRight class="ml-2" :size="20" />
        </el-button>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'pinia';
import { useAuthStore } from '@/config/stores/authStore';

export default {
  name: 'SessionForm',

  components: {},

  setup(){
    return {
      runFunction, toNumber, dateIndo,
    }
  },

  data() {
    return {
      editMapel:false,
      editGuru:false,
      idKelas:null,
      idSesi:null,
      tanggal:null,
      idGuru:'',
      idMapel:'',
      dataKelas:{
        topik:'',
        is_seragam:'',
        is_telat:'',
        waktu_telat:'',
        alasan_telat:'',
        is_perangkat:'',
        id_mapel:'',
        waktu_mulai:'',
        waktu_selesai:'',
      },
      timeMinute:0,
      optionsMapel:[],
      optionsGuru:[],
    }
  },
  computed:{
    ...mapState(useAuthStore, {
      user: 'loggedUser',
      role: 'role',
    }),
  },
  watch:{
    idKelas(val){
      this.getData()
    },
  },
  methods: {
    getInitial(){
      this.$http.get('mapel/admin/options')
        .then(res => this.optionsMapel = res.data)
      this.$http.get('data/guru/options')
        .then(res => this.optionsGuru = res.data)
    },
    getData(){
      this.$http.get('presensi/mengajar',{
        params:{
          id_kelas:this.idKelas,
          id_sesi:this.idSesi,
          tanggal:this.tanggal,
        }
      }).then(res => {
        this.dataKelas = res.data
        if (this.idGuru != this.dataKelas.id_guru){
          this.dataKelas.id_pengganti = this.idGuru
        }
        this.timeMinute = Math.floor(this.dataKelas.waktu_telat / 60)
      }).catch(err => {
        console.log(err)
      })
    },
    saveData(){
      
    },
    handleSubmit() {

      if (!this.dataKelas.topik.trim()) {
        this.$alert('Mohon isi topik materi hari ini','Error',{type:'warning'})
        return
      }
      
      let form = window.jsonToFormData({
        id: this.dataKelas.id,
        id_guru: this.dataKelas.id_guru,
        id_semester: this.dataKelas.id_semester,
        id_sesi: this.dataKelas.id_sesi,
        id_kelas: this.dataKelas.id_kelas,
        id_pengganti: this.dataKelas.id_pengganti,
        tanggal: this.dataKelas.tanggal,
        id_mapel: this.dataKelas.id_mapel,
        jam: this.dataKelas.jam,
        kehadiran:'hadir',
        topik: this.dataKelas.topik,
        is_seragam: this.dataKelas.is_seragam,
        is_perangkat: this.dataKelas.is_perangkat,
        is_telat: this.dataKelas.is_telat,
        alasan_telat: this.dataKelas.alasan_telat,
        waktu_telat: this.dataKelas.waktu_telat,
      })
      this.$http.post('presensi/mengajar/store', form)
        .then(res => {
          let data = res?.data
          this.$router.replace({name:'presensi-list', query: {id:data.id}})
        })
        .catch(err => {
          console.log(err)
        })
    },
  },
  created(){
    let id_kelas = this.$route?.query?.id_kelas ?? -1
    this.idGuru = this.user.id
    this.getInitial()
    if (id_kelas <= 0)
      this.$alert('Anda belum scan QR','Error',{
        type:'error',
        callback: () => {
          this.$router.replace({name:'presensi-scanner'})
        }
      })
    else
      this.idKelas = id_kelas
    this.idSesi = this.$route?.query?.id_sesi ?? null
    this.tanggal = this.$route?.query?.tanggal ?? null
  }
}
</script>

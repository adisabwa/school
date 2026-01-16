<template>
  <div class="py-6 md:px-10 text-center bg-white/[0.8] relative" >
    <div class="max-w-[600px] shadow-xl p-3 py-5 md:p-5 mx-auto rounded-3xl">
      <div class="text-left">
        <div class="">
          <div class="flex flex-col md:flex-row items-center gap-2 lg:gap-7 mb-3 lg:mb-5 text-center md:text-left border-b border-slate-50">
            <div class="flex flex-col items-center">
              <div class="w-20 h-20 lg:w-28 lg:h-28 bg-emerald-100 text-emerald-600 rounded-xl lg:rounded-3xl flex items-center justify-center font-black text-3xl lg:text-5xl shadow-inner">
                {{ dataKelas?.kelas }}
              </div>
            </div>
            <div>
              <div class="text-sm lg:text-xl text-slate-500 grid grid-cols-[130px,_1fr] md:grid-cols-1 items-center justify-center md:justify-start gap-x-2 gap-y-0 md:gap-y-1">
                <span class="bg-slate-100 px-2 py-0.5 rounded text-[10px] lg:text-sm font-bold text-slate-400 uppercase">Mata Pelajaran</span>
                <span class="text-emerald-600 font-bold flex items-center">
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
                <span class="text-emerald-600 font-bold flex items-center">
                  <icons icon="line-md:edit-twotone" class="mr-2 text-[20px] cursor-pointer" @click="editGuru = true"/>
                  <span v-if="editGuru">
                    <floating-select v-model:value="dataKelas.id_pengganti" placeholder="Pilih Guru Pengganti" 
                      :filterable="true" :clearable="true" size="large"
                      @change="editGuru = false"
                      class="max-w-full"
                      :options="optionsGuru">
                    </floating-select>
                  </span>
                  <span v-else>{{ dataKelas.id_pengganti > 0 ? runFunction({data:dataKelas.id_pengganti, options: optionsGuru}) : dataKelas?.nama_guru }}</span>
                </span>
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
                    dataKelas.is_seragam ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-50 text-slate-400'
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
                    dataKelas.is_perangkat ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-50 text-slate-400'
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

export default {
  name: 'SessionForm',

  components: {},

  props: {
  },

  data() {
    return {
      editMapel:false,
      editGuru:false,
      idKelas:null,
      idGuru:'',
      idMapel:'',
      dataKelas:{
        topik:'',
        is_seragam:'',
        is_perangkat:'',
        id_mapel:'',
      },
      optionsMapel:[],
      optionsGuru:[],
    }
  },
  computed:{
    
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
        }
      }).then(res => {
        this.dataKelas = res.data
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
        id_kelas: this.dataKelas.id_kelas,
        id_pengganti: this.dataKelas.id_pengganti,
        id_mapel: this.dataKelas.id_mapel,
        topik: this.dataKelas.topik,
        is_seragam: this.dataKelas.is_seragam,
        is_perangkat: this.dataKelas.is_perangkat,
      })
      this.$http.post('presensi/mengajar/store', form)
        .then(res => {
          this.$router.replace({name:'presensi-list', query: {id:this.dataKelas.id}})
        })
        .catch(err => {
          console.log(err)
        })
    },
  },
  created(){
    let id_kelas = this.$route?.query?.id_kelas ?? -1
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
  }
}
</script>

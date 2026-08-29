<template>
  <div class="h-full flex flex-col justify-between bg-white/[0.7] text-slate-900 font-sans">
    <!-- Drawer Header -->
    <div class="bg-white text-slate-900 p-3 flex items-center justify-between border-b border-slate-200">
      <div class="flex items-center space-x-3">
        <div class="w-8 h-8 rounded-md bg-[var(--color-main-50)] border-solid border border-[var(--color-main-100)] flex items-center justify-center text-[var(--color-main-600)]">
          <icons icon="mdi:file-document-outline" class="w-4 h-4" />
        </div>
        <div>
          <h3 class="font-bold text-base text-slate-900">Riwayat RPP Tersimpan</h3>
          <p class="text-xs text-slate-500">Total {{ savedRPPs.length }} dokumen tersimpan</p>
        </div>
      </div>
    </div>

    <!-- List Content -->
    <div class="p-3 overflow-y-auto flex-1 space-y-3 bg-slate-50/50" v-loading="loading">
      <!-- Empty State -->
      <div v-if="savedRPPs.length === 0" class="text-center py-16 text-slate-400 space-y-2">
        <icons icon="mdi:file-document-outline" class="w-12 h-12 mx-auto stroke-1 text-slate-300" />
        <p class="text-sm font-semibold text-slate-700">Belum ada RPP tersimpan</p>
        <p class="text-xs max-w-xs mx-auto text-slate-500">
          Buat RPP otomatis lalu klik tombol "Simpan Dokumen" untuk menyimpan di browser Anda.
        </p>
      </div>

      <!-- RPP List -->
      <div  v-else  
        class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <div
          v-for="rpp in savedRPPs"
          :key="rpp.subjectInfo?.id"
          class="bg-white border-solid border border-slate-200 rounded-lg p-4 space-y-3 hover:border-[var(--color-main-300)] transition shadow-xs
            flex flex-col justify-between"
        >
          <div class="flex items-start justify-between gap-2">
            <div>
              <span class="text-[10px] font-bold uppercase tracking-wider bg-[var(--color-main-50)] text-[var(--color-main-700)] border-solid border border-[var(--color-main-100)] px-2 py-0.5 rounded-md inline-block mb-1">
                Kelas {{ rpp.subjectInfo?.tingkat }}
              </span>
              <h4 class="font-bold text-slate-900 text-sm leading-snug">
                {{ rpp.subjectInfo?.nama_mapel }} — {{ rpp.subjectInfo?.materi }}
              </h4>
              <div class="text-xs text-slate-500 mt-3 space-y-2">
                <div class="flex items-center gap-1">
                  <icons icon="mdi:calendar-month-outline" class="w-3 h-3 text-slate-400 m-0" />
                  {{ dateIndo(rpp.created_at) }}
                </div>
                <div class="flex items-center gap-1">
                  <icons icon="mdi:building" class="w-3 h-3 text-slate-400 m-0" />
                  {{ rpp.subjectInfo?.nama_unit }}
                </div>
              </div>
            </div>

            <el-button
              type="danger" plain
              class="p-1.5 rounded-md  transition cursor-pointer"
              title="Hapus RPP"
              @click="handleDelete(rpp.subjectInfo?.id)"
            >
              <icons icon="mdi:trash-can-outline" class="w-4 h-4 m-0" />
            </el-button>
          </div>

          <div class="flex items-center justify-between pt-2 border-t border-slate-100">
            <el-button
              type="success" plain
              class="text-xs font-bold hover:underline cursor-pointer flex items-center gap-1"
              @click="handleSelect(rpp.subjectInfo?.id)"
            >
              <span>Buka & Edit Dokumen</span>
              <icons icon="mdi:arrow-right" class="w-3.5 h-3.5 m-0" />
            </el-button>

            <div class="flex items-center space-x-1.5">
              <el-button
                type="warning" plain
                class="p-1.5 rounded-lg transition cursor-pointer"
                title="Cetak PDF"
                @click="handlePrintPDF(rpp.subjectInfo)"
              >
                <icons icon="mdi:printer" class="w-4 h-4 m-0" />
              </el-button>

              <el-button
                type="primary" plain
                class="p-1.5  rounded-lg transition cursor-pointer"
                title="Unduh Word (.docx)"
                @click="handleExportWord(rpp.subjectInfo)"
              >
                <icons icon="mdi:download" class="w-4 h-4 m-0" />
              </el-button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="hidden">
      <RppGenerator :id="dataId" :key="formKey"/>
    </div>

  </div>
</template>

<script>
import RppGenerator from './RppGenerator.vue';

import { mapState } from 'pinia';
export default {
  name: 'SavedRPPDrawer',
  components:{
    RppGenerator
  },
  setup(){
    return {
      dateIndo
    }
  },
  data(){
    return {
      tahunAjaran:'2026/2027',
      savedRPPs:[],
      dataId:'',
      formKey:1,
      loading: false,
    }
  },
  computed:{
    ...mapState(useAuthStore,{
      user:'loggedUser',
    })
  },
  watch:{
    dataId(val) {
      this.formKey++
    }  
  },
  methods: {
    getRpp(){
      this.$http.get('mapel/materi', {
        params:{
          where:{
            '{n}tahun_ajaran': this.tahunAjaran,
            '{n}id_guru' : this.user.id_guru,
          },
          order:['created_at']
        }
      }).then(res => {
        let materis = res?.data.filter(d => {
          try {
            return JSON.parse(d?.rpp)
          } catch {
            return false
          }
        })
        this.savedRPPs = materis.map(d => {
          let rpp = JSON.parse(d?.rpp)
          
          return {
            ...rpp,
            ...{
              created_at: d.created_at
            }
          }
        })
      })
    },
    handleSelect(id) {
      console.log(id)
      this.$router.push({name:'mapel-rpp', query:{id: id}})
    },
    handleDelete(id) {
      this.$confirm('Apakah anda yakin untuk menghapus data ini?',
          'Konfirmasi',
          {
            confirmButtonText: 'OK',
            cancelButtonText: 'Batal',
            type: 'warning',
          })
          .then(() => {
            this.$http.post('mapel/materi/store', window.jsonToFormData({
              id: id,
              rpp: null,
            }))
            .then(result => {
                this.$notify({
                  type:'success',
                  title: 'Berhasil',
                  message: 'Data berhasil dihapus',
                  position: 'bottom-right'
                });
                this.getRpp()
              })
              .catch(err => {
                this.$notify({
                  type:'error',
                  title: 'Gagal',
                  message: 'Tidak dapat menghapus data',
                  position: 'bottom-right'
                });
              });
            })
            .catch(err => {
              console.log(err)
            });
    },
    handlePrintPDF(subject) {
      this.dataId = subject.id
      this.loading = true
      console.log('click')
      setTimeout(() => {
        console.log('print')
        this.loading = false
        printElementById('printable-rpp',{
          paperSize: 'A4',
        })
      }, 3000)
    },
    handleExportWord(subject) {
      this.dataId = subject.id
      this.loading = true
      console.log('click')
      setTimeout(() => {
        console.log('print')
        this.loading = false
        let filename = `RPP ${subject?.nama_mapel} - Materi ${subject?.no ?? 1} : ${subject?.materi}`
        downloadHtmlAsWordById('printable-rpp', filename)
      }, 3000)
    },
  },
  mounted(){
    this.getRpp()
  }
};
</script>
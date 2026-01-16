<template>
  <div class="p-2 h-full bg-white/[0.7]">
      <img :src="$baseUrl + 'assets/images/ekstra/ts.png'" width="50px" 
        class="absolute -left-1 sm:left-1 translate-y-[-10px] hover:scale-90 z-[99]"
        @click="$router.replace({name:'tsdac-dashboard'})"/>
      <div class="w-full flex text-slate-800">
        <div class="w-12 text-slate-400 border-0 border-b border-solid border-b-400">
        </div>
        <div v-for="value in [{ value:'penilaian',label:'Pertandingan'},
          {value:'peserta',label:'Peserta'},
          {value:'rekap',label:'Rekap'}]"
          :class="['w-1/2 bg-white text-center p-1',
          activeTab === value.value ? 
            'text-blue-600 border border-b-white border-solid border-blue-400' : 
            'text-slate-400 border-0 border-b border-solid border-b-400']" @click="activeTab = value.value">
          <b>{{ ucFirst(value.label) }}</b>
        </div>
      </div>
      <div class="border border-t-0 border-solid border-blue-400 px-3 pt-4 pb-6">
        <div v-if="activeTab == 'penilaian'" class="h-full">
          <form-comp ref="formPenilaian"
            :fields="filterPenilaianFields"
            :form-class="'mt-2 mb-0'"
            label-width="100px"
            v-model:form-value="filterPenilaian"
            :pass-columns="[]"
            :show-submit="false"
            :label-positon="$windowWidth > 400 ? 'left' : 'top'"
            :show-label="$windowWidth > 400"
            error-submit-text="Tidak dapat mengambil data"
            :show-required-text="false"
            >
          </form-comp>
          <div class="w-full grid grid-cols-[1fr_70px_1fr]" v-if="Object.values(matchResults).length > 0">
            <template v-for="(match, macthInd) in matchResults" :key="match.id" class="mb-1 cursor-pointer border rounded-lg p-2 hover:shadow-md transition" @click="selectedMatch = match.id">
              <div @click="match.showDetail = !match.showDetail" class="relative grid grid-cols-subgrid col-span-3" >  
                <div>
                  <div class="mt-2 text-right text-white  
                    w-full">
                    <div class="flex gap-x-2 justify-between items-center px-3 py-2 bg-blue-400">
                      <span class="font-semibold pl-8">{{ match.nama_biru }} ({{ match.kelas_biru }})</span>
                      <span :class="['px-2 py-1 rounded text-2xl font-bold',
                        match.isJuryMismatchBiru ? 'bg-red-600' : 'bg-blue-600']">{{ match.total_nilai_biru }}</span>
                    </div>
                  </div>
                </div>
                <div class="text-center px-4">
                  <img :src="$baseUrl + 'assets/images/ekstra/vs.png'" width="40px" class="invert mt-2"/>
                  <img :src="$baseUrl + 'assets/images/ekstra/winner.png'" width="70px" 
                    :class="['absolute top-1/2 -translate-y-1/2',
                    match.total_nilai_biru == match.total_nilai_kuning ? 'hidden':
                    match.total_nilai_biru < match.total_nilai_kuning ? '-right-7' : '-left-7 -scale-x-100']"/>
                </div>
                <div>
                  <div class="mt-2 text-left 
                    w-full">
                    <div class="flex gap-x-2 justify-between items-center bg-yellow-500 text-white px-3 py-2">
                      <span :class="['px-2 py-1 rounded text-2xl font-bold',
                        match.isJuryMismatchKuning ? 'bg-red-600' : 'bg-yellow-700']">{{ match.total_nilai_kuning }}</span>
                      <span class="font-semibold pr-8">{{ match.nama_kuning }} ({{ match.kelas_kuning }})</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="text-left mt-1">
                <div v-if="match.isJuryMismatchBiru" class="text-red-600 text-xs mb-1 font-semibold">⚠️ Perbedaan nilai juri</div>
              </div>
              <div class="text-center font-bold text-teal-950"> {{ match.showDetail ? 'Babak' : '' }}</div>
              <div class="text-right mt-1">
                <div v-if="match.isJuryMismatchKuning" class="text-red-600 text-xs mb-1 font-semibold">⚠️ Perbedaan nilai juri</div>
              </div>

              <template v-for="(m, mInd) in match.match" v-if="match.showDetail">
                <div>
                  <div :class="['w-full flex gap-x-1 text-center add-transition']">
                    <div v-for="i in range(4, 1)" :key="i" 
                      :class="['text-xs mt-1 w-1/4 p-2 rounded-md',
                        m.is_match_biru[i-1] ? 'bg-blue-200 text-blue-900' : 'bg-red-300 text-red-900']">
                      <div class="font-bold ">Juri {{ i }}</div>
                      <div v-if="m.biru?.[i]?.total_nilai" 
                        class=" text-2xl font-bold">
                        {{ m.biru[i].total_nilai }}
                      </div>
                      <div v-else class="text-gray-600 italic text-[11px]">Belum dinilai</div>
                    </div>
                  </div>
                </div>
                <div class="relative px-3 text-center flex flex-col items-center justify-center gap-1" >
                  <div class="rounded-sm w-full py-1
                    bg-teal-200 text-teal-800 text-center font-bold">{{ m.babak }}</div>
                  <div class="text-[11px] bg-red-600 text-white w-full py-[1px] hover:scale-90" 
                    @click="deleteMatch(macthInd, mInd, m)" >Hapus</div>
                </div>
                <div>
                  <div :class="['w-full flex gap-x-1 text-center add-transition']">
                    <div v-for="i in range(4, 1)" :key="i" 
                      :class="['text-xs mt-1 w-1/4 p-2 rounded-md',
                        m.is_match_kuning[i-1] ? 'bg-yellow-200 text-yellow-900' : 'bg-red-300 text-red-900']">
                      <div class="font-bold ">Juri {{ i }}</div>
                      <div v-if="m.kuning?.[i]?.total_nilai" 
                        class=" text-2xl font-bold">
                        {{ m.kuning[i].total_nilai }}
                      </div>
                      <div v-else class="text-gray-600 italic text-[11px]">Belum dinilai</div>
                    </div>
                  </div>
                </div>
              </template>
            </template>
          </div>
          <div v-else 
            class="font-bold text-[32px] flex items-center justify-center
            text-slate-600 h-32">
            <img :src="$baseUrl + 'assets/images/ekstra/ts.png'" width="130px" 
              class="opacity-30"/>
            <div class="absolute left-1/2 -translate-x-1/2 z-[1] ">- Belum Ada Data -</div>
          </div>
        </div>
        <div v-else-if="activeTab == 'peserta'" class="h-full">
          <table-data ref="tableData" :fields="fieldsPeserta" href="ekstra/ts/tsdac/peserta"
            class="*:mt-0" :show-delete="false"
            :checked="true"  :pass-columns="[]"
            :params="pesertaParams"  >
          </table-data>
        </div>
        <div v-else-if="activeTab == 'rekap'" class="h-full">
          <table-data ref="tableRekap" :fields="fieldsRekap" href="ekstra/ts/tsdac/penilaian/summary"
            class="*:mt-0" :show-dropdown="false" :show-upload="false" :show-create="false" :show-search="false"
            :checked="true"  :pass-columns="[]"
            :params="pesertaParams"  >
          </table-data>
        </div>
      </div>
  </div>
</template>

<style lang="postcss" scoped>
  deep(.add-transition) {
    transition: all 0.3s ease-in-out;
  }
</style>
<script>
import { range } from 'lodash';


export default {
    name: 'Sekretaris',
    components: {
    },
    data() {
      const optionsPartai = this.range(20, 1).map(i => {
        return {value: i, label: `Partai ke-${(i - 1) * 10 + 1} sampai ke-${(i * 10)}` }
      })
      return {
        activeTab: 'penilaian',
        filterPenilaianFields:{
          partai: {
            nama_kolom:'partai',
            label: 'Partai',
            input: 'select',
            options: optionsPartai,
            placeholder: 'Pilih Partai',
          },
        },
        filterPenilaian: {
          partai: 1,
        },
        fieldsPeserta:{
          nama: { nama_kolom:'nama', label: 'Nama', input: 'input'},
          kelas: { nama_kolom:'kelas', label: 'Kelas', input: 'input', 'max-width':'100px',align:'center' },
        },
        fieldsRekap:{
          nama: { nama_kolom:'nama', label: 'Nama', input: 'input'},
          kelas: { nama_kolom:'kelas', label: 'Kelas', input: 'input', 'max-width':'100px',align:'center' },
          total_nilai: { nama_kolom:'total_nilai', label: 'Total', input: 'input', 'max-width':'100px',align:'center' },
        },
        matchResults: [],
      }
    },
    computed: {
    },
    watch: {
      filterPenilaian:{
        deep: true,
        handler() {
          this.fetchData();
        }
      },
      activeTab(val) {
        if (val === 'rekap') {
          this.$nextTick(() => {
            this.$refs?.tableRekap?.getData()
          })
        } else if (val === 'peserta') {
          this.$nextTick(() => {
            this.$refs?.tableData?.getData()
          })
        } else if (val == 'penilaian') {
          this.$nextTick(() => {
            this.fetchData()
          })
        }
      }
    },
    methods: {
      fetchData() {
        this.$http.get('/ekstra/ts/tsdac/penilaian/get_match_results', {
          params: {
            partai: this.filterPenilaian.partai,
          }
        }).then(res => {
          this.matchResults = res.data;
          this.selectedMatch = null;
        }).catch(err => {
          console.error('Error fetching match results:', err);
        });
      },
      deleteMatch(macthInd, mInd, match){
        this.$confirm('Apakah anda yakin untuk menghapus data ini?',
          'Konfirmasi',
          {
            confirmButtonText: 'OK',
            cancelButtonText: 'Batal',
            type: 'warning',
          })
          .then(() => {
            let data = window.jsonToFormData({ id:[...match.ids_biru,...match.ids_kuning] })
            // console.log(this.href)
            this.$http.post(`ekstra/ts/tsdac/penilaian/delete_many`, data)
              .then(result => {
                this.$notify.success({
                  title: 'Berhasil',
                  message: 'Data berhasil dihapus',
                  position: 'bottom-right'
                });
                delete this.matchResults[macthInd].match[mInd]
              })
              .catch(err => {
                console.log(err)
                this.$notify.error({
                  title: 'Gagal',
                  message: 'Tidak dapat menghapus',
                  position: 'bottom-right'
                });
              });
        })       
      }
    },
    mounted() {
      this.$refs?.tableData?.getData()
    }
}
</script>
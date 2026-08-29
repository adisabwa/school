<template>
  <div id="presensi" class="py-2 overflow-x-hidden max-w-screen">
    <el-card class="bg-white/[0.7] mb-2">
      <form-comp ref="formFilter"
        :key="formKey"
        :fields="filterFields"
        :label-position="labelPosition"
        :form-class="'mt-2 mb-0'"
        label-width="150px"
        v-model:form-value="filter"
        :pass-columns="[]"
        :show-submit="false"
        :show-label="$windowWidth.value > 640"
        text-submit="Cari"
        error-submit-text="Tidak dapat mengambil data"
        :show-required-text="false"
        >
      </form-comp>
    </el-card>
    <el-card class="bg-white/[0.7]"
      body-class="p-0">
      <div class="bg-white/[0.7] h-fit px-3 pt-3 pb-2">
        <div class="text-right md:block hidden">
          <el-button size="small" type="success" @click="savePresensi">
            <icons icon="fluent:save-20-filled" /> Simpan
          </el-button>
        </div>
      </div>
      <div class="relative bg-white">
        <div :class="[scrollY > showHidden ? 'opacity-100' : 'opacity-0',
          'animate absolute right-0 z-[9999] bg-white/[0.7] h-fit',
          'px-3 pt-3 pb-2']"
          v-fixed-to-position="(this.scrollY - this.showHidden) ">
          <div class="text-right md:block hidden">
            <el-button size="small" type="success" @click="savePresensi">
              <icons icon="fluent:save-20-filled" /> Simpan
            </el-button>
          </div>
        </div>
        <div v-if="dataPresensi.length == 0"
          class="text-center text-gray-500 text-lg p-5">
          <icons icon="mdi:alert" class="text-[50px] mb-3" />
          <div class="text-[18px]">Data Jadwal belum dimasukkan</div>
        </div>
        <div v-else>
          <div id="freeze-container" class="mx-3 overflow-x-hidden w-full h-full">
          </div>
          <div class="mx-3 overflow-x-auto" @scroll="(event) => {
            let tFreezeHead = jquery('#table-freeze-head')
            let target = event.target
            let scrollLeft = target.scrollLeft
            let left = scrollLeft - 13
            tFreezeHead.css({left: -left + 'px'})
          }">
            <div class="min-w-[600px]"> 
              <table id="table-base" class=" table mt-1 md:text-[14px] text-[12px] leading-[1.5]">
                <thead class="bg-slate-100 [&_*]:border [&_*]:border-solid [&_*]:border-slate-300">
                  <tr>
                    <th width="15px" class="fixed-col">No</th>
                    <th class="text-center fixed-col" width="30px">Kelas</th>
                    <th class="">Guru / Mapel</th>
                    <th class="text-center w-[180px]" width="180px">Kejadian</th>
                    <th width="150px" class="text-center">Tugas</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(data, key) in dataPresensi">
                    <td class="align-top">{{ key + 1 }}</td>
                    <td class="text-center align-top">{{ data.kelas }}</td>
                    <td class="align-top">
                      <div class="">{{ data.nama_guru }}</div>
                      <div>{{ data.nama_mapel }}</div>
                    </td>
                    <td class="align-top" width="180px">
                      <el-select v-model="data.kehadiran"
                        class=""
                        size="large" clearable filterable>
                        <el-option v-for="opt in (fields?.kehadiran?.options ?? [])" 
                          :value="opt.value"
                          :label="opt.label"
                          />
                      </el-select>
                      <el-input v-if="data.kehadiran == 'lainnya'"
                        v-model="data.lainnya" 
                        size="large" class="mt-2"
                        placeholder="Masukkan keterangan lainnya"/>
                      <template v-if="['terlambat','terlambat dengan izin','keluar sebelum bel'].includes(data.kehadiran)">
                        <el-input type="number" v-model="data.keterlambatan" 
                          size="large" placeholder="Waktu"
                          class="mt-2 [&_.el-input-group\_\_append]:px-2">
                          <template #append>Menit</template>
                        </el-input>
                      </template>
                      <el-input v-if="!['hadir','tanpa keterangan'].includes(data.kehadiran)"
                        type="textarea" v-model="data.alasan" 
                        size="large" class="mt-2" :rows="3"
                        :placeholder="'Masukkan alasan ' + (['terlambat','terlambat dengan izin','keluar sebelum bel'].includes(data.kehadiran) ? 
                        'keterlambatan' : 'ketidak hadiran')"/>
                    </td>
                    <td class="text-center align-top">
                      <template v-if="data.kehadiran != 'hadir'">
                        <div class="flex items-center">
                          <el-checkbox true-value="1" false-value="0"
                            v-model="data.tugas" />
                          <div class="ml-2"> {{ data.tugas ? '' : 'Tidak ' }} Ada Tugas</div>
                        </div>
                        <div v-if="data.tugas == '1'">
                          <floating-select v-model:value="data.id_pengganti" placeholder="Pilih Guru Pendamping" 
                            filterable clearable
                            size="default"
                            :options="fields.id_pengganti.options"/>
                        </div>
                      </template>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </el-card>
  </div>
</template>
  
<script>
  import { mapState } from 'pinia';
import { useAuthStore } from '@/config/stores/authStore'
import { useDataStore } from '@/config/stores/dataStore'
  
  
  export default {
    name: "mapel",
    components: {
      
    },
    data: function() {
      return {
        loading: false,
        saving: false,
        filterFields: {
          id_semester:{
            label:'Semester',
            nama_kolom:'id_semester',
            input:'select',
            options:[],
          },
          tanggal:{
            label:'Tanggal',
            nama_kolom:'tanggal',
            input:'date-wheel',
            format:'dddd, DD MMMM YYYY',
          },
          id_kelas:{
            label:'Kelas',
            nama_kolom:'id_kelas',
            input:'select',
            options:[],
          },
          id_sesi:{
            label:'Sesi',
            nama_kolom:'id_sesi',
            input:'select',
            options:[],
          },
        },
        fields:{},
        filter:{
          id_semester: '',
          komplek: '',
          tanggal:'',
          id_sesi: '',
        },
        params:{
          where:[],
        },
        editId:-1,
        ids:[],
        formKey:0,
        dataPresensi:[],
        scrollY:0,
        promptDinas:false ,
        presensiMin:78,
        presensiMax:0,
        showHidden: 286,
        // role:'walas',
      };
    },
    watch: {
      'filter.id_semester' (val) {
        // console.log('id',val)
        this.getData()
      },
      'filter.id_sesi' (val) {
        // console.log('id',val)
        this.getData()
      },
      'filter.komplek' (val) {
        // console.log('id',val)
        this.getData()
      },
      promptDinas(val){
        if (val) {
          let max = 0
          this.dataPresensi.forEach(d => {
            let rap = d.presensi.presensi_rapor
            if (rap > max) max = rap
          })
          this.presensiMax = max
        }
      },
      dataPresensi:{
        deep: true,
        handler(val){
          setTimeout(() => {
            this.getFreezeHeader()
          }, 300)
        }
      }
    },  
    computed: {
      ...mapState(useAuthStore, {
        user: 'loggedUser',
        role: 'role',
      }),
      ...mapState(useDataStore, {
        storeFilters: 'filters',
      }),
      labelPosition(){
        return this.$windowWidth.value < 500 ? 'top' : 'left'
      },
    },
    methods: {
      searchData(){
        this.params.where = Object.fromEntries(
          Object.entries(this.filter).filter(([key, value]) => value)
        )
      },
      getInitial: async function() {
          // this.loading = true;
        let date = this.dateNow()
        this.filter.tanggal = date
        // let time = this.timeNow()
        let time = '07:40'
        this.filter.komplek = 'putra'
        await this.$http.get('data/semester/options')
          .then(result => {
            let res = result.data
            this.filterFields.id_semester.options = res
            console.log('semester', res)
            res.forEach(r => {
              // console.log('semester', r.tanggal_mulai, date, r.tanggal_selesai, time, r.tanggal_mulai <= date && r.tanggal_selesai >= date)
              if (r.tanggal_mulai <= date && r.tanggal_selesai >= date) {
                // console.log('semester', r)
                this.filter.id_semester = r.value
              }
            })
            // console.log('filter', this.filter)
          }).
          catch(err => {
            console.error('Error fetching semester options:', err);
          });
        await this.$http.get('data/kelas/options')
          .then(result => {
            let res = result.data
            this.filterFields.id_kelas.options = res
            this.filter.id_kelas = res[0].value
          }).
          catch(err => {
            console.error('Error fetching semester options:', err);
          });
        await this.$http.get('data/sesi/options')
          .then(result => {
            let res = result.data
            this.filterFields.id_sesi.options = res
            // console.log('sesi', res)
            res.forEach(r => {
              if (r.waktu_mulai <= time && r.waktu_selesai >= time) {
                // console.log('sesi', r)
                this.filter.id_sesi = r.value
              }
            })
            this.formKey++
          }).
          catch(err => {
            console.error('Error fetching semester options:', err);
          });
        await this.$http.get('/kolom/preparation?table=' + this.$prefixTable + 'pre_mengajar_kelas&grouping=0&input=0')
            .then(result => {
              var res = result.data;
              this.fields = res
            });
        console.log('filter', this.filter)
      },
      getData(){
        this.$http.get('presensi/admin/kelas',{
          params: this.filter
        }).then(result => {
          this.dataPresensi = result.data
        })
      },
      getFreezeHeader(){
        let tBase = jquery('#table-base')
        let tFreezeContainer = jquery('#freeze-container')
        tFreezeContainer.empty()

        let tBodyFreeze = tBase.clone(true)
        this.removeColumnByClass(tBodyFreeze, [], 'fixed-col')
        tBodyFreeze.attr('id', 'table-freeze-body')
        tBodyFreeze.appendTo(tFreezeContainer)
        tBodyFreeze.css({position:'absolute'})

        let tHeadFreeze = tBase.clone(true).find('tbody').remove().end()
        tHeadFreeze.attr('id', 'table-freeze-head')
        tHeadFreeze.appendTo(tFreezeContainer) 
        tHeadFreeze.css({position:'absolute'})

        let tHeadBodyFreeze = tBodyFreeze.clone().find('tbody').remove().end()
        tHeadBodyFreeze.attr('id', 'table-freeze-head-body')
        tHeadBodyFreeze.appendTo(tFreezeContainer) 
        tHeadBodyFreeze.css({position:'absolute'})
        // console.log(tBodyFreeze)
        // console.log(tHeadFreeze.width(), tBase.width())

        let thBase = tBase.find('th')
        let trBaseHead = tBase.find('thead tr')
        let trBaseBody = tBase.find('tbody tr')
        let thBodyFreeze = tBodyFreeze.find('th')
        
        let thFreeze = tHeadFreeze.find('th')
        let thHeadBodyFreeze = tHeadBodyFreeze.find('th')
        // // console.log(tHeadFreeze, tBase, tFreeze, thFreeze)
        let trBodyFreezeHead = tBodyFreeze.find('thead tr')
        let trBodyFreezeBody = tBodyFreeze.find('tbody tr')

        let keys = Object.keys(thFreeze)
        
        for (let i = 0; i < keys.length; i++) {
          const key = keys[i]
          let elBase = jquery(thBase[key])
          let elFreeze = jquery(thFreeze[key])
          let elBodyFreeze = jquery(thBodyFreeze[key])
          let elHeadBodyFreeze = jquery(thHeadBodyFreeze[key])
          // console.log(elBase, elFreeze)
          // continue;
          try {
            // console.log(elBase.width(), elFreeze.width())
            elFreeze.width(elBase.width())
            elBodyFreeze.width(elBase.width())
            elHeadBodyFreeze.width(elBase.width())
            
            // jquery(elFreeze).width(elBase.width)
        //     elFreeze.style.width = elBase.outerWidth + 'px'
        //     // let baseF = Math.ceil(elFreeze.width)
        //     // elBase.width(baseF)
        //     console.log(elBase.width, elFreeze.width)
          } catch(err) {
            // console.log(err)
          }
        }

        trBodyFreezeHead.each((index, tr) => {
          let trF = jquery(jquery(tr).find('th')[0])
          let trB = jquery(jquery(trBaseHead[index]).find('th')[0])
          trF.height(trB.height()) 
        })

        trBodyFreezeBody.each((index, tr) => {
          let trF = jquery(jquery(tr).find('td')[0])
          let trB = jquery(jquery(trBaseBody[index]).find('td')[0])
          // console.log(trF, trB)
          trF.height(trB.height())
          // console.log(trF.height(), trB.height())
        })
          // var targetOffset = tBase[0].getBoundingClientRect(); // relative to viewport
          // tFreeze.css({
          //   left: targetOffset.left + 'px'
          // });
        this.addStyletoHeader()
        
      },
      addStyletoHeader(){
        let tHeadFreeze = jquery('#table-freeze-head')
        let tHeadBodyFreeze = jquery('#table-freeze-head-body')
        let tBase = jquery('#table-base')
        let tBodyFreeze = jquery('#table-freeze-body')
        tBodyFreeze.css({
          zIndex: 9997,
          width: 'auto',
          height: 'auto',
          // height: tBase.height() + 'px',
        })
        tHeadFreeze.css({
          zIndex: 9998,
          top: (this.scrollY - this.showHidden - 50) + 'px',
          width: tBase.width() + 'px',
          opacity: this.scrollY < this.showHidden ? 0 : 1,
        })
        tHeadBodyFreeze.css({
          zIndex: 9999,
          top: (this.scrollY - this.showHidden - 50) + 'px',
          width: tBodyFreeze.width() + 'px',
          height: tHeadFreeze.height() + 'px',
          opacity: this.scrollY < this.showHidden ? 0 : 1,
        })
      },
      // countRapor(key){
      //   let presensi = this.dataPresensi[key].presensi
      //   this.dataPresensi[key].presensi.presensi_rapor = Math.round((presensi.presensi_harian + presensi.uts * 2 + presensi.uas * 3) / 6 * 100)  / 100
      // },
      // generateDinas(){
      //   let max = this.presensiMax
      //   let min = this.presensiMin
      //   let real_min = 999
      //   let real_max = -1
      //   this.dataPresensi.forEach(d => {
      //     let rap = d.presensi.presensi_rapor
      //     if (rap < real_min) real_min = rap
      //     if (rap > real_max) real_max = rap
      //   })

      //   this.dataPresensi.forEach(d => {
      //     let rap = d.presensi.presensi_rapor
      //     let katrol1 = min + ( ( rap - real_min ) / ( real_max - real_min ) * ( max - min ) )
      //     let katrol2 = katrol1 + 1
      //     d.presensi.katrol1 = this.rounding(katrol1, 2)
      //     d.presensi.katrol2 = this.rounding(katrol2, 2)
      //   })

      //   this.promptDinas = false
      // },
      savePresensi() {
        let form = []
        this.dataPresensi.forEach(d => {
          // console.log(d)
          form.push({
            id:d.id,
            'id_semester' : d.id_semester,
            'id_sesi' : d.id_sesi,
            'tanggal' : d.tanggal,
            'id_kelas' : d.id_kelas,
            'id_mapel' : d.id_mapel,
            'id_guru' : d.id_guru,
            'kode_mapel' : d.kode_mapel,
            'kehadiran' : d.kehadiran,
            'lainnya' : d.lainnya,
            'keterlambatan' : d.keterlambatan,
            'tugas' : d.tugas,
            'alasan' : d.alasan,
            'seragam' : d.seragam,
            'id_pengganti' : d.id_pengganti > 0 ? d.id_pengganti : null,
          })
        })
        form = window.jsonToFormData(form)
        this.$http.post('presensi/admin/kelas/store_many', form)
          .then(res => {
            this.getData()
            this.$notify.success({
              title: 'Berhasil',
              message: 'presensi berhasil disimpan',
              position: 'bottom-right'
            });
          })
          .catch(err => {
            console.log(err)
            this.$notify.error({
              title: 'Gagal',
              message: 'presensi tidak berhasil disimpan',
              position: 'bottom-right'
            });
          })
      }
    },
    created: function() {
      this.getInitial()
      // console.log(this.$router);
    },
    mounted(){
      window.addEventListener('scroll', () => {
        this.scrollY = window.scrollY
        this.addStyletoHeader()
        // console.log(this.scrollY)
      })
    },
    beforeUnmount() {
      // let dataStore = useDataStore()
      // Object.entries(this.filter).forEach(([index, val]) =>
      //   dataStore.setFilter({
      //     key:index,
      //     val:val
      //   })
      // )
      // console.log('change-filter', dataStore.filters)
    },
  }
</script>
  
<style lang="postcss" scoped>
  :deep(.el-checkbox__inner) {
    @apply border border-solid border-cyan-700;
  }
</style>
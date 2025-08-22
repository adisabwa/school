<template>
    <div id="presensi" class="py-2">
      <el-card class="bg-white/[0.7]">
        <form-comp ref="formFilter"
          :key="formKey"
          :fields="filterFields"
          :label-position="labelPosition"
          :form-class="'mt-2 mb-0'"
          label-width="150px"
          v-model:form-value="filter"
          :pass-columns="[]"
          :show-submit="false"
          text-submit="Cari"
          error-submit-text="Tidak dapat mengambil data"
          :show-required-text="false"
          >
        </form-comp>
        <teleport to="body">
          <div :class="[scrollY > 286 ? 'opacity-100' : 'opacity-0',
            'animate fixed top-[50px] flex z-[9999] right-11 bg-white/[0.7]']">
            <div class="py-2 px-4">
              <el-button size="default" type="success" @click="downloadDinas">
                <icons icon="ri:file-excel-2-fill" /> Template Dinas
              </el-button>
              <el-button size="default" type="primary" @click="promptDinas = true">
                <icons icon="ic:twotone-create" /> Generate Raport Dinas
              </el-button>
              <el-divider direction="vertical" />
              <el-button size="default" type="success" @click="saveScore">
                <icons icon="fluent:save-20-filled" /> Simpan
              </el-button>
            </div>
          </div>
        </teleport>
        <el-card class="shadow-none">
          <div :class="[scrollY > 286 ? 'opacity-0' : 'opacity-100'],
            'animate'">
            <div class="text-right">
              <el-button size="default" type="success" @click="downloadDinas">
                <icons icon="ri:file-excel-2-fill" /> Template Dinas
              </el-button>
              <el-button size="default" type="primary" @click="promptDinas = true">
                <icons icon="ic:twotone-create" /> Generate Raport Dinas
              </el-button>
              <el-divider direction="vertical" />
              <el-button size="default" type="success" @click="saveScore">
                <icons icon="fluent:save-20-filled" /> Simpan
              </el-button>
            </div>
          </div>
          <teleport to="body">
            <el-dialog  
              v-model="promptDinas"
              class="p-7 w-[400px]"
              :close-on-click-modal="true">
              <template #header>
                <b>Setting Raport Dinas</b>
              </template>
              <b>Masukkan presensi minimal dan presensi maksimal terlebih dahulu</b>
              <div class="flex gap-4 mt-4">
                <div class="flex flex-col">
                  <label class="font-semibold mb-1">presensi Minimal</label>
                  <el-input size="large" v-model="presensiMin"
                    placeholder="presensi Terkecil" />
                </div>
                <div class="flex flex-col">
                  <label class="font-semibold mb-1">presensi Maksimal</label>
                  <el-input size="large" v-model="presensiMax"
                    placeholder="presensi Terbesar" />
                </div>
              </div>
              <template #footer>
                <el-button @click="promptDinas = false">Batal</el-button>
                <el-button 
                  type="success" 
                  @click="generateDinas()" :icon="saving ? 'el-icon-loading' : ''" 
                  :disabled="saving">Generate</el-button>
              </template>
            </el-dialog>
          </teleport>
          <!-- <table class="table  mt-3">
            <thead>
              <tr>
                <th rowspan="2" width="20px">No</th>
                <th rowspan="2">Nama</th>
                <th rowspan="2" width="80px" class="text-center">presensi Harian</th>
                <th rowspan="2" width="80px" class="text-center">UTS</th>
                <th rowspan="2" width="80px" class="text-center">UAS</th>
                <th rowspan="2" width="80px" class="text-center">Raport</th>
                <th colspan="2" class="text-center">presensi Raport Dinas</th>
              </tr>
              <tr>
                <th width="80px" class="text-center">presensi 1</th>
                <th width="80px" class="text-center">presensi 2</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(data, key) in datapresensi">
                <td>{{ key + 1 }}</td>
                <td>{{ data.nama }}</td>
                <td v-for="ujian in ['presensi_harian','uts','uas']">
                  <el-input v-model="data.presensi[ujian]" size="large"
                    @change="data.presensi[ujian] = checkMinMax(rounding(data.presensi[ujian],2), 0, 100)
                      countRapor(key);"
                    class="w-full" />
                </td>
                <td class="text-center">{{ data.presensi.presensi_rapor }}</td>
                <td class="text-center">{{ data.presensi.katrol1 }}</td>
                <td class="text-center">{{ data.presensi.katrol2 }}</td>
              </tr>
            </tbody>
          </table> -->
        </el-card>
      </el-card>
    </div>
</template>
  
<script>
  import { mapState } from 'pinia';
  
  
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
          komplek:{
            label:'Komplek',
            nama_kolom:'komplek',
            input:'select',
            options:[
              { value: 'putra', label: 'Putra' },
              { value: 'putri', label: 'Putri' },
            ],
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
          id_sesi: '',
        },
        params:{
          where:[],
        },
        editId:-1,
        ids:[],
        formKey:0,
        datapresensi:[],
        scrollY:0,
        promptDinas:false ,
        presensiMin:78,
        presensiMax:0,
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
          this.datapresensi.forEach(d => {
            let rap = d.presensi.presensi_rapor
            if (rap > max) max = rap
          })
          this.presensiMax = max
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
        return this.$windowWidth < 800 ? 'top' : 'left'
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
        let time = this.timeNow()
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
        console.log('filter', this.filter)
      },
      getData(){
        this.$http.get('mapel/kmi/record',{
          params: this.filter
        }).then(result => {
          this.datapresensi = result.data
        })
      },
      // countRapor(key){
      //   let presensi = this.datapresensi[key].presensi
      //   this.datapresensi[key].presensi.presensi_rapor = Math.round((presensi.presensi_harian + presensi.uts * 2 + presensi.uas * 3) / 6 * 100)  / 100
      // },
      // generateDinas(){
      //   let max = this.presensiMax
      //   let min = this.presensiMin
      //   let real_min = 999
      //   let real_max = -1
      //   this.datapresensi.forEach(d => {
      //     let rap = d.presensi.presensi_rapor
      //     if (rap < real_min) real_min = rap
      //     if (rap > real_max) real_max = rap
      //   })

      //   this.datapresensi.forEach(d => {
      //     let rap = d.presensi.presensi_rapor
      //     let katrol1 = min + ( ( rap - real_min ) / ( real_max - real_min ) * ( max - min ) )
      //     let katrol2 = katrol1 + 1
      //     d.presensi.katrol1 = this.rounding(katrol1, 2)
      //     d.presensi.katrol2 = this.rounding(katrol2, 2)
      //   })

      //   this.promptDinas = false
      // },
      // saveScore() {
      //   let form = []
      //   this.datapresensi.forEach(d => {
      //     console.log(d)
      //     form.push({
      //       id:d.id,
      //       id_pembagian_mapel: d.id_pembagian_mapel,
      //       id_santri: d.id_santri,
      //       presensi_harian: d.presensi.presensi_harian,
      //       uts: d.presensi.uts,
      //       uas: d.presensi.uas,
      //       presensi_rapor: d.presensi.presensi_rapor,
      //       katrol1: d.presensi.katrol1,
      //       katrol2: d.presensi.katrol2,
      //     })
      //   })
      //   form = window.jsonToFormData(form)
      //   this.$http.post('mapel/presensi/store_many', form)
      //     .then(res => {
      //       this.getData()
      //       this.$notify.success({
      //         title: 'Berhasil',
      //         message: 'presensi berhasil disimpan',
      //         position: 'bottom-right'
      //       });
      //     })
      //     .catch(err => {
      //       console.log(err)
      //       this.$notify.error({
      //         title: 'Gagal',
      //         message: 'presensi tidak berhasil disimpan',
      //         position: 'bottom-right'
      //       });
      //     })
      // }
    },
    created: function() {
      this.getInitial()
      // console.log(this.$router);
    },
    mounted(){
      window.addEventListener('scroll', () => {
        this.scrollY = window.scrollY
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
  
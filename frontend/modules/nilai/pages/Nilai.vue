<template>
    <div id="nilai" class="py-2">
      <el-card class="bg-white/[0.7] mb-2">
        <form-comp ref="formFilter"
          :key="formKey"
          :fields="filterFields"
          size="default"
          label-position="left"
          :show-label="showLabel"
          class="max-sm:mt-4"
          form-class="m-0"
          form-item-class="mb-2"
          label-width="150px"
          v-model:form-value="filter"
          :pass-columns="[]"
          :show-submit="false"
          text-submit="Cari"
          error-submit-text="Tidak dapat mengambil data"
          :show-required-text="false"
          >
        </form-comp>
      </el-card>
      <el-card class="bg-white/[0.7]"
        body-class="p-0">
        <div :class="[$scrollY.value > showHidden ? 'opacity-0' : 'opacity-100',
          'animate px-3 pt-3 pb-2']">
          <div class="text-right">
            <template v-if="role == 'guru' || role == 'admin'">
              <el-button size="small" type="success" @click="downloadScoreTemplate">
                <icons icon="mdi:download" /> Unduh Template
              </el-button>
              <el-button size="small" type="success" @click="saveRapor">
                <icons icon="fluent:save-20-filled" /> Simpan
              </el-button>
            </template>
          </div>
        </div>
        <div class="relative bg-white">
          <div :class="[$scrollY.value > showHidden ? 'opacity-100 z-[9999]' : 'opacity-0 z-[-1]',
            'animate fixed right-0 bg-white/[0.7] h-fit',
            'px-3 pt-3 pb-2']"
            v-fixed-to-position="'50px'">
            <div class="text-right">
              <template v-if="role == 'guru' || role == 'admin'">
                <el-button size="small" type="success" @click="downloadScoreTemplate">
                  <icons icon="mdi:download" /> Unduh Template
                </el-button>
                <el-button size="small" type="success" @click="saveRapor">
                  <icons icon="fluent:save-20-filled" /> Simpan
                </el-button>
              </template>
            </div>
          </div>
          <loading v-if="loading" />
          <div v-else-if="dataNilai.length == 0"
            class="text-center text-gray-500 text-lg p-5">
            <icons icon="mdi:alert" class="text-[50px] mb-3" />
            <div class="text-[18px]">Tidak ada data nilai</div>
          </div>
          <div v-else class="mb-12">
            <!-- <div class="mx-3">
              <el-switch />
              <b class="ml-3">Wali Kelas dan Admin boleh mengedit nilai</b>
            </div> -->
            <div class="mx-4 my-2">Inputkan Nilai / Copy-Paste dari File Excel</div>
            <table-freeze ref="tableFreeze" :data="dataNilai">
              <template #header="{data}">
                <tr class="*:border *:border-solid *:border-slate-300">
                  <th width="20px" class="fixed-col">No</th>
                  <th class="fixed-col">Nama</th>
                  <template v-for="(ujian) in listUjians">
                    <th class="text-center">
                      <div class="flex items-center justify-center">
                        <el-tooltip content="Reset Data" placement="bottom-start"
                          v-if="allowEdit[ujian]">
                          <icons icon="ri:reset-left-line" class="border-0 cursor-pointer" @click="resetData(ujian)"/>
                        </el-tooltip>
                        {{ ujian.toUpperCase().replace('_',' ') }}
                      </div>
                    </th>
                  </template>
                  <th class="text-center">Raport</th>
                  <th class="text-center">Raport Dinas</th>
                  <th class="text-center" v-if="is3Akhir">Konversi UM</th>
                </tr>
              </template>
              <template #body="{data}">
                <tr v-for="(data, key) in data">
                  <td>{{ key + 1 }}</td>
                  <td>{{ data.nama }}</td>
                  <td v-for="(ujian) in listUjians" class="text-center">
                    <el-input v-if="allowEdit[ujian]"
                      v-model="data.nilai[ujian]" size="large"
                      @focus="(event) => {  }"
                      @change="data.nilai[ujian] = checkMinMax(rounding(data.nilai[ujian],2), 10, 99)
                        countRapor(key);"
                      @paste="(event) => { handlePaste(event, key, ujian)}"
                      :class="[allowEdit[ujian] ? 'ml-[20px]' : '',
                        'w-[70px]']" />
                    <span v-else>
                      {{ data.nilai[ujian] }}
                    </span>
                  </td>
                  <td class="text-center">{{ data.nilai.nilai_rapor }}</td>
                  <td class="text-center">{{ data.nilai.katrol1 }}</td>
                  <td class="text-center" v-if="is3Akhir">
                    {{ data.nilai.katrol2 }}
                  </td>
                </tr>
              </template>
            </table-freeze>
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
    setup(){
      return {
        checkMinMax, rounding,
      }
    },
    components: {
      
    },
    data: function() {
      return {
        initial:false,
        loading: false,
        saving: false,
        filterFields: {
          id_semester:{
            label:'Semester',
            nama_kolom:'id_semester',
            input:'select',
            options:[],
          },
          id_kelas:{
            label:'Kelas',
            nama_kolom:'id_kelas',
            input:'select',
            options:[],
          },
          id_mapel:{
            label:'Mata Pelajaran',
            nama_kolom:'id_mapel',
            input:'select',
            options:[],
          },
        },
        filter:{
          id_semester:'',
          id_kelas:'',
          id_mapel:'',
        },
        params:{
          where:[],
        },
        editId:-1,
        ids:[],
        formKey:0,
        dataNilai:[],
        nilaiMin:78,
        nilaiMax:99,
        showHidden: 286,
        PembagianMapel:{},
        // role:'guru',
      };
    },
    watch: {
      'filter.id_semester' (val){
        if (!this.initial) {
          let semester = this.filterFields.id_semester.options.find(d => d.value == val)
          this.filterFields.id_kelas.options = semester?.options ?? []
          this.filter.id_kelas = -1
          setTimeout(() => {
            this.filter.id_kelas = this.filterFields.id_kelas.options[0]?.value
          }, 100)
        }
      },
      'filter.id_kelas' (val){
        if (!this.initial) {
          let kelas = this.filterFields.id_kelas.options.find(d => d.value == val)
          this.filterFields.id_mapel.options = kelas?.options ?? []
          // console.log(this.filter.id_mapel, this.filterFields.id_mapel.options[0].value, 'selected kelas')
          if (this.filter.id_mapel == this.filterFields.id_mapel.options[0]?.value) {
            console.log('get data')
            this.getData()
          } else {
            this.filter.id_mapel = this.filterFields.id_mapel.options[0]?.value
          }
          // this.getData()
        }
      },
      'filter.id_mapel' (val) {
        let data = this.filterFields.id_mapel.options.filter(d => d.value == val)[0] ?? ''
        this.PembagianMapel = data?.pembagian_mapel ?? {}
        this.getData()
      },
    },  
    computed: {
      ...mapState(useAuthStore, {
        user: 'loggedUser',
        role: 'role',
      }),
      ...mapState(useDataStore, {
        storeFilters: 'filters',
      }),
      showLabel(){
        return this.$windowWidth.value > 800
      },
      allowEdit(){
        let access = (this.role == 'admin' || this.role == 'guru' || this.PembagianMapel.allow_access == '1')
        // access = true
        // console.log(access)
        let data = {
          nilai_harian: access && this.PembagianMapel['lock_nilai_harian'] == '0',
          uts: access && this.PembagianMapel['lock_uts'] == '0',
          um: access && this.PembagianMapel['lock_uas'] == '0',
          uas: access && this.PembagianMapel['lock_uas'] == '0',
        }
        // data.uas = true
        // console.log(data)
        return data
      },
      tingkat(){
        let options = this.filterFields.id_kelas.options
        if (options) {
          if (typeof options == 'object') options = Object.values(options)
          let kelas = options.find(d => d.value == this.filter.id_kelas)
          return kelas?.tingkat ?? '1'
        } else {
          return '1'  
        }
      },
      id_jurusan(){
        let options = this.filterFields.id_kelas.options
        if (options) {
          if (typeof options == 'object') options = Object.values(options)
          let kelas = options.find(d => d.value == this.filter.id_kelas)
          return kelas?.id_jurusan ?? null
        } else {
          return null 
        }
      },
      is3Akhir(){
        return this.tingkat == '3' && (this.filter.id_semester % 2) == 0
      },
      is6Akhir(){
        return this.tingkat == '6' && (this.filter.id_semester % 2) == 0
      },
      listUjians(){
        // console.log('tingkat', this.tingkat)
        if (this.PembagianMapel.is_praktik == '1')
          return ['uas']
        if (this.is3Akhir)
          return ['nilai_harian', 'uas', 'um']
        else if (this.is6Akhir)
          return ['nilai_harian', 'uas']
        else
          return ['nilai_harian', 'uts', 'uas']
      },
        
    },
    methods: {
      searchData(){
        this.params.where = Object.fromEntries(
          Object.entries(this.filter).filter(([key, value]) => value)
        )
      },
      getInitial: async function() {
          this.loading = true;
          // console.log(this.storeFilters)
          let where = {}
          switch (this.role) {
            case 'guru':
              where.id_guru = this.user.id_guru
              break;
            case 'walas':
              where.id_kelas = this.user.id_kelas
              // where.id_kelas = 1
              break;
            default:
              break;
          }
          this.$http.get('mapel/admin/pembagian/options',{
            params:{
              where:where
            }
          })
            .then(res => {
              this.initial = true
              let data = res.data
              this.filterFields.id_semester.options = data
              this.filter.id_semester = this.storeFilters?.id_semester ? this.storeFilters?.id_semester : Object.values(data).at(-1)?.value
              let semester = data.find(d => d.value == this.filter.id_semester)
              this.filterFields.id_kelas.options = semester?.options ?? []
              this.filter.id_kelas = this.storeFilters?.id_kelas ? this.storeFilters?.id_kelas : this.user.id_kelas ?? Object.values(this.filterFields.id_kelas.options)[0]?.value
              let kelas = this.filterFields.id_kelas.options.find(d => d.value == this.filter.id_kelas)
              this.filterFields.id_mapel.options = kelas?.options ?? []
              if (this.storeFilters?.nama_mapel) {
                // console.log(this.storeFilters?.nama_mapel)
                let filter = this.filterFields.id_mapel.options.filter(o => {
                  let nama_mapel = o.label.slice(0, o.label.indexOf('(') - 1);
                  // console.log(nama_mapel)
                  return nama_mapel == this.storeFilters?.nama_mapel
                })
                // console.log(filter, filter[0])
                this.filter.id_mapel = filter[0]?.value
              } else
                this.filter.id_mapel = this.storeFilters?.id_mapel ? this.storeFilters?.id_mapel : Object.values(this.filterFields.id_mapel.options)[0]?.value
              setTimeout(() => {
                this.initial = false
              },1000)    
          })
        },
      getData(){
        this.loading = true
        this.$http.get('nilai',{
          params: {
            id_semester: this.filter.id_semester,
            id_kelas: this.filter.id_kelas,
            id_mapel: this.filter.id_mapel,
          }
        }).then(result => {
          this.loading = false
          this.dataNilai = result.data
        })
      },
      async handlePaste(event, key, _ujian) {
        let currInd = this.listUjians.findIndex(d => d == _ujian)
        // this.loading = true;
        // console.log(event)
        const pastedData = await event.clipboardData.getData('Text');
        // console.log(pastedData)
        // Process the pastedData (e.g., split by lines and tabs/commas)
        // Example: Simple parsing for tab-separated values
        // console.log(data)
        const rows = pastedData.split('\r\n');
        // console.log(rows)
        // rows.pop()
        const parsedRows = rows.map(row => row.replace('\r','').split('\t'));
        // console.log(parsedRows)
        parsedRows.forEach((rows, ind) => {
          let no_nilai = key + ind
          rows.forEach( (cell, num) => {
            let uj = this.listUjians[(currInd + num)]
            if (this.dataNilai[no_nilai]) {
              // console.log(no_nilai)
              if (this.allowEdit[uj]) {
                // console.log('uj', this.dataNilai[no_nilai].nilai[uj])
                this.dataNilai[no_nilai].nilai[uj] = this.checkMinMax(cell, 40, 99)
                this.countRapor(no_nilai)
              }
            }
          })
        })
        // this.loading = false;
        event.preventDefault(); // Prevent default paste behavior if needed


      },
      resetData(ujian){
        this.dataNilai.forEach(d => {
          d.nilai[ujian] = 0
        })
      },
      countRapor(key){
        let nilai = this.dataNilai[key].nilai
        if (this.PembagianMapel.is_praktik == '1') {
          this.dataNilai[key].nilai.nilai_rapor = nilai.uas
          return
        }
        this.dataNilai[key].nilai.nilai_rapor = this.is3Akhir || this.is6Akhir ?
          Math.round((parseInt(nilai.nilai_harian) + parseInt(nilai.uas) * 2) / 3) :
          Math.round((parseInt(nilai.nilai_harian) + parseInt(nilai.uts) * 2 + parseInt(nilai.uas) * 3) / 6)
        // this.generateDinas(key)
      },
      generateDinas(key){
        let max = this.nilaiMax
        let min = this.nilaiMin
        let real_min = 40
        let real_max = 99

        let d = this.dataNilai[key]
        let rap = d.nilai.nilai_rapor
        if (rap < real_min) rap = real_min
        let katrol1 = min + ( ( rap - real_min ) / ( real_max - real_min ) * ( max - min ) )
        let katrol2 = katrol1 + 1
        if (katrol1 == NaN) katrol1 = 0
        if (katrol2 == NaN) katrol2 = 0
        d.nilai.katrol1 = this.rounding(katrol1, 2)
        d.nilai.katrol2 = this.rounding(katrol2, 2)
      },
      saveRapor() {
        let form = []
        this.dataNilai.forEach(d => {
          // console.log(d)
          form.push({
            id:d.id_rapor,
            id_semester: d.id_semester,
            id_kelas: d.id_kelas,
            id_santri: d.id_santri,
          })
        })
        form = window.jsonToFormData(form)
        this.$http.post('rapor/store_many', form)
          .then(res => {
            let rapor = res.data
            this.saveScore(rapor)
          })
          .catch(err => {
            console.log(err)
            this.$notify.error({
              title: 'Gagal',
              message: 'Nilai tidak berhasil disimpan',
              position: 'bottom-right'
            });
          })
      },
      saveScore(rapor) {
        let form = []
        this.dataNilai.forEach((d , key)=> {
          // console.log(d)
          let r = rapor.find(r => r.id_santri == d.id_santri)
          form.push({
            id:d.id,
            id_rapor: r.id,
            id_mapel: d.id_mapel,
            nilai_harian: d.nilai.nilai_harian,
            uts: d.nilai.uts,
            uas: d.nilai.uas,
            um: d.nilai.um,
            nilai_rapor: d.nilai.nilai_rapor,
            katrol1: d.nilai.katrol1,
            katrol2: d.nilai.katrol2,
          })
        })
        form = window.jsonToFormData(form)
        this.$http.post('nilai/store_many', form)
          .then(res => {
            this.generateRaportDinas()
            this.generateRanking()
            this.$notify.success({
              title: 'Berhasil',
              message: 'Nilai berhasil disimpan',
              position: 'bottom-right'
            });
          })
          .catch(err => {
            console.log(err)
            this.$notify.error({
              title: 'Gagal',
              message: 'Nilai tidak berhasil disimpan',
              position: 'bottom-right'
            });
          })
      },
      generateRanking(){
        this.$http('rapor/count_ranking', {
          params: {
            id_semester: this.filter.id_semester,
            id_kelas: this.filter.id_kelas,
            tingkat: this.tingkat,
            id_jurusan: this.id_jurusan,
          }
        }).then(res => {
        })
      },
      generateRaportDinas(){
        this.$http('rapor/get_nilai_rdm', {
          params: {
            id_semester: this.filter.id_semester,
            tingkat: this.tingkat,
            id_mapel: this.filter.id_mapel,
          }
        }).then(res => {
          this.getData();
        })
      },
      downloadScoreTemplate(){
        let params = {
          id_mapel: this.filter.id_mapel
        }
        let queryString = new URLSearchParams(params).toString();
        this.openLink(this.$siteUrl + 'nilai/download_template?' + queryString)
      },
    },
    created: function() {
      this.getInitial()
      // console.log(this.$router);
    },
    mounted(){
      
    },
    beforeUnmount() {
      let dataStore = useDataStore()
      Object.entries(this.filter).forEach(([index, val]) =>
        dataStore.setFilter({
          key:index,
          val:val
        })
      )
      // console.log('change-filter', dataStore.filters)
    },
  }
  </script>
  
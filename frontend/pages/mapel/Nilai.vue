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
        <div :class="[scrollY > showHidden ? 'opacity-0' : 'opacity-100',
          'animate px-3 pt-3 pb-2']">
          <div class="text-right">
            <template v-if="role == 'guru'">
              <el-button size="small" type="success" @click="downloadScoreTemplate">
                <icons icon="mdi:download" /> Unduh Template
              </el-button>
              <el-button size="small" type="success" @click="saveScore">
                <icons icon="fluent:save-20-filled" /> Simpan
              </el-button>
            </template>
          </div>
        </div>
        <div class="relative bg-white">
          <div :class="[scrollY > showHidden ? 'opacity-100 z-[9999]' : 'opacity-0 z-[-1]',
            'animate fixed right-0 bg-white/[0.7] h-fit',
            'px-3 pt-3 pb-2']"
            v-fixed-to-position="50">
            <div class="text-right">
              <template v-if="role == 'guru'">
                <el-button size="small" type="success" @click="downloadScoreTemplate">
                  <icons icon="mdi:download" /> Unduh Template
                </el-button>
                <el-button size="small" type="success" @click="saveScore">
                  <icons icon="fluent:save-20-filled" /> Simpan
                </el-button>
              </template>
            </div>
          </div>
          <div v-if="dataNilai.length == 0"
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
            <div id="freeze-container" class="mx-3 overflow-x-hidden w-full h-full">
            </div>
            <div class="mx-3 overflow-x-auto" @scroll="(event) => {
              let tFreezeHead = jquery('#table-freeze-head')
              let target = event.target
              let scrollLeft = target.scrollLeft
              let left = scrollLeft - 13
              tFreezeHead.css({left: -left + 'px'})
            }">
              <table id="table-base" class=" table mt-1 md:text-[14px] text-[12px] leading-[1.5]">
                <thead class="bg-slate-100 ">
                  <tr class="*:border *:border-solid *:border-slate-300">
                    <th width="20px" class="fixed-col">No</th>
                    <th class="fixed-col">Nama</th>
                    <template v-for="(ujian) in ['nilai_harian','uts','uas']">
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
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(data, key) in dataNilai">
                    <td>{{ key + 1 }}</td>
                    <td>{{ data.nama }}</td>
                    <td v-for="(ujian) in ['nilai_harian','uts','uas']" class="text-center">
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
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </el-card>
    </div>
</template>
  
<script>
  import { event } from 'jquery';
import { head } from 'lodash';
import { mapState } from 'pinia';
  
  
  export default {
    name: "mapel",
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
          id_pembagian_mapel:{
            label:'Mata Pelajaran',
            nama_kolom:'id_pembagian_mapel',
            input:'select',
            options:[],
          },
        },
        filter:{
          id_semester:'',
          id_kelas:'',
          id_pembagian_mapel:'',
        },
        params:{
          where:[],
        },
        editId:-1,
        ids:[],
        formKey:0,
        dataNilai:[],
        scrollY:0,
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
          this.filterFields.id_kelas.options = this.filterFields.id_semester.options[val]?.options ?? []
          this.filter.id_kelas = -1
          setTimeout(() => {
            this.filter.id_kelas = Object.values(this.filterFields.id_kelas.options)[0]?.value
          }, 100)
        }
      },
      'filter.id_kelas' (val){
        if (!this.initial) {
          this.filterFields.id_pembagian_mapel.options = this.filterFields.id_kelas.options[val]?.options ?? []
          this.filter.id_pembagian_mapel = Object.values(this.filterFields.id_pembagian_mapel.options)[0]?.value
        }
      },
      'filter.id_pembagian_mapel' (val) {
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
        return this.$windowWidth > 800
      },
      allowEdit(){
        let access = (this.role == 'guru' || this.PembagianMapel.allow_access == '1')
        // access = true
        // console.log(access)
        let data = {
          nilai_harian: access && this.PembagianMapel['lock_nilai_harian'] == '0',
          uts: access && this.PembagianMapel['lock_uts'] == '0',
          uas: access && this.PembagianMapel['lock_uas'] == '0',
        }
        // data.uas = true
        // console.log(data)
        return data
      }
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
              this.filter.id_semester = this.storeFilters?.id_semester ? this.storeFilters?.id_semester : Object.values(data)[0]?.value
              this.filterFields.id_kelas.options = data[this.filter.id_semester]?.options ?? {}
              this.filter.id_kelas = this.storeFilters?.id_kelas ? this.storeFilters?.id_kelas : this.user.id_kelas ?? Object.values(this.filterFields.id_kelas.options)[0]?.value
              this.filterFields.id_pembagian_mapel.options = this.filterFields.id_kelas.options[this.filter.id_kelas]?.options
              if (this.storeFilters?.nama_mapel) {
                console.log(this.storeFilters?.nama_mapel)
                let filter = this.filterFields.id_pembagian_mapel.options.filter(o => {
                  let nama_mapel = o.label.slice(0, o.label.indexOf('(') - 1);
                  console.log(nama_mapel)
                  return nama_mapel == this.storeFilters?.nama_mapel
                })
                console.log(filter, filter[0])
                this.filter.id_pembagian_mapel = filter[0]?.value
              } else
                this.filter.id_pembagian_mapel = this.storeFilters?.id_mapel ? this.storeFilters?.id_mapel : Object.values(this.filterFields.id_pembagian_mapel.options)[0]?.value
              setTimeout(() => {
                this.initial = false
              },1000)    
          })
        },
      getData(){
        this.$http.get('mapel/pembagian/get',{
          params: {
            id: this.filter.id_pembagian_mapel
          }
        }).then(result => {
          this.PembagianMapel = result.data
          // console.log(this.PembagianMapel)
        })
        this.$http.get('mapel/nilai',{
          params: {
            id_pembagian_mapel: this.filter.id_pembagian_mapel
          }
        }).then(result => {
          this.dataNilai = result.data
          setTimeout(() => {
            this.getFreezeHeader()
          }, 300)
        })
      },
      async handlePaste(event, key, _ujian) {
        let ujian = ['nilai_harian','uts','uas']
        let currInd = ujian.findIndex(d => d == _ujian)
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
            let uj = ujian[(currInd + num)]
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
        this.dataNilai[key].nilai.nilai_rapor = Math.round((parseInt(nilai.nilai_harian) + parseInt(nilai.uts) * 2 + parseInt(nilai.uas) * 3) / 6)
        this.generateDinas(key)
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
      saveScore() {
        let form = []
        this.dataNilai.forEach(d => {
          // console.log(d)
          form.push({
            id:d.id,
            id_pembagian_mapel: d.id_pembagian_mapel,
            id_santri: d.id_santri,
            nilai_harian: d.nilai.nilai_harian,
            uts: d.nilai.uts,
            uas: d.nilai.uas,
            nilai_rapor: d.nilai.nilai_rapor,
            katrol1: d.nilai.katrol1,
            katrol2: d.nilai.katrol2,
          })
        })
        form = window.jsonToFormData(form)
        this.$http.post('mapel/nilai/store_many', form)
          .then(res => {
            this.getData()
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
      downloadScoreTemplate(){
        let params = {
          id_pembagian_mapel: this.filter.id_pembagian_mapel
        }
        let queryString = new URLSearchParams(params).toString();
        this.openLink(this.$siteUrl + 'mapel/nilai/download_template?' + queryString)
      },
      getFreezeHeader(){
        console.log('freeze-header')
        let tBase = jquery('#table-base')
        let tFreezeContainer = jquery('#freeze-container')
        tFreezeContainer.empty() 
        // console.log(tBase, tFreezeContainer)

        let tBodyFreeze = tBase.clone(true)
        this.removeColumnByClass(tBodyFreeze, [], 'fixed-col')
        tBodyFreeze.attr('id', 'table-freeze-body')
        tBodyFreeze.appendTo(tFreezeContainer)
        tBodyFreeze.css({position:'fixed'})

        let tHeadFreeze = tBase.clone(true).find('tbody').remove().end()
        tHeadFreeze.attr('id', 'table-freeze-head')
        tHeadFreeze.appendTo(tFreezeContainer) 
        tHeadFreeze.css({position:'fixed'})

        let tHeadBodyFreeze = tBodyFreeze.clone().find('tbody').remove().end()
        tHeadBodyFreeze.attr('id', 'table-freeze-head-body')
        tHeadBodyFreeze.appendTo(tFreezeContainer) 
        tHeadBodyFreeze.css({position:'fixed'})
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
          top: (this.scrollY - 20) + 'px',
          width: tBase.width() + 'px',
          opacity: this.scrollY < this.showHidden ? 0 : 1,
        })
        tHeadBodyFreeze.css({
          zIndex: 9999,
          top: (this.scrollY - 20) + 'px',
          width: tBodyFreeze.width() + 'px',
          height: tHeadFreeze.height() + 'px',
          opacity: this.scrollY < this.showHidden ? 0 : 1,
        })
      },
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
  
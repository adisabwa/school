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
        body-class="p-0 py-2">
        <div class="relative bg-white" v-loading="loading">
          <div v-if="dataNilai.length == 0"
            class="text-center text-gray-500 text-lg p-5">
            <icons icon="mdi:alert" class="text-[50px] mb-3" />
            <div class="text-[18px]">Tidak ada data nilai</div>
          </div>
          <table-freeze v-else
            ref="tableFreeze"
            :data="dataNilai">
            <template #header="{data}">
              <tr class="*:border *:border-solid *:border-slate-300">
                <th width="20px" class="fixed-col" rowspan="2">No</th>
                <th class="fixed-col min-w-[160px]" rowspan="2">Nama</th>
                <th v-for="(mapel, key) in data[0].mapel"
                  :key="key"
                  :colspan="listUjians.length"
                  class="text-center">
                  {{ mapel.nama_mapel ?? mapel }}
                </th>
                <th rowspan="2"
                  class="text-center px-2">Jml
                </th>
                <th rowspan="2"
                  class="text-center px-2">Rata2
                </th>
                <th rowspan="2"
                  class="text-center px-2">Ranking Kelas
                </th>
                <th rowspan="2"
                  class="text-center px-2">Ranking Angkatan
                </th>
              </tr>
              <tr class="*:border *:border-solid *:border-slate-300 *:px-2">
                <template v-for="(mapel, key) in data[0].mapel">
                  <th v-for="ujian in listUjians">
                    {{ getLabelShort(ujian) }}
                  </th>
                </template>
              </tr>
            </template>
            <template #body="{data}">
              <tr v-for="(d, key) in data"
                class="*:border *:border-solid *:border-slate-300">
                <td>{{ key + 1 }}</td>
                <td>{{ d.nama }}</td>
                <template v-for="(mapel, mKey) in d.mapel">
                  <td v-for="ujian in listUjians"
                    :key="mKey"
                    :class="['text-center px-2',
                      ['nilai_rapor','um'].includes(ujian) ? 'bg-[var(--color-main-50)]' : ['katrol1','katrol2'].includes(ujian) ? 'bg-blue-5 0' : '',
                      mapel[ujian] > 0 && mapel[ujian] < 100 ? '' : 'bg-red-700 text-white']">
                    {{ mapel[ujian] }}
                  </td>
                </template>
                <td class="text-center font-bold">{{ d.total_nilai_rapor }}</td>
                <td class="text-center font-bold">{{ (d.total_nilai_rapor / d.mapel.length).toFixed(2) }}</td>
                <td class="text-center font-bold">{{ d.ranking_kelas }}</td>
                <td class="text-center font-bold">{{ d.ranking_angkatan }}</td>
              </tr>
            </template>
          </table-freeze>
        </div>
      </el-card>
    </div>
</template>
  
<script>
  import { zip } from 'lodash';
import { mapState } from 'pinia';
import { useAuthStore } from '@/config/stores/authStore'
import { useDataStore } from '@/config/stores/dataStore'
import { getLabelShort } from '@rapor/helpers/labelUjian'
  
  
  export default {
    name: "mapel",
    setup(){
      return {
        getLabelShort,
      }
    },
    components: {
      
    },
    data: function() {
      return {
        showDownload:false,
        generating:true,
        progress:0,
        downloadStatus:'',
        files:[],
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
            input_only:'1',
            options:[],
          },
        },
        filter:{
          id_semester:'',
          id_kelas:'',
        },
        params:{
          where:[],
        },
        editId:-1,
        ids:[],
        formKey:0,
        dataNilai:[],
        scrollY:0,
        showHidden: 286,
        PembagianMapel:{},
        // role:'guru',
      };
    },
    watch: {
      'filter.id_semester' (val){
        if (!isEmpty(val)) {
          this.getKelasOptions()
        }
      },
      'filter.id_kelas' (val){
        if (!isEmpty(val))
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
      percentage(){
        if (this.files.length == 0) return 0
        let all = this.dataNilai.length
        return Math.round((this.files.length / all) * 100)
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
      tahun_ajaran(){
        let options = this.filterFields.id_semester.options
        if (options) {
          if (typeof options == 'object') options = Object.values(options)
          let semester = options.find(d => d.value == this.filter.id_semester)
          return semester?.tahun_ajaran ?? null
        } else {
          return null 
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
      idUnit(){
        let options = this.filterFields.id_kelas.options
        if (options) {
          if (typeof options == 'object') options = Object.values(options)
          let kelas = options.find(d => d.value == this.filter.id_kelas)
          return kelas?.id_unit ?? null
        } else {
          return null 
        }
      },      
      is3Akhir(){
        console.log(this.tingkat, this.filter.id_semester)
        return this.tingkat == '3' && (this.filter.id_semester % 2) == 0
      },
      is6Akhir(){
        console.log(this.tingkat, this.filter.id_semester)
        return this.tingkat == '6' && (this.filter.id_semester % 2) == 0
      },
      listUjians(){
        console.log(this.is3Akhir, this.is6Akhir)
        if (this.is3Akhir)
          return ['nilai_harian', 'uas', 'nilai_rapor', 'katrol1', 'um', 'katrol2']
        else if (this.is6Akhir)
          return ['nilai_harian', 'uas', 'nilai_rapor', 'katrol1']
        else
          return ['nilai_harian', 'uts', 'uas','nilai_rapor', 'katrol1']
      },
    },
    methods: {
      getInitial: async function() {
          this.loading = true;
          this.$http.get('data/semester/options')
            .then(res => {
              this.loading = false;
              let options = res.data
              this.filterFields.id_semester.options = options
              this.filter.id_semester = this.storeFilters?.id_semester ? this.storeFilters?.id_semester : this.user.id_semester?? Object.values(options)[0]?.value
            })
        },
      getKelasOptions(){
        this.$http.get('data/kelas-ajar/options',{
          params:{
            where:{
              tahun_ajaran:this.tahun_ajaran
            }
          }
        })
          .then(res => {
            this.loading = false;
            let options = res.data
            this.filterFields.id_kelas.options = options
            this.filterFields.id_kelas.readonly = this.role != 'admin'
            this.filter.id_kelas = this.storeFilters?.id_kelas ? this.storeFilters?.id_kelas : this.user.id_kelas?? Object.values(options)[0]?.value
          })

      },
      getData(){
        this.loading = true
        this.$http.get('rapor/rekapitulasi',{
          params: {
            id_semester: this.filter.id_semester,
            id_kelas: this.filter.id_kelas
          }
        }).then(result => {
          this.loading = false
          this.dataNilai = result.data
        })
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
  
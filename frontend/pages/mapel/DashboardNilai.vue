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
        <div v-loading="loading">
          <div class="m-2 p-3 border border-solid border-slate-200 shadow-md">
            <h2 class="text-lg font-bold m-0 mb-1">Total Progress Pengisian Nilai {{ role == 'guru' ? user.nama : runFunction({
              data: this.filter.id_kelas, 
              options: this.filterFields.id_kelas.options,
            }) }}</h2>
            <table class="w-full">
              <tbody>
                <tr v-for="u in ujians">
                  <td width="100">{{ getLabel(u) }}</td>
                  <td width="10">:</td>
                  <td>
                    <el-progress :percentage="percentage[`persentase_${u}`]"
                      class="w-full"
                      :stroke-width="18"
                      :show-text="true"
                      :status="percentage[`persentase_${u}`] < 100 ? 'warning' : ''"
                      striped
                      >
                      {{ percentage[`persentase_${u}`] }} % 
                    </el-progress>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="m-2 mt-5 p-3 border border-solid border-slate-200 shadow-md text-md ">
            <div class="font-bold m-0 mb-2">Progress Pengisian Nilai per Mapel</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-5">
              <div v-for="mapels in [percentageMapel.slice(0, Math.ceil(percentageMapel.length / 2)), percentageMapel.slice(Math.ceil(percentageMapel.length / 2), percentageMapel.length)]">
                <div  v-for="(mapel, key) in mapels"
                  :class="['my-1 mb-3 p-2 border border-solid border-slate-200']">
                  <table class="w-full text-sm">
                    <tbody>
                      <tr>
                        <td colspan="3" class="font-bold text-[15px]">
                          <div class="mb-2 bg-teal-50 px-2 py-1">
                            <el-button link type="primary" size="small" class="p-0 mr-1"
                              @click="filter.nama_mapel = mapel.nama_mapel;
                                filter.id_kelas = mapel.id_kelas;
                                loading = true;
                                $router.push({name:'nilai-mapel'})">
                                <icons v-if="role == 'guru'" icon="bxs:edit" class="m-0"/>
                                <icons v-else icon="mdi:eye" class="m-0"/>
                            </el-button>
                            {{ role == 'guru' ? 'Kelas ' + mapel.kelas + ' - ': ''}} {{ mapel.nama_mapel }} | {{ mapel.nama_guru }}
                          </div>
                        </td>
                      </tr>
                      <tr v-for="u in ujians"> 
                        <td width="80">{{ getLabel(u) }}</td>
                        <td width="10">:</td>
                        <td>
                          <el-progress :percentage="mapel[`persentase_${u}`]"
                            class="w-full *:text-sm"
                            :stroke-width="18"
                            :show-text="true"
                            :status="mapel[`persentase_${u}`] < 100 ? 'warning' : ''"
                            striped
                            >
                            {{ mapel[`persentase_${u}`] }} %
                          </el-progress>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </el-card>
    </div>
</template>
  
<script>
import { formItemValidateStates } from 'element-plus';
import { forEach, isObject } from 'lodash';
import { mapState } from 'pinia';
import { useAuthStore } from '@/config/stores/authStore'
import { useDataStore } from '@/config/stores/dataStore'
  
  
  export default {
    name: "mapel",
    components: {
      
    },
    data: function() {
      return {
        initial:true,
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
            options:{},
            hidden:false,
          },
        },
        filter:{
          id_semester:'-1',
          id_kelas:'-1',
        },
        params:{
          where:[],
        },
        editId:-1,
        ids:[],
        formKey:0,
        percentage:{
          persentase_nilai_harian:0,
          persentase_uts:0,
          persentase_uas:0,
        },
        percentageMapel:[],
        ujians:['nilai_harian','uts','uas'],
        getLabel: function(u) {
          switch (u) {
            case 'nilai_harian':
              return 'Nilai Harian'
            case 'uts':
              return 'UTS'
            case 'uas':
              return 'UAS'
            default:
              break;
          }
        }
        // role:'guru',
      };
    },
    watch: {
      'filter.id_semester' (val){
        if (!this.isEmpty(val)) {
          if (this.role == 'admin') this.filter.id_kelas = this.filterFields.id_kelas.options[0].value
          if (this.role == 'guru') {
            this.filter.id_kelas = ''
            this.getData()
          }
          else this.filter.id_kelas = this.user.id_kelas
        }
      },
      'filter.id_kelas' (val){
        if (!this.isEmpty(val))
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
    },
    methods: {
      getInitial: async function() {
        this.initial = true
        await this.$http.get('data/kelas/options')
          .then(res => {
            this.initial = false
            let data = res.data
            this.filterFields.id_kelas.options = data
          })
        await this.$http.get('data/semester/options')
          .then(res => {
            let data = res.data
            this.filterFields.id_semester.options = data
            this.filter.id_semester = this.storeFilters?.id_semester ? this.storeFilters?.id_semester : data[0]?.value
            console.log('id_semester', this.filter.id_semester)
            if (this.role != 'admin') this.filterFields.id_kelas.hidden = true;
          })
      },
      async getData(){
        this.percentageMapel = []
        this.$http.get('mapel/nilai/get_progres',{
          params: {
            id_semester: this.filter.id_semester,
            id_kelas: this.filter.id_kelas,
            id_guru: this.role == 'guru' ? this.user.id_guru : null,
          }
        }).then(result => {
          this.loading = false
          let res = result.data
          this.percentageMapel = res
          this.countPercentage()
        })
      },
      countPercentage(){
        this.percentage.persentase_nilai_harian = 0
        this.percentage.persentase_uts = 0
        this.percentage.persentase_uas = 0

        this.percentageMapel.forEach(m => {
          m.persentase_nilai_harian = Math.floor(m.persentase_nilai_harian * 100)
          m.persentase_uts = Math.floor(m.persentase_uts * 100)
          m.persentase_uas = Math.floor(m.persentase_uas * 100)
          this.percentage.persentase_nilai_harian += m.persentase_nilai_harian
          this.percentage.persentase_uts += m.persentase_uts
          this.percentage.persentase_uas += m.persentase_uas
        })

        let countMapel = this.percentageMapel.length
        this.percentage.persentase_nilai_harian = Math.floor(this.percentage.persentase_nilai_harian / countMapel)
        this.percentage.persentase_uts = Math.floor(this.percentage.persentase_uts / countMapel)
        this.percentage.persentase_uas = Math.floor(this.percentage.persentase_uas / countMapel)
        console.log(this.percentage)
      }
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
  
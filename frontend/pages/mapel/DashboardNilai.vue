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
            <h2 class="text-lg font-bold m-0 mb-1">Total Progress Pengisian Nilai</h2>
            <table class="w-full">
              <tbody>
                <tr v-for="u in ujians">
                  <td width="100">{{ getLabel(u) }}</td>
                  <td width="10">:</td>
                  <td>
                    <el-progress :percentage="percentage[u]"
                      class="w-full"
                      :stroke-width="18"
                      :show-text="true"
                      :status="percentage[u] < 100 ? 'warning' : ''"
                      striped
                      >
                      {{ percentage[u] }} % 
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
                            {{ role == 'guru' ? 'Kelas ' + mapel.kelas + ' - ': ''}} {{ mapel.nama_mapel }}
                          </div>
                        </td>
                      </tr>
                      <tr v-for="u in ujians"> 
                        <td width="80">{{ getLabel(u) }}</td>
                        <td width="10">:</td>
                        <td>
                          <el-progress :percentage="mapel[u]"
                            class="w-full *:text-sm"
                            :stroke-width="18"
                            :show-text="true"
                            :status="mapel[u] < 100 ? 'warning' : ''"
                            striped
                            >
                            {{ mapel[u] }} %
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
import { forEach, isObject } from 'lodash';
import { mapState } from 'pinia';
  
  
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
            hidden:true,
            multiple:true,
          },
        },
        filter:{
          id_semester:'',
          id_kelas:'',
        },
        ids_kelas:[],
        params:{
          where:[],
        },
        editId:-1,
        ids:[],
        formKey:0,
        dataNilai:[],
        percentage:{
          nilai_harian:0,
          uts:0,
          uas:0,
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
          let all_kelas = this.filterFields.id_semester.options[val]?.options ?? []
          // console.log(all_kelas, isObject(all_kelas), Object.values(all_kelas))
          all_kelas = isObject(all_kelas) ? Object.values(all_kelas) : all_kelas
          this.filterFields.id_kelas.options = all_kelas
          if (this.role == 'guru')
            this.ids_kelas = all_kelas.map(d => d.value)
          else
            this.filter.id_kelas = this.storeFilters?.id_kelas ?? all_kelas[0]?.value
        }
      },
      'filter.id_kelas' (val){
        if (!this.isEmpty(val))
          this.ids_kelas = [val]
      },
      'ids_kelas' (val){
        console.log('ids_kelas', val)
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
          this.loading = true;
          this.initial = true
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
              let data = res.data
              this.filterFields.id_semester.options = data
              this.filter.id_semester = this.storeFilters?.id_semester ? this.storeFilters?.id_semester : Object.values(data)[0]?.value
              setTimeout(() => {
                if (this.role == 'guru') {

                } else {
                  this.filterFields.id_kelas.hidden = false
                  if (this.role == 'walas') {
                    this.filterFields.id_kelas.readonly = true
                  }
                }
              },500)
              setTimeout(() => {
                this.initial = false
              },1000)
              this.loading = false
          })
      },
      async getData(){
        this.dataNilai = []
        this.percentageMapel = []
        for (const id_kelas of this.ids_kelas) {
          let optKelas = this.filterFields.id_kelas?.options ?? {}
          // console.log('optKelas', optKelas)
          let kelas = Object.values(optKelas).find(d => d.value === id_kelas)
          try {
            await this.fetchData(kelas);
            console.log(`Get Data `);
          } catch (error) {
            console.error(`Failed to get data`, error);
          }
        }
        
        try {
          console.log("Counting percentages...");
          // this.countPercentage()
        } catch (error) {
          console.error("Failed to count:", error);
        }
      },
      async fetchData(kelas) {
        try {
          this.loading = true
          let id_pembagian = (kelas?.options ?? []).map(d => d.value)
          this.$http.get('mapel/nilai/rekapitulasi',{
            params: {
              id_semester: this.filter.id_semester,
              id_kelas: kelas.value,
              id_pembagian: id_pembagian,
            }
          }).then(result => {
            this.loading = false
            let res = result.data
            this.dataNilai = [...this.dataNilai, ...res]
            this.countPercentage(res)
          })
        } catch (error) {
          console.error(`Error get data:`, error);
          throw error; // allow caller to handle it
        }
      },
      countPercentage(data){
        let percentage = []
        let countStudent = data.length
        if (countStudent <= 0)
          return
        let countMapel = data[0].mapel.length
        data[0].mapel.forEach(d => {
          percentage.push({
            nama_mapel:d.nama_mapel,
            kelas:data[0].kelas,
            id_kelas:data[0].id_kelas,
            nilai_harian:0,
            uts:0,
            uas:0,
          })
        })

        data.forEach(s => {
          s.mapel.forEach((m, key) => {
            if (m.nilai_harian > 0)
              percentage[key].nilai_harian++
            if (m.uts > 0)
              percentage[key].uts++
            if (m.uas > 0)
              percentage[key].uas++
          })
        })

        this.percentage.nilai_harian = 0
        this.percentage.uts = 0
        this.percentage.uas = 0

        percentage.forEach(m => {
          m.nilai_harian = Math.floor(m.nilai_harian / countStudent * 100)
          m.uts = Math.floor(m.uts / countStudent * 100)
          m.uas = Math.floor(m.uas / countStudent * 100)
          this.percentage.nilai_harian += m.nilai_harian
          this.percentage.uts += m.uts
          this.percentage.uas += m.uas
        })

        this.percentageMapel = [...this.percentageMapel, ...percentage]
        this.percentage.nilai_harian = Math.floor(this.percentage.nilai_harian / countMapel)
        this.percentage.uts = Math.floor(this.percentage.uts / countMapel)
        this.percentage.uas = Math.floor(this.percentage.uas / countMapel)
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
  
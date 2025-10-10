<template>
  <div id="mapel" class="py-2">
    <el-card class="bg-white/[0.7]">
      <form-comp ref="formFilter"
        :key="filterKey"
        :fields="filterFields"
        :label-position="labelPosition"
        class="mt-2"
        label-width="180px"
        v-model:form-value="filter"
        :show-submit="false"
        text-submit="Cari"
        error-submit-text="Tidak dapat mengambil data"
        :show-required-text="false"
        >
      </form-comp>
      <table-data ref="tableData" 
        :key="tableKey"
        href="mapel/admin/penjadwalan/detail" 
        :params="params"
        :show-search="false"
        :title="'Data Mata Pelajaran'"
        v-model:form-value="formValue"
        :fields="fields"
        :pass-columns="['id_penjadwalan']"
        :default-value="[
         {
          key:'id_penjadwalan',
          value:filter.id_penjadwalan,
         }
        ]"
        @reset-field="getInitial"
        class="p-0">
        <template #before-menu>
          <el-button type="success" class="h-full" size="small"
            @click="showAdd = true"
            ><icons icon="mdi:plus"/>Buat Jadwal Baru</el-button>
        </template>
        <template #menu>
          <el-button type="primary" class="float-right h-full" size="small"
            @click="searchData"
            ><icons icon="mdi:search"/>Cari</el-button>
        </template>
      </table-data>
      
      <data-create v-model:show="showAdd"
        v-model:form-value="valuePenjadwalan"
        title="Versi Penjadwalan" :href="'mapel/admin/penjadwalan/store'" :href-get="'mapel/admin/penjadwalan/get'"
        :fields="fieldsPenjadwalan"
        :label-position="labelPosition"
        :data-id="editIdPenjadwalan" :type="dataType"
        @saved="onUpdated"></data-create>
    </el-card>
  </div>
</template>

<script>
import { mapState } from 'pinia';
import DataCreate from '@/components/table/DataCreate.vue'

export default {
  name: "mapel",
  components: {
    DataCreate,
  },
  data: function() {
    return {
      loading: false,
      showAdd: false,
      editIdPenjadwalan:-1,
      dataType:'create',
      fieldsPenjadwalan:{},
      valuePenjadwalan:{},
      filterKey:-1,
      filterFields: {
        'id_penjadwalan' : {
          nama_kolom:'id_penjadwalan',
          label:'Versi Jadwal',
          input:'select',
          clearable:false,
        },
        'hari' : {
          nama_kolom:'hari',
          label:'Hari',
          input:'select',
        },
        'id_kelas' : {
          nama_kolom:'id_kelas',
          label:'Kelas',
          input:'select',
          options:[],
        },
        'id_guru' : {
          nama_kolom:'id_guru',
          label:'Guru',
          input:'select',
          options:[],
        },
      },
      filter:{
        id_penjadwalan:null,
        id_kelas:null,
        hari:null,
        id_guru:null,
      },
      tableKey:0,
      fields:{},
      formValue:{},
      params:{
        where:[],
      },
    };
  },
  watch: {
    'filter.id_penjadwalan'(val){
      if (this.fields.id_penjadwalan) {
        this.fields.id_penjadwalan.default = val
        this.newPenjadwalan(val)
      }
    },
    'formValue.id_penjadwalan'(val){
      this.newPenjadwalan(val)
    }
  },  
  computed: {
    ...mapState(useAuthStore, {
      user: 'loggedUser',
    }),
    labelPosition(){
      return this.$windowWidth < 800 ? 'top' : 'left'
    },
  },
  methods: {
    newPenjadwalan(id){
      this.$http.get('/mapel/admin/penjadwalan/get',{
        params:{
          id:id
        }
      }).then(result => {
        var res = result.data;
        let idSemester = res.id_semester
        this.$http.get('/mapel/admin/pembagian/options_penjadwalan',{
            params:{
              where:{
                id_semester:idSemester ?? -1
              }
            }
          }).then(result => {
            var res = result.data;
            this.fields.id_pembagian_mapel.options = res
            // console.log(idSemester, res, this.fields)
            this.$refs.tableData.resetDataCreate()
          })
      })
    },
    searchData(){
      this.params = {
        where:{
          hari:this.filter.hari,
          id_penjadwalan:this.filter.id_penjadwalan,
        },
        condition:{
          id_pembagian_mapel: {
            id_kelas: this.filter.id_kelas,
            id_guru: this.filter.id_guru
          },
        }
      }
    },
    getInitial: async function() {
        this.loading = true;
        await this.$http.get('/kolom/preparation?table=sch_aka_penjadwalan_detail',{
          params: {
            grouping:'0',
            input:'0',
          }
        })
          .then(result => {
            var res = result.data;
            this.fields = res
            this.fields.id_pembagian_mapel.similar_criteria = 0.95
            this.filterFields = this.fillObjectValue(this.filterFields, JSON.parse(JSON.stringify(res)))
            this.filterFields.id_penjadwalan.clearable = false
            this.filterFields.id_penjadwalan.required = '0'
            this.filterFields.id_kelas.required = '0'
            this.filterFields.hari.required = '0'
            this.filter.id_penjadwalan = res?.id_penjadwalan?.options[0]?.value ?? ''
            this.filterKey++
            this.tableKey++
            this.loading = false
            this.searchData()
          });
        await this.$http.get('/kolom/preparation?table=sch_aka_penjadwalan&grouping=0&input=0')
          .then(result => {
            var res = result.data;
            this.fieldsPenjadwalan = res
            this.loading = false
          });
        await this.$http.get('/data/kelas/options')
          .then(result => {
            var res = result.data;
            this.filterFields.id_kelas.options = res
            this.loading = false
          });
        await this.$http.get('/data/guru/options')
          .then(result => {
            var res = result.data;
            this.filterFields.id_guru.options = res
            this.loading = false
          });
      },
    onUpdated(data){
      this.getInitial()
      this.filter.id_penjadwalan = data.id
    }
  },
  created: function() {
    let filter = useDataStore().filter
    this.fillObjectValue(this.filter, filter)
    this.getInitial()
    // console.log(this.$router);
  },
  mounted: function() {
    console.log('mounted')
    this.searchData()
  },
}
</script>

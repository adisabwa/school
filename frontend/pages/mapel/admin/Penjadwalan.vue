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
        href="mapel/admin/penjadwalan" 
        :params="params"
        :show-search="false"
        :show-upload="false"
        :show-upload-normal="true"
        :title="'Data Penjadwalan'"
        v-model:form-value="formValue"
        :fields="fields"
        @reset-field="getInitial"
        :pass-columns-input="['kelas','nama_guru']"
        class="p-0">
        <template #menu>
          <el-button type="primary" class="float-right h-full" size="small"
            @click="searchData"
            ><icons icon="mdi:search"/>Cari</el-button>
        </template>
      </table-data>
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
      showAdd: false,
      filterKey:-1,
      filterFields: {
        'hari' : {
          nama_kolom:'hari',
          label:'Hari',
          input:'select',
          options:[
            { value: 'senin', label: 'Senin' },
            { value: 'selasa', label: 'Selasa' },
            { value: 'rabu', label: 'Rabu' },
            { value: 'kamis', label: 'Kamis' },
            { value: 'jumat', label: 'Jumat' },
            { value: 'sabtu', label: 'Sabtu' },
            { value: 'ahad', label: 'Ahad' }
          ],
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
    searchData(){
      let params = {
        where:{
          hari:this.filter.hari,
        },
        condition:{
          id_pembagian_mapel: {}
        }
      }
      if (this.filter.id_kelas)
        params.condition.id_pembagian_mapel.id_kelas = this.filter.id_kelas
      if (this.filter.id_guru)
        params.condition.id_pembagian_mapel.id_guru = this.filter.id_guru
      
      this.params = params
    },
    getInitial: async function() {
        this.loading = true;
        await this.$http.get('/kolom/preparation?table=sch_aka_penjadwalan&grouping=0&input=0')
          .then(result => {
            var res = result.data;
            this.fields = {...{
              kelas:{
                nama_kolom:'kelas',label:'Kelas',width:'50px',
              },
              nama_guru:{
                nama_kolom:'nama_guru',label:'Guru',
              }
            },...res}
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

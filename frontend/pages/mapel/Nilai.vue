<template>
    <div id="nilai" class="py-2">
      <el-card class="bg-white/[0.7]">
        <form-comp ref="formFilter"
          :key="formKey"
          :fields="filterFields"
          :label-position="labelPosition"
          class="mt-2 "
          label-width="150px"
          v-model:form-value="filter"
          :show-submit="false"
          text-submit="Cari"
          error-submit-text="Tidak dapat mengambil data"
          :show-required-text="false"
          >
        </form-comp>
        <!-- <table-data ref="tableData" 
          href="mapel/admin/pembagian" 
          :params="params"
          :show-search="false"
          :title="'Data Mata Pelajaran'"
          v-model:checked-id="ids"
          :fields="fields"
          :pass-columns="[]"
          :default-value="[
           {
            key:'id_semester',
            value:filter.id_semester,
           }
          ]"
          @reset-field="getInitial"
          class="p-0">
          <template #menu>
            <el-button type="primary" class="float-right h-full" size="small"
              @click="searchData"
              ><icons icon="mdi:search"/>Cari</el-button>
          </template>
        </table-data> -->
      </el-card>
    </div>
</template>
  
<script>
  import { mapState } from 'pinia';
  import TableData from '@/components/TableData.vue'
  
  export default {
    name: "mapel",
    components: {
      TableData,
    },
    data: function() {
      return {
        loading: false,
        showAdd: false,
        filterFields: {
          id_semester:{
            label:'Semester',
            nama_kolom:'id_semester',
            input:'select',
            options:[],
          },
          id_guru:{
            label:'Guru',
            nama_kolom:'id_guru',
            input:'select',
            input_only:'1',
            options:[],
          },
          id_kelas:{
            label:'Kelas',
            nama_kolom:'id_kelas',
            input:'select',
            input_only:'1',
            options:[],
          },
          id_pembagian_mapel:{
            label:'Mapel',
            nama_kolom:'id_pembagian_mapel',
            input:'select',
            options:[],
          },
        },
        fields:{},
        filter:{
          id_semester:'',
          id_guru:'',
          id_kelas:'',
          id_pembagian_mapel:'',
        },
        params:{
          where:[],
        },
        editId:-1,
        ids:[],
        formKey:0,
      };
    },
    watch: {
      'paging.currentPage': function(val) {
        this.paging.offset = val * this.paging.perPage - this.paging.perPage;
      },
      'filter.id_guru' (val){
        this.filterFields.id_kelas.options = this.filterFields.id_guru.options[val].options
        this.filter.id_kelas = Object.values(this.filterFields.id_kelas.options)[0].value
      },
      'filter.id_kelas' (val){
        this.filterFields.id_pembagian_mapel.options = this.filterFields.id_kelas.options[val].options
        this.filter.id_pembagian_mapel = Object.values(this.filterFields.id_pembagian_mapel.options)[0].value
      },
      'filter.id_mapel' (val) {
        this.getData()
      }
    },  
    computed: {
      ...mapState(useAuthStore, {
        user: 'loggedUser',
      }),
      labelPosition(){
        return this.sizeWindow < 800 ? 'top' : 'left'
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
          this.$http.get('data/semester/options')
            .then(res => {
              this.filterFields.id_semester.options = res.data
              this.filter.id_semester = res.data[0].value
            })
          this.$http.get('mapel/admin/pembagian/options')
            .then(res => {
              let data = res.data
              this.filterFields.id_guru.options = data
              this.filter.id_guru = Object.values(data)[0].value
              this.filterFields.id_kelas.options = data[this.filter.id_guru].options
              this.filter.id_kelas = Object.values(this.filterFields.id_kelas.options)[0].value
              this.filterFields.id_pembagian_mapel.options = this.filterFields.id_kelas.options[this.filter.id_kelas].options
              this.filter.id_pembagian_mapel = Object.values(this.filterFields.id_pembagian_mapel.options)[0].value
            })
        },
    },
    created: function() {
      let filter = useDataStore().filter
      this.fillObjectValue(this.filter, filter)
      this.getInitial()
      // console.log(this.$router);
    },
  }
  </script>
  
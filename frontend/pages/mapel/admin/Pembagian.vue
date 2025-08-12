<template>
  <div id="mapel" class="py-2">
    <el-card class="bg-white/[0.7]">
      <form-comp ref="formFilter"
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
        href="mapel/admin/pembagian" 
        :params="params"
        :show-search="false"
        :title="'Data Mata Pelajaran'"
        v-model:checked-id="ids"
        :fields="fields"
        :pass-columns="['id_semester']"
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
      </table-data>
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
        'id_semester' : {
          nama_kolom:'id_semester',
          label:'Semester',
          input:'select',
          options: [],
        },
        'id_guru' : {
          nama_kolom:'id_guru',
          label:'Nama Guru',
          input:'select',
          options: [],
        },
        'id_kelas' : {
          nama_kolom:'id_kelas',
          label:'Kelas',
          input:'select',
          options: [],
        },
      },
      fields:{},
      filter:{
        id_guru:null,
        id_kelas:null,
        id_semester:null,
      },
      params:{
        where:[],
      },
      editId:-1,
      ids:[],
      sizeWindow:window.innerWidth,
    };
  },
  watch: {
    'paging.currentPage': function(val) {
      this.paging.offset = val * this.paging.perPage - this.paging.perPage;
    },
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
        await this.$http.get('/kolom/preparation?table=sch_aka_pembagian_mapel&grouping=0&input=0')
          .then(result => {
            var res = result.data;
            this.fields = this.fillAndAddObjectValue(this.fields, res)
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
        await this.$http.get('/data/semester/options')
          .then(result => {
            var res = result.data;
            this.filterFields.id_semester.options = res
            this.filter.id_semester = res[0].value
            this.loading = false
          });
      },
  },
  created: function() {
    let filter = useDataStore().filter
    this.fillObjectValue(this.filter, filter)
    this.getInitial()
    // console.log(this.$router);
  },
  mounted: function() {
    let vm = this
    vm.sizeWindow = window.innerWidth
    window.addEventListener('resize', () => {
      vm.sizeWindow = window.innerWidth
    });
    this.searchData()
  },
}
</script>

<template>
  <div id="kalender-list" class="pt-1" v-loading="loading">
    <el-card class="bg-white/[0.7] mb-4">
      <form-comp ref="formFilter"
        :key="formKey"
        :fields="filterFields"
        :label-position="labelPosition"
        :form-class="'mt-2 mb-0'"
        label-width="150px"
        v-model:form-value="filter"
        :pass-columns="[]"
        :show-submit="false"
        :show-label="$windowWidth > 640"
        text-submit="Cari"
        error-submit-text="Tidak dapat mengambil data"
        :show-required-text="false"
        >
      </form-comp>
    </el-card>
    <table-data ref="tableData" :fields="fields" href="kmi/admin/kaldik"
      :checked="true"  :pass-columns="['color']"
      v-model:formValue="formValue"
      :params="params">
      <template #menu>
        <el-button type="primary"
          @click="openLink($baseUrl + '/kmi/admin/kaldik/download_kalender?where[id_semester]=' + filter.id_semester)">
          <icons icon="mdi:download"/> Download Kaldik
        </el-button>
      </template>
      <el-table-column label="Warna Latar" width="80" align="center">
        <template #default="scope">
          <div :style="{ backgroundColor: scope.row.color }"
            class="w-6 h-6 rounded-full border border-slate-300 mx-auto">
          </div>
        </template>
      </el-table-column>
    </table-data>
  </div>
</template>
  
  <script>
    
    import { reactive } from 'vue';
    import { mapActions, mapState } from 'pinia';
import { filterFields } from 'element-plus/es/components/form/src/utils.mjs';
import { useAuthStore } from '@/config/stores/authStore'
  
  export default {
    name: "kalender-list",
    props:{
      type:'',
      showCreate:{
        type:Boolean,
        default: true,
      },
      showSearch:{
        type:Boolean,
        default: true,
      },
    },
    components: {
      
    },
    data: function() {
      return {
        loading:false,
        data:{},
        fields:[],
        state: reactive({
          passColumns : [],
          showColumns : [],
        }),
        filterFields: {
          id_semester:{
            label:'Semester',
            nama_kolom:'id_semester',
            input:'select',
            options:[],
          },
        },
        filter:{
          id_semester: '',
        },
        params:{
          where:[],
        },
        formValue:{},
      };
    },
    provide() {
      return {
        sharedState: this.state
      }
    },
    watch: {
     
      'filter.id_semester' (val) {
        // console.log('id',val)
        this.formValue.id_semester = val
        this.searchData()
      },
      
    },
    computed: {
      ...mapState(useAuthStore,{
        user:'loggedUser'
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
          this.loading = true;
          await this.$http.get('/kolom/preparation?table=sch_aka_kaldik&grouping=0&input=0')
            .then(result => {
              var res = result.data;
              this.fields = res
              this.loading = false
              this.$nextTick(() => {
                this.$refs.tableData.getData()
              })
            });
          await this.$http.get('data/semester/options')
            .then(result => {
              let res = result.data
              this.filterFields.id_semester.options = res
              this.filter.id_semester = res.length > 0 ? res[0].value : ''
            })
        },
    },
    created: function() {
      this.getInitial();
    }
  }
  </script>
  
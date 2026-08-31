<template>
  <div id="santri-list" class="pt-1" v-loading="loading">
    <el-card class="bg-white/[0.7] mb-2">
        <form-comp ref="formFilter"
            :fields="filterFields"
            size="large"
            label-position="left"
            :show-label="showLabel"
            class="max-sm:mt-4 mb-5"
            form-class="m-0"
            form-item-class="mb-2"
            label-width="150px"
            v-model:form-value="filter"
            :show-submit="false"
            text-submit="Cari"
            error-submit-text="Tidak dapat mengambil data"
            :show-required-text="false"
            >
        </form-comp>
        <div id="kelas-list" class="pt-1" v-loading="loading">
          <table-data ref="tableData" :fields="fields" href="data/kelas-ajar"
            :checked="true" 
            v-model:form-value="valueForm"
            :params="tableParams">
            <el-table-column
              label="Tanda Tangan"
              width="180"
              align="center">
              <template #default="{ row }">
                <img v-if="row.walas_signature"
                  :src="row.walas_signature" 
                  :alt="`Tanda Tangan`" 
                  class="max-h-12 mx-auto"/>
              </template>
            </el-table-column>
          </table-data>
        </div>
    </el-card>
  </div>
</template>
  
  <script>
    
  import { reactive } from 'vue';
  import { mapActions, mapState } from 'pinia';
  import { useAuthStore } from '@/config/stores/authStore'
  
  export default {
    name: "santri-list",
    components: {
      
    },
    data: function() {
      return {
        loading:false,
        showAdd:false,
        filterFields: {
          tahun_ajaran:{
            label:'Tahun Ajaran',
            nama_kolom:'tahun_ajaran',
            input:'select',
            options:[],
          },
        },
        filter:{
          tahun_ajaran:'',
        },
        state: reactive({
          passColumns : [],
          showColumns : [],
        }),
        valueForm:{},
      };
    },
    provide() {
      return {
        sharedState: this.state
      }
    },
    watch: {
      'filter.tahun_ajaran'(val){
        console.log(val)
        this.valueForm.tahun_ajaran = val
      },
    },
    computed: {
      ...mapState(useAuthStore,{
        user:'loggedUser',
        role: 'role',
      }),
      showLabel(){
        return this.$windowWidth.value > 800
      },
      tableParams() {
        return {
          where: {
            tahun_ajaran: this.filter.tahun_ajaran
          },
          offset: 0,
          limit: 0
        };
      }
    },
    methods: {
      getInitial: async function() {
          this.loading = true;
          await this.$http.get('/kolom/preparation?table=' + this.$prefixTable + '_kelas_ajar&grouping=0&input=0')
            .then(result => {
              var res = result.data;
              this.fields = res
              this.loading = false
              this.$nextTick(() => {
               //  this.$refs.tableData.getData()
              })
            });
          await this.$http.get('/data/semester/options_tahun_ajaran')
            .then(result => {
              this.loading = false;
              var res = result.data;
              this.filterFields.tahun_ajaran.options = res;
              if (this.role != 'admin') this.filterFields.tahun_ajaran.readonly = true;
              this.filter.tahun_ajaran = this.storeFilters?.tahun_ajaran ? this.storeFilters?.tahun_ajaran : res[0]?.value
            });
        },
    },
    created: function() {
      this.getInitial();
    }
  }
  </script>
  
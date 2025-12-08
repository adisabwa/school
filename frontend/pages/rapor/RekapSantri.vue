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
            :pass-columns="[]"
            :show-submit="false"
            text-submit="Cari"
            error-submit-text="Tidak dapat mengambil data"
            :show-required-text="false"
            >
        </form-comp>
        <table-data ref="tableData" :fields="fields" href="data/santri"
            :checked="true" 
            :show-columns="['no_presensi', 'stb','nisn','nama','nama_arab','id_daerah']"
            :show-columns-input="['no_presensi', 'status', 'stb','nisn','nama','nama_arab','id_daerah']"
            :show-upload="false" :show-create="false" :show-dropdown="false"
            :params="params">
            <template #default="{ handleActionClick }">
              <el-table-column label=" " width="80" align="center">
                <template #default="scope">
                    <el-button type="primary" size="default" class="px-2"
                        @click="handleActionClick({id:scope.row.id, action:'edit'})">
                        <icons icon="mdi:edit" class="m-0"/>
                    </el-button>
                </template>
              </el-table-column>
            </template>
        </table-data>
    </el-card>
  </div>
</template>
  
  <script>
    
    import { reactive } from 'vue';
    import { mapActions, mapState } from 'pinia';
  
  export default {
    name: "santri-list",
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
        showAdd:false,
        isContinue:true,
        data:{},
        formKeyAdd:-1,
        filterFields: {
          id_kelas:{
            label:'Kamar',
            nama_kolom:'id_kelas',
            input:'select',
            options:[],
          },
        },
        filter:{
          id_kelas:'',
        },
        addValue:{
          id_kelas:'',
          id_santri:'',
        },
        params:{
          where:[],
        },
        fields:{},
        fieldsAdd:{
          id_kelas:{
            nama_kolom:'id_kelas',
            label:'Kamar',
            hidden:true,
          },
          id_santri:{
            nama_kolom:'id_santri',
            label:'Santri',
            input:'select',
            options:[],
            required:true,
          },
        },
        state: reactive({
          passColumns : [],
          showColumns : [],
        })
      };
    },
    provide() {
      return {
        sharedState: this.state
      }
    },
    watch: {
        filter: {
            handler: function(newVal) {
            this.params.where = {}
            for (const key in newVal) {
                if (newVal[key] !== '' && newVal[key] !== null && newVal[key] !== undefined) {
                    this.params.where[key] = newVal[key]
                }
            }
            this.params.where.status = '0'
            this.params.order = ['no_presensi asc','nama asc']
            console.log(this.params)
            this.$nextTick(() => {
                this.$refs.tableData.getData()
            })
            },
            deep: true,
        },
        'filter.id_kelas': function(newVal) {
          this.addValue.id_kelas = newVal;
        }, 
    },
    computed: {
      ...mapState(useAuthStore,{
        user:'loggedUser'
      }),
      showLabel(){
        return this.$windowWidth > 800
      },
    },
    methods: {
      getInitial: async function() {
          this.loading = true;
          await this.$http.get('/kolom/preparation?table=sch__santri&grouping=0&input=0')
            .then(result => {
              var res = result.data;
              this.fields = {
                ...{
                  no_presensi:{
                    nama_kolom:'no_presensi',
                    label:'No. Presensi',
                    width:'100px',
                    sortable:true,
                    align:'center',
                  },
                },
                ...res,
              }
              this.loading = false
              this.$nextTick(() => {
                this.$refs.tableData.getData()
              })
            });
          await this.$http.get('/data/kelas/options')
            .then(result => {
              this.loading = false;
              var res = result.data;
              this.filterFields.id_kelas.options = res;
              this.filter.id_kelas = this.storeFilters?.id_kelas ? this.storeFilters?.id_kelas : this.user.id_kelas?? res[0]?.value
            });
          await this.$http.get('/data/santri/options')
            .then(result => {
              this.loading = false;
              var res = result.data;
              this.fieldsAdd.id_santri.options = res;
            });
        },
      getTable(){
        this.$refs.tableData.getData()
      }
    },
    created: function() {
      this.getInitial();
    }
  }
  </script>
  
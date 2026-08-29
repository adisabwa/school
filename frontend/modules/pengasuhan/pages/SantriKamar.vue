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
        <div>
          <div v-if="showAdd"
            class="bg-slate-100 p-3 border-slate-500">
            <form-comp ref="formAdd"
              :key="formKeyAdd"
              class="mb-2 flex gap-4"
              form-class="m-0 w-full"
              size="large"
              v-model:form-value="addValue"
              :fields="fieldsAdd"
              :show-label="false" 
              submit-text="Simpan dan Lanjutkan"
              error-submit-text="Gagal menambahkan santri ke kamar"
              :show-required-text="false"
              href="data/santri-kamar/store"
              @saved="getTable(); showAdd = isContinue; isContinue = true; formKeyAdd++; addValue.id_santri = '';">
              <template #submit="{ submitForm }">
                <el-button type="primary" @click="submitForm(); isContinue = false;" size="large">Simpan</el-button>
              </template>
            </form-comp>
          </div>
          <el-button v-else type="success" class="w-full" 
            @click="showAdd = true; addValue.id_santri = ''">
            <icons icon="mdi:plus"/> Tambah Santri ke Kamar
          </el-button>
        </div>
        <table-data ref="tableData" :fields="fields" href="data/santri-kamar" hrefGet="data/santri/get" hrefStore="data/santri/store"
            :checked="false" 
            :show-upload="false" :show-create="false" :show-dropdown="false"
            :pass-columns-input="['id_kelas']"
            :params="params">
            <template #default="{ handleActionClick }">
              <el-table-column label=" " width="120" align="center">
                <template #default="scope">
                  <el-button type="primary" size="large" class="px-2"
                    @click="handleActionClick({id:scope.row.id_santri, action:'edit'})">
                    <icons icon="mdi:edit" class="m-0"/>
                  </el-button>
                  <el-button type="danger" size="large" class="px-2"
                    @click="handleActionClick({id:scope.row.id, action:'delete'})">
                    <icons icon="mdi:delete" class="m-0"/>
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
import { useAuthStore } from '@/config/stores/authStore'
  
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
          id_kamar:{
            label:'Kamar',
            nama_kolom:'id_kamar',
            input:'select',
            options:[],
          },
        },
        filter:{
          id_kamar:'-1',
        },
        addValue:{
          id_kamar:'-1',
          id_santri:'',
        },
        params:{
          where:[],
          order:['id_kelas', 'nama'],
        },
        fields:{
          id_kelas:{nama_kolom:'id_kelas', view_kolom:'kelas', label:'Kls', sortable:true, width:'60px', align:'center'},
          nama:{nama_kolom:'nama', label:'Nama Santri', sortable:true, min_width:'150px'},
          nama_arab:{nama_kolom:'nama_arab', label:'Nama Arab', sortable:true, min_width:'100px'},
          id_daerah:{nama_kolom:'id_daerah', view_kolom:'daerah', label:'Asal', sortable:true, width:'150px', options:[],input:'select', align:'center'},
          stb:{nama_kolom:'stb', label:'STB', sortable:true, width:'100px', align:'center'},
        },
        fieldsAdd:{
          id_kamar:{
            nama_kolom:'id_kamar',
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
            console.log(this.params)
            this.$nextTick(() => {
                this.$refs.tableData.getData()
            })
            },
            deep: true,
        },
        'filter.id_kamar': function(newVal) {
          this.addValue.id_kamar = newVal;
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
    },
    methods: {
      getInitial: async function() {
          this.loading = true;
          await this.$http.get('/data/kamar/options',{
            params:{
              order:['komplek','rayon'],
            }
          })
            .then(result => {
              this.loading = false;
              var res = result.data;
              this.filterFields.id_kamar.options = res;
              if (this.role != 'admin') this.filterFields.id_kamar.readonly = true;
              this.filter.id_kamar = this.storeFilters?.id_kamar ? this.storeFilters?.id_kamar : this.user.id_kamar?? res[0]?.value
            });
          await this.$http.get('/data/daerah/options')
            .then(result => {
              this.loading = false;
              var res = result.data;
              this.fields.id_daerah.options = res;
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
  
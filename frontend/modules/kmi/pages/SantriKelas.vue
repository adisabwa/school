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
              error-submit-text="Gagal menambahkan santri ke kelas"
              :show-required-text="false"
              href="data/santri-kelas/store"
              @saved="onUpdated(); showAdd = isContinue; isContinue = true">
              <template #submit="{ submitForm }">
                <el-button type="primary" @click="submitForm(); isContinue = false;" size="large">Simpan</el-button>
                <el-button type="danger" @click="showAdd = false" size="large">
                  <icons icon="material-symbols:cancel" class="m-0" />
                </el-button>
              </template>
            </form-comp>
          </div>
          <div v-else class="flex">
            <el-button type="success" class="w-full" 
              @click="showAdd = true; addValue.id_santri = ''">
              <icons icon="mdi:plus"/> Tambah Santri ke Kelas
            </el-button>
            <el-button type="success" class="w-full" 
              @click="showAdd = true; showMany = true; addValue.id_santri = ''">
              <icons icon="mdi:upload"/> Upload Data
            </el-button>
            <el-button type="primary" class="w-full" v-if="tingkat == '6' && !lulusAll"
              @click="santriLulusAll">
              <icons icon="game-icons:graduate-cap"/> Ubah Status Kelulusan
            </el-button>
          </div>
        </div>
            
        <excel-dialog 
          v-model:show="showMany" :href="'data/santri-kelas/store_many'"  @saved=" showAdd = false; showMany = false; onUpdated();" :fields="fieldsAdd"
          :default-value="[
          {key:'id_kelas', value:filter.id_kelas},
          {key:'tahun_ajaran', value:filter.tahun_ajaran},
          ]"/>

        <table-data ref="tableData" :fields="fields" href="data/santri-kelas" hrefGet="data/santri/get" hrefStore="data/santri/store"
            :checked="false" 
            :show-upload="false" :show-create="false" :show-dropdown="false"
            :pass-columns-input="['id_kelas']"
            v-model:datas-value="tableValues"
            :params="params">
            <template #default="{ handleActionClick }">
              <el-table-column label=" " width="200" align="center">
                <template #default="scope">
                  <el-button type="primary" size="large" class="px-2"
                    @click="handleActionClick({id:scope.row.id_santri, action:'edit'})">
                    <icons icon="mdi:edit" class="m-0"/>
                  </el-button>
                  <el-button type="danger" size="large" class="px-2"
                    @click="deleteSantri(scope.row)">
                    <icons icon="mdi:delete" class="m-0"/>
                  </el-button>
                  <el-button type="success" size="large" class="px-2" v-if="tingkat == '6' && scope.row?.status != '1'"
                    @click="santriLulus(scope.row)">
                    <icons icon="game-icons:graduate-cap" class="m-0"/>
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
      ExcelDialog: defineAsyncComponent(() =>
        import('@/components/table/ExcelDialog.vue')
      ),
    },
    data: function() {
      return {
        loading:false,
        showAdd:false,
        showMany:false,
        isContinue:true,
        data:{},
        formKeyAdd:-1,
        filterFields: {
          tahun_ajaran:{
            label:'Tahun Ajaran',
            nama_kolom:'tahun_ajaran',
            input:'select',
            options:[],
          },
          id_kelas:{
            label:'Kelas',
            nama_kolom:'id_kelas',
            input:'select',
            options:[],
          },
        },
        filter:{
          id_kelas:'-1',
          tahun_ajaran:'',
        },
        params:{
          where:[],
          order:['nama'],
        },
        fields:{
          nisn:{nama_kolom:'nisn', label:'NISN', sortable:true, width:'150px', align:'center'},
          stb:{nama_kolom:'stb', label:'STB', sortable:true, width:'100px', align:'center'},
          nama:{nama_kolom:'nama', label:'Nama Santri', sortable:true, min_width:'150px'},
          nama_arab:{nama_kolom:'nama_arab', label:'Nama Arab', sortable:true, min_width:'100px'},
          id_daerah:{nama_kolom:'id_daerah', view_kolom:'daerah', label:'Asal', sortable:true, width:'150px', options:[],input:'select', align:'center'},
        },
        addValue:{
          id_kelas:'-1',
          tahun_ajaran:'', 
          id_santri:'',
        },
        fieldsAdd:{
          id_kelas:{
            nama_kolom:'id_kelas',
            label:'Kelas',
            hidden:true,
          },
          tahun_ajaran:{
            nama_kolom:'tahun_ajaran',
            label:'Tahun Ajaran',
            hidden:true,
          },
          id_santri:{
            nama_kolom:'id_santri',
            label:'Santri',
            input:'select',
            options:[],
            required:true,
            add_href:'data/santri/store',
            allow_add:'1',
            add_col:'nama',
            add_reset:'data/santri/options'
          },
        },
        state: reactive({
          passColumns : [],
          showColumns : [],
        }),
        tableValues: [],
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
        'filter.id_kelas': function(newVal) {
          this.addValue.id_kelas = newVal;
        }, 
        'filter.tahun_ajaran': function(newVal) {
          this.addValue.tahun_ajaran = newVal;
        },
        addValue: {
          deep:true,
          handler: function(newVal) {
            console.log(newVal)
          }
        }
    },
    computed: {
      ...mapState(useAuthStore,{
        user:'loggedUser',
        role: 'role',
      }),
      showLabel(){
        return this.$windowWidth.value > 800
      },
        tingkat(){
          let options = this.filterFields.id_kelas.options
          if (options) {
            if (typeof options == 'object') options = Object.values(options)
            let kelas = options.find(d => d.value == this.filter.id_kelas)
            return kelas?.tingkat ?? '1'
          } else {
            return '1'  
          }
        },
        lulusAll(){
          let value = true
          this.tableValues.forEach(d => {
            if (d.status != '1')
              value = false
          })
          return value
        },
    },
    methods: {
      getInitial: async function() {
          this.loading = true;
          await this.$http.get('/data/kelas/options',{
            params:{
              order:['tingkat'],
            }
          })
            .then(result => {
              this.loading = false;
              var res = result.data;
              this.filterFields.id_kelas.options = res;
              if (this.role != 'admin') this.filterFields.id_kelas.readonly = true;
              this.filter.id_kelas = this.storeFilters?.id_kelas ? this.storeFilters?.id_kelas : this.user.id_kelas?? res[0]?.value
              // this.filter.id_kelas = 13
            });
          await this.$http.get('/data/semester/options_tahun_ajaran')
            .then(result => {
              this.loading = false;
              var res = result.data;
              this.filterFields.tahun_ajaran.options = res;
              if (this.role != 'admin') this.filterFields.tahun_ajaran.readonly = true;
              this.filter.tahun_ajaran = this.storeFilters?.tahun_ajaran ? this.storeFilters?.tahun_ajaran : res[0]?.value
            });
          await this.$http.get('/data/daerah/options')
            .then(result => {
              this.loading = false;
              var res = result.data;
              this.fields.id_daerah.options = res;
            });
          await this.getSantriList();
        },
      async getSantriList(){
        await this.$http.get('/data/santri/options_kelas', {
          params:{
            'tahun_ajaran':this.filter.tahun_ajaran,
          }}
        )
          .then(result => {
            this.loading = false;
            var res = result.data;
            this.fieldsAdd.id_santri.options = res;
          });
      },
      getTable(){
        this.$refs.tableData.getData()
        this.getSantriList();
      },
      onUpdated(){
        console.log('onUpdated')
        this.getTable(); 
        this.formKeyAdd++; 
        this.addValue.id_santri = '';
      },
      santriLulus(row){
        let formData = window.jsonToFormData({
          id: row.id_santri,
          status:'1',
        })
        this.$confirm('Apakah Anda yakin ingin mengubah status santri ini menjadi lulus?', 'Konfirmasi', {
          confirmButtonText: 'Ya',
          cancelButtonText: 'Keluar',
          closeButtonText:'Batal',
          // type: 'warning',
        }).then(() => {
            this.$http.post('/data/santri/store', formData)
              .then(result => {
                this.$notify({
                  type:'success',
                  title: 'Berhasil',
                  message: 'Santri berhasil diubah',
                  position: 'bottom-right'
                });
                this.$refs.tableData.getData()
              })
          })
      },
      santriLulusAll(){
        let datas = []
        this.tableValues.forEach(d => {
          datas.push({
            id: d.id_santri,
            status:1,
          })
        })  
        console.log(datas)
        let formData = window.jsonToFormData(datas)
        this.$confirm('Apakah Anda yakin ingin mengubah status santri ini menjadi lulus?', 'Konfirmasi', {
          confirmButtonText: 'Ya',
          cancelButtonText: 'Keluar',
          closeButtonText:'Batal',
          // type: 'warning',
        }).then(() => {
            this.$http.post('/data/santri/store_many', formData)
              .then(result => {
                this.$notify({
                  type:'success',
                  title: 'Berhasil',
                  message: 'Santri berhasil diubah',
                  position: 'bottom-right'
                });
                this.$refs.tableData.getData()
              })
          })
      },
      deleteSantri(row){
        this.$confirm('Apakah Anda yakin ingin menghapus santri ini dari kelas? Kenapa anda menghapusnya?', 'Konfirmasi', {
          confirmButtonText: 'Salah Kelas',
          cancelButtonText: 'Keluar',
          closeButtonText:'Batal',
          cancelButtonClass : 'bg-red-500 text-white border-0',
          confirmButtonClass: 'bg-orange-500 text-white border-0',
          type: 'warning',
          distinguishCancelAndClose: true,
        }).then(() => {
          this.$http.delete(`/data/santri-kelas/delete/${row.id}`)
            .then(result => {
              this.$notify({
                type:'success',
                title: 'Berhasil',
                message: 'Data berhasil dihapus',
                position: 'bottom-right'
              });
              this.$refs.tableData.getData()
            })
            .catch(error => {
              this.$notify({
                type:'error',
                title: 'Gagal',
                message: 'Tidak dapat menghapus data',
                position: 'bottom-right'
              });
            });
        }).catch((action) => {
          console.log(action)
          if (action == 'cancel') {
            this.$http.delete(`/data/santri-kelas/delete/${row.id}`)
              .then(result => {                
                let formData = window.jsonToFormData({
                  id: row.id_santri,
                  status:'-1',
                });
                this.$http.post('/data/santri/store', formData)
                  .then(result => {
                    this.$notify({
                      type:'success',
                      title: 'Berhasil',
                      message: 'Santri berhasil dihapus',
                      position: 'bottom-right'
                    });
                    this.$refs.tableData.getData()
                  })
              })
          } 
          // Batal menghapus
        });
      },
    },
    created: function() {
      this.getInitial();
    }
  }
  </script>
  
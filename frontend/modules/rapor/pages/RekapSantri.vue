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
        <b class="text-[14px] absolute">Pindahkan data dengan mengklik panah / mendrag nama santri</b>
        <draggable-table-data ref="tableData" :fields="fields" href="data/santri"
            :use-handle="true"
            :checked="false" :sortable="false"
            v-model:datas-value="data"
            :show-columns="['no_presensi', 'stb','nisn','nama','nama_arab','id_daerah']"
            :show-columns-input="['no_presensi', 'status', 'stb','nisn','nama','nama_arab','id_daerah']"
            :show-upload="false" :show-create="false" :show-dropdown="false" :show-index="false"
            :params="params"
            @on-drag-end="(ndata) => {
              console.log(ndata)
              data = ndata
              reArrangePresensi()
            }">
            <template #before-no_presensi="{ handleActionClick }">
              <el-table-column label=" " width="40" align="center" fixed>
                <template #default="scope">
                  <div class="flex flex-col justify-center items-center handle">
                    <el-button type="text" size="small" class="p-0 m-0 h-fit text-blue-500"
                        @click="moveElement(scope.row, -1)">
                        <icons icon="ep:arrow-up" class="m-0 text-[14px]"/>
                    </el-button>
                    <el-button type="text" size="small" class="p-0 m-0 h-fit text-blue-500"
                        @click="moveElement(scope.row, 1)">
                        <icons icon="ep:arrow-down" class="m-0 text-[14px]"/>
                    </el-button>
                  </div>
                </template>
              </el-table-column>
            </template>
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
        </draggable-table-data>
    </el-card>
  </div>
</template>
  
  <script>
    
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
            label:'Kelas',
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
            label:'Kelas',
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
            this.params.where = {
              '{n}id_kelas ': newVal.id_kelas,
              '{n}kelas_santri >': '0',
              'status': '0',
            }
            this.params.order = ['no_presensi asc','nama asc']
            console.log(this.params)
            this.$nextTick(() => {
                this.getTable()
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
        return this.$windowWidth.value > 800
      },
    },
    methods: {
      getInitial: async function() {
          this.loading = true;
          await this.$http.get('/kolom/preparation?table=' + this.$prefixTable + '_santri&grouping=0&input=0')
            .then(result => {
              var res = result.data;
              this.fields = {
                ...{
                  no_presensi:{
                    nama_kolom:'no_presensi',
                    label:'No. Presensi',
                    width:'70px',
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
      savePresensi(){
        let els = []
        this.data.forEach((d, key) =>{
          els.push({
            id: d.id,
            no_presensi: key + 1,
          })
        })
        let formValue = window.jsonToFormData(els)
        this.$http.post('/data/santri/store_many', formValue)
          .then(res => {
            let rapor = res.data
            this.getTable()
          })
          .catch(err => {
            console.log(err)
            this.$notify.error({
              title: 'Gagal',
              message: 'Presensi tidak berhasil disimpan',
              position: 'bottom-right'
            });
          })
      },
      reArrangePresensi(){
        this.data.forEach((d, key) =>{
          d.no_presensi = key + 1
        })
        this.savePresensi()
        // console.log('Data setelah di-rearrange:', this.data[0]);
      },
      moveElement(row, direction){
        let index = this.data.findIndex(d => d.id === row.id)
        console.log(index, direction)
        if (index >= 0) {
          if ((index === 0 && direction === -1) || (index === this.data.length - 1 && direction === 1)) {
            return
          }
          [this.data[index + direction], this.data[index]] = [this.data[index], this.data[index + direction]];
          this.reArrangePresensi()
        }
      },
      getTable(){
        if (this.filter.id_kelas > 0)
          this.$refs.tableData.getData()
      }
    },
    created: function() {
      this.getInitial();
    }
  }
  </script>
  
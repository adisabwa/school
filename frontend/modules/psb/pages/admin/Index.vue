<template>
    <div id="psb" class="py-6">
      <el-card class="bg-white/[0.7]">
        <form-comp ref="formFilter"
          :fields="filterFields"
          :label-position="labelPosition"
          class="mt-3"
          form-class="mb-0"
          form-item-class="mb-2"
          label-width="180px"
          v-model:form-value="filter"
          :show-submit="false"
          text-submit="Cari"
          error-submit-text="Tidak dapat mengambil data"
          :show-required-text="false"
          ></form-comp>
        <el-button type="primary"
          @click="searchData"
          ><icons icon="mdi:search"/>Cari</el-button>
        <table-data ref="tableData" :href="hrefData" :params="params"
          :show-create="true" 
          :show-search="true" 
          :show-download="true"
          :show-upload="false" 
          :show-dropdown="false"
          :title="'Data Calon Santri'"
          v-model:checked-id="ids"
          :fields="fields"
          class="p-0 mt-3">
          <template #menu>
            <el-button type="primary" class="h-full" @click="handleActionClick({action:'download-many'})" size="small"> 
              <icons icon="mdi:download"/> Unduh Kartu Pendaftaran
            </el-button>
          </template>
          <template #nama-inside="el">
            {{ el.scope.row.nama  }} <br/>
            {{ el.scope.row.nisn  }} 
          </template>
          <template #ayah_nama-inside="el">
              Ayah : {{ el.scope.row.ayah_nama  }} ( {{ el.scope.row.ayah_nik  }} ) <br/>
              Ibu : {{ el.scope.row.ibu_nama  }} ( {{ el.scope.row.ibu_nik  }} ) <br/>
              Wali : {{ el.scope.row.wali_nama  }} ( {{ el.scope.row.wali_nik  }}  )
          </template>
          <el-table-column
            width="120">
            <template #default="scope">
              <el-dropdown trigger="click" @command="handleActionClick">
                <el-button type="primary" size="small"> 
                  Aksi <icons icon="fe:arrow-down" class="el-icon--right" />
                </el-button>
                <template #dropdown>
                  <el-dropdown-menu slot="dropdown">
                    <el-dropdown-item 
                      :command="{action: 'view', nisn: scope.row.nisn, id: scope.row.id}">
                      <icons icon="lsicon:view-filled"/> Lihat Data</el-dropdown-item>
                    <el-dropdown-item 
                      :command="{action: 'edit', nisn: scope.row.nisn, id: scope.row.id}">
                      <icons icon="material-symbols:edit-outline"/> Ubah</el-dropdown-item>
                    <el-dropdown-item v-if="scope.row.status == '0'"
                      :command="{action: 'delete', id: scope.row.id}">
                      <icons icon="material-symbols:delete-outline"/> Hapus</el-dropdown-item>
                    <el-dropdown-item 
                      :command="{action: 'download', id:scope.row.id}" >
                      <icons icon="mdi:download"/> Unduh Kartu Pendaftaran</el-dropdown-item>
                  </el-dropdown-menu>
                </template>
              </el-dropdown>
            </template>
          </el-table-column>
        </table-data>
      </el-card>
      <teleport to="body">
        <el-dialog 
          v-model="showView"
          class="p-7 w-fit min-w-[50vw] my-10"
          :close-on-click-modal="false">
          <PsbView />
        </el-dialog>
      </teleport>
    </div>
</template>
  
<script>
  import { mapState } from 'pinia';
  import { setStatusText, setStatusType } from '@/modules/psb/helpers/psb'
  import PsbView from '../View.vue'
  import { useAuthStore } from '@/config/stores/authStore'
  import { useDataStore } from '@/config/stores/dataStore'

  export default {
    name: "psb",
    setup(){
      return {
        isEmpty,
      }
    },
    components: {
      PsbView,
    },
    data: function() {
      return {
        showView:false,
        loading: false,
        filterFields: [
          {
            nama_kolom:'nama',
            label:'Nama Calon Santri'
          },
          {
            nama_kolom: 'nomor',
            label:'NISN atau NIK Wali Santri'
          },
        ],
        fields:{
          no_pendaftaran: {
            nama_kolom:'no_pendaftaran',
            label:'No. Pendaftaran',
            'width':'150px',
            sort:'',
            sortable:'1',
            align:'left',
          },
          nama: {
            nama_kolom:'nama',
            label:'Nama Calon Santri / NISN',
            'min-width':'300px',
            sort:'',
            sortable:'1',
            align:'left',
            hide_content: true,
          },
          ayah_nama: {
            nama_kolom:'ayah_nama',
            label:'Data Orang Tua',
            'min-width':'250px',
            sort:'',
            sortable:'1',
            align:'left',
            hide_content: true,
          },
        },
        filter:{
          nama:'',
          nomor:'',
        },
        params:{},
        editId:-1,
        ids:[],
        setStatusText: setStatusText,
        setStatusType: setStatusType,
        hrefData:'psb/admin',
      };
    },
    watch: {
      'filter.nama': function(val) {
        useDataStore().setFilter({key:nama, val:val})
      },
      'filter.nomor': function(val) {
        useDataStore().setFilter({key:nomor, val:val})
      },
      'paging.currentPage': function(val) {
        this.paging.offset = val * this.paging.perPage - this.paging.perPage;
      },
    },  
    computed: {
      ...mapState(useAuthStore, {
        user: 'loggedUser',
      }),
      labelPosition(){
        return this.$windowWidth.value < 800 ? 'top' : 'left'
      },
    },
    methods: {
      searchData(){
        this.params = {
          where: {
          }
        }
        console.log(this.filter)
        if (!isEmpty(this.filter.nama)) {
          let nama = "nama LIKE '%"+this.filter.nama+"%'"
          this.params.where[0] = nama
          console.log('nama', this.params.where)
        }
        if (!isEmpty(this.filter.nomor)) {
          let nomor = "(nisn LIKE '%"+this.filter.nomor+"%' OR nik LIKE '%"+this.filter.nomor+"%'' OR ayah_nik LIKE '%"+this.filter.nomor+"%' OR ibu_nik LIKE '%"+this.filter.nomor+"%' OR wali_nik LIKE '%"+this.filter.nomor+"%')"
          this.params.where[1] = nomor
        }
        console.log(this.params)
      },
      handleActionClick: function(obj) {
        var action = obj.action;
        var id = obj.id;
        var nisn = obj.nisn;
        var status = obj.status;
        var index = obj.index;
        useDataStore().setFilter({key:'keyword', val:nisn})
        if (action == 'edit') {
          this.$router.push({name:'psb-create', query:{id:id}})
        } else if (action == 'view') {
          this.showView = true
        } else if (action == 'pay') {
          this.statusPsb(id, status);
        } else  if (action == 'pay-many') {
          this.statusMany(status);
        } else  if (action == 'download') {
          this.downloadPsb(id);
        } else  if (action == 'download-many') {
          this.downloadMany();
        } else {
          this.$refs.tableData.handleActionClick(obj);
        } 
      },
      statusPsb: function(id, status) {
        this.$confirm('Yakin mengubah status data ini?', 'Konfirmasi', {
          confirmButtonText: 'Ubah',
          cancelButtonText: 'Batal',
          type: 'warning'
        }).then(() => {
          this.$http.post('/psb/admin/status/' + id + '/' + status)
            .then(result => {
              this.$notify.success({
                title: 'Berhasil',
                message: 'Data berhasil diubah',
                position: 'bottom-right'
              });
              this.searchData()
            })
            .catch(err => {
              this.$notify.error({
                title: 'Gagal',
                message: 'Tidak dapat mengubah status',
                position: 'bottom-right'
              });
            });        
          }).catch(() => {
          // Do nothing          
        });
      },
      statusMany: function(status) {
        this.$confirm('Yakin mengubah status data yang di checklist?', 'Konfirmasi', {
          confirmButtonText: 'Ubah',
          cancelButtonText: 'Batal',
          type: 'warning'
        }).then(() => {
          let data = window.jsonToFormData({ id: this.ids , status : status})
  
          this.$http.post(`psb/admin/status_many`, data)
            .then(result => {
              this.$notify.success({
                title: 'Berhasil',
                message: 'Data berhasil diubah',
                position: 'bottom-right'
              });
              this.searchData()
            })
            .catch(err => {
              console.log(err)
              this.$notify.error({
                title: 'Gagal',
                message: 'Tidak dapat mengubah status',
                position: 'bottom-right'
              });
            });        
          }).catch(err => {
            console.log(err)
          // Do nothing          
        });
      },
      downloadPsb: function(id, index) {
        window.open(this.$siteUrl + '/psb/admin/download/'+id,'_blank')
      },
      downloadMany: function() {
          let url = ''
          url = this.$siteUrl + '/psb/admin/download_many'
          // Replace with your URL
  
          // Create a form
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = url;
          form.target = '_blank'; // Open in a new tab
        
          console.log(this.ids)
          // Add data as hidden input fields
          this.ids.forEach((s, key) => {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = `id[${key}]`;
            input.value = s;
            form.appendChild(input);
          })
  
          // Append the form to the body and submit it
          document.body.appendChild(form);
          form.submit();
  
          // Remove the form after submission
          document.body.removeChild(form);
      },
    },
    created: function() {
      let filter = useDataStore().filter
      this.filter.nama = filter?.nama ?? ''
      this.filter.nomor = filter?.nomor ?? ''
      // console.log(this.$router);
    },
    mounted: function() {
      this.searchData()
    },
  }
  </script>
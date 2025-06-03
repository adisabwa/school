<template>
    <div id="psb" class="py-6">
      <el-card class="bg-white/[0.7]">
        <form-filter ref="formFilter"
          :fields="filterFields"
          :label-position="labelPosition"
          class="mt-6"
          label-width="250px"
          @form-value="getFilter"
          :show-submit="false"
          text-submit="Cari"
          error-submit-text="Tidak dapat mengambil data"
          :show-required-text="false"
          ></form-filter>
        <div class="mt-3 flex flex-row justify-between">
          <div class="flex flex-col md:flex-row gap-3 justify-start">
            <el-dropdown trigger="click" @command="handleActionClick">
              <el-button type="primary"> 
                Ubah Checklist <icons icon="fe:arrow-down" class="el-icon--right" />
              </el-button>
              <template #dropdown>
                <el-dropdown-menu slot="dropdown">
                  <el-dropdown-item class="text-green-600"
                    :command="{action: 'pay-many', status:'1'}">
                    <icons icon="mdi:money"/> Sudah Dibayar</el-dropdown-item>
                  <el-dropdown-item class="text-sky-600"
                    :command="{action: 'pay-many', status:'2'}">
                    <icons icon="mdi:check"/> Verifikasi</el-dropdown-item>
                  <el-dropdown-item class="text-red-500"
                    :command="{action: 'delete-many'}" >
                    <icons icon="material-symbols:delete-outline"/> Hapus Data</el-dropdown-item>
                  <el-dropdown-item
                    :command="{action: 'download-many'}" >
                    <icons icon="mdi:download"/> Unduh Kartu Pendaftaran</el-dropdown-item>
                </el-dropdown-menu>
              </template>
            </el-dropdown>
            <el-button type="success" @click="showUpload = true" class="m-0">
              <icons icon="mdi:download"/>
              Download Excel</el-button>
          </div>
          <el-button type="primary"
            @click="searchData"
            ><icons icon="mdi:search"/>Cari</el-button>
        </div>
        <table-data ref="tableData" :href="hrefData" :params="params"
          :show-create="false" :show-search="false" :show-dropdown="false"
          :upload="false" :add-columns="['nomor']"
          :title="'Data Calon Santri'"
          v-model:checked-id="ids"
          :fields="fields"
          class="p-0">
          <template #nama-inside="el">
            {{ el.scope.row.nama  }} <br/>
            {{ el.scope.row.nisn  }} 
          </template>
          <template #ayah_nama-inside="el">
              Ayah : {{ el.scope.row.ayah_nama  }} ( {{ el.scope.row.ayah_nik  }} ) <br/>
              Ibu : {{ el.scope.row.ibu_nama  }} ( {{ el.scope.row.ibu_nik  }} ) <br/>
              Wali : {{ el.scope.row.wali_nama  }} ( {{ el.scope.row.wali_nik  }}  )
          </template>
          <el-table-column label="Status" min-width="130" align="center">
            <template #default="scope">
              <el-tag :type="setStatusType(scope.row.status) "  effect="dark">
                {{ setStatusText(scope.row.status) }}
              </el-tag>
            </template>
          </el-table-column>
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
                      :command="{action: 'edit', nisn: scope.row.nisn}">
                      <icons icon="material-symbols:edit-outline"/> Ubah</el-dropdown-item>
                    <el-dropdown-item 
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
    </div>
  </template>
  
  <script>
  import { mapGetters } from 'vuex';
  import { setStatusText, setStatusType } from '@/helpers/psb'
  import Form from '@/components/Form.vue'
  import TableData from '@/components/TableData.vue'
  
  export default {
    name: "psb",
    components: {
      'form-filter' : Form,
      TableData,
    },
    data: function() {
      return {
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
            'min-width':'200px',
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
        params:{
          nama:'-1',
          nomor:'-1',
        },
        editId:-1,
        ids:[],
        sizeWindow:window.innerWidth,
        setStatusText: setStatusText,
        setStatusType: setStatusType,
        hrefData:'psb/admin',
      };
    },
    watch: {
      'filter.nama': function(val) {
        this.$store.dispatch('saveFilter', {ref:'nama', data: val})
      },
      'filter.nomor': function(val) {
        this.$store.dispatch('saveFilter', {ref:'nomor', data: val})
      },
      'paging.currentPage': function(val) {
        this.paging.offset = val * this.paging.perPage - this.paging.perPage;
      },
    },  
    computed: {
      ...mapGetters({
        user: 'loggedUser',
      }),
      labelPosition(){
        return this.sizeWindow < 800 ? 'top' : 'left'
      },
    },
    methods: {
      searchData(){
        this.resetObjectValue(this.params)
        this.fillObjectValue(this.params, this.filter)
      },
      getFilter(filter){
        this.fillObjectValue(this.filter, filter)
      },
      handleActionClick: function(obj) {
        var action = obj.action;
        var id = obj.id;
        var nisn = obj.nisn;
        var status = obj.status;
        var index = obj.index;
        if (action == 'edit') {
          this.$router.push({name:'psb-view'})
          this.$store.dispatch('saveFilter', {ref:'keyword', data: nisn})
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
      let filter = this.$store.getters.filter
      this.filter.nama = this.isEmpty(filter.nama) ? '' : filter.nama
      this.filter.nomor = this.isEmpty(filter.nomor) ? '' : filter.nomor
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
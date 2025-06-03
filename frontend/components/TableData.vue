<template>
  <div id="table-data">
    <div v-if="showCreate || showSearch"
      class="flex flex-col sm:flex-row mb-3 gap-4
      mt-7 sm:mt-2">
      <div v-if="showCreate" class="w-full
        grid grid-cols-2 [&_*]:m-0
        gap-x-3 gap-y-2
        sm:block sm:[&_*]:mr-3">
        <el-button class="py-[15px]" type="primary" size="small" v-if="upload" @click="showUpload = true">
          <icons icon="mdi:upload"/>
          Upload Excel</el-button>
        <el-button class="py-[15px]" type="success" size="small" @click="handleActionClick({action:'add'})">
          <icons icon="mdi:plus"/>
          Buat Baru</el-button>
        <el-button class="py-[15px]" type="primary" size="small" @click="handleActionClick({action:'edit-all'})">
          <icons icon="mdi:edit"/>
          Edit Bersama</el-button>
        <el-button class="py-[15px]" type="danger" size="small" v-if="checked" @click="deleteMany">
          <icons icon="mdi:trash"/>
          Delete Checklist</el-button>
        <slot name="menu" :action="handleActionClick"></slot>
      </div>
      <div v-if="showSearch" class="w-full sm:w-1/3 flex">
        <el-select v-model="searchField" placeholder="Kolom"
          :empty-values="[null, undefined]"
          :value-on-clear="null"
          clearable>
          <el-option value="" label="Semua"/>
          <el-option v-for="field in fields"
            :value="field.nama_kolom" :label="field.label"/>
        </el-select>
        <el-input placeholder="Cari..." class="min-w-[200px] rounded-full" size="default" type="search" @input="isTyping=true" v-model="searchKeyword" />
      </div>
    </div>
    <el-card class="bg-white/[0.7]">
      <loading v-if="loading" />
      <el-table
        v-else
        :data="datasFilter.slice(paging.offset, paging.offset + paging.perPage)"
          :header-cell-style="{ verticalAlign: headerVerticalAlign }"
          :cell-style="{ verticalAlign: verticalAlign}"
        stripe fit>
        <el-table-column
          v-if="checked"
          width="30" align="center">
          <template #header>
            <el-checkbox  
              v-model="checkAll"
              :indeterminate="isIndeterminate"
              @change="handleCheckAllChange"/>
          </template>
          <template #default="scope">
            <el-checkbox  
              v-model="scope.row.checked"
              @change="handleChange(scope)"/>
          </template>
        </el-table-column>
        <el-table-column
          width="60" label="No" align="center">
          <template #default="scope">
            {{ scope.$index + 1 + paging.offset }}
          </template>
        </el-table-column>
        <template v-for="(field, ind) in fields">
          <el-table-column v-if="!passColumns.includes(field.nama_kolom)"
            :width="field.width" :align="field.align" :min-width="field.min_width">
            <template #header="scope">
              <div class="flex items-center pointer"
                @click="changeSort(ind, field.sort)"
                :style="{
                  'justify-content' : field.align,
                  'font-weight': (order[0] == field.nama_kolom ? '900' : ''),
                }">
                <template v-if="field.sortable == '1'">
                  <icons v-if="field.sort == 'asc'" icon="bx:sort-down" class="mr-2 text-xl"/>
                  <icons v-else-if="field.sort == 'desc'" icon="bx:sort-up" class="mr-2 text-xl"/>
                  <icons v-else icon="bx:sort" class="mr-2 text-xl"/>
                </template>
                <div>{{ field.label }}</div>
              </div>
            </template>
            <template #default="scope">
              <template v-if="!field.hide_content">
                {{ runFunction(field.function, isEmpty(field.view_kolom) ? scope.row[field.nama_kolom] : scope.row[field.view_kolom], field.options)  }}
              </template>
              <slot :name="field.nama_kolom+'-inside'" :scope="scope" :field="field"></slot>
            </template>
          </el-table-column>
        </template>
        <slot></slot>
        <el-table-column v-if="showDropdown"
          width="120">
          <template #default="scope">
            <el-dropdown trigger="click" @command="handleActionClick">
              <el-button type="primary" size="small"> 
                Aksi <icons icon="fe:arrow-down" class="el-icon--right" />
              </el-button>
              <template #dropdown>
                <el-dropdown-menu slot="dropdown">
                  <el-dropdown-item 
                    :command="{action: 'edit', id: scope.row.id}">
                    <icons icon="material-symbols:edit-outline"/> Ubah</el-dropdown-item>
                  <el-dropdown-item 
                    :command="{action: 'delete', id: scope.row.id}">
                    <icons icon="material-symbols:delete-outline"/> Hapus</el-dropdown-item>
                </el-dropdown-menu>
              </template>
            </el-dropdown>
          </template>
        </el-table-column>
      </el-table>
      <div v-if="!isEmpty(order) && showOrderText" class="text-[12px] mt-4 text-left">
        Diurutkan berdasarkan : <br/>
        <span class="font-semibold">{{ orderText }}</span>
      </div>
      <el-row type="flex" justify="space-between" style="margin-top: 15px" v-if="paging.dataTotal > paging.perPage">
        <el-select v-model="paging.perPage" placeholder="Select" style="width:70px;">
          <el-option
            v-for="(item,key) in page"
            :key="key"
            :label="item"
            :value="item">
          </el-option>
        </el-select>
        <el-pagination
          background
          layout="total, prev, pager, next"
          :total="paging.dataTotal"
          v-model:page-size="paging.perPage"
          v-model:current-page="paging.currentPage">
        </el-pagination>
      </el-row>
    </el-card>

    <data-create v-model:show="showAdd"
      :title="title" :href="href + '/store'" :href-get="href + '/get'"
      :fields="fieldsCreate"
      :data-id="editId" :type="dataType"
      @saved="onUpdated"></data-create>
    
    <upload-dialog v-model:show="showUpload" title="Peminatan" :link="href" @saved="onUpdated" />
  </div>
</template>
  
<script>
  import { isEmpty, unset } from 'lodash';
  import DataCreate from './DataCreate.vue'
  import UploadDialog from '@/components/UploadDialog.vue'

  export default {
    name: "table-data",
    props:{
      type:'',
      component:'',
      showCreate:{
        type:Boolean,
        default: true,
      },
      showSearch:{
        type:Boolean,
        default: true,
      },
      showDropdown:{
        type:Boolean,
        default: true,
      },
      showOrderText:{
        type:Boolean,
        default: true,
      },
      fields:{
        type:[Array, Object],
        default: {},
      },
      passColumns:{
        type:Array,
        default: [],
      },
      href:{
        type:String,
        default: '',
      },
      params:{
        type:Object,
        default: {},
      },
      checked:{
        type:Boolean,
        default: true,
      },
      upload:{
        type:Boolean,
        default: true,
      },
      title:{
        type:String,
        default: '',
      },
      checkedId:{
        type:Array,
        default: [],
      },
      headerVerticalAlign:{
        type:String,
        default:'middle',
      },
      verticalAlign:{
        type:String,
        default:'middle',
      }
    },
    emits:['update:checkedId'],
    components: {
      DataCreate,
      UploadDialog,
    },
    data: function() {
      return {
        page: [10,20,30,40,50],
        showAdd: false,
        showUpload: false,
        editId: '',
        loading: false,
        datas: [],
        saving: false,
        searchField:'',
        searchKeyword: '',
        paging: {
          offset: 0,
          perPage: 10, // [10,20,30,40,50,100]
          currentPage: 1,
          dataTotal: 0,
        },
        checkAll:false,
        dataType: 'create',
        order:[],
        fieldsCreate:[],
      };
    },
    watch: {
      datasFilter: function(val) {
        this.paging.dataTotal = val.length;
        let checked = val.filter(d => d.checked).length
        this.checkAll = checked == val.length
      },
      'paging.currentPage': function(val) {
        this.paging.offset = val * this.paging.perPage - this.paging.perPage;
      },
      href(val){
        console.log(val)
        this.getData();
      },
      params: {
        handler(newVal, oldVal) {
          this.getData()
        },
        deep: true, // Watch nested properties
      },
      checkedIdList(val){
        this.$emit('update:checkedId', val)
      },
    },
    computed: {
      datasFilter: function() {
        var q = this.searchKeyword?.toLowerCase();
        if (q.length > 0) {
          let fields = this.fields
          let keys = Object.keys(fields)
          let vm = this
          return vm.datas?.filter(data => {
              let exist = false
              if (vm.isEmpty(vm.searchField)) {
                for (var i = 0; i < keys.length; i++) {
                  let ind = keys[i]
                  let field = fields[ind]
                  let text = vm.runFunction(field.function, data[field.nama_kolom])
                  if (text?.toLowerCase()?.includes(q)) {
                    exist = true
                    break
                  }
                }
              } else {
                let field = fields[vm.searchField]
                let text = vm.runFunction(field.function, data[field.nama_kolom])
                if (text?.toLowerCase()?.includes(q))
                  exist = true
              }
              return exist
            });
        }
        return this.datas;
      },   
      isIndeterminate(){
        let count = this.datasFilter.filter(d => d.checked).length
        // console.log(count, this.datasFilter.length)
        if (count == 0 || count == this.datasFilter.length)
          return false
        return true
      },
      hrefGet(){
        return this.href + '/'
      },
      checkedIdList(){
        let list = []
        this.datasFilter.forEach(d => {
          if (d.checked)
            list.push(d.id)
        });
        return list
      },
      orderText(){
        let text = []
        let vm = this
        vm.order.forEach(d => {
          text.push(vm.fields[d].label + ' ( ' + vm.fields[d].sort.toUpperCase() +' )')
        })
        return text.join(', ')
      }
    },
    methods: {
      handleChange(params){
        // console.log(params)
      },
      handleCheckAllChange(val){
        this.datasFilter.forEach(d => {
          d.checked = val
        });
      },
      changeSort(ind, sort_val){
        let f = this.fields[ind]
        let o = this.order
        let _io = o.indexOf(f.nama_kolom)
        if (_io > -1)
          o.splice(_io, 1)
        
        switch (sort_val) {
          case 'asc':
            sort_val = 'desc' 
            break   
          case 'desc':
            sort_val = ''
            break     
          default:
            sort_val = 'asc'
            break;
        }
        f.sort = sort_val
        if (sort_val != '')
          o.unshift(ind)
        this.getData()
      },
      async getData(){
        let list = []
        let f = this.fields
        this.order.forEach(o => {
          let field = f[o]
          list.push(o + ' ' + field.sort)
        })
        await this.$http.get(this.href, {
          params: {
            ...this.params,
            order:list
          }
        })
          .then(result => {
            this.loading = false;
            var res = result.data;
            this.datas = res;
            this.paging.dataTotal = this.datas.length;
          })
          .catch(err => {
            this.loading = false;
            this.$notify({
              type:'error',
              title: 'Gagal',
              message: 'Tidak dapat mengambil data',
              position: 'bottom-right',
            });
          });
      },
      handleActionClick: function(obj) {
        var action = obj?.action;
        var id = obj?.id;
        var cols = obj?.cols ?? [];
        var pass = obj?.pass ?? [];
        
        if (!action) {
          return
        }
        // console.log(obj, cols)
        if (cols.length > 0) {
          this.fieldsCreate = []
          for (let index = 0; index < cols.length; index++) {
            const element = cols[index];
            this.fieldsCreate[element] = this.fields[element]
          }
        } else {
          this.fieldsCreate = JSON.parse(JSON.stringify(this.fields));
        }

        if (pass.length > 0) {
          for (let index = 0; index < pass.length; index++) {
            const element = pass[index];
            delete this.fieldsCreate[element]
          }
        }
        // console.log(obj, this.fieldsCreate)
        if (action == 'add') {
          this.showAdd = true;
          this.dataType = 'create';
          this.editId = -1;
        } else if (action == 'edit') {
          this.editId = id;
          this.showAdd = true;
          this.dataType = 'update';
        } else if (action == 'edit-all') {
          let id = [];
          this.datasFilter.forEach(s => {
            if (s.checked)
              id.push(s.id)
          })
          this.editId = id;
          this.showAdd = true;
          this.dataType = 'update';
        } else if (action == 'delete') {
          this.$confirm('Apakah anda yakin untuk menghapus data ini?',
          'Konfirmasi',
          {
            confirmButtonText: 'OK',
            cancelButtonText: 'Batal',
            type: 'warning',
          })
          .then(() => {
            console.log(this.href)
            this.$http.get(this.href + `/delete/` + id)
              .then(result => {
                this.getData();
                this.$notify({
                  type:'success',
                  title: 'Berhasil',
                  message: 'Data berhasil dihapus',
                  position: 'bottom-right'
                });
              })
              .catch(err => {
                this.$notify({
                  type:'error',
                  title: 'Gagal',
                  message: 'Tidak dapat menghapus data, Data sudah digunakan',
                  position: 'bottom-right'
                });
              });
            })
           .catch(err => {
              console.log(err)
            });
        } else if (action == 'delete-many') {
          this.deleteMany()
        }
      },
      onUpdated: function(teacher) {
        this.showAdd = false;
        this.getData();
        this.$notify({
          type:'success',
          title: 'Berhasil',
          message: `Data ${this.title} berhasil diperbarui`,
          position: 'bottom-right'
        });
      },
      deleteMany: function() {
        this.$confirm('Yakin menghapus data yang di checklist?', 'Konfirmasi', {
          confirmButtonText: 'Hapus',
          cancelButtonText: 'Batal',
          type: 'error'
        }).then(() => {
          let id = [];
          this.datasFilter.forEach(s => {
            if (s.checked)
              id.push(s.id)
          })
          let data = window.jsonToFormData({ id:id })
  
          this.$http.post(this.href + `/delete_many`, data)
            .then(result => {
              this.$notify.success({
                title: 'Berhasil',
                message: 'Data berhasil dihapus',
                position: 'bottom-right'
              });
              this.getData();
            })
            .catch(err => {
              console.log(err)
              this.$notify.error({
                title: 'Gagal',
                message: 'Tidak dapat menghapus',
                position: 'bottom-right'
              });
            });        
          }).catch(err => {
            console.log(err)
          // Do nothing          
        });
      },
    },
    created: function() {
    }
  }
</script>
  
  
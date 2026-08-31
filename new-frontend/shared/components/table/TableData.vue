<template>
  <div id="table-data">
    <div v-if="showCreate || showSearch || showDownload || showUpload"
      class="flex flex-col md:flex-row mb-3 gap-4
      mt-7 md:mt-2">
      <div class="w-full
        grid grid-cols-[repeat(auto-fill,minmax(160px,1fr))] [&_button]:m-0
        gap-x-3 gap-y-2
        *:py-[15px] *:text-[12px]">
        <slot name="before-menu" :action="handleActionClick"></slot>
        <el-button  type="primary" v-if="showUploadNormal" @click="showUploadDialogNormal = true">
          <icons icon="mdi:upload"/>
          Upload Data</el-button>
        <el-button  type="primary" v-if="showUpload" @click="showUploadDialog = true">
          <icons icon="mdi:upload"/>
          Import Data</el-button>
        <template v-if="showCreate">
          <el-button  type="success" @click="handleActionClick({action:'add'})">
            <icons icon="mdi:plus"/>
            Buat Baru</el-button>
          <el-button  type="primary" @click="handleActionClick({action:'edit-all', id:1})">
            <icons icon="mdi:edit"/>
            Edit Bersama</el-button>
          <el-button  type="danger" v-if="checked" @click="deleteMany">
            <icons icon="mdi:trash"/>
            Delete Checklist</el-button>
        </template>
        <el-button type="success" 
          v-if="showDownload"
          @click="handleActionClick({action:'download-excel'})" >
          <icons icon="mdi:download"/>
          Download Excel</el-button>
        <slot name="menu" :action="handleActionClick"></slot>
      </div>
      <div v-if="showSearch" class="w-full md:w-1/3 flex h-fit">
        <el-select v-model="searchField" placeholder="Kolom"
          class="min-w-[120px]"
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
    <el-card class="bg-white/[0.7]"
      body-class="px-2 py-2 pb-3 sm:px-3">
      <loading v-if="loading" />
      <el-table
        class="max-sm:[&_.cell]:text-[12px]
          max-sm:[&_.el-table\_\_cell]:p-[5px]
          max-sm:[&_.cell]:p-0"
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
          :width="$windowWidth > 600 ? 50 : 30" label="No" align="center">
          <template #default="scope">
            {{ scope.$index + 1 + paging.offset }}
          </template>
        </el-table-column>
        <template v-for="(field, ind) in fields">
          <slot :name="'before-'+field.nama_kolom" :field="field" :fields="fields"></slot>
          <el-table-column v-if="showColumns.length > 0 ? showColumns.includes(field.nama_kolom) : !passColumns.includes(field.nama_kolom)"
            :width="field.width" :align="field.align" :min-width="field.min_width" :max-width="field.max_width">
            <template #header="scope">
              <div :class="['flex items-center pointer', labelClass, eachLabelClass?.[field.nama_kolom] ?? '' ]"
                @click="changeSort(ind, field.sort)"
                :style="{
                  'justify-content' : field.align,
                  'font-weight': (order[0] == field.nama_kolom ? '900' : ''),
                }">
                <template v-if="field.sortable == '1'">
                  <icons :icon="field.sort == 'asc' ? 'bx:sort-down' :
                    (field.sort == 'desc' ? 'bx:sort-up' : 'bx:sort')"
                    class="mr-1 h-[15px] shrink-0"/>
                </template>
                <div>{{ field.label }}</div>
              </div>
            </template>
            <template #default="scope">
              <div :class="[field.sortable == '1' ? 'ml-[20px]' : 'ml-0', dataClass, eachDataClass?.[field.nama_kolom] ?? '']">
                <div v-if="!field.hide_content">
                  {{ runFunction({
                    func: field.function, 
                    data: isEmpty(field.view_kolom) ? scope.row[field.nama_kolom] : scope.row[field.view_kolom], 
                    options: field.options,
                    defaultData: field?.defaultData,
                  })  }}
                </div>
                <slot :name="field.nama_kolom+'-inside'" :scope="scope" :field="field"></slot>
              </div>
            </template>
          </el-table-column>
          <slot :name="'after-'+field.nama_kolom" :field="field"></slot>
        </template>
        <slot name="default" :handleActionClick="handleActionClick"></slot>
        <el-table-column v-if="showDropdown"
          width="100px" align="center">
          <template #default="scope">
            <el-dropdown trigger="click" @command="handleActionClick" class="max-sm:[&_*]:text-[11px] ">
              <el-button type="primary" size="small" class=""> 
                Aksi <icons icon="fe:arrow-down" class="el-icon--right text-[11px] m-0 ml-1" />
              </el-button>
              <template #dropdown>
                <el-dropdown-menu slot="dropdown" class="max-sm:[&_*]:text-[12px]">
                  <el-dropdown-item class="py-[2px]"
                    v-if="dropdownItemProps?.view?.show ?? true"
                    :command="{action: 'view', id: scope.row.id}">
                    <icons icon="lsicon:view-filled"/> Lihat Detail</el-dropdown-item>
                  <el-dropdown-item class="py-[2px]"
                    v-if="dropdownItemProps?.edit?.show ?? true"
                    :command="{action: 'edit', id: scope.row.id}">
                    <icons icon="material-symbols:edit-outline"/> Ubah</el-dropdown-item>
                  <el-dropdown-item class="py-[2px]"
                    v-if="dropdownItemProps?.delete?.show ?? true"
                    :command="{action: 'delete', id: scope.row.id}">
                    <icons icon="material-symbols:delete-outline"/> Hapus</el-dropdown-item>
                </el-dropdown-menu>
              </template>
            </el-dropdown>
            <div>
              <slot name="button-addition" :scope="scope" :field="field"></slot>
            </div>
          </template>
        </el-table-column>
      </el-table>
      <div v-if="!isEmpty(order) && showOrderText" class="text-[12px] mt-4 text-left">
        Diurutkan berdasarkan : <br/>
        <span class="font-semibold">{{ orderText }}</span>
      </div>
      <el-row type="flex" justify="space-between" class="mt-3 gap-y-2" v-if="paging.dataTotal > paging.perPage">
        <el-pagination
          background
          :pager-count="3"
          size="small"
          layout="prev, pager, next, total"
          :total="paging.dataTotal"
          v-model:page-size="paging.perPage"
          v-model:current-page="paging.currentPage">
        </el-pagination>
        <el-select v-model="paging.perPage" placeholder="Select"
          class="w-[60px]"
          size="small">
          <el-option
            v-for="(item,key) in page"
            :key="key"
            :label="item"
            :value="item">
          </el-option>
        </el-select>
      </el-row>
    </el-card>

    <el-dialog 
      :title="(dataType == 'create' ? 'Tambah' : 'Edit') + ' ' + title" 
      v-model="showAdd"
      append-to-body
      class="w-auto sm:max-w-[50%] mx-10 sm:mx-auto
        px-6"
      :close-on-click-modal="false">
      <form-comp ref="form"
        :fields="fields" 
        :key="'from'+keyCreate"
        :id="editId"
        label-position="top"
        class="mt-6"
        v-model:form-value="valueForm"
        :label-position="labelPosition"
        :label-width="labelWidth"
        :pass-columns="passColumnsInput"
        :show-columns="showColumnsInput"
        :href="hrefStore ?? href + '/store'"
        :href-get="hrefGet ?? href + '/get'"
        :addValues="addValues"
        @changedValue="changedValue"
        @saved="onUpdated"  
        @error="saving=false"
        :show-submit="false"
        >
      </form-comp>
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="showAdd = false">Batal</el-button>
          <el-button 
            type="success" 
            @click="submitForm()"
            :disabled="saving">Simpan</el-button>
        </span>
      </template>
    </el-dialog>
    <data-view ref="dataView" v-model:show="showView" :key="keyCreate"
      v-model:form-value="valueForm"
      :title="title" :href-get="hrefGet ?? href + '/get'"
      :fields="fields"
      :pass-columns="passColumnsInput"
      :show-columns="showColumnsInput"
      :label-position="labelPosition"
      :label-width="labelWidth"
      :data-id="editId" :type="dataType"
      :params="{
        id:editId
      }"
      @changedValue="changedValue"
      @saved="onUpdated"></data-view>
      
    <excel-dialog v-model:show="showUploadDialog" :href="href + '/store_many'"      @saved="onUpdated" :fields="fields" :datas="datas" :options="options"
      :default-value="defaultValue"/>

    <upload-dialog v-model:show="showUploadDialogNormal" :href="href + '/upload'"  @saved="onUpdated"/>
  </div>
</template>
  
<script>
  import _ from 'lodash';
  import DataCreate from './DataCreate.vue'
  import DataView from './DataView.vue'
  import ExcelDialog from './ExcelDialog.vue'
  import UploadDialog from './UploadDialog.vue'
  import { dropdownItemProps } from 'element-plus';

  export default {
    name: "table-data",
    setup(){
      return {
        isEmpty,
        runFunction,
      }
    },
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
      showDownload:{
        type:Boolean,
        default: false,
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
      showColumns:{
        type:Array,
        default: [],
      },
      passColumnsInput:{
        type:Array,
        default: [],
      },
      showColumnsInput:{
        type:Array,
        default: [],
      },
      href:{
        type:String,
        default: '',
      },
      hrefStore:{
        type:String,
        default: null,
      },
      hrefGet:{
        type:String,
        default: null,
      },
      params:{
        type:Object,
        default: {},
      },
      checked:{
        type:Boolean,
        default: true,
      },
      showUpload:{
        type:Boolean,
        default: true,
      },
      showUploadNormal:{
        type:Boolean,
        default: false,
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
      },
      defaultValue:{
        type: [Array, Object],
        default: []
      },
      formValue: {
        type:[Array, Object],
        default:[],
      },
      dropdownItemProps: {
        type: Object,
        default: {}
      },
      labelWidth:{type:[String, Number], default: '150px',},
      addValues: {type:[Object, Array], default: {}},
      labelClass:{type:String, default:''},
      dataClass:{type:String, default:''},
      eachLabelClass:{type:[Object, Array], default:() => {}},
      eachDataClass:{type:[Object, Array], default:() => {}},
    },
    emits:['update:checkedId','resetField','update:formValue','changedFormValue','updateData', 'onFormSaved'],
    components: {
      DataCreate,
      DataView,
      ExcelDialog,
      UploadDialog,
    },
    data: function() {
      return {
        keyCreate:1,
        page: [10,20,30,40,50],
        showAdd: false,
        showView: false,
        showUploadDialog: false,
        showUploadDialogNormal: false,
        editId: '',
        loading: false,
        datas: [],
        datasFilter: [],
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
        valueForm: {},
        options:[],
      };
    },
    watch: {
      datas(val){
        this.$emit('updateData', val)
        this.datasFilter = this.filterData(); 
      },
      datasFilter: function(val) {
        this.paging.dataTotal = val.length;
        let checked = val.filter(d => d.checked).length
        this.checkAll = checked == val.length
        this.paging.currentPage = 1
      },
      'paging.currentPage': function(val) {
        this.paging.offset = val * this.paging.perPage - this.paging.perPage;
      },
      href(val){
        // console.log(val)
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
      formValue: {
        immediate: true,
        deep: true,
        handler(val) {
          this.valueForm = val;
        }
      },
      valueForm: {
        deep: true,
        handler(val) {
          this.$emit('update:formValue', val);
        }
      },
      searchKeyword(val){
        clearTimeout(this.timeout);
        this.timeout = setTimeout(() => {
          // Jalankan logika filter di sini
          this.datasFilter = this.filterData();
        }, 500);
      },
      // fields:{
      //   deep: true,
      //   handler(val){
      //     this.keyCreate += 1
      //   }
      // }
    },
    computed: {
      isIndeterminate(){
        let count = this.datasFilter.filter(d => d.checked).length
        // console.log(count, this.datasFilter.length)
        if (count == 0 || count == this.datasFilter.length)
          return false
        return true
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
      },
      labelPosition(){
        // console.log(this.$windowWidth)
        return this.$windowWidth > 700 ? 'left' : 'top'
      }
    },
    methods: {
      filterData: function() {
        var q = this.searchKeyword?.toLowerCase();
        // console.log('computed', this.datas)
        if (q.length > 0) {
          let fields = this.fields
          let keys = Object.keys(fields)
          let vm = this
          return vm.datas?.filter(data => {
              let exist = false
              if (isEmpty(vm.searchField)) {
                for (var i = 0; i < keys.length; i++) {
                  let ind = keys[i]
                  let field = fields[ind]
                  let text = vm.runFunction({
                    func: field.function, 
                    data: isEmpty(field.view_kolom) ? data[field.nama_kolom] : data[field.view_kolom],
                    options: field.options,
                    defaultData: field?.defaultData,
                  })
                  if (text?.toLowerCase()?.includes(q)) {
                    exist = true
                    break
                  }
                }
              } else {
                let field = fields[vm.searchField]
                let text = vm.runFunction({
                  func: field.function, 
                  data: data[field.nama_kolom],
                  options: field.options,
                  defaultData: field?.defaultData,
                })
                if (text?.toLowerCase()?.includes(q))
                  exist = true
              }
              return exist
            });
        }
        return this.datas;
      },   
      submitForm(){
        this.$refs.form.submitForm(); 
        this.saving=true
      },
      resetDataCreate(){
        // console.log('reset-form-data-create')
        this.$refs.dateCreate.resetForm()
      },
      handleChange(params){
        // console.log(params)
      },
      handleCheckAllChange(val){
        this.datasFilter.forEach(d => {
          d.checked = val
        });
      },
      changedValue(params){
        // console.log('params', params)
        this.$emit('changedFormValue', params);
      },
      changeSort(ind, sort_val){
        let f = this.fields[ind] ?? {nama_kolom:ind}
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
          o.push(ind)
        this.getData()
      },
      getParams(){
        // console.log('getParams', this.params)
        let list = []
        let f = this.fields
        this.order.forEach(o => {
          let field = f[o]
          list.push(o + ' ' + field.sort)
        })
        let order = this.params?.order ?? []
        // console.log(order, [...order, ...list])
        order = [...list, ...order]
        return {
            ...this.params,
            order:order
          }
      },
      async getData(){
        let params = this.getParams()
        console.log(params)
        await this.$http.get(this.href, {
          params: params
        })
          .then(result => {
            this.loading = false;
            var res = result.data;
            // console.log('getData', res)
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
        
        await this.$http.get(this.href + '/options')
          .then(result => {
            this.loading = false;
            var res = result.data;
            this.options = res;
          })
          .catch(err => {

          });
      },
      handleActionClick: function(obj) {
        var action = obj?.action;
        var id = obj?.id;
        
        if (!action) {
          return
        }
        
        if (action == 'add') {
          this.showAdd = true;
          this.dataType = 'create';
          this.editId = -1;
        } else if (action == 'edit') {
          this.editId = id;
          this.showAdd = true;
          this.dataType = 'update';
        } else if (action == 'view') {
          this.editId = id;
          this.showView = true;
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
            // console.log(this.href)
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
                  type:'success',
                  title: 'Berhasil',
                  message: 'Data berhasil dihapus',
                  position: 'bottom-right'
                });
              });
            })
           .catch(err => {
              console.log(err)
            });
        } else if (action == 'delete-many') {
          this.deleteMany()
        } else if (action == 'download-excel') {
          let params = this.getParams()
          // console.log(params, this)
          params = this.toQueryString(params)
          this.openLink(this.$siteUrl + this.href + '/download_excel?' + params)
        }
      },
      onUpdated: function(teacher) {
        this.showAdd = false;
        this.showUploadDialog = false;
        this.saving = false
        this.getData();
        this.$emit('resetField')
        this.$emit('onFormSaved', teacher)
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
  
  
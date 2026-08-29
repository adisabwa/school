
<template>
  <div>
    <teleport to="body">
      <el-dialog  
        v-model="showDialog"
        class="p-7 w-fit min-w-[50vw] my-10"
        :close-on-click-modal="false">
        <template #header>
          <b>Import Data dari Excel</b>
        </template>
        <div class="w-full" v-loading="loading">
          <div class="flex w-full gap-x-2 mb-2 ">
            <el-input @paste="handlePaste" placeholder="Paste Data dari File Excel kemudian tempel disini"
              class="w-full"></el-input>
            <span class="flex flex-row items-center">atau</span>
            <el-upload
              ref="uploadRef"
              class="upload-demo flex"
              :auto-upload="false"
              v-model:file-list="fileList"
              :show-file-list="false"
              :on-change="handleFile"
              >
              <template #trigger>
                <el-button type="success" size="large">Upload File Excel</el-button>
              </template>
              </el-upload>
          </div>
          <!-- Sheet Selector -->
          <div v-if="sheetNames.length" class="mt-4">
            <label>Pilih sheet:</label>
            <el-select v-model="selectedSheet" placeholder="Choose a sheet" @blur="loadSelectedSheet" multiple>
              <el-option
                v-for="name in sheetNames"
                :key="name"
                :label="name"
                :value="name"
              />
            </el-select>
          </div>
          <div>
            <el-checkbox v-model="haveHeader">
              <span class="text-sm">Baris Pertama adalah Header</span>
            </el-checkbox>
            <el-checkbox v-model="addReq">
              <span class="text-sm">Cek Data yang harus diisi</span>
            </el-checkbox>
            <el-checkbox v-model="skipCheck">
              <span class="text-sm">Skip Data di Kolom Acuan</span>
            </el-checkbox>
          </div>
          <div class="flex gap-x-5">
            <el-checkbox v-model="updateData">
              <span class="text-sm">Update Data yang Sama</span>
            </el-checkbox>
            <el-select v-if="updateData" 
              v-model="checkColumn" multiple clearable filterable
              placeholder="Pilih Kolom Acuan"
              @change="checkIds">
              <el-option v-for="data in Object.values(excelFields)"
                :value="data.nama_kolom"
                :label="data.label"/>
            </el-select>
          </div>
          <floating-select ref="updateDataSelect"
            v-model:value="updateId" 
            filterable clearable
            :class="['w-full']" 
            @change="(val) => { 
              this.ids[this.updateKey] = val
              this.updates[this.updateKey] = val > 0
              this.oldDatas[this.updateKey] = this.datas.filter(d => d.id == val)[0]
              // console.log(val)
            }"
            :show-input="false"
            :options="options">
          </floating-select>
          <div v-if="parsedData.length > 0" class="mt-3">
            <div v-if="mustEdit?.filter(d => d)?.length > 0"
              class="p-3 bg-cyan-50 border border-solid border-cyan-700/[0.5]">
              <div class="text-left font-bold italic">Aksi untuk Menyesuaikan Data yang Tidak Sesuai</div>
              <div class="w-full flex mt-2">
                <el-button :disabled="saving" 
                  v-if="!showOption" size="small" type="primary" class="" @click="showOption = true">
                  <icons icon="mdi:edit" class="text-[13px]"/> Pasangkan ke Data Lain
                </el-button>
                <el-button :disabled="saving" 
                  v-if="showOption" size="small" type="success" class="" @click="showOption = false">
                  <icons icon="mdi:check" class="text-[13px]"/> Selesaikan Pemasangan
                </el-button>
                <el-button :disabled="saving" 
                  size="small" type="primary" class="" @click="addNewAll">
                  <icons icon="mdi:plus" class="text-[13px]"/> Tambahkan Sebagai Data Baru
                </el-button>
                <el-button :disabled="saving" 
                  size="small" type="warning" class="" @click="findError">
                  <icons icon="mdi:search" class="text-[13px]"/> Cari Data yang Tidak Sesuai
                </el-button>
              </div>
            </div>
            <h3 class="mb-1 mt-3 text-center">Data yang dimasukkan:</h3>
            <div class="max-w-[90vw] overflow-x-auto max-h-[50vh] overflow-y-auto" >
              <table class="table two-line">
                <thead class="*:bg-cyan-100/[0.7] *:leading-[1.2] sticky top-0 z-[9999]">
                  <tr>
                    <th v-if="updateData"
                      rowspan="2" class="text-center relative" width="10px">
                      <div class="-rotate-90 absolute
                        left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2">Update</div>
                    </th>
                    <th colspan="2" class="text-center" width="50px">
                      Pilih Kolom
                    </th>
                    <th v-for="(col, key) in parsedData[0]"
                      valign="top">
                      <el-select placeholder="Nama Kolom"
                        size="small"
                        clearable filterable
                        class="font-normal"
                        v-model="cols[key]"
                        @change="addDataToForm(key); checkIds()">
                        <el-option v-for="f in excelFields"
                          :value="f.nama_kolom"
                          :label="f.label" />
                      </el-select>
                      <div class="text-center mt-1" >{{ excelFields[cols[key]]?.label ?? 'Kolom belum dipilih' }}</div>
                    </th>
                  </tr>
                  <tr v-if="haveHeader">
                    <th colspan="2" class="text-center" valign="top">Header</th>
                    <th v-for="(col, key) in headerData"
                      class="text-center font-normal"
                      valign="top">
                      {{ col }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <template v-for="(row, row_num) in parsedDataTransformed">
                    <tr class="[&:has(.not-found)]:bg-red-50
                    [&:has(.not-found)_+_tr]:bg-red-50">
                      <td v-if="updateData ">
                        <el-checkbox v-if="ids[(row_num)] > 0"
                          v-model="updates[(row_num)]">
                          {{ ids[(row_num)] }}
                        </el-checkbox>
                      </td>
                      <td align="center" class="px-1 ">
                        <icons icon="mdi-delete" 
                          class="text-md text-center text-red-700 m-0 cursor-pointer"
                            @click="
                                ids.splice((row_num), 1);
                                oldDatas.splice((row_num), 1);
                                parsedData.splice((row_num), 1);
                                forms.splice((row_num), 1);
                                errors.splice((row_num), 1)"/>
                        <icons icon="mdi-edit"
                          class="text-md text-center text-cyan-700 m-0 cursor-pointer"
                            @click="$refs.updateDataSelect.openSelect();
                              updateId = ids[(row_num)] ?? '';
                              updateKey = (row_num)"/>
                      </td>
                      <td align="center">{{ parseInt(row_num) + 1}}</td>
                      <td v-for="(col, col_num) in row"
                        :class="['min-w-[150px]',
                          cols[col_num] ?
                          forms[(row_num)][col_num] === false ? 'not-found bg-red-200' : ''
                          : 'bg-gray-100 text-gray-400']">
                        <template v-if="cols[col_num]">
                          <div class="text-sm" v-if="forms[(row_num)][col_num] !== false">
                            {{ runFunction({
                              data:forms[(row_num)][col_num], 
                              options:excelFields[cols[col_num]].options
                            }) }}
                          </div>
                          <div v-else class="text-red-900">
                            <div class="flex justify-between"> 
                              <div>{{ col }}</div>
                              <icons icon="jam:plus-circle" class="text-cyan-600 cursor-pointer" @click="addNew(row_num, col_num)" />
                            </div>
                            <div v-if="showOption">
                              <el-select v-model="forms[(row_num)][col_num]"
                                filterable clearable
                                @change="(value) => {
                                  changeSimilar((row_num), col_num, col, value)
                                }"
                                :placeholder="`Pilih ${excelFields[cols[col_num]].label} yang sesuai` "
                                :title="`Pilih ${excelFields[cols[col_num]].label} yang sesuai` "
                                size="small"
                                >
                                <el-option v-for="opt in excelFields[cols[col_num]].options"
                                  :label="opt.label"
                                  :value="opt.value" />
                              </el-select>
                            </div>
                            <div class="text-[12px]" v-else>
                              Tidak ada data yang sesuai dalam daftar <b>{{ excelFields[cols[col_num]].label }}</b>
                            </div>
                          </div>
                        </template>
                        <div v-else>{{ col }}</div>
                        <!-- {{ oldDatas[row_num] }} -->
                        <div v-if="oldDatas[(row_num)]"
                          class="text-[12px] text-slate-500">
                          <template v-if="oldDatas[(row_num)][cols[col_num]]">
                            <span>Data Lama : </span> {{ oldDatas[(row_num)][cols[col_num]] }}
                          </template>
                        </div>
                      </td>
                    </tr>
                    <tr v-show="errors[(row_num)]">
                      <td></td>
                      <td></td>
                      <td></td>
                      <td :colspan="parsedData[0].length">
                        <div v-if="errors[(row_num)]"
                          class="text-left text-[12px] text-red-600">
                          Error Data {{parseInt(row_num) + 1}} : {{ errors[(row_num)] }}
                        </div>
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>
            <el-row type="flex" justify="space-between" class="mt-2" v-if="paging.dataTotal > paging.perPage">
              <el-select v-model="paging.perPage" placeholder="Select" style="width:70px;">
                <el-option
                  v-for="(item,key) in page"
                  :key="key"
                  :label="item"
                  :value="item">
                </el-option>
              </el-select>
              <el-pagination
                class="mt-2"
                background
                layout="total, prev, pager, next"
                :total="paging.dataTotal"
                v-model:page-size="paging.perPage"
                v-model:current-page="paging.currentPage">
              </el-pagination>
            </el-row>
          </div>
        </div>
        <template #footer>
          <el-button @click="showDialog = false">Batal</el-button>
          <el-button 
            type="success" 
            @click="submitUpload()" :icon="saving ? 'el-icon-loading' : ''" 
            :disabled="saving">Simpan</el-button>
        </template>
      </el-dialog>
    </teleport>
  </div>
</template> 

<script>
import { update } from 'lodash';
import * as XLSX from "xlsx";

export default {
  name: 'excel-dialog',
  props: {
    show: {
      type: Boolean,
      default: false,
    },
    showNotification: {
      type: Boolean,
      default: true,
    },
    title: {
      type:String,
      default:'Import Data',
    },
    href: {
      type:String,
      default:'data/peminatan',
    },
    params: {
      type:String,
      default:'',
    },
    fields: {
      type: [Array, Object],
      default: []
    },
    defaultValue:{
      type: [Array, Object],
      default: []
    },
    datas:{
      type: [Array, Object],
      default: []
    },
    options:{
      type: [Array, Object],
      default: []
    }
  },
  emits:['update:show'],
  data: function() {
    return {
      showDialog: true,
      saving:false,
      loading: false,
      parsedData: [],
      headerData: [],
      cols:[],
      reqCols:[],
      forms:[],
      errors:[],
      mustEdit:[],
      showOption:false,
      haveHeader: true,
      updateData: true,
      addReq: true,
      skipCheck: false,
      checkColumn:[],
      oldDatas:[],
      ids:[],
      updates:[],
      fileList : [],
      workbook: null,
      sheetNames: [],
      selectedSheet: [],
      updateId:null,
      updateKey:null,
      page: [10,20,30,40,50],
      paging: {
        offset: 0,
        perPage: 10, // [10,20,30,40,50,100]
        currentPage: 1,
        dataTotal: 0,
      },
    }
  },
  watch: {
    show: {
      immediate: true,
      async handler(val) {
        this.showDialog = val;
      }
    },
    showDialog: function(val, oldVal) {
      this.$emit('update:show', val);
      this.parsedData = []
      this.forms = []
      this.errors = []
      this.cols = []
      this.ids = []
      this.updates = []
      this.oldDatas = []
      // console.log(this.excelFields)
      this.reqCols = Object.values(this.excelFields).filter(f => f.required == '1').map(f => f.nama_kolom)
      // console.log('show',val)
    },
    haveHeader: {
      handler(val) {
        this.createForm()
        if (!this.parsedData || this.parsedData.length == 0) {
          return
        }
        // console.log('haveHeader', val, this.headerData)
        if (val) {
          this.parsedData?.shift()
          this.forms?.shift()
          this.errors?.shift()
          this.setAutoCols()
        } else {
          this.parsedData?.unshift(this.headerData)
          this.forms?.unshift(this.headerData.map(() => ''))
          this.errors?.unshift('')
        }
        // console.log(this.parsedData)
      }
    },
    'paging.currentPage': function(val) {
      this.paging.offset = val * this.paging.perPage - this.paging.perPage;
    },
    parsedData(val){
      this.paging.dataTotal = val.length
    }
    
  },
  computed: {
    parsedDataTransformed(){
      let data = {}
      this.parsedData.slice(this.paging.offset, this.paging.offset + this.paging.perPage).forEach((d, key) => {
        let newKey = key + this.paging.offset
        data[newKey] = d
      })
      // console.log(data)
      return data
    },
    excelFields(){
      let fields = {}
      let tmp = JSON.parse(JSON.stringify(this.fields))
      let keys = Object.keys(tmp)
      for (let i = 0; i < keys.length; i++) {
        const key = keys[i];
        let field = tmp[key]
        if (field?.options) {
          let opt = Object.values(field?.options)
          field.options = opt.flatMap(item => {
            if (Array.isArray(item.options)) {
              return item.options.map(option => ({
                value: option.value,
                label: item.label + ' - ' + option.label,
                match: option.match ?? null,
              }))
            } else {
              return [item]; // No options? Skip this item.
            }
          })
          field.options.sort()
        }
        // console.log(field)
        fields[key] = field
      }
      // console.log('excel', fields)
      return fields
    }
  },
  methods: {
    handleFile(file, fileList) {
      console.log('handleFile', file, fileList)
      if (!file) return;

      const reader = new FileReader();
      reader.onload = (e) => {
        const data = new Uint8Array(e.target.result);
        this.workbook = XLSX.read(data, { type: "array" });
        this.sheetNames = this.workbook.SheetNames;
        this.selectedSheet = [];
      };
      reader.readAsArrayBuffer(file.raw);
    },
    loadSelectedSheet() {
      if (!this.selectedSheet || !this.workbook) return;
      let datas = []
      this.selectedSheet.forEach((sheet, key) => {
        let worksheet = this.workbook.Sheets[sheet];
        const rows = XLSX.utils.sheet_to_json(worksheet, { 
          header: 1,
          blankrows: true,  // keep empty rows
          defval: ""        // fill empty cells with ""
        });

        const trimmed = [];
        for (let row of rows) {
          const isEmpty = row.every(cell => cell === "" || cell === null);
          if (isEmpty) break; // stop at first empty row
          trimmed.push(row);
        }
        if (this.haveHeader && key != 0) {
          trimmed.shift(); // remove header row if haveHeader is true
        }
        datas = [...datas, ...trimmed];
      })
      this.parsedData = datas;
      this.generateInitial();
    },
    async handlePaste(event) {
      // this.loading = true;
      // console.log(event)
      const pastedData = await event.clipboardData.getData('Text');
      // Process the pastedData (e.g., split by lines and tabs/commas)
      this.parsedData = await this.parseExcelData(pastedData);
      // console.log(this.parsedData)
      this.generateInitial()
      // this.loading = false;
      event.preventDefault(); // Prevent default paste behavior if needed
    },
    generateInitial(){
      this.createForm();
      this.headerData = this.parsedData[0]
      if (this.haveHeader) {
        this.parsedData?.shift()
        this.forms?.shift()
        this.errors?.shift()
        this.setAutoCols()
      }
    },
    async createForm(){
      for (let i = 0; i < this.parsedData.length; i++) {
        const element = this.parsedData[i];
        this.forms[i] = []
        this.errors[i] = ''
        for (let j = 0; j < element.length; j++) {
          this.forms[i][j] = ''
        }
        
      }
      // console.log(this.forms)
      this.cols = this.parsedData[0]?.map(a => null) 
    },
    parseExcelData(data) {
      // Example: Simple parsing for tab-separated values
      // console.log(data)
      const rows = data.split('\n');
      rows.pop()
      const parsedRows = rows.map(row => row.replace('\r','').split('\t'));
      return parsedRows;
    },
    setAutoCols() {
      // this.createForm()
      // console.log('setAutoCols', this.headerData)
      let optionsFields = []
      Object.values(this.excelFields).forEach(f => {
        optionsFields.push({
          value:f.nama_kolom,
          label:f.label
        })
      })
      this.headerData.forEach((col, index) => {
        let findKolom = this.getValueFromOption(col, optionsFields, 0.8);
        // console.log('findKolom', findKolom, col)
        if (findKolom) {
          this.cols[index] = findKolom
          this.addDataToForm(index, findKolom);
        } else {
          this.cols[index] =  null; // or some default value
        }
      });
    },
    addDataToForm(key, kolom = null) {
      // console.log('addDataToForm', key, kolom, this.cols[key])
      let field = this.excelFields[kolom ?? this.cols[key]]
      this.mustEdit[key] = false
      // console.log(this.cols[key], field.options)
      for (let i = 0; i < this.parsedData.length; i++) {
        // console.log('addDataToForm', key, kolom, this.cols[key])
        let val = this.parsedData[i][key]
        let value = ''
        this.errors[i] = ''
        if (val) {
          value = field?.options?.length > 0 ? 
            this.getValueFromOption(val, field.options, field.similar_criteria ?? 0.85) :
            val
          // console.log('opt', field, field?.options?.length, val, value)
          if (!value)
            this.mustEdit[key] = true
        }
        if (field?.function_input) {
          value = this.runFunction({
            func: field?.function_input,
            data: value,
          })
        }
        this.forms[i][key] = value
      }
      this.findError()
    },
    findError(){
      let errInd =  this.forms.findIndex((d, row_num) => {
        let err = d.filter(el => el === false)
        // console.log(err)
        return err.length > 0
      })
      if (errInd < 0) return
      this.paging.currentPage = Math.floor(parseFloat(errInd) / parseFloat(this.paging.perPage)) + 1
      // console.log('errind', errInd)
    },
    checkIds(){
      let keys = []
      // if (this.checkColumn.length <= 0) return
      this.checkColumn.forEach(c => {
        let ind = this.cols.findIndex(d => d == c)
        keys.push(ind)
      })
      let setId = []
      let setData = []
      if (this.checkColumn.length > 0)
        this.datas.forEach(d => {
          let ind = []
          this.checkColumn.forEach(c => {
            let text = d[c]
            if (typeof text == 'string')
              text = text.toLowerCase().trim()
            ind.push(text)
          })
          ind = ind.join('-')
          setId[ind] = d.id
          setData[ind] = d
        })
      console.log(setId, keys)
      this.forms.forEach((row, row_num) => {
        let ind = []
        keys.forEach(k => {
          let text = row[k] ?? ''
          if (typeof text == 'string')
            text = text.toLowerCase().trim()
          ind.push(text)
        })
        // ind = ind.join('-')
        // Cari ID dengan mencari data dengan index yang paling mirip
        console.log('ind', ind)
        let id = -1
        let data = {}
        let _sim = -1
        Object.keys(setId).forEach(k => {
          // console.log('k', k, ind)
          let keys = k.split('-')
          let simTotal = 0
          keys.forEach((key, i) => {
            simTotal += parseFloat(this.isSimilar(key, ind[i] ?? ''))
            // console.log('sim', key, ind[i] ?? '', this.isSimilar(key, ind[i] ?? ''))
          })
          // console.log(_sim, simTotal)
          if (simTotal > _sim) {
            _sim = simTotal
            if (_sim > 0.7) {
              id = setId[k] 
              data = setData[k]
            }
          }
        } )
        this.ids[row_num] = id
        this.updates[row_num] = this.ids[row_num] > 0
        // console.log(this.ids[row_num], this.ids[row_num] > 0, this.updates[row_num])
        this.oldDatas[row_num] = this.updates[row_num] ? data : false
      })
      // console.log(this.ids, this.updates)
      // this.forms.forEach((row, row_num) => {
      //   let id = -1
      //   for (let ind_data = 0; ind_data < this.datas.length; ind_data++) {
      //     console.log('ind',ind_data)
      //     const d = this.datas[ind_data];
      //     let same = true
      //     this.checkColumn.forEach((c, i_c) => {
      //       console.log(c, row[keys[i_c]], d[c], row[keys[i_c]] == d[c])
      //       if (row[keys[i_c]] != d[c]) 
      //         same = false
      //     })
      //     if (same) {
      //       id = d.id
      //       break
      //     }
      //   }
      //   this.ids[row_num] = id
      //   this.updates[row_num] = id > 0
      //  })
    },
    changeSimilar(row_num_select, col_num, col, newValue) {
      // console.log('changeSimilar', col_num, col, value)
      if (this.isEmpty(newValue)) {
        return
      }
      this.parsedData.forEach((row, row_num) => {
        if (row[col_num] == col) {
          this.forms[row_num][col_num] = newValue
        }
      })
    },
    submitUpload(){
      // Simpan data
      this.saving = true;
      this.resetObjectValue(this.errors)
      let vm = this
      let cols = this.cols
      // let usedCols = this.cols.filter(d => !this.isEmpty(d))
      let addCols = this.reqCols.filter(element => !cols.includes(element));
      // console.log(this.reqCols, cols, addCols)
      // console.log(this.forms)
      let form = this.forms.map((el, key) => {
        let formData = Object.fromEntries(
          el.map((val, col) => {
            return [cols[col], val]
          })
          .filter(([key, value]) => {
            if (this.isEmpty(key)) return false
            if (this.skipCheck) {
              if (this.checkColumn.includes(key)) {
                return false
              } else {
                return true
              }
            } else {
              return true
            }
          })
        )
        console.log('formData', formData)
        if (this.addReq)
          addCols.forEach(col => {
            formData[col] = ''
          })
        if (this.updates[key]) {
          formData.id = this.ids[key]
        } else {
          formData.id = -1
        }
        this.defaultValue.forEach(d => {
          formData[d.key] = d.value
        })
        return formData
      })
      
      let backUpForm = JSON.parse(JSON.stringify(vm.forms))
      // Object.keys(vm.original).forEach(ind => {
      //   if (vm.original[ind]) 
      //     delete form[ind]
      // });
      // form.id = this.dataId
      console.log(form)
      form = this.convertNullToEmptyString(form)
      form = {
        json: JSON.stringify(form),
      }
      var formData = window.jsonToFormData(form); 
      
      // this.saving = false;
      // return false;
      this.$http.post(this.href, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      } )
        .then(result => {
          this.saving = false;
          var psb = result.data;
          this.dataId = psb.id
          this.$emit('saved', psb);
        })
        .catch(err => {
          this.saving = false;
          console.log(err)
          var res = err.response;
          var code = res.status;
          // this.$emit('error', false);
          
          if (code == '400') {
            // Populating error message
            res.data.messages.forEach((m, key )=> {
              let e =  [...new Set(Object.values(m))];
              e = e.join(', ')
              this.errors[key] = e
            })
            // console.log(res.data.messages, this.errors)
            if (this.showNotification)
              this.$notify.error({
                title: 'Gagal',
                message: 'Data belum benar',
                position: 'bottom-right'
              });
          } else {
            if (this.showNotification)
              this.$notify.error({
                title: 'Gagal',
                message: this.errorSubmitText,
                position: 'bottom-right'
              });
          }
          this.form = backUpForm;
        });
    },
    addNewAll(){
      // Tambahkan data baru untuk semua baris yang belum sesuai
      let promises = []
      this.saving = true
      for (let i = 0; i < this.parsedData.length; i++) {
        const element = this.parsedData[i];
        for (let j = 0; j < element.length; j++) {
          // console.log(this.cols[j], !this.cols[j])
          if (!this.cols[j]) continue
          let formData = this.forms[i][j]
          // console.log(formData)
          if (formData) continue
          let field = this.excelFields[this.cols[j]]
          console.log(field?.allow_add)
          if (field?.allow_add != '1') continue 
          let data = this.parsedData[i][j]
          let href = field.add_href
          let form = {}
          form.id = -1
          form[field.add_col] = data
          form = window.jsonToFormData(form)
          promises.push(this.$http.post(href, form)
            .then(result => {
              var psb = result.data;
              let id = psb.id
              this.forms[i][j] = id
            }))
        }
      }
      Promise.all(promises)
      .then(() => {
        this.saving = false
        this.$notify.success({
          title: 'Berhasil',
          message: 'Data berhasil ditambahkan',
          position: 'bottom-right'
        });
        for (let i = 0; i < this.cols.length; i++) {
          const element = this.cols[i];
          let field = this.excelFields[element]
          if (field?.allow_add == '1'){
            let link = field.add_reset
            this.$http.get(link)
              .then(res => {
                let data = res.data
                field.options = data
              })
          }
        }
      })
      .catch(error => {
        console.error('One of the requests failed:', error);
      });
    },
    addNew(row_num, col_num){
      // Tambahkan data baru untuk baris dan kolom tertentu
      if (!this.cols[col_num]) return
      let formData = this.forms[row_num][col_num]
      // console.log(formData)
      if (formData) return
      let field = this.excelFields[this.cols[col_num]]
      console.log(field?.allow_add)
      if (field?.allow_add != '1') return 
      let data = this.parsedData[row_num][col_num]
      let href = field.add_href
      let form = {}
      form.id = -1
      form[field.add_col] = data
      form = window.jsonToFormData(form)
      this.$http.post(href, form)
        .then(result => {
          var psb = result.data;
          let id = psb.id
          this.forms[row_num][col_num] = id
          this.changeSimilar((row_num), col_num, data, id)
          if (field?.allow_add == '1'){
            let link = field.add_reset
            this.$http.get(link)
              .then(res => {
                let data = res.data
                this.excelFields[this.cols[col_num]].options = data
              })
          }
      })
      .catch(error => {
        console.error('One of the requests failed:', error);
      });
    }
  }
}
</script>
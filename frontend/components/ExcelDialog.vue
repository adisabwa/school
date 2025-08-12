
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
          </div>
          <div class="flex gap-x-5">
            <el-checkbox v-model="updateData">
              <span class="text-sm">Update Data yang Sama</span>
            </el-checkbox>
            <el-select v-if="updateData" 
              v-model="checkColumn" multiple clearable filterable
              placeholder="Pilih Kolom Acuan"
              @change="checkIds">
              <el-option v-for="data in Object.values(fields)"
                :value="data.nama_kolom"
                :label="data.label"/>
            </el-select>
          </div>
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
              </div>
            </div>
            <h3 class="mb-1 mt-3 text-center">Data yang dimasukkan:</h3>
            <div class="max-w-[90vw] overflow-x-auto max-h-[55vh] overflow-y-auto" >
              <table class="table two-line">
                <thead class="*:bg-cyan-100/[0.7] *:leading-[1.2]">
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
                        <el-option v-for="f in fields"
                          :value="f.nama_kolom"
                          :label="f.label" />
                      </el-select>
                      <div class="text-center mt-1" >{{ fields[cols[key]]?.label ?? 'Kolom belum dipilih' }}</div>
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
                  <template v-for="(row, row_num) in parsedData">
                    <tr class="[&:has(.not-found)]:bg-red-50
                    [&:has(.not-found)_+_tr]:bg-red-50">
                      <td v-if="updateData ">
                        <el-checkbox v-if="ids[row_num] > 0"
                          v-model="updates[row_num]">
                          {{ ids[row_num] }}
                        </el-checkbox>
                      </td>
                      <td align="center" class="px-1 ">
                        <icons icon="mdi-delete" 
                          class="text-md text-center text-red-700 m-0 cursor-pointer"
                            @click="parsedData.splice(row_num, 1)"/>
                      </td>
                      <td align="center">{{ row_num + 1}}</td>
                      <td v-for="(col, col_num) in row"
                        :class="['min-w-[150px]',
                          cols[col_num] ?
                          forms[row_num][col_num] === false ? 'not-found bg-red-200' : ''
                          : 'bg-gray-100 text-gray-400']">
                        <template v-if="cols[col_num]">
                          <div class="text-sm" v-if="forms[row_num][col_num] !== false">
                            {{ runFunction({
                              data:forms[row_num][col_num], 
                              options:fields[cols[col_num]].options
                            }) }}
                          </div>
                          <div v-else class="text-red-900">
                            <div>{{ col }}</div>
                            <div v-if="showOption">
                              <el-select v-model="forms[row_num][col_num]"
                                @change="(value) => {
                                  changeSimilar(col_num, col, value)
                                }"
                                :placeholder="`Pilih ${fields[cols[col_num]].label} yang sesuai` "
                                :title="`Pilih ${fields[cols[col_num]].label} yang sesuai` "
                                size="small"
                                >
                                <el-option v-for="opt in fields[cols[col_num]].options"
                                  :label="opt.label"
                                  :value="opt.value" />
                              </el-select>
                            </div>
                            <div class="text-[12px]" v-else>
                              Tidak ada data yang sesuai dalam daftar <b>{{ fields[cols[col_num]].label }}</b>
                            </div>
                          </div>
                        </template>
                        <div v-else>{{ col }}</div>
                      </td>
                    </tr>
                    <tr v-show="errors[row_num]">
                      <td></td>
                      <td></td>
                      <td></td>
                      <td :colspan="parsedData[0].length">
                        <div v-if="errors[row_num]"
                          class="text-left text-[12px] text-red-600">
                          Error Data {{row_num + 1}} : {{ errors[row_num] }}
                        </div>
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>
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
      checkColumn:[],
      ids:[],
      updates:[],
      fileList : [],
      workbook: null,
      sheetNames: [],
      selectedSheet: [],
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
      this.cols = []
      this.reqCols = Object.values(this.fields).filter(f => f.required == '1').map(f => f.nama_kolom)
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
  },
  computed: {
    
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
      const rows = data.split('\n');
      rows.pop()
      const parsedRows = rows.map(row => row.split('\t'));
      return parsedRows;
    },
    setAutoCols() {
      // this.createForm()
      console.log('setAutoCols', this.headerData)
      let optionsFields = []
      Object.values(this.fields).forEach(f => {
        optionsFields.push({
          value:f.nama_kolom,
          label:f.label
        })
      })
      this.headerData.forEach((col, index) => {
        let findKolom = this.getValueFromOption(col, optionsFields, 0.8);
        console.log('findKolom', findKolom, col)
        if (findKolom) {
          this.cols[index] = findKolom
          this.addDataToForm(index, findKolom);
        } else {
          this.cols[index] =  null; // or some default value
        }
      });
    },
    addDataToForm(key, kolom = null) {
      let field = this.fields[kolom ?? this.cols[key]]
      this.mustEdit[key] = false
      // console.log(this.cols[key], field.options)
      for (let i = 0; i < this.parsedData.length; i++) {
        let val = this.parsedData[i][key]
        let value = ''
        this.errors[i] = ''
        if (val) {
          value = field?.options?.length > 0 ? 
            this.getValueFromOption(val, field.options) :
            val
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
    },
    checkIds(){
      let keys = []
      this.checkColumn.forEach(c => {
        let ind = this.cols.findIndex(d => d == c)
        keys.push(ind)
      })
      console.log(keys)
      let setId = []
      this.datas.forEach(d => {
        let ind = []
        this.checkColumn.forEach(c => {
          ind.push(d[c])
        })
        ind = ind.join('-')
        setId[ind] = d.id
      })
      console.log(setId)
      this.forms.forEach((row, row_num) => {
        let ind = []
        keys.forEach(k => {
          ind.push(row[k] ?? '')
        })
        ind = ind.join('-')
        this.ids[row_num] = parseInt(setId[ind]) ?? -1
        console.log(this.ids[row_num], this.ids[row_num] > 0)
        this.updates[row_num] = this.ids[row_num] > 0
      })
      console.log(this.ids, this.updates)
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
    changeSimilar(col_num, col, value) {
      // console.log('changeSimilar', row_num, col, value)
      if (this.isEmpty(value)) {
        return
      }
      this.parsedData.forEach((row, row_num) => {
        let value = row[col_num]
        if (value == col) {
          this.forms[row_num][col_num] = value
        }
      })
    },
    submitUpload(){
      this.saving = true;
      this.resetObjectValue(this.errors)
      let vm = this
      let cols = this.cols
      // let usedCols = this.cols.filter(d => !this.isEmpty(d))
      let addCols = this.reqCols.filter(element => !cols.includes(element));
      console.log(this.reqCols, cols, addCols)
      let form = this.forms.map((el, key) => {
        let formData = Object.fromEntries(
          el.map((val, col) => {
            return [cols[col], val]
          })
          .filter(([key, value]) => !this.isEmpty(key))
        )
        console.log('formData', formData)
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
          let field = this.fields[this.cols[j]]
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
          let field = this.fields[element]
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
    }

  }
}
</script>
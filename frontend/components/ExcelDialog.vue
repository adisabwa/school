
<template>
  <div>
    <teleport to="body">
      <el-dialog  
        v-model="showDialog"
        class="p-7 w-fit min-w-[50vw]"
        :close-on-click-modal="false">
        <template #header>
          <b>Import Data dari Excel</b>
        </template>
        <div class="text-center w-full">
          <el-input @paste="handlePaste" placeholder="Copy Data dari File Excel kemudian tempel disini"
            class="w-full mb-5"></el-input>
          <div v-if="parsedData">
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
            <h3 class="mb-1 mt-3">Data yang dimasukkan:</h3>
            <table class="table two-line">
              <thead>
                <tr>
                  <th align="center" width="5px"></th>
                  <th align="center" width="10px">No.</th>
                  <th v-for="(col, key) in parsedData[0]"
                    valign="top">
                    <el-select placeholder="Nama Kolom"
                      size="small"
                      clearable
                      class="font-normal"
                      v-model="cols[key]"
                      @change="addDataToForm($event, key)">
                      <el-option v-for="f in fields"
                        :value="f.nama_kolom"
                        :label="f.label" />
                    </el-select>
                    <div class="mt-2 text-center" >{{ fields[cols[key]]?.label ?? 'Kolom belum dipilih' }}</div>
                    <div class="mt-1 font-normal text-[13px] text-center" v-if="mustEdit[key] && fields[cols[key]].allow_add == '0'">
                      <div >Silahkan masuk ke halaman {{ fields[cols[key]]?.label }}</div>
                    </div>
                  </th>
                </tr>
              </thead>
              <tbody>
                <template v-for="(row, row_num) in parsedData">
                  <tr >
                    <td align="center" class="px-1 ">
                      <icons icon="mdi-delete" 
                        class="text-md text-center text-red-700 m-0 cursor-pointer"
                          @click="parsedData.splice(row_num, 1)"/>
                    </td>
                    <td align="center">{{ row_num + 1}}</td>
                    <td v-for="(col, col_num) in row"
                      :class="['min-w-[150px]',
                        cols[col_num] ?
                        !forms[row_num][col_num] ? 'bg-red-200' : ''
                        : 'bg-gray-100 text-gray-400']">
                      <template v-if="cols[col_num]">
                        <div class="text-sm" v-if="forms[row_num][col_num]">
                          {{ runFunction(null, forms[row_num][col_num], fields[cols[col_num]].options) }}
                        </div>
                        <div v-else class="text-red-900">
                          <div>{{ col }}</div>
                          <div v-if="showOption">
                            <el-select v-model="forms[row_num][col_num]"
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
    }
  },
  emits:['update:show'],
  data: function() {
    return {
      showDialog: true,
      saving:false,
      parsedData: null,
      cols:[],
      reqCols:[],
      forms:[],
      errors:[],
      mustEdit:[],
      showOption:false,
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
      this.parsedData = ''
      this.cols = []
      this.reqCols = Object.values(this.fields).filter(f => f.required == '1').map(f => f.nama_kolom)
      // console.log('show',val)
    }
  },
  computed: {
    
  },
  methods: {
    handlePaste(event) {
      // console.log(event)
      const pastedData = event.clipboardData.getData('Text');
      // Process the pastedData (e.g., split by lines and tabs/commas)
      this.parsedData = this.parseExcelData(pastedData);
      for (let i = 0; i < this.parsedData.length; i++) {
        const element = this.parsedData[i];
        this.forms[i] = []
        this.errors[i] = ''
        for (let j = 0; j < element.length; j++) {
          this.forms[i][j] = ''
        }
        
      }
      console.log(this.forms)
      this.cols = this.parsedData[0].map(a => null) 
      event.preventDefault(); // Prevent default paste behavior if needed
    },
    parseExcelData(data) {
      // Example: Simple parsing for tab-separated values
      const rows = data.split('\n');
      rows.pop()
      const parsedRows = rows.map(row => row.split('\t'));
      return parsedRows;
    },
    addDataToForm(event, key){
      let field = this.fields[this.cols[key]]
      this.mustEdit[key] = false
      // console.log(this.cols[key], field.options)
      for (let i = 0; i < this.parsedData.length; i++) {
        let val = this.parsedData[i][key]
        this.errors[i] = ''
        if (val) {
          this.forms[i][key] = field.options?.length > 0 ? 
            this.getValueFromOption(val, field.options) :
            val
          if (!this.forms[i][key])
            this.mustEdit[key] = true
        }
      }
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
          el.filter((val, col) => cols[col])
          .map((val, col) => {
          return [cols[col], val]
        }))
        addCols.forEach(col => {
          formData[col] = ''
        })
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
          console.log(field.allow_add)
          if (field.allow_add != '1') continue 
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
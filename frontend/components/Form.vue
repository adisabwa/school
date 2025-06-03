<template>
	<div class="">
    <el-form :label-width="labelWidth" :label-position="labelPosition" v-loading="saving" :inline="inline"
      :class="[gridClass,formClass]">
    <slot name="before" :errors="errors" :form="form" :fields="fieldsData"></slot>
      <template  v-for="(field, ind) in fieldsData">
        <el-form-item :class="['grow-0', gridItemClass(field?.span), formItemClass, formItemClass[field.nama_kolom], formItemClass['all']]"
          v-show="resolvedShowColumns.length > 0 ? resolvedShowColumns.includes(field.nama_kolom) : !resolvedPassColumns.includes(field.nama_kolom)"
          :error="field.input == 'array' ? '' : errors[field.nama_kolom]">
          <template #label v-if="showLabel">
            <span :class="[field.required == '1' ? 'required' : '','leading-[1.5] mt-2', labelClass]"> {{ field.label }} </span>
          </template>
          <div class="flex gap-x-3 w-full">
            <el-select v-if="showOriginal" v-model="original[field.nama_kolom]" class="w-[130px] grow-0 shrink-0" size="large" @change="changedValue(field.nama_kolom)">
              <el-option :value="true" label="Nilai Asli"></el-option>
              <el-option :value="false" label="Berubah"></el-option>
            </el-select>
            <template v-if="field.input == 'input' || isEmpty(field.input)">
              <el-input v-model="form[field.nama_kolom]" :placeholder="!isEmpty(field.placeholder) ? field.placeholder : `Masukkan ${field.label}`"
                :class="['w-full',inputClass]" 
                @change="searchData(ind); changedValue(field.nama_kolom)" @input="form[field.nama_kolom] = runFunction(field.function_input, form[field.nama_kolom])"
                :size="size"
                :style="{width:field.width_input + ' !important'}">
                <template #prepend v-if="!isEmpty(field.prepend)"> {{ field.prepend }}</template>
                <template #apppend v-if="!isEmpty(field.apppend)"> {{ field.append }}</template>  
              </el-input>
            </template> 
            <template v-else-if="field.input == 'password'">
              <el-input v-model="form[field.nama_kolom]" :placeholder="!isEmpty(field.placeholder) ? field.placeholder : `Masukkan ${field.label}`"
                :class="['w-full in-password',inputClass]" 
                type="password" show-password
                @change="changedValue(field.nama_kolom)"
                :size="size"
                :style="{width:field.width_input + ' !important'}">
                <template #prefix>
                  <icons icon="material-symbols:lock-outline" />
                </template>
              </el-input>
            </template>
            <template v-else-if="field.input == 'input-number'">
              <el-input-number v-model="form[field.nama_kolom]" :placeholder="!isEmpty(field.placeholder) ? field.placeholder : `Masukkan ${field.label}`"
                @change="changedValue(field.nama_kolom)"
                :class="['w-full',inputClass]" 
                :size="size"
                :style="{width:field.width_input + ' !important'}"/>
            </template>
            <template v-else-if="field.input == 'number'">
              <el-input v-model="form[field.nama_kolom]" :placeholder="!isEmpty(field.placeholder) ? field.placeholder : `Masukkan ${field.label}`"
                :class="['w-full',inputClass]" 
                type="number"
                @change="changedValue(field.nama_kolom)"
                :size="size"
                :style="{width:field.width_input + ' !important'}"/>
            </template>
            <template v-else-if="field.input == 'textarea'">
              <el-input v-model="form[field.nama_kolom]" :placeholder="!isEmpty(field.placeholder) ? field.placeholder : `Masukkan ${field.label}`"
                :class="['w-full',inputClass]" 
                type="textarea" row="3"
                @change="changedValue(field.nama_kolom)"
                :size="size"
                :style="{width:field.width_input + ' !important'}"/>
            </template>
            <template v-else-if="['select','select-multiple','scroll'].includes(field.input)">
              <floating-select v-model:value="form[field.nama_kolom]" :placeholder="!isEmpty(field.placeholder) ? field.placeholder : `Pilih ${field.label}`" 
                filterable clearable
                :allow-create="field.allow_create"
                :class="['w-full',inputClass]" 
                :size="size"
                @change="changedValue(field.nama_kolom)"
                :style="{width:(field.width_input.split('-')[1] ?? '') + ' !important'}"  
                :type="field.input.split('-')[0]"
                :options="field.options"
                :prefix="field.prepend">
                <template v-if="field.allow_add" #footer>
                  <el-button v-if="!field.isAdding" text  size="small" @click="field.isAdding = !field.isAdding">
                    Tambah Pilihan
                  </el-button>
                </template>
              </floating-select>
              <el-dialog v-model="field.isAdding"
                :title="'Tambah ' + field.label"
                class="p-7"
                :close-on-click-modal="false"
                width="500px">
                <Form 
                  :fields="field.addFields" 
                  ref="formAdd"
                  :key="'from'+field.nama_kolom"
                  size="default"
                  :show-label="false"
                  :href="field.addHref"
                  :href-get="field.addHrefGet"
                  @saved="field.isAdding = false; resetOptions(ind, field.addReset);"
                  :show-required-text="false"
                  :text-submit="'Konfirmasi'"
                  />
                <el-button size="default" @click="field.isAdding = !field.isAdding"
                  class="float-left translate-y-[-25px]">Batal</el-button>
              </el-dialog>
            </template>
            <template v-else-if="field.input.includes('select-double')">
              <floating-select v-model:value="field.parentSelect" :placeholder="!isEmpty(field.placeholder) ? field.placeholder : `Pilih ${field.label1}`" 
                :class="['w-full',inputClass]" 
                filterable clearable
                @change="form[field.nama_kolom] = null; changedValue(field.nama_kolom)"
                :size="size"
                :options="field.options"
                :type="field.input.split('/')[1]?.split('-')[0]"
                :style="{width:(field.width_input.split('-')[0] ?? '') + ' !important'}"
                :prefix="field.prepend1">
              </floating-select>
              <floating-select v-model:value="form[field.nama_kolom]" :placeholder="!isEmpty(field.placeholder) ? field.placeholder : `Pilih ${field.label2}`" 
                filterable clearable
                :class="['w-full',inputClass]" 
                @change="changedValue(field.nama_kolom)"
                :size="size"
                :type="field.input.split('/')[1]?.split('-')[1]"
                :style="{width:(field.width_input.split('-')[1] ?? '') + ' !important'}"  
                :options="field.options[field.parentSelect]?.options"
                :prefix="field.prepend2">
                <template v-if="field.allow_add" #footer>
                  <el-button v-if="!field.isAdding" text  size="small" @click="field.isAdding = !field.isAdding">
                    Tambah Pilihan
                  </el-button>
                </template>
              </floating-select>
              <el-dialog v-model="field.isAdding"
                :title="'Tambah ' + field.label"
                class="p-7"
                :close-on-click-modal="false"
                width="500px">
                <form-comp 
                  :fields="field.addFields" 
                  ref="formAdd"
                  :key="'from'+field.nama_kolom"
                  size="default"
                  :show-label="false"
                  :href="field.addHref"
                  :href-get="field.addHrefGet"
                  @saved="field.isAdding = false; resetOptions(ind, field.addReset);"
                  :show-required-text="false"
                  :text-submit="'Konfirmasi'"
                  />
                <el-button size="default" @click="field.isAdding = !field.isAdding"
                  class="float-left translate-y-[-25px]">Batal</el-button>
              </el-dialog>
            </template>
            <template v-else-if="field.input=='radio'">
              <el-radio-group v-model="form[field.nama_kolom]"
                :class="[inputClass]" 
                @change="changedValue(field.nama_kolom)"
                :style="{width:field.width_input + ' !important'}">
                <template 
                  v-for="item in field.options"
                  :key="item">
                  <el-radio-button :value="item.value" :size="size">{{ item.label }}</el-radio-button>
                </template>
              </el-radio-group>
            </template>
            <template v-if="field.input == 'date-wheel'">
              <date-wheel-picker
                :placeholder="!isEmpty(field.placeholder) ? field.placeholder : `Masukkan ${field.label}`" :class="['w-full',inputClass]" 
                v-model:value="form[field.nama_kolom]"
                value-format="YYYY-MM-DD"
                format="DD MMMM YYYY"
                clearable 
                :size="size"
                @change="changedValue(field.nama_kolom)"
                :style="{width:field.width_input + ' !important'}"
              />
            </template>
            <template v-else-if="field.input == 'date'">
              <el-date-picker
                :placeholder="!isEmpty(field.placeholder) ? field.placeholder : `Masukkan ${field.label}`"
                :class="['w-full',inputClass]" 
                v-model="form[field.nama_kolom]"
                clearable 
                value-format="YYYY-MM-DD"
                format="DD MMMM YYYY"
                @change="changedValue(field.nama_kolom)"
                @blur="changeData"
                :size="size"
                :style="{width:field.width_input + ' !important'}"/>
            </template>
            <template v-else-if="field.input == 'file'">
              <div  :class="['w-full',inputClass]" 
                :style="{width:field.width_input + ' !important'}">
                <div v-if="!isEmpty(links[field.nama_kolom])">
                  <el-checkbox v-model="field.change">Upload Ulang</el-checkbox>
                </div>
                <file v-if="!field.change" :href="`${links[field.nama_kolom]}`" :title="`Unduh ${field.label}`"/>
                <el-upload 
                  v-else
                  class="doc-upload-wrap max-w-[400px]" 
                  :ref="field.nama_kolom" 
                  :auto-upload="false"
                  :multiple="false"
                  :limit="1"
                  :on-change="getFileRaw(field.nama_kolom)"
                  accept="application/pdf,image/jpeg,image/jpg,image/png"
                  action=""
                  drag>
                  <icons icon="material-symbols:cloud-upload" class="text-5xl text-blue-600"/>
                  <div class="mx-auto w-[80%] el-upload__text leading-[1.5] text-[13px]">Tarik dokumen kesini atau <em>klik untuk mengunggah</em></div>
                  <div class="mx-auto w-[80%] el-upload__tip leading-[1.5] text-[11px] my-1" slot="tip">
                    Dokumen JPG/PNG/PDF, maks. 5 MB.
                  </div>
                </el-upload>
              </div>
            </template>
            <template v-else-if="field.input == 'array'">
              <div>
                <div v-for="(recData, ind) in form[field.nama_kolom]"
                  class="flex flex-col gap-y-0"
                  :style="{width:field.width_input + ' !important'}">
                  <form-comp ref="formItem"
                    class="mb-0"
                    :key="'form-item-'+ ind"
                    :fields="field.fields"
                    v-model:form-value="form[field.nama_kolom][ind]" 
                    v-model:error-value="errors[field.nama_kolom][ind]"
                    size="large"
                    :show-submit="false"
                    :show-label="false"
                    :inline="true"
                    form-class="flex gap-2 mb-0 items-start"
                    form-item-class="w-full m-0"
                    input-class="[&_*]:rounded-[15px]"
                    :show-required-text="false">
                  </form-comp>  
                  <div class="ml-3 mt-1 mb-2 flex items-baseline">
                    <el-button text class="text-sky-500
                      p-0 h-auto text-[12px]"
                      @click="() => {
                        let array = form[field.nama_kolom]
                        console.log(array, ind)
                        form[field.nama_kolom].splice(ind, 0, 
                          JSON.parse(JSON.stringify(array[ind]))
                        )
                      }">
                      Tambah</el-button>
                    <template v-if="form[field.nama_kolom].length > 1">
                      <el-divider direction="vertical" class="translate-y-[3px]"/>
                      <el-button text class="text-red-500
                        p-0 h-auto text-[12px]"
                        @click="form[field.nama_kolom].splice(ind, 1)">
                        Hapus</el-button>
                    </template>
                  </div>
                </div>
              </div>
            </template> 
          </div>
        </el-form-item>
      </template>
      <slot :errors="errors" :form="form" :fields="fieldsData"></slot>
      <div class="text-md mt-10" v-if="showRequiredText">
        <span class="text-red-500">)*</span> isian harus diisi
      </div>
    </el-form>
    <div class="text-right" v-if="showSubmit">
      <el-button type="success" :size="size" @click="submitForm"
        :disabled="saving">
        <icons v-if="saving" icon="eos-icons:loading"/>
        {{ submitText }}
      </el-button>
    </div>
	</div>
</template>

<script>
import { defineAsyncComponent } from 'vue'

export default {
  name: 'Form',
  components: {
    
  },
  props: {
    id: {
      type:[Number, String, Array],
      default: ''
    },
    formValue: {
      type:[Array, Object],
      default:[],
    },
    errorValue:{
      type:[Array, Object],
      default:[],
    },
    fields: {
      type:[Array, Object],
      default:[],
    },
    size: {
      type: String,
      default: 'large',
    },
    showLabel: {
      type: Boolean,
      default: true,
    },
    labelWidth: {
      type: String,
      default: '100px',
    },
    labelPosition: {
      type: String,
      default: 'left',
    },
    submitText:{
      type:String,
      default: 'Submit',
    },
    showSubmit:{
      type:Boolean,
      default: true,
    },
    inline:{
      type:Boolean,
      default: false,
    },
    href:{
      type:String,
      default: '',
    },
    hrefGet:{
      type:String,
      default: '',
    },
    errorSubmitText:{
      value:String,
      default:'Tidak dapat menyimpan'
    },
    showRequiredText:{
      type:Boolean,
      default: true,
    },
    showNotification:{
      type:Boolean,
      default: true,
    },
    passColumns:{
      type:Array,
      default:[],
    },
    showColumns:{
      type:Array,
      default:[],
    },
    formClass:{
      type:String,
      default:'mb-4',
    },
    formItemClass:{
      type:String,
      default:'mb-3',
    },
    labelClass:{
      type:String,
      default:'',
    },
    inputClass:{
      type:String,
      default:'',
    },
    cols:{
      type:[String, Number],
      default:'6',
    }
  },
  inject: ['sharedState'],
  emits:['update:id','saved','error','get','changeId','update:formValue','changedValue','update:errorValue'],
  data: function() {
    return {
      saving: false,
      showOriginal: true,
      form:{},
      links:{},
      errors:{},
      original:{},
      fieldsData:{},
      dataId:null,
      // initial:true,
    };
  },
  watch: {
    fieldsData: function(val, oldVal) {
      
    },
    id: {
      immediate: true,
      async handler(val) {
        this.dataId = val;
        this.showOriginal = val instanceof Array
      }
    },
    dataId: function(val, oldVal) {
      this.$emit('update:id', val);
        // this.initial = val <= 0

        this.getData({id:val})
      // }
    },
    form: {
      handler(newVal, oldVal) {
        this.$emit('update:formValue', newVal)
      },
      deep: true, // Watch nested properties
    },
    formValue: {
      handler(newVal, oldVal) {
        console.log('form-value', newVal)
      },
      deep: false, // Watch nested properties
    },
    errors: {
      handler(newVal, oldVal) {
        this.$emit('update:errorValue', newVal)
      },
      deep: true, // Watch nested properties
    },
    errorValue: {
      handler(newVal, oldVal) {
        this.fillObjectValue(this.errors, newVal)
        // console.log('new-error', this.errors)
      },
      deep: true, // Watch nested properties
    },
  },
  computed: {
    resolvedPassColumns(){
      if (Array.isArray(this.passColumns) && this.passColumns.length > 0)
        return this.passColumns 
      
      if (Array.isArray(this?.sharedState?.passColumns) && this?.sharedState?.passColumns?.length > 0)
        return this?.sharedState?.passColumns
      
       return []
    },
    resolvedShowColumns(){
      if (Array.isArray(this.showColumns) && this.showColumns.length > 0)
        return this.showColumns 
      
      if (Array.isArray(this?.sharedState?.showColumns) && this?.sharedState?.showColumns?.length > 0)
        return this?.sharedState?.showColumns
      
       return []
    },
    gridClass(){
      return 'gap-x-4 grid grid-cols-' + this.cols
    },
  },
  methods: {
    gridItemClass(span){
      if (this.isEmpty(span))
        return 'col-span-' + this.cols
      else
        return 'col-span-' + span
    },
    changeData({field, value, parent, func = null}){
      // if (this.initial) return
      // console.log('change')
      if (typeof func == 'function') {
        value = func({
          field:field,
          fieldData:this.fields[field],
          value:value,
        })
      }
      
      this.form[field] = value
      if (!this.isEmpty(parent))
        this.fields[field].parentSelect = parent
    },
    changedValue(field){
      // if (this.initial) return
      let value = this.form[field]
      let parent = this.fields[field]?.parentSelect
      let options = this.fields[field]?.options ?? []
      // console.log(field, value, parent, options)
      if (Array.isArray(options)) {
        options = Object.values(options)
      }
      this.$emit('changedValue', {
        field: field,
        value: value,
        parent: parent,
        option: options?.filter?.(d => d.value == value)?.[0]
      })
      this.errors[field] = ''
    },
    searchData(ind){
      let field = this.fieldsData[ind]
      let where = {}
      if (field.search == '1') {
        where[field.nama_kolom] = this.form[field.nama_kolom]
        this.getData(where, true)
      }   
    },
    async getData(where, changeId){
      console.log('get-data', this.initial)
      await this.settingFields();
      if (this.hrefGet == '') 
        return
      this.saving = true
      await this.$http.get(this.hrefGet,
        {
          params:where
        }
      )
        .then(result => {
          this.saving = false;
          var psb = result.data;
          if (!this.isEmpty(psb)) {
            // console.log('psb:', psb);
            // console.log('form keys:', Object.keys(this.form));
            this.fillObjectValue(this.form, psb)
            this.fillObjectValue(this.links, psb)
            if (changeId) {
              this.dataId = psb.id
              this.$emit('changeId', this.dataId);
            }
            // console.log(psb, this.form)
            let fieldsData = Object.values(this.fields)
            fieldsData.forEach(d => {
              if (d.input.includes('select-double')) {
                // delete vm.form[d.nama_kolom]
                this.fields[d.nama_kolom].parentSelect = psb.parentSelect[d.nama_kolom]
              }
            })
            // console.log(this.fields)
          }
          this.$emit('get')
          // setTimeout(() => {
          //   this.initial = false
          // }, 500)
          // console.log(psb, this.form)
        })
        .catch(err => {
          console.log(err)
          this.saving = false;
          var res = err.response;
          var code = res.status;
          if (this.showNotification)
            this.$notify.error({
              title: 'Gagal',
              message: 'Tidak dapat mengambil data',
              position: 'bottom-right'
            });
        });
    },
    getFileRaw(refName) {
      return (file, fileList) => {
        const rawFile = file.raw; // Access the raw file
        const refInstance = this.$refs[refName]; // Get the ref instance

        this.form[refName] = rawFile
      };
    },
    resetOptions(ind, link){
      this.$http.get(link)
        .then(res => {
          let data = res.data
          this.fieldsData[ind].options = data
        })
    },
    submitForm(){
      this.saving = true;
      this.resetObjectValue(this.errors)
      let vm = this
      let form = this.form
      let backUpForm = JSON.parse(JSON.stringify(vm.form))
      Object.keys(vm.original).forEach(ind => {
        if (vm.original[ind]) 
          delete form[ind]
      });
      form.id = this.dataId
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
          this.$emit('error', false);
          
          if (code == '400') {
            // Populating error message
            this.fillAndAddObjectValue(this.errors, res.data.messages);
            console.log(res.data.messages, this.errors)
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
    settingFields() {
      return new Promise((resolve) => {
        let vm = this;

        vm.form = {};
        vm.errors = {};
        vm.links = {};
        vm.original = {};
        vm.fieldsData = {};

        Object.values(vm.fields).forEach(d => {
          if (d.from_user == '1' || d.from_user == undefined) {
            vm.fieldsData[d.nama_kolom] = d;
            vm.form[d.nama_kolom] = d.default ?? (d.input == 'array' ? [] : '');
            vm.errors[d.nama_kolom] = d.input == 'array' ? [] : '';
            vm.original[d.nama_kolom] = false;
            if (d.input == 'file') {
              vm.links[d.nama_kolom] = '';
            }
          }
        });

        console.log('form isi', vm.form);
      vm.fillObjectValue(vm.form, vm.formValue)
      setTimeout(() => {
        vm.fillObjectValue(vm.form, vm.formValue)
      },500)
        resolve();
      });
    }
  },
  mounted(){
    // console.log('mounted')
    // this.settingFields();
    this.getData({id:this.dataId});
  }
}
</script>

<style lang="postcss" scoped>
:deep(.el-input__wrapper) {
  @apply rounded-[15px];
}
:deep(.el-input-group__prepend) {
  @apply rounded-l-[15px];
}
:deep(.el-input-group__prepend + .el-input__wrapper) {
  @apply rounded-none rounded-r-[15px];
}
:deep(.el-form-item__error) {
  margin-top: 3px;
  position: relative;
}
</style>
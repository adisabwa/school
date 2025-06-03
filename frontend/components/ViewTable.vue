<template>
	<div class="">
    <el-form 
      :label-width="labelWidth" :label-position="labelPosition" class="w-full">
      <template  v-for="(field, ind) in fields">
        <el-form-item :class="[`mb-0 px-4 [&_*]:items-center [&_*]:m-0
          pointer hover:bg-gray-200
          border-0 border-b border-solid border-gray-300`,
          ind == fields.length - 1 ? 'border-b-0' : '',
          labelPosition == 'left' ? 'flex items-center [&_*]:leading-[1.5] py-2 ' : 'flex flex-col [&_*]:leading-[1.7] py-[8px]']"
          v-if="!passColumns.includes(field.nama_kolom)">
          <template #label>
            <div class="font-semibold my-2"> {{ field.label }} </div>
          </template>
          <div class="flex">
            <span v-if="labelPosition == 'left'"> &nbsp; &nbsp; :  &nbsp; &nbsp; </span>
            <template v-if="field.input == 'file'">
              <file v-if="!isEmpty(form[field.nama_kolom])" :href="`${form[field.nama_kolom]}`" :title="`Unduh ${field.label}`" class="font-bold"/>
              <span v-else>Belum Ada File</span>
            </template>
            <template v-else-if="field.input == 'tag'">
              <el-tag :type="setStatusType(form[field.nama_kolom]) "  effect="dark">
                {{ setStatusText(form[field.nama_kolom]) }}
              </el-tag>
            </template>
            <template v-else>
              <div v-if="hideContents.includes(field.nama_kolom)">
                <slot :name="field.nama_kolom+'-inside'" :data="isEmpty(field.view_kolom) ? form[field.nama_kolom] : form[field.view_kolom]" :field="field"></slot>
              </div>
              <div v-else>
                {{ runFunction(field.function, (isEmpty(field.view_kolom) ? form[field.nama_kolom] : form[field.view_kolom]), field.options ) }}
              </div>
            </template>
          </div>
        </el-form-item>
      </template>
    </el-form>
    <slot name="outside-form" :form="form"/>
	</div>
</template>

<script>
import { cloneDeep } from 'lodash';

export default {
  name: 'loading',
  props: {
    keyword: {
      type:[String],
      default: ''
    },
    fields: {
      type:[Array,Object],
      default:[],
    },
    size: {
      type: String,
      default: 'large',
    },
    labelWidth: {
      type: String,
      default: '100px',
    },
    labelPosition: {
      type: String,
      default: 'left',
    },
    hrefGet:{
      type:String,
      default: '',
    },
    searchColumns:{
      type:[Array,Object],
      default:[],
    },
    empty:{
      type:Boolean,
      default:false,
    },
    loading:{
      type:Boolean,
      default:false,
    },
    id:{
      type:[String,Number],
      default:-1,
    },
    hideContents: {
      type:[Array,Object],
      default:[],
    },
    passColumns: {
      type:[Array,Object],
      default:[],
    },
    setStatusText:{
      type:Function,
      default: () => {return ''}
    },
    setStatusType:{
      type:Function,
      default: () => {return ''}
    },
    formValue: {
      type:[Array, Object],
      default:[],
    },
  },
  emits:['update:empty','update:id','update:loading','update:formValue'],
  data: function() {
    return {
      form:{},
      innerLoading:false,
    };
  },
  watch: {
    keyword: function(val, oldVal) {
      this.getData(val);
    },
    loading(val){
    },
    form: {
      handler(newVal, oldVal) {
        this.$emit('update:formValue', newVal)
      },
      deep: true, // Watch nested properties
    },
    formValue: {
      handler(newVal, oldVal) {
        
      },
      deep: false, // Watch nested properties
    },
  },
  computed: {
    
  },
  methods: {
    getData(keyword, from = 'parent'){
      if (this.isEmpty(keyword))
        return this.emptyData()

      let params = {or:{}}
      this.searchColumns.forEach(d => {
        params.or[d] = keyword
      })
      this.getLoading(from, true)
      this.$http.get(this.hrefGet,
        {
          params:params
        }
      )
        .then(result => {
          this.getLoading(from, false)
          var data = result.data;
          if (!this.isEmpty(data)) {
            this.form = cloneDeep(data)
            this.$emit('update:empty', false);
            this.$emit('update:id', data.id);
          } else {
            this.emptyData()
          }
        })
        .catch(err => {
          this.getLoading(from, false)
          var res = err.response;
          var code = res.status;
            this.$notify.error({
              title: 'Gagal',
              message: 'Tidak dapat mengambil data',
              position: 'bottom-right'
            });
        });
    },
    getLoading(from, val){
      if (from == 'inner')
        this.innerLoading = val
      else
        this.$emit('update:loading', val);
    },
    emptyData(){
      this.resetObjectValue(this.form)
      this.$emit('update:empty',true)
      this.$emit('update:id',-1)
    },
    settingFields(){
      let vm = this
      vm.form = {}
    }
  },
  mounted(){
    this.getData(this.keyword,'inner')
    this.settingFields();
  }
}
</script>

<style lang="scss" scoped>
	
</style>
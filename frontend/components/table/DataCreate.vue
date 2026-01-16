<template>
  <teleport to="body">
    <div class="data-create-dialog">
      <el-dialog 
        :title="(type == 'create' ? 'Tambah' : 'Edit') + ' ' + title" 
        v-model="showDialog"
        class="w-auto sm:max-w-[50%] mx-10 sm:mx-auto
          px-6"
        :close-on-click-modal="false">
        <form-comp ref="form"
          :fields="fields" 
          :key="'from'+key"
          :id="dataId"
          label-position="top"
          class="mt-6"
          v-model:form-value="valueForm"
          :label-position="labelPosition"
          :pass-columns="passColumns"
          :show-columns="showColumns"
          :href="href"
          :href-get="hrefGet"
          :addValues="addValues"
          @changedValue="changedValue"
          @saved="submitted"  
          @error="saving=false"
          :show-submit="false"
          >
        </form-comp>
        <template #footer>
          <span class="dialog-footer">
            <el-button @click="showDialog = false">Batal</el-button>
            <el-button 
              type="success" 
              @click="$refs.form.submitForm(); saving=true"
              :disabled="saving">Simpan</el-button>
          </span>
        </template>
      </el-dialog>
    </div>
  </teleport>
</template>
  
<script>
  
  export default {
    name: 'data-create-dialog',
    components:{
    },
    props: {
      show: {
        type: Boolean,
        default: false,
      },
      type: {
        type: String,
        default: 'create'
      },
      width: {
        type: String,
        default: ''
      },
      dataId: {
        type: [Number, String, Array],
        default: -1,
      },
      title: {
        type: String,
        default: 'Data'
      },
      href: {
        type: String,
        default: ''
      },
      hrefGet: {
        type: String,
        default: ''
      },
      fields: {
        type: [Array, Object],
        default: []
      },
      passColumns:{
        type:Array,
        default: [],
      },
      showColumns:{
        type:Array,
        default: [],
      },
      formValue: {
        type:[Array, Object],
        default:[],
      },
      labelPosition: {
        type: String,
        default: 'top'
      },
      addValues: {type:[Object, Array], default: {}},
    },
    emits:['update:show','saved','update:formValue','changedValue'],
    data: function() {
      return {
        saving: false,
        showDialog: false,
        key:0,  
        valueForm: {},
      };
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
        if (val==true) {
          this.key++
          // this.resetData();
        }
      },
      formValue: {
        immediate: true,
        deep: true,
        handler(val) {
          this.valueForm = val;
        }
      },
      fields: {
        deep: true,
        handler(val) {
        //  this.key += 1
        //  console.log('key', this.key)
        }
      },
      valueForm: {
        deep: true,
        handler(val) {
          this.$emit('update:formValue', val);
        }
      }
    },
    computed: {
    },
    methods: {
      submitted(){
        this.saving = false
        this.showDialog = false
        this.$emit('saved')
      },
      resetForm(){
        // console.log(this.key)
        setTimeout(() => {
          this.key = this.key + 1
          // console.log('reset-form',this.key)
        }, 300)
      },
      changedValue(params){
        this.$emit('changedValue', params)
      }
    },
    mounted() {
      console.log('field-data', this.fields)
    }
  }
  </script>
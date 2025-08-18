<template>
  <teleport to="body">
    <div class="data-view-dialog">
      <el-dialog 
        :title="'Data ' + title" 
        v-model="showDialog"
        class="w-auto sm:max-w-[50%] mx-10 sm:mx-auto
          px-6"
        :close-on-click-modal="false">
        <view-table ref="viewTable"
          :fields="fields" 
          :key="'from'+key"
          :params="params"
          :label-position="labelPosition"
          :pass-columns="passColumns"
          :show-columns="showColumns"
          class="mt-6"
          label-width="150px"
          :href-get="hrefGet"
          @saved="submitted"  
          v-model:id="viewId"
          />
        <template #footer>
          <span class="dialog-footer">
            <el-button @click="showDialog = false">Batal</el-button>
          </span>
        </template>
      </el-dialog>
    </div>
  </teleport>
</template>
  
<script>
  import ViewTable from '@/components/form/ViewTable.vue'
  
  export default {
    name: 'data-view-dialog',
    components:{
      ViewTable,
    },
    props: {
      show: {
        type: Boolean,
        default: false,
      },
      type: {
        type: String,
        default: 'view'
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
      params: {
        type: [Array, Object],
        default: []
      },
    },
    emits:['update:show','saved','update:formValue'],
    data: function() {
      return {
        saving: false,
        showDialog: false,
        key:0,  
        valueForm: {},
        viewId:'',
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
        }
      },
      dataId: {
        immediate: true,
        handler(val) {
          this.viewId = val;
        }
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
      }
    },
    computed: {
    },
    methods: {
      submitted(){
        this.saving = false
        this.showDialog = false
        this.$emit('saved')
      }
    },
  }
  </script>
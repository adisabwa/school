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
          :href="href"
          :href-get="hrefGet"
          @saved="submitted"  
          @error="saving=false"
          :show-submit="false"
          ></form-comp>
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
  import Form from '@/components/Form.vue'
  
  export default {
    name: 'data-create-dialog',
    components:{
      'form-comp' : Form,
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
    },
    emits:['update:show','saved'],
    data: function() {
      return {
        saving: false,
        showDialog: false,
        key:0,  
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
    created() {
  
    }
  }
  </script>
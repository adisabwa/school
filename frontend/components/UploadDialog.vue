<style lang="postcss">
  .el-upload-dragger {
    @apply py-7 px-5 !important;
  }
</style>
<template>
  <div>
    <el-dialog  
      v-model="showDialog"
      class="p-7"
      :close-on-click-modal="false"
      width="500px">
      <template #header>
        <b>Upload Data {{ title }} File Excel</b>
      </template>
       <div class="text-center">
        <el-upload
          class="upload-demo mt-4"
          ref="upload" drag
          :action="`${$siteUrl}/${link}/upload${params}`"
          :on-change="handleChange" :on-success="handleSuccess" :on-error="handleError"
          :file-list="fileList"  :auto-upload="false">
          <icons icon="material-symbols:cloud-upload" class="text-5xl text-blue-600"/>
          <div class="el-upload__text">Drop file here or <em>click to upload</em></div>
          <div class="el-upload__tip text-sm" slot="tip">.xls, kurang dari 5MB</div>
        </el-upload>
        <div class="text-left mx-6" style="word-break: break-word;">
          <ul class="p-0">
            <li>File yang diunggah harus sesuai dengan template yang sudah ditentukan.<br>
                Silakan <a type="text" class="cursor-pointer text-blue-500" @click="downloadTemplate">Unduh Template.</a></li>
            <li>Apabila ada data {{ title }} yang sebelumnya sudah ada, maka akan dilewati.</li>
            <li>Apabila ada data {{ title }} yang keliru, maka akan dilewati.</li>
          </ul>
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


    <el-dialog 
      title="Konfirmasi" 
      width="1000px"
      v-model="showErrorDialog">
      <h3>Ada data yang keliru</h3>
      <br>
      <p>Berikut adalah data yang tidak bisa disimpan</p>
      <el-table
        :data="dataError"
        max-height="300"
        style="width: 100%">
        <el-table-column
          prop="keterangan"
          label="Data">
        </el-table-column>
        <el-table-column
          prop="error"
          label="Kesalahan">
        </el-table-column>
      </el-table>
      <br/>
      <template #footer>
        <el-button @click="showErrorDialog = false">Batal</el-button>
        <el-button type="success" @click="showErrorDialog = false; showDialog = true;">Upload Ulang</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script>

export default {
  name: 'upload-dialog',
  props: {
    show: {
      type: Boolean,
      default: false,
    },
    title: {
      type:String,
      default:'Peminatan',
    },
    link: {
      type:String,
      default:'data/peminatan',
    },
    params: {
      type:String,
      default:'',
    },
  },
  emits:['update:show'],
  data: function() {
    return {
      saving: false,
      upload: false,
      showDialog: false,
      fileList: [],
      dataError:[],
      showErrorDialog: false,
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
      // console.log(this.show, val);
      if (val==true) {
        this.showUpload();
      }
    }
  },
  computed: {
    
  },
  methods: {
    showUpload: function() {
      this.saving = false;
      this.fileList = [];
      this.showUploadDialog = true;
    },
    handleChange(file, fileList) {
      // console.log('change');
      if (!this.upload) this.fileList = [file];
      this.upload = false;
    },
    submitUpload() {
      if (this.isEmpty(this.fileList)) {
        this.$notify({
          type:'error',
          title: 'Gagal',
          message: 'File belum dipilih',
          position: 'bottom-right'
        });
      } else {
        // this.saving = true;
        // this.upload = true;
        this.$refs.upload.submit();
      }
    },
    handleSuccess(respone,file, fileList) {
      this.$refs.upload.clearFiles();
      // console.log(respone)
      this.$notify.success({
        title: 'Berhasil',
        message: 'File berhasil di upload',
        position: 'bottom-right'
      });
      this.showDialog = false;
      this.dataError = respone.error;
      if (!this.isEmpty(this.dataError)) {
        this.showErrorDialog = true;
      }
      this.$emit('saved', respone.data);
      this.saving = false;
    },
    handleError(err,file, fileList) {
      this.$refs.upload.clearFiles();
      this.saving = false;
      let message = JSON.parse(err.message)
      this.$notify({
        type:'error',
        title: 'Gagal',
        message: message.messages.file,
        position: 'bottom-right'
      });
    },
    downloadTemplate:  function() {
      window.open(`${this.$siteUrl}${this.link}/template`,"_blank");
    },
  }
}
</script>
<style lang="sass" scoped>
.text-danger
  font-size: 120%
</style>
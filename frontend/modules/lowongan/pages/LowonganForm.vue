<template>
  <el-card
    header-class="flex gap-x-2 items-center " v-loading="loading" class="w-full">
    <template #header>
      <el-button type="text" class="flex items-center gap-2 text-sm font-bold text-[#64748b] hover:text-[#4f46e5] transition-colors"
        @click="$router.go(-1)">
        <icons icon="mdi:arrow-left"/>
      </el-button>
      {{ (dataType == 'create' ? 'Tambah' : 'Edit') + ' Data Lowongan' }}
    </template>
    <form-comp ref="form"
      :fields="fields" 
      :key="'from'+keyCreate"
      :id="editId"
      label-position="top"
      class=""
      v-model:form-value="valueForm"
      v-model:files-value="filesForm"
      :label-position="$windowWidth < 640 ? 'top' : 'left'"
      :label-width="230"
      :href="'lowongan/store'"
      :href-get="'lowongan/get'"
      size="default"
      @get="getPersyaratan"
      @changedValue="changedValue"
      @saved="onUpdated"  
      @error="saving=false">
        <template #before="{ form, errors, fields }">
          <el-form-item class="col-span-6" prop="poster" :error="errors?.poster">
            <template #label>
              <span class="font-semibold">Poster</span>
              <span class="text-xs text-gray-400 block mb-2">Unggah poster lowongan kerja dalam format JPG/PNG/PDF.</span>
              <el-checkbox v-model="isGenerate">
                Generate Data secara otomatis dari Poster
              </el-checkbox>
            </template>
            <el-upload 
              class="doc-upload-wrap max-w-[600px]" 
              ref="poster" 
              :auto-upload="false"
              :multiple="false"
              :limit="1"
              :on-change="getDataFromImage"
              accept="application/pdf,image/jpeg,image/jpg,image/png"
              drag>
              <icons icon="material-symbols:cloud-upload" class="text-5xl text-blue-600"/>
              <div class="mx-auto w-[80%] el-upload__text leading-[1.5] text-[13px]">Tarik dokumen kesini atau <em>klik untuk mengunggah</em></div>
              <div class="mx-auto w-[80%] el-upload__tip leading-[1.5] text-[11px] my-1" slot="tip">
                Dokumen JPG/PNG/PDF, maks. 5 MB.
              </div>
            </el-upload>
          </el-form-item>
        </template>
        <template #default="{ form, errors, fields }">
          <el-form-item prop="persyaratan" :error="errors?.persyaratan"
            class="col-span-6">
            <template #label>
              <span class="font-semibold">Persyaratan</span>
              <span class="text-xs text-gray-400 block mb-2">Masukkan persyaratan lowongan kerja, pisahkan dengan enter.</span>
            </template>
            <div v-for="(item, index) in persyaratan" :key="index" class="flex gap-2 items-center mb-2 w-full">
              <span class="font-bold"># {{ index + 1}}</span>
              <el-input 
                @paste="(event) => { handlePaste(event, index)}"
                v-model="persyaratan[index]" placeholder="Masukkan persyaratan" class="flex-1 w-full" />
              <el-button type="primary" @click="editData" circle class="m-0">
                <icons icon="mdi:plus" class="m-0"/>
              </el-button>
              <el-button type="danger" @click="persyaratan.splice(index, 1)" circle class="m-0" v-if="persyaratan.length > 1">
                <icons icon="mdi:delete" class="m-0"/>
              </el-button>
            </div>
          </el-form-item>
        </template>
      </form-comp>
  </el-card>
</template>

<script>
export default {
  name:'lowongan-form',
  data(){
    return {
      loading:false,
      saving:false,
      valueForm:{},
      filesForm:{},
      keyCreate:0,
      fields:{},
      editId:null,
      dataType:'create',
      persyaratan:[],
      isGenerate:true,
    }
  },
  watch:{
    persyaratan:{
      handler(newValue){
        this.valueForm.persyaratan = newValue.join(';');
      },
      deep:true
    },
  },
  methods:{
    getInitial(){
      this.$http.get('kolom/preparation?table=lowongan&grouping=0').then((res)=>{
        this.fields = res.data;
        this.fields.poster.hidden = '1'
        // this.$refs.form.settingFields();
        let query = this.$route.query;
        console.log(query)
        if(query?.id){
          this.dataType = 'edit';
          this.editId = query.id;
        } else {
          this.dataType = 'create';
          this.editId = null;
        }
        this.keyCreate++;
      })
    },
    getPersyaratan(data){
      console.log(data)
      if (!isEmpty(data?.persyaratan)) {
        this.persyaratan = data.persyaratan.split(';')
      } else {
        this.persyaratan = ['']
      }
    },
    getDataFromImage(val){
      console.log(val)
      this.filesForm.poster = val.raw
      if (!this.isGenerate)
        return
      if (!val) 
        return;
      this.loading = true
      let formData = new FormData();
      formData.append('poster', val.raw);
      this.$http.post('lowongan/generate-content-data', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      } )
      .then((res)=>{
        console.log(res.data)
        this.loading = false;
        let data = res.data;
        this.valueForm.nama_lowongan = data?.nama_lowongan;
        this.valueForm.perusahaan = data?.perusahaan;
        this.valueForm.alamat_lowongan = data?.alamat_lowongan;
        this.valueForm.keterangan_lowongan = data?.keterangan_lowongan;
        this.valueForm.type = data?.type;
        this.persyaratan = data?.persyaratan || [''];
        this.valueForm.email_lowongan = data?.email_lowongan;
        this.valueForm.kontak_lowongan = data?.kontak_lowongan;
        this.valueForm.tanggal_mulai = data?.tanggal_mulai;
        this.valueForm.tanggal_selesai = data?.tanggal_selesai;
        this.valueForm.gaji_start = data?.gaji_start;
        this.valueForm.gaji_end = data?.gaji_end;
      })
      .catch((err)=>{
        this.loading = false;
        console.log(err)
      })
    },
    editData(){
      let filter = this.persyaratan.filter((item)=>item == '');
      console.log(filter)
      if (filter.length > 0) return;
      this.persyaratan.push('');
    },
    submitForm(){
      this.saving = true;
      this.$refs.form.submit();
    },
    changedValue(value){
      this.valueForm = value;
    },
    onUpdated(data){
      this.saving = false;
      console.log(this.valueForm)
      this.$router.push({name:'lowongan-list'});
    },
    async handlePaste(event, key) {
      const pastedData = await event.clipboardData.getData('Text');
      const rows = pastedData.split('\n');
      console.log(rows)
      rows.forEach((row, index) => {
        console.log(row, row.trim() != '')
        if (row.trim() != '') {
          if (!this.persyaratan[key]) {
            this.persyaratan[key] = row.trim();
          } else {
            this.persyaratan.splice(key, 1, row.trim());
          }
          key++
        }
      });
      event.preventDefault(); // Prevent default paste behavior if needed
    }
  },
  mounted(){
    this.getInitial()
  }
}
</script>
<template>
  <div id="register" class="max-w-[1100px] mx-6 sm:mx-auto bg-white bg-opacity-[0.9]
    border-solid border border-gray-300">
    <div class="h-full sm:px-10">
      <div class="flex flex-col items-center align-middle
          pt-[90px] pb-20">
        <div class="w-full
          text-center">
          <h2 class="mt-0 mb-0 text-center text-2xl
            font-[600] font-montserrat ">Buat Akun Baru</h2>
          <div class="mt-0 mb-0 text-center text-[12px]
          font-montserrat ">Silahkan Isi Data Diri Terlebih Dahulu</div>
          <el-divider class="my-4 mx-0"/>
          <el-form class="flex flex-col mt-6 [&_*]:rounded-[15px] [&_*]:text-center"
            label-position="top">
            <el-form-item label="Nama Guru">
              <floating-select v-model:value="form.id" placeholder="Pilih Nama Anda" 
                filterable clearable
                size="large" class="w-full"
                :options="optionsGuru">
              </floating-select>
            </el-form-item>
            <el-form-item label="Email">
              <el-input v-model="form.email" placeholder="Email Anda" size="large" class="w-full"/>
            </el-form-item>
          </el-form>
          <el-button 
            type="primary" 
            size="large" 
            @click="register();
            saving=true"
            :loading="saving" 
            class="mt-2 w-full bg-teal-700 font-bold
              rounded-full">Buat Akun</el-button>
        </div>
      </div>
    </div>
  </div>
</template>
  
<script>
  
export default {
  name: 'register',
  components:{
    
  },
  data() {
    return {
      saving: false,
      form:{
        id:-1,
        email:'',
      },
      dataId:-1,
      dataIdAkun:-1,
      formValue:{},
      optionsGuru:[],
    };
  },
  methods: {
    getInitial: async function() {
      this.saving = true;
      await this.$http.get('/data/guru/options',{
        params: {
          where:{
            'email': "",
          }
        }
      })
        .then(res => {
          this.optionsGuru = res.data
          this.form.email = useDataStore().filters.email
          this.saving = false
        })
      // await this.$http.get('/kolom/preparation?table=sch__guru&grouping=0&input=0')
      //   .then(result => {
      //     var res = result.data;
      //     let keys = Object.keys(res)
      //     for (let i = 0; i < keys.length; i++) {
      //       const element = res[keys[i]];
      //       if (element.required == '0')
      //         element.placeholder = element.label += ' (Opsional) '
      //     }
      //     this.fields = {...this.fields, ...res}
      //     console.log(this.fields)
      //     this.formKey++
      //     this.saving = false
      //   });

    },
    register(){
      let form = this.convertNullToEmptyString(this.form)
      var formData = window.jsonToFormData(form); 

      this.$http.post('data/guru/store', formData)
        .then(result => {
          this.saving = false;
          this.submittedAnggota(result.data)
        })
        .catch(err => {
          this.saving = false;
          console.log(err)
          var res = err.response;
          var code = res.status;
        });
    },
    submittedAnggota(data){
      console.log(data)
      let payload = {
        email: data.email,
      }
      // console.log(payload)
      useAuthStore().login(payload, true)
        .then(() => {
          this.$router.push({name:'default'})
        })
    },
  },
  updated(){
    // console.log(this.formValue, this.formAkun)
  },
  created() {
    this.getInitial();
  }
};
</script>
  
<template>
    <div id="register" class="max-w-[1100px] mx-6 sm:mx-auto bg-white bg-opacity-[0.9]
      border-solid border border-gray-300">
      <div class="h-full sm:px-10">
        <div class="flex flex-col items-center align-middle
           pt-[90px] px-5 pb-20">
          <div class="w-full
            text-center">
            <h2 class="mt-0 mb-0 text-center text-2xl
              font-[600] font-montserrat ">Buat Akun Baru</h2>
            <div class="mt-0 mb-0 text-center text-[12px]
            font-montserrat ">Silahkan Isi Data Diri Terlebih Dahulu</div>
            <el-divider class="my-4 mx-0"/>
            <div class="flex flex-col mt-6">
              <form-comp ref="formRegistrasi"
                class="[&_*]:text-center"
                :key="'form-registrasi-'+formKey"
                :fields="fields" 
                :id="dataId"
                v-model:form-value="formValue"
                href="data/anggota/store"
                href-get="data/anggota/get"
                :pass-columns="['tempat_lahir','tanggal_lahir','photo','role']"
                @saved="submittedAnggota"  
                @error="saving=false"
                size="large"
                :show-submit="false"
                :show-label="false"
                label-position="top"
                :show-required-text="false"
              ></form-comp> 
              <el-button 
                type="primary" 
                size="large" 
                @click="$refs.formRegistrasi.submitForm();
                saving=true"
                :loading="saving" 
                class="mt-2 w-full bg-teal-700 font-bold
                  rounded-full">Buat Akun</el-button>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>
  
<script>
  import Form from '@/components/Form.vue'
  
export default {
  name: 'register',
  components:{
    'form-comp' : Form,
  },
  data() {
    return {
      saving: false,
      formKey:1,
      formKeyAkun:1,
      fields:{},
      dataId:-1,
      dataIdAkun:-1,
      formValue:{},
    };
  },
  methods: {
    getInitial: async function() {
      this.saving = true;
      await this.$http.get('/kolom/preparation?table=mu_anggota&grouping=0&input=0')
        .then(result => {
          var res = result.data;
          let keys = Object.keys(res)
          for (let i = 0; i < keys.length; i++) {
            const element = res[keys[i]];
            if (element.required == '0')
              element.placeholder += ' (Opsional) '
          }
          this.fields = res
          this.formKey++
          this.saving = false
        });
    },
     submittedAnggota(data){
      console.log(data)
      this.saving = false
      let payload = {
        no_hp: this.formValue.no_hp,
        password: this.formValue.password,
      }
      // console.log(payload)
      authStore.login(payload, true)
        .then(() => {
          this.$router.push({name:'dashboard'})
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
  
<template>
  <div id="auth" class="max-w-[1100px] mx-6 sm:mx-auto bg-white bg-opacity-[0.9]
    border-solid border border-gray-300">
    <div class="h-full sm:px-10 pb-12">
      <div class="flex flex-col items-center align-middle">
        <div class="p-5 pt-36 
          text-center">
          <img id="logo" :src="$baseUrl + 'assets/images/vector.png'" height="220px" 
            class="my-2 mt-0
              "/>
          <!-- Error message -->
          <h2 class="mt-0 mb-0 text-center text-[22px]
            font-[600] font-montserrat ">Masuk Sekarang</h2>
          <div class="mt-0 mb-0 text-center text-[12px]
          font-montserrat ">Masuk untuk memulai  aplikasi</div>
          <template v-if="errorLogin" >
            <el-divider class="m-0 mt-3"/>
            <p class="text-danger error-message my-1" v-html="errorMessage"></p>
          </template>
          <template v-else>
            <el-divider class="m-3 mx-0"/>
          </template>
          <div class="flex flex-col gap-y-3 mt-2">
            <el-input 
              size="large" 
              class="w-full"
              v-model="form.no_hp" 
              placeholder="Nomor HP / Email"
              @keypress.enter.native="onEnter">
              <template #prefix>
                <icons icon="mdi:phone" />
              </template>
            </el-input>
            <el-input 
              size="large" 
              v-model="form.password" 
              placeholder="Kata Sandi" 
              type="password" 
              show-password
              class="w-full in-password"
              @keypress.enter.native="onEnter">
              <template #prefix>
                <icons icon="material-symbols:lock-outline" />
              </template>
            </el-input>    
            <el-checkbox v-model="saveAuth"
              class="h-fit p-1 hidden sm:flex">Ingat Saya</el-checkbox>     
            <el-button 
              type="primary" 
              size="large" 
              @click="doLogin()"
              :loading="loading" 
              class="mt-1 w-full bg-teal-700 h-fit
                py-2 text-[14px] font-bold">Masuk</el-button>
            <div class="w-full text-center mt-2">
              Belum punya akun?
              <a @click="$router.push({name:'register'})" 
                class="p-0 w-auto pointer
                text-emerald-800 font-bold
                active:text-emerald-600">Buat Akun Baru</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  const authStore = useAuthStore()

export default {
  name: 'auth',
  data() {
    return {
      loading: false,
      errorLogin: false,
      errorMessage: 'Username atau password salah.',
      nextUrl: null,
      prodiOptions: [],
      form: {
        no_hp: '',
        password: '',
      },
      saveAuth:true,
      authStore:authStore,
    };
  },
  methods: {
    onEnter(event) {
      console.log(event, this.form);
      if (!this.isEmpty(this.form.no_hp) && !this.isEmpty(this.form.password)) {
        this.doLogin();
      }
    },
    doLogin() {
      this.loading = true;
      console.log(this.authStore)
      this.authStore.login(this.form, this.saveAuth)
        .then(res => {
          this.loading = false;
          this.redirect();
        }).catch(err => {
          this.loading = false;
          const res = err.response;
          console.log(err)
          if (res.status == 401) {
            this.errorMessage = res.data.message;
            this.errorLogin = true;
          } else {
            this.$notify.error({
              title: 'Gagal',
              message: 'Terjadi kesalahan pada server',
              position: 'bottom-right'
            });
          }
        });
    },
    redirect(){
      this.authStore.checkUser();
      const loggedUser = this.authStore.loggedUser;
      if (loggedUser.role != '') {
        if (this.nextUrl) {
          this.$router.replace({ path: this.nextUrl });
        } else {
          this.$router.replace({ name: 'dashboard' });
        }
      }
    }
  },
  created() {
    // this.initial();
    this.nextUrl = this.$route.query.nextUrl;
    // this.redirect();
  }
};
</script>

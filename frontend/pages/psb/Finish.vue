<template>
    <div id="psb" class="flex flex-col">
      <div class="flex flex-col items-center align-middle bg-white bg-opacity-[0.8]
        h-full pt-16 mx-12 flex-1 text-center
        text-[20px]">
        <div class="mt-5 text-5xl font-[500] text-emerald-800">
          Selamat!
        </div>
        <div class="mt-4 text-xl font-[500] text-emerald-800">
          Anda sudah selesai memasukkan data pendaftaran calon santri<br/>
          Pondok Pesantren Muhammadiyah Darul Arqam Patean<br/>
          Tahun Ajaran 2024/2025
        </div>
        <div class="mt-4 text-xl font-[500] text-emerald-800">
          Silahkan melakukan pembayaran ke rekening dibawah :
        </div>
        <div class="mt-3 py-3 px-5 pb-5 bg-emerald-100 bg-opacity-50
          relative overflow-hidden">
          <div class="h-[500px] w-[50px] bg-gradient-to-r from-transparent via-white/[0.8] to-transparent
            absolute rotate-[30deg] -translate-y-1/2
            animate-[fly-in-absolute_4s_ease-in-out_infinite]
            [--from-left:150%] [--left:-150%]"></div>
          <div class="text-emerald-700 ml-2 flex items-end">
            <img :src="$baseUrl + 'assets/images/logo-bsi-pendek.png'" height="60px"
              class="mr-5 mb-2"/>
            <div class="flex flex-col items-start leading-[1]">
              <div class="text-3xl font-bold">7655999889</div>
              <div class="text-[20px]">a.n. Pondok Modern Darul Arqom</div>
            </div>
          </div>
          <div class="text-emerald-700 mt-3 font-bold">
            Konfirmasi ke WA (0838-3870-5818)
          </div>
        </div>
        <div class="mt-5 flex space-x-4">
          <el-button size="large"
            class="m-0 text-white font-bold rounded-[10px]
              bg-[linear-gradient(to_right,theme(colors.purple.500),theme(colors.green.500)_50%,theme(colors.red.500)_50%,theme(colors.orange.500))]
              bg-[length:200%_200%] bg-right-bottom hover:bg-left-top py-2 px-4
              transition-all duration-500 ease-in-out"
            @click="$router.push({name:'psb-start'})">
              Halaman Utama
          </el-button>
          <el-button size="large"
            class="m-0 text-white font-bold rounded-[10px]
              bg-[linear-gradient(to_right,theme(colors.cyan.500),theme(colors.emerald.500)_50%,theme(colors.amber.500)_50%,theme(colors.yellow.500))]
              bg-[length:200%_200%] bg-right-bottom hover:bg-left-top py-2 px-4
              transition-all duration-500 ease-in-out"
            @click="$router.push({name:'psb-view'});">
              Cek Data Pendaftaran
          </el-button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { mapGetters } from 'vuex';
  import Form from '@/components/Form.vue'
  
  export default {
    name: "psb",
    components: {
      'form-comp' : Form,
    },
    data: function() {
      return {
        
      };
    },
    watch: {
      
    },  
    computed: {
      ...mapGetters({
        user: 'loggedUser',
      }),
    },
    methods: {
      
    },
    created: function() {
  
    },
    mounted: function() {
    },
  }
  </script>
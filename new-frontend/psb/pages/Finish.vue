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
          Tahun Ajaran 2026/2027
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
   import { mapState } from 'pinia';
import { useAuthStore } from '@/config/stores/authStore'
  
  export default {
    name: "psb",
    components: {
    },
    data: function() {
      return {
        
      };
    },
    watch: {
      
    },  
    computed: {
      
      ...mapState(useAuthStore, {
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
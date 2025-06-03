<style lang="postcss" scoped>
  .button-menu {
    @apply
      text-white font-bold rounded-[10px] py-2 px-4 
      bg-[linear-gradient(to_right,theme(colors.purple.400),theme(colors.teal.500)_50%,theme(colors.red.500)_50%,theme(colors.yellow.500))]
      bg-[length:200%_200%] bg-left-bottom hover:bg-right-top 
      transition-all duration-500 ease-in-out
    !important;
  }
</style>
<template>
  <div id="psb" class="flex flex-col justify-center max-w-[1100px] mx-6 md:mx-auto">
    <div class="flex flex-col items-center align-middle bg-white bg-opacity-[0.8]
      h-full pt-48 pb-20 flex-1 px-5 md:px-0
      text-center text-[20px]">
      <div class="text-4xl md:text-5xl font-[500] text-emerald-800 leading-[1.3]">
        Pendaftaran Peserta Didik Baru
      </div>
      <div class="text-sm md:text-2xl font-[500] text-emerald-800
        mt-2 ">
        Tahun Ajaran 2025 / 2026
      </div>
      <div class="mt-8 text-[13px] md:text-[18px] text-emerald-800 leading-[1.8]
        px-0 md:px-12">
        Visi dan Misi Sekolah
      </div>
      <el-button size="large"
        class="m-0 mt-8 button-menu"
        @click="$router.push({name:'psb-create'});">
          Mulai Pendaftaran
      </el-button>
      <el-button size="large"
        class="m-0 mt-4 button-menu"
        @click="$router.push({name:'psb-view'});">
          Cek Data Pendaftaran
      </el-button>
      <el-button size="large"
        class="m-0 mt-4 button-menu"
        @click="$router.push({name:'psb-info'});">
          Info Pendaftaran
      </el-button>
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
}
</script>
<style lang="postcss">
    .el-collapse-item__header {
        @apply
            h-[50px] p-[30px]
        !important;
    }
</style>
<template>
    <div id="psb" class="flex flex-col">
      <div class="flex flex-col items-center align-middle bg-white bg-opacity-[0.8]
        h-full pt-16 mx-12 flex-1 text-center
        text-[20px]">
        <el-collapse class="w-full " v-model="active">
            <el-collapse-item  name="1" >
                <template #title>
                    <div class="text-xl font-semibold text-center">
                        <span>Brosur Pendaftaran Santri Baru 2024/2025</span>
                    </div>
                </template>
                <div class="overflow-x-auto">
                  <img :src="$baseUrl + 'assets/images/brosur1.jpg'" 
                    class="mt-3 max-w-[1100px] h-auto"/>
                </div>
                <div class="overflow-x-auto">
                  <img :src="$baseUrl + 'assets/images/brosur2.jpg'" 
                    class="mt-3 max-w-[1100px] h-auto"/>
                </div>
                
            </el-collapse-item>
            <el-collapse-item  name="2" >
                <template #title>
                    <div class="text-xl font-semibold text-center">
                        <span>Alur Pendaftaran</span>
                    </div>
                </template>
                <div class="overflow-x-auto">
                  <img :src="$baseUrl + 'assets/images/alur.png'" 
                  class="mt-3 max-w-[1000px] h-auto py-5"/>
                </div>
                
            </el-collapse-item>
        </el-collapse>
      </div>
      <div class="h-20 w-full"/>
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
        active:'2'
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
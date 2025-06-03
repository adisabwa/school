<template>
  <div id="bacaan-dzikir-sholat" class="pt-[55px] translate-y-[-10px] px-0">
    <el-card class="relative overflow-hidden
        bg-gradient-to-tr from-white/[0.8] from-40% to-emerald-100/[0.7] rounded-[10px]
      z-[0]
        font-montserrat
      mb-3 p-0" 
      header-class="relative px-4 pt-6 pb-2 text-[16px] font-bold text-left text-center"
      body-class="p-0">
      <template #header>
        <div @click="showList = true">Dzikir Ba'da Sholat</div>
        <img :src="dzikir.image" height="90px" width="90px"
            class="absolute z-[-1] top-[0px] left-[-15px]
              opacity-[0.5]"/>
      </template>
      <div class="max-h-[calc(100vh-250px)] overflow-y-auto py-3 px-4 ">
        <div
          class="bg-white rounded-[15px] 
            px-4 py-4 mb-4  text-left
            leading-[1.5]
            relative
            font-roboto text-[14px]">
          <div class="text-[16px] font-bold mb-2
            absolute w-full top-[16px] left-0 z-[3]">
            <icons v-show="dataKey > 0" icon="fe:arrow-left" 
                class="cursor-pointer text-[20px] text-emerald-700 ml-3 z-[2] float-left"
              @click="dataKey--;direction='left'"/>
            <icons v-show="dataKey < (datas.length - 1)" icon="fe:arrow-right" 
              class="cursor-pointer text-[20px] text-emerald-700 mr-3 z-[2] float-right"
              @click="dataKey++;direction='right'"/>
          </div>
          <transition name="fade"
            enter-active-class="animate"
            leave-active-class="animate"
            :enter-from-class="'absolute opacity-0 ' + (direction == 'right' ? 'translate-x-full' : ' -translate-x-full')"
            enter-to-class="opacity-100 translate-x-0"
            leave-from-class=" opacity-100 translate-x-0"
            :leave-to-class="'absolute opacity-0 ' + (direction == 'right' ? '-translate-x-full' : ' translate-x-full')">
              <div :key="dataKey">
              <div class="relative text-center text-[16px] font-bold mb-2 px-4">
                <div>{{ data.judul }}</div>
              </div>
              <el-divider class="my-3"></el-divider>
              <div class="">
                <div class="mt-1 font-noto
                  whitespace-pre-line text-right rtl font-arabic text-[22px] leading-loose">{{ data.arab }}</div>
              </div>
              <div class="mt-5">
                <div class="underline underline-offset-4 italic font-semibold">
                  Tulisan Latin :
                </div>
                <div class="mt-1
                  whitespace-pre-line text-md italic">{{ data.latin }}</div>
              </div>
              <div class="mt-5">
                <div class="underline underline-offset-4 italic font-semibold">
                  Artinya : 
                </div>
                <div class="mt-1
                  whitespace-pre-line">{{ data.terjemah }}</div>
              </div>
            </div>
          </transition>
        </div>
      </div>
      <div class="my-3 mx-auto w-fit text-center
        bg-teal-100 px-3 py-1 rounded-[10px]
        shadow-md shadow-teal-800/[0.2]">
        {{ dataKey + 1 }} / {{ datas.length }}
      </div>
    </el-card>
  </div>
</template>
  
  <script>
  import { mapState } from 'pinia';
  import { facilityMenu } from '@/helpers/menus.js'
  
  export default {
    name: "bacaan-dzikir-sholat",
    components: {
      
    },
    data: function() {
      return {
        formValue:{},
        loading: false,
        dataKey: 0,
        datas: [],
        dzikir: facilityMenu.bacaanDzikirSholat,
        showList: true,
        direction: 'right',
      };
    },
    watch: {
     
    },  
    computed: {
      ...mapState({
        user: 'loggedUser',
      }),    
      data : function() {
        return this.datas[this.dataKey] || {};
      },  
    },
    methods: {
      getInitial: async function() {
        this.loading = true;
        await this.$http.get('/data/bacaan/dzikir-sholat',{
            params: {
              limit:0 
            }
        })
          .then(result => {
            var res = result.data;
            this.datas = res;
          });
      },
    },
    created: function() {
      this.getInitial();
    },
    mounted: function() {
      
    },
  }
  </script>
  
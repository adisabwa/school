<template>
  <div id="bacaan-sholat" class="pt-[55px] translate-y-[-10px] px-0">
    <el-card class="relative overflow-hidden
        bg-gradient-to-tr from-white/[0.8] from-40% to-fuchsia-100/[0.7] rounded-[10px]
      z-[0]
        font-montserrat
      mb-3 p-0" 
      header-class="relative px-4 pt-6 pb-3 text-[18px] font-bold text-left text-center"
      body-class="p-0 pb-2">
      <template #header>
        <div @click="showList = true" class="text-fuchsia-900">
          Bacaan Sholat
        </div>
        <img :src="sholat.image" height="90px" width="90px"
            class="absolute z-[-1] top-[0px] right-[-15px]
              opacity-[0.5]"/>
        <icons icon="mdi:menu" @click="showList = true"
          class="absolute z-[1] top-[18px] left-[20px] text-[20px]
            text-fuchsia-700 bg-fuchsia-100 rounded-full p-2"/>
      </template>
      <transition-group name="fade"
        enter-active-class="animate"
        leave-active-class="animate"
        enter-from-class="absolute opacity-0 -scale-[2]"
        enter-to-class="opacity-60 scale-100"
        leave-from-class="opacity-60 scale-100"
        leave-to-class="absolute opacity-0 scale-0">
        <div v-if="showList"
          class="grid grid-cols-2 gap-x-4 p-4">
          <template v-for="(item, key) in datas">
            <div class="relative text-center bg-fuchsia-50 text-fuchsia-800 rounded-[15px]  shadow-md
              mb-5 px-3 py-3
              cursor-pointer active:scale-90"
              @click="showList = false, dataKey = key">
              <div class="relative p-1 py-2 rounded-[15px]
                bg-white shadow-inner shadow-fuchsia-900/[0.3]">
                <img :src="$baseUrl + '/assets/images/icons/' + item.icon"
                  class="w-full"/>
              </div>
              <div class="mt-3 text-slate-700 font-semibold leading-[1.3]">{{ item.nama }}</div>
            </div>
          </template>
        </div>
        <div v-if="!showList">
          <div class="py-3 px-4 
              text-left
              leading-[1.5]
              relative">
            <div class="text-[16px] font-bold mb-2
              absolute w-full top-[28px] left-0 z-[3]
              flex justify-between">
              <icons icon="fe:arrow-left" 
                :class="[dataKey > 0 ? 'visible' : 'invisible',
                  'cursor-pointer text-[20px] text-fuchsia-700 z-[2] ml-6']"
                @click="dataKey--;direction='left'"/>
              <icons icon="fe:arrow-right" 
                :class="[dataKey < (datas.length - 1) ? 'visible' : 'invisible',
                  'cursor-pointer text-[20px] text-fuchsia-700 z-[2] mr-6']"
                @click="dataKey++;direction='right'"/>
            </div>
            <div class="bg-white py-2 px-2 rounded-[15px] relative">
              <transition name="fade"
                enter-active-class="animate"
                leave-active-class="animate"
                :enter-from-class="'absolute top-0 opacity-0 ' + (direction == 'right' ? 'translate-x-full' : ' -translate-x-full')"
                enter-to-class="top-0 opacity-100 translate-x-0"
                leave-from-class="top-0 opacity-100 translate-x-0"
                :leave-to-class="'absolute top-0 opacity-0 ' + (direction == 'right' ? '-translate-x-full' : ' translate-x-full')">
                <div :key="dataKey">
                  <div class="relative overflow-hidden">
                    <div class="relative text-center text-[16px] font-bold mt-2 mb-2">
                      <div class="px-8">{{ data.nama }}</div>
                    </div>
                    <el-divider class="my-3"></el-divider>
                    <img :src="$baseUrl + '/assets/images/icons/' + data.icon" 
                      class="absolute -right-8 w-[180px] z-[0]
                        opacity-80 -scale-x-100 bottom-3"/>
                    <div class="min-h-[40vh] max-h-[calc(100vh-330px)] overflow-y-auto
                      relative z-[2]">
                      <div class="">
                        <div class="mt-1 font-noto
                          whitespace-pre-line text-center rtl font-arabic text-[24px] leading-loose">
                          <markup class="bg-white/[0.8] px-3 py-[3px]">{{ data.arab }}</markup>
                        </div>
                      </div>
                      <div class="mt-5 ">
                        <div class="underline underline-offset-4 italic font-semibold">
                          Tulisan Latin :
                        </div>
                        <div class="mt-1
                          whitespace-pre-line text-md italic">
                          <markup class="bg-white/[0.8] px-3 py-[3px]">{{ data.latin }}</markup>
                        </div>
                      </div>
                      <div class="mt-5 ">
                        <div class="underline underline-offset-4 italic font-semibold">
                          Artinya : 
                        </div>
                        <div class="mt-1
                          whitespace-pre-line">
                          <markup class="bg-white/[0.8] px-3 py-[3px]">{{ data.terjemah }}</markup>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </transition>
            </div>
          </div>
          <div class="mb-3 mx-auto w-fit text-center
            bg-teal-100 px-3 py-1 rounded-[10px]
            shadow-md shadow-teal-800/[0.2]">
            {{ dataKey + 1 }} / {{ datas.length }}
          </div>
        </div>
      </transition-group>
    </el-card>
  </div>
</template>
  
  <script>
  import { mapState } from 'pinia';
  import { facilityMenu } from '@/helpers/menus.js'
import { data } from 'jquery';
  
  export default {
    name: "bacaan-sholat",
    components: {
      
    },
    data: function() {
      return {
        formValue:{},
        loading: false,
        dataKey: 0,
        datas: [],
        sholat: facilityMenu.bacaanSholat,
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
        await this.$http.get('/data/bacaan/sholat',{
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
  
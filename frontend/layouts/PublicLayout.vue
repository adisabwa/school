<template>
  <div id="main-layout" class="bg-white">
    <div class="animate fixed top-0 z-50 w-full">
      <el-header class="h-full w-full relative p-0 cursor-pointer"
        @click="$router.push({name:'default'})">
        <div class="add-play h-full w-full bg-[#11716d]
          animate in [--transY:-60px]"></div>
        <div class="add-play 
          md:h-[90px] h-[70px]
          w-full 
          bg-cover bg-bottom
          absolute z-[5] top-0
          animate in [--transY:-40px]"
          :style="`background-image:url('${$baseUrl}assets/images/top-2.png')`"></div>
        <div id="top" class="add-play bg-cover bg-bottom
          h-[100px] md:h-[150px] w-[300%] md:w-[150%] 
          absolute z-[-1]
          left-1/2 [--toTransX:-50%] [--transX:-50%]
          
          animate in md:[--transY:-75px] [--transY:-40px]"
          :style="`background-image:url('${$baseUrl}assets/images/top.png')`"></div>
        <img id="logo" :src="$baseUrl + 'assets/images/logo-color.png'"
          class="add-play animate zoom
            absolute top-0 md:top-[10px]  left-1/2 -translate-x-1/2 z-10
            md:h-[160px] h-[130px]"/>
        <img id="logo" :src="$baseUrl + 'assets/images/logo-kecil.png'"
          class="remove-play animate zoom play
            absolute top-[10px] left-1/2 -translate-x-1/2 z-10
            md:h-[100px] h-[80px]"/>
      </el-header>
    </div>
      
    <el-container class="w-full main-content-wrap ml-0">
      <el-main class="p-0 overflow-x-hidden
        min-h-[calc(100vh-70px)] mt-[50px] relative h-full
        flex flex-col"
        :style="{
          overflow:'unset',
        }">
        <div class="fixed w-screen h-screen left-0 top-0
          z-[0]
          bg-cover bg-no-repeat bg-left-center bg-fixed"
          :style="`background-image:url('${$baseUrl}assets/images/back-sketch.png');`">
        </div>
        <router-view v-slot="{ Component , route}" class="h-full flex-1 z-[1]" :key="$route.fullPath" >
          <transition name="slide-in" mode="out-in"
            enter-active-class="transition-all ease-in-out duration-500"
            leave-active-class="transition-all ease-in-out duration-500"
            :enter-from-class="route.meta.enterFromClass"
            :enter-to-class="route.meta.enterToClass"
            :leave-from-class="route.meta.leaveFromClass"
            :leave-to-class="route.meta.leaveToClass">
            <component :is="Component" :key="route.path" />
          </transition>
        </router-view>
      </el-main>
      <el-footer height="auto" class="h-[20px] px-0 z-[99999] relative">
        <div class="overflow-hidden h-[60px] w-screen
          absolute bottom-0 left-1/2 -translate-x-1/2 -translate-y-[20px]">
          <div id="bottom" class="bg-cover bg-top bg-repeat 
            h-full min-w-[600px] w-full"
            :style="`background-image:url('${$baseUrl}assets/images/bottom.png')`">
          </div>
        </div>
        <div class="text-[12px] text-center h-full bg-gray-100 flex items-center justify-center gap-2">
          &copy; 2023 by <a href="https://codev-app.my.id/" target="_blank" class="no-underline text-green-900"> Codev-App</a>
        </div>
      </el-footer>
    </el-container>
  </div>
</template>

<script>
import { mapState } from 'pinia';

export default {
  name: 'default-layout',
  components:{
  },
  data: function() {
    return {
      activeMenu: '',
      bigs:['default','psb-start','login','account','unauthorized', this.defaultRoute],
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
    setHeader(to, from){
      let To = this.bigs.includes(to);
      let From = this.bigs.includes(from);
      if (To == From) return ''
      else if (To) {
        this.addClass('.add-play','play');
        this.removeClass('.remove-play','play');
      } else if (From) {
        this.removeClass('.add-play','play');
        this.addClass('.remove-play','play');
      } 
    },
  },
  mounted(){
    this.setHeader(this.$route.name, null)
  },
  updated: function() {
    this.setHeader(this.$route.name, null)
  },
  beforeRouteLeave(to, from){
    this.setHeader(to.name, from.name)
  },
}
</script>

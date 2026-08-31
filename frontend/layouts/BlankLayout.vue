<template>
  <div id="main-layout" class="bg-white">
    <div class="fixed w-screen h-screen left-0 top-0
      z-[-1]
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
  </div>
</template>

<script>
import { mapState } from 'pinia';

export default {
  name: 'blank-layout',
  components:{
  },
  data: function() {
    return {
      
    };
  },
}
</script>

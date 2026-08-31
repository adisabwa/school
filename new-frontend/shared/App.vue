<template>
  <div id="app"  :style="layout == 'horizontal' || template != 'main' ? '' : '--menu-width:230px;'">
  <div class="p-10">
    <router-view></router-view>
  </div>
  <div class="fixed w-screen h-screen left-0 top-0
    -scale-x-100 z-[-1] bg-white">
      <div class="h-full w-full
      opacity-50
      bg-cover bg-no-repeat bg-left-center bg-fixed" 
      :style="`background-image:url('${$baseUrl}assets/images/back-sketch.png')`" />
  </div>
  </div>
</template>

<script>
  
  import { mapState } from 'pinia';
  import { useDataStore } from '@2/shared/config/stores/dataStore';

  export default {
    name: "app",
    components: {
    },
    computed:{
      ...mapState(useDataStore,{
        layout: 'layout',
        template: 'template',
      }),
    },

  }
</script>
<style scoped>
  #app {
    
    /* top: 0; */
    height: 100%;
    width: calc(100% - var(--menu-width));
    min-height: 100vh;
    overflow: hidden;
    margin-left: var(--menu-width);
  }
</style>

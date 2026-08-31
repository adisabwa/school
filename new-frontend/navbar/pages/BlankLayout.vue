<template>
  <div id="main-layout" class="bg-white">
    <div class="fixed w-screen h-screen left-0 top-0
      z-[-1]
      bg-cover bg-no-repeat bg-left-center bg-fixed"
      :style="`background-image:url('${$baseUrl}assets/images/back-sketch.png');`">
    </div>
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

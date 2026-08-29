<template>
  <div id="app"
    :style="{
      '--base-url': `${$baseUrl}`, 
    }">
    <router-view>
    </router-view>
  </div>
</template>

<script>
export default {
  name: 'App',
  setup(){
  },
  mounted(){
    console.log(getEl('body'), this.$theme)
    addClass('body', this.$theme)
  }
} 
</script>

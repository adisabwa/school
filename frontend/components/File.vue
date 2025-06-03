<template>
  <div class="file flex align-middle items-center space-x-3" @click="openLink(href)" >
    <div v-if="['jpg','png','jpeg'].includes(getFileType(href))">
      <img :src="href" class="h-[200px]" />
    </div>
    <div v-else>
      <img class="icon" height="15px" :src="$baseUrl + 'assets/images/pdf.png'"/>
      <span type="text" size="mini" class="text text-success" target="_blank">{{title}}</span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'form-error',
  props: {
    href: {
      type:String,
      default: ''
    },
    title: {
      type:String,
      default: ''
    }
  },
  
}
</script>

<style lang="sass" scoped>
.file
  margin: 0px
  font-size: 13px
  width: auto
  cursor: pointer
  .text
    margin: auto 0px
    margin-left: 10px
    text-align: left
  img
    height: 20px
</style>
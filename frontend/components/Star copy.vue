<template>
	<img :id="id" 
		:src="`${$baseUrl}assets/images/star.png`"
		:width="width"
		class="animate scale-0" 
		:style="{
			maskImage:`${$baseUrl}assets/images/star.png`,
		}"/>
</template>

<script>

export default {
  name: 'star',
  components:{
  },
  props: {
		width:{
			type:String,
			default:'20px'
		},
		id:{
			type:String,
			default:'star'
		}
  }
}
</script>

<style lang="scss" scoped>
</style>
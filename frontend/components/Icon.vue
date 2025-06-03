<template>
	<Icon :icon="iconName" />
</template>


<script>
import { Icon } from '@iconify/vue';

export default {
  name: 'get-icon',
  components:{
  	Icon,
  },
  props: {
  	iconName: {
  		type: String,
	  	default: '',
  	},
  }
}
</script>

<style lang="scss" scoped>
	.iconify {
		font-size: 18px;
		margin-right: 6px;
	}
</style>
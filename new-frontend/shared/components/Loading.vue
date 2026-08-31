<template>
	<div id="loading" class="bg-white shadow-md py-6">
		<div class="no-data text-center">
      <h3 class="my-3">{{ title }}</h3>
      <span><icons :icon="icon" class="text-[60px] animate-[spin_2s_linear_infinite]"/></span>
      <p class="my-1">{{ subtitle }}</p>
    </div>
	</div>
</template>

<script>
export default {
  name: 'loading',
  props: {
    title: {
      default: 'Mengambil data...'
    },
    subtitle: {
      default: 'Silahkan tunggu, sedang mengambil data'
    },
    icon: {
      type: String,
      default: 'ri:loader-2-line',
    }
  }
}
</script>

<style lang="scss" scoped>
	
</style>
<template>
	<div class="absolute w-full h-full z-[999] flex flex-col items-center justify-center
		bg-slate-800/[0.67]" 
		@click="$emit('click')">
		<img :src="$baseUrl + 'assets/images/ekstra/winner-tag.png'"
			class="max-h-[70vh] max-w-[70vw] "/>
		<div class="text-white text-3xl font-bold -translate-y-[10px] italic">{{ winnerName }}</div>
	</div>
	<confetti ref="confetti" :origin="{y:1}" :show-button="false"></confetti>
</template>

<script>
import confetti from '@/components/Confetti.vue';

export default {
	components: {
		confetti
	},
	emits:['click'],
	props: {
		winnerName: {
			type: String,
			default: 'Nama Pemenang',
		}
	},
	data(){
		return {
			intervalId:'',
		}
	},
	mounted() {
		this.intervalId = setInterval(() => {
			if (this.$refs?.confetti)
				this.$refs.confetti?.createConfetti()
			// console.log('confetti')
		}, 2000);
	},
	beforeDestroy() {
		clearInterval(this.intervalId)
	},
}
</script>
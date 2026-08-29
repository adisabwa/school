<template>
	<el-button v-if="showButton" @click="createConfetti" class="bg-blue-500 text-white py-2 px-4 rounded">Show Confetti</el-button>
</template>

<script>
import confetti from 'canvas-confetti';
export default {
	name: 'Confetti',
	data() {
		return {
			particles: []
		}
	},
	props: {
		showButton:{type:Boolean, default:true},
		particleCount: {
			type: Number,
			default: 100
		},
		spread: {
			type: Number,
			default: 70
		},
		origin: {
			type: Object,
			default: () => ({ x: 0.5, y: 0.5 })
		}
	},
	mounted() {
	},
	methods: {
		createConfetti() {
			confetti({
				particleCount: this.particleCount,
				spread: this.spread,
				origin: this.origin,
				startVelocity: this.$windowHeight / 11,
			});
		}
	}
}
</script>
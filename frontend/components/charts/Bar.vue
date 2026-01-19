<template>
	<div class="flex flex-col h-full">
		<div :class="[chartClass]">
			<Bar ref="bar" :data="statistic" :options="options" />
		</div>

		<div class="mt-3 grow-0 w-full flex flex-wrap gap-x-4 gap-y-2 font-montserrat justify-center
			h-fit max-h-[100px] overflow-auto scrollbar-thin scrollbar-thumb-rounded-full
			scrollbar-thumb-slate-300 scrollbar-track-slate-100">
			<div
				v-for="(item, i) in legendItems"
				:key="i"
				class="w-fit flex items-start gap-2 cursor-pointer text-[12px] break-words break-all"
				:style="{ opacity: item.hidden ? 0.5 : 1 }"
				@click="toggleDataset(item.datasetIndex)"
			>
				<span
					class="mt-1 w-[12px] h-[12px] shrink-0"
					:style="{ backgroundColor: item.fillStyle }"
				></span>
				{{ item.text }}
			</div>
		</div>
	</div>
</template>

<script setup>
import {
	Chart as ChartJS,
	CategoryScale,
	LinearScale,
	BarElement,
	Title,
	Tooltip,
	Legend
} from 'chart.js'
import { Bar } from 'vue-chartjs'

ChartJS.register(
	CategoryScale,
	LinearScale,
	BarElement,
	Title,
	Tooltip,
	Legend
)
</script>

<script>
export default {
	name: 'chart-bar',
	components: { Bar },

	props: {
		statistic: {
			type: [Array, Object],
			default: () => ({})
		},
		max: {
			type: [Number, String],
			default: 5
		},
		min: {
			type: [Number, String],
			default: -1
		},
		addOptions: {
			type: Object,
			default: () => ({})
		},
		chartClass: {
			type: String,
			default: 'h-[300px]'
		}
	},

	data() {
		return {
			legendItems: [],
			options: {
				responsive: true,
				maintainAspectRatio: false,
				layout: { padding: 0 },
				scales: {
					y: {
						suggestedMin: 0,
						suggestedMax: 5,
						ticks: {
							stepSize: 1,
							font: { size: 11 }
						}
					},
					x: {
						offset: true,
						ticks: {
							font: { size: 11 },
							callback(value) {
								return this.getLabelForValue(value).split(' ')
							}
						}
					}
				},
				plugins: {
					legend: { display: false },
					tooltip: {
						callbacks: {
							label: (ctx) => `${ctx.dataset.label}: ${ctx.raw}`
						},
						titleFont: { size: 11, family: 'montserrat' },
						footerFont: { size: 11, family: 'montserrat' }
					}
				}
			}
		}
	},

	computed: {
		chart() {
			return this.$refs.bar?.chart
		}
	},

	watch: {
		statistic: {
			handler() {
				const step = this.options?.scales?.y?.ticks?.stepSize || 1
				this.options.scales.y.suggestedMax = Number(this.max) + step
				this.options.scales.y.suggestedMin = Number(this.min) - step
				this.chart?.update()
				setTimeout(this.updateLegend, 50)
			},
			deep: true
		}
	},

	methods: {
		addingOptions() {
			this.traverse(this.addOptions, (path, value) => {
				this.setObjectValueByPath(this.options, path, value)
			})
		},
		updateLegend() {
			if (this.chart?.legend?.legendItems) {
				this.legendItems = this.chart.legend.legendItems
			}
		},
		toggleDataset(index) {
			const meta = this.chart.getDatasetMeta(index)
			meta.hidden = meta.hidden === null
				? !this.chart.data.datasets[index].hidden
				: null
			this.chart.update()
			this.updateLegend()
		}
	},

	created() {
		this.addingOptions()
	}
}
</script>

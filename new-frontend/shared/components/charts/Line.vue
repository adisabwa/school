<template>
	<div class="flex flex-col h-full">
		<div :class="[chartClass]">
			<Line ref="line" :data="statistic" :options="options" />
		</div>
		<div class="mt-3 grow-0 w-full
			flex flex-wrap gap-x-4 gap-y-2 font-montserrat justify-center
			h-fit max-h-[100px]
			overflow-auto scrollbar-thin scrollbar-thumb-rounded-full scrollbar-thumb-slate-300 scrollbar-track-slate-100">
			<div
				v-for="(item, i) in legendItems"
				:key="i"
				class="w-fit flex items-start gap-2 cursor-pointer text-[12px]
					break-words break-all"
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
	import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip,  Legend} from 'chart.js'
	import { Line } from 'vue-chartjs'
	ChartJS.register( CategoryScale, LinearScale, PointElement, LineElement, Title,  Tooltip, Legend)
</script>

<script>
export default {
	name: 'chart-line',
	components: {
		Line,
	},
	props: {
		statistic: {
			type:[Array, Object],
			default: {
			}
		},
		max:{
			type:[Number, String],
			default:5,
		},
		min:{
			type:[Number, String],
			default:-1,
		},
    addOptions:{
      type:Object,
      default:{}
    },
		chartClass:{
			type:String,
			default:'h-[300px]',
		},
	},
	watch: {
		statistic: {
			handler(newVal, oldVal){
        let size = this.coalesce[this.options?.scales?.y?.ticks?.stepSize, 1];
				this.options.scales.y.suggestedMax = parseInt(this.max) + (size)
        this.options.scales.y.suggestedMin = parseInt(this.min) - (size)
				this.chart.options = this.options;
				this.chart.update();
				setTimeout(this.updateLegend, 100) // wait for chart to render
			},
			deep: true,
		}
	},
	data: function() {
    return {
			legendItems: [],
			options:{
				responsive: true,
				maintainAspectRatio: false,
				layout: {
					padding: {
						left: 0,
						right: 0,
						top: 0,
						bottom: 0,
					},
				},
				scales: {
					y: {
						suggestedMin:-1,
						suggestedMax:5,
						ticks: {
							stepSize: 1,
							font: {
								size: 11
							}
						},
						title: {
							display: false,
							text: 'Judul X',
							color: '#11716d',
							font: {
								size: 11,
								weight: 'bold'
							},
							padding: {
								top: 0,
								bottom: 5
							},
						}
					},
					x: {
						// Apply offset to create padding at the start and end
						offset: true,
						ticks: {
							font: {
								size: 11
							},
							callback: function (value) {
								const label = this.getLabelForValue(value);
								return label.split(' ')
							}
						},
						title: {
							display: false,
							text: 'Judul Y',
							color: '#11716d',
							font: {
								size: 11,
								weight: 'bold'
							},
							padding: {
								top: 5,
								bottom: 0
							},
						}
					},
				},
				plugins: {
					legend: {
						display: false,
					},
					tooltip: {
						callbacks: {
							label: (tooltipItem) => {
								const label = tooltipItem.dataset.label || '';
								const value = tooltipItem.raw || 0;
								return `${label}: ${value}`;
							},
						},
						titleFont: {
							size: 11,
							family:'montserrat'
						},
						footerFont: {
							size: 11,
							family:'montserrat'
						},
					},
				}
			},
		}
	},
	computed: {
		chart() {
			return this.$refs.line.chart
		},
	},
	methods: {
    addingOptions(){
      this.traverse(this.addOptions, (path, value) => {
        // console.log(path, value)
        this.setObjectValueByPath(this.options, path, value)
      })
    },
		updateLegend() {
			console.log(this.chart?.legend?.legendItems)
			if (this.chart?.legend?.legendItems) {
					this.legendItems =  this.chart.legend.legendItems
			}
			this.chart.update();
		},
		toggleDataset(datasetIndex) {
			const meta = this.chart.getDatasetMeta(datasetIndex)
			meta.hidden = meta.hidden === null ? !this.chart.data.datasets[datasetIndex].hidden : null
			this.chart.update()
			this.updateLegend()
		}
	},
	created(){
		this.addingOptions()
	},
	mounted() {
	},
	
}
</script>
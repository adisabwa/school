<template>
	<div id="transaksi-list" class="pt-1 p-3" v-loading="loading">
		<form-comp :fields="filterFields" v-model:formValue="filter"
			:show-required-text="false" :show-submit="false"
			:label-position="$windowWidth.value < 400 ? 'top' : 'left'"
			class="mb-2 mt-1 bg-white/80"/>
		<table-data ref="tableData" :fields="fields" href="keuangan/admin/transaksi"
			:checked="true"  :pass-columns="['id_kas','id_metode']"
			v-model:formValue="formValue"
			@changedFormValue="changedFormValue"
			@onFormSaved="onFormSaved"
			labelClass="uppercase tracking-widest text-[90%] leading-[1.3]"
			:eachLabelClass="{
				keterangan:'font-bold',
			}"
			:eachDataClass="{keterangan:'font-bold leading-[1.3]',
				id_kategori:'py-2 px-3 rounded-xl leading-[1] bg-slate-100 text-slate-400 w-fit text-[12px] uppercase tracking-wide font-bold'
			}"
			:params="tableParams">
			<template #keterangan-inside="{scope}">
				<div class="text-[80%] text-slate-500">{{ scope.row.nama_kas }}</div>
			</template>
			<template #jenis-inside="{scope}">
				<span>{{ ucFirst(scope.row.jenis) }}</span>
			</template>
			<template #nominal_disetor-inside="{scope}">
				<div :class="[scope.row.jenis == 'pengeluaran' ? 'text-red-500' : 'text-[var(--color-main-700)]',
					'font-bold']">
					{{ scope.row.jenis == 'pengeluaran' ? '-' : '+'}} {{ toIDR(scope.row.nominal_disetor) }}
				</div>
			</template>
		</table-data>
	</div>
</template>
	
	<script>
		
		import { reactive } from 'vue';
		import { mapActions, mapState } from 'pinia';
		import { useAuthStore } from '@/config/stores/authStore'
	
	export default {
		name: "transaksi-list",
		setup(){
			return {
				toIDR, ucFirst,
			}
		},
		props:{
			type:'',
			showCreate:{
				type:Boolean,
				default: true,
			},
			showSearch:{
				type:Boolean,
				default: true,
			},
		},
		components: {
			
		},
		data: function() {
			return {
				loading:false,
				data:{},
				fields:[],
				kategoriOptions:[],
				posOptions:[],
				formValue:{},
				state: reactive({
					passColumns : [],
					showColumns : [],
				}),
				filter:{
					bulan:dateNow().substr(5, 2),
					tahun:parseInt(dateNow().substr(0, 4)),
				},
				filterFields:{
					bulan:{
						nama_kolom:'bulan',
						label:'Bulan',
						input:'select',
						options:monthList()
					},
					tahun:{
						nama_kolom:'tahun',
						label:'Tahun',
						input:'select',
						options: yearList(4)
					},
				}
			};
		},
		provide() {
			return {
				sharedState: this.state
			}
		},
		watch: {
			'formValue.jenis'(val){
				this.fields.id_kategori.options = this.kategoriOptions.filter(d => d.jenis == val)
				this.fields.id_pos.options = this.posOptions.filter(d => d.jenis == val)
			},
			'formValue.nominal_disetor'(val){
				this.formValue.nominal_alokasi = toNumber(val)
			}
		},
		computed: {
			...mapState(useAuthStore,{
				user:'loggedUser'
			}),
			tableParams() {
				let { startOfMonth, endOfMonth } = getStartAndEndOfMonth(this.filter.tahun + '-' + this.filter.bulan + '-01')

				return {
					where:{
						'tanggal>=': startOfMonth,
						'tanggal<=': endOfMonth,
						'jenis !=' : 'iuran',
					},
					offset: 0,
					limit: 0
				};
			}
		},
		methods: {
			onFormSaved(){
				this.fields.id_kategori.options = this.kategoriOptions
				this.fields.id_pos.options = this.posOptions
			},
			changedFormValue({ field, value, option}){
				
			},
			getInitial: async function() {
					this.loading = true;
					await this.$http.get('/kolom/preparation?table=' + this.$prefixTable + 'keu_transaksi&grouping=0&input=0')
						.then(result => {
							var res = result.data;
							this.fields = res
							// this.fields.id_kas.readonly = true
							this.fields.jenis.hide_content = true
							this.fields.nominal_disetor.hide_content = true
							this.fields.nominal_disetor.align = 'right'
							this.kategoriOptions = res.id_kategori.options
							this.posOptions = res.id_pos.options
							this.loading = false
							this.$nextTick(() => {
								this.$refs.tableData.getData()
							})
						});
				},
		},
		created: function() {
			this.getInitial();
		}
	}
	</script>
	
<template>
	<div id="iuran-list" class="pt-3 p-2" v-loading="loading">
		<form-comp :fields="filterFields" v-model:formValue="filter"
			:show-required-text="false" :show-submit="false"
			class="mb-2"/>
		<table-data ref="tableData" :fields="fields" href="keuangan/admin/data/iuran"
			:checked="true"  :show-columns="['nama_iuran','id_pos','nominal','tipe','sasaran']"
			:passColumnsInput="passColumnsInput"
			vertical-align="top"
			v-model:formValue="dataForm"
			:eachDataClass="{
				id_pos:'font-bold',
				nama_iuran:'font-bold',
				sasaran:'font-bold',
				tipe:'font-bold',
			}"
			@changedFormValue="changeData"
			:params="tableParams">
			<template #tipe-inside="{scope}">
				<div class="font-normal text-[90%] leading-[1.2]" v-if="scope.row.tipe == 'non-bulanan'">
					Tanggal Pembayaran : <br/>
					{{ dateIndo(scope.row.tanggal_mulai) }} - {{ dateIndo(scope.row.tanggal_selesai) }}
				</div>
			</template>
			<template #sasaran-inside="{scope}">
				<div class="font-normal text-[90%] leading-[1.2]">
					{{ pre?.[scope.row.sasaran]}} {{ runFunction({
						func: fields[showData(scope.row.sasaran)]?.function, 
						data: isEmpty(fields[showData(scope.row.sasaran)]?.view_kolom) ? scope.row[fields[showData(scope.row.sasaran)]?.nama_kolom] : scope.row[fields[showData(scope.row.sasaran)]?.view_kolom], 
						options: fields[showData(scope.row.sasaran)]?.options,
						defaultData: fields[showData(scope.row.sasaran)]?.defaultData,
					})  }}
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
		name: "iuran-list",
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
		setup() {
			return {
				isEmpty, runFunction, dateIndo, toIDR,
			}
		},
		data: function() {
			return {
				loading:false,
				data:{},
				fields:[],
				filter:{
					id_semester:'',
				},
				filterFields:{
					id_semester:{
						nama_kolom:'id_semester',
						label:'Semester',
						input:'select',
						options:[],
					}
				},
				dataForm:{},
				state: reactive({
					passColumns : [],
					showColumns : [],
				}),
				pre:{
					'angkatan':'Kelas ',
				}
			};
		},
		provide() {
			return {
				sharedState: this.state
			}
		},
		watch: {
			'filter.id_semester'(val){
				this.dataForm.id_semester = val
			}
		},
		computed: {
			...mapState(useAuthStore,{
				user:'loggedUser'
			}),
			passColumnsInput(){
				let cols = ['id_unit','angkatan','id_kelas','id_santri']
				switch (this.dataForm.sasaran) {
					case 'unit': cols = cols.filter(d => d != 'id_unit'); break;
					case 'angkatan': cols = cols.filter(d => d != 'angkatan'); break;
					case 'kelas': cols = cols.filter(d => d != 'id_kelas'); break;
					case 'santri': cols = cols.filter(d => d != 'id_santri'); break;
					default: cols = cols; break;
				}
				if (this.dataForm.tipe == 'bulanan')
					return [...cols,...['tanggal_mulai','tanggal_selesai']]
				
				return cols
			},
			tableParams() {
				return {
					where: {
						id_semester: this.filter.id_semester,
					},
					offset: 0,
					limit: 0
				};
			}
		},
		methods: {
			changeData({field, value, options}){
				if (field.nama_kolom == 'sasaran') {
					this.dataForm.id_unit = ''
					this.dataForm.angkatan = ''
					this.dataForm.id_kelas = ''
					this.dataForm.id_santri = ''

				}
			},
			showData(sasaran){
				switch (sasaran) {
					case 'unit': return 'id_unit'
					case 'angkatan': return 'angkatan'
					case 'kelas': return 'id_kelas'
					case 'santri': return 'id_santri'
					default: return ''
				}
			},
			getInitial: async function() {
					this.loading = true;
					await this.$http.get('/kolom/preparation?table=' + this.$prefixTable + 'keu_iuran&grouping=0&input=0')
						.then(result => {
							var res = result.data;
							this.fields = res
							this.fields.sasaran.min_width = '120px'
							this.fields.tipe.min_width = '120px'
							this.loading = false
							this.$nextTick(() => {
								this.$refs.tableData.getData()
							})
						});
				await this.$http.get('data/semester/options')
						.then(res => {
							this.filterFields.id_semester.options = res.data
							this.filter.id_semester = res.data[0].value
						})
			},
		},
		created: function() {
			this.getInitial();
		}
	}
	</script>
	
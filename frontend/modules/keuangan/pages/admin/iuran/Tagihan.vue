<template>
	<div id="kas-list" class="pt-1 p-3" v-loading="loading">
		<form-comp :fields="filterFields" v-model:formValue="filter"
			label-width="180px" :pass-columns="filter.status == '0' ? [] : ['id_kelas']"
			:show-required-text="false" :show-submit="false"
			:label-position="$windowWidth.value < 400 ? 'top' : 'left'"
			class="mb-2 bg-white/70 mt-2"/>
		<table-data ref="tableData" :fields="fields" href="keuangan/admin/iuran/tagihan/get_all_grouping"
			:checked="true" 
			:show-upload="false" :show-create="false" :show-dropdown="false"
			:pass-columns="filter.status == '-1' ? [] : ['id_kelas']"
			labelClass="uppercase tracking-widest text-[90%] leading-[1.2]"
			:eachLabelClass="{nama_kas:'font-bold'}"
			:eachDataClass="{nama_kas:'font-bold'}"
			:params="tableParams">
			<template #menu="{handleActionClick}">
				<el-button type="success" @click="generateTagihan">
					<icons icon="solar:refresh-linear"/> Generate Tagihan
				</el-button>
				<el-button type="success" @click="$router.replace({name:'keuangan-iuran-pembayaran-tagihan'})">
					<icons icon="mdi:money"/> Pembayaran Iuran
				</el-button>
			</template>
			<template #periode-inside="{scope}">
				{{ scope.row.periode ? monthIndo(scope.row.periode) : '' }}
			</template>
			<template #saldo-inside="{scope}">
				<div v-if="scope.row.saldo > 0" :class="['rounded-md uppercase px-2 py-0 font-bold w-fit text-[85%] mx-auto h-fit',
					'bg-cyan-200 text-cyan-800]']">
					{{ toIDR(scope.row.saldo) }}
				</div>
			</template>
			<template #jumlah_lunas-inside="{scope}">
				<div v-if="scope.row.jumlah_lunas > 0" :class="['rounded-md uppercase px-2 py-0 font-bold w-fit text-[85%] mx-auto h-fit cursor-pointer hover:scale-95',
					'bg-[var(--color-main-200)] text-[var(--color-main-800)]']"
					@click="getBill(scope.row.id_santri, '1')">
					{{ toIDR(scope.row.jumlah_lunas) }}
				</div>
			</template>
			<template #jumlah_tunggakan-inside="{scope}">
				<div v-if="scope.row.jumlah_tunggakan > 0" :class="['rounded-md uppercase px-2 py-0 font-bold w-fit text-[85%] mx-auto h-fit cursor-pointer hover:scale-95',
					'bg-orange-200 text-orange-800']"
					@click="getBill(scope.row.id_santri, '0')">
					{{ toIDR(scope.row.jumlah_tunggakan) }}
				</div>
			</template>
			<el-table-column width="70" align="center">
				<template #default="scope">
					<icons class="m-0 cursor-pointer text-[var(--color-main-700)]" icon="streamline-ultimate:cash-payment-sign-2-bold" 
						@click="$router.replace({name:'keuangan-iuran-pembayaran-tagihan',query:{id_santri:scope.row.id_santri}})"/> 
				</template>
			</el-table-column>
		</table-data>

		<el-dialog v-model="showDetail"
			append-to-body
			class="p-0"
			header-class="bg-slate-900 text-slate-100 p-3"
			body-class="p-3">
			<template #header>
				<spav class="uppercase font-bold">Detail Tagihan {{ titleDetail }}</spav>
			</template>
			<el-table :data="bills">
				<el-table-column label="Semester" >
					<template #default="scope">
						{{ ucFirst(scope.row.semester)}} {{ scope.row.tahun_ajaran }}
					</template>
				</el-table-column>
				<el-table-column label="Nama Iuran" class="font-bold">
					<template #default="scope">
						<div class="font-bold leading-[1.3]">{{ ucFirst(scope.row.nama_iuran)}}</div>
						<div class="text-[12px] leading-[1.3]">{{ scope.row.tipe == 'rutin' ? 'Bulanan' : 'Non-Bulanan'}}</div>
					</template>
				</el-table-column>
				<el-table-column label="Periode" class="font-bold">
					<template #default="scope">
						{{ scope.row.periode ? monthIndo(scope.row.periode) : '-'}}
					</template>
				</el-table-column>
				<el-table-column label="Nominal" class="">
					<template #default="scope">
						{{ toIDR(scope.row.nominal)}}
					</template>
				</el-table-column>
			</el-table>
		</el-dialog>
	</div>
</template>
	
	<script>
		
		import { reactive } from 'vue';
		import { mapActions, mapState } from 'pinia';
		import { useAuthStore } from '@/config/stores/authStore'
	
	export default {
		name: "kas-list",
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
				isEmpty, monthIndo, toIDR, runFunction, ucFirst, monthIndo,
			}
		},
		data: function() {
			return {
				loading:false,
				data:{},
				fields:{
					nama:{
						nama_kolom:'nama',
						label:'Nama Santri',
						sortable:'1',
						min_width:'200px',
					},
					kelas:{
						nama_kolom:'kelas',
						view_kolom:'kelas',
						label:'Kelas',
						sortable:'1',
						width:'80px',
						align:'center',
					},
					saldo:{
						nama_kolom:'saldo',
						label:'Saldo Tersimpan',
						hide_content:'1',
						align:'center',
						width:'150px',
					},
					jumlah_tunggakan:{
						nama_kolom:'jumlah_tunggakan',
						label:'Nominal Tunggakan',
						hide_content:'1',
						align:'center',
						width:'150px',
					},
					jumlah_lunas:{
						nama_kolom:'jumlah_lunas',
						label:'Nominal Lunas',
						hide_content:'1',
						align:'center',
						width:'150px',
					},
				},
				state: reactive({
					passColumns : [],
					showColumns : [],
				}),
				filter:{
					id_semester:'',
					status:'0',
					id_kelas:'',
				},
				filterFields:{
					id_semester:{
						nama_kolom:'id_semester',
						label:'Pembayaran Semester',
						input:'select',
						options:[],
					},
					status:{
						nama_kolom:'status',
						label:'Status',
						input:'radio',
						options:[
							{value:'0',label: 'Santri Aktif'},
							{value:'1',label: 'Santri Lulus'},
							{value:'-1',label: 'Santri Tidak Lulus'},
						],
					},
					id_kelas:{
						nama_kolom:'id_kelas',
						label:'Kelas',
						input:'select',
						options:[],
					}
				},
				showDetail:false,
				titleDetail:'',
				bills:[],
			};
		},
		provide() {
			return {
				sharedState: this.state
			}
		},
		watch: {
		 
			
		},
		computed: {
			...mapState(useAuthStore,{
				user:'loggedUser'
			}),
			tableParams() {
				return {
					where:{
						id_semester: this.filter.id_semester,
						// status:'0',
					},
					having:{
						'id_kelas': this.filter.status == '0' ? this.filter.id_kelas : null,
						'status_santri': this.filter.status,
					},
					order:['nama asc'],
					offset: 0,
					limit: 0
				};
			}
		},
		methods: {
			getInitial: async function() {
					let filter = useDataStore().filters
					console.log(filter)
					await this.$http.get('data/semester/options')
							.then(res => {
								this.filterFields.id_semester.options = res.data
								this.filter.id_semester = filter?.id_semester ?? res.data[0].value
							})
					await this.$http.get('data/kelas/options')
							.then(res => {
								this.filterFields.id_kelas.options = res.data
								this.filter.id_kelas = filter?.id_kelas ?? res.data[0].value
							})
					this.filter.status = filter?.status ?? '0'
					await this.$refs.tableData.getData()
			},
			getBill(id_santri, status = '0'){
				this.titleDetail = status == '1' ? 'Lunas' : 'Belum Lunas'
				this.$http.get('keuangan/admin/iuran/tagihan',{
					params:{
						where:{
							id_santri:id_santri,
							status:status,
						},
						order:[
							'id_semester, periode asc'
						]
					}
				})
				.then(res => {
					this.showDetail = true
					this.bills = res?.data ?? []
				})
			},
			generateTagihan(){
				this.$http.post('keuangan/admin/iuran/tagihan/generate',window.jsonToFormData({
						id_semester: this.filter.id_semester,
					}))
					.then(res => {
						this.$refs.tableData.getData()
						this.$notify.success({
							title:'Berhasil',
							message:'Tagihan berhasil dibuat',
							position:'bottom-top',
						})
					})
					.catch(err => {
						this.$notify.error({
							title:'Error',
							message:'Tagihan gagal dibuat',
							position:'bottom-top',
						})
					})
			}
		},
		mounted: function() {
			this.getInitial();
		},
    beforeRouteLeave(){
      console.log('leave')
			Object.keys(this.filter).forEach(key => {
				useDataStore().setFilter({key:key, val:this.filter[key]})
			})
    }
	}
</script>
	
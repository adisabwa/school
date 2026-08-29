<template>
	<div id="kas-list" class="pt-1" v-loading="loading">
		<table-data ref="tableData" :fields="fields" href="keuangan/admin/iuran/tagihan/get_all_grouping"
			:checked="true"  :pass-columns="[]"
			:show-upload="false" :show-create="false" :show-dropdown="false"
			labelClass="uppercase tracking-widest text-[90%] leading-[1.2]"
			:eachLabelClass="{nama_kas:'font-bold'}"
			:eachDataClass="{nama_kas:'font-bold'}"
			:params="tableParams">
			<template #periode-inside="{scope}">
				{{ scope.row.periode ? monthIndo(scope.row.periode) : '' }}
			</template>
			<template #jumlah_lunas-inside="{scope}">
				<div v-if="scope.row.jumlah_lunas > 0" :class="['rounded-md uppercase px-2 py-0 font-bold w-fit text-[85%] mx-auto h-fit',
					'bg-[var(--color-main-200)] text-[var(--color-main-800)]']">
					{{ toIDR(scope.row.jumlah_lunas) }}
				</div>
			</template>
			<template #jumlah_tunggakan-inside="{scope}">
				<div v-if="scope.row.jumlah_tunggakan > 0" :class="['rounded-md uppercase px-2 py-0 font-bold w-fit text-[85%] mx-auto h-fit',
					'bg-orange-200 text-orange-800']">
					{{ toIDR(scope.row.jumlah_tunggakan) }}
				</div>
			</template>
			<template #status-inside="{scope}">
				<div :class="['rounded-md uppercase px-2 py-0 font-bold w-fit text-[85%] mx-auto h-fit',
					scope.row.status == '1' ? 'bg-[var(--color-main-200)] text-[var(--color-main-800)]' : 'bg-orange-200 text-orange-700']">
					{{ scope.row.status == '1' ? 'Sudah Dibayar' : 'Belum Dibayar' }}
				</div>
			</template>
			<el-table-column align="center">
				<template #header>
					<div class="uppercase tracking-widest text-[90%] leading-[1.2]">Saldo</div>
				</template>
				<template #default="scope">
					<div v-if="(scope.row.total_pem - scope.row.jumlah_lunas) > 0" 
						:class="['rounded-md uppercase px-2 py-0 font-bold w-fit text-[85%] mx-auto h-fit bg-sky-200 text-sky-700']">
						{{ toIDR(scope.row.total_pem - scope.row.jumlah_lunas) }}
					</div>
				</template>
			</el-table-column>
		</table-data>
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
				toIDR, monthIndo,
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
					jumlah_lunas:{
						nama_kolom:'jumlah_lunas',
						label:'Nominal Terbayar',
						hide_content:'1',
						align:'center',
					},
					jumlah_tunggakan:{
						nama_kolom:'jumlah_tunggakan',
						label:'Nominal Tunggakan',
						hide_content:'1',
						align:'center',
					},
				},
				state: reactive({
					passColumns : [],
					showColumns : [],
				})
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
					offset: 0,
					limit: 0
				};
			}
		},
		methods: {
			getInitial: async function() {
					this.$refs.tableData.getData()
			},
			generateTagihan(){
				this.$http.post('keuangan/admin/iuran/tagihan/generate')
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
		}
	}
</script>
	
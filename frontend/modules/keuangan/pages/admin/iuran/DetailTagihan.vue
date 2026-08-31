<template>
	<div id="kas-list" class="pt-1" v-loading="loading">
		<table-data ref="tableData" :fields="fields" href="keuangan/admin/iuran/tagihan"
			:checked="true"  :pass-columns="[]"
			:show-upload="false" :show-create="false" :show-dropdown="true"
			labelClass="uppercase tracking-widest text-[90%] leading-[1.2]"
			:eachLabelClass="{nama_kas:'font-bold'}"
			:eachDataClass="{nama_kas:'font-bold'}"
			:params="tableParams">
			<template #menu="{handleActionClick}">
				<el-button type="success" @click="generateTagihan">
					<icons icon="solar:refresh-linear"/> Generate Tagihan
				</el-button>
			</template>
			<template #periode-inside="{scope}">
				{{ scope.row.periode ? monthIndo(scope.row.periode) : '' }}
			</template>
			<template #status-inside="{scope}">
				<div :class="['rounded-md uppercase px-2 py-0 font-bold w-fit text-[85%] mx-auto h-fit',
					scope.row.status == '1' ? 'bg-[var(--color-main-200)] text-[var(--color-main-800)]' : 'bg-orange-200 text-orange-700']">
					{{ scope.row.status == '1' ? 'Sudah Dibayar' : 'Belum Dibayar' }}
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
		components: {
			
		},
		data: function() {
			return {
				loading:false,
				data:{},
				fields:{
					id_santri:{
						nama_kolom:'id_santri',
						view_kolom:'nama',
						label:'Nama Santri',
						sortable:'1',
						min_width:'200px',
					},
					kelas:{
						nama_kolom:'kelas',
						label:'Kelas',
						sortable:'1',
						width:'80px',
					},
					id_iuran:{
						nama_kolom:'id_iuran',
						view_kolom:	'nama_iuran',
						label:'Nama Iuran',
						sortable:'1',
					},
					periode:{
						nama_kolom:'periode',
						label:'Periode',
						sortable:'1',
						hide_content:true,
					},
					nominal:{
						nama_kolom:'nominal',
						label:'Nominal',
						sortable:'1',
						function:'toIDR',
					},
					status:{
						nama_kolom:'status',
						label:'Status',
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
	
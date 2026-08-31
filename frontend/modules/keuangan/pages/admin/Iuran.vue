<template>
	<div id="kas-list" class="pt-1" v-loading="loading">
		<table-data ref="tableData" :fields="fields" href="keuangan/admin/iuran"
			:checked="true"  :pass-columns="[]"
			:show-upload="false" :show-create="false"
			labelClass="uppercase tracking-widest text-[90%]"
			:eachLabelClass="{nama_kas:'font-bold'}"
			:eachDataClass="{nama_kas:'font-bold'}"
			:params="tableParams">
			<template #menu="{handleActionClick}">
				<el-button type="success">
					<icons icon="mdi:money"/> Pembayaran Tagihan
				</el-button>
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
					nama:{
						nama_kolom:'nama',
						label:'Nama Santri',
						sortable:'1',
					},
					kelas:{
						nama_kolom:'kelas',
						label:'Kelas',
						sortable:'1',
					},
					nama_iuran:{
						nama_kolom:	'nama_iuran',
						label:'Nama Iuran',
						sortable:'1',
					},
					periode:{
						nama_kolom:'periode',
						label:'Periode',
						sortable:'1',
					},
					nominal:{
						nama_kolom:'nominal',
						label:'Nominal',
						sortable:'1',
					},
					status:{
						nama_kolom:'status',
						label:'Status',
						hide_content:'1',
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
					this.$nextTick(() => {
						this.$refs.tableData.getData()
					})
				},
		},
		created: function() {
			this.getInitial();
		}
	}
</script>
	
<template>
	<div id="kategori-list" class="pt-1" v-loading="loading">
		<table-data ref="tableData" :fields="fields" href="keuangan/admin/data/kategori"
			:checked="true"  :pass-columns="[]"
			labelClass="uppercase tracking-widest text-[90%]"
			:eachLabelClass="{nama_kategori:'font-bold'}"
			:eachDataClass="{nama_kategori:'font-bold'}"
			:params="tableParams">
			<template #jenis-inside="{ scope }">
				<div :class="['rounded-md uppercase px-2 py-1 font-bold w-fit',
					scope.row.jenis == 'pemasukan' ? 'bg-[var(--color-main-200)] text-[var(--color-main-800)]' : scope.row.jenis == 'pengeluaran' ? 'bg-red-200 text-red-700' : 'bg-slate-200 text-slate-700']">
					{{ scope.row.jenis }}
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
		name: "kategori-list",
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
					where: this.user.role === 'super-admin' ? {} : { bidang: this.user.bidang },
					offset: 0,
					limit: 0
				};
			}
		},
		methods: {
			getInitial: async function() {
					this.loading = true;
					await this.$http.get('/kolom/preparation?table=' + this.$prefixTable + 'keu_kategori&grouping=0&input=0')
						.then(result => {
							var res = result.data;
							this.fields = res
							this.fields.jenis.hide_content = true
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
	
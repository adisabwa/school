<template>
	<div id="kas-list" class="pt-1" v-loading="loading">
		<table-data ref="tableData" :fields="fields" href="keuangan/admin/data/kas"
			:checked="true"  :pass-columns="[]"
			labelClass="uppercase tracking-widest text-[90%]"
			:eachLabelClass="{nama_kas:'font-bold'}"
			:eachDataClass="{nama_kas:'font-bold'}"
			:params="tableParams">
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
					await this.$http.get('/kolom/preparation?table=' + this.$prefixTable + 'keu_kas&grouping=0&input=0')
						.then(result => {
							var res = result.data;
							this.fields = res
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
	
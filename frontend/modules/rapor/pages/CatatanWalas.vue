<template>
	<div id="nilai" class="py-2">
		<el-card class="bg-white/[0.7] mb-2">
			<form-comp ref="formFilter"
				:key="formKey"
				:fields="filterFields"
				size="default"
				label-position="left"
				:show-label="showLabel"
				class="max-sm:mt-4"
				form-class="m-0"
				form-item-class="mb-2"
				label-width="150px"
				v-model:form-value="filter"
				:pass-columns="[]"
				:show-submit="false"
				text-submit="Cari"
				error-submit-text="Tidak dapat mengambil data"
				:show-required-text="false"
				>
			</form-comp>
		</el-card>
		<el-card class="bg-white/[0.7]"
			body-class="p-0">
			<div :class="['animate px-3 pt-3 pb-2']">
				<div class="text-right">
					<el-button size="small" type="success" @click="saveRapor">
						<icons icon="mdi:check" /> Simpan Data
					</el-button>
				</div>
			</div>
			<div class="relative bg-white" v-loading="loading">
				<div v-if="dataNilai.length == 0"
					class="text-center text-gray-500 text-lg p-5">
					<icons icon="mdi:alert" class="text-[50px] mb-3" />
					<div class="text-[18px]">Tidak ada data nilai</div>
				</div>
				<table-freeze v-else
					ref="tableFreeze"
					:data="dataNilai">
					<template #header="{data}">
						<tr class="*:border *:border-solid *:border-slate-300">
							<th width="20px" class="fixed-col">No</th>
							<th class="fixed-col min-w-[160px]" width="300px">Nama</th>
							<th align="center">Catatan Wali kelas</th>
							<th align="center">Korikuler</th>
						</tr>
					</template>
					<template #body="{data}">
						<tr v-for="(d, key) in data"
							class="*:border *:border-solid *:border-slate-300">
							<td>{{ key + 1 }}</td>
							<td>{{ d.nama }}</td>
							<td>
								<el-input type="textarea" rows="3" 
									v-model="d.catatan"
									placeholder="Masukkan catatan wali kelas ... "/>
							</td>
							<td>
								<el-input type="textarea" rows="3" 
									v-model="d.korikuler"
									placeholder="Masukkan catatan korikulen ... "/>
							</td>
						</tr>
					</template>
				</table-freeze>
			</div>
		</el-card>
	</div>
</template>
	
<script>
import { mapState } from 'pinia';
import { useAuthStore } from '@/config/stores/authStore'
import { useDataStore } from '@/config/stores/dataStore'
	
export default {
	name: "mapel",
	components: {
		
	},
	data: function() {
		return {
			showDownload:false,
			generating:true,
			progress:0,
			downloadStatus:'',
			files:[],
			loading: false,
			saving: false,
			filterFields: {
				id_semester:{
					label:'Semester',
					nama_kolom:'id_semester',
					input:'select',
					options:[],
				},
				id_kelas:{
					label:'Kelas',
					nama_kolom:'id_kelas',
					input:'select',
					input_only:'1',
					options:[],
				},
			},
			filter:{
				id_semester:'',
				id_kelas:'',
			},
			params:{
				where:[],
			},
			editId:-1,
			ids:[],
			formKey:0,
			dataNilai:[],
			scrollY:0,
			showHidden: 286,
			PembagianMapel:{},
			// role:'guru',
		};
	},
	watch: {
		'filter.id_semester' (val){
			if (!isEmpty(val))
				this.getData()
		},
		'filter.id_kelas' (val){
			if (!isEmpty(val))
				this.getData()
		},
	},  
	computed: {
		...mapState(useAuthStore, {
			user: 'loggedUser',
			role: 'role',
		}),
		...mapState(useDataStore, {
			storeFilters: 'filters',
		}),
		showLabel(){
			console.log('window width', this.$windowWidth.value)
			return this.$windowWidth.value > 800
		},
		percentage(){
			if (this.files.length == 0) return 0
			let all = this.dataNilai.length
			return Math.round((this.files.length / all) * 100)
		}
	},
	methods: {
		getInitial: async function() {
				this.loading = true;
				this.$http.get('data/semester/options')
					.then(res => {
						this.loading = false;
						let options = res.data
						this.filterFields.id_semester.options = options
						this.filter.id_semester = this.storeFilters?.id_semester ? this.storeFilters?.id_semester : this.user.id_semester?? Object.values(options)[0]?.value
					})
				this.$http.get('data/kelas/options')
					.then(res => {
						this.loading = false;
						let options = res.data
						this.filterFields.id_kelas.options = options
						this.filterFields.id_kelas.readonly = this.role != 'admin'
						this.filter.id_kelas = this.storeFilters?.id_kelas ? this.storeFilters?.id_kelas : this.user.id_kelas?? Object.values(options)[0]?.value
					})
			},
		getData(){
			this.loading = true
			this.$http.get('rapor',{
				params: {
					where:{
						id_semester: this.filter.id_semester,
						id_kelas: this.filter.id_kelas
					}
				}
			}).then(result => {
				this.loading = false
				this.dataNilai = result.data
			})
		},
		saveRapor() {
			let form = []
			this.dataNilai.forEach(d => {
				// console.log(d)
				form.push({
					id:d.id,
					catatan: d.catatan,
					korikuler: d.korikuler,
				})
			})
			form = window.jsonToFormData(form)
			this.$http.post('rapor/store_many', form)
				.then(res => {
					let rapor = res.data
					this.$notify.success({
						title: 'Berhasil',
						message: 'Nilai berhasil disimpan',
						position: 'bottom-right'
					});
				})
				.catch(err => {
					console.log(err)
					this.$notify.error({
						title: 'Gagal',
						message: 'Nilai tidak berhasil disimpan',
						position: 'bottom-right'
					});
				})
		},
	},
	created: function() {
		this.getInitial()
		// console.log(this.$router);
	},
	mounted(){
	},
	beforeUnmount() {
		let dataStore = useDataStore()
		Object.entries(this.filter).forEach(([index, val]) =>
			dataStore.setFilter({
				key:index,
				val:val
			})
		)
		// console.log('change-filter', dataStore.filters)
	},
}
</script>
	
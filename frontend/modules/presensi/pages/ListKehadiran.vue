<template>
	<div class="py-6 md:px-10 text-center bg-white/[0.8] relative" >
		<div class="p-5 px-2 md:px-5 mx-auto space-y-3 md:space-y-6">
			<div
				class="bg-[var(--color-main-600)] relative
					p-5 lg:p-6 rounded-xl lg:rounded-2xl text-white shadow-lg flex flex-col lg:flex-row justify-between items-center 
					gap-3 lg:gap-6"
				>
				<!-- Info -->
				<div class="flex-1 text-center md:text-left">
					<div
						class="flex items-center gap-2 justify-center md:justify-start mb-2.5"
					>
						<span
							class="bg-white/20 px-3 py-1 rounded-full text-[12px] font-black uppercase tracking-wider"
						>
							Presensi Santri
						</span>
						<span class="text-[var(--color-main-100)] text-[14px] font-bold">
							Kelas {{ dataMengajar.kelas }}
						</span>
					</div>

					<h2
						class="font-black text-xl lg:text-3xl mb-1 leading-tight"
					>
						{{ dataMengajar.nama_mapel }}
					</h2>
					
					<div
						class="flex flex-col lg:flex-row items-center gap-x-2 gap-y-1 justify-center md:justify-start text-[var(--color-main-50)] text-md font-bold mb-2"
					>
						{{ dataMengajar.nama_guru }} 
						<span v-if="dataMengajar.id_pengganti > 0">
							(<span class="text-[var(--color-main-100)]">Diganti oleh {{ dataMengajar.nama_guru_pengganti }}</span>)
						</span>
					</div>
					<div
						class="flex items-center gap-2 justify-center md:justify-start text-[var(--color-main-50)] text-sm italic font-medium opacity-80"
					>
						"{{ dataMengajar.topik }}"
					</div>
				</div>

				<!-- Stats -->
				<div class="flex justify-center gap-3 md:gap-5 bg-black/10 backdrop-blur-md p-2 lg:p-5 rounded-2xl border border-white/10 w-full md:w-auto" >
					<div class="text-center px-3">
						<p class="text-[11px] opacity-60 uppercase font-black mb-1">
							Hadir
						</p>
						<p class="text-2xl font-black">{{ totalPresensi.hadir }}</p>
					</div>

					<div class="w-px bg-white/10 h-10 self-center"></div>

					<div class="text-center px-3">
						<p class="text-[11px] opacity-60 uppercase font-black mb-1">
							Sakit / Izin
						</p>
						<p class="text-2xl font-black">{{  totalPresensi.izin }}</p>
					</div>

					<div class="w-px bg-white/10 h-10 self-center"></div>

					<div class="text-center px-3">
						<p class="text-[11px] opacity-60 uppercase font-black mb-1">
							Target
						</p>
						<p class="text-2xl font-black">{{ totalPresensi.alfa }}</p>
					</div>
				</div>
			</div>

			<div
				class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4 lg:gap-5 pb-20"
			>
				<div
					v-for="student in students"
					:key="student.id"
					class="bg-white p-2 md:p-3 rounded-2xl border border-solid border-slate-200 shadow-sm flex flex-col justify-between hover:shadow-md transition-all group"
				>
					<!-- Header -->
					<div class="flex items-center gap-1 lg:gap-2 mb-1 md:mb-2">
						<div class="relative flex-shrink-0 ">
							<img v-if="!isEmpty(student.foto)"
								:src="student.foto"
								:alt="student.nama"
								class="w-11 h-11 lg:w-14 lg:h-14 rounded-xl border-2 border-slate-200 shadow-sm"
							/>
							<div v-else
								class="w-11 h-11 lg:w-14 lg:h-14 rounded-xl border-2 border-slate-200 shadow-sm
									flex items-center justify-center">
								<icons icon="qlementine-icons:user-16" class="m-0 w-full m-full text-[40px] text-slate-500" />
							</div>
							<div
								class="absolute -top-1 -right-1 w-3.5 h-3.5 lg:w-4.5 lg:h-4.5 rounded-full border-2 border-white shadow-sm"
								:class="statusLabels[student.kehadiran].activeBg"
							/>
						</div>

						<div class="text-left">
							<div
								class="text-base font-black text-slate-900  group-hover:text-[var(--color-main-700)] transition-colors leading-tight"
							>
								{{ student.nama }}
							</div>
							<div
								class="text-[11px] text-slate-400 font-bold tracking-wider uppercase"
							>
								No. STB <span class="text-slate-800">{{ student.stb }}</span>
							</div>
						</div>
					</div>

					<!-- Status Buttons -->
					<div class="flex gap-1 p-2 bg-slate-50 !rounded-xl">
						<el-button
							v-for="status in statusKeys"
							:key="status"
							@click="student.kehadiran = status"
							class="m-0 flex-1 !py-2.5 !rounded-xl text-[11px] font-black transition-all duration-200"
							:class="
								student.kehadiran === status
									? `${statusLabels[status].activeBg} text-white shadow-md shadow-[var(--color-main-100)]`
									: 'bg-slate-100 text-slate-400 hover:bg-slate-200'
							"
							text
						>
							{{ statusLabels[status].label }}
						</el-button>
					</div>
				</div>
				<el-button class="bg-slate-800 text-white py-3 px-6 rounded-xl h-fit
					absolute left-1/2 -translate-x-1/2 bottom-10" @click="savePresensi"
					v-fixed-to-position="'100vh - 120px'">
					<icons icon="mdi:check-circle" /> Simpan Presensi
				</el-button>
			</div>
		</div>
	</div>
</template>

<script>

export default {
  name: 'AttendanceList',
	setup(){
		return {
			runFunction, isEmpty,
		}
	},
  props: {
	
  },

  data() {
		return {
			statusLabels: {
				hadir: {
					label: 'H',
					activeBg: 'bg-[var(--color-main-600)]'
				},
				izin: {
					label: 'I',
					activeBg: 'bg-amber-500'
				},
				sakit: {
					label: 'S',
					activeBg: 'bg-blue-500'
				},
				alfa: {
					label: 'A',
					activeBg: 'bg-red-600'
				}
			},
			dataMengajar:{},
			students:[],	
		}
	},
  computed: {
		statusKeys() {
			return Object.keys(this.statusLabels)
		},
		totalPresensi(){
			let total = {
				hadir:0,
				izin:0,
				alfa:0,
			}
			for(let s of this.students){
				console.log(s)
				switch (s.kehadiran) {
					case 'hadir': total.hadir++; break;
					case 'alfa': total.alfa++; break;
					default: total.izin++;break;
				}
			}

			return total
		}
  },

  methods: {
		currentStatus(studentId) {
			return this.attendance[studentId] || 'hadir'
		},
		getData(){
			this.$http.get('presensi/mengajar/get',{
		params:{id:this.idMengajar,},
	  }).then(res => {
		this.dataMengajar = res.data
	  }).catch(err => {
		console.log(err)
	  })

			this.$http.get('presensi/santri',{
		params:{id_mengajar_kelas:this.idMengajar},
	  }).then(res => {
		this.students = res.data
	  }).catch(err => {
		console.log(err)
	  })
		},
		savePresensi(){
			let form = []
			this.students.forEach(d => {
				// console.log(d)
				form.push({
					id:d.id,
					id_mengajar_kelas: d.id_mengajar_kelas,
					id_santri: d.id_santri,
					kehadiran: d.kehadiran,
					alasan: d.alasan,
				})
			})
			form = window.jsonToFormData(form)
			this.$http.post('presensi/santri/store_many', form)
				.then(res => {
					this.$confirm('Presensi berhasil disimpan', 'Berhasil', {
						type: 'success',
						confirmButtonText: 'Konfirmasi',
						cancelButtonText: 'Isi ulang',
					})
					.then(res => {
							this.$router.replace({name:'presensi-finish'})
						})
					.catch(res => {
							
						})
				})
				.catch(err => {
					console.log(err)
					this.$notify.error({
						title: 'Gagal',
						message: 'Presensi tidak berhasil disimpan',
						position: 'bottom-right'
					});
				})
		}
  },
  mounted(){
		this.getData()
  },
	created(){
		let query = this.$route.query
		this.idMengajar = query?.id ?? -1
	}
}
</script>

<template>
  <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-x-3 gap-y-1 mb-2
    bg-white ">
    <div class="text-2xl p-4 rounded-2xl border border-slate-100 shadow-sm
    font-bold text-[var(--color-main-600)] ">
      <div class="flex items-center" v-if="editSesi">
        <icons icon="lucide:check" class="text-2xl mr-2 cursor-pointer" @click="editSesi = false"/> 
        <date-wheel-picker v-model:value="date" placeholder="Pilih Tanggal"/>
        <floating-select v-model:value="noSesi" :options="optionsSesi" placeholder="Pilih Sesi"/>
      </div>
      <div class="flex items-center" v-else>
        <icons icon="lucide:edit" class="text-2xl mr-2 cursor-pointer" @click="editSesi = true"/> {{ dateDayIndo(this.date) }} - Sesi {{ noSesi }}
      </div>
    </div>
    <div class="flex items-center gap-2 bg-white p-3 rounded-2xl border border-slate-100 shadow-sm">
      <div class="bg-[var(--color-main-50)] text-[var(--color-main-600)] px-3 py-1 rounded-lg text-sm font-black">
        {{ Object.keys(scannedClasses).length }} Kelas Aktif
      </div>
      <div class="bg-slate-100 text-slate-400 px-3 py-1 rounded-lg text-sm font-black">
        {{ allClass.length - Object.keys(scannedClasses).length }} Menunggu Scan
      </div>
    </div>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    <div v-for="cls in allClass" :key="cls.id_kelas" :class="`bg-white p-5 rounded-[2rem] border shadow-sm transition-all relative overflow-hidden group ${scannedClasses[cls.id_kelas] ? 'border-[var(--color-main-100)]' : 'border-slate-100 bg-slate-50/30'}`">
      <div class="flex justify-between items-start mb-3 relative z-10">
        <div :class="`px-4 py-2 rounded-xl flex items-center justify-center font-black text-xl shadow-inner ${scannedClasses[cls.id_kelas] ? 'bg-[var(--color-main-600)] text-white' : 'bg-white text-slate-500 border border-slate-100'}`">
          KELAS {{ cls.kelas }}
        </div>
        <div :class="`px-3 py-1 rounded-xl text-xs font-black uppercase tracking-widest ${scannedClasses[cls.id_kelas] ? 'bg-[var(--color-main-50)] text-[var(--color-main-600)]' : 'bg-amber-50 text-amber-600 animate-pulse'}`">
          {{ scannedClasses[cls.id_kelas] ? 'Guru Sudah Scan' : 'Menunggu Scan' }}
        </div>
      </div>

      <div class="space-y-2 relative z-10">

        <template v-if="scannedClasses[cls.id_kelas]">
          <div class="space-y-2 animate-in fade-in slide-in-from-bottom-2 duration-300">
            <div class="grid grid-cols-[30px_1fr] gap-1 gap-y-0 text-slate-600 items-center ">
              <icons icon="mdi:account-check" class="text-lg m-0 mx-auto" />
              <div class="text-base font-bold">{{ scannedClasses[cls.id_kelas].id_pengganti > 0 ? scannedClasses[cls.id_kelas].nama_guru_pengganti : scannedClasses[cls.id_kelas].nama_guru }}</div>
              <div v-if="scannedClasses[cls.id_kelas]?.id_pengganti > 0"
                class="text-sm font-bold col-start-2">Menggantikan {{ scannedClasses[cls.id_kelas].nama_guru }}</div>
            </div>

            <div class="bg-[var(--color-main-50)]/50 p-3 rounded-xl border border-[var(--color-main-100)]">
              <div class="text-xs font-black text-[var(--color-main-600)] uppercase tracking-widest mb-1">Materi Sedang Berlangsung</div>
              <div class="text-sm text-slate-700 font-bold  line-clamp-1">{{ scannedClasses[cls.id_kelas].nama_mapel }}</div>
              <div class="text-sm text-slate-700  italic line-clamp-1">"{{ scannedClasses[cls.id_kelas].topik }}"</div>
            </div>

            <div class="flex items-center justify-between text-xs font-black text-slate-400">
              <span class="flex items-center gap-1">
                <icons icon="mdi:clock-outline" />Scan pada {{ timeIndo(scannedClasses[cls.id_kelas].created_at) }}
                <span v-if="scannedClasses[cls.id_kelas].is_telat == '1'">Telat ({{ Math.round(scannedClasses[cls.id_kelas].waktu_telat / 60) }} menit)</span>
              </span>
              <span class="text-[var(--color-main-600)]">TERHUBUNG</span>
            </div>
          </div>
        </template>

        <template v-else>
          <div class="space-y-3"																																									>
            <div class="flex items-center gap-2 text-slate-400 italic">
              <icons icon="mdi:nfc" />
              <div class="text-sm font-bold">Belum ada aktivitas scan hari ini.</div>
            </div>

            <div class="bg-slate-100 p-3 rounded-xl border border-slate-200/50">
              <div class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Jadwal Seharusnya</div>
              <div class="text-sm text-slate-500 font-bold">{{ cls.nama_mapel }} ({{ cls.nama_guru }})</div>
            </div>

						<div class="flex flex-col gap-3">
							<el-button class="w-full py-3 rounded-xl text-xs font-black uppercase tracking-widest" plain
								@click="openLink('https://wa.me/'+ (cls.no_hp_guru.slice(0, 2) == '08' ? '62' + cls.no_hp_guru.slice(3) : cls.no_hp_guru))">
								Kirim Colekan (Ping)
							</el-button>
							<el-button class="w-full py-3 rounded-xl text-xs font-black uppercase tracking-widest m-0
								 " type="primary"
								@click="this.$router.push({name:'presensi-form', query:{id_kelas:cls.id_kelas, id_sesi:noSesi}})">
								Isi Presensi
							</el-button>
						</div>
          </div>
        </template>
      </div>

      <icons icon="mdi:radio-tower" class="absolute -right-10 -bottom-10 opacity-5 text-[var(--color-main-900)] text-[120px] pointer-events-none" />
    </div>
  </div>
</template>

<script>

export default {
	name:'dashboard-admin',
	components:{
	},
  props:{
    classes:{
      type:Array,
      default:() => []
    }
  },
	setup(){
		const { openLink } = useBrowserActions()

		return {
			dateDayIndo, openLink, timeIndo,
		}
	},
	data() {
		return {
			showPage:'dashboard',
			editSesi:false,
			scannedClasses:{},
			allClass:[],
			noSesi:'',
			optionsSesi:[],
			date:dateNow(),
			idSemester:'',
		}
	},
	watch:{
		date(val){
			this.getInitial()
		},
		idSemester(val){
			this.getSchedule()
		},
		noSesi(val){
			this.getSchedule()
		},
	},
  computed: {
  },
	methods:{
		async getInitial(){		
			try {
				// 1. Ambil Semester
				const resSem = await this.$http.get('data/semester/semester_now');
				this.idSemester = resSem.data?.id;

				// 2. Ambil Sesi
				const resSesi = await this.$http.get('data/sesi/sesi_now');
				this.noSesi = resSesi.data?.sesi || 91; // Default atau fallback
				const resSesiOptions = await this.$http.get('data/sesi');
				this.optionsSesi = resSesiOptions.data.filter(d => parseInt(d.sesi) != NaN ).map(d => {
					return {
						value:d.sesi,
						label:'Sesi ' + d.sesi
					}
				})

				await this.getSchedule()

			} catch (error) {
				console.error("Gagal memuat data monitoring:", error);
			}
		},
		async getSchedule(){		
			try {
				// 3. Ambil Jadwal Kelas
				const resJadwal = await this.$http.get('mapel/penjadwalan', {
					params: {
						where: {
							'{n}id_semester': this.idSemester,
							'{n}hari': dayIndo(this.date),
						},
						having:{
							'sesi_awal<=': this.noSesi,
							'sesi_akhir>=': this.noSesi,
						},
						order: ['kelas']
					}
				});
				this.allClass = resJadwal.data || [];

				// 4. Ambil Data Presensi
				const resPresensi = await this.$http.get('presensi/mengajar/get_all', {
					params: {
						where: {
							'{n}id_semester': this.idSemester,
							'{n}tanggal': this.date,
						},
						having:{
							'sesi_awal<=': this.noSesi,
							'sesi_akhir >=': this.noSesi,
						},
						order: ['id_kelas asc']
					}
				});

				let data = resPresensi.data || [];
				data = Object.values(data).sort((a,b) => a.kelas.localeCompare(b.kelas))
				let scanned = {}; // Gunakan Object temporary
				data.forEach(item => {
					scanned[item.id_kelas] = item;
				});
				this.scannedClasses = scanned; // Update sekaligus

			} catch (error) {
				console.error("Gagal memuat data monitoring:", error);
			}

		},
	},
	created(){
		this.getInitial()
	}
}
</script>
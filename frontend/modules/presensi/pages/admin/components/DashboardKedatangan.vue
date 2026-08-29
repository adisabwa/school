<template>
	<div class="bg-white p-5 rounded-[2rem] border border-slate-100 shadow-sm">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-x-3 gap-y-1 mb-2 w-full
      bg-white ">
      <div class="text-2xl p-4 rounded-2xl border border-slate-100 shadow-sm
      font-bold text-[var(--color-main-600)] w-full ">
        <div class="flex items-center" v-if="editSesi">
          <icons icon="lucide:check" class="text-2xl mr-2 cursor-pointer" @click="editSesi = false"/> 
          <date-wheel-picker v-model:value="date" placeholder="Pilih Tanggal" @close="editSesi = false"/>
        </div>
        <div class="flex items-center" v-else>
          <icons icon="lucide:edit" class="text-2xl mr-2 cursor-pointer" @click="editSesi = true"/> {{ dateDayIndo(this.date) }}
        </div>
      </div>
      <!-- <div class="flex items-center gap-2 bg-white p-3 rounded-2xl border border-slate-100 shadow-sm">
        <div class="bg-[var(--color-main-50)] text-[var(--color-main-600)] px-3 py-1 rounded-lg text-sm font-black">
          {{ Object.keys(daftarKedatangan).length }} Kelas Aktif
        </div>
        <div class="bg-slate-100 text-slate-400 px-3 py-1 rounded-lg text-sm font-black">
          {{ allClass.length - Object.keys(daftarKedatangan).length }} Menunggu Scan
        </div>
      </div> -->
    </div>
      
		<div class="grid grid-cols-3 gap-4">
      <div class="flex gap-x-4 items-center">
        <div class="w-12 h-12 flex items-center justify-center bg-blue-500/10 text-blue-500 rounded-xl">
          <icons icon="solar:user-check-bold" class="text-3xl" />
        </div>
        <div>
          <div class="text-sm text-slate-400 font-medium">Kedatangan Guru</div>
          <div class="text-2xl font-black text-slate-900">{{ totalGuruHadir }}</div>
        </div>
      </div>
			<div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
				<div class="text-xs font-black text-slate-600 uppercase tracking-widest mb-1">Kedatangan</div>
        <div class="flex gap-3">
          <div class="w-full">
            <div class="text-sm text-slate-400">Tepat Waktu</div>
            <div class="text-xl font-black text-[var(--color-main-600)]">{{ totalDatangTepat }}</div>
          </div>
          <div class="w-full">
            <div class="text-sm text-slate-400">Terlambat</div>
            <div class="text-xl font-black text-amber-600">{{ totalDatangTelat }}</div>
          </div>
        </div>
			</div>
			<div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
				<div class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Perpulangan</div>
        <div class="flex gap-3">
          <div class="w-full">
            <div class="text-sm text-slate-400">Tepat Waktu</div>
            <div class="text-xl font-black text-[var(--color-main-600)]">{{ totalPulangTepat }}</div>
          </div>
          <div class="w-full">
            <div class="text-sm text-slate-400">Terlambat</div>
            <div class="text-xl font-black text-purple-600">{{ totalPulangTelat }}</div>
          </div>
        </div>
			</div>
		</div>

    
		<div class="mt-4 pb-2">
			<div class="text-sm font-black text-slate-400 uppercase tracking-widest mb-2">Daftar Presensi Guru</div>
			<div class="space-y-2 max-h-[500px] overflow-y-auto pr-2 grid grid-cols-[repeat(auto-fit,_minmax(250px,_1fr))] gap-4">
				<div v-for="guru in daftarKedatangan" :key="guru.id_guru" class="p-2 rounded-lg bg-white border border-slate-100 shadow-sm">
					<div class="flex items-center justify-between gap-2">
						<div class="flex items-center gap-2">
							<div :class="['w-2 h-2 rounded-full', guru.telat_datang == '0' ? 'bg-[var(--color-main-500)]' : 'bg-amber-500']"></div>
							<div class="font-bold text-slate-700 text-sm">{{ guru.nama_lengkap }}</div>
						</div>
					</div>
					<div class="mt-2 text-xs font-semibold text-slate-500 flex justify-between items-start gap-2">
            <div class="w-full">
              <div class="text-[15px] font-bold mb-1">Datang: <strong class="text-slate-800">{{ guru.waktu_datang?.slice(0,5) || '-' }}</strong></div>
              <div :class="['text-xs font-bold px-2 py-1 rounded-md', guru.telat_datang == '0' ? 'bg-[var(--color-main-50)] text-[var(--color-main-600)]' : 'bg-amber-50 text-amber-600']">
                {{ guru.telat_datang == '0' ? 'Tepat Waktu' : 'Terlambat' }}
                <div v-if="guru.telat_datang == '1'" class="mt-1 text-xs text-amber-600 font-bold">
                  <span class="font-normal">{{ guru.jenis_telat_datang }}</span>
                </div>
              </div>
            </div>
            <div class="w-full">
              <div class="text-[15px] font-bold mb-1" v-if="guru.waktu_pulang">Pulang: <strong class="text-slate-800">{{ guru.waktu_pulang?.slice(0,5) }}</strong></div>
              <span v-else class="text-indigo-500 font-bold">Belum Pulang</span>
              <div :class="['text-xs font-bold px-2 py-1 rounded-md', guru.telat_pulang == '0' ? 'bg-[var(--color-main-50)] text-[var(--color-main-600)]' : 'bg-purple-50 text-purple-600']">
                {{ guru.telat_pulang == '0' ? 'Tepat Waktu' : 'Pulang Lebih Awal' }}
                <div v-if="guru.telat_pulang == '1'" class="mt-1 text-xs text-purple-600 font-bold">
                  <span class="font-normal">{{ guru.jenis_telat_pulang }}</span>
                </div>
              </div>
            </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	data() {
		return {
      daftarKedatangan: [],
      dataGuru:[],
      date:dateNow(),
      editSesi:false,
		}
	},
  setup(){
    return {
      dateDayIndo,
    }
  },
  watch:{
    date(val){
      this.getData()
    }
  },
	computed: {
		totalGuru() {
      return this.dataGuru.length
		},
		totalGuruHadir() {
			return this.daftarKedatangan.length
		},
		totalDatangTepat() {
			return Object.values(this.daftarKedatangan).reduce((count, cls) => {
				return count + (cls.telat_datang == '0'? 1 : 0);
			}, 0);
		},
		totalDatangTelat() {
			return Object.values(this.daftarKedatangan).reduce((count, cls) => {
				return count + (cls.telat_datang == '1' ? 1 : 0);
			}, 0);
		},
		totalPulangTepat() {
			return Object.values(this.daftarKedatangan).reduce((count, cls) => {
				return count + (cls.telat_pulang == '0'? 1 : 0);
			}, 0);
		},
		totalPulangTelat() {
			return Object.values(this.daftarKedatangan).reduce((count, cls) => {
				return count + (cls.telat_pulang == '1' ? 1 : 0);
			}, 0);
		}
	},
  mounted(){
    this.getData()
  },
	methods: {
		async getData(){
      this.$http.get('presensi/kedatangan',{
        params:{
          where:{
            'tanggal':this.date
          }
        }
      })
      .then(res => {
        this.daftarKedatangan = res.data
      })
      .catch(err => {
        console.log(err)
        this.$notify.error({
          title:'Gagal mengambil data',
          message:"Ada kesalahan pada server",
          position:'bottom-right',
        })
      })
    }
  }
};
</script>
		
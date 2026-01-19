<template>
  <div class="space-y-4 lg:space-y-6 animate-in fade-in duration-500 pt-4">
    <div class="flex flex-col xl:flex-row gap-4 lg:gap-6">
      <!-- Hero Card -->
      <div
        class="flex-1 bg-gradient-to-br from-slate-900 to-slate-800 p-6 md:p-8 lg:p-10 rounded-[1rem] lg:rounded-[2rem] text-white shadow-xl relative overflow-hidden flex flex-col justify-center"
      >
        <div class="relative z-10 max-w-xl">
          <h2 class="text-1xl md:text-2xl lg:text-3xl font-extrabold mb-2 lg:mb-3 leading-tight">
            Selamat Datang,<br />Ustadz {{ user.nama }}
          </h2>

          <div
            class="text-slate-300 text-sm lg:text-lg mb-6 lg:mb-10 leading-relaxed opacity-90 "
          >
            Sistem siap untuk pencatatan presensi hari ini. Silakan scan QR kelas
            atau pilih jadwal terdekat.
          </div>

          <el-button
            @click="$router.replace({name:'presensi-scanner'})"
            class="h-fit border-0 bg-emerald-500 hover:bg-emerald-400 text-white py-3.5 lg:py-3 px-6 lg:px-10 rounded-md lg:rounded-xl font-bold text-md lg:text-lg flex items-center justify-center gap-2 lg:gap-3 shadow-xl transition-all hover:scale-[1.02] active:scale-95 w-full sm:w-auto"
          >
            <icons icon="bitcoin-icons:scan-filled" class="w-6 h-6 lg:w-8 lg:h-8" />
            Mulai Presensi Baru
          </el-button>
        </div>

        <div
          class="absolute top-0 right-0 w-1/2 h-full opacity-5 pointer-events-none hidden md:block"
        >
          <icons icon="bitcoin-icons:scan-filled" class="w-[400px] absolute -right-20 -top-20" />
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-4">
      <!-- ONGOING CLASS -->
      <div
        class="bg-emerald-600 p-4 rounded-[1.3rem] text-white shadow-md relative overflow-hidden h-full flex flex-col justify-between group"
      >
        <div class="relative z-10">
          <div class="flex items-center gap-1.5 mb-3">
            <div class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
            <span class="text-[13px] font-black uppercase tracking-widest opacity-80">
              Sedang Berlangsung
            </span>
          </div>

          <template v-if="ongoingClass">
            <div class="space-y-0.5">
              <div class="rounded-md bg-slate-200 px-2 text-emerald-900 text-[13px] mb-1 font-semibold uppercase w-fit">
                Kelas {{ ongoingClass.kelas }}
              </div>
              <h3 class="text-xl font-black leading-tight mb-1">
                {{ ongoingClass.nama_mapel }}
              </h3>

              <div class="flex items-center gap-1 text-[12px] opacity-70 font-bold">
                <icons icon="mdi:clock" class="text-[13px] m-0"/>
                {{ ongoingClass.waktu_mulai.substring(0, 5) }} - {{ ongoingClass.waktu_selesai_akhir.substring(0, 5) }}
              </div>
            </div>
          </template>

          <template v-else>
            <div class="py-3 space-y-1">
              <p class="text-[13px] italic opacity-60 font-medium">
                Tidak ada jadwal KBM aktif saat ini.
              </p>
              <p class="text-[13px] opacity-40 uppercase font-black">
                Sesi Mandiri / Istirahat
              </p>
            </div>
          </template>
        </div>

        <icons icon="mynaui:book-open"
          class="text-[100px] absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform pointer-events-none"
        />
      </div>

      <!-- NEXT CLASS -->
      <div
        class="bg-white p-4 rounded-[1.3rem] border border-slate-100 shadow-sm h-full flex flex-col justify-between group hover:border-blue-200 transition-all"
      >
        <div>
          <div class="flex items-center gap-1.5 mb-3 text-blue-600">
            <icons icon="mdi:clock" class="text-[14px] m-0" />
            <span class="text-[13px] font-black uppercase tracking-widest">
              Jadwal Mendatang
            </span>
          </div>

          <template v-if="nextClass">
            <div class="flex items-end justify-between">
              <div class="space-y-0.5">
                <h3 class="text-lg font-black text-slate-800 mb-1">
                  {{ nextClass.nama_mapel }}
                </h3>
                <div
                  class="flex items-center gap-1.5 text-[11px] text-slate-400 font-bold uppercase tracking-wider"
                >
                  {{ nextClass.nama_guru }}
                  • {{ nextClass.waktu_mulai.substring(0, 5) }} WIB
                </div>
              </div>

              <div class="text-right shrink-0">
                <div class="text-2xl font-black text-blue-600 leading-[1] flex gap-x-2">
                  <template v-if="Object.values(getTime(nextClass.timeDiff, true)).length > 0">
                    <div v-for="(el, key) in Object.values(getTime(nextClass.timeDiff, true)).slice(0, 2)"
                      class="text-center leading-[1] flex flex-col gap-y-1">
                      <div class="flex gap-[3px]">
                        <div class="bg-slate-200 text-[18px] p-1 py-2" v-for="t in el.value.split('')">
                          {{ t }}
                        </div>
                      </div>
                      <span class="text-[40%] uppercase ">
                        {{ el.label }}
                      </span>
                    </div>
                  </template>
                </div>
              </div>
            </div>
          </template>

          <template v-else>
            <div class="py-3">
              <p class="text-[13px] italic text-slate-400 font-medium">
                Jadwal untuk hari ini telah selesai.
              </p>
              <div
                class="mt-1.5 text-[11px] font-black text-slate-500 uppercase tracking-widest"
              >
                Sampai Jumpa Besok
              </div>
            </div>
          </template>
        </div>
      </div>

      <!-- LAST SESSION -->
      <div
        class="bg-white p-4 rounded-[1.3rem] border border-slate-100 shadow-sm h-full flex flex-col justify-between group hover:border-slate-300 transition-all"
      >
        <div>
          <div class="flex items-center gap-1.5 mb-3 text-slate-400">
            <icons icon="mdi:history" class="m-0 text-[16px]"/>
            <span class="text-[13px] font-black uppercase tracking-widest">
              Sesi Terakhir Selesai
            </span>
          </div>

          <template v-if="lastClassSummary">
            <div class="space-y-0.5">
              <h3 class="text-[16px] font-black text-slate-800 line-clamp-1 mb-1">
                {{ lastClassSummary.nama_mapel }}
              </h3>
              <p class="text-[13px] text-slate-400 font-bold uppercase tracking-wider">
                {{ lastClassSummary.kelas }} • {{ dateDayIndo(lastClassSummary.tanggal) }}
              </p>
              <p v-if="lastClassSummary.id_pengganti > 0"
                class="text-[13px] text-slate-400 font-bold tracking-wider">
                Mengganti {{ lastClassSummary?.nama_guru }}
              </p>
            </div>

            <div class="flex gap-2 mt-2 pt-3 border-t border-slate-50 text-center leading-[1]">
              <div class="flex flex-col">
                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">
                  Hadir
                </span>
                <span class="text-[22px] font-black text-emerald-600">
                  {{ lastClassSummary.hadir }}
                </span>
              </div>

              <div class="flex flex-col">
                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">
                  Alfa
                </span>
                <span class="text-[22px] font-black text-red-600">
                  {{ lastClassSummary.alfa }}
                </span>
              </div>

              <div class="ml-auto self-center">
                <el-button
                  @click="$router.replace({name:'presensi-report'})"
                  class="w-7 h-7 border-0 bg-slate-50 text-slate-400 rounded-md flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all"
                >
                  <icons icon="fa:arrow-right" class="text-[15px] m-0" />
                </el-button>
              </div>
            </div>
          </template>
          <template v-else>
            <div class="py-3">
              <p class="text-[14px] italic text-slate-400 font-medium">
                Anda belum pernah mengajar
              </p>
            </div>
          </template>
        </div>
      </div>
    </div>

    <!-- Bottom Section -->
			<div class="space-y-4 lg:space-y-5 bg-white p-4">
				<div
					class="font-black text-base lg:text-xl text-slate-900 flex items-center gap-2.5"
				>
					<Calendar :size="18" class="text-emerald-600" />
					Jadwal Mengajar
				</div>

				<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-y-2 lg:gap-y-3 gap-x-3 lg:gap-x-5">
					<div
						v-for="(s, i) in schedules"
						:key="i"
						class="bg-white p-2 lg:p-3 rounded-md border border-slate-100 flex items-center justify-between hover:border-emerald-300 transition-all shadow-sm"
					>
						<div class="flex items-center gap-2 lg:gap-3">
							<!-- Time -->
							<div
								class="w-9 h-9 lg:w-10 lg:h-10 bg-emerald-50 text-emerald-700 rounded-xl flex flex-col items-center justify-center"
							>
								<div
									class="text-[18px] font-black  leading-none"
								>
									{{ s.kelas }}
								</div>
							</div>

							<!-- Info -->
							<div>
								<div class="font-bold text-slate-800 text-[16px]">
									{{ s.nama_mapel }}
								</div>
								<div class="text-[13px] text-slate-500 font-medium">
									{{ ucFirst(s.hari) }} | {{ s.waktu_mulai.substring(0, 5) }} - {{ s.waktu_selesai_akhir.substring(0, 5) }}
								</div>
							</div>
						</div>

						<!-- Class Badge -->
						<!-- <div 
							class="bg-slate-50 text-slate-700 px-2 py-1 rounded-md text-[13px] font-black tracking-wider gap-2
                flex items-center justify-center"
						>
              <template v-if="Object.values(getTime(s.timeDiff, true)).length > 0">
                <div v-for="(el, key) in Object.values(getTime(s.timeDiff, true)).slice(0, 2)"
                  class="text-center leading-[1]">
                  <div class="flex gap-[3px]">
                    <div class="bg-slate-200 text-[15px] p-1 py-2" v-for="t in el.value.split('')">
                      {{ t }}
                    </div>
                  </div>
                  <span class="text-[60%] uppercase ">
                    {{ el.label }}
                  </span>
                </div>
              </template>
              <template v-else-if="Object.values(getTime(s.timeDiffAkhir, true)).length > 0">
                <div class="bg-emerald-700 text-white text-[13px] px-2">
                  ONGOING
                </div>
              </template>
						</div> -->
					</div>
				</div>
			</div>
  </div>
</template>

<script>
import WeeklySummary from './components/WeeklySummary.vue'
import { mapState } from 'pinia';
import { useAuthStore } from '@/config/stores/authStore'
import { useDataStore } from '@/config/stores/dataStore'

export default {
  name: 'DashboardHome',

  components: {
    WeeklySummary
  },
	computed: {
		...mapState(useAuthStore, {
			user: 'loggedUser',
		}),
		...mapState(useDataStore, {
			datas: 'datas',
		}),
	},
  data() {
    return {
      idSemester:-1,
      idGuru:-1,
      schedules: [],
      intervalId:-1,
      ongoingClass:null,
      nextClass:null,
      lastClassSummary:null,
      currentDate: new Date(),
    }
  },

  methods: {
    async getSchedule(){
      this.idGuru = this.user.id ?? -1
      await this.$http.get('data/semester/semester_now')
        .then(res => this.idSemester = res.data?.id)
      await this.$http.get('mapel/penjadwalan', {
        params:{
          where:{
            '{n}id_semester':this.idSemester,
            '{n}id_guru':this.idGuru
          },
          order:['id_sesi']
        }
      }).then(res => {
        this.schedules = res.data 
        this.intervalId = setInterval(this.timeDiff, 1000)
      })
      await this.$http.get('presensi/mengajar/get_where', {
        params:{
          where:{
            '{n}id_semester':this.idSemester,
          },
          or:{
            '{n}id_guru':this.idGuru,
            '{n}id_pengganti':this.idGuru,
          },
          order:['tanggal desc','id_sesi desc']
        }
      }).then(res => {
        this.lastClassSummary = res.data
      })
    },
    timeDiff(){
      let now = new Date('2026-01-17 07:40');
      // let now = new Date()
      this.schedules.forEach(d => {
        d.timeDiff = parseInt((new Date(`${d.date} ${d.waktu_mulai}`) - now) / 1000)
        d.timeDiffAkhir = parseInt((new Date(`${d.date} ${d.waktu_selesai_akhir}`) - now) / 1000)
      })
      
      let d = this.schedules[0]
      let detailJadwal = this.getTime(d.timeDiff)
      if (d.timeDiff > 0) {
        if (detailJadwal.day == now.getDay())
          this.nextClass = d
      } else if (d.timeDiffAkhir > 0) {
        this.ongoingClass = d
        this.nextClass = this.schedules[1]
      } else {
        clearInterval(this.intervalId)
        this.getSchedule()
      }
    },
  },
  beforeDestroy() {
    clearInterval(this.intervalId)
  },
  created(){
    this.getSchedule()
  }
}
</script>

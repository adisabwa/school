<template>
  <div class="space-y-5 bg-white/[0.8] p-2 pb-6">

    <!-- HEADER -->
    <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-4">
      <div class="space-y-0.5">
        <h2 class="text-2xl lg:text-3xl font-black text-slate-900 tracking-tight mb-1">
          Laporan Presensi
        </h2>
        <p class="mt-0 text-sm text-slate-500 font-medium">
          Monitoring kehadiran & kedisiplinan santri.
        </p>
      </div>

      <div class="flex bg-white p-1 rounded-xl border border-slate-100 shadow-sm gap-x-2 *:m-0">
        <el-button
          v-for="p in periods"
          :key="p.id"
          @click="activePeriod = p.id"
          :class="[
            'px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all',
            activePeriod === p.id
              ? 'bg-emerald-600 text-white shadow-md shadow-emerald-100'
              : 'text-slate-400 hover:text-slate-600'
          ]"
        >
          {{ p.label }}
        </el-button>
      </div>
    </div>

    <!-- STATS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      <div class="lg:col-span-1 grid grid-cols-2 gap-3">

        <div class="bg-white p-4 rounded-[1.3rem] border shadow-sm">
          <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
            Kehadiran
          </p>
          <div class="text-2xl font-black text-slate-900 mt-1">
            {{ presenceRate }}%
          </div>
        </div>

        <div class="bg-white p-4 rounded-[1.3rem] border shadow-sm">
          <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
            Total Izin/Sakit
          </p>
          <div class="text-2xl font-black text-blue-600">
            {{ aggregatedStats.sakit + aggregatedStats.izin }}
          </div>
        </div>

        <div class="bg-white p-4 rounded-[1.3rem] border shadow-sm">
          <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
            Total Alfa
          </p>
          <div class="text-2xl font-black text-red-600">
            {{ aggregatedStats.alfa }}
          </div>
        </div>

        <div class="bg-emerald-600 p-4 rounded-[1.3rem] shadow-lg text-white relative">
          <p class="text-[9px] font-black uppercase tracking-widest opacity-60">
            Kualitas
          </p>
          <span class="text-lg font-black">Sangat Baik</span>
        </div>

      </div>

      <!-- CHART -->
      <div class="lg:col-span-2 bg-white p-4 lg:p-5 rounded-[1.8rem] border shadow-sm">
        <h4 class="font-black m-0 mb-2 text-sm">Tren Kehadiran</h4>
        <div class="flex items-end gap-3 h-36 px-1">
          <div
            v-for="item in chartData"
            :key="item.label"
            class="flex-1 flex flex-col items-center gap-2"
          >
            <div class="w-full h-28 bg-slate-50 rounded-lg relative overflow-hidden">
              <div
                class="absolute bottom-0 left-0 right-0 bg-emerald-500 rounded-t-md"
                :style="{ height: item.rate + '%' }"
              />
            </div>
            <span class="text-[9px] font-black text-slate-400 uppercase">
              {{ item.label }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- DAILY / PERIODIC -->
    <div class="space-y-4">
      <div v-if="activePeriod === 'HARIAN'" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div
          v-for="log in presensiMengajar"
          :key="log.id"
          @click="selectedReport = log"
          class="bg-white p-4 rounded-lg border shadow-sm cursor-pointer hover:shadow-md transition"
        >
          <div class="flex items-start justify-between mb-6">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all">
                <Clock size={24} />
              </div>
              <div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                  {{ dateDayIndo(log.date)}}</div>
                <h3 class="font-bold text-lg text-slate-800">
                  {{ log.nama_mapel }}
                </h3>
              </div>
            </div>
            <div class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest">
              KELAS {{ log.kelas }}
            </div>
          </div>
          <div class="bg-slate-50/50 p-3 rounded-2xl mb-2 border border-transparent group-hover:bg-white group-hover:border-slate-100 transition-all" >
            <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">
              Materi:
            </p>
            <p class="text-sm text-slate-600 italic font-medium line-clamp-1">
              "{{ log.topik }}"
            </p>
          </div>
            <div class="flex items-center justify-between leading-none">
              <div class="flex gap-4">
                <div class="flex flex-col">
                  <span class="text-[12px] font-black text-slate-300 uppercase">
                    Hadir
                  </span>
                  <span class="text-2xl font-black text-emerald-600">
                    {{ log.hadir }}
                  </span>
                </div>

                <div class="flex flex-col">
                  <span class="text-[12px] font-black text-slate-300 uppercase">
                    Alfa
                  </span>
                  <span class="text-2xl font-black text-red-600">
                    {{ log.alfa }}
                  </span>
                </div>
              </div>

              <el-button
                class="w-9 h-9 bg-slate-50 rounded-xl flex items-center justify-center
                      text-slate-300 group-hover:bg-emerald-600
                      group-hover:text-white transition-all"
              >
                <icons icon="mdi:arrow-right" class="m-0 text-[18px]"/>
              </el-button>
            </div>
        </div> 
      </div>

      <div v-else class="bg-white rounded-[2rem] p-8 text-center">
        <h3 class="text-xl font-black">
          Rekapitulasi {{ currentPeriodLabel }}
        </h3>
        <button
          @click="showPeriodicDetail = true"
          class="mt-4 bg-slate-900 text-white px-6 py-3 rounded-xl font-black text-sm"
        >
          Lihat Detail Santri
        </button>
      </div>
    </div>

    <!-- MODAL -->
    <div
      v-if="selectedReport"
      class="fixed inset-0 z-[70] flex items-center justify-center"
    >
      <div class="absolute inset-0 bg-black/60" @click="selectedReport = null" />
      <div class="bg-white max-w-4xl w-full rounded-[2rem] p-6 relative z-10">
        <h3 class="text-2xl font-black mb-2">
          {{ selectedReport.subject }}
        </h3>
        <p class="italic text-sm text-slate-500">
          "{{ selectedReport.topic }}"
        </p>
      </div>
    </div>

  </div>
</template>


<script>
import { mapState } from 'pinia';
import { useAuthStore } from '@/config/stores/authStore'

export default {
  name: 'ReportsView',

  components: {
    
  },

  data() {
    return {
      activePeriod: 'HARIAN',
      selectedReport: null,
      showPeriodicDetail: false,
      searchQuery: '',
      presensiMengajar:[],
      idSemester:-1,
      idGuru:-1,
    }
  },

  computed: {
		...mapState(useAuthStore, {
			user: 'loggedUser',
		}),
    periods() {
      return [
        { id: 'HARIAN', label: 'Harian' },
        { id: 'MINGGUAN', label: 'Mingguan' },
        { id: 'BULANAN', label: 'Bulanan' },
        { id: 'SEMESTER', label: 'Semester' }
      ]
    },

    aggregatedStats() {
      const m =
        this.activePeriod === 'HARIAN' ? 1 :
        this.activePeriod === 'MINGGUAN' ? 6 :
        this.activePeriod === 'BULANAN' ? 24 : 120

      const base = this.presensiMengajar[0] ?? {}
      return {
        hadir: base?.hadir * m,
        izin: base?.izin * m,
        sakit: base?.sakit * m,
        alfa: base?.alfa * m
      }
    },

    presenceRate() {
      const t = Object.values(this.aggregatedStats).reduce((a, b) => a + b, 0)
      return t ? Math.round((this.aggregatedStats.hadir / t) * 100) : 0
    },

    chartData() {
      return [
        { label: 'Sen', rate: 95 },
        { label: 'Sel', rate: 88 },
        { label: 'Rab', rate: 92 },
        { label: 'Kam', rate: 96 },
        { label: 'Jum', rate: 90 },
        { label: 'Sab', rate: 85 }
      ]
    },

    currentPeriodLabel() {
      return this.periods.find(p => p.id === this.activePeriod)?.label
    }
  },
  methods:{
    async getInitial(){
      this.idGuru = this.user.id ?? -1
      await this.$http.get('data/semester/semester_now')
        .then(res => this.idSemester = res.data?.id)
      await this.$http.get('presensi/mengajar/get_all', {
        params:{
          where:{
            'id_semester':this.idSemester,
            'id_guru':this.idGuru
          },
          order:['tanggal desc','id_sesi desc']
        }
      }).then(res => {
        this.presensiMengajar = res.data
      })
    }
  },
  mounted(){
    this.getInitial()
  }
}
</script>

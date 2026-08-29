<template>
  <div class="space-y-5 bg-white/[0.8] p-2 pb-6 pt-3 md:pt-2">

    <!-- HEADER -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
      <div class="space-y-0.5">
        <div class="text-2xl lg:text-3xl font-black text-slate-900 tracking-tight mb-1">
          {{ role === 'walas' ? 'Monitoring Kelas ' + user?.kelas : 'Laporan Akademik Pusat'}}
        </div>
        <div class="mt-0 text-sm text-slate-500 font-medium">
          {{ role === 'walas' ? 'Pantau kedisiplinan dan kehadiran santri di kelas binaan Anda.' : 'Analisis kehadiran santri di seluruh jenjang kelas.' }}
        </div>
      </div>

      <div class="flex bg-white p-1 rounded-xl border border-slate-100 shadow-sm gap-x-2 *:m-0
        overflow-x-scroll no-scrollbar">
        <el-button
          v-for="p in periods"
          :key="p.id"
          @click="activePeriod = p.id; scrollElement('#filter-report','#filter-report-'+p.id)"
          :class="[
            'px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all',
            activePeriod === p.id
              ? 'bg-[var(--color-main-600)] text-white shadow-md shadow-[var(--color-main-100)]'
              : 'text-slate-400 hover:text-slate-600'
          ]"
        >
          {{ p.label }}
        </el-button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 lg:grid-rows-[100px_1fr]  gap-6">
      <!-- STATS -->
      <div class="lg:col-span-3 grid grid-cols-2 lg:grid-cols-4 gap-3">

          <div class="bg-white p-4 rounded-[1.3rem] border shadow-sm">
            <div class="text-[11px] font-black text-slate-400 uppercase tracking-widest">
              Kehadiran
            </div>
            <div class="text-2xl font-black text-slate-900 mt-1">
              {{ presenceRate }}%
            </div>
          </div>

          <div class="bg-white p-4 rounded-[1.3rem] border shadow-sm">
            <div class="text-[11px] font-black text-slate-400 uppercase tracking-widest">
              Total Izin/Sakit (Jam)
            </div>
            <div class="text-2xl font-black text-blue-600">
              {{ aggregatedStats.sakit + aggregatedStats.izin }}
            </div>
          </div>

          <div class="bg-white p-4 rounded-[1.3rem] border shadow-sm">
            <div class="text-[11px] font-black text-slate-400 uppercase tracking-widest">
              Total Alfa (Jam)
            </div>
            <div class="text-2xl font-black text-red-600">
              {{ aggregatedStats.alfa }}
            </div>
          </div>

          <div class="bg-[var(--color-main-600)] p-4 rounded-[1.3rem] shadow-lg text-white relative">
            <div class="text-[11px] font-black uppercase tracking-widest opacity-60">
              Kualitas
            </div>
            <span class="text-lg font-black">
              {{ getRank(presenceRate) }}
            </span>
          </div>

      </div>

      
      <!-- CHART -->
      <div class="lg:col-span-2 row-span-2 relative flex flex-col w-full bg-white p-4 lg:p-5 rounded-[1.8rem] border shadow-sm">
        <floating-select v-if="activePeriod == 'SEMESTER'" v-model:value="idSemester" :options="optionsSemester"
          class="mb-3"/>
        <div v-else
          class="flex items-center mb-3 ">
          <date-wheel-picker
            size="large" v-model:value="dateFrom" placeholder="Pilih Tanggal"
            ref="dateSelect" class="w-full" :day-locked="activePeriod == 'BULANAN'"
            @change="getData">
          </date-wheel-picker>
          <template v-if="activePeriod !== 'HARIAN'">
            <span class="text-[12px] mx-2">Sampai</span>
            <date-wheel-picker readonly v-model:value="dateEnd" class="w-full h-full" size="large"
              prefix-icon=""/>
          </template> 
        </div>
        <div class="font-bold text-xl mb-1">Statistik Kehadiran Santri</div>
        <div v-if="!isEmpty(statistic.datasets)" class="overflow-hidden">
          <div>
            <div v-for="(d, key) in statistic.datasets[0].data">
              <div class="flex justify-between text-[12px] font-bold mt-2 px-2 text-slate-700">
                <span>{{ statistic.labels[key] }}</span>
                <span>{{ d }} %</span>
              </div>
              <div class="w-full rounded-full bg-slate-100">
                <div class="h-[20px] rounded-full"
                  :style="{
                    width:d + '%',
                    backgroundColor: statistic.datasets[0].backgroundColor
                  }">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- SUMMARY ALL LESSON -->
      <div class="lg:col-span-3">
        <div class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3 gap-4">
          <div v-for="(sub,i) in summaryStudents" :key="i" @click="selectedReport=sub;showSummaryDetail=true;" class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm group hover:border-[var(--color-main-200)] hover:shadow-xl transition-all cursor-pointer overflow-hidden relative">
            <div class="flex gap-x-3 items-center w-full">
              <div class="flex flex-col w-full">
                <div class="font-black text-slate-800 text-xl relative z-10">{{ sub.nama }}</div>
              <div class="text-[13px] text-slate-400 font-bold uppercase tracking-widest mb-1 relative z-10 leading-[1.3]">STB : {{ sub.stb }}</div>
              </div>
              <el-button
                class="w-9 h-9 bg-slate-50 rounded-xl flex items-center justify-center
                  text-slate-300 group-hover:bg-[var(--color-main-600)]
                  group-hover:text-white" >
                <icons icon="mdi:arrow-right" class="m-0 text-[18px]"/>
              </el-button>
            </div>
            <div class="flex pt-5 border-t border-slate-50 relative z-10">
              <div class="w-full flex gap-x-5">
                <div class="w-fit">
                  <div class="text-[12px] font-black text-slate-300 uppercase">Sesi</div><div class="text-2xl font-black text-slate-700">{{ sub.sesi }}</div>
                </div>
                <div class="w-fit">
                  <div class="text-[12px] font-black text-slate-300 uppercase">Jam</div><div class="text-2xl font-black text-slate-700">{{ sub.total_jam }}</div>
                </div>
                <div class="w-fit">
                  <div class="text-[12px] font-black text-orange-300 uppercase">S / I</div><div class="text-2xl font-black text-orange-700">{{ parseInt(sub.total_izin) + parseInt(sub.total_sakit) }}</div>
                </div>
                <div class="w-fit">
                  <div class="text-[12px] font-black text-red-300 uppercase">Alfa</div><div class="text-2xl font-black text-red-700">{{ sub.total_alfa }}</div>
                </div>
              </div>
              <div class="text-right">
                <div class="text-[12px] font-black text-[var(--color-main-400)] uppercase">Hadir</div>
                <div class="text-2xl font-black text-[var(--color-main-600)]">{{ sub.presentase_hadir}}&nbsp;%</div>
              </div>
            </div>
            <div class="absolute -left-3 -bottom-3 opacity-5 group-hover:opacity-10 transition-opacity">
              <icons icon="lucide:book-open" class="text-[80px]" />
            </div>
          </div>
        </div>
        <div class="flex items-center justify-center flex-col
          text-[var(--color-main-700)] font-bold h-[100px]"
            v-if="isEmpty(summaryMengajar)">
          <icons icon="ph:empty" class="m-0 text-[45px]"/>
          <div class="text-[20px]">Tidak ada data</div>
        </div>
      </div>
    </div>

    

    <!-- DETAIL RANGKUMAN -->
    <el-dialog v-model="showSummaryDetail"
      append-to-body
      class="p-0 rounded-3xl shadow-2xl mt-10 overflow-hidden
        min-w-[300px] max-w-[90vw] !m-0 mx-auto"
      header-class="*:text-white p-0">
      <template #header>
        <div class="bg-slate-900 p-5 lg:p-7 pb-3 lg:pb-4 text-white relative">
          <div class="flex items-center gap-2 text-[var(--color-main-400)] font-black text-md uppercase tracking-widest mb-2 lg:mb-4">
            <icons icon="mdi:calendar" class="m-0 text-[16px] shrink-0" /> 
            <span class="leading-[1.2]">{{ dateDayIndo(dateFrom) }} <span v-if="dateFrom != dateEnd">- {{ dateDayIndo(dateEnd) }}</span></span>
          </div>

          <div class="text-2xl lg:text-3l font-black m-0 mb-1 leading-tight">{{ selectedReport?.nama }} • KELAS {{ selectedReport?.kelas }}</div>
        </div>
      </template>
      <div class="bg-white w-full max-w-5xl relative z-10 overflow-hidden flex flex-col max-h-[calc(100vh-200px)]">
        <div class="flex-1 overflow-y-auto overflow-x-auto px-2 pt-1 pb-3 max-h-[calc(100vh-300px)]">
          <div class="space-y-2">
            <!-- Table -->
            <table class="w-full text-left">
              <thead class="font-semibold uppercase">
                <tr class="bg-slate-100 text-slate-500">
                  <td rowspan="2" class="px-2 py-2 text-center" width="50">No.</td>
                  <td rowspan="2" class="px-2 py-2">Mata Pelajaran</td>
                  <td colspan="4" class="px-2 py-2 text-center">Kehadiran (Jam)</td>
                  <td rowspan="2" class="px-4 py-2 text-center " width="100">Presentase</td>
                </tr>
                <tr class="bg-slate-100 text-slate-500">
                  <td class="px-2 py-2 text-center w-[60px] text-[var(--color-main-600)]">Hadir</td>
                  <td class="px-2 py-2 text-center w-[60px] text-orange-600">Sakit</td>
                  <td class="px-2 py-2 text-center w-[60px] text-blue-600">Izin</td>
                  <td class="px-2 py-2 text-center w-[60px] text-red-600">Alfa</td>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50">
                <tr v-for="(s, idx) in selectedReport.detail_nama_mapel" :key="s.id" class="hover:bg-slate-50/30 transition-colors group">
                  <td class="px-2 py-2 text-xs font-bold text-slate-500 text-center">{{ idx + 1 }}</td>

                  <td class="px-2 py-2">
                    <div class="text-sm font-bold text-slate-800">{{ s }}</div>
                  </td>

                  <td class="px-2 py-2 text-center font-semibold text-lg text-[var(--color-main-700)]">{{ selectedReport.detail_hadir[idx] }}</td>
                  <td class="px-2 py-2 text-center font-semibold text-lg text-orange-700">{{ selectedReport.detail_sakit[idx] }}</td>
                  <td class="px-2 py-2 text-center font-semibold text-lg text-blue-700">{{ selectedReport.detail_izin[idx] }}</td>
                  <td class="px-2 py-2 text-center font-semibold text-lg text-red-700">{{ selectedReport.detail_alfa[idx] }}</td>
                  <td class="px-4 py-2 text-right font-bold text-[var(--color-main-700)]">
                    {{ selectedReport.detail_presentase[idx] }} %
                    <div :style="{width: selectedReport.detail_presentase[idx] + '%'}" class="h-[4px] rounded-full bg-[var(--color-main-700)]"></div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <!-- Body -->
      </div>
    </el-dialog>

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
      searchQuery: '',
      summaryMengajar:[],
      presensiMengajar:[],
      presensiStudents:[],
      idSemester:-1,
      idGuru:-1,
      optionsSemester:[],
      dateFrom:'',
      dateEnd:'',
      statistic:{
        labels:[],
        datasets:[
          {
            label: 'Hadir',
            data: [],
          },
        ]
      },
      changeDateFrom:false,
      showSummaryDetail:false,
      summaryStudents:[],
    }
  },

  computed: {
		...mapState(useAuthStore, {
			user: 'loggedUser',
      role: 'role',
      roles: 'roles',
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
      let m = {
        hadir:0,
        sakit:0,
        izin:0,
        alfa:0,
      }
      this.summaryMengajar.forEach(d => {
        m.hadir += parseInt(d.total_hadir ?? 0)
        m.sakit += parseInt(d.total_sakit ?? 0)
        m.izin += parseInt(d.total_izin ?? 0)
        m.alfa += parseInt(d.total_alfa ?? 0)
      })

      return m
    },

    presenceRate() {
      const t = Object.values(this.aggregatedStats).reduce((a, b) => a + b, 0)
      return t ? Math.round((this.aggregatedStats.hadir / t) * 100) : 0
    },

    currentPeriodLabel() {
      return this.periods.find(p => p.id === this.activePeriod)?.label
    },
    paramsStatistic(){
      let params = {
        where:{
          'id_semester':this.idSemester,
          'id_kelas':this.user.id_kelas,
        },
      }
      switch (this.activePeriod) {
        case 'HARIAN':
          params.where = {...params.where,...{
            'tanggal>=':this.dateFrom ?? '',
            'tanggal<=':this.dateFrom ?? '',
          }}
          return {...params, ...{type:'harian',}}
        case 'MINGGUAN':
          params.where = {...params.where,...{
            'tanggal>=':this.dateFrom ?? '',
            'tanggal<=':this.dateEnd ?? '',
          }}
          return {...params, ...{type:'mingguan',}}
        case 'BULANAN':
          params.where = {...params.where,...{
            'tanggal>=':this.dateFrom ?? '',
            'tanggal<=':this.dateEnd ?? '',
          }}
          return {...params, ...{type:'bulanan',}}
        case 'SEMESTER':
          params.where = {...params.where,...{
            'tanggal>=':this.dateFrom ?? '',
            'tanggal<=':this.dateEnd ?? '',
          }}
          return {...params, ...{type:'semester',}}
        default:
          return params
          break;
      }
    },
  },
  watch:{
    showSummaryDetail(val){
      if (val) {
        this.getListMengajar()
        this.selectedReport.detail_nama_mapel = this.selectedReport.detail_nama_mapel.split(',')
        this.selectedReport.detail_hadir = this.selectedReport.detail_hadir.split(',').map(d => parseInt(d))
        this.selectedReport.detail_sakit = this.selectedReport.detail_sakit.split(',').map(d => parseInt(d))
        this.selectedReport.detail_izin = this.selectedReport.detail_izin.split(',').map(d => parseInt(d))
        this.selectedReport.detail_alfa = this.selectedReport.detail_alfa.split(',').map(d => parseInt(d))
        this.selectedReport.detail_presentase = this.selectedReport.detail_hadir.map((d, i) => {
          let total = d + this.selectedReport.detail_sakit[i] + this.selectedReport.detail_izin[i] + this.selectedReport.detail_alfa[i]
          return total ? Math.round((d / total) * 100) : 0
        })
      }
    },
    idSemester(val){
      this.findDateStart()
    },
    activePeriod(val){
      this.findDateStart()
    },
    dateFrom(val){
      this.findDateEnd()
      this.getData()
    }
  },
  methods:{
    getRank(val){
      if (val >= 90) return 'Sangat Baik'
      else if (val >= 80) return 'Baik'
      else if (val >= 70) return 'Cukup'
      else if (val >= 60) return 'Kurang'
      else return 'Sangat Kurang'
    },
    findDateStart(){
      switch (this.activePeriod) {
        case 'HARIAN':
          this.dateFrom = this.dateNow()
          break
        case 'MINGGUAN':
          this.dateFrom = getStartAndEndOfWeek(this.dateNow()).startOfWeek
          break
        case 'BULANAN':
          this.dateFrom = getStartAndEndOfMonth(this.dateNow()).startOfMonth
          break
        case 'SEMESTER':
          let sem = this.optionsSemester.filter(d => d.value == this.idSemester)?.[0] ?? {}
          this.dateFrom = sem.tanggal_mulai
          break
        default:
          break;
      }
    },
    findDateEnd(){
      switch (this.activePeriod) {
        case 'HARIAN':
          this.dateEnd = this.dateFrom
          break
        case 'MINGGUAN':
          this.dateEnd = getStartAndEndOfWeek(this.dateFrom).endOfWeek
          break
        case 'BULANAN':
          this.dateEnd = getStartAndEndOfMonth(this.dateFrom).endOfMonth
          break
        case 'SEMESTER':
          let sem = this.optionsSemester.filter(d => d.value == this.idSemester)?.[0] ?? {}
          this.dateEnd = sem.tanggal_selesai
          break
        default:
          break;
      }
    },
    async getInitial(){
      await this.$http.get('data/semester/options')
        .then(res => {
          let data = res.data
          this.optionsSemester = data
          this.idSemester = data[0]?.value
          this.idGuru = this.user.id ?? -1
          setTimeout(() => {
            this.dateFrom = this.dateNow()
            this.getData()
            this.getSummary()
          }, 1000)
        })
    },
    getPresensiSantri(){
      this.$http.get('presensi/santri',{
        params:{
          id_mengajar_kelas:this.selectedReportDetail.id
        }
      }).then(res => {
        this.presensiStudents = res.data
      })
    },
    getData(){
      this.$http.get('presensi/mengajar/get_all_grouping', {
        params: {...this.paramsStatistic,
          ...{order:['tanggal desc','id_sesi desc']},
        }
      }).then(res => {
        this.summaryMengajar = res.data
        this.getSummary()
      })
    },
    getListMengajar(){
      this.$http.get('presensi/mengajar/get_all', {
        params: {...this.paramsStatistic,
          ...{where:{
            'id_mapel':this.selectedReport.id_mapel,
          }},
          ...{order:['tanggal desc','id_sesi desc']},
        }
      }).then(res => {
        this.presensiMengajar = res.data
      })
    },
    getSummary(){
      this.$http.get('presensi/mengajar/summary',{
        params: this.paramsStatistic
      }).then(res => {
        let statistic = res.data
        let ids = []
        this.summaryMengajar.forEach(d => {
          console.log(d)
          ids = [...ids, ...d.ids]
        })
        this.$http.get('presensi/santri/summary',{
          params:{
            ids: ids.join(','),
          }
        }).then(res => {
          this.summaryStudents = res.data
          this.summaryStudents.forEach(s => {
            statistic.labels.push(s.nama)
            statistic.datasets[0].data.push(s.presentase_hadir)
          })
          this.statistic = statistic
          console.log(this.statistic  )
        })
      })
    }
  },
  created(){
    this.getInitial()
  }
}
</script>

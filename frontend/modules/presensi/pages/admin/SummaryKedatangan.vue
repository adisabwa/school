<template>
  <div class="space-y-7 px-2 pt-2 pb-14">
    <!-- HEADER -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
      <div class="space-y-1">
        <div class="text-4xl font-black text-slate-900 tracking-tight">Monitoring Kedisiplinan Guru</div>
        <div class="text-slate-500 font-medium italic text-base">Rekapitulasi keterlambatan, tugas mandiri, dan performa pengajar.</div>
      </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_minmax(300px,400px)] lg:items-end justify-between gap-4
      bg-white py-2 px-3 rounded-xl">
      <div id="filter-report" class="flex bg-white p-1 rounded-xl border border-slate-100 shadow-sm gap-x-2 *:m-0
        overflow-x-scroll no-scrollbar">
        <el-button
          :id="'filter-report-'+p.id"
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
      <floating-select v-if="activePeriod == 'SEMESTER'" v-model:value="idSemester" :options="optionsSemester"
          class=""/>
      <div v-else
        class="flex items-center ">
        <date-wheel-picker
          size="large" v-model:value="dateFrom" placeholder="Pilih Tanggal"
          ref="dateSelect" class="w-full" :day-locked="activePeriod == 'BULANAN'"
          @change="getSummary">
        </date-wheel-picker>
        <template v-if="activePeriod !== 'HARIAN'">
          <span class="text-[12px] mx-2">Sampai</span>
          <date-wheel-picker readonly v-model:value="dateEnd" class="w-full h-full" size="large"
            prefix-icon=""/>
        </template> 
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div v-for="(c,i) in overviewCards" :key="i" class="bg-white p-5 rounded-[1.6rem] border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all border-b-4 hover:border-b-[var(--color-main-500)]">
        <div :class="['w-12 h-12 rounded-xl flex items-center justify-center', c.bg, c.color]">
					<icons :icon="c.icon" class="text-[26px] m-0" />
				</div>
        <div>
          <div class="text-sm font-black text-slate-400 uppercase tracking-widest">{{ c.label }}</div>
          <div class="text-4xl font-black text-slate-800">
            {{ c.value }}<span class="text-slate-400 text-lg ml-2">{{ c.sub }}</span>
          </div>
        </div>
      </div>
      
    </div>

    <div class="bg-white rounded-[2.4rem] border border-slate-100 shadow-sm overflow-hidden relative">
      <icons icon="lucide:sparkle" class="text-[220px] absolute -right-16 -top-16 opacity-5 text-[var(--color-main-600)]" />
      <div class="p-6 border-b border-slate-50 bg-slate-50/30">
        <h3 class="text-2xl font-black flex items-center gap-1 mb-1"><icons icon="mdi:history" class="text-[var(--color-main-600)] text-3xl" /> 
        Rekapitulasi Kedatangan Guru</h3>
        <table-data refs="tableData" :fields="{nama_guru:{nama_kolom:'nama_guru',label:'Nama Guru'}}" href="presensi/kedatangan/summary"
          :pass-columns="['nama_guru']"
					:params="paramsTable" 
					:show-create="false" :show-upload="false" :show-dropdown="false" :checked="false"
          @updateData="(data) => summaryTeacher = data"
					>
					<el-table-column>
						<template #header="scope">
							<span class="font-bold">NAMA GURU</span>
            </template>
            <template #default="scope">
              <div class="font-bold">{{ scope.row.nama }}</div>
            </template>
					</el-table-column>
					<el-table-column align="center" width="100px">
						<template #header="scope">
							<div class="text-[var(--color-main-700)] leading-[1.3] tracking-widest">HADIR</div>
            </template>
            <template #default="scope">
              <div class="mx-auto w-11 h-11 flex items-center justify-center
								text-xl font-semibold text-[var(--color-main-700)] bg-[var(--color-main-50)] rounded-xl">{{ scope.row.total_kehadiran }}</div>
            </template>
					</el-table-column>
					<el-table-column align="center" width="150px">
						<template #header="scope">
							<div class="text-orange-600 leading-[1.3] tracking-widest">TELAT DATANG</div>
            </template>
            <template #default="scope">
              <div class="mx-auto flex items-center justify-center
								text-xl font-semibold text-orange-600 ">{{ scope.row.total_telat_datang }}</div>
            </template>
					</el-table-column>
					<el-table-column align="center" width="150px">
						<template #header="scope">
							<div class="text-orange-600 leading-[1.3] tracking-widest">PULANG AWAL</div>
            </template>
            <template #default="scope">
              <div class="mx-auto flex items-center justify-center
								text-xl font-semibold text-orange-600 ">{{ scope.row.total_telat_pulang }}</div>
            </template>
					</el-table-column>
				</table-data>
      </div>
    </div>

  </div>
</template>

<script>

export default {
  name: 'TeacherMonitoring',
  setup(){
    return {
      scrollElement,
    }
  },
  components: { 

	},
  data() {
    return { 
			idSemester:'',
			searchQuery: '', 
			summaryTeacher:[],
			fieldData:{
			},
      activePeriod: 'HARIAN',
      dateFrom:'',
      dateEnd:'',
      optionsSemester:[],
		}
  },
  computed: {
    aggregateStats() {
      const total = this.summaryTeacher.length
      const totalHadir = this.summaryTeacher.reduce((a,c)=>a+ parseInt(c.total_kehadiran ?? 0),0)
      const totalTelatDatang = this.summaryTeacher.reduce((a,c)=>a+ parseInt(c.total_telat_datang ?? 0),0)
      const totalTelatPulang = this.summaryTeacher.reduce((a,c)=>a+ parseInt(c.total_telat_pulang ?? 0),0)
      return { 
        totalHadir,
        totalTelatDatang,
        totalTelatPulang,
        presentaseDatang: Math.round((totalHadir - totalTelatDatang) / total * 100) ,
        presentasePulang: Math.round((totalHadir - totalTelatPulang) / total * 100) ,
      }
    },
    overviewCards() {
      return [
        { label:'Persentase Kedisplinan Kedatangan', value:`${this.aggregateStats.presentaseDatang}`, icon:'mdi:calendar-check', color:'text-[var(--color-main-600)]', bg:'bg-[var(--color-main-50)]', sub:'%' },
        { label:'Persentase Kedisplinan Perpulangan', value:`${this.aggregateStats.presentasePulang}`, icon:'mdi:calendar-check', color:'text-purple-600', bg:'bg-purple-50', sub:'%' },
        { label:'Total Kehadiran', value:`${this.aggregateStats.totalHadir}`, icon:'mdi:file-document-check-outline', color:'text-cyan-600', bg:'bg-cyan-50' },
        { label:'Total Terlambat Datang', value:`${this.aggregateStats.totalTelatDatang}`, icon:'lucide:clipboard-check', color:'text-orange-600', bg:'bg-orange-50' },
        { label:'Total Pulang Lebih Awal', value:`${this.aggregateStats.totalTelatPulang} `, icon:'lucide:clipboard-check', color:'text-amber-600', bg:'bg-amber-50'},
      ]
    },
    paramsTable(){
      let params = {
        where:{ },
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
    periods() {
      return [
        { id: 'HARIAN', label: 'Harian' },
        { id: 'MINGGUAN', label: 'Mingguan' },
        { id: 'BULANAN', label: 'Bulanan' },
        { id: 'SEMESTER', label: 'Semester' }
      ]
    },
  },
  watch:{
    activePeriod(val){
      this.findDateStart()
    },
  },
  methods: {
    
    findDateStart(){
      switch (this.activePeriod) {
        case 'HARIAN':
          this.dateFrom = dateNow()
          break
        case 'MINGGUAN':
          this.dateFrom = getStartAndEndOfWeek(dateNow()).startOfWeek
          break
        case 'BULANAN':
          this.dateFrom = getStartAndEndOfMonth(dateNow()).startOfMonth
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
		async getData(){
      await this.$http.get('data/semester/options')
        .then(res => {
          let data = res.data
          this.optionsSemester = data
          this.idSemester = data[0]?.value
        })

      await this.$refs.tableData.getData()
		}
  },
	mounted(){
		this.getData()
	}
}
</script>

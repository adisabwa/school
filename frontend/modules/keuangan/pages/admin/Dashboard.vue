<template>
  <div class="space-y-4 animate-in fade-in slide-in-from-bottom-4 duration-500 p-3">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
      <div>
        <div class="text-4xl font-black text-gray-900">Dashboard</div>
        <div class="text-gray-400 font-medium">
          Kilas balik kondisi finansial sekolah.
        </div>
      </div>

      <el-button
        :loading="isAnalyzing"
        class="flex items-center gap-3 px-8 py-4 rounded-3xl font-black shadow-xl bg-gradient-to-br from-indigo-600 to-blue-600 text-white hover:scale-105 transition-all"
        @click="onAnalyze"
      >
        <icons icon="lucide:sparkles" :class="isAnalyzing ? 'animate-spin' : ''"/>
        <div>
          {{ isAnalyzing ? 'Menganalisis...' : 'Analisis AI' }}
        </div>
      </el-button>
    </div>

    <!-- Stats -->
    <div class="flex flex-col md:flex-row gap-6">
      <div class="min-w-[360px] bg-white/70 p-3">
        <div class="text-slate-500 uppercase text-lg font-bold text-center mt-3">Statistik {{ monthIndo(dateNow()) }}</div>
        <div class="flex flex-col gap-3 cursor-pointer">
          <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1 group
            flex items-center gap-4">
            <div class="flex justify-between items-start mb-2">
              <div :class="`p-4 rounded-2xl w-fit transition-transform group-hover:scale-110 bg-blue-50 aspect-square
                flex items-center justify-center`">
                <icons icon="mdi:wallet" class="m-0 text-[30px] text-blue-600" />
              </div>
            </div>
            <div>
              <div class="text-xs text-gray-400 font-bold uppercase tracking-widest">Saldo Tunai </div>
              <div class="text-2xl font-black text-gray-900 truncate">{{ toIDR(stats.balance) }}</div>
            </div>
          </div>
          <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1 group
            flex items-center gap-4">
            <div class="flex justify-between items-start mb-2">
              <div :class="`p-4 rounded-2xl w-fit transition-transform group-hover:scale-110 bg-[var(--color-main-50)] aspect-square
                flex items-center justify-center`">
                <icons icon="mdi:wallet" class="m-0 text-[30px] text-[var(--color-main-600)]" />
              </div>
            </div>
            <div>
              <div class="text-xs text-gray-400 font-bold uppercase tracking-widest">Pemasukan </div>
              <div class="text-2xl font-black text-gray-900 truncate">{{ toIDR(stats.monthlyIncome) }}</div>
            </div>
          </div>
          <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1 group
            flex items-center gap-4">
            <div class="flex justify-between items-start mb-2">
              <div :class="`p-4 rounded-2xl w-fit transition-transform group-hover:scale-110 bg-rose-50 aspect-square
                flex items-center justify-center`">
                <icons icon="mdi:wallet" class="m-0 text-[30px] text-rose-600" />
              </div>
            </div>
            <div>
              <div class="text-xs text-gray-400 font-bold uppercase tracking-widest">Pengeluaran </div>
              <div class="text-2xl font-black text-gray-900 truncate">{{ toIDR(stats.monthlyExpense) }}</div>
            </div>
          </div>
          <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1 group
            flex items-center gap-4">
            <div class="flex justify-between items-start mb-2">
              <div :class="`p-4 rounded-2xl w-fit transition-transform group-hover:scale-110 bg-amber-50 aspect-square
                flex items-center justify-center`">
                <icons icon="mdi:wallet" class="m-0 text-[30px] text-amber-600" />
              </div>
            </div>
            <div>
              <div class="text-xs text-gray-400 font-bold uppercase tracking-widest">Rasio Pelunasan </div>
              <div class="text-2xl font-black text-gray-900 truncate">{{ stats.collectionRate }} %</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Transaksi -->
      <div class="bg-white p-6 rounded-[40px] border shadow-sm flex-1">
        <div class="text-xl font-black mb-8 flex items-center gap-3">
          <el-icon class="text-blue-600"><History /></el-icon>
          <div>Transaksi Terbaru</div>
        </div>

        <div class="space-y-2">
          <div
            v-for="tx in transactions.slice(0, 5)"
            :key="tx.id"
            class="flex justify-between items-center p-5 bg-gray-50 rounded-3xl hover:bg-gray-100 transition-colors"
          >
            <div class="flex items-center gap-4">
              <div
                class="p-3 rounded-2xl"
                :class="tx.jenis == 'pemasukan' || tx.jenis == 'iuran'
                  ? 'bg-[var(--color-main-100)] text-[var(--color-main-600)]'
                  : 'bg-rose-100 text-rose-600'"
              >
                <icons :icon="tx.jenis == 'pemasukan' || tx.jenis == 'iuran' ? 'gg:trending' : 'material-symbols:receipt'"
                  class="m-0 text-[22px]" />
              </div>

              <div>
                <div class="text-sm font-black text-gray-900">
                  {{ tx.keterangan }}
                </div>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                  {{ dateIndo(tx.tanggal) }} • {{ tx.jenis.toUpperCase() }}
                </div>
              </div>
            </div>

            <div
              class="text-sm font-black"
              :class="tx.jenis == 'pemasukan' || tx.jenis == 'iuran' ? 'text-[var(--color-main-600)]' : 'text-rose-600'"
            >
              {{ tx.jenis == 'pemasukan' || tx.jenis == 'iuran' ? '+' : '-' }}
              {{ toIDR(tx.nominal_disetor) }}
            </div>
          </div>
        </div>

        <el-button
          text
          class="w-full mt-8 py-4 text-xs font-black text-blue-600 uppercase tracking-widest hover:bg-blue-50 rounded-2xl transition-all"
          @click="$router.replace({name:'keuangan-transaksi'})"
        >
          <div class="flex items-center gap-1">
            Lihat Semua Transaksi
            <el-icon><ArrowRight /></el-icon>
          </div>
        </el-button>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  name: 'Dashboard',
  setup(){
    return {
      toIDR, dateIndo, monthIndo, dateNow, 
    }
  },
  components: {
    
  },
  props: {
  },
  computed: {
    totalStudentBalance() {
      return this.students.reduce((sum, s) => sum + s.balance, 0)
    }
  },
  data(){
    return {
      stats: {},
      transactions: [],
      students: [],
      isAnalyzing: false,
    }
  },
  watch:{
    setView(){
      
    },
    onAnalyze(){
      
    },
  },
  methods: {
    getInitial(){
      this.getSaldo()
      this.getTransactions()
      this.getTransactionsMonthly()
      this.getIuran()
    },
    getSaldo(){
      this.$http('keuangan/admin/iuran-saldo/get_saldo')
        .then(res => {
          let saldos = res?.data ?? []
          let balance = 0
          saldos.forEach(d => {
            balance += (d.total_saldo_masuk - d.total_saldo_keluar)
          })
          this.stats.balance = balance

        })
    },
    getTransactions(){
      this.$http('keuangan/admin/transaksi',{
        params:{
          limit:5,
          order:['tanggal desc']
        }
      }).then(res => {
        this.transactions = res?.data ?? []
      })
    },
    getTransactionsMonthly(){
      let { startOfMonth, endOfMonth} = getStartAndEndOfMonth(dateNow())
      this.$http('keuangan/admin/transaksi',{
        params:{
          where:{
            'tanggal >= ': startOfMonth,
            'tanggal <= ': endOfMonth,
          }
        }
      }).then(res => {
        let transactions = res?.data ?? []
        let income = 0
        let expense = 0
        transactions.forEach(d => {
          let nominal = typeof d.nominal_disetor == 'number' ? d.nominal_disetor : parseInt(d.nominal_disetor)
          if (d.jenis == 'pengeluaran') 
            expense += nominal
          else
            income += nominal
          console.log('f', d.nominal_disetor, typeof d.nominal_disetor, income, expense)
        })
        this.stats.monthlyIncome = income
        this.stats.monthlyExpense = expense
      })
    },
    getIuran(){
    
      let { startOfMonth, endOfMonth} = getStartAndEndOfMonth(dateNow())
      this.$http.get('keuangan/admin/iuran/tagihan/get_all_grouping')
        .then(res => {
          let datas = res?.data ?? []
          let tagihan = 0
          let iuran = 0
          datas.forEach(d => {
            if (d.status == '1') iuran += parseInt(d.nominal)
            tagihan += parseInt(d.nominal)
          })

          console.log(iuran, tagihan)
          this.stats.collectionRate = Math.round(iuran / tagihan * 10000) / 100
        })
    }
  },
  created(){
    this.getInitial();
  }
}
</script>
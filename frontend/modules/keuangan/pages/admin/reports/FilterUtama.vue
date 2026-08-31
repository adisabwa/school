<template>
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 bg-white p-4 rounded-[36px] 
    border border-gray-100 shadow-sm">
    <!-- 1. PERIOD SELECTOR -->
    <div class="space-y-2">
      <label class="flex items-center gap-2 text-xs font-black text-gray-400 uppercase tracking-widest">
        <el-icon class="text-[18px]"><Calendar /></el-icon> 1. Rentang Waktu
      </label>

      <!-- Radio Group Rentang Waktu -->
      <el-radio-group v-model="localPeriodType" size="small" class="w-full !flex" fill="var(--color-main-700) !important">
        <el-radio-button value="daily" class="flex-1">Harian</el-radio-button>
        <el-radio-button value="monthly" class="flex-1">Bulanan</el-radio-button>
        <el-radio-button value="yearly" class="flex-1">Tahunan</el-radio-button>
      </el-radio-group>

      <!-- Dynamic Time Inputs -->
      <div class="p-4 py-2 bg-gray-50 rounded-2xl border border-gray-100 space-y-3">
        <!-- Harian -->
        <div v-if="localPeriodType === 'daily'" class="grid grid-cols-2 gap-3">
          <div class="space-y-1">
            <span class="text-[11px] font-black text-gray-400 uppercase">Mulai</span>
            <date-wheel-picker
              v-model:value="localStartDate"
              type="date"
              format="YYYY-MM-DD"
              value-format="YYYY-MM-DD"
              placeholder="Mulai"
              class="w-full"
              size="small"
            />
          </div>
          <div class="space-y-1">
            <span class="text-[11px] font-black text-gray-400 uppercase">Selesai</span>
            <date-wheel-picker
              v-model:value="localEndDate"
              type="date"
              format="YYYY-MM-DD"
              value-format="YYYY-MM-DD"
              placeholder="Selesai"
              class="w-full"
              size="small"
            />
          </div>
        </div>
        <!-- Bulanan -->
        <div v-if="localPeriodType === 'monthly' || localPeriodType === 'yearly'" class="grid grid-cols-2 gap-3">
          <!-- Floating Select Bulan -->
          <div class="relative" v-if="localPeriodType === 'monthly'">
            <span class="text-[11px] font-black text-gray-400 uppercase">Bulan</span>
            <floating-select
              v-model:value="localSelectedMonth" 
              class="peer"
              :options="[
                {value:'ALL', label:'Semua Bulan'},
                ...months,
              ]"
            />
          </div>
          <!-- Floating Select Tahun -->
          <div class="relative" >
            <span class="text-[11px] font-black text-gray-400 uppercase">Tahun</span>
            <floating-select
              v-model:value="localSelectedYear"
              class="peer"
              :options="yearList"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- 2. DIMENSION SELECTOR -->
    <div class="space-y-2">
      <label class="flex items-center gap-2 text-xs font-black text-gray-400 uppercase tracking-widest">
        <el-icon class="text-[18px]"><Discount /></el-icon> 2. Dimensi Laporan
      </label>
      <el-radio-group class="grid grid-cols-3 gap-2" v-model="localDimension" size="small" fill="var(--color-main-700) !important">
        <el-radio-button
          v-for="dim in dimensionsList"
          :key="dim.id"
          :value="dim.id"
        >
          <div :class="[
              'flex w-full justify-center items-center gap-2 py-1 rounded-2xl text-xs font-black transition-all',
            ]">
            <el-icon class="text-[16px]">
              <component :is="dim.icon" />
            </el-icon>
            {{ dim.label }}
          </div>
        </el-radio-button>
      </el-radio-group >
      <div class="bg-[var(--color-main-50)] border border-blue-100/50 rounded-xl p-3 text-[11px] font-medium text-[var(--color-main-700)] leading-relaxed 
        flex gap-2 items-center">
        <el-icon class="shrink-0 text-[var(--color-main-500)] text-md"><InfoFilled /></el-icon>
        <span>{{ currentDimensionHelp }}</span>
      </div>
    </div>

    <!-- 3. DISPLAY FORMAT & SEARCH -->
    <div class="space-y-2">
      <label class="flex items-center gap-2 text-xs font-black text-gray-400 uppercase tracking-widest">
        <el-icon><DataAnalysis /></el-icon> 3. Pengelompokan Data
      </label>
      <!-- Search Input -->
      <!-- <div class="bg-gray-50 rounded-2xl border border-gray-100 p-3 space-y-1.5">
        <span class="text-[11px] font-black text-gray-400 uppercase tracking-wider block">Pencarian Cepat</span>
        <el-input
          v-model="localReportSearchQuery"
          placeholder="Cari kata kunci..."
          clearable
          size="small"
        >
          <template #prefix>
            <el-icon><Search /></el-icon>
          </template>
        </el-input>
      </div> -->

      <el-radio-group v-model="localSubtotalGroup" size="small" class="w-full !grid !grid-cols-3 gap-1.5 bg-gray-50 p-1.5 rounded-xl border border-gray-100"
        fill="var(--color-main-700) !important">
        <el-radio-button v-for="sg in subtotalGroups" :key="sg.id" :value="sg.id" class="w-full text-center">
          <div class="p-1">{{ sg.label }} </div>
        </el-radio-button>
      </el-radio-group>
      <p class="text-[10px] text-gray-400 font-bold px-1">
        {{ currentSubtotalHelp }}
      </p>
    </div>
    
  </div>
</template>

<script>
import { 
  Calendar, Discount, InfoFilled, Operation, DataAnalysis, Search, 
  User, Files, Wallet 
} from '@element-plus/icons-vue';

export default {
  name: 'FilterUtama',
  components: {
    Calendar, Discount, InfoFilled, Operation, DataAnalysis, Search, 
    User, Files, Wallet
  },
  props: {
    periodType: { type: String, required: true },
    startDate: { type: String, required: true },
    endDate: { type: String, required: true },
    selectedMonth: { type: String, required: true },
    selectedYear: { type: Number, required: true },
    dimension: { type: String, required: true },
    subtotalGroup: { type: String, required: true },
    formatType: { type: String, required: true },
    reportSearchQuery: { type: String, required: true },
  },
  emits: [
    'update:periodType',
    'update:startDate',
    'update:endDate',
    'update:selectedMonth',
    'update:selectedYear',
    'update:dimension',
    'update:subtotalGroup',
    'update:formatType',
    'update:reportSearchQuery',
  ],
  data() {
    const now = dateNow();
    const currentYear = now.substr(0, 4);
    const currentMonth = now.substr(5, 2);
    const currentDate= now.substr(8, 2);
    return {
      // Local variables (mirrored from props)
      localPeriodType: this.periodType,
      localStartDate: this.startDate,
      localEndDate: now,
      localSelectedMonth: currentMonth,
      localSelectedYear: parseInt(currentYear),
      localDimension: this.dimension,
      localSubtotalGroup: this.subtotalGroup,
      localReportSearchQuery: this.reportSearchQuery,

      // Reference options
      months: monthList(),
      yearList: yearList(4),
      dimensionsList: [
        { id: 'santri', label: 'Siswa', icon: 'User' },
        { id: 'pos', label: 'Per Pos', icon: 'Files' },
        { id: 'kas', label: 'Per Kas', icon: 'Wallet' },
      ],
      subtotalGroups: [
        { id: 'trans', label: 'Transaksi' },
        { id: 'daily', label: 'Harian' },
        { id: 'monthly', label: 'Bulanan' },
      ],
    };
  },
  computed: {
    currentDimensionHelp() {
      const helps = {
        santri: 'Tagihan iuran & tabungan siswa.',
        pos: 'Kas per pos anggaran penanggung jawab.',
        kas: 'Arus kas per rekening & kas fisik.',
      };
      return helps[this.localDimension] || '';
    },
    currentSubtotalHelp() {
      const helps = {
        dimension: `Data di baris tabel dikelompokkan berdasarkan item ${this.localDimension}.`,
        daily: 'Data di baris tabel dijumlahkan & direkap per tanggal (Harian).',
        weekly: 'Data di baris tabel dijumlahkan & direkap per minggu (Mingguan).',
        monthly: 'Data di baris tabel dijumlahkan & direkap per bulan (Bulanan).',
      };
      return helps[this.localSubtotalGroup] || '';
    },
  },
  watch: {
    // Sync external props -> local data
    periodType(val) { this.localPeriodType = val; },
    startDate(val) { this.localStartDate = val; },
    endDate(val) { this.localEndDate = val; },
    selectedMonth(val) { this.localSelectedMonth = val; },
    selectedYear(val) { this.localSelectedYear = val; },
    dimension(val) { this.localDimension = val; },
    subtotalGroup(val) { this.localSubtotalGroup = val; },
    formatType(val) { this.localFormatType = val; },
    reportSearchQuery(val) { this.localReportSearchQuery = val; },

    // Emit local changes -> parent
    localPeriodType(val) { this.$emit('update:periodType', val); },
    localStartDate(val) { 
      if (this.localStartDate > this.localEndDate) this.localEndDate = val
      this.$emit('update:startDate', val); },
    localEndDate(val) { 
      if (this.localStartDate > this.localEndDate) this.localStartDate = val
      this.$emit('update:endDate', val); },
    localSelectedMonth(val) { this.$emit('update:selectedMonth', val); },
    localSelectedYear(val) { this.$emit('update:selectedYear', Number(val)); },
    localDimension(val) { this.$emit('update:dimension', val); },
    localSubtotalGroup(val) { this.$emit('update:subtotalGroup', val); },
    localReportSearchQuery(val) { this.$emit('update:reportSearchQuery', val); },
  },
  methods:{
  }
};
</script>

<style scoped>
/* Custom styling override untuk el-radio-button agar sesuai tema UI */
:deep(.el-radio-button__inner) {
  width: 100%;
  border-radius: 12px !important;
  border: none !important;
  font-size: 10px !important;
  font-weight: 800 !important;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 8px 12px !important;
  background-color: transparent;
  color: #9ca3af;
}

:deep(.el-radio-button__original-radio:checked + .el-radio-button__inner) {
  background-color: #2563eb !important;
  color: #ffffff !important;
  box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2) !important;
}

:deep(.el-radio-group) {
  background-color: #f9fafb;
  padding: 4px;
  border-radius: 16px;
  border: 1px solid #f3f4f6;
}
</style>
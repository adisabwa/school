<template>
  <div class="p-3 bg-white/70 space-y-3 animate-in fade-in slide-in-from-bottom-4 duration-500 pb-12">
    <!-- HEADER BAR -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 border-b border-gray-100 pb-4">
      <div>
        <div class="flex items-center gap-2 text-blue-600 font-black text-xs uppercase tracking-widest mb-2">
          <el-icon><Files /></el-icon> Laporan Gabungan Adaptif & Subtotal
        </div>
        <h2 class="text-4xl font-black text-gray-900 tracking-tight">{{ dynamicTitle }}</h2>
        <p class="text-gray-400 font-medium text-sm mt-1">
          Analisis data finansial harian, bulanan, tahunan dengan rekapitulasi jumlah per periode dan total keseluruhan.
        </p>
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <el-button
          type="primary"
          class="!rounded-2xl !px-6 !py-3 !text-xs !font-black uppercase tracking-widest"
          @click="handleDownloadCsv"
        >
          <icons icon="mdi:download" class=""/> Ekspor Data
        </el-button>
      </div>
    </div>

    
    <!-- FILTER UTAMA -->
    <FilterUtama
      v-model:periodType="periodType"
      v-model:startDate="startDate"
      v-model:endDate="endDate"
      v-model:selectedMonth="selectedMonth"
      v-model:selectedYear="selectedYear"
      v-model:dimension="dimension"
      v-model:subtotalGroup="subtotalGroup"
      v-model:reportSearchQuery="reportSearchQuery"
    />

    <!-- FILTER SPESIFIK -->
    <FilterSpesifik
      v-model:selectedStudent="selectedStudent"
      v-model:selectedUnit="selectedUnit"
      v-model:selectedClass="selectedClass"
      v-model:selectedStatus="selectedStatus"
      v-model:selectedCategory="selectedCategory"
      v-model:selectedPos="selectedPos"
      v-model:selectedCashAccount="selectedCashAccount"
      v-model:selectedTxType="selectedTxType"
      :dimension="dimension"
    />

    <!-- REPORT CONTENT VIEWPORT -->
    <div class="space-y-4">
      <!-- STATISTIK DATA (Ditampilkan hanya pada mode summary) -->
      <StatistikData
        v-if="formatType === 'summary'"
        :dimension="dimension"
        :subtotal-group="subtotalGroup"
        :student-stats="studentStats"
        :transaction-stats="transactionStats"
      />

      <!-- TABEL DETAIL & REKAPITULASI -->
      <div class="overflow-x-scroll">
        <TabelDetail
          :periodType="periodType"
          :startDate="startDate"
          :endDate="endDate"
          :selectedMonth="selectedMonth"
          :selectedYear="selectedYear"
          :dimension="dimension"
          :subtotalGroup="subtotalGroup"
          :reportSearchQuery="reportSearchQuery"

          :selectedStudent="selectedStudent"
          :selectedUnit="selectedUnit"
          :selectedClass="selectedClass"
          :selectedStatus="selectedStatus"
          :selectedCategory="selectedCategory"
          :selectedPos="selectedPos"
          :selectedCashAccount="selectedCashAccount"
          :selectedTxType="selectedTxType"
        />
      </div>
    
    </div>
  </div>
</template>

<script>
import { Files, RefreshRight, Download } from '@element-plus/icons-vue';

import FilterUtama from './FilterUtama.vue';
import FilterSpesifik from './FilterSpesifik.vue';
// import StatistikData from './StatistikData.vue';
import TabelDetail from './TabelDetail.vue';

export default {
  name: 'Reports',
  setup() {
    const { openLink } = useBrowserActions()
    return {
      openLink,
    }
  },
  components: {
    Files,
    RefreshRight,
    Download,
    FilterUtama,
    FilterSpesifik,
  //   StatistikData,
    TabelDetail,
  },
  props: {
    transactions: { type: Array, required: true, default: () => [] },
    payments: { type: Array, required: true, default: () => [] },
  },
  data() {
    const d = new Date();
    d.setMonth(d.getMonth() - 1);
    const prevMonthStr = d.toISOString().split('T')[0];
    const todayStr = new Date().toISOString().split('T')[0];

    return {
      periodType: 'monthly',
      dimension: 'pos',
      subtotalGroup: 'trans',
      startDate: prevMonthStr,
      endDate: todayStr,
      selectedMonth: dateNow().substr(5, 2),
      selectedYear: dateNow().substr(0, 4),
      selectedStudent: 'ALL',
      selectedClass: 'ALL',
      selectedUnit: 'ALL',
      selectedStatus: 'ALL',
      selectedCategory: 'ALL',
      selectedPos: 'ALL',
      selectedCashAccount: 'ALL',
      selectedTxType: 'ALL',
      reportSearchQuery: '',
    };
  },
  computed: {
    dynamicTitle() {
      const dimensionLabels = {
        santri: 'Per Santri',
        pos: 'Per POS',
        cash: 'Per Kas',
      };

      const periodLabels = {
        daily: 'Harian',
        monthly: 'Bulanan',
        yearly: 'Tahunan',
      };

      let title = `Laporan ${dimensionLabels[this.dimension] || ''} ${periodLabels[this.periodType] || ''}`;

      if (this.periodType === 'monthly') {
        title += this.selectedMonth === 'ALL' ? ' ' : (' ' + monthIndo(this.selectedYear + '-' + this.selectedMonth + '-01'))
      } else if (this.periodType === 'yearly') {
        title += ` ${this.selectedYear}`;
      }
      return title;
    },
  },
  methods: {
    handleDownloadCsv() {
      let urlParams =  {
          period_type:this.periodType,
          start_date:this.startDate,
          end_date:this.endDate,
          selected_month:this.selectedMonth,
          selected_year:this.selectedYear,
          dimension:this.dimension,
          subtotal_group:this.subtotalGroup,
          report_search_query:this.reportSearchQuery,
          where:{},
      }
      if (!(this.selectedStudent == 'ALL' || this.selectedStudent == '')) urlParams.where['{n}id_santri'] = this.selectedStudent
      if (!(this.selectedUnit == 'ALL' || this.selectedUnit == '')) urlParams.where['{n}id_unit'] = this.selectedUnit
      if (!(this.selectedClass == 'ALL' || this.selectedClass == '')) urlParams.where['{n}id_kelas'] = this.selectedClass
      if (!(this.selectedStatus == 'ALL' || this.selectedStatus == '')) urlParams.where['status'] = this.selectedStatus
      if (!(this.selectedCategory == 'ALL' || this.selectedCategory == '')) urlParams.where['id_kategori'] = this.selectedCategory
      if (!(this.selectedPos == 'ALL' || this.selectedPos == '')) urlParams.where['id_pos'] = this.selectedPos
      if (!(this.selectedCashAccount == 'ALL' || this.selectedCashAccount == '')) urlParams.where['id_kas'] = this.selectedCashAccount
      if (!(this.selectedTxType == 'ALL' || this.selectedTxType == '')) urlParams.where['jenis'] = this.selectedTxType
      console.log(urlParams)
      urlParams = toQueryString(urlParams)
      this.openLink(this.$siteUrl + '/keuangan/admin/transaksi/download?' + urlParams)
    },
  },
};
</script>
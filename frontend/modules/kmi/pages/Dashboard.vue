<template>
  <div class="p-8 bg-slate-50 min-h-screen">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Dashboard KMI</h1>
        <p class="text-slate-500 mt-1">Ringkasan data akademik dan aktivitas santri bulan ini.</p>
      </div>
      <div class="bg-white p-3 rounded-xl shadow-sm border border-slate-200">
        <span class="text-sm font-medium text-slate-500 block">Total Santri Aktif</span>
        <span class="text-2xl font-bold text-indigo-600">{{ totalSantri }}</span>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
      <!-- Card: Monthly Activity Summary -->
      <el-card class="box-card shadow-sm border-t-4 border-t-indigo-500 hover:shadow-lg transition-shadow duration-300">
        <template #header>
          <div class="flex items-center gap-2">
            <icons icon="mdi:calendar" class="text-indigo-500 text-2xl" />
            <span class="text-lg font-bold text-slate-700">Aktivitas Bulan Ini</span>
          </div>
        </template>
        <div v-if="loadingAktivitas" class="text-center py-4">
          <loading />
        </div>
        <div v-else>
          <el-timeline class="px-2">
            <el-timeline-item
              v-for="activity in aktivitasBulanan"
              :key="activity.id"
              :timestamp="dateIndoRange(activity.tanggal_mulai, activity.tanggal_selesai)"
              placement="top"
              center
              color="#6366f1"
            >
              <p class="text-sm text-slate-700 leading-relaxed font-medium">{{ activity.keterangan }}</p>
            </el-timeline-item>
          </el-timeline>

          <div v-if="aktivitasBulanan?.length === 0" class="text-center py-6">
            <el-empty description="Tidak ada aktivitas" :image-size="60" />
          </div>
        </div>
      </el-card>
      <el-card class="box-card shadow-sm border-t-4 border-t-amber-500 hover:shadow-lg transition-shadow duration-300 ">
        <template #header>
          <div class="flex items-center gap-2">
            <icons icon="mdi:chart-bar" class="text-amber-500 text-2xl" />
            <span class="text-lg font-bold text-slate-700">Jumlah Santri</span>
          </div>
        </template>
        <div v-loading="loadingSantri" class="flex flex-col gap-4">
          <div class="flex justify-center">
            <el-radio-group v-model="viewType" size="small" class="bg-slate-100 p-1 rounded-lg">
              <el-radio-button value="kelas">Per Kelas</el-radio-button>
              <el-radio-button value="tingkat">Per Tingkat</el-radio-button>
              <el-radio-button value="unit">Per Unit</el-radio-button>
            </el-radio-group>
          </div>

          <el-table :data="displayData" style="width: 100%" height="230" class="rounded-lg overflow-hidden border border-slate-100">
            <el-table-column prop="label" :label="viewType === 'kelas' ? 'Kelas' : (viewType === 'tingkat' ? 'Tingkat' : 'Unit')">
              <template #default="scope">
                <div class="flex items-center gap-2">
                  <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                  <span class="font-medium text-slate-700">{{ scope.row.label }}</span>
                </div>
              </template>
            </el-table-column>
            <el-table-column prop="total" label="Total Santri" align="right" width="120">
              <template #default="scope">
                <el-tag type="warning" effect="plain" class="font-bold border-amber-200 text-amber-700">
                  {{ scope.row.total }} Santri
                </el-tag>
              </template>
            </el-table-column>
          </el-table>
          <div v-if="displayData.length === 0" class="text-center py-6">
            <p class="text-slate-400 italic text-sm">Data tidak tersedia</p>
          </div>
        </div>
      </el-card>
    </div>
  </div>
</template>

<script>
// import { mapState } from 'vuex'; // Uncomment if Vuex is used for state management

export default {
  name: 'KmiDashboard',
  setup() {
    return {
      dateNow, dateIndoRange, isEmpty, getStartAndEndOfMonth
    }
  },
  data() {
    return {
      loadingAktivitas: true,
      loadingSantri: true,
      aktivitasBulanan: [],
      santriPerKelas: [],
      viewType: 'tingkat',
    };
  },
  computed: {
    totalSantri() {
      return this.santriPerKelas.reduce((acc, curr) => acc + (parseInt(curr.total_santri) || 0), 0);
    },
    displayData() {
      if (this.viewType === 'kelas') {
        return this.santriPerKelas.map(item => ({
          label: 'Kelas ' + item.kelas,
          total: parseInt(item.total_santri) || 0
        }));
      }

      const grouped = {};
      this.santriPerKelas.forEach(item => {
        let key = '';
        let name = '';
        
        if (this.viewType === 'tingkat') {
          key = item.tingkat;
          name = 'Tingkat ' + key;
        } else {
          // Pengelompokan Unit: MTs (Tingkat 1-3) dan MA (Tingkat 4-6)
          key = item.id_unit || (parseInt(item.tingkat) <= 3 ? 'Unit MTs' : 'Unit MA');
          name = item.nama_unit || key;
        }

        if (!grouped[key]) grouped[key] = { label: name, total: 0 };
        grouped[key].total += parseInt(item.total_santri) || 0;
      });

      return Object.values(grouped).sort((a, b) => {
        return a.label.localeCompare(b.label, undefined, { numeric: true, sensitivity: 'base' });
      });
    }
  },
  components: {
    // Element Plus components are globally registered, no need to import here
    // but for clarity in diff, listing them.
    // ElTimeline, ElTimelineItem, ElCard, ElTable, ElTableColumn, ElTag, ElEmpty, Loading
  },
  created() {
    this.fetchMonthlyActivities();
    this.fetchStudentsSummary();
  },
  methods: {
    fetchMonthlyActivities() {
      this.loadingAktivitas = true;
      let now = this.dateNow();
      let {startOfMonth, endOfMonth} = getStartAndEndOfMonth(now)

      this.$http.get('/kmi/kaldik',{
        params:{
          or:[
            `(tanggal_mulai>='${startOfMonth}' AND tanggal_mulai<='${endOfMonth}')`,
            `(tanggal_selesai>='${startOfMonth}' AND tanggal_selesai<='${endOfMonth}')`
          ]
        }
      })
        .then(response => {
          this.aktivitasBulanan = response.data;
        })
        .catch(error => {
          console.error('Error fetching monthly activities:', error);
          this.$notify.error({
            title: 'Gagal',
            message: 'Tidak dapat memuat aktivitas bulan ini.',
            position: 'bottom-right',
          });
        })
        .finally(() => {
          this.loadingAktivitas = false;
        });
    },
    fetchStudentsSummary() {
      this.loadingSantri = true;
      this.$http.get('/data/santri-kelas/total-santri')
        .then(response => {
          this.santriPerKelas = response.data
        })
        .catch(error => {
          console.error('Error fetching student summary:', error);
          this.$notify.error({
            title: 'Gagal',
            message: 'Tidak dapat memuat data santri.',
            position: 'bottom-right',
          });
        })
        .finally(() => {
          this.loadingSantri = false;
        });
    },
  },
};
</script>

<style scoped>
.box-card {
  min-height: 350px;
}

:deep(.el-card__header) {
  background-color: #fafafa;
  border-bottom: 1px solid #f1f5f9;
}

:deep(.el-table) {
  --el-table-header-bg-color: #f8fafc;
  --el-table-header-text-color: #475569;
}
</style>
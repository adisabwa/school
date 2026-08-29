<template>
  <div class="sm:px-3"> 
      <div class="bg-white border border-[#e2e8f0] py-4 rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.02)] flex flex-col md:flex-row gap-4 items-center">
        <div class="w-full md:flex-1 flex gap-3 items-center">
          <el-button v-if="role == 'admin'"
            @click="createNew"
            class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-[#4f46e5] hover:bg-[#4338ca] rounded-xl transition-all shadow-sm"
          >
            <icons icon="mdi:plus" class="w-3.5 h-3.5" />
            Pasang Baru
          </el-button>
          <div class="relative flex-1">
            <icons icon="mdi:search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-[#64748b]" />
            <input
              id="search-input"
              type="text"
              placeholder="Cari posisi kerja, nama perusahaan, atau lokasi penempatan..."
              v-model="searchTerm"
              class="w-full pl-11 pr-4 py-2.5 bg-[#f8fafc] border border-[#e2e8f0] rounded-xl text-sm text-[#1e293b] placeholder-[#64748b]/60 focus:bg-white focus:ring-4 focus:ring-indigo-100/45 focus:border-[#4f46e5] transition-all outline-none"
            />
          </div>
        </div>

        <div class="flex flex-wrap gap-1 md:w-auto shrink-0">
          <div class="flex items-center gap-2 bg-[#f8fafc] border border-[#e2e8f0] rounded-xl px-3.5 py-2 text-xs text-[#64748b] hover:bg-white transition-all">
            <icons icon="iconamoon:options" class="w-3.5 h-3.5 text-[#64748b] flex-none" />
            <span class="font-semibold text-slate-700">Tipe:</span>
            <el-select
              id="filter-type-select"
              v-model="filterType"
              class="bg-transparent border-none outline-none font-bold text-[#1e293b] cursor-pointer w-[160px]"
            >
              <el-option value="all" label="Semua Tipe" />
              <el-option value="full-time" label="Full-time" />
              <el-option value="part-time" label="Part-time" />
              <el-option value="contract" label="Kontrak (Contract)" />
              <el-option value="internship" label="Magang (Internship)" />
              <el-option value="remote" label="Remote" />
            </el-select>
          </div>

          <div class="flex items-center gap-2 bg-[#f8fafc] border border-[#e2e8f0] rounded-xl px-3.5 py-2 text-xs text-[#64748b] hover:bg-white transition-all">
            <icons icon="mdi:filter" class="flex-none w-3.5 h-3.5 text-[#64748b]" />
            <span class="font-semibold text-slate-700">Status:</span>
            <el-select
              id="filter-status-select"
              v-model="filterStatus"
              class="flex-none bg-transparent border-none outline-none font-bold text-[#1e293b] cursor-pointer w-[130px]"
            >
              <el-option value="all" label="Semua Status" />
              <el-option value="active" label="Aktif (Buka)" />
              <el-option value="closed" label="Ditutup" />
            </el-select>
          </div>
        </div>
      </div>

      <div class="flex justify-between items-center">
        <h2 class="text-sm font-bold text-[#1e293b] uppercase tracking-wider flex items-center gap-2">
          Daftar Lowongan Kerja ({{ filtered.length }})
          <span v-if="filtered.length !== jobs.length" class="text-xs text-[#64748b] font-normal capitalize">
            (tersaring dari {{ jobs.length }})
          </span>
        </h2>
      </div>

      <div v-if="loading" class="py-20 flex flex-col items-center justify-center gap-1 .5 bg-white border border-[#e2e8f0] rounded-2xl">
        <div class="w-9 h-9 rounded-full border-4 border-[#eef2ff] border-t-[#4f46e5] animate-spin"></div>
        <p class="text-sm font-semibold text-[#64748b]">Memuat data lowongan kerja...</p>
      </div>

      <div v-else-if="filtered.length === 0" class="bg-white border border-[#e2e8f0] p-12 rounded-3xl text-center max-w-lg mx-auto shadow-[0_1px_3px_rgba(0,0,0,0.01)] my-3">
        <div class="w-16 h-16 rounded-2xl bg-indigo-50/50 flex items-center justify-center text-indigo-400 mx-auto mb-4 border border-indigo-100/50">
          <icons icon="mdi:inbox" class="w-8 h-8" />
        </div>
        <h3 class="text-lg font-extrabold text-slate-900 tracking-tight mb-1">Lowongan Tidak Ditemukan</h3>
        <p class="text-sm text-[#64748b] leading-relaxed mb-6">
          Tidak ditemukan lowongan pekerjaan yang cocok dengan kata kunci atau kriteria penyaringan Anda saat ini.
        </p>
        <div class="flex justify-center gap-1.5">
          <el-button
            v-if="searchTerm || filterType !== 'all' || filterStatus !== 'all'"
            @click="resetFilters"
            class="px-4 py-2 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 rounded-xl transition-all border border-[#e2e8f0] shadow-sm"
          >
            Reset Filter
          </el-button>
        </div>
      </div>

      <div v-else class="grid gap-6 grid-cols-[repeat(auto-fit,_minmax(300px,_1fr))] w-full">
        <LowonganCard
          v-for="job in filtered"
          :key="job.id"
          :job="job"
          @select="handleSelect"
          @edit="handleStartEdit"
          @delete="fetchJobs"
        />
      </div>
  </div>
</template>

<script>
import LowonganCard from './LowonganCard.vue'; // Sesuaikan path komponen LowonganCard Anda
import { mapState } from 'pinia'

export default {
  name: 'LowonganListView',
  components: {
    LowonganCard,
  },
  data() {
    return {
      view: 'list', // 'list' | 'detail' | 'form'
      searchTerm: '',
      filterType: 'all',
      filterStatus: 'active',
      selected: null, // Untuk detail lowongan
      jobToEdit: null, // Untuk form edit lowongan
      loading: false,
      jobs: [],
    };
  },
  computed: {
    ...mapState(useAuthStore, ['role']),
    // Memindahkan logika filtering React ke Computed Properties Vue (lebih efisien & reactive)
    filtered() {
      if (isEmpty(this.searchTerm) && this.filterType === 'all' && this.filterStatus === 'all') {
        return this.jobs;
      }
      return this.jobs.filter((job) => {
        const matchesSearch =
          job?.nama_lowongan?.toLowerCase()?.includes(this.searchTerm.toLowerCase()) ||
          job?.perusahaan?.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
          job?.alamat_lowongan?.toLowerCase().includes(this.searchTerm.toLowerCase());

        const matchesType =
          this.filterType === 'all' || job.type === this.filterType;

        const matchesStatus =
          this.filterStatus === 'all' || job.status === this.filterStatus;

        return matchesSearch && matchesType && matchesStatus;
      });
    },
  },
  methods: {
    resetFilters() {
      this.searchTerm = '';
      this.filterType = 'all';
      this.filterStatus = 'all';
    },
    handleSelect(job) {
      this.selected = job;
      this.$router.push({ name: 'lowongan-detail', query: { id: job.id } });
    },
    handleStartEdit(job) {
      this.jobToEdit = job;
      this.$router.push({ name: 'lowongan-form', query: { id: job.id } });
    },
    createNew() {
      this.jobToEdit = null;
      this.$router.push({ name: 'lowongan-form' });
    },
    fetchJobs() {
      this.loading = true;
      this.$http.get('lowongan',{
        params:{

        }
      }).then((response) => {
        this.jobs = response.data;
        this.loading = false;
      }).catch(() => {
        this.loading = false;
      });
    }
  },
  created() {
    this.fetchJobs()
  },
  
};
</script>
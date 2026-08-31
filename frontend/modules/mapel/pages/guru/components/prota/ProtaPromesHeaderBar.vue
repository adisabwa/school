<template>
  <div class="print:hidden bg-slate-900 text-white rounded-xl p-6 shadow-xs border border-slate-800 space-y-4">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-4">
      <div class="space-y-1">
        <div class="flex items-center text-[var(--color-main-400)] font-bold text-xs uppercase tracking-wider">
          <icons icon="mdi:calendar-multiselect" class="w-4 h-4 text-[var(--color-main-300)]" />
          <span>Modul Perencanaan Tahunan & Semester</span>
        </div>
        <h2 class="text-xl font-bold text-white tracking-tight">
          Program Tahunan (PROTA) & Program Semester (PROMES)
        </h2>
        <p class="text-xs text-slate-400 max-w-xl">
          Susun pembagian alokasi Jam Pelajaran (JP) secara terstruktur per minggu dan bulan sesuai kalender pendidikan Kurikulum Merdeka.
        </p>
      </div>

      <div class="flex flex-wrap items-center gap-2" >
        <el-button
          type="primary"
          class="!font-medium !text-xs"
          @click="$emit('print')"
        >
          <icons icon="mdi:printer" class="w-4 h-4" />
          Cetak / PDF
        </el-button>

        <el-button
          type="success"
          class="!font-medium !text-xs"
          @click="$emit('export-word')"
        >
          <icons icon="mdi:download" class="w-4 h-4" />
          Unduh Word (.docx)
        </el-button>
      </div>
    </div>

    <!-- Controls Bar: Subject Selection & AI Generator Button -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-slate-800/80 p-3 rounded-lg border border-slate-700/80">
      <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
        <span class="text-xs font-semibold text-slate-300 shrink-0">Pilih Mapel &nbsp; : &nbsp; </span>
        <floating-select
          v-model:value="localSelectedSubjectId"
          class="w-full sm:w-64"
          :options="subjects?.map(s => {
            return {
              label: `${s.nama_mapel} (Kelas ${s.tingkat})`,
              value: s.id
            }
          })" />

        <el-tag v-if="activeSubject" type="warning" effect="dark" class="!font-bold">
          ⚡ Beban Belajar: {{ jam }} JP / Minggu ({{ activeSubject.pertemuan || 2 }} Pertemuan x {{ activeSubject.jam_per_pertemuan || activeSubject.jam || 3 }} JP)
        </el-tag>
      </div>
    </div>

    <el-alert
      v-if="aiError"
      :title="aiError"
      type="error"
      show-icon
      :closable="false"
      class="!bg-rose-950/60 !border-rose-800 !text-rose-200"
    />

    <!-- Tab Toggle Buttons -->
    <div class="flex items-center space-x-2 pt-1 border-t border-slate-800">
      <el-button
        :type="activeSubTab === 'prota' ? 'primary' : 'info'"
        :plain="activeSubTab !== 'prota'"
        class="!font-bold !text-xs"
        @click="$emit('update:activeSubTab', 'prota')"
      >
        <icons icon="mdi:table" class="w-4 h-4" />
        Program Tahunan (PROTA)
      </el-button>

      <el-button
        :type="activeSubTab === 'promes' ? 'primary' : 'info'"
        :plain="activeSubTab !== 'promes'"
        class="!font-bold !text-xs"
        @click="$emit('update:activeSubTab', 'promes')"
      >
        <icons icon="mdi:calendar-month-outline" class="w-4 h-4" />
        Program Semester (PROMES)
      </el-button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProtaPromesHeaderBar',
  props: {
    subjects: {
      type: Array,
      default: () => []
    },
    selectedSubjectId: {
      type: String,
      default: ''
    },
    activeSubject: {
      type: Object,
      default: null
    },
    activeSubTab: {
      type: String,
      default: 'prota'
    },
    loadingAI: {
      type: Boolean,
      default: false
    },
    aiError: {
      type: String,
      default: null
    }
  },
  emits: [
    'update:selectedSubjectId',
    'update:activeSubTab',
    'generate-ai',
    'print',
    'export-word'
  ],
  data(){
    return {
      localSelectedSubjectId: ''
    }
  },
  watch:{
    selectedSubjectId:{
      immediate: true,
      handler(val){
        console.log(val)
        this.localSelectedSubjectId = val
      }
    },
    localSelectedSubjectId(val){
      this.$emit('update:selectedSubjectId', val)
    }
  },
  computed: {
    jam() {
      if (!this.activeSubject) return 0;
      return (
        this.activeSubject.jam ||
        (this.activeSubject.pertemuan || 2) * (this.activeSubject.jam_per_pertemuan || activeSubject.jam || 3)
      );
    }
  }
};
</script>
<template>
  <div class="space-y-3">
    <!-- Intro Box -->
    <el-card shadow="never" class="!rounded-xl !border-slate-200">
      <div class="space-y-2">
        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
          <icons icon="mdi:checkbox-marked-outline" class="w-5 h-5 text-[var(--color-main-600)]" />
          <span>Pengaturan Jenis Asesmen & Profil Pelajar Pancasila</span>
        </h2>
        <p class="text-xs text-slate-500">
          Pilih teknik dan instrumen penilaian yang akan dimasukkan ke dalam RPP / Modul Ajar Anda.
          Gunakan fitur AI di bawah untuk mendapatkan rekomendasi asesmen adaptif sesuai materi dan TP Anda.
        </p>
      </div>
    </el-card>

    <!-- Adaptive Assessment Recommendation Generator Banner -->
    <div class="bg-slate-900 text-white rounded-xl p-6 shadow-xs border border-slate-800 space-y-2">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-2">
        <div class="space-y-1">
          <div class="flex items-center space-x-2 text-[var(--color-main-300)] font-bold text-xs uppercase tracking-wider">
            <icons icon="mdi:lightbulb-on-outline" class="w-4 h-4 text-amber-400" />
            <span>Saran Asesmen Adaptif (AI Engine)</span>
          </div>
          <h3 class="text-lg font-bold text-white">
            Rekomendasi Asesmen Berdasarkan Tujuan Pembelajaran (TP)
          </h3>
          <p class="text-xs text-slate-400 max-w-2xl">
            Sistem akan menganalisis materi <strong>{{ localSubject?.materi }}</strong> dan {{ localSubject?.materials?.length }} sub-materi TP Anda untuk menyarankan jenis asesmen yang paling efektif (Formatif, Sumatif, Proyek, Portofolio, Observasi) beserta alasannya.
          </p>
        </div>

        <el-button
          type="primary"
          class="!font-medium !text-xs shrink-0"
          :loading="loadingSuggestions"
          @click="handleGenerateSuggestions"
        >
          <icons  v-if="!loadingSuggestions" icon="mdi:wand" class="w-4 h-4 text-amber-300" />
          <span>{{ suggestions ? 'Regenerate Saran Adaptif' : 'Dapatkan Saran Asesmen Adaptif' }}</span>
        </el-button>
      </div>

      <el-alert
        v-if="suggestionError"
        type="error"
        show-icon
        :closable="false"
        class="!bg-rose-950/60 !border-rose-800/80 !text-rose-200"
      >
        <template #title>
          <span>{{ suggestionError }}</span>
        </template>
      </el-alert>

      <!-- Display Suggested Recommendations -->
      <div v-if="suggestions" class="pt-1">
        <!-- Overview reasoning -->
        <div class="bg-[var(--color-main-950)] border-solid border border-[var(--color-main-800)] rounded-lg p-4 space-y-1.5">
          <div class="flex items-center gap-2 text-[var(--color-main-300)] text-xs font-bold uppercase tracking-wide">
            <icons icon="mdi:sparkles" class="w-4 h-4 text-amber-400" />
            <span>Strategi Evaluasi Terintegrasi</span>
          </div>
          <p class="text-xs text-slate-200 leading-relaxed">
            {{ suggestions.overviewReasoning }}
          </p>
        </div>

        <!-- Recommendations Grid Header -->
        <div class="flex items-center justify-between mt-4">
          <span class="text-xs font-bold text-[var(--color-main-200)] uppercase tracking-wider">
            Saran Jenis Asesmen Disajikan ({{ suggestions.recommendations ? suggestions.recommendations.length : 0 }} Rekomendasi):
          </span>
          <el-button
            type="warning"
            plain
            size="small"
            class="!font-bold !text-xs"
            @click="handleApplyAllSuggestions"
          >
            <icons icon="mdi:check-circle-outline" class="w-3.5 h-3.5" />
            Terapkan Semua Saran AI
          </el-button>
        </div>

        <!-- Recommendation Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-2">
          <div
            v-for="(rec, idx) in suggestions.recommendations"
            :key="idx"
            :class="[
              'bg-slate-800/90 border rounded-lg p-4 space-y-2.5 transition relative flex flex-col justify-between',
              appliedIndexes.has(idx) ? 'border-emerald-500/80 bg-slate-800' : 'border-slate-700 hover:border-[var(--color-main-500)]/60'
            ]"
          >
            <div class="space-y-2">
              <div class="flex items-center justify-between gap-2">
                <el-tag :type="getBadgeType(rec.category)" size="small" effect="dark" class="!font-bold !text-[10px] uppercase">
                  {{ getCategoryLabel(rec.category) }}
                </el-tag>

                <el-tag v-if="appliedIndexes.has(idx)" type="success" size="small" class="!font-bold !text-[11px]">
                  <span class="flex items-center"><icons icon="mdi:check" class="w-3 h-3 inline mr-1" /> Sudah Diterapkan</span>
                </el-tag>
              </div>

              <h4 class="text-sm font-bold text-white leading-snug">
                {{ rec.assessmentType }}
              </h4>

              <div class="bg-slate-900/80 p-2.5 rounded border border-slate-700/80 text-xs text-slate-300 space-y-1">
                <span class="text-[10px] uppercase font-bold text-[var(--color-main-300)] block">
                  Mengapa Disarankan?
                </span>
                <p class="leading-relaxed text-[11px]">
                  {{ rec.explanation }}
                </p>
              </div>

              <div v-if="rec.recommendedInstruments && rec.recommendedInstruments.length > 0" class="space-y-1">
                <span class="text-[10px] uppercase font-semibold text-slate-400 block">
                  Instrumen Disarankan:
                </span>
                <div class="flex flex-wrap gap-1">
                  <el-tag
                    v-for="(inst, iIdx) in rec.recommendedInstruments"
                    :key="iIdx"
                    type="info"
                    effect="plain"
                    class="!text-[10px] font-bold"
                  >
                    {{ inst }}
                  </el-tag>
                </div>
              </div>
            </div>

            <div class="pt-2">
              <el-button
                :type="appliedIndexes.has(idx) ? 'info' : 'primary'"
                class="w-full !font-semibold !text-xs"
                size="small"
                @click="applyRecommendationToConfig(rec, idx)"
              >
                <span class="flex items-center">
                  <icons :icon="appliedIndexes.has(idx) ? 'mdi:check' : 'mdi:plus-circle-outline'" class="w-3.5 h-3.5" />
                  {{ appliedIndexes.has(idx) ? 'Terapkan Ulang' : 'Terapkan Saran Ini' }}
                </span>
              </el-button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Profil Pelajar Pancasila Selection -->
    <el-card shadow="never" class="!rounded-xl !border-slate-200">
      <div class="space-y-3">
        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center justify-between">
          <span class="flex items-center gap-2 text-[var(--color-main-700)]">
            <icons icon="mdi:shield-check-outline" class="w-4 h-4" />
            <span>Dimensi Profil Pelajar Pancasila</span>
          </span>
          <span class="text-[11px] text-slate-400 font-normal normal-case">Pilih yang relevan</span>
        </label>

        <div class="flex flex-wrap gap-2 pt-1">
          <el-button
            v-for="item in pancasilaOptions"
            :key="item"
            :class="['!font-medium m-0 py-3 rounded-md',
              localSubject?.pancasila?.includes(item) ? 'bg-[var(--color-main-700)] text-white' : 'bg-slate-50 text-slate-600']"
            size="small"
            @click="togglePancasila(item)"
          >
            {{ localSubject?.pancasila?.includes(item) ? '✓ ' : '+ ' }}
            {{ item }}
          </el-button>
        </div>
      </div>
    </el-card>

    <!-- Asesmen 1: Diagnostik -->
    <el-card shadow="never" class="!rounded-xl !border-slate-200">
      <div class="space-y-2">
        <div class="flex items-center justify-between border-b border-slate-100">
          <div class="flex items-center space-x-3">
            <el-checkbox
              :model-value="assessmentConfig.diagnostic ? assessmentConfig.diagnostic.enabled : false"
              @change="(val) => updateCategoryEnabled('diagnostic', val)"
            />
            <span class="font-bold text-slate-900 text-sm">
              1. Asesmen Diagnostik (Awal Pembelajaran)
            </span>
          </div>
          <el-tag type="primary" size="small" effect="light" class="!font-semibold">
            Mengecek Kesiapan Awal
          </el-tag>
        </div>

        <div v-if="assessmentConfig.diagnostic && assessmentConfig.diagnostic.enabled" class="space-y-3 pt-1">
          <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide">
            Bentuk Instrumen Diagnostik:
          </label>
          <div class="flex flex-wrap gap-2">
            <el-button
              v-for="opt in asesmenOptions.diagnostic"
              :key="opt"
              :class="['!font-medium m-0 py-3 rounded-md',
                isTypeSelected('diagnostic', opt) ? 'bg-sky-700 text-white' : 'bg-slate-50 text-slate-600']"
              size="small"
              @click="toggleOption('diagnostic', opt)"
            >
              {{ isTypeSelected('diagnostic', opt) ? '✓ ' : '+ ' }}
              {{ opt }}
            </el-button>
          </div>

          <div>
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1">
              Catatan / Keterangan Khusus Asesmen Diagnostik:
            </label>
            <el-input
              type="textarea" rows="4"
              :model-value="assessmentConfig.diagnostic.notes"
              @input="(val) => updateCategoryNotes('diagnostic', val)"
              placeholder="Misal: Mengecek pemahaman prasyarat operasi hitung aljabar..."
            />
          </div>
        </div>
      </div>
    </el-card>

    <!-- Asesmen 2: Formatif -->
    <el-card shadow="never" class="!rounded-xl !border-slate-200">
      <div class="space-y-2">
        <div class="flex items-center justify-between border-b border-slate-100">
          <div class="flex items-center space-x-3">
            <el-checkbox
              :model-value="assessmentConfig.formative ? assessmentConfig.formative.enabled : false"
              @change="(val) => updateCategoryEnabled('formative', val)"
            />
            <span class="font-bold text-slate-900 text-sm">
              2. Asesmen Formatif (Proses Pembelajaran & Proyek)
            </span>
          </div>
          <el-tag type="success" size="small" effect="light" class="!font-semibold">
            Memantau Perkembangan
          </el-tag>
        </div>

        <div v-if="assessmentConfig.formative && assessmentConfig.formative.enabled" class="space-y-3 pt-1">
          <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide">
            Bentuk Instrumen Formatif:
          </label>
          <div class="flex flex-wrap gap-2">
            <el-button
              v-for="opt in asesmenOptions.formative"
              :key="opt"
              :class="['!font-medium m-0 py-3 rounded-md',
                isTypeSelected('formative', opt) ? 'bg-emerald-700 text-white' : 'bg-slate-50 text-slate-600']"
              size="small"
              @click="toggleOption('formative', opt)"
            >
              {{ isTypeSelected('formative', opt) ? '✓ ' : '+ ' }}
              {{ opt }}
            </el-button>
          </div>

          <div>
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1">
              Catatan / Keterangan Khusus Asesmen Formatif:
            </label>
            <el-input
              type="textarea" rows="4"
              :model-value="assessmentConfig.formative.notes"
              @input="(val) => updateCategoryNotes('formative', val)"
              placeholder="Misal: Penilaian keaktifan saat diskusi kelompok dan pengerjaan LKPD..."
            />
          </div>
        </div>
      </div>
    </el-card>

    <!-- Asesmen 3: Sumatif -->
    <el-card shadow="never" class="!rounded-xl !border-slate-200">
      <div class="space-y-2">
        <div class="flex items-center justify-between border-b border-slate-100">
          <div class="flex items-center space-x-3">
            <el-checkbox
              :model-value="assessmentConfig.summative ? assessmentConfig.summative.enabled : false"
              @change="(val) => updateCategoryEnabled('summative', val)"
            />
            <span class="font-bold text-slate-900 text-sm">
              3. Asesmen Sumatif (Akhir Pembelajaran / Produk / Portofolio)
            </span>
          </div>
          <el-tag type="warning" size="small" effect="light" class="!font-semibold">
            Evaluasi Hasil Belajar
          </el-tag>
        </div>

        <div v-if="assessmentConfig.summative && assessmentConfig.summative.enabled" class="space-y-3 pt-1">
          <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide">
            Bentuk Instrumen Sumatif:
          </label>
          <div class="flex flex-wrap gap-2">
            <el-button
              v-for="opt in asesmenOptions.summative"
              :key="opt"
              :class="['!font-medium m-0 py-3 rounded-md',
                isTypeSelected('summative', opt) ? 'bg-orange-400 text-white' : 'bg-slate-50 text-slate-600']"
              size="small"
              @click="toggleOption('summative', opt)"
            >
              {{ isTypeSelected('summative', opt) ? '✓ ' : '+ ' }}
              {{ opt }}
            </el-button>
          </div>

          <div>
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1">
              Catatan / Keterangan Khusus Asesmen Sumatif:
            </label>
            <el-input
              type="textarea" rows="4"
              :model-value="assessmentConfig.summative.notes"
              @input="(val) => updateCategoryNotes('summative', val)"
              placeholder="Misal: Soal tes essay 3 nomor konteks kehidupan sehari-hari..."
            />
          </div>
        </div>
      </div>
    </el-card>

    <!-- Bottom Action Section -->
    <div class="bg-slate-900 rounded-xl p-6 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs border border-slate-800">
      <div>
        <h3 class="font-bold text-base">Semua Pengaturan Asesmen Sudah Siap!</h3>
        <p class="text-xs text-slate-400 mt-1">
          Sistem AI akan menyusun RPP / Modul Ajar lengkap berstandar Kemendikbudristek beserta instrumen asesmen dan rubriknya.
        </p>
      </div>

      <div class="flex items-center space-x-3 shrink-0">
        <el-button
          plain
          class="!font-medium"
          @click="$emit('back')"
        >
        <icons icon="mdi:arrow-left" class="w-4 h-4" />
        </el-button>

        <el-button
          type="primary"
          :disabled="!isSelected"
          :class="[isSelected ? '!bg-[var(--color-main-600)] hover:!bg-[var(--color-main-700)]' : 'bg-slate-400 hover:bg-slate-600',
          '!font-bold !text-sm']"
          :loading="isGenerating"
          @click="$emit('generate-rpp');"
        >
          <icons v-if="!isGenerating" icon="mdi:sparkles" class="w-4 h-4 text-white" />
          <span>{{ isGenerating ? 'Menyusun RPP Lengkap...' : 'Buat RPP Otomatis Sekarang' }}</span>
        </el-button>
      </div>
    </div>
  </div>
</template>

<script>
const PANCASILA_PROFILES_OPTIONS =  [
  'Beriman, Bertakwa kepada Tuhan YME, dan Berakhlak Mulia',
  'Bernalar Kritis',
  'Gotong Royong',
  'Kreatif',
  'Mandiri',
  'Berkebinekaan Global',
];

export default {
  name: 'Step3Asesmen',
  props: {
    subjectInfo: {
      type: Object,
      default: () => ({ topic: '' })
    },
    assessmentConfig: {
      type: Object,
      default: () => ({
        diagnostic: { enabled: true, types: [], notes: '' },
        formative: { enabled: true, types: [], notes: '' },
        summative: { enabled: true, types: [], notes: '' }
      })
    },
    isGenerating: {
      type: Boolean,
      default: false
    }
  },
  emits: [
    'update:assessmentConfig',
    'back',
    'generate-rpp'
  ],
  watch:{
    subjectInfo:{
      deep:true,
      handler(val){
        // console.log('subject', val)
        this.localSubject = val
        this.localSubject.pancasila = PANCASILA_PROFILES_OPTIONS
      }
    },
    localSubject:{
      deep:true,
      handler(val){
        // generateNewMaterial()
        this.$emit('update:localSubject', val)
      }
    }
  },
  computed:{
    isSelected(){
      // console.log(this.assessmentConfig.diagnostic , this.assessmentConfig.formative , this.assessmentConfig.summative)
      return (this.assessmentConfig.diagnostic.enabled ? this.assessmentConfig.diagnostic.types.length > 0 : true) && 
        (this.assessmentConfig.formative.enabled ? this.assessmentConfig.formative.types.length > 0 : true) && 
        (this.assessmentConfig.summative.enabled ? this.assessmentConfig.summative.types.length > 0 : true);
    }
  },
  data() {
    return {
      localSubject:{},
      loadingSuggestions: false,
      suggestions: null,
      suggestionError: null,
      appliedIndexes: new Set(),
      pancasilaOptions: PANCASILA_PROFILES_OPTIONS || [],
      asesmenOptions:{
        diagnostic: [
          'Pertanyaan Pemantik Kesiapan Belajar',
          'Tanya Jawab Lisan Awal Bab',
          'Kuis Singkat Pilihan Ganda Awal',
          'Angket Pemetaan Gaya Belajar'
        ],
        formative: [
          'Observasi Sikap & Diskusi Kelompok',
          'Lembar Kerja Peserta Didik (LKPD)',
          'Kuis Interaktif / Latihan Soal',
          'Penilaian Antar Teman (Peer Assessment)',
          'Refleksi Diri Siswa'
        ],
        summative: [
          'Tes Pilihan Ganda Akhir Bab',
          'Tes Isian Singkat',
          'Tes Tertulis Uraian / Essay',
          'Unjuk Kerja / Presentasi Kelompok',
          'Produk / Proyek Sederhana',
          'Portofolio Hasil Belajar'
        ]
      }
    };
  },
  methods: {
    handleBefore(){
      if (!this.localSubject.pancasila) {
        this.localSubject.pancasila = []
      }
      

      ['diagnostic','formative','summative'].forEach((category) => {
        let types = this.assessmentConfig[category]?.types ?? []
        console.log(types)
        types.forEach(type => {
          if (!this.asesmenOptions[category].includes(type)) {
            this.asesmenOptions[category].push(type)
          }
        })
      });
    },
    async handleGenerateSuggestions() {
      this.loadingSuggestions = true;
      this.suggestionError = null;
      this.$http.post('mapel/materi/saran-asesmen', window.jsonToFormData(this.localSubject))
        .then(res => {
        this.loadingSuggestions = false;
          let data = res?.data
          this.suggestions = data
        })
        .catch(err => {
        this.loadingSuggestions = false;
          let res = err?.response
          let message = res?.data?.message
          this.suggestionError = message || 'Terjadi kesalahan saat memproses saran AI.';

        })
    },
    applyRecommendationToConfig(item, idx) {
      const category = item.category || 'formative';
      const configCopy = JSON.parse(JSON.stringify(this.assessmentConfig));
      const currentCategory = configCopy[category] || { enabled: true, types: [], notes: '' };
      const currentTypes = [...(currentCategory.types || [])];

      if (!currentTypes.includes(item.assessmentType)) {
        currentTypes.push(item.assessmentType);
      }

      if (item.recommendedInstruments && Array.isArray(item.recommendedInstruments)) {
        item.recommendedInstruments.forEach((inst) => {
          if (!currentTypes.includes(inst)) {
            currentTypes.push(inst);
          }
          if (!this.asesmenOptions[category].includes(inst)) {
            this.asesmenOptions[category].push(inst)
          }
        });
      }

      const existingNotes = currentCategory.notes ? currentCategory.notes + ' ' : '';
      const updatedNotes = existingNotes.includes(item.explanation)
        ? currentCategory.notes
        : `${existingNotes}[Saran AI]: ${item.explanation}`.trim();

      configCopy[category] = {
        enabled: true,
        types: currentTypes,
        notes: updatedNotes
      };

      this.$emit('update:assessmentConfig', configCopy);

      const newIndexes = new Set(this.appliedIndexes);
      newIndexes.add(idx);
      this.appliedIndexes = newIndexes;
    },
    handleApplyAllSuggestions() {
      if (!this.suggestions || !this.suggestions.recommendations) return;
      const allIndexes = new Set();
      this.suggestions.recommendations.forEach((rec, idx) => {
        this.applyRecommendationToConfig(rec, idx);
        allIndexes.add(idx);
      });
      this.appliedIndexes = allIndexes;
    },
    toggleOption(category, item) {
      const configCopy = JSON.parse(JSON.stringify(this.assessmentConfig));
      const currentTypes = configCopy[category]?.types || [];
      const exists = currentTypes.includes(item);
      const updatedTypes = exists
        ? currentTypes.filter((t) => t !== item)
        : [...currentTypes, item];

      configCopy[category] = {
        ...configCopy[category],
        types: updatedTypes
      };

      this.$emit('update:assessmentConfig', configCopy);
    },
    updateCategoryEnabled(category, enabled) {
      const configCopy = JSON.parse(JSON.stringify(this.assessmentConfig));
      configCopy[category] = {
        ...configCopy[category],
        enabled
      };
      this.$emit('update:assessmentConfig', configCopy);
    },
    updateCategoryNotes(category, notes) {
      const configCopy = JSON.parse(JSON.stringify(this.assessmentConfig));
      configCopy[category] = {
        ...configCopy[category],
        notes
      };
      this.$emit('update:assessmentConfig', configCopy);
    },
    togglePancasila(item) {
      const current = [...this.localSubject.pancasila];
      const updated = current.includes(item)
        ? current.filter((p) => p !== item)
        : [...current, item];
      this.localSubject.pancasila = [...updated]
    },
    isTypeSelected(category, opt) {
      return this.assessmentConfig[category]?.types?.includes(opt) || false;
    },
    getBadgeType(category) {
      switch (category) {
        case 'diagnostic':
          return 'primary';
        case 'summative':
          return 'warning';
        case 'formative':
        default:
          return 'success';
      }
    },
    getCategoryLabel(category) {
      switch (category) {
        case 'diagnostic':
          return 'Diagnostik';
        case 'summative':
          return 'Sumatif';
        case 'formative':
        default:
          return 'Formatif';
      }
    }
  },
  mounted() {
    this.localSubject = this.subjectInfo;
    this.handleBefore()
  }
};
</script>
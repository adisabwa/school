<template>
  <div class="max-w-5xl mx-auto space-y-3">
    <!-- Top Action & Export Bar (Hidden on Print) -->      

    <div class="print:hidden bg-slate-900 text-white rounded-2xl p-5 shadow-lg border border-slate-800">
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
          <div class="flex items-center space-x-2 text-emerald-400 font-bold text-xs uppercase tracking-wider mb-1">
            <icons icon="mdi:check-circle-outline" class="w-4 h-4" />
            <span>Dokumen RPP & Asesmen Siap Digunakan</span>
          </div>
          <h2 class="text-xl font-black text-white tracking-tight">
            {{ rpp?.subjectInfo?.nama_mapel }} — {{ rpp?.subjectInfo?.materi }}
          </h2>
          <p class="text-xs text-slate-400 mt-1">
            Lengkap dengan RPP/Modul Ajar, Alur Pertemuan, Lembar Asesmen Diagnostik, dan Lembar Asesmen Sumatif.
          </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 *:m-0">
          <!-- Toggle Edit Mode -->
          <el-button
            :type="isEditingMode ? 'warning' : 'info'"
            size="small"
            class="!font-semibold !text-xs h-full"
            @click="copyHtml(); isEditingMode = !isEditingMode;"
          >
            <icons :icon="isEditingMode ? 'mdi:check' : 'mdi:pencil-outline'" class="w-4 h-4" />
            <span>{{ isEditingMode ? 'Selesai Edit' : 'Edit Teks Live' }}</span>
          </el-button>

          <!-- Print / Save PDF -->
          <el-button
            type="primary"
            size="small"
            class="!font-bold !text-xs h-full !bg-indigo-600 hover:!bg-indigo-700"
            @click="handlePrintPDF"
          >
            <icons icon="mdi:printer" class="w-4 h-4" />
            <span>Cetak / PDF</span>
          </el-button>

          <!-- Export All Word -->
          <el-button
            type="primary"
            size="small"
            class="!font-bold !text-xs h-full !bg-blue-600 hover:!bg-blue-700"
            @click="handleExportWord"
          >
            <icons icon="mdi:download" class="w-4 h-4" />
            <span>Unduh Word (.doc)</span>
          </el-button>

          <!-- Save to History -->
          <el-button
            type="success"
            size="small"
            class="!font-bold !text-xs h-full"
            @click="handleSave"
          >
            <icons icon="mdi:content-save-outline" class="w-4 h-4" />
            <span>{{ saveSuccess ? 'Tersimpan!' : 'Simpan Dokumen' }}</span>
          </el-button>
        </div>
      </div>

      <!-- Tab Filter Nav for Focused View & Printing -->
      <div class="pt-3 text-xs text-slate-400 font-semibold mr-1 mb-2">Tampilan Dokumen:</div>
      <div class="border-t border-slate-800 flex flex-wrap items-center gap-2">

        <el-button
          v-for="tab in docTabs"
          :key="tab.id"
          size="small"
          :class="[
            activeDocView === tab.id ? tab.activeClass : 'bg-transparent text-slate-400 border-slate-300',
            '!font-bold !text-xs m-0 py-2 rounded-md h-full'
          ]"
          @click="activeDocView = tab.id"
        >
          <icons :icon="tab.icon" class="w-3.5 h-3.5" />
          <span>{{ tab.label }}</span>
        </el-button>

        <!-- Quick standalone docx download buttons -->
        <div class="ml-auto flex items-center gap-2 *:m-0">
          <el-button
            size="small"
            class="!text-[11px] !bg-cyan-950 !text-cyan-300 !border-cyan-800 hover:!bg-cyan-900"
            @click="handleExportDiagnostik"
          >
            <icons icon="mdi:download" class="w-3 h-3" />
            <span>Word Diagnostik</span>
          </el-button>
          <el-button
            size="small"
            class="!text-[11px] !bg-rose-950 !text-rose-300 !border-rose-800 hover:!bg-rose-900"
            @click="handleExportSumatif"
          >
            <icons icon="mdi:download" class="w-3 h-3" />
            <span>Word Sumatif</span>
          </el-button>
        </div>
      </div>
    </div>

    <!-- DOCUMENT PREVIEW CONTAINER (PRINT TARGET) -->
    <div id="printable-rpp" class="bg-white r ounded-2xl shadow-sm border border-slate-200 p-8 sm:p-12 text-slate-900 font-sans print:p-0 print:border-none print:shadow-none space-y-10">
      <RppMainSection
        v-if="activeDocView === 'all' || activeDocView === 'rpp'"
        :rpp="rpp"
        :is-editing-mode="isEditingMode"
        @update-array-item="handleUpdateArrayItem"
      />

      <RppDiagnostikSection
        v-if="diagnostikData && (activeDocView === 'all' || activeDocView === 'diagnostik')"
        :rpp="rpp"
        :no-lampiran="noLampiranDiagnostik"
        :diagnostik-data="diagnostikData"
        :is-editing-mode="isEditingMode"
        @update-array-item="handleUpdateArrayItem"
      />
      
      
      <RppFormativeSection
        v-if="formatifData && (activeDocView === 'all' || activeDocView === 'formatif')"
        :rpp="rpp"
        :no-lampiran="noLampiranFormatif"
        :formatif-data="formatifData"
        :is-editing-mode="isEditingMode"
        @update-array-item="handleUpdateArrayItem"
      />

      <RppSumativeSection
        v-if="sumatifData && (activeDocView === 'all' || activeDocView === 'sumatif')"
        :rpp="rpp"
        :no-lampiran="noLampiranSumatif"
        :sumatif-data="sumatifData"
        :is-editing-mode="isEditingMode"
        @update-array-item="handleUpdateArrayItem"
      />

      <RppLampiranSection
        v-if="activeDocView === 'all' || activeDocView === 'lampiran'"
        :no-lampiran="noLampiran"
        :rpp="rpp"
        :is-editing-mode="isEditingMode"
        @update-array-item="handleUpdateArrayItem"
      />
    </div>

    <!-- Bottom Nav Bar (Hidden on Print) -->
    <div class="print:hidden flex items-center justify-between pt-2 pb-6">
      <el-button
        plain
        class="!font-bold !text-sm"
        @click="$emit('back')"
      >
        <icons icon="mdi:arrow-left" class="w-4 h-4" />
        <span>Kembali Edit</span>
      </el-button>

      <el-button
        type="primary"
        class="!bg-slate-800 hover:!bg-slate-700 !font-bold !text-sm"
        @click="$emit('reset')"
      >
        <icons icon="mdi:refresh" class="w-4 h-4" />
        <span>Buat RPP Baru</span>
      </el-button>
    </div>
  </div>
</template>

<script>
// import { exportRPPToWord, exportDiagnostikToWord, exportSumatifToWord } from '../lib/docxExport';
import RppMainSection from './components-viewer/RppMainSection.vue';
import RppDiagnostikSection from './components-viewer/RppDiagnosticSection.vue';
import RppFormativeSection from './components-viewer/RppFormativeSection.vue';
import RppSumativeSection from './components-viewer/RppSummativeSection.vue';
import RppLampiranSection from './components-viewer/RppLampiranSection.vue';
import { document } from 'postcss';

export default {
  name: 'Step4RPPViewer',
  components: {
    RppMainSection,
    RppDiagnostikSection,
    RppFormativeSection,
    RppSumativeSection,
    RppLampiranSection
  },
  props: {
    rpp: {
      type: Object,
      required: true
    }
  },
  setup(){
    return {
      
    }
  },
  emits: ['update:rpp', 'save-to-history', 'reset', 'back'],
  data() {
    return {
      contentHtml:'',
      isEditingMode: false,
      saveSuccess: false,
      activeDocView: 'all',
      docTabs: [
        { id: 'all', label: 'Semua Dokumen Lengkap', icon: 'mdi:layers-outline', activeClass: 'bg-slate-200 text-slate-900 border-slate-200' },
        { id: 'rpp', label: 'Modul Ajar / RPP', icon: 'mdi:file-document-outline', activeClass: 'bg-sky-600 text-white border-sky-600' },
        { id: 'diagnostik', label: 'Lembar Asesmen Diagnostik (Awal)', icon: 'mdi:help-circle-outline', activeClass: 'bg-teal-600 text-white border-teal-600' },
        { id: 'sumatif', label: 'Lembar Asesmen Sumatif (Akhir Bab)', icon: 'mdi:trophy-outline', activeClass: 'bg-red-600 text-white border-red-600' },
        { id: 'lampiran', label: 'LKPD Formatif', icon: 'mdi:clipboard-text-outline', activeClass: 'bg-orange-600 text-white border-orange-600' }
      ]
    };
  },
  computed: {
    diagnostikData() {
      return this.rpp?.asesmenDiagnostik
    },
    formatifData() {
      return this.rpp?.asesmenFormatif
    },
    sumatifData() {
      return this.rpp?.asesmenSumatif
    },
    noLampiranDiagnostik(){
      return this.rpp?.asesmenDiagnostik ? 1 : 0
    },
    noLampiranFormatif(){
      return this.noLampiranDiagnostik + (this.rpp?.asesmenFormatif ? 1 : 0)
    },
    noLampiranSumatif(){
      return this.noLampiranFormatif + (this.rpp?.asesmenSumatif ? 1 : 0)
    },
    noLampiran(){
      return this.noLampiranSumatif + 1
    }
  },
  methods: {
    handlePrintPDF() {
      printElementById('printable-rpp',{
        paperSize: 'A4',
      })
    },
    handleExportWord() {
      let filename = `RPP ${this.rpp.subjectInfo?.nama_mapel} - Materi ${this.rpp.subjectInfo?.no ?? 1} : ${this.rpp.subjectInfo?.materi}`
      downloadHtmlAsWordById('printable-rpp', filename)
    },
    handleExportDiagnostik() {
      exportDiagnostikToWord(this.rpp);
    },
    handleExportSumatif() {
      exportSumatifToWord(this.rpp);
    },
    handleSave() {
      this.saveSuccess = false
      this.$http.post('mapel/materi/store', window.jsonToFormData({
        id: this.rpp.subjectInfo.id,
        rpp: JSON.stringify(this.rpp)
      }))
        .then(res => {
          this.saveSuccess = true
        })
    },
    handleUpdateArrayItem({ path, value }) {
      const updated = JSON.parse(JSON.stringify(this.rpp));

      setObjectValueByPath(updated, path, value)
      // if (path[0] === 'tujuanPembelajaran') {
      //   updated.tujuanPembelajaran[index] = newValue;
      // } else if (path[0] === 'pertanyaanPemantik') {
      //   updated.pertanyaanPemantik[index] = newValue;
      // } else if (path[0] === 'kegiatanPembelajaran' && path[1] && path[2] === 'activities') {
      //   const phase = path[1];
      //   updated.kegiatanPembelajaran[phase].activities[index] = newValue;
      // } else if (path[0] === 'refleksiGuruDanSiswa' && path[1] === 'refleksiGuru') {
      //   updated.refleksiGuruDanSiswa.refleksiGuru[index] = newValue;
      // } else if (path[0] === 'refleksiGuruDanSiswa' && path[1] === 'refleksiSiswa') {
      //   updated.refleksiGuruDanSiswa.refleksiSiswa[index] = newValue;
      // }

      this.$emit('update:rpp', updated);
    },
    copyHtml(){
      // let el =  window.document.getElementById('printable-rpp')
      // let htmlBody = getElementWithInlineStyles(el)
      // console.log(htmlBody)
      // this.contentHtml = htmlBody.outerHTML
    }
  }
};
</script>

<style lang="postcss" scoped>
:deep(.el-input__wrapper), :deep(.el-textarea) {
  @apply bg-orange-50 border border-solid border-orange-300
    focus-within:bg-slate-50 focus-within:border-slate-500 !important;
}
</style>
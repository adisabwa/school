<template>
  <div class="space-y-6 mx-auto">
    <!-- Top Action Header Bar -->
    <ProtaPromesHeaderBar
      :subjects="subjects"
      v-model:selectedSubjectId="selectedSubjectId"
      v-model:activeSubTab="activeSubTab"
      :activeSubject="activeSubject"
      :loadingAI="loadingAI"
      :aiError="aiError"
      @print="handlePrint"
      @export-word="handleExportWord"
    />

    <!-- Printable Document Sheet (A4 Styling) -->
    <div
      id="printable-prota-promes"
      class="bg-white rounded-xl border border-slate-200 p-8 sm:p-12 shadow-xs print:shadow-none print:p-0 print:border-none print:rounded-none text-slate-900 leading-relaxed font-sans font-normal text-xs space-y-6"
    >
      <!-- Document Header -->
      <div class="text-center space-y-1 pb-4 border-b-2 border-slate-900">
        <h1 class="text-base sm:text-lg font-black uppercase tracking-wide text-slate-900">
          {{ activeSubTab === 'prota' ? 'PROGRAM TAHUNAN (PROTA)' : 'PROGRAM SEMESTER (PROMES)' }}
        </h1>
        <h2 class="text-xs sm:text-sm font-bold text-slate-800 uppercase">
          {{ activeSubject?.curriculum === 'kurikulum_berbasis_cinta' ? 'KURIKULUM BERBASIS CINTA (KBC)' : 'KURIKULUM MERDEKA' }}
          — TAHUN AJARAN {{ activeSubject?.tahun_ajaran || '2024/2025' }}
        </h2>
      </div>

      <!-- Metadata Header Table -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs bg-slate-50 p-4 rounded-lg border border-slate-200 print:bg-transparent print:p-0 print:border-none">
        <table class="[&_td:first-child]:font-bold">
          <tbody>
            <tr>
              <td width="120px">Satuan Pendidikan</td><td width="20px">:</td> <td>{{ activeSubject?.nama_unit || 'SD / SMP / SMA Negeri' }}</td>
            </tr>
            <tr>
              <td>Mata Pelajaran</td><td>:</td> <td>{{ activeSubject?.nama_mapel }}</td>
            </tr>
            <tr>
              <td>Fase / Kelas</td><td>:</td> <td>{{ activeSubject?.fase }} / {{ activeSubject?.kelas }}</td>
            </tr>
          </tbody>
        </table>
        <table class="[&_td:first-child]:font-bold">
          <tbody>
            <tr>
              <td width="120px">Sistem Kurikulum</td><td width="20px">:</td> <td>{{ activeSubject?.curriculum === 'kurikulum_berbasis_cinta' ? 'Kurikulum Berbasis Cinta (KBC)' : 'Kurikulum Merdeka' }}</td>
            </tr>
            <tr>
              <td>Tahun Ajaran</td><td>:</td> <td>{{ activeSubject?.tahun_ajaran }}</td>
            </tr>
            <tr>
              <td>Nama Guru</td><td>:</td> <td>{{ activeSubject?.nama_guru }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- PROTA CONTENT VIEW -->
      <ProtaTable
        v-if="activeSubTab === 'prota'"
        :activeSubject="activeSubject"
        @update-jp="handleUpdateMateri"
        @select-chapter-for-rpp="handleSelectChapterForRPP"
      />

      <!-- PROMES CONTENT VIEW -->
      <PromesTable
        v-if="activeSubTab === 'promes'"
        :activeSubject="activeSubject"
        @update-weekly-jp="handleUpdatePromesWeekly"
      />

      <!-- Signatures Block -->
      <div class="grid grid-cols-2 gap-8 pt-6 text-xs border-t border-slate-200 text-slate-900">
        <div>
          <p>Mengetahui,</p>
          <p class="font-bold">Kepala Sekolah</p>
          <div class="h-16" />
          <p class="font-bold underline">{{ activeSubject?.nama_kepala_lengkap }}</p>
          <p class="text-[11px] text-slate-600">NBM. {{ activeSubject?.nbm_kepala }}</p>
        </div>

        <div class="text-right">
          <p>Kendal, {{ dateNow() }}</p>
          <p class="font-bold">Guru Mata Pelajaran</p>
          <div class="h-16" />
          <p class="font-bold underline">{{ activeSubject?.nama_guru_lengkap || activeSubject?.nama_guru }}</p>
          <p class="text-[11px] text-slate-600">NBM. {{ activeSubject?.nbm_guru }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'pinia'
import ProtaPromesHeaderBar from './components/prota/ProtaPromesHeaderBar.vue';
import ProtaTable from './components/prota/ProtaTable.vue';
import PromesTable from './components/prota/PromesTable.vue';
let year = parseInt(dateNow().substr(0,4)) + 1
let yearList = []
for (let i = year;i >= 2024;i--) {
  let l = `${i}/${i+1}`
  yearList.push({
    value:l,
    label:l,
  })
}

export default {
  name: 'ProtaPromesView',
  components: {
    ProtaPromesHeaderBar,
    ProtaTable,
    PromesTable
  },
  setup() {

    return {
      dateNow,
    }
  },
  emits: ['update-subject-chapter-jp', 'select-chapter-for-rpp'],
  data() {
    return {
      yearList:yearList,
      subjects:[],
      tahunAjaran:'2026/2027',
      activeSubTab: 'promes',
      selectedSubjectId: '',
      loadingAI: false,
      aiError: null,
      customProtaJP: {},
      customPromesDist: {},
      waitUpdate:null,
    };
  },
  computed: {
    ...mapState(useAuthStore,{
      user: 'loggedUser'
    }),
    activeSubject() {
      if (!this.subjects || this.subjects.length === 0) return null;
      return this.subjects.find((s) => s.id === this.selectedSubjectId) || this.subjects[0];
    },
  },
  watch: {
    subjects: {
      immediate: true,
      handler(newVal) {
        if (newVal && newVal.length > 0 && !this.selectedSubjectId) {
          this.selectedSubjectId = newVal[0].id;
        }
      }
    },
  },
  methods: {
    getSubject(){
      this.$http.get('mapel/materi/summary', {
        params:{
          tahun_ajaran:this.tahunAjaran,
          id_guru:this.user.id_guru,
        }
      })
      .then(res => {
        this.subjects = res?.data
        // this.selectedSubjectId = this.subjects[0].id
      })
    },
    handlePrint() {
      printElementById('printable-prota-promes',{
        paperSize: 'landscape',
      })
    },
    handleExportWord() {
      const activeDocName = this.activeSubTab === 'prota' ? 'Program_Tahunan_PROTA' : 'Program_Semester_PROMES';
      const content = document.getElementById('printable-prota-promes')?.innerText || '';
      const blob = new Blob([content], { type: 'application/msword' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `${activeDocName}_${this.activeSubject ? this.activeSubject.nama_mapel.replace(/\s+/g, '_') : 'Mapel'}.doc`;
      a.click();
      URL.revokeObjectURL(url);
    },
    handleUpdateMateri(subject) {
      let materi = subject?.materi
      let forms = []
      materi.forEach(m => {
        forms.push({
          id:m.id,
          jam:m.jam,
          pertemuan:m.pertemuan,
        })
      })
      clearTimeout(this.waitUpdate)
      this.waitUpdate = setTimeout(() => {
        this.$http.post('mapel/materi/store_many', window.jsonToFormData(forms))
        .then(res => {
          this.getSubject()
        })
      }, 1500); // 1000 milliseconds = 1 second
      
    },
    handleSelectChapterForRPP(chapter) {
      if (this.activeSubject) {
        this.$emit('select-chapter-for-rpp', { subject: this.activeSubject, chapter });
      }
    }
  },
  mounted(){
    this.tahunAjaran = yearList[1].value
    this.getSubject()
  }
};
</script>
<template>
  <div class="space-y-2">
    <div class="print:hidden bg-[var(--color-main-50)] border border-[var(--color-main-200)] text-[var(--color-main-900)] p-3 rounded-lg text-xs flex items-center justify-between">
      <span class="font-semibold">
        ✏️ <strong>Alokasi Waktu Per Mapel / Bab Bisa Diedit:</strong> Ubah angka JP di kolom "Alokasi Waktu" untuk memperbarui total jam pelajaran secara real-time.
      </span>
    </div>

    <table class="w-full border-collapse border border-solid [&_th]:border-solid [&_td]:border-solid border-slate-400 text-left text-xs">
      <thead>
        <tr class="bg-slate-100 print:bg-slate-200 text-slate-900 font-bold border-b border-slate-400">
          <th class="p-2 border border-slate-400 text-center w-12">No</th>
          <th class="p-2 border border-slate-400 w-24">Semester</th>
          <th class="p-2 border border-slate-400">Bab / Topik Materi & Capaian Pembelajaran</th>
          <th class="p-2 border border-slate-400 text-center w-36">Alokasi Waktu (JP)</th>
          <th class="p-2 border border-slate-400 text-center w-28 print:hidden">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(entry, idx) in activeSubject?.materi || []" :key="idx" class="hover:bg-slate-50 border-b border-slate-300">
          <td class="p-2 border border-slate-400 text-center font-bold">{{ entry.no || idx + 1 }}</td>
          <td class="p-2 border border-slate-400 font-semibold text-[var(--color-main-900)]">{{ ucFirst(entry.semester) }}</td>
          <td class="p-2 border border-slate-400 space-y-1">
            <strong class="block text-slate-900">Bab {{ entry.no }}: {{ entry.materi }}</strong>
            <p class="text-[11px] text-slate-600 leading-snug">{{ entry.cp }}</p>
          </td>
          <td class="p-2 border border-slate-400 text-center font-bold text-slate-900">
            <div class="flex items-center justify-center space-x-1">
              <el-input-number
                v-model="entry.jam"
                :min="1"
                :max="100"
                @change="$emit('update-jp', activeSubject)"
                size="small"
                controls-position="right"
                class="!w-20 print:hidden"
              />
              <span class="hidden print:inline font-bold">{{ entry.jam }}</span>
              <span class="font-bold text-slate-700">JP</span>
            </div>
          </td>
          <td class="p-2 border border-slate-400 text-center print:hidden">
            <el-button
              type="primary"
              size="small"
              class="!font-bold !text-[10px]"
              @click="$emit('select-chapter-for-rpp', getMatchingChapter(entry, idx))"
            >
              <icons icon="mdi:sparkles" class="w-3 h-3 text-amber-300" />
              ⚡ Buat RPP
            </el-button>
          </td>
        </tr>

        <!-- Summary Rows -->
        <tr class="bg-slate-100 font-bold border-t-2 border-slate-900">
          <td colSpan="3" class="p-2 border border-slate-400 text-right uppercase">Total Alokasi JP Semester 1 (Ganjil)</td>
          <td colSpan="2" class="p-2 border border-slate-400 text-center text-[var(--color-main-900)] text-sm font-black">{{ sem1TotalJP }} JP</td>
        </tr>
        <tr class="bg-slate-100 font-bold">
          <td colSpan="3" class="p-2 border border-slate-400 text-right uppercase">Total Alokasi JP Semester 2 (Genap)</td>
          <td colSpan="2" class="p-2 border border-slate-400 text-center text-[var(--color-main-900)] text-sm font-black">{{ sem2TotalJP }} JP</td>
        </tr>
        <tr class="bg-[var(--color-main-50)] font-black border-t border-slate-900">
          <td colSpan="3" class="p-2 border border-slate-400 text-right uppercase">TOTAL ALOKASI JP KESELURUHAN (1 TAHUN)</td>
          <td colSpan="2" class="p-2 border border-slate-400 text-center text-[var(--color-main-950)] text-base">{{ totalJPSetahun }} JP</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: 'ProtaTable',
  setup(){
    return {
      ucFirst,
    }
  },
  props: {
    activeSubject: {
      type: Object,
      default: null
    },
  },
  emits: ['update-jp', 'select-chapter-for-rpp'],
  computed:{
    sem1TotalJP(){
      return this.activeSubject?.materi?.filter(e => {
        return e.semester?.includes('gasal') || e.semester?.includes('Ganjil')
      }).reduce((sum, entry) => sum + parseInt(entry.jam), 0)
    },
    sem2TotalJP(){
      return this.activeSubject?.materi?.filter(e => {  
        return e.semester?.includes('genap') || e.semester?.includes('Genap')
      }).reduce((sum, entry) => sum + parseInt(entry.jam), 0)
    },
    totalJPSetahun(){
      return this.sem1TotalJP + this.sem2TotalJP
    },
  },
  methods: {
    getMatchingChapter(entry, idx) {
      const match = this.activeSubject?.chapters?.find(
        (c) => c.chapterNumber === entry.chapterNumber || c.title.includes(entry.chapterTitle)
      );
      if (match) {
        return { ...match, alokasiJP: entry.alokasiJP };
      }
      return {
        id: 'chap_temp_' + idx,
        chapterNumber: entry.chapterNumber || idx + 1,
        title: entry.chapterTitle,
        semester: entry.semester,
        alokasiJP: entry.alokasiJP,
        learningObjectives: [entry.capaianPembelajaran],
        subTopics: [entry.chapterTitle]
      };
    }
  }
};
</script>
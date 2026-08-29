<template>
  <div class="space-y-3 pt-6 border-t-2 border-dashed border-slate-300 print:break-before-page">
    <!-- Header Lembar Sumatif -->
    <div class="text-center border-b-2 border-slate-900 pb-2">
      <div class="inline-flex items-center space-x-1.5 bg-rose-100 text-rose-900 text-xs px-3 py-1 rounded-full font-bold uppercase tracking-wider mb-1.5 border border-rose-300">
        <icons icon="mdi:trophy-outline" class="w-3.5 h-3.5" />
        <span>Lampiran {{ noLampiran }}: Asesmen Sumatif (Akhir Bab)</span>
      </div>
      <h2 class="text-base sm:text-lg font-black uppercase text-slate-900">
        LEMBAR KERJA ASESMEN SUMATIF (EVALUASI AKHIR BAB)
      </h2>
      <p class="text-xs text-slate-600 font-bold uppercase">
        {{ rpp.subjectInfo.nama_unit }}
      </p>
    </div>

    <!-- Kop Nilai & Identitas Siswa Resmi -->
    <div class="border-2 border-slate-900 rounded-xl p-4 bg-white grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
      <div class="sm:col-span-3 space-y-1.5">
        <div class="grid grid-cols-2 gap-2">
          <div>
            <span class="font-bold text-slate-600">Nama Siswa:</span>
            <span class="text-slate-400 block italic">........................................................</span>
          </div>
          <div>
            <span class="font-bold text-slate-600">Kelas / Semester:</span>
            <span class="font-semibold text-slate-900 block">{{ rpp.subjectInfo.kelas }} / {{ rpp.subjectInfo.semester }}</span>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-2 pt-1">
          <div>
            <span class="font-bold text-slate-600">Nomor Absen / Tanggal:</span>
            <span class="text-slate-400 block italic">.......... / ....................................</span>
          </div>
          <div>
            <span class="font-bold text-slate-600">Alokasi Waktu:</span>
            <span class="font-semibold text-slate-900 block">{{ sumatifData.alokasiWaktu }}</span>
          </div>
        </div>
      </div>
      <div class="border-2 border-slate-900 rounded-lg p-2 text-center flex flex-col justify-between bg-slate-50">
        <span class="text-[10px] font-black uppercase tracking-wider text-slate-700">Skor / Nilai Akhir</span>
        <div class="text-2xl font-black text-slate-900 my-1">&nbsp;</div>
        <span class="text-[9px] text-slate-500 border-t border-slate-300 pt-0.5">Paraf Guru</span>
      </div>
    </div>

    <div  v-for="(soal, idx) in sumatifData?.daftarJenisSoal" class="space-y-2">
      <div class="flex items-center justify-between bg-slate-100 px-3 py-1.5 rounded-md border-l-4 border-rose-600">
        <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 w-full">
          <el-input
            v-if="isEditingMode"
            :model-value="soal.namaJenisSoal"
            @input="(val) => updateArray(`asesmenFSumatif.daftarJenisSoal.${idx}.namaJenisSoal`, val)"
          />
          <span v-else>{{ soal.namaJenisSoal }}</span>
        </h4>
      </div>
      <template v-if="isEditingMode">
        <document-editor class="mx-2 text-[12px]" v-model:content="soal.soalHtml" />
        <document-editor class="mx-2 text-[12px]" v-model:content="soal.penilaian" />
      </template>
      <template v-else>
        <div class="*:py-2 text-[12px] px-2" v-html="soal.soalHtml" />
        <div class="*:py-2 text-[12px] px-2" v-html="soal.penilaian" />
      </template>
    </div>
    <!-- KUNCI JAWABAN & PEDOMAN PENSKORAN GURU -->
    <div class="bg-slate-900 text-white rounded-xl p-4 space-y-2 text-xs">
      <div class="font-bold text-rose-400 uppercase tracking-wider flex items-center space-x-1.5">
        <icons icon="mdi:trophy-outline" class="w-4 h-4" />
        <span>Nilai Akhir</span>
      </div>

      <div v-if="!isEditingMode" v-html="sumatifData?.nilaiAkhir" class="*:pt-1"/>
    </div>
    <document-editor v-if="isEditingMode" class="mx-2 text-[12px]" :content="sumatifData?.nilaiAkhir" />
  </div>
</template>

<script>
export default {
  name: 'RppSumatifSection',
  emits: ['update-array-item'],
  props: {
    rpp: {
      type: Object,
      required: true
    },
    sumatifData: {
      type: Object,
      required: true
    },
    noLampiran:{
      type:[String, Number]
    },
    isEditingMode: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    totalBobotPG() {
      return (this.sumatifData.bagianPilihanGanda?.soalList || []).reduce((acc, curr) => acc + (curr.bobot || 10), 0);
    },
    totalBobotUraian() {
      return (this.sumatifData.bagianUraianStudiKasus?.soalList || []).reduce((acc, curr) => acc + (curr.bobot || 20), 0);
    }
  },
  methods:{
    updateArray(path, value) {
      this.$emit('update-array-item', { path, value });
    }
  }
};
</script>
<template>
  <div class="space-y-2 pt-6 border-t-2 border-dashed border-slate-300 print:break-before-page">
    <!-- Header Lembar Formatif -->
    <div class="text-center border-b-2 border-slate-900 pb-1">
      <div class="inline-flex items-center space-x-1.5 bg-emerald-100 text-emerald-900 text-xs px-3 py-1 rounded-full font-bold uppercase tracking-wider mb-1.5 border border-emerald-300">
        <icons icon="mdi:help-circle-outline" class="w-3.5 h-3.5" />
        <span>Lampiran {{ noLampiran }}: Asesmen Formatif (Selama Pembelajaran)</span>
      </div>
      <h2 class="text-base sm:text-lg font-black uppercase text-slate-900">
        LEMBAR KERJA ASESMEN FORMATIF (SELAMA PEMBELAJARAN)
      </h2>
      <p class="text-xs text-slate-600 font-bold uppercase">
        {{ rpp.subjectInfo.nama_unit }}
      </p>
    </div>

    <!-- Identitas Siswa -->
    <div class="border border-slate-300 rounded-xl p-3 py-1 bg-slate-50/60 text-xs grid grid-cols-2 sm:grid-cols-3 gap-3">
      <div>
        <span class="font-bold text-slate-600 block">Nama Peserta Didik:</span>
        <span class="text-slate-400 italic">......................................................</span>
      </div>
      <div>
        <span class="font-bold text-slate-600 block">Kelas / Fase:</span>
        <span class="font-semibold text-slate-900">{{ rpp.subjectInfo.kelas }} / {{ rpp.subjectInfo.fase }}</span>
      </div>
      <div>
        <span class="font-bold text-slate-600 block">Nomor Absen / Tanggal:</span>
        <span class="text-slate-400 italic">........ / ................................</span>
      </div>
      <div class="sm:col-span-2">
        <span class="font-bold text-slate-600 block">Mata Pelajaran & Topik:</span>
        <span class="font-semibold text-slate-900">{{ rpp.subjectInfo.nama_mapel }} — {{ rpp.subjectInfo.materi }}</span>
      </div>
      <div>
        <span class="font-bold text-slate-600 block">Guru Pengampu:</span>
        <span class="font-semibold text-slate-900">{{ rpp.subjectInfo.nama_guru }}</span>
      </div>
    </div>

    <!-- A. Bagian Non-Kognitif -->
    <div class="space-y-2" 
      v-for="(soal, idx) in formatifData?.daftarJenisSoal" >
      <h4 class="text-xs font-black uppercase tracking-wider bg-emerald-100 text-emerald-950 px-3 py-1.5 rounded-md border-l-4 border-emerald-600">
        <el-input
          v-if="isEditingMode"
          :model-value="soal.namaJenisSoal"
          @input="(val) => updateArray(`asesmenFormatif.daftarJenisSoal.${idx}.namaJenisSoal`, val)"
        />
        <span v-else>{{ soal.namaJenisSoal }}</span>
      </h4>
      <template v-if="isEditingMode">
        <document-editor class="mx-2 text-[12px]" v-model:content="soal.soalHtml" />
        <document-editor class="mx-2 text-[12px]" v-model:content="soal.penilaian" />
      </template>
      <template v-else>
        <div class="*:py-2 text-[12px] px-2" v-html="soal.soalHtml" />
        <div class="*:py-2 text-[12px] px-2" v-html="soal.penilaian" />
      </template>
    </div>

  </div>
</template>

<script>
export default {
  name: 'RppFormatifSection',
  emits: ['update-array-item'],
  props: {
    rpp: {
      type: Object,
      required: true
    },
    formatifData: {
      type: Object,
      required: true
    },
    noLampiran:{
      type:[String, Number],
    },
    isEditingMode: {
      type: Boolean,
      default: false
    }
  },
  methods:{
    updateArray(path, value) {
      this.$emit('update-array-item', { path, value });
    }
  }
};
</script>
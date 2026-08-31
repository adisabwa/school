<template>
  <div class="space-y-2 pt-6 border-t-2 border-dashed border-slate-300 print:break-before-page">
    <!-- Header Lembar Diagnostik -->
    <div class="text-center border-b-2 border-slate-900 pb-1">
      <div class="inline-flex items-center space-x-1.5 bg-cyan-100 text-cyan-900 text-xs px-3 py-1 rounded-full font-bold uppercase tracking-wider mb-1.5 border border-cyan-300">
        <icons icon="mdi:help-circle-outline" class="w-3.5 h-3.5" />
        <span>Lampiran {{ noLampiran }}: Asesmen Diagnostik (Awal)</span>
      </div>
      <h2 class="text-base sm:text-lg font-black uppercase text-slate-900">
        LEMBAR KERJA ASESMEN DIAGNOSTIK (AWAL PEMBELAJARAN)
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

    <!-- Petunjuk & Tujuan -->
    <div class="bg-cyan-50/60 border border-cyan-200 rounded-xl p-3.5 py-1 text-xs text-cyan-950 space-y-1.5">
      <p><strong>Tujuan Asesmen:</strong> Memetakan kesiapan belajar, profil minat, serta pemahaman awal siswa pada topik {{ this.rpp?.subjectInfo?.materi || ''}},</p>
      <div>
        <strong>Petunjuk Pengerjaan:</strong>
        <ul class="list-disc pl-4 space-y-0.5 mt-0.5">
          <li>Isilah lembar kerja ini secara mandiri dan jujur sesuai dengan kondisi dan pemahamanmu.</li>
          <li>Tidak ada jawaban salah pada bagian kesiapan dan preferensi belajar.</li>
          <li>Hasil asesmen digunakan guru untuk merancang pembelajaran berdiferensiasi yang tepat.</li>
        </ul>
      </div>
    </div>

    <!-- A. Bagian Non-Kognitif -->
    <div class="space-y-2" 
      v-for="(soal, idx) in diagnostikData?.daftarJenisSoal" >
      <h4 class="text-xs font-black uppercase tracking-wider bg-cyan-100 text-cyan-950 px-3 py-1.5 rounded-md border-l-4 border-cyan-600">
        <el-input
          v-if="isEditingMode"
          :model-value="soal.namaJenisSoal"
          @input="(val) => updateArray(`asesmenDiagnostik.daftarJenisSoal.${idx}.namaJenisSoal`, val)"
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

    <!-- C. Panduan Tindak Lanjut Guru -->
    <div class="bg-slate-900 text-white rounded-xl p-5 pt-3 space-y-2 text-xs">
      <div class="font-bold text-cyan-400 uppercase tracking-wider flex items-center space-x-1.5">
        <icons icon="mdi:sparkles" class="w-4 h-4" />
        <span>Panduan Pemetaan & Diferensiasi Pembelajaran (Pegangan Guru)</span>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1 text-[11px]">
        <div :class="getClassPanduan(p)"
          v-for="(p, idx) in diagnostikData?.panduan">
          <span :class="['font-bold mb-1 flex items-center']">
            <icons icon="mdi:circle" class="text-sm"/>{{ p?.kategori }}
          </span>
          <el-input
            v-if="isEditingMode"
            :model-value="p?.tindakLanjut"
            size="small" type="textarea" rows="5"
            @input="(val) => updateArray(`asesmenDiagnostik.panduan.${idx}.tindakLanjut`, val)"
          />
          <p v-else class="text-slate-200">{{ p?.tindakLanjut }}</p>
        </div>
      </div>
      <p class="text-[11px] text-slate-400 pt-1">
        <strong>Strategi Diferensiasi:</strong> 
          <el-input
            v-if="isEditingMode"
            :model-value="diagnostikData?.strategiDiferensiasi"
            size="small" type="textarea" rows="3"
            @input="(val) => updateArray(`asesmenDiagnostik.strategiDiferensiasi`, val)"
          />
          <span v-else>{{ diagnostikData?.strategiDiferensiasi }}</span>
      </p>
    </div>
  </div>
</template>

<script>
import { document } from 'postcss';

export default {
  name: 'RppDiagnostikSection',
  setup(){
    return {
      getColor,
    }
  },
  emits: ['update-array-item'],
  props: {
    rpp: {
      type: Object,
      required: true
    },
    diagnostikData: {
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
    getClassPanduan(p){
      return `p-2.5 rounded-lg border border-slate-700 border-solid text-slate-100 ${p?.warna}`
    },
    updateArray(path, value) {
      this.$emit('update-array-item', { path, value });
    }
  }
};
</script>
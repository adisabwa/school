<template>
  <div class="space-y-3">
    <!-- Header / Kop Modul -->
    <div class="text-center border-b-2 border-slate-900 pb-2">
      <h1 class="text-lg sm:text-xl font-black uppercase tracking-wide text-slate-900">
        {{ rpp?.subjectInfo?.curriculum === 'kurikulum_berbasis_cinta'
          ? 'MODUL AJAR KURIKULUM BERBASIS CINTA (KBC)'
          : 'MODUL AJAR KURIKULUM MERDEKA' }}
      </h1>
      <p class="text-sm font-bold text-slate-700 uppercase tracking-wider mt-0.5">
        {{ rpp?.subjectInfo?.nama_unit }}
      </p>
      <div v-if="rpp?.subjectInfo?.curriculum === 'kurikulum_berbasis_cinta'" class="inline-flex items-center space-x-1 mt-1 bg-rose-50 border border-rose-200 text-rose-700 text-xs px-2.5 py-0.5 rounded-full font-medium">
        <icons icon="mdi:heart-handshake" class="w-3.5 h-3.5" />
        <span>Pendekatan Kasih Sayang, Empati, & Kehangatan Pembelajaran</span>
      </div>
    </div>

    <!-- A. INFORMASI UMUM -->
    <section >
      <h3 class="text-xs font-black uppercase tracking-wider bg-slate-100 text-slate-900 px-3 py-1.5 rounded-md border-l-4 border-blue-600">
        A. INFORMASI UMUM
      </h3>

      <table className="w-full text-xs border-collapse">
        <tbody>
          <tr class="border-b border-slate-100">
            <td class="py-1.5 font-bold w-1/3 text-slate-700">Mata Pelajaran</td>
            <td class="py-1.5 w-4 text-slate-400">:</td>
            <td class="py-1.5 font-semibold text-slate-900">{{ rpp?.subjectInfo?.nama_mapel }}</td>
          </tr>
          <tr class="border-b border-slate-100">
            <td class="py-1.5 font-bold text-slate-700">Fase / Kelas</td>
            <td class="py-1.5 text-slate-400">:</td>
            <td class="py-1.5 text-slate-900">{{ rpp?.subjectInfo?.fase }} / {{ rpp?.subjectInfo?.tingkat }}</td>
          </tr>
          <tr class="border-b border-slate-100">
            <td class="py-1.5 font-bold text-slate-700">Semester / Tahun Ajaran</td>
            <td class="py-1.5 text-slate-400">:</td>
            <td class="py-1.5 text-slate-900">{{ rpp?.subjectInfo?.semester_keterangan }}</td>
          </tr>
          <tr class="border-b border-slate-100">
            <td class="py-1.5 font-bold text-slate-700">Judul / Topik Pembelajaran</td>
            <td class="py-1.5 text-slate-400">:</td>
            <td class="py-1.5 font-black text-slate-900">{{ rpp?.subjectInfo?.materi }}</td>
          </tr>
          <tr class="border-b border-slate-100">
            <td class="py-1.5 font-bold text-slate-700">Alokasi Waktu & Pertemuan</td>
            <td class="py-1.5 text-slate-400">:</td>
            <td class="py-1.5 text-slate-900">
              <span class="font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-sm border border-indigo-100">
                {{ rpp?.subjectInfo?.jam }} JP ( {{ rpp?.subjectInfo?.pertemuan }} pertemuan x {{ rpp?.subjectInfo?.jam / rpp?.subjectInfo?.pertemuan }} JP x 40 menit)
              </span>
            </td>
          </tr>
          <tr class="border-b border-slate-100">
            <td class="py-1.5 font-bold text-slate-700">Model Pembelajaran</td>
            <td class="py-1.5 text-slate-400">:</td>
            <td class="py-1.5 text-slate-900">{{ rpp?.subjectInfo?.learning_model }}</td>
          </tr>
          <tr class="border-b border-slate-100">
            <td class="py-1.5 font-bold text-slate-700">Sarana & Prasarana</td>
            <td class="py-1.5 text-slate-400">:</td>
            <td class="py-1.5 text-slate-900">{{ rpp?.subjectInfo?.sarana }}</td>
          </tr>
          <tr class="border-b border-slate-100">
            <td class="py-1.5 font-bold text-slate-700">Target Peserta Didik</td>
            <td class="py-1.5 text-slate-400">:</td>
            <td class="py-1.5 text-slate-900">{{ rpp?.targetPesertaDidik }}</td>
          </tr>
          <tr>
            <td class="py-1.5 font-bold text-slate-700">Profil Pelajar Pancasila</td>
            <td class="py-1.5 text-slate-400">:</td>
            <td class="py-1.5 text-slate-900">
              <div class="flex flex-wrap gap-1 mt-0.5">
                <span v-for="(p, idx) in rpp?.pancasilaProfiles" :key="idx" class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-[11px] font-medium border border-slate-200">
                  {{ p }}
                </span>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </section>

    <!-- B. KOMPONEN INTI -->
    <section class="space-y-3">
      <h3 class="text-xs font-black uppercase tracking-wider bg-slate-100 text-slate-900 px-3 py-1.5 rounded-md border-l-4 border-blue-600">
        B. KOMPONEN INTI
      </h3>

      <div class="text-xs space-y-3">
        <div>
          <strong class="block text-slate-900 font-bold mb-1">1. Capaian Pembelajaran (CP):</strong>
          <el-input
            v-if="isEditingMode" :model-value="rpp?.subjectInfo?.cp" size="small"
            type="textarea" cols="5"
            @input="(val) => updateArray(`subjectInfo.cp`, val)"
          />
          <p v-else class="bg-slate-50 p-2.5 rounded-lg border border-slate-200 text-slate-800 leading-relaxed">
            {{ rpp?.subjectInfo?.cp }}
          </p>
        </div>

        <div>
          <strong class="block text-slate-900 font-bold mb-1">2. Tujuan Pembelajaran (TP):</strong>
          <ul class="list-disc pl-5 space-y-1 text-slate-800">
            <li v-for="(tp, idx) in rpp?.subjectInfo?.sub_materi" :key="idx">
              <el-input
                v-if="isEditingMode" :model-value="tp.tujuan_pembelajaran" size="small"
                @input="(val) => updateArray(`subjectInfo.sub_materi.${idx}.tujuan_pembelajaran`, val)"
              />
              <span v-else>{{ tp.tujuan_pembelajaran }}</span>
            </li>
          </ul>
        </div>

        <div>
          <strong class="block text-slate-900 font-bold mb-1">3. Pemahaman Bermakna:</strong>
          <el-input
            v-if="isEditingMode" :model-value="rpp?.pemahamanBermakna" size="small"
            type="textarea" cols="5"
            @input="(val) => updateArray(`pemahamanBermakna`, val)"
          />
          <p v-else class="text-slate-800 italic bg-blue-50/50 p-2 rounded-lg border border-blue-100">
            "{{ rpp?.pemahamanBermakna }}"
          </p>
        </div>

        <div>
          <strong class="block text-slate-900 font-bold mb-1">4. Pertanyaan Pemantik:</strong>
          <ul class="list-disc pl-5 space-y-1 text-slate-800">
            <li v-for="(pp, idx) in rpp?.pertanyaanPemantik" :key="idx">
              <el-input
                v-if="isEditingMode"
                :model-value="pp"
                size="small"
                @input="(val) => updateArray(`pertanyaanPemantik.${idx}`, val)"
              />
              <span v-else>{{ pp }}</span>
            </li>
          </ul>
        </div>
      </div>
    </section>

    <!-- C. KEGIATAN PEMBELAJARAN PER PERTEMUAN -->
    <section class="space-y-4">
      <h3 class="text-xs font-black uppercase tracking-wider bg-slate-100 text-slate-900 px-3 py-1.5 rounded-md border-l-4 border-blue-600">
        C. RINCIAN ALUR KEGIATAN PEMBELAJARAN PER PERTEMUAN
      </h3>

      <div v-if="rpp?.pertemuanList && rpp?.pertemuanList.length > 0" class="space-y-4">
        <div v-for="(meet, mInd) in rpp?.pertemuanList" :key="meet.pertemuanKe" class="border border-slate-300 rounded-xl overflow-hidden shadow-xs">
          <div class="bg-slate-800 text-white px-3.5 py-2 flex items-center justify-between gap-5">
            <span class="font-bold text-xs uppercase tracking-wide flex w-full gap-2">
              <span class="shrink-0">Pertemuan ke-{{ meet.pertemuanKe }} : </span>
              <el-input
                v-if="isEditingMode" :model-value="meet.topikPertemuan" size="small"
                @input="(val) => updateArray(`pertemuanList.${mInd}.topikPertemuan`, val)"
              />
              <span v-else> {{ meet.topikPertemuan }}</span>
            </span>
            <span class="text-[11px] bg-slate-700 px-2.5 py-0.5 rounded-full font-medium text-slate-200">
              <el-input
                v-if="isEditingMode" :model-value="meet.alokasiWaktu" size="small"
                class="w-[150px]"
                @input="(val) => updateArray(`pertemuanList.${mInd}.alokasiWaktu`, val)"
              />
              <span v-else>{{ meet.alokasiWaktu }}</span>
            </span>
          </div>
          <table className="w-full border-collapse text-xs">
            <tbody>
              <tr class="border-b border-slate-200 bg-slate-50/40">
                <td class="p-2.5 font-bold w-1/4 align-top text-slate-900 border-r border-slate-200">
                  Pendahuluan 
                  <el-input
                    v-if="isEditingMode" :model-value="meet.pendahuluan.duration" size="small"
                    class="w-[150px] mt-1"
                    @input="(val) => updateArray(`pertemuanList.${mInd}.pendahuluan.duration`, val)"
                  />
                  <span v-else>({{ meet.pendahuluan.duration }})</span>
                </td>
                <td class="p-2.5 align-top">
                  <ul class="list-disc pl-4 space-y-1 m-0">
                    <li v-for="(act, idx) in meet.pendahuluan.activities" :key="idx">
                      <el-input
                        v-if="isEditingMode" :model-value="act" size="small"
                        class="w-full mt-1 resize" type="textarea"
                        @input="(val) => updateArray(`pertemuanList.${mInd}.pendahuluan.activities.${idx}`, val)"
                      />
                      <span v-else>{{ act }}</span>
                    </li>
                  </ul>
                </td>
              </tr>
              <tr class="border-b border-slate-200">
                <td class="p-2.5 font-bold align-top text-slate-900 border-r border-slate-200 bg-slate-50/20">
                  Kegiatan Inti  
                  <el-input
                    v-if="isEditingMode" :model-value="meet.inti.duration" size="small"
                    class="w-[150px] mt-1"
                    @input="(val) => updateArray(`pertemuanList.${mInd}.inti.duration`, val)"
                  />
                  <span v-else>({{ meet.inti.duration }})</span>
                </td>
                <td class="p-2.5 align-top">
                  <ul class="list-disc pl-4 space-y-1 m-0">
                    <li v-for="(act, idx) in meet.inti.activities" :key="idx">
                      <el-input
                        v-if="isEditingMode" :model-value="act" size="small"
                        class="w-full mt-1 resize" type="textarea"
                        @input="(val) => updateArray(`pertemuanList.${mInd}.inti.activities.${idx}`, val)"
                      />
                      <span v-else>{{ act }}</span>
                    </li>
                  </ul>
                </td>
              </tr>
              <tr class="bg-slate-50/40">
                <td class="p-2.5 font-bold align-top text-slate-900 border-r border-slate-200">
                  Penutup  
                  <el-input
                    v-if="isEditingMode" :model-value="meet.penutup.duration" size="small"
                    class="w-[150px] mt-1"
                    @input="(val) => updateArray(`pertemuanList.${mInd}.penutup.duration`, val)"
                  />
                  <span v-else>({{ meet.penutup.duration }})</span>
                </td>
                <td class="p-2.5 align-top">
                  <ul class="list-disc pl-4 space-y-1 m-0">
                    <li v-for="(act, idx) in meet.penutup.activities" :key="idx">
                      <el-input
                        v-if="isEditingMode" :model-value="act" size="small"
                        class="w-full mt-1 resize" type="textarea"
                        @input="(val) => updateArray(`pertemuanList.${mInd}.penutup.activities.${idx}`, val)"
                      />
                      <span v-else>{{ act }}</span></li>
                  </ul>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- D. ASESMEN DAN PENILAIAN -->
    <section class="space-y-1">
      <h3 class="text-xs font-black uppercase tracking-wider bg-slate-100 text-slate-900 px-3 py-1.5 rounded-md border-l-4 border-blue-600">
        D. ASESMEN DAN PENILAIAN
      </h3>
      <div class="bg-slate-50 pt-1 p-3 rounded-lg border border-slate-200 text-xs space-y-1.5">
        <p><strong>1. Asesmen Diagnostik (Awal):</strong> {{ rpp?.asesmen?.diagnostik.deskripsi }}</p>
        <p><strong>2. Asesmen Formatif (Proses):</strong> {{ rpp?.asesmen?.formatif.deskripsi }}</p>
        <p><strong>3. Asesmen Sumatif (Akhir):</strong> {{ rpp?.asesmen?.sumatif.deskripsi }}</p>
      </div>

    </section>

    <!-- E. PENGAYAAN, REMEDIAL, DAN REFLEKSI -->
    <section class="space-y-3">
      <h3 class="text-xs font-black uppercase tracking-wider bg-slate-100 text-slate-900 px-3 py-1.5 rounded-md border-l-4 border-blue-600">
        E. PENGAYAAN, REMEDIAL, DAN REFLEKSI
      </h3>
      <div class="text-xs space-y-2">
        <p><strong>Pengayaan:</strong> 
          <el-input
            v-if="isEditingMode"
            :model-value="rpp?.pengayaanDanRemedial?.pengayaan"
            size="small" type="textarea"
            @input="(val) => updateArray(`pengayaanDanRemedial.pengayaan`, val)"
          />
          <span v-else>{{ rpp?.pengayaanDanRemedial?.pengayaan }}</span></p>
        <p><strong>Remedial:</strong> 
          <el-input
            v-if="isEditingMode"
            :model-value="rpp?.pengayaanDanRemedial?.remedial"
            size="small" type="textarea"
            @input="(val) => updateArray(`pengayaanDanRemedial.remedial`, val)"
          />
          <span v-else>{{ rpp?.pengayaanDanRemedial?.remedial }}</span></p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs pt-2">
        <div class="bg-slate-50 p-3 rounded-lg border border-slate-200">
          <strong class="block text-slate-900 font-bold mb-1">Refleksi Guru:</strong>
          <ul class="list-disc pl-4 space-y-1">
            <li v-for="(rg, idx) in rpp?.refleksiGuruDanSiswa?.refleksiGuru" :key="idx">
              <el-input
                v-if="isEditingMode"
                :model-value="rg"
                size="small"
                @input="(val) => updateArray(`refleksiGuruDanSiswa.refleksiGuru.${idx}`, val)"
              />
              <span v-else>{{ rg }}</span>
            </li>
          </ul>
        </div>

        <div class="bg-slate-50 p-3 rounded-lg border border-slate-200">
          <strong class="block text-slate-900 font-bold mb-1">Refleksi Peserta Didik:</strong>
          <ul class="list-disc pl-4 space-y-1">
            <li v-for="(rs, idx) in rpp?.refleksiGuruDanSiswa?.refleksiSiswa" :key="idx">
              <el-input
                v-if="isEditingMode"
                :model-value="rs"
                size="small"
                @input="(val) => updateArray(`refleksiGuruDanSiswa.refleksiSiswa.${idx}`, val)"
              />
              <span v-else>{{ rs }}</span>
            </li>
          </ul>
        </div>
      </div>
    </section>

    <!-- Tanda Tangan -->
    <div class="pt-8 border-t border-slate-200 print:break-inside-avoid">
      <table className="w-full text-xs text-center border-collapse">
        <tbody>
          <tr>
            <td class="w-1/2 align-top pb-14">
              Mengetahui,<br />
              Kepala {{ rpp?.subjectInfo?.nama_unit }}
              <div class="h-14"></div>
              <strong class="underline uppercase font-bold">{{ rpp?.subjectInfo?.nama_kepala }}</strong><br />
              NBM. {{ rpp?.subjectInfo?.nbm_kepala }}
            </td>
            <td class="w-1/2 align-top pb-14">
              Patean, <date-wheel-picker v-if="isEditingMode" :value="rpp?.tanggal"
                value-format="YYYY-MM-DD"
                :format="'DD MMMM YYYY'"      
                class="w-[140px] mx-auto"
                size="small text-center my-1"
                @change="(val) => updateArray(`tanggal`, val)"                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          
                />
              <span v-else>{{ dateIndo(rpp?.tanggal) ?? dateNow() }}<br /></span>
              Guru Mata Pelajaran
              <div class="h-14"></div>
              <strong class="underline uppercase font-bold">{{ rpp?.subjectInfo?.nama_guru }}</strong><br />
              NBM. {{ rpp?.subjectInfo?.nbm_guru }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RppMainSection',
  props: {
    rpp: {
      type: Object,
      required: true
    },
    isEditingMode: {
      type: Boolean,
      default: false
    }
  },
  setup(){
    return {
      dateIndo
    }
  },
  emits: ['update-array-item'],
  computed: {
  },
  methods: {
    updateArray(path, value) {
      this.$emit('update-array-item', { path, value });
    }
  }
};
</script>
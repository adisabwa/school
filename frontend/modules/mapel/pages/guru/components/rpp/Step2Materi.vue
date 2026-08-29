<template>
  <div class="space-y-4">
    <!-- Top Banner AI Generate -->
    <div class="bg-slate-900 text-white rounded-xl p-6 shadow-xs border border-solid border-slate-800">
      <div class="space-y-4">
        <div class="flex items-center space-x-2 text-[var(--color-main-300)] font-bold text-xs uppercase tracking-wider">
          <icons icon="mdi:sparkles" class="w-4 h-4 text-[var(--color-main-400)]" />
          <span>Generator Materi Pembelajaran Otomatis AI</span>
        </div>

        <div>
          <h2 class="text-xl font-bold text-white">
            {{ localSubject.nama_mapel }} — {{ localSubject.materi }}
          </h2>
          <p class="text-xs text-slate-400 mt-1">
            Fase/Kelas: {{ localSubject.tingkat }} | Kurikulum: {{ curriculumFormatted }}
          </p>
        </div>

        <div class="bg-[var(--color-main-950)] rounded-lg p-3.5 border border-solid border-[var(--color-main-800)]">
          <label class="block text-[11px] font-bold text-[var(--color-main-300)] uppercase tracking-wide mb-1">
            Catatan Khusus Guru / Pokok Bahasan (Opsional - Jika belum ada, AI akan membuatkan otomatis)
          </label>
          <el-input
            v-model="notesInput"
            placeholder="Misal: Sertakan bahasan tentang keterkaitan manusia dengan lingkungan sekitar..."
            size="default"
            class="[&_.el-input\_\_wrapper]:bg-[var(--color-main-950)] [&_.el-input\_\_wrapper]:border-[var(--color-main-900)] [&_input]:text-[12px] [&_input]:text-[var(--color-main-100)]"
          />
        </div>

        <div class="flex flex-wrap items-center gap-3 pt-1">
          <el-button
            type="primary"
            class="!font-medium !text-sm shadow-sm"
            :loading="loadingAI"
            @click="handleGenerateMaterials"
          >
            <icons icon="mdi:sparkles" class="w-4 h-4 text-[var(--color-main-200)]" />
            <span class="">
              {{ loadingAI ? 'Sedang Menyusun Materi dengan AI...' : (localSubject?.materials?.length > 0 ? 'Regenerate Ulang Materi dengan AI' : 'Generate Rincian Materi dengan AI') }}
            </span>
          </el-button>

          <el-tag
            v-if="localSubject?.materials?.length > 0"
            type="success"
            effect="dark"
            class="!font-semibold "
          >
            <div class="flex items-center gap-1">
              <icons icon="mdi:check-circle-outline" class="w-3.5 h-3.5 inline mr-1" />
              {{ localSubject?.materials?.length }} Sub-Materi Siap Diedit
            </div>
          </el-tag>
        </div>
      </div>
    </div>

    <!-- Error Alert -->
    <el-alert
      v-if="errorMsg"
      type="error"
      show-icon
      :closable="false"
      class="!bg-rose-50 !border border-solid !border-rose-200 !text-rose-800"
    >
      <template #sub_materi>
        <span class="font-bold">Gagal Membuat Materi:</span> {{ errorMsg }}
      </template>
    </el-alert>

    <!-- Capaian Pembelajaran (CP) Edit Box -->
    <el-card shadow="never" class="!rounded-xl !border-slate-200"
      body-class="">
      <div class="space-y-2 mb-5">
        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center justify-between">
          <span class="flex items-center gap-2 text-slate-800">
            <icons icon="mdi:pencil-outline" class="w-4 h-4 text-[var(--color-main-600)] m-0" />
            <span>Capaian Pembelajaran (CP) / Kompetensi Dasar (KD)</span>
          </span>
          <span class="text-[11px] text-slate-400 font-normal normal-case">Dapat diedit langsung</span>
        </label>
        <el-input
          v-model="localSubject.cp"
          type="textarea"
          :rows="3"
          placeholder="Isi Capaian Pembelajaran (CP) sesuai standar Kurikulum..."
        />
      </div>
    </el-card>

    <!-- Rincian Sub-Materi (Editable Cards) -->
    <div class="space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="font-bold text-slate-900 text-base">
            Daftar Sub-Materi & Tujuan Pembelajaran (TP)
          </h3>
          <p class="text-xs text-slate-500">
            Setiap poin sub-materi di bawah ini dapat Anda ubah, tambah, atau hapus sesuai kebutuhan kelas.
          </p>
        </div>
        <el-button
          plain
          size="small"
          class="!font-medium"
          @click="handleAddManualMaterial"
        >
          <icons icon="mdi:plus" class="w-3.5 h-3.5 text-[var(--color-main-600)]" />
          Tambah Sub-Materi
        </el-button>
      </div>

      <div v-if="localSubject?.materials?.length === 0" class="bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl p-8 text-center space-y-3">
        <icons icon="mdi:sparkles" class="w-10 h-10 text-slate-400 mx-auto" />
        <div>
          <h4 class="font-bold text-slate-700 text-sm">Belum Ada Materi Pembelajaran</h4>
          <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">
            Klik tombol <strong class="text-[var(--color-main-600)]">"Generate Rincian Materi dengan AI"</strong> di atas, atau tambahkan sub-materi secara manual.
          </p>
        </div>
      </div>

      <div v-else class="space-y-2">
        <el-card
          v-for="(m, idx) in localSubject.materials"
          :key="m.id"
          shadow="never"
          class="!rounded-xl !border-slate-200 hover:!border-slate-300 transition-all"
        >
          <div class="space-y-2">
            <!-- Header -->
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-1">
              <div class="flex items-center space-x-2 w-full">
                <span class="w-6 h-6 rounded bg-[var(--color-main-100)] text-[var(--color-main-700)] font-bold text-xs flex items-center justify-center shrink-0">
                  {{ idx + 1 }}
                </span>
                <el-input
                  v-model="m.sub_materi"
                  placeholder="Judul Sub-Materi"
                  class="font-bold text-slate-900"
                />
              </div>
              <el-button
                type="danger"
                link
                size="small"
                sub_materi="Hapus Sub-Materi"
                @click="localSubject.materials.splice(idx,1)"
              >
                <icons icon="mdi:trash-can-outline" class="w-4 h-4" />
              </el-button>
            </div>

            <!-- deskripsi & Objective -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1">
                  Sub Materi
                </label>
                <el-input
                  v-model="m.deskripsi"
                  type="textarea"
                  :rows="2"
                />
              </div>

              <div>
                <label class="block text-[11px] font-bold text-[var(--color-main-700)] uppercase tracking-wide mb-1">
                  Tujuan Pembelajaran Spesifik (TP)
                </label>
                <el-input
                  v-model="m.tujuan_pembelajaran"
                  type="textarea"
                  :rows="2"
                />
              </div>
            </div>

            <!-- Sub Topics List -->
            <div class="space-y-2 pt-1">
              <div class="flex items-center justify-between">
                <label class="text-xs font-semibold text-slate-700">
                  Poin Pembahasan / Pokok Bahasan:
                </label>
                <el-button
                  type="primary"
                  link
                  size="small"
                  class="!font-bold !text-[11px]"
                  @click="handleAddSubTopicItem(idx)"
                >
                  <icons icon="mdi:plus" class="w-3 h-3" />
                  Tambah Poin
                </el-button>
              </div>

              <div class="space-y-2">
                <div v-for="(sub, sIdx) in m.poin" :key="sIdx" class="flex items-center space-x-2">
                  <span class="text-slate-400 text-xs font-semibold shrink-0">•</span>
                  <el-input
                    v-model="sub.poin"
                    size="small"
                    class="w-full"
                  />
                  <el-button
                    v-if="m.poin.length > 1"
                    type="danger"
                    link
                    size="small"
                    @click="m.poin.splice(sIdx, 1)"
                  >
                    <icons icon="mdi:trash-can-outline" class="w-3.5 h-3.5" />
                  </el-button>
                </div>
              </div>
            </div>
          </div>
        </el-card>
      </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="flex items-center justify-between pt-4">
      <el-button
        plain
        class="!font-medium"
        @click="$emit('back')"
      >
        <icons icon="mdi:arrow-left" class="w-4 h-4" />
        Kembali
      </el-button>

      <el-button
        type="primary"
        class="!bg-[var(--color-main-600)] hover:!bg-[var(--color-main-700)] !font-medium"
        @click="handleSubmit"
      >
        <span>Lanjut ke Jenis Asesmen</span>
        <icons icon="mdi:arrow-right" class="w-4 h-4 ml-1" />
      </el-button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Step2Materi',
  props: {
    subjectInfo: {
      type: Object,
      default: () => {}
    },
  },
  emits: [
    'update:subjectInfo',
    'back',
    'next'
  ],
  data() {
    return {
      loadingAI: false,
      localSubject:{},
      errorMsg: null,
      notesInput: '',
    };
  },
  wacth:{
    subjectInfo:{
      deep:true,
      handler(val){
        // console.log('subject', val)
        this.localSubject = val
      }
    },
    localSubject:{
      deep:true,
      handler(val){
        // generateNewMaterial()
        this.$emit('update:subjectInfo', val)
      }
    }
  },
  computed: {
    curriculumFormatted() {
      if (!this.localSubject.curriculum) return '';
      return this.localSubject.curriculum.replace('_', ' ').toUpperCase();
    },
  },
  methods: {
    handleBeforeMaterials() {
      this.localSubject.materials = [];
      this.localSubject.sub_materi.forEach((item, idx) => {
        this.localSubject.materials.push({
          id_sub: item.id_sub,
          id_materi: item.id_materi,
          sub_materi: item.sub_materi,
          deskripsi: item.sub_materi,
          tujuan_pembelajaran: item.tujuan_pembelajaran,
          poin: item.poin.split(';').map(d => {
            return {
              poin: d
            }
          }),
        });
      });
    },
    async handleGenerateMaterials() {
      this.loadingAI = true;
      this.errorMsg = null;

      this.$http.get('mapel/materi/generate-materi', {
        params:{
          nama_mapel:this.localSubject.nama_mapel,
          tingkat:this.localSubject.tingkat,
          nama_unit: this.localSubject.nama_unit,
          tahun_ajaran:this.localSubject.tahun_ajaran,
          materi: this.localSubject.materi,
          curriculum: this.localSubject.curriculum,
          notes:this.notesInput,
        }
      }).then(res => {
        this.loadingAI = false
        let data = res?.data
        this.localSubject.cp = data.cp
        this.localSubject.materials = data?.list_materi.map(item => {
          return {
            id_sub:-1,
            id_materi: this.localSubject.id,
            tujuan_pembelajaran: item.tujuan_pembelajaran,
            sub_materi: item.sub_materi,
            deskripsi: item.deskripsi,
            poin: item.poin
          }
        })
      }).catch(err => {
        
        this.loadingAI = false
        this.errorMsg = err?.response?.data?.message
      })
    },
    handleAddManualMaterial() {
      const newMat = {
        id: -1,
        id_materi: this.localSubject.id,
        sub_materi: 'Judul Sub-Materi Baru',
        deskripsi: 'Penjelasan singkat ringkasan materi...',
        tujuan_pembelajaran: 'Peserta didik dapat memahami konsep ini dengan baik.',
        poin: [
          {poin: 'Poin pembahasan 1'}, 
          {poin: 'Poin pembahasan 2'}, 
          {poin: 'Poin pembahasan 3'}, 
        ]
      };
      this.localSubject.materials.push(newMat);
    },
    handleAddSubTopicItem(idx) {
      this.localSubject.materials[idx].poin.push({ poin: '' });
    },
    handleSubmit() {
      let form = {
        id:this.localSubject.id,
        cp:this.localSubject.cp,
      }
      this.$http.post('mapel/materi/store', window.jsonToFormData(form))
        .then(res => {
          this.handleSubMateri()
        })
        .catch(err => {
          console.log(err)
          var res = err.response;
          this.valueError = res.data.messages
        })
      // if (!this.valueForm.subject || !this.valueForm.topic) {
      //   this.$message ? this.$message.error('Mohon isi Mata Pelajaran dan Judul Topik Pembelajaran.') : alert('Mohon isi Mata Pelajaran dan Judul Topik Pembelajaran.');
      //   return;
      // }
    },
    handleSubMateri(){
      let form = []
      this.localSubject.materials.forEach(item => {
        form.push({
          id:item.id_sub ?? -1,
          id_materi:item.id_materi,
          sub_materi:item.sub_materi,
          deskripsi:item.deskripsi,
          tujuan_pembelajaran:item.tujuan_pembelajaran,
          poin:item.poin.map(e => e.poin).join(';'),
        })
      })
      this.$http.post('mapel/sub-materi/store_many', window.jsonToFormData(form))
        .then(res => {
          this.$emit('next');
        })
        .catch(err => {
          console.log(err)
          var res = err.response;
          this.valueError = res.data.messages
        })
      // if (!this.valueForm.subject || !this.valueForm.topic) {
      //   this.$message ? this.$message.error('Mohon isi Mata Pelajaran dan Judul Topik Pembelajaran.') : alert('Mohon isi Mata Pelajaran dan Judul Topik Pembelajaran.');
      //   return;
      // }

    }
  },
  mounted() {
    this.localSubject = this.subjectInfo;
    if (!this.localSubject?.materials)
      this.handleBeforeMaterials()
  }
};
</script>
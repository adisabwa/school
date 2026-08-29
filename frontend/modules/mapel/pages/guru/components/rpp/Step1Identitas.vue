<template>
  <div class="bg-white rounded-xl border border-slate-200 shadow-xs">
    <!-- Quick Pick From Prota / Mapel Database -->
    <div v-if="subjects.length > 0" class="bg-[var(--color-main-50)] border border-[var(--color-main-200)] rounded-xl p-4 space-y-2">
      <label class="text-xs font-bold text-[var(--color-main-900)] uppercase tracking-wider flex items-center gap-2">
        <icons icon="mdi:sparkles" class="w-6 h-6 m-0 text-amber-500" />
        <span class="text-sm">⚡ Pengisian Otomatis: Pilih dari Prota / Silabus Mapel Anda</span>
      </label>
      <p class="text-xs text-[var(--color-main-800)]">
        Pilih bab pelajaran dari database Prota untuk mengambil alokasi waktu JP, materi, dan CP secara otomatis:
      </p>
      <el-select
        v-model="selectedTahunAjaran"
        placeholder="-- Klik untuk memilih Tahun Ajaran --"
        class="w-full mb-2"
      >
        <el-option
          v-for="chap in optionTahunAjaran"
          :key="chap.value"
          :label="chap.label"
          :value="chap.value"
        />
      </el-select>
      <el-select
        v-model="selectedProtaChapter"
        placeholder="-- Klik untuk memilih Bab & Alokasi JP dari Prota --"
        class="w-full"
        @change="handleProtaSelect"
      >
        <el-option-group
          v-for="subj in subjects"
          :key="subj.id"
          :label="`${subj.nama_mapel} ( Kelas ${subj.tingkat} )`"
        >
          <el-option
            v-for="chap in subj.materi"
            :key="chap.id"
            :label="`Bab ${chap.no}: ${chap.materi} — [${chap.jam} JP, Sem ${chap.semester}]`"
            :value="`${subj.id}::${chap.id}`"
          />
        </el-option-group>
      </el-select>
    </div>

    <!-- Template Format Selection -->
    <div class="mt-6">
      <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-2">
        <icons icon="mdi:layers-outline" class="w-6 h-6 m-0 text-[var(--color-main-600)]" />
        <span class="text-sm">Format & Format Templat RPP / Modul Ajar</span>
      </label>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div
          v-for="tmpl in templateOptions"
          :key="tmpl.id"
          @click="selectCurriculum(tmpl.id)"
          :class="[
            'p-3.5 rounded-lg border-2 cursor-pointer transition relative flex flex-col justify-between border-solid',
            valueForm.curriculum === tmpl.id
              ? 'border-[var(--color-main-600)] bg-[var(--color-main-50)]/60 shadow-xs'
              : 'border-slate-200 hover:border-slate-300 bg-slate-50/50'
          ]"
        >
          <div>
            <span
              :class="[
                'text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded-md inline-block mb-2',
                valueForm.curriculum === tmpl.id ? 'bg-[var(--color-main-600)] text-white' : 'bg-slate-200 text-slate-700'
              ]"
            >
              {{ tmpl.badge }}
            </span>
            <h4 class="font-bold text-slate-900 text-xs leading-tight">{{ tmpl.title }}</h4>
            <p class="text-[11px] text-slate-500 mt-1 leading-snug">{{ tmpl.desc }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Form Component Wrapper -->
      <form-comp
        ref="form"
        :key="'key'+formKey"
        :fields="fields"
        :id="dataId"
        label-class="uppercase text-slate-500 font-bold text-[12px] tracking-wider"
        form-item-class="!col-span-6 md:!col-span-3"
        v-model:formValue="viewValue"
        v-model:errorValue="valueError"
        label-position="top"
        :show-submit="false"
      >
        <template #before-nama_unit>
          <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-2 col-span-6 mt-8">
            <icons icon="mdi:school" class="w-6 h-6 m-0 text-[var(--color-main-600)]" />
            <span class="text-sm">1. Identitas Satuan Pendidikan & Pengajar</span>
          </label>
        </template>
        <template #before-semester>
          <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-2 col-span-6 mt-5">
            <icons icon="tabler:book-filled" class="w-6 h-6 m-0 text-[var(--color-main-600)]" />
            <span class="text-sm">2. Data Pelajaran & Tingkat Kelas</span>
          </label>
        </template>
      </form-comp>

    <!-- Bottom Button -->
    <div class="pt-4 flex justify-end border-t border-slate-100">
      <el-button
        type="primary"
        class="!bg-[var(--color-main-600)] hover:!bg-[var(--color-main-700)] !font-medium !text-sm"
        @click="handleSubmit"
      >
        <span>Lanjut ke Penyusunan Materi</span>
        <icons icon="mdi:arrow-right" class="w-6 h-6 m-0 ml-2" />
      </el-button>
    </div>
  </div>
</template>

<script>
// import { SAMPLE_PRESETS, MODEL_PEMBELAJARAN_OPTIONS } from '../data/presets';
const MODEL_PEMBELAJARAN_OPTIONS = [
  'Pembelajaran Berbasis Empati & Cinta (Love-Based Pedagogy)',
  'Problem Based Learning (PBL)',
  'Project Based Learning (PjBL)',
  'Discovery Learning',
  'Inquiry Learning',
  'Cooperative Learning (STAD/Jigsaw)',
  'Contextual Teaching and Learning (CTL)',
  'Direct Instruction (Pengajaran Langsung)',
  'Differentiated Learning (Pembelajaran Berdiferensiasi)',
]

export default {
  name: 'Step1Identitas',
  props: {
    subjectInfo: {
      type: Object,
      default: () => ({})
    },
    subjects: {
      type: Array,
      default: () => []
    },
    id: {
      type: [String, Number],
      default: null
    },
    tahunAjaran:{
      type:String,
    },
    optionTahunAjaran:{
      type: Array,
      default: () => []
    },
  },
  emits: [
    'update:subjectInfo',
    'update:id',
    'update:tahunAjaran',
    'select-chapter-from-prota',
    'select-preset',
    'next',
  ],
  data() {
    return {
      formKey: 1,
      saving: false,
      selectedProtaChapter: '',
      selectedTahunAjaran:'',
      samplePresets: [],
      viewValue:{
        nama_unit: '',
        nama_guru: '',
        nbm_guru: '',
        nama_kepala: '',
        nama_mapel: '',
        tingkat: '',
        materi: '',
        jam: '',
        learning_model: '',
        semester: '',
        tahun_ajaran: '',
        sarana: '',
        curriculum: '',
      },
      valueForm:{},
      valueError: {},
      templateOptions: [
        {
          id: 'kurikulum_berbasis_cinta',
          title: 'Kurikulum Berbasis Cinta (KBC)',
          desc: 'Pendekatan penuh kehangatan, empati, apresiasi positif & kedekatan emosional',
          badge: '❤️ Berbasis Cinta'
        },
        {
          id: 'kurikulum_merdeka',
          title: 'Modul Ajar Kurikulum Merdeka',
          desc: 'Lengkap dengan CP, TP, Profil Pelajar Pancasila, Refleksi & LKPD',
          badge: 'Kemendikbud'
        },
        {
          id: 'rpp_1_lembar',
          title: 'RPP 1 Lembar (Efektif)',
          desc: 'Format ringkas 3 komponen utama: Tujuan, Kegiatan, & Asesmen',
          badge: 'Praktis'
        },
        {
          id: 'rpp_2013',
          title: 'RPP Kurikulum 2013 (K13)',
          desc: 'Lengkap dengan KD, Indikator, Langkah Pembelajaran & Rubrik',
          badge: 'Standar K13'
        }
      ]
    };
  },
  computed: {
    fields() {
      return {
        nama_unit: {
          nama_kolom: 'nama_unit',
          label: 'NAMA SATUAN PENDIDIKAN / SEKOLAH',
          input: 'input',
          required: true,
          placeholder: 'Contoh: SD Negeri 01 Pancasila'
        },
        nama_guru:{
          nama_kolom: 'nama_guru',
          label: 'NAMA GURU PENGAMPU',
          input: 'input',
          required: true,
          placeholder: 'Contoh: Budi Santoso, S.Pd.',
          readonly:true,
        },
        nbm_guru: {
          nama_kolom: 'nbm_guru',
          label: 'NBM GURU (OPSIONAL)',
          input: 'input',
          placeholder: '19850312 201001 1 008 atau -',
          readonly:true,
        },
        nama_kepala:{
          nama_kolom: 'nama_kepala',
          label: 'NAMA KEPALA SEKOLAH',
          input: 'input',
          placeholder: 'Contoh: Siti Rahmah, M.Pd.',
          readonly:true,
        },
        semester_keterangan: {
          nama_kolom: 'semester_keterangan',
          label: 'SEMESTER',
          input: 'input',
          placeholder: 'Semester 1 (Ganjil)',
          readonly:true,
        },
        tingkat: {
          nama_kolom: 'tingkat',
          label: 'TINGKAT KELAS & FASE',
          input: 'select',
          required: true,
          readonly:true,
          options: [
            { label: 'MTs / Fase D (Kelas 1)', value: '1' },
            { label: 'MTs / Fase D (Kelas 2)', value: '2' },
            { label: 'MTs / Fase D (Kelas 3)', value: '3' },
            { label: 'SMA/SMK / Fase E (Kelas 10)', value: '4' },
            { label: 'SMA/SMK / Fase F (Kelas 11)', value: '5' },
            { label: 'SMA/SMK / Fase F (Kelas 12)', value: '5' },
          ]
        },
        nama_mapel : {
          nama_kolom: 'nama_mapel',
          label: 'MATA PELAJARAN',
          input: 'input',
          placeholder: 'Contoh: Matematika / IPAS / Bahasa Indonesia',
          readonly:true,
        },
        materi: {
          nama_kolom: 'materi',
          label: 'JUDUL BAB / TOPIK UTAMA PELAJARAN',
          input: 'input',
          readonly:true,
          placeholder: 'Contoh: Ekosistem & Rantai Makanan / Persamaan Linear'
        },
        jam: {
          nama_kolom: 'jam',
          label: 'ALOKASI WAKTU (Jam)',
          input: 'input',
          placeholder: 'Contoh: 16'
        },
        pertemuan: {
          nama_kolom: 'pertemuan',
          label: 'JUMLAH PERTEMUAN',
          input: 'input',
          placeholder: 'Contoh: 16'
        },
        learning_model: {
          nama_kolom: 'learning_model',
          label: 'MODEL PEMBELAJARAN',
          input: 'select',
          placeholder: 'Pilih Model Pembelajaran yang dipakai',
          options: (MODEL_PEMBELAJARAN_OPTIONS || []).map((m) => ({ label: m, value: m })),
          required: true,
        },
        sarana: {
          nama_kolom: 'sarana',
          label: 'SARANA & PRASARANA MEDIA BELAJAR (OPSIONAL)',
          input: 'input',
          placeholder: 'Contoh: Laptop, LCD Projector, Video Animasi, Kartu Gambar, Papan Tulis',
          required: true,
        }
      }
    }
  },
  watch: {
    subjects:{
      immediate:true,
      deep:true,
      handler(val){
        // console.log(val)
        this.handleProtaSelect(this.id)
      }
    },
    viewValue:{
      deep:true,
      handler(val){
        fillObjectValue(this.valueForm, val)
      }
    },
    valueForm:{
      deep:true,
      handler(val){
        this.$emit('update:subjectInfo', val);
      }
    },
    tahunAjaran(val){
      this.selectedTahunAjaran = val
    },
    selectedTahunAjaran(val){
      this.$emit('update:tahunAjaran', val)
    }
  },
  methods: {
    selectCurriculum(curriculumId) {
      this.valueForm.curriculum = curriculumId;
    },
    handleProtaSelect(val) {
      // console.log(val)
      if (!val) return;
      if (isEmpty(this.valueForm?.curriculum))
        this.valueForm.curriculum = this.templateOptions[0].id;

      const [subjId, chapId] = val.split('::');
      if (!subjId || !chapId) return;

      const subj = this.subjects.find((s) => s.id === subjId);
      const chap = subj?.materi.find((c) => c.id === chapId);

        this.$emit('update:id', val)
        this.valueForm.nama_unit = subj.nama_unit;
        this.valueForm.nama_guru = subj.nama_guru;
        this.valueForm.nbm_guru = subj.nbm_guru;
        this.valueForm.nama_kepala = subj.nama_kepala;
        this.valueForm.nama_mapel = subj.nama_mapel;
        this.valueForm.tingkat = subj.tingkat;
        this.valueForm.kelas = subj.kelas;
        this.valueForm.fase = subj.fase;

        this.valueForm.id = chap?.id;
        this.valueForm.no = chap?.no;
        this.valueForm.materi = chap?.materi;
        this.valueForm.jam = chap?.jam;
        this.valueForm.semester = chap?.semester;
        this.valueForm.semester_keterangan = chap?.semester_keterangan;
        this.valueForm.tahun_ajaran = chap?.tahun_ajaran;
        this.valueForm.pertemuan = chap?.pertemuan;
        this.valueForm.learning_model = chap?.learning_model;
        this.valueForm.sarana = chap?.sarana;
        this.valueForm.curriculum = chap?.curriculum ?? this.templateOptions[0].id;
        this.valueForm.cp = chap?.cp;
        this.valueForm.sub_materi = chap?.sub_materi;
        this.valueForm.rpp = chap?.rpp ? JSON.parse(chap?.rpp) : ''

        // console.log(this.valueForm)

        
        fillObjectValue(this.viewValue, this.valueForm)
        // this.formKey++

      // if (subj && chap) {
      //   this.$emit('select-chapter-from-prota', { subject: subj, chapter: chap });
      // }
    },
    handleSubmit() {
      let form = {
        id:this.valueForm.id,
        jam:this.valueForm.jam,
        pertemuan:this.valueForm.pertemuan,
        learning_model:this.valueForm?.learning_model ?? '',
        sarana:this.valueForm.sarana,
      }
      // console.log(this.valueForm, form)
      this.$http.post('mapel/materi/store', window.jsonToFormData(form))
      .then(res => {
        this.$emit('next');
      })
      .catch(err => {
        var res = err.response;
        this.valueError = res.data.messages
      })
      // if (!this.valueForm.subject || !this.valueForm.topic) {
      //   this.$message ? this.$message.error('Mohon isi Mata Pelajaran dan Judul Topik Pembelajaran.') : alert('Mohon isi Mata Pelajaran dan Judul Topik Pembelajaran.');
      //   return;
      // }
    },
  },
  mounted(){
    // console.log(this.valueForm)
    this.selectedProtaChapter = this.id
    this.selectedTahunAjaran = this.tahunAjaran
    this.handleProtaSelect(this.id)
    // this.formKey++
  }
};
</script>
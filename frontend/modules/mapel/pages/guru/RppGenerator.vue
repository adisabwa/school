<template>
  <div class="bg-white border-b border-slate-200 py-3.5 px-4 sm:px-6 shadow-xs">
    <div class="mx-auto">
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-4">
        <el-card
          v-for="s in steps"
          :key="s.number"
          shadow="hover"
          :class="[
            '!border transition-all select-none !rounded-lg w-full',
            currentStep === s.number
              ? '!bg-sky-50 !border-sky-600 shadow-xs cursor-pointer'
              : isCompleted(s.number)
              ? '!bg-emerald-50/70 !border-emerald-200 hover:!bg-emerald-100/50 cursor-pointer'
              : isSelectable(s.number)
              ? '!bg-slate-50 !border-slate-200 hover:!bg-slate-100 cursor-pointer'
              : 'opacity-40 !cursor-not-allowed !bg-slate-50 !border-slate-100'
          ]"
          :body-style="{ padding: '10px' }"
          @click="handleStepClick(s.number)"
        >
          <div class="flex items-center space-x-3">
            <div
              :class="[
                'w-9 h-9 rounded-md flex items-center justify-center font-bold text-sm shrink-0 transition',
                currentStep === s.number
                  ? 'bg-sky-600 text-white shadow-xs'
                  : isCompleted(s.number)
                  ? 'bg-emerald-600 text-white'
                  : 'bg-slate-200 text-slate-600'
              ]"
            >
              <icons :icon="s.icon" class="w-5 h-5 m-0" />
            </div>

            <div class="min-w-0">
              <div class="flex items-center space-x-1">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">
                  Langkah {{ s.number }}
                </span>
              </div>
              <div class="text-xs sm:text-sm font-bold truncate leading-tight">
                {{ s.title }}
              </div>
            </div>
          </div>
        </el-card>
      </div>
      <el-card class="w-full mt-3">
        <Identitas v-if="currentStep == '1'"
          :key="formKey"
          :subjects="subjects"
          :option-tahun-ajaran="optionTahunAjaran"
          v-model:subject-info="subjectInfo"
          v-model:id="selectedMateri"
          v-model:tahun-ajaran="tahunAjaran"
          @next="currentStep++; maxStepReached = maxStepReached < 2 ? 2 : maxStepReached"
        />
        <Materi v-else-if="currentStep == '2'"
          :key="formKey+1"
          v-model:subject-info="subjectInfo"
          @next="currentStep++; maxStepReached = maxStepReached < 3 ? 3 : maxStepReached"
          @back="currentStep--;"
        />
        <Asesmen v-else-if="currentStep == '3'"
          :key="formKey+2"
          v-model:subject-info="subjectInfo"
          v-model:assessment-config="assessmentConfig"
          v-model:is-generating="isGenerating"
          @back="currentStep--;"
          @generate-rpp="handleGenerateRpp"
        />
        <RppViewer v-else-if="currentStep == '4'"
          :key="formKey+3"
          v-model:rpp="rpp"
          @back="currentStep--;"
          @reset="currentStep = 1; maxStepReached = 1; selectedMateri = null;"
          />
      </el-card>  
    </div>
  </div>
</template>

<script>
import { mapState } from 'pinia'
import Identitas from './components/rpp/Step1Identitas.vue';
import Materi from './components/rpp/Step2Materi.vue';
import Asesmen from './components/rpp/Step3Asesmen.vue';
import RppViewer from './components/rpp/Step4RppViewer.vue';

export default {

  name: 'RppGenerator',
  components: {
    Identitas,
    Materi,
    Asesmen,
    RppViewer,
  },
  props:{
    id:{
      type:[String, Number],
      default:null,
    },
  },
  data() {
    return {
      steps: [
        {
          number: 1,
          title: 'Identitas Mapel',
          icon: 'mdi:account-check-outline',
          desc: 'Sekolah & Kurikulum'
        },
        {
          number: 2,
          title: 'Materi & Pembahasan',
          icon: 'mdi:book-open-page-variant-outline',
          desc: 'Generate/Edit Materi'
        },
        {
          number: 3,
          title: 'Jenis Asesmen',
          icon: 'mdi:checkbox-marked-outline',
          desc: 'Diagnostik, Formatif, Sumatif'
        },
        {
          number: 4,
          title: 'Hasil RPP & Cetak',
          icon: 'mdi:file-document-check-outline',
          desc: 'PDF & Export Word'
        }
      ],
      subjects:[],
      optionTahunAjaran:[],
      subjectInfo:{
      },
      rpp:{},
      assessmentConfig:{
        diagnostic: { enabled: true, types: [], notes: '' },
        formative: { enabled: true, types: [], notes: '' },
        summative: { enabled: true, types: [], notes: '' }
      },
      allInfo:{},
      selectedMateri:'',
      currentStep: 1,
      maxStepReached: 1,
      tahunAjaran:'',
      isGenerating: false,
      formKey:1,
    };
  },
  computed:{ 
    ...mapState(useAuthStore,{
      user: 'loggedUser'
    }),
  },
  watch:{
    id(val){
      console.log(val)
      this.getIdFromQuery()
    },
    currentStep(val){
      if(val == 1){
        this.getTahunAjaran()
      }
    },
    subjectInfo:{
      deep: true,
      handler(val){
        this.rpp = {}
        if (this.subjectInfo.rpp) {
          this.rpp = this.subjectInfo.rpp
        } else {
          this.rpp.subjectInfo = val
        }
        if (!this.rpp?.tanggal)
          this.rpp.tanggal = dateNow()
      }
    },
    tahunAjaran(val){
      this.getSubject()
    }
  },
  methods: {
    getTahunAjaran(){
      this.$http.get('data/semester/options_tahun_ajaran')
        .then(res => {
          this.optionTahunAjaran = res?.data
          this.tahunAjaran = this.optionTahunAjaran[0].value
          this.getSubject()
        })
    },
    getSubject(){
      this.$http.get('mapel/materi/summary', {
        params:{
          tahun_ajaran:this.tahunAjaran,
          id_guru:this.user.id_guru,
        }
      })
      .then(res => {
        this.subjects = res?.data
        this.getIdFromQuery()
        // this.selectedSubjectId = this.subjects[0].id
      })
    },
    getIdFromQuery(){
      let queryId = this.id ? this.id : this.$route?.query?.id
      if (!queryId) return

      // console.log(this.id, queryId, this.subjects)
      this.subjects.forEach(s => {
        s.materi.forEach(m => {
          if (m.id == queryId)
            this.selectedMateri = `${s.id}::${m.id}`
        })
      })
      console.log('materi', this.selectedMateri)
      setTimeout(() => {
        this.maxStepReached = 4
        this.currentStep = 4
        this.formKey++
      }, 1000);
    },
    isCompleted(stepNumber) {
      return this.currentStep > stepNumber;
    },
    isSelectable(stepNumber) {
      return stepNumber <= this.maxStepReached;
    },
    handleStepClick(stepNumber) {
      if (this.isSelectable(stepNumber)) {
        this.currentStep = stepNumber;
      }
    },
    async handleGenerateRpp() {
      this.isGenerating = true;
      this.$http.post('mapel/materi/generate-rpp', window.jsonToFormData({
        data_mapel: this.subjectInfo,
        konfigurasi_asesmen: this.assessmentConfig,
      }))
        .then(res => {
        this.isGenerating = false;
          let data = res?.data
          this.currentStep++;
          this.maxStepReached = 4
          this.rpp = {}
          this.rpp = {...{
            subjectInfo: this.subjectInfo
          },...data}
        })
        .catch(err => {
          this.isGenerating = false;
          console.log(err)

        })
    },
  },
  mounted(){
    this.getTahunAjaran()
    // this.id = '12847::30'
    // this.handleGenerateRpp()
    // this.currentStep = 3
  }
};
</script>
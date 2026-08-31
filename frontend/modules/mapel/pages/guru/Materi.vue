<template>
  <div class="space-y-2 max-w-6xl mx-auto">
    <!-- Header Bar -->
    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="space-y-1">
        <div class="flex items-center space-x-2 text-indigo-600 font-bold text-xs uppercase tracking-wider">
          <icons icon="mdi:book-open" class="w-4 h-4 m-0" />
          <span>Manajemen Kurikulum & Silabus</span>
        </div>
        <h2 class="text-xl font-bold text-slate-900 tracking-tight">
          Mata Pelajaran & Daftar Bab Materi
        </h2>
        <p class="text-xs text-slate-500">
          Kelola daftar mata pelajaran, alokasi jam pelajaran (JP), dan buat RPP per bab secara langsung.
        </p>
      </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white rounded-md shadow-sm p-3 px-5">
      <div class="uppercase text-slate-400 font-extrabold text-sm mb-2">Filter Data</div>
      <el-form class="m-0 flex justify-between">
        <el-form-item label="Tahun Ajaran">
          <floating-select v-model:value="tahunAjaran" :options="yearList" />
        </el-form-item>
        <el-form-item label="Pencarian">
          <el-input v-model="searchTerm" class="w-[200px]" placeholder="Cari ....."/>
        </el-form-item>
      </el-form>
    </div>

    <!-- Subjects Accordion List -->
    <div class="space-y-2">
      <div v-if="filteredSubjects.length === 0" class="bg-white rounded-xl border border-slate-200 p-12 text-center text-slate-400 space-y-2">
        <icons  class="w-12 h-12 mx-auto stroke-1 text-slate-300" />
        <p class="text-sm font-semibold text-slate-700">Tidak ada mata pelajaran ditemukan</p>
        <p class="text-xs text-slate-500 max-w-sm mx-auto">
          Coba sesuaikan kata kunci pencarian
        </p>
      </div>

      <template v-else>
        <el-card shadow="never" class="!rounded-xl !border-slate-200 overflow-hidden"
          v-for="(subject, indSub) in filteredSubjects">
          <!-- Subject Header -->
          <div class="-m-5 p-4 bg-slate-50/80 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start space-x-3">
              <el-button
                link
                @click="subject.is_expand = !subject.is_expand; indexExpand = indSub"
                class="!p-1 text-slate-400 hover:text-slate-600 mt-0.5"
              >
                <icons :icon="subject.is_expand ? 'mdi:chevron-up' : 'mdi:chevron-down'" class="w-5 h-5" />
              </el-button>

              <div>
                <div class="flex flex-wrap items-center gap-3">
                  <el-tag type="primary" size="small" effect="dark" class="!font-bold">
                    {{ subject.kode_mapel }}
                  </el-tag>
                  <el-tag type="info" size="small" effect="plain" class="!font-semibold">
                    Kelas {{ subject.tingkat }}
                  </el-tag>
                  <el-tag type="success" size="small">
                    <div class="!font-bold flex items-center gap-1">
                      <icons icon="mdi:clock-outline" class="w-3.5 h-3.5 inline" />
                      {{ parseInt(subject.jam) * parseInt(subject.minggu) }} JP Total
                    </div>
                  </el-tag>
                  <el-tag type="primary" size="small">
                    <div class="!font-bold flex items-center gap-1">
                      <icons icon="mdi:book-open" class="w-3.5 h-3.5 inline" />
                      {{ subject.materi.length }} Materi
                    </div>
                  </el-tag>
                  <el-tag type="warning" size="small" class="!font-bold">
                    ⚡ {{ subject.jam }} JP/Minggu ({{ subject.pertemuan > 0 ? subject.pertemuan : 1 }}x Pertemuan @ {{ subject.jam_per_pertemuan || subject.jam }} JP)
                  </el-tag>
                </div>

                <div class="font-bold text-slate-900 text-[17px] mt-3">
                  {{ subject.nama_mapel }}
                </div>

                <div class="text-xs text-slate-500 mt-1">
                  Guru Pengampu: {{ subject.nama_guru }}
                </div>
              </div>
            </div>

            <div class="grid grid-rows-2 grid-flow-col gap-2 shrink-0 *:m-0">
              <el-button
                type="primary"
                size="small"
                class="!font-bold rounded-[5px] w-full"
                @click="showAddSubjectModal = true; dataSubject = subject; dataIdMapel = subject.id;"
              >
                <icons icon="mdi:edit" class="text-lg" />
                Edit Mapel
              </el-button>
              <el-button
                type="success"
                size="small"
                class="!font-bold rounded-[5px] w-full"
                @click="showAddChapterModal = true; dataSubject = subject; dataId = -1;"
              >
                <icons icon="mdi:plus" class="text-lg" />
                Tambah Bab
              </el-button>
              <el-button
                type="primary"
                size="small"
                class="!font-bold rounded-[5px] w-full"
                @click="generateMateri(subject); indexExpand = indSub"
              >
                <icons icon="ant-design:gemini-filled" class="text-lg" />
                Generate Materi
              </el-button>
            </div>
          </div>

          <!-- Chapters List -->
          <div v-if="subject.is_expand" class="-mx-5 -mb-5 p-5 space-y-3 bg-white mt-5 overflow-y-g">
            <div v-if="!subject.materi || subject.materi.length === 0" class="text-center py-6 text-slate-400 text-xs border border-dashed border-slate-200 rounded-lg">
              Belum ada bab materi. Klik "Tambah Bab" untuk memasukkan silabus bab.
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
              <ChapterCard
                v-for="chap in subject.materi"
                :chapter="chap"
                @delete="deleteChapter(chap)"
                @edit="(event) => {
                  showAddChapterModal = true;
                  dataId = chap.id;
                  dataSubject = subject;
                }"
                @select-for-rpp="$emit('select-chapter-for-rpp', { subject, chapter: chap })"
              />
            </div>
          </div>
        </el-card>
      </template>
    </div>

    <EditSubjectModal 
      :show="showAddSubjectModal" 
      v-model:id="dataIdMapel"
      @close="showAddSubjectModal = false"  
      @saved="getSubject"
      />
    <!-- Modal: Tambah Bab Baru -->
    <AddChapterModal
      :show="showAddChapterModal"
      :defaultChapterNumber="nextChapterNumber"
      v-model:id="dataId"
      v-model:subject="dataSubject"
      :semester-options="semesterOptions"
      @close="showAddChapterModal = false"  
      @saved="getSubject"
    />
  </div>
</template>

<script>
import { mapState } from 'pinia'
import ChapterCard from './components/ChapterCard.vue';
import AddChapterModal from './components/AddChapterModal.vue';
import EditSubjectModal from './components/EditSubjectModal.vue';
import { sum } from 'lodash';

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
  name: 'SubjectsView',
  components: {
    // BookOpen,
    // Plus,
    // SubjectFilter,
    EditSubjectModal,
    ChapterCard,
    AddChapterModal
  },
  data() {
    return {
      yearList:yearList,
      tahunAjaran:'',
      subjects:[],
      dataId:'',
      dataIdMapel:'',
      dataSubject:{},
      searchTerm: '',
      showAddSubjectModal: false,
      showAddChapterModal: false,
      indexExpand:0,
    };
  },
  computed: {
    ...mapState(useAuthStore,{
      user:'loggedUser',
    }),
    filteredSubjects() {
      return this.subjects?.filter?.((s) => {
        const searchLower = this.searchTerm.toLowerCase();
        const matchesSearch =
          s?.nama_mapel?.toLowerCase().includes(searchLower) ||
          (s.materi && s.sub_materi.some((c) => c.toLowerCase().includes(searchLower)));
        return matchesSearch;
      });
    },
    nextChapterNumber() {
      return (this.dataSubject?.materi?.length || 0) + 1;
    }
  },
  watch: {
    tahunAjaran(val){
      this.$http.get('data/semester/options',{
        params:{
          where:{
            tahun_ajaran:val
          }
        }
      }).then(res => {
        this.semesterOptions = res?.data
      })
      this.getSubject()
    }
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
        this.subjects[this.indexExpand].is_expand = true
      })
    },
    deleteChapter(chapter){
      useDataStore().deleteData({href:'mapel/materi/delete', id:chapter.id})
        .then(res => {
          this.getSubject()
        })
    },
    generateMateri(subject){
        let params = {
          id: subject.id,
          id_semester: subject.id_semester,
          id_mapel: subject.id_mapel,
          id_guru: subject.id_guru,
          nama_mapel: subject.nama_mapel,
          nama_unit: subject.nama_unit,
          tingkat: subject.tingkat,
          jam: subject.jam,
          pertemuan: subject.pertemuan,
          jam_per_pertemuan: subject.jam_per_pertemuan,
          minggu:subject.minggu,
          jml_materi:subject.materi.length,
        }
        this.$confirm('Apakah anda ingin menghapus materi yang sudah ada atau menambahkan materi baru?', 'Konfirmasi', {
          confirmButtonText: 'Hapus Materi',
          cancelButtonText: 'Tambah Materi',
          closeButtonText:'Batal',
          cancelButtonClass : 'bg-sky-500 text-white border-0',
          confirmButtonClass: 'bg-teal-500 text-white border-0',
          type: 'warning',
          distinguishCancelAndClose: true,
        }).then(() => {
          params = {...params,
            ...{
              reset: '1'
            }}
          this.runGenerate(params)
        }).catch((action) => {
          console.log(action)
          if (action == 'cancel') {
            params = {...params,
              ...{
                jam_lama:subject.materi.reduce((sum, el) => sum + parseInt(el.jam), 0),
                pertemuan_lama:subject.materi.reduce((sum, el) => sum + parseInt(el.pertemuan), 0),
                reset: '0'
              }}
            this.runGenerate(params)
          } 
          // Batal menghapus
        });
    },
    runGenerate(params){
      this.$http.post(`mapel/materi/generate-list-materi`, window.jsonToFormData(params))
          .then(result => {
            this.$notify({
              type:'success',
              title: 'Berhasil',
              message: 'Materi berhasil di generate',
              position: 'bottom-right'
            });
            this.getSubject()
          })
          .catch(error => {
            this.$notify({
              type:'error',
              title: 'Gagal',
              message: 'Tidak dapat mengenerte materi',
              position: 'bottom-right'
            });
          });
    }
  },
  mounted() {
    this.tahunAjaran = yearList[1]?.value
    this.getSubject()
  }
};
</script>
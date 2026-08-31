<template>
  <div class="space-y-4 mx-auto font-sans bg-white/70 px-2">
    <!-- Hero Welcome Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-[var(--color-main-90  0)] to-slate-900 text-white rounded-2xl p-6 sm:p-8 shadow-sm border border-solid border-slate-800 space-y-4 relative overflow-hidden">

      <div class="relative z-10 space-y-2">
        <div class="inline-flex items-center space-x-2 bg-[var(--color-main-900)] text-[var(--color-main-300)] px-3 py-1 rounded-full text-xs font-semibold border border-solid border-[var(--color-main-50)]0/30">
          <icons icon="mdi:sparkles" class="w-3.5 h-3.5 text-amber-300 m-0" />
          <span>Platform Perangkat Ajar Kurikulum Merdeka AI</span>
        </div>

        <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">
          Selamat Datang di Perangkat AI! 👋
        </h2>

        <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
          Susun Perangkat Ajar lengkap meliputi <strong>RPP / Modul Ajar</strong>, <strong>Program Tahunan (Prota)</strong>, dan <strong>Program Semester (Promes)</strong> secara otomatis, adaptif, dan berstandar Kemendikbudristek.
        </p>

        <div class="pt-2 flex flex-wrap gap-3">
          <el-button
            type="primary"
            class="!bg-[var(--color-main-600)] hover:!bg-[var(--color-main-50)]0 !text-white !font-bold !text-xs !py-2.5 !px-4 !rounded-lg !shadow-sm !border-[var(--color-main-400)]/30"
            @click="this.$router.push({name:'mapel-rpp'})"
          >
            <icons icon="mdi:sparkles" class="w-5 h-5 text-amber-300" />
            <span>Buat RPP Baru Sekarang</span>
          </el-button>

          <el-button
            class="!bg-slate-800 hover:!bg-slate-700 !text-slate-200 !font-semibold !text-xs !py-2.5 !px-4 !rounded-lg !border-slate-700"
            @click="this.$router.push({name:'mapel-prota'})"
          >
            <icons icon="mdi:calendar-month-outline" class="w-5 h-5 text-[var(--color-main-400)]" />
            <span>Generate Prota & Promes</span>
          </el-button>
        </div>
      </div>
    </div>

    <!-- Metric Cards Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl border border-solid border-slate-200 p-4 shadow-xs space-y-1">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Mapel Diampu</span>
          <div class="w-8 h-8 rounded-lg bg-[var(--color-main-50)] border border-solid border-[var(--color-main-100)] flex items-center justify-center text-[var(--color-main-600)]">
            <icons icon="mdi:book-open-page-variant-outline" class="w-5 h-5 m-0" />
          </div>
        </div>
        <p class="text-2xl font-black text-slate-900">{{ totalSubjects }}</p>
        <p class="text-[11px] text-slate-500">Mata pelajaran aktif</p>
      </div>

      <div class="bg-white rounded-xl border border-solid border-slate-200 p-4 shadow-xs space-y-1">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Bab / Topik</span>
          <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-solid border-emerald-100 flex items-center justify-center text-emerald-600">
            <icons icon="mdi:layers-outline" class="w-5 h-5 m-0" />
          </div>
        </div>
        <p class="text-2xl font-black text-slate-900">{{ totalChapters }}</p>
        <p class="text-[11px] text-slate-500">Bab alokasi materi</p>
      </div>

      <div class="bg-white rounded-xl border border-solid border-slate-200 p-4 shadow-xs space-y-1">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Jam Pelajaran</span>
          <div class="w-8 h-8 rounded-lg bg-purple-50 border border-solid border-purple-100 flex items-center justify-center text-purple-600">
            <icons icon="mdi:clock-outline" class="w-5 h-5 m-0" />
          </div>
        </div>
        <p class="text-2xl font-black text-slate-900">
          {{ totalJP }} <span class="text-xs font-normal text-slate-500">JP</span>
        </p>
        <p class="text-[11px] text-slate-500">Alokasi JP per tahun</p>
      </div>

      <div class="bg-white rounded-xl border border-solid border-slate-200 p-4 shadow-xs space-y-1">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">RPP Tersimpan</span>
          <div class="w-8 h-8 rounded-lg bg-amber-50 border border-solid border-amber-100 flex items-center justify-center text-amber-600">
            <icons icon="mdi:file-document-outline" class="w-5 h-5 m-0" />
          </div>
        </div>
        <p class="text-2xl font-black text-slate-900">{{ totalSavedRPPs }}</p>
        <p class="text-[11px] text-slate-500">Dokumen siap cetak</p>
      </div>
    </div>

    <!-- Main Module Cards -->
    <div class="space-y-3">
      <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
        <icons icon="mdi:school-outline" class="text-[20px] text-[var(--color-main-600)] m-0" />
        <span>Modul Perangkat Ajar Utama</span>
      </h3>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card 1: Generator RPP -->
        <div class="bg-white rounded-xl border border-solid border-slate-200 p-5 shadow-xs hover:border-teal-300 transition flex flex-col justify-between space-y-4">
          <div class="space-y-2">
            <div class="w-10 h-10 rounded-xl bg-teal-50 border border-solid border-teal-100 flex items-center justify-center text-teal-600">
              <icons icon="mdi:file-document-outline" class="w-5 h-5 m-0" />
            </div>
            <h4 class="font-bold text-slate-900 text-base">Generator RPP & Modul Ajar</h4>
            <p class="text-xs text-slate-500 leading-relaxed">
              Penyusun RPP otomatis 4 langkah lengkap dengan Kesiapan Belajar, TP, Langkah Inti, Asesmen Adaptif, dan Rubrik Penilaian.
            </p>
          </div>
          <el-button
            
            class="w-full bg-slate-50 hover:bg-teal-50 text-teal-700 font-bold text-xs py-2 px-3 rounded-md border border-solid border-slate-200 hover:border-teal-200 transition flex items-center justify-between cursor-pointer"
            @click="this.$router.push({name:'mapel-rpp'})"
          >
            <span>Buka Generator RPP</span>
            <icons icon="mdi:arrow-right" class="w-5 h-5 m-0" />
          </el-button>
        </div>

        <!-- Card 2: Prota & Promes AI -->
        <div class="bg-white rounded-xl border border-solid border-slate-200 p-5 shadow-xs hover:border-sky-500 transition flex flex-col justify-between space-y-4">
          <div class="space-y-2">
            <div class="w-10 h-10 rounded-xl bg-sky-50 border border-solid border-sky-100 flex items-center justify-center text-sky-600">
              <icons icon="mdi:calendar-month-outline" class="w-5 h-5 m-0" />
            </div>
            <h4 class="font-bold text-slate-900 text-base">Prota & Promes AI</h4>
            <p class="text-xs text-slate-500 leading-relaxed">
              Susun Program Tahunan (PROTA) dan Program Semester (PROMES) lengkap dengan alokasi JP per minggu, bulan, dan semester.
            </p>
          </div>
          <el-button
            
            class="w-full bg-slate-50 hover:bg-sky-50 text-sky-700 font-bold text-xs py-2 px-3 rounded-md border border-solid border-slate-200 hover:border-sky-200 transition flex items-center justify-between cursor-pointer"
            @click="this.$router.push({name:'mapel-prota'})"
          >
            <span>Buat Prota & Promes</span>
            <icons icon="mdi:arrow-right" class="w-5 h-5 m-0" />
          </el-button>
        </div>

        <!-- Card 3: Mata Pelajaran & Bab -->
        <div class="bg-white rounded-xl border border-solid border-slate-200 p-5 shadow-xs hover:border-indigo-300 transition flex flex-col justify-between space-y-4">
          <div class="space-y-2">
            <div class="w-10 h-10 rounded-xl bg-purple-50 border border-solid border-purple-100 flex items-center justify-center text-purple-600">
              <icons icon="mdi:book-open-page-variant-outline" class="w-5 h-5 m-0" />
            </div>
            <h4 class="font-bold text-slate-900 text-base">Mata Pelajaran & Bab Materi</h4>
            <p class="text-xs text-slate-500 leading-relaxed">
              Kelola daftar mata pelajaran, alokasi JP per bab, dan buat RPP per bab secara instan langsung dari daftar bab.
            </p>
          </div>
          <el-button
            
            class="w-full bg-slate-50 hover:bg-purple-50 text-purple-700 font-bold text-xs py-2 px-3 rounded-md border border-solid border-slate-200 hover:border-purple-200 transition flex items-center justify-between cursor-pointer"
            @click="this.$router.push({name:'mapel-materi'})"
          >
            <span>Kelola Mapel & Bab</span>
            <icons icon="mdi:arrow-right" class="w-5 h-5 m-0" />
          </el-button>
        </div>
      </div>
    </div>

    <!-- Quick Chapter Launch Section ("Buat RPP per Bab") -->
    <div class="bg-white rounded-xl border border-solid border-slate-200 p-6 shadow-xs space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div>
          <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
            <icons icon="mdi:sparkles" class="w-5 h-5 text-amber-500 m-0" />
            <span>Buat RPP Langsung Per Bab Materi</span>
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">
            Pilih bab pelajaran di bawah ini untuk langsung mengisi otomatis data mata pelajaran dan sub-materi ke dalam Generator RPP.
          </p>
        </div>

        <el-button
          
          class="text-xs font-semibold text-[var(--color-main-600)] hover:text-[var(--color-main-800)] flex items-center gap-1 cursor-pointer"
          @click="this.$router.push({name:'mapel-materi'})"
        >
          <span>Lihat Semua Bab</span>
          <icons icon="mdi:chevron-right" class="w-5 h-5 m-0" />
        </el-button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div
          v-for="item in flattenedChapters"
          :key="`${item.subject.id}-${item.materi.id}`"
          class="bg-slate-50 border border-solid border-slate-200 rounded-lg p-3.5 space-y-2 hover:border-[var(--color-main-300)] transition
            flex flex-col justify-between"
        >
          <div>
            <div class="flex items-start justify-between gap-2">
              <div>
                <span class="text-[10px] font-bold bg-[var(--color-main-100)] text-[var(--color-main-800)] px-2 py-0.5 rounded uppercase">
                  {{ item.subject.nama_mapel }} • {{ item.materi.semester_keterangan }}
                </span>
                <h4 class="text-xs font-bold text-slate-900 mt-1">
                  Bab {{ item.materi.no }}: {{ item.materi.materi }}
                </h4>
              </div>
              <span class="text-[10px] bg-slate-200 text-slate-700 font-semibold px-2 py-0.5 rounded shrink-0">
                {{ item.materi.jam }} JP
              </span>
            </div>
            
            <ol class="text-[11px] text-slate-500 line-clamp-2 my-1 pl-3">
              <li class="pl-1"
                v-for="sub in item.materi.sub_materi?.map(d => d.sub_materi)">
                {{ sub }}
              </li>
            </ol>
          </div>

          <el-button
            
            class="w-full bg-[var(--color-main-600)] hover:bg-[var(--color-main-50)]0 text-white font-bold text-xs py-1.5 px-3 rounded shadow-2xs transition flex items-center justify-center space-x-1.5 cursor-pointer"
            @click="handleSelectChapter(item.subject, item.materi)"
          >
            <icons icon="mdi:sparkles" class="w-3.5 h-3.5 text-amber-300 m-0" />
            <span>⚡ Buat RPP Bab Ini</span>
          </el-button>
        </div>
      </div>
    </div>

    <!-- Recent RPPs Section -->
    <div class="bg-white rounded-xl border border-solid border-slate-200 p-6 shadow-xs space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div>
          <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
            <icons icon="mdi:folder-open-outline" class="w-5 h-5 text-[var(--color-main-600)] m-0" />
            <span>Dokumen RPP & Modul Ajar Terbaru</span>
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">
            Total {{ savedRPPs.length }} dokumen tersimpan di browser Anda.
          </p>
        </div>

        <el-button
          
          class="text-xs font-semibold text-[var(--color-main-600)] hover:text-[var(--color-main-800)] flex items-center gap-1 cursor-pointer"
          @click="this.$router.push({name:'mapel-rpp-history'})"
        >
          <span>Buka Semua Dokumen ({{ savedRPPs.length }})</span>
          <icons icon="mdi:chevron-right" class="w-5 h-5 m-0" />
        </el-button>
      </div>

      <!-- Empty State -->
      <div v-if="savedRPPs.length === 0" class="text-center py-8 text-slate-400 space-y-2">
        <icons icon="mdi:file-document-outline" class="w-10 h-10 mx-auto stroke-1 text-slate-300 m-0" />
        <p class="text-xs font-semibold text-slate-600">Belum ada RPP tersimpan</p>
        <p class="text-[11px] text-slate-400">
          Gunakan Generator RPP untuk menyusun RPP otomatis dan menyimpannya.
        </p>
      </div>

      <!-- Recent RPP List -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3">
        <div
          v-for="rpp in recentRPPs"
          :key="rpp.id"
          class="bg-slate-50 border border-solid border-slate-200 rounded-lg p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-[var(--color-main-300)] transition"
        >
          <div class="space-y-1">
            <div class="flex items-center gap-2">
              <span class="text-[10px] font-bold uppercase bg-[var(--color-main-100)] text-[var(--color-main-800)] px-2 py-0.5 rounded">
                Kelas {{ rpp.subjectInfo?.tingkat }}
              </span>
              <span class="text-[10px] text-slate-400">
                {{ dateIndo(rpp.created_at) }}
              </span>
            </div>
            <h4 class="font-bold text-slate-900 text-xs">
              {{ rpp.subjectInfo?.nama_mapel }} — {{ rpp.subjectInfo?.materi }}
            </h4>
            <p class="text-[11px] text-slate-500">
              Guru: {{ rpp.subjectInfo?.nama_guru }} • {{ rpp.subjectInfo?.nama_unit }}
            </p>
          </div>

          <div class="flex items-center space-x-2 shrink-0">
            <el-button
              type="primary"
              size="small"
              class="!bg-[var(--color-main-600)] hover:!bg-[var(--color-main-700)] !text-white !font-semibold !text-xs"
              @click="this.$router.push({name:'mapel-rpp',query:{id:rpp?.subjectInfo?.id}})"
            >
              Buka Dokumen
            </el-button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'pinia'

export default {
  name: 'DashboardView',
  setup(){
    return {
      dateIndo
    }
  },
  data(){
    return {
      subjects:[],
      savedRPPs:[],
      semester:{},
    }
  },
  computed: {
    ...mapState(useAuthStore,{
      user:'loggedUser'
    }),
    totalSubjects() {
      return this.subjects.length;
    },
    totalChapters() {
      return this.subjects.reduce((sum, s) => sum + (s.materi?.length || 0), 0);
    },
    totalJP() {
      return this.subjects.reduce((sum, s) => sum + parseInt(s.jam || 0), 0);
    },
    totalSavedRPPs() {
      return this.savedRPPs.length;
    },
    flattenedChapters() {
      return this.subjects
        .filter(s => s.materi.length > 0)
        .map((s) => {
          return {subject: s, materi: s.materi[s.materi.length - 1] }
        })
        .slice(0, 4);
    },
    recentRPPs() {
      return this.savedRPPs.slice(0, 3);
    }
  },
  methods: {
    getInitial(){
      this.$http.get('data/semester/semester_now')
        .then(res => {
          this.semester = res?.data
          this.getSubject()
          this.getRpp()
        })
    },
    getSubject(){
      this.$http.get('mapel/materi/summary',{
        params:{
          'id_guru': this.user.id_guru,
          'tahun_ajaran': this.semester.tahun_ajaran
        }
      }).then(res => {
        this.subjects = res?.data
      })
    },
    getRpp(){
      this.$http.get('mapel/materi', {
        params:{
          where:{
            '{n}tahun_ajaran': this.semester.tahun_ajaran,
            '{n}id_guru' : this.user.id_guru,
          },
          order:['created_at']
        }
      }).then(res => {
        let materis = res?.data.filter(d => {
          try {
            return JSON.parse(d?.rpp)
          } catch {
            return false
          }
        })
        this.savedRPPs = materis.map(d => {
          let rpp = JSON.parse(d?.rpp)
          
          return {
            ...rpp,
            ...{
              created_at: d.created_at
            }
          }
        })
      })
    },
    handleSelectChapter(subject, materi) {
      this.$emit('select-materi-for-rpp', subject, materi);
    },
    formatDate(dateString) {
      if (!dateString) return '-';
      return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
      });
    }
  },
  created(){
    this.getInitial()
  }
};
</script>
<template>
  <div class="space-y-4">
    <!-- Semester Selector inside Promes -->
    <div class="print:hidden flex flex-col sm:flex-row items-center justify-between gap-3 bg-slate-100 p-2.5 rounded-lg border border-slate-200">
      <div class="flex items-center space-x-2">
        <span class="text-xs font-bold text-slate-700 uppercase">Pilih Semester Promes:</span>
        <el-button
          :type="promesSemester === 'sem1' ? 'primary' : 'default'"
          size="small"
          class="!font-bold"
          @click="promesSemester = 'sem1'"
        >
          Semester 1 (Ganjil: Juli - Des)
        </el-button>
        <el-button
          :type="promesSemester === 'sem2' ? 'primary' : 'default'"
          size="small"
          class="!font-bold"
          @click="promesSemester = 'sem2'"
        >
          Semester 2 (Genap: Jan - Juni)
        </el-button>
      </div>

      <!-- <span class="text-xs text-[var(--color-main-800)] font-semibold bg-[var(--color-main-50)] px-2.5 py-1 rounded border border-[var(--color-main-200)]">
        💡 Alokasi per minggu di bawah ini dapat diubah langsung
      </span> -->
    </div>

    <!-- Promes Matrix Table -->
    <div class="overflow-x-auto">
      <table class="w-full border-collapse border border-slate-400 text-left text-[11px] [&_td]:border-solid [&_th]:border-solid">
        <thead>
          <tr class="bg-slate-100 print:bg-slate-200 text-slate-900 font-bold border-b border-slate-400 text-center">
            <th rowSpan="2" class="p-1.5 border border-slate-400 w-8">No</th>
            <th rowSpan="2" class="p-1.5 border border-slate-400 text-left min-w-[200px]">Materi / Bab Pembelajaran</th>
            <th rowSpan="2" class="p-1.5 border border-slate-400 w-12">Total JP</th>
            <th v-for="(m, key) in activePromesMonths" :key="m" :colSpan="weekPerMonth[key]?.length" class="p-1.5 border border-slate-400 uppercase bg-slate-100">
              {{ m }}
            </th>
          </tr>
          <tr class="bg-slate-50 text-slate-700 text-center">
            <template v-for="(m, mIndx) in activePromesMonths" :key="m">
              <th v-for="wIdx in weekPerMonth[mIndx]" class="p-1 border border-slate-400 w-7 text-[10px]">
                M{{ wIdx + 1 }}
              </th>
            </template>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(entry, idx) in currentPromesEntries" :key="idx" class="hover:bg-slate-50 border-b border-slate-400">
            <td class="p-1.5 border border-slate-400 text-center font-bold">{{ entry.no || idx + 1 }}</td>
            <td class="p-1.5 border border-slate-400 font-semibold text-slate-900">
              Bab {{ entry.no }}: {{ entry.materi }}
            </td>
            <td class="p-1.5 border border-slate-400 text-center font-black text-[var(--color-main-900)] text-xs bg-[var(--color-main-50)]/50">
              {{ entry.jam }} JP
            </td>

            <template v-for="(month, mIndx) in activePromesMonths" :key="month">
              <td v-for="wIdx in weekPerMonth[mIndx]" :key="wIdx" :class="['p-0.5 border border-slate-400 text-center',
                  'w-7 h-7 text-center font-bold text-[11px] outline-none transition print:border-none print:w-auto',
                  nonActive.includes(getIndex(mIndx, wIdx))? 'bg-red-300 text-slate-400' : jamProta[idx][getIndex(mIndx, wIdx)] > 0
                    ? 'bg-[var(--color-main-100)] text-[var(--color-main-950)] border border-[var(--color-main-300)] focus:bg-white focus:ring-2 focus:ring-[var(--color-main-50)]0'
                    : 'bg-slate-50/50 text-slate-400 hover:bg-slate-100 focus:bg-white focus:text-slate-900 focus:ring-2 focus:ring-[var(--color-main-50)]',
                  ]"
                >
                {{ jamProta[idx][getIndex(mIndx, wIdx)] }}
              </td>
            </template>
          </tr>

          <!-- Column Totals Row -->
          <tr class="bg-slate-100 font-bold border-t-2 border-slate-900 text-center">
            <td colSpan="2" class="p-1.5 border border-slate-400 text-right uppercase">
              Jumlah JP Per Minggu:
            </td>
            <td class="p-1.5 border border-slate-400 text-[var(--color-main-950)] font-black text-xs">
              {{ promesSemesterTotalJP }} JP
            </td>
            <template v-for="(month, mIdx) in activePromesMonths" :key="month">
              <td
                v-for="wIdx in weekPerMonth[mIdx]"
                :key="wIdx"
                :class="[
                  'p-1 border border-slate-400 text-center text-[10px]',
                  getColPromesTotalJP(mIdx, wIdx) > 0 ? 'font-black text-[var(--color-main-900)] bg-[var(--color-main-50)]' : 'text-slate-400'
                ]"
              >
                {{ getColPromesTotalJP(mIdx, wIdx) || '-' }}
              </td>
            </template>
          </tr>

          <!-- Summary / Total Row -->
          <tr class="bg-[var(--color-main-50)] font-black border-t border-slate-900">
            <td colSpan="2" class="p-2 border border-slate-400 text-right uppercase">
              TOTAL JP SEMESTER {{ promesSemester === 'sem1' ? '1 (GANJIL)' : '2 (GENAP)' }}
            </td>
            <td class="p-2 border border-slate-400 text-center text-[var(--color-main-950)] text-sm">
              {{ promesSemesterTotalJP }} JP
            </td>
            <td :colSpan="weekPerMonth.reduce((sum, el) => sum + el.length,0 )" class="p-2 border border-slate-400 text-slate-600 italic text-[10px]">
              *Distribusi JP per minggu di atas telah terhitung otomatis dan dapat diubah secara bebas oleh guru pengampu.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Legend -->
    <div class="flex flex-wrap items-center gap-4 text-[11px] text-slate-600 pt-2 border-t border-slate-200">
      <span class="font-bold">Keterangan:</span>
      <span class="flex items-center gap-1">
        <span class="w-3 h-3 bg-[var(--color-main-100)] border border-[var(--color-main-300)] inline-block rounded-xs"></span>
        Angka = Jam Pelajaran (JP) Tatap Muka Per Minggu (Dapat Diubah)
      </span>
      <span class="flex items-center gap-1">
        <span class="w-3 h-3 bg-slate-100 border border-slate-400 inline-block rounded-xs"></span>
        Kosong = Asesmen / Minggu Cadangan / Libur Semester
      </span>
    </div>
  </div>
</template>

<script>
import { isEmpty, min } from 'lodash';
import { dateNow } from '../../../../../../helpers/dateHelper';

export default {
  name: 'PromesTable',
  props: {
    activeSubject: {
      type: Object,
      default: null
    }
  },
  emits: ['update:promesSemester', 'update-weekly-jp'],
  data() {
    return {
      promesSemester: 'sem1',
      customProtaJP: {},
      customPromesDist: {},
      nonActive:[0, 1, 2, 13, 14, 22, 23, 24, 25, 26],
      weekPerMonth:[],
    };
  },
  computed: {
    sem1Months() {
      return ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    },
    sem2Months() {
      return ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];
    },
    activePromesMonths() {
      return this.promesSemester === 'sem1' ? this.sem1Months : this.sem2Months;
    },
    currentPromesEntries() {
      return this.activeSubject?.materi?.filter((e) =>
        this.promesSemester === 'sem1'
          ? e.semester?.includes('gasal') || e.semester?.includes('Ganjil')
          : e.semester?.includes('genap') || e.semester?.includes('Genap')
      );
    },
    jamProta(){
      let datas = this.weekPerMonth.map( d => d.map( c => 0) )
      datas = datas.reduce((a,b) => [...a,...b], [])
      let materis = {};
      let ind = 0
      this.activeSubject?.materi?.forEach((m, key) => {
        materis[key] = Object.values(datas)
        let tmp = 0
        do {
          let jmp = parseInt(this.activeSubject.jam)
          if (!this.nonActive.includes(ind)) {
            materis[key][ind] = jmp
            tmp += jmp
          }
          ind++
        } while (tmp < m.jam)
      })
      return materis
    },
    promesSemesterTotalJP(){
      return this.activeSubject?.materi?.reduce((sum, entry) => sum + parseInt(entry.jam), 0) || 0
    },
  },
  watch:{
    promesSemester(val) {
      this.getWeeks()
    }
  },
  methods: {
    getIndex(mIdx, weekIdx){
      let ind = mIdx - 1
      // console.log(ind)
      if (ind < 0) 
        return weekIdx
      else
        return weekIdx + this.weekPerMonth.slice(0, mIdx).reduce((sum,el) => sum + el?.length, 0)
    },
    getColPromesTotalJP(mIdx, weekIdx) {
      return this.currentPromesEntries?.reduce(
        (sum, entry, index) => sum + this.jamProta[index][this.getIndex(mIdx, weekIdx)] || 0,
        0
      );
    },
    getWeeks(){
      
      let start = this.promesSemester == 'sem1' ? 6 : 0
      let tahun = (this.activeSubject?.tahun_ajaran ?? '').split('/')
      console.log(tahun)
      tahun = this.promesSemester == 'sem1' ? tahun[0] : (tahun[1] ?? '')
      if (isEmpty(tahun))
        tahun = dateNow().slice(0, 4)
      this.weekPerMonth = this.activePromesMonths.map((d, key) => range(hitungHariSenin(tahun, (start + key)), 0))
    }
  },
  mounted(){
    this.getWeeks()
  }
};
</script>
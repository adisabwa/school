<template>
  <div
    class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden h-full flex flex-col hover:shadow-md transition-all"
  >
    <!-- Header -->
    <div
      class="p-4 lg:p-5 border-b border-slate-50 flex items-center justify-between bg-slate-50/30"
    >
      <div
        class="text-base lg:text-xl font-black text-slate-900 flex items-center gap-2.5"
      >
        <icons icon="vaadin:line-bar-chart" class="text-[var(--color-main-600)]" />
        Ringkasan Kedisiplinan
      </div>
    </div>

    <!-- Table -->
    <div class="flex-1 overflow-x-auto p-4">
        <table-freeze
        class="m-0 *:m-0"
        ref="tableFreeze"
        :data="weeklyStats">
        <template #header="{data}">
          <tr>
            <th width="200px" class="fixed-col p-3.5 text-[11px] font-black text-slate-400 uppercase tracking-widest">
              Santri
            </th>
            <th class="p-3.5 text-center text-[11px] font-black text-[var(--color-main-600)] uppercase tracking-widest">
              Hadir
            </th>
            <th class="p-3.5 text-center text-[11px] font-black text-amber-600 uppercase tracking-widest">
              Izin
            </th>
            <th class="p-3.5 text-center text-[11px] font-black text-blue-600 uppercase tracking-widest">
              Sakit
            </th>
            <th class="p-3.5 text-center text-[11px] font-black text-red-600 uppercase tracking-widest">
              Alfa
            </th>
          </tr>
        </template>
        <template #body="{data}">
          <tr
            v-for="stat in data"
            :key="stat.studentId"
            class="hover:bg-slate-50/60 transition-colors"
          >
            <td class="p-3.5 fixed-col">
              <div class="flex items-center gap-3 ">
                <img
                  :src="`https://picsum.photos/seed/${stat.studentId}/100`"
                  class="w-9 h-9 rounded-xl border border-white"
                  alt="avatar"
                />
                <span class="font-bold text-slate-800 text-base">
                  {{ stat.studentName }}
                </span>
              </div>
            </td>

            <td class="p-3.5 text-center">
              <span
                class="inline-block px-3.5 py-1.5 rounded-xl bg-[var(--color-main-50)] text-[var(--color-main-700)] font-black text-base"
              >
                {{ stat.hadir }}
              </span>
            </td>

            <td class="p-3.5 text-center text-amber-600 font-bold text-base">
              {{ stat.izin }}
            </td>

            <td class="p-3.5 text-center text-blue-600 font-bold text-base">
              {{ stat.sakit }}
            </td>

            <td class="p-3.5 text-center">
              <span
                :class="[
                  'px-3.5 py-1.5 rounded-xl text-base font-black',
                  stat.alfa > 0 ? 'bg-red-50 text-red-600' : 'text-slate-300'
                ]"
              >
                {{ stat.alfa }}
              </span>
            </td>
          </tr>
        </template>
    </table-freeze>
    </div>

    <!-- Legend -->
    <div
      class="p-2 lg:p-4 pb-7 bg-slate-50/50 flex justify-center gap-6 border-t border-slate-100"
    >
      <div
        v-for="item in legendItems"
        :key="item.label"
        class="flex items-center gap-2.5"
      >
        <div
          :class="['w-3 h-3 rounded-full', item.dotClass]"
        ></div>
        <span
          class="text-[11px] font-black text-slate-500 uppercase tracking-widest"
        >
          {{ item.label }}
        </span>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  name: 'WeeklySummary',

  components: {
  },

  data() {
    return {
      weeklyStats: [
        { studentId: '1', studentName: 'Ahmad Fauzi', hadir: 5, izin: 0, sakit: 1, alfa: 0 },
        { studentId: '2', studentName: 'Budi Santoso', hadir: 6, izin: 0, sakit: 0, alfa: 0 },
        { studentId: '3', studentName: 'Cahyo Utomo', hadir: 4, izin: 1, sakit: 0, alfa: 1 },
        { studentId: '4', studentName: 'Dedi Irawan', hadir: 5, izin: 1, sakit: 0, alfa: 0 },
        { studentId: '5', studentName: 'Eko Wahyudi', hadir: 3, izin: 0, sakit: 2, alfa: 1 },
        { studentId: '6', studentName: 'Farhan Azis', hadir: 6, izin: 0, sakit: 0, alfa: 0 },
        { studentId: '7', studentName: 'Gilang Ramadhan', hadir: 5, izin: 0, sakit: 1, alfa: 0 },
        { studentId: '8', studentName: 'Hafiz Syahputra', hadir: 4, izin: 2, sakit: 0, alfa: 0 },
    ],
      legendItems: [
        { label: 'H', dotClass: 'bg-[var(--color-main-500)] shadow-[var(--color-main-200)]' },
        { label: 'I', dotClass: 'bg-amber-500 shadow-amber-200' },
        { label: 'S', dotClass: 'bg-blue-500 shadow-blue-200' },
        { label: 'A', dotClass: 'bg-red-500 shadow-red-200' }
      ]
    }
  },
  methods:{
    
  },
  mounted(){
    setTimeout(() => {
        this.$refs.tableFreeze?.getFreezeHeader()
    }, 300)
  }
}
</script>

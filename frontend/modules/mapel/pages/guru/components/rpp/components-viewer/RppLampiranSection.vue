<template>
  <div class="space-y-3 pt-6 border-t-2 border-dashed border-slate-300 print:break-before-page">
    <div class="text-center border-b-2 border-slate-900 pb-3">
      <div class="inline-flex items-center space-x-1.5 bg-amber-100 text-amber-950 text-xs px-3 py-1 rounded-full font-bold uppercase tracking-wider mb-1.5 border border-amber-300">
        <icons icon="mdi:clipboard-text-outline" class="w-3.5 h-3.5" />
        <span>Lampiran {{ noLampiran }}: Sumber Belajar dan Pengayaan</span>
      </div>
      <div class="text-base sm:text-lg font-black uppercase text-slate-900">
       SUMBER BELAJAR DAN PENGAYAAN
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
      <div class="bg-white p-3 rounded-xl border border-slate-200">
        <strong class="block text-slate-900 font-bold mb-1">Bahan Bacaan:</strong>
          <el-input
            v-if="isEditingMode"
            :model-value="rpp?.lampiran?.bahanBacaan"
            type="textarea" rows="10" size="small"
            @input="(val) => updateArray(`lampiran.bahanBacaan`, val)"
          />
        <p v-else class="text-slate-700">{{ rpp?.lampiran?.bahanBacaan }}</p>
      </div>
      <div class="bg-white p-3 rounded-xl border border-slate-200">
        <strong class="block text-slate-900 font-bold mb-1">Glosarium:</strong>
        <el-input
            v-if="isEditingMode"
            :model-value="rpp?.lampiran?.glosarium"
            type="textarea" rows="10" size="small"
            @input="(val) => updateArray(`lampiran.glosarium`, val)"
          />
        <p v-else class="text-slate-700">{{ rpp?.lampiran?.glosarium }}</p>
      </div>
      <div class="bg-white p-3 rounded-xl border border-slate-200">
        <strong class="block text-slate-900 font-bold mb-1">Daftar Pustaka:</strong>
        <el-input
            v-if="isEditingMode"
            :model-value="rpp?.lampiran?.daftarPustaka"
            type="textarea" rows="10" size="small"
            @input="(val) => updateArray(`lampiran.daftarPustaka`, val)"
          />
        <p v-else class="text-slate-700">{{ rpp?.lampiran?.daftarPustaka }}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RppLkpdSection',
  emits: ['update-array-item'],
  props: {
    rpp: {
      type: Object,
      required: true
    },
    noLampiran:{
      type:[String, Number]
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
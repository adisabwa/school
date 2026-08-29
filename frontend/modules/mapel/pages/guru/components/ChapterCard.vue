<template>
  <el-card shadow="hover" class="!border-slate-200 hover:!border-indigo-300 transition-all !rounded-lg space-y-3"
    body-class="flex flex-col justify-between h-full">
    <div class="space-y-2">
      <div class="flex items-start justify-between gap-2">
        <div>
          <el-tag type="primary" size="small" effect="light" class="mb-1 font-bold bg-[var(--color-main-100)] text-[var(--color-main-700)]">
            Semester {{ ucFirst(chapter?.semester) }}
          </el-tag>
          <h4 class="font-bold text-slate-900 text-sm" >
            Bab {{ chapter?.no }}: {{ chapter?.materi }}
          </h4>
        </div>

        <div class="flex items-center space-x-1 shrink-0">
          <el-tag type="info" size="small" effect="solid" class="font-bold">
            {{ chapter?.jam }} JP
          </el-tag>
          <el-button
            type="primary" link size="small"
            @click="$emit('edit', chapter?.id)"
            title="Hapus Bab"
          >
            <icons icon="mdi:edit-outline" class="w-4 h-4 m-0" />
          </el-button>
          <el-button
            type="danger" 
            link
            size="small"
            @click="$emit('delete', chapter?.id)"
            title="Hapus Bab"
          >
            <icons icon="mdi:trash-can-outline" class="w-4 h-4 m-0" />
          </el-button>
        </div>
      </div>

      <!-- Learning Objectives list -->
      <div class="space-y-1 mb-2">
        <span class="text-[10px] font-bold uppercase text-slate-400 block">
          Capaian Pembelajaran (CP):
        </span>
        <div class="text-xs text-slate-600 space-y-1 list-disc pl-4">
          {{ chapter?.cp }}
        </div>
      </div>
      <div class="space-y-1">
        <span class="text-[10px] font-bold uppercase text-slate-400 block">
          Tujuan Pembelajaran (TP):
        </span>
        <ul class="text-xs text-slate-600 space-y-1 list-disc pl-4">
          <li v-for="(obj, oIdx) in chapter?.sub_materi" :key="oIdx" class="leading-snug">
            {{ obj.tujuan_pembelajaran }}
          </li>
        </ul>
      </div>

      <!-- Sub topics tags -->
      <div v-if="chapter?.sub_materi && chapter?.sub_materi.length > 0" class="flex flex-wrap gap-1 pt-1">
        <el-tag
          v-for="(st, sIdx) in chapter?.sub_materi"
          :key="sIdx"
          type="info"
          size="small"
          effect="plain"
          class="!text-[10px]"
        >
          • {{ st.sub_materi }}
        </el-tag>
      </div>
    </div>

    <el-button
      type="primary"
      class="w-full !mt-3 !font-bold bg-[var(--color-main-700)] text-[13px]"
      @click="$emit('select-for-rpp', chapter)"
    >
      <icons icon="mdi:sparkles" class="text-[20px] text-amber-300" />
      ⚡ Buat RPP Bab Ini
    </el-button>
  </el-card>
</template>

<script>
export default {
  name: 'ChapterCard',
  setup(){
    return {
      ucFirst
    }
  },
  props: {
    chapter: {
      type: Object,
      required: true
    }
  },
  emits: ['delete', 'select-for-rpp','edit']
};
</script>
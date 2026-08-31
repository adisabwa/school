<template>
  <div
    :id="`job-card-${job?.id}`"
    :class="[
      'bg-white rounded-xl border border-[#e2e8f0] p-5 shadow-[0_1px_2px_rgba(0,0,0,0.02)] flex flex-col justify-between group transition-all duration-200 ease-out',
      'hover:-translate-y-1 hover:border-[#4f46e5] hover:shadow-[0_10px_25px_-5px_rgba(79,70,229,0.08),0_8px_10px_-6px_rgba(79,70,229,0.04)]',
      job?.status === 'closed' ? 'opacity-75 bg-slate-50/55' : ''
    ]"
  >
    <div>
      <div class="flex justify-between items-center gap-2 mb-4">
        <span class="text-[10px] font-mono text-[#64748b] flex items-center gap-1 justify-center">
          <icons icon="mdi:calendar" class="w-3.5 h-3.5 text-slate-400" />
          {{ dateIndo(job?.created_at) }}
        </span>
        <div class="flex gap-1.5 items-center">
          <span
            :class="[
              'text-[10px] uppercase tracking-wider px-2 py-0.5 rounded font-bold border',
              typeColors[job?.type] || 'bg-slate-50 text-slate-600 border-slate-150'
            ]"
          >
            {{ job?.type }}
          </span>
          <span 
            v-if="job?.status === 'closed'" 
            class="text-[10px] uppercase tracking-wider px-2 py-0.5 rounded font-bold bg-[#f1f5f9] text-[#475569]"
          >
            Closed
          </span>
          <span 
            v-else 
            class="text-[10px] uppercase tracking-wider px-2 py-0.5 rounded font-bold bg-[#dcfce7] text-[#166534]"
          >
            Active
          </span>
        </div>
      </div>
      
      <div class="w-full min-h-[250px] max-h-[300px] bg-center bg-cover mb-2
        relative overflow-hidden
        flex items-center justify-center">
        <icons icon="mdi:image" class="w-2/3 h-2/3 flex items-center justify-center text-slate-300 z-[0]" />
        <img :src="job.poster" class="absolute w-full" />
      </div>
      
      <div class="flex gap-3 mb-4">
        <div>
          <h3 class="text-base font-bold text-[#1e293b] tracking-tight group-hover:text-[#4f46e5] transition-colors mb-1">
            {{ job?.nama_lowongan }}
          </h3>
          <p class="text-xs font-semibold text-[#64748b] mb-4">{{ job?.perusahaan }}</p>
        </div>
      </div>

      <div class="space-y-2 mb-5"  >
        <div class="flex items-center text-xs text-[#64748b] gap-2">
          <icons icon="fa7-solid:location-dot" class="w-4 h-4 text-slate-400 shrink-0" />
          <span class="truncate">{{ job?.alamat_lowongan }}</span>
        </div>
        <div class="flex items-center text-xs text-[#64748b] gap-2" v-if="job.gaji_start > 0 ">
          <icons icon="fa7-solid:rupiah-sign" class="w-4 h-4 text-[var(--color-main-500)] shrink-0" />
          <span class="font-bold text-[#4f46e5]">{{ setCurrency(job?.gaji_start) }}
            <template v-if="job.gaji_end > 0 && job.gaji_end != job.gaji_start">- {{ setCurrency(job.gaji_end) }}</template>
          </span>
        </div>
      </div>
    </div>

    <div class="flex gap-2 pt-4 border-t border-[#e2e8f0]">
      <el-button
        :id="`view-details-${job?.id}`"
        @click="$emit('select', job)"
        class="m-0 flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 rounded-lg transition-colors border border-slate-200"
      >
        <icons icon="mdi:eye" class="w-3.5 h-3.5 text-slate-400" />
        Detail Pekerjaan
      </el-button>
      <template v-if="role === 'admin'">
        <el-button
          :id="`edit-job-${job?.id}`"
          @click.stop="$emit('edit', job)"
          title="Edit Lowongan"
          class="flex m-0"
          type="primary"
        >
          <icons icon="mdi:edit" class="m-0" />
        </el-button>
        <el-button
          :id="`edit-job-${job?.id}`"
          @click.stop="deleteData()"
          title="Hapus Lowongan"
          class="flex m-0"
          type="danger"
        >
          <icons icon="mdi:delete" class="m-0" />
        </el-button>
      </template>
    </div>
  </div>
</template>

<script>
import { mapState } from 'pinia';

export default {
  name: 'JobCard',
  setup() {
    return { dateIndo, setCurrency };
  },
  props: {
    job: {
      type: Object,
      required: true,
    },
  },
  emits: ['select', 'edit','delete'],
  data() {
    return {
      typeColors: {
        'full-time': 'bg-indigo-50 text-[#4f46e5] border-indigo-100',
        'part-time': 'bg-amber-50 text-amber-700 border-amber-100',
        'contract': 'bg-purple-50 text-purple-700 border-purple-100',
        'internship': 'bg-blue-50 text-blue-700 border-blue-100',
        'remote': 'bg-[var(--color-main-50)] text-[var(--color-main-700)] border-[var(--color-main-100)]',
      },
    };
  },
  computed:{
    ...mapState(useAuthStore, ['role']),
  },
  methods: {
    deleteData(){
      this.$confirm('Apakah anda yakin untuk menghapus data ini?',
        'Konfirmasi',
        {
          confirmButtonText: 'OK',
          cancelButtonText: 'Batal',
          type: 'warning',
        })
        .then(() => {
          // console.log(this.href)
          this.$http.get(`lowongan/delete/` + this.job.id)
            .then(result => {
              this.$emit('delete', this.job.id);
              this.$notify({
                type:'success',
                title: 'Berhasil',
                message: 'Data berhasil dihapus',
                position: 'bottom-right'
              });
            })
            .catch(err => {
              this.$notify({
                type:'error',
                title: 'Gagal',
                message: 'Data tidak berhasil dihapus',
                position: 'bottom-right'
              });
            });
          })
    }
  },
};
</script>
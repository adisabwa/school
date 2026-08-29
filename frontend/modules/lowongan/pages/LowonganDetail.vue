<template>
  <div
    id="job-detail-panel"
    class="bg-white rounded-xl border border-[#e2e8f0] p-6 md:p-8 shadow-[0_1px_3px_rgba(0,0,0,0.02)]"
  >
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6 pb-6 border-b border-[#e2e8f0]">
      <el-button type="text"
        @click="$router.push({ name: 'lowongan-list' })"
        class="inline-flex items-center gap-2 text-sm font-bold text-[#64748b] hover:text-[#4f46e5] transition-colors"
      >
        <icons icon="mdi:arrow-left" class="w-4 h-4" />
        Kembali ke Daftar
      </el-button>

      <div class="flex items-center gap-2" v-if="role === 'admin'">
        <el-button
          @click="$router.push({ name: 'lowongan-form', query: { id: job.id } })"
          class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-[#4f46e5] bg-[#eef2ff] hover:bg-[#eef2ff]/80 rounded-lg transition-colors border border-indigo-100"
        >
          <icons icon="mdi:edit" class="w-3.5 h-3.5" />
          Edit Lowongan
        </el-button>
        <el-button
          @click="showDeleteConfirm = true"
          class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors border border-rose-100"
        >
          <icons icon="mdi:trash-can" class="w-3.5 h-3.5" />
          Hapus
        </el-button>
      </div>
    </div>

    <div
      v-if="showDeleteConfirm"
      class="mb-6 p-4 bg-rose-50 border border-rose-100 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
    >
      <div class="flex items-start gap-3">
        <icons icon="AlertCircle" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" />
        <div>
          <p class="text-xs font-bold text-rose-900">Konfirmasi Hapus Lowongan</p>
          <p class="text-xs text-rose-700">Apakah Anda yakin ingin menghapus lowongan pekerjaan ini secara permanen?</p>
        </div>
      </div>
      <div class="flex items-center gap-2 self-end sm:self-auto">
        <el-button
          @click="showDeleteConfirm = false"
          class="px-3 py-1.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 rounded-lg border border-slate-200"
        >
          Batal
        </el-button>
        <el-button
          @click="handleDelete"
          :disabled="isDeleting"
          class="px-3 py-1.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-lg disabled:opacity-50"
        >
          {{ isDeleting ? 'Menghapus...' : 'Ya, Hapus' }}
        </el-button>
      </div>
    </div>

    <div>
      <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-1">
        <div >
          <div class="flex items-center gap-2 mb-3 flex-wrap">
            <span class="text-[10px] uppercase tracking-wider px-2.5 py-0.5 rounded font-bold border border-indigo-150 bg-[#eef2ff] text-[#4f46e5]">
              {{ job.type }}
            </span>
            <span
              v-if="job.status === 'closed'"
              class="text-[10px] uppercase tracking-wider px-2.5 py-0.5 rounded font-bold bg-[#f1f5f9] text-[#475569]"
            >
              Ditutup / Terisi
            </span>
            <span
              v-else
              class="text-[10px] uppercase tracking-wider px-2.5 py-0.5 rounded font-bold bg-[#dcfce7] text-[#166534]"
            >
              Aktif / Rekrutmen
            </span>
          </div>
          
          
        </div>
        <div class="text-left md:text-right text-xs text-[#64748b] self-start md:self-auto font-mono">
          <span class="flex items-center md:justify-end gap-1 mb-1 font-semibold">
            <icons icon="mdi:calendar" class="w-3.5 h-3.5" />
            Diposting:
          </span>
          <span class="text-slate-700 block font-medium">{{ dateIndo(job.updated_at ?? job.created_at) }}</span>
        </div>
      </div>
      <img :src="job?.poster" v-if="job.poster" 
        class="max-sm:w-full sm:h-full max-w-[400px] mt-2" />
      <div class="flex-1">
        <h1 class="text-2xl font-extrabold text-[#1e293b] tracking-tight mb-1.5">{{ job.nama_lowongan }}</h1>
        <p class="text-sm font-bold text-[#4f46e5]">{{ job.perusahaan }}</p>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 border border-[#e2e8f0] p-4 rounded-xl mb-8">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-white border border-[#e2e8f0] flex items-center justify-center shrink-0 shadow-sm">
            <icons icon="mdi:map-marker" class="w-5 h-5 text-slate-500" />
          </div>
          <div>
            <p class="text-[9px] text-[#64748b] uppercase tracking-wider font-bold">Lokasi Penempatan</p>
            <p class="text-sm font-bold text-[#1e293b]">{{ job.alamat_lowongan }}</p>
          </div>
        </div>
        <div class="flex items-center gap-3" v-if="job.gaji_start > 0 ">
          <div class="w-10 h-10 rounded-lg bg-white border border-[#e2e8f0] flex items-center justify-center shrink-0 shadow-sm">
            <icons icon="fa7-solid:rupiah-sign" class="w-5 h-5 text-[var(--color-main-500)]" />
          </div>
          <div >
            <p class="text-[9px] text-[#64748b] uppercase tracking-wider font-bold">Penawaran Gaji</p>
            <p class="text-sm font-extrabold text-[#1e293b]">Rp. {{ setCurrency(job.gaji_start) }} 
              <template v-if="job.gaji_end > 0 && job.gaji_end != job.gaji_start">- {{ setCurrency(job.gaji_end) }}</template>
            </p>
          </div>
        </div>
      </div>

      <div class="mb-8">
        <h2 class="my-1 text-sm font-bold text-[#1e293b] uppercase tracking-wider border-b border-[#e2e8f0] pb-2">Deskripsi Pekerjaan</h2>
        <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-wrap">{{ job.keterangan_lowongan }}</p>
      </div>

      <div v-if="job.persyaratan && job.persyaratan.length > 0" class="mb-8">
        <h2 class="my-1 text-sm font-bold text-[#1e293b] uppercase tracking-wider border-b border-[#e2e8f0] pb-2">Persyaratan Kandidat</h2>
        <ul class="my-2 space-y-3 pl-3">
          <li v-for="(req, idx) in job.persyaratan" :key="idx" class="flex items-start gap-3 text-sm text-slate-600">
            <div class="w-5 h-5 rounded bg-[#eef2ff] border border-indigo-100 flex items-center justify-center shrink-0 mt-0.5">
              <span class="text-[10px] font-bold text-[#4f46e5]">{{ idx + 1 }}</span>
            </div>
            <span class="leading-relaxed">{{ req }}</span>
          </li>
        </ul>
      </div>

      <div class="p-4 bg-gradient-to-r from-[#eef2ff] to-[#f8fafc] border border-indigo-100/40 rounded-xl flex flex-col md:flex-row items-center justify-between gap-5">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-[#4f46e5] text-white flex items-center justify-center shadow-lg shrink-0">
            <icons icon="mdi:mail" class="w-5.5 h-5.5 m-0" />
          </div>
          <div>
            <h3 class="font-bold text-[#1e293b] text-sm">Ingin Melamar Posisi Ini?</h3>
            <p class="text-xs text-[#64748b]">Silakan hubungi perwakilan HRD dan kirim resume Anda melalui email resmi berikut.</p>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto shrink-0">
          
          <a
            :href="`mailto:${job.email_lowongan}?subject=Lamaran%20Pekerjaan%20-%20${encodeURIComponent(job.nama_lowongan)}`"
            @click="hasApplied = true"
            class="inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-bold text-white bg-[#4f46e5] hover:bg-[#3f37c9] transition-all rounded-lg shadow-md text-center"
          >
            <icons icon="mdi:mail" class="w-4 h-4 m-0" />
            Kirim Email Lamaran
          </a>
          <a
            :href="getWhatsAppLink(job.kontak_lowongan, job.nama_lowongan)"
            target="_blank"
            class="inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-bold text-white bg-[#25d366] hover:bg-[#20ba5a] transition-all rounded-lg shadow-md text-center"
          >
            <icons icon="mdi:whatsapp" class="w-4 h-4" />
            Hubungi via WhatsApp
          </a>
          <span v-if="hasApplied" class="text-xs text-center text-[var(--color-main-600)] font-bold self-center sm:ml-2">
            ✓ Email terbuka di sistem Anda
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { mapState } from 'pinia';

export default {
  name: 'JobDetail',
  setup() {
    return { dateIndo, setCurrency };
  },
  props: {
  },
  emits: ['back', 'edit', 'delete'],
  data() {
    return {
      showDeleteConfirm: false,
      isDeleting: false,
      hasApplied: false,
      job: {
        id: 1,
        nama_lowongan: 'Software Engineer',
        perusahaan: 'PT. Contoh Perusahaan',
        alamat_lowongan: 'Jakarta, Indonesia',
        gaji_awal: 5000000,
        gaji_akhir: 10000000,
        keterangan_lowongan: 'Kami mencari Software Engineer yang berpengalaman untuk bergabung dengan tim kami. Kandidat ideal harus memiliki pengalaman dalam pengembangan web dan pemrograman.',
        persyaratan: [
          'Pengalaman minimal 2 tahun di bidang pengembangan perangkat lunak.',
          'Menguasai bahasa pemrograman JavaScript, Python, atau Java.',
          'Memiliki kemampuan problem-solving yang baik.',
          'Mampu bekerja dalam tim dan berkomunikasi dengan baik.',
        ],
      }
    };
  },
  computed:{
    ...mapState(useAuthStore, ['role']),
  },
  methods: {
    getInitial(){
      // Fetch job details from API if needed
      this.$http.get('lowongan/get', { params: { id: this.$route.query.id } }).then((res) => {
        this.job = res.data;
        this.job.persyaratan = this.job.persyaratan ? this.job.persyaratan.split(';') : [];
      });
    },
    async handleDelete(){
      this.isDeleting = true;
      try {
        // Meneruskan aksi delete asinkronus ke parent component
        this.$http.get(`lowongan/delete/` + this.job.id)
          .then(result => {
            this.$emit('delete', this.job.id);
            this.$router.go(-1)
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
        
      } finally {
        this.isDeleting = false;
        this.showDeleteConfirm = false;
      }
    },
    getWhatsAppLink(phone: string, jobTitle: string){
      let cleaned = phone?.replace(/\D/g, '');
      if (cleaned?.startsWith('0')) {
        cleaned = '62' + cleaned?.slice(1);
      }
      const text = encodeURIComponent(`Halo, saya tertarik dengan lowongan pekerjaan "${jobTitle}" yang dipasang di Data Lowongan SMK Muh 5 Darul Arqom.`);
      return `https://wa.me/${cleaned}?text=${text}`;
    },
  },
  created(){
    this.getInitial();
  }
};
</script>
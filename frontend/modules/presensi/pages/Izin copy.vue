<template>
  <div class="space-y-5 p-2 pb-7">
    
		<div class="space-y-1">
			<div class="text-2xl lg:text-4xl font-black text-slate-900 tracking-tight">Izin Tidak Mengajar</div>
			<div class="text-slate-500 text-base font-medium">Manajemen ketidakhadiran & tugas guru.</div>
		</div>

    <div v-if="activeRole === 'TEACHER'" class="bg-[var(--color-main-600)] p-5 rounded-[2rem] text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-4 relative overflow-hidden">
      <div class="relative z-10">
        <div class="text-3xl font-black mb-1 leading-tight">Berhalangan Mengajar?</div>
        <div class="opacity-80 text-base italic">"Pilih jadwal mengajar dan kirimkan tugas untuk santri."</div>
      </div>
      <el-button @click="openRequestModal" type="default" class="!bg-white !text-[var(--color-main-600)] !px-7 !py-3 !rounded-xl !font-black !text-xl !flex !items-center !justify-center !gap-2 !shadow-xl hover:scale-105 transition-all relative z-10">
        <Plus :size="20" /> Buat Permohonan
      </el-button>
      <ShieldAlert :size="100" class="absolute -right-6 -bottom-6 opacity-10 pointer-events-none" />
    </div>

    <div class="space-y-4">
      <div class="flex items-center justify-between px-1">
        <div class="font-black text-slate-800 text-xl flex items-center gap-2">
          <component :is="activeIcon" :size="24" :class="`text-${roleConfigs[activeRole].color}-600`" />
          Daftar Perizinan {{ activeRole === 'TEACHER' ? 'Saya' : activeRole === 'ACADEMIC_HEAD' ? 'Menunggu Persetujuan' : 'Penugasan Pengganti' }}
        </div>
      </div>

      <div v-if="filteredRequests.length === 0" class="bg-white p-14 rounded-[2rem] border border-slate-100 text-center flex flex-col items-center opacity-30">
        <FileText :size="48" class="mb-3" />
        <div class="font-black text-2xl">Tidak ada data permohonan</div>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
        <div v-for="req in filteredRequests" :key="req.id" class="bg-white p-4 rounded-[1.6rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all group flex flex-col justify-between">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-slate-50 text-slate-400 group-hover:bg-slate-900 group-hover:text-white transition-all">
                <Clock :size="24" />
              </div>
              <div>
                <div class="font-black text-slate-900 text-xl leading-tight">{{ req.subject }}</div>
                <div class="text-[12px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-1 mt-1">
                  <Calendar :size="10" /> {{ req.date }} • {{ getClassName(req.classId) }}
                </div>
              </div>
            </div>
            <div :class="['px-2 py-1 rounded-md text-[10px] font-black uppercase tracking-widest', getStatusClass(req.status)]">
              {{ req.status }}
            </div>
          </div>

          <div class="space-y-3 mb-4">
            <div class="bg-slate-50 p-3 rounded-xl">
              <div class="text-[12px] font-black text-slate-400 uppercase tracking-widest mb-1">Alasan:</div>
              <div class="text-sm text-slate-700 italic font-medium">"{{ req.reason }}"</div>
            </div>
            <div class="bg-white border border-slate-100 p-3 rounded-xl">
              <div class="text-[12px] font-black text-[var(--color-main-600)] uppercase tracking-widest mb-1">Tugas Untuk Santri:</div>
              <div class="text-sm text-slate-600 font-medium line-clamp-2">{{ req.assignment }}</div>
            </div>
          </div>

          <div class="pt-3 border-t border-slate-50">
            <div v-if="activeRole === 'ACADEMIC_HEAD' && req.status === 'PENDING'" class="grid grid-cols-2 gap-2">
              <el-button @click="handleUpdateStatus(req.id, 'REJECTED')" class="!py-2 !bg-red-50 !text-red-600 !rounded-lg !text-[12px] !font-black !uppercase !tracking-widest hover:!bg-red-600 hover:!text-white">Tolak</el-button>
              <el-button @click="handleUpdateStatus(req.id, 'APPROVED')" class="!py-2 !bg-[var(--color-main-600)] !text-white !rounded-lg !text-[12px] !font-black !uppercase !tracking-widest shadow-lg shadow-[var(--color-main-100)] hover:!bg-[var(--color-main-700)]">Setujui</el-button>
            </div>

            <div v-if="activeRole === 'ACADEMIC_STAFF' && req.status === 'APPROVED'" class="space-y-2">
              <div v-if="req.substituteTeacherId" class="flex items-center gap-2 bg-[var(--color-main-50)] p-2 rounded-lg border border-[var(--color-main-100)]">
                <div class="w-6 h-6 bg-[var(--color-main-600)] text-white rounded flex items-center justify-center"><UserCheck :size="14" /></div>
                <div>
                  <div class="text-[9px] font-black text-[var(--color-main-600)] uppercase tracking-widest">Guru Pengganti:</div>
                  <div class="text-sm font-black text-slate-800">{{ getTeacherName(req.substituteTeacherId) }}</div>
                </div>
              </div>
              <el-button v-else @click="openAssignModal(req.id)" class="w-full !py-3 !bg-slate-900 !text-white !rounded-lg !text-[12px] !font-black !uppercase !tracking-widest !flex !items-center !justify-center !gap-2 hover:!bg-slate-800">
                <UserPlus :size="14" /> Pilih Guru Pengganti
              </el-button>
            </div>

            <div v-if="activeRole === 'TEACHER'" class="flex items-center justify-between text-[12px] font-black">
              <div class="text-slate-400 uppercase tracking-widest">Oleh: {{ req.teacherName }}</div>
              <div v-if="req.substituteTeacherId" class="text-[var(--color-main-600)] flex items-center gap-1 uppercase tracking-widest">
                <UserCheck :size="12" /> Pengganti Siap
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="isRequestModalOpen" class="fixed inset-0 z-[70] flex items-center justify-center p-3 lg:p-7">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="isRequestModalOpen = false"></div>
        </div>
  </div>
</template>

<script>

export default {
  name: 'TeacherPermissionsView',
  emits: ['update:requests'],
  data() {
    return {
			requests:[],
      activeRole: 'TEACHER',
      isRequestModalOpen: false,
      requestStep: 1,
      isAssignModalOpen: false,
      selectedRequestId: null,
      selectedSchedule: null,
      formData: {
        date: '',
        reason: '',
        assignment: ''
      },
      roles: ['TEACHER', 'ACADEMIC_HEAD', 'ACADEMIC_STAFF'],
      roleConfigs: {
        TEACHER: { label: 'Menu Guru', color: 'emerald' },
        ACADEMIC_HEAD: { label: 'Akademik (Head)', color: 'blue' },
        ACADEMIC_STAFF: { label: 'Akademik (Staff)', color: 'amber' }
      }
    };
  },
  computed: {
    filteredRequests() {
      if (this.activeRole === 'ACADEMIC_HEAD') return this.requests.filter(r => r.status === 'PENDING');
      if (this.activeRole === 'ACADEMIC_STAFF') return this.requests.filter(r => r.status === 'APPROVED');
      return this.requests;
    },
    activeIcon() {
      return this.roleConfigs[this.activeRole].icon;
    },
    mySchedule() {
      return MOCK_SCHEDULES;
    }
  },
  methods: {
    resetForm() {
      this.requestStep = 1;
      this.selectedSchedule = null;
      this.formData = { date: '', reason: '', assignment: '' };
    },
    openRequestModal() {
      this.resetForm();
      this.isRequestModalOpen = true;
    },
    handleRequestSubmit() {
      if (!this.selectedSchedule) return;

      const newRequest = {
        id: Math.random().toString(36).substr(2, 9),
        teacherId: 't1',
        teacherName: 'Ust. Ahmad Fauzi',
        subject: this.selectedSchedule.subjectName,
        classId: this.selectedSchedule.classId,
        date: this.formData.date,
        reason: this.formData.reason,
        assignment: this.formData.assignment,
        status: 'PENDING'
      };

      this.$emit('update:requests', [newRequest, ...this.requests]);
      
      this.addNotification({
        title: 'Permohonan Izin Guru Baru',
        message: `${newRequest.teacherName} mengajukan izin untuk mata pelajaran ${newRequest.subject}.`,
        type: 'INFO',
        targetRole: 'ACADEMIC_HEAD'
      });

      this.isRequestModalOpen = false;
      this.resetForm();
    },
    handleUpdateStatus(id, status) {
      const request = this.requests.find(r => r.id === id);
      if (!request) return;

      const updatedRequests = this.requests.map(r => r.id === id ? { ...r, status } : r);
      this.$emit('update:requests', updatedRequests);

      this.addNotification({
        title: status === 'APPROVED' ? 'Izin Disetujui' : 'Izin Ditolak',
        message: `Permohonan izin Anda untuk ${request.subject} telah ${status === 'APPROVED' ? 'disetujui' : 'ditolak'}.`,
        type: status === 'APPROVED' ? 'SUCCESS' : 'ALERT',
        targetRole: 'TEACHER'
      });
    },
    getClassName(classId) {
      return MOCK_CLASSES.find(c => c.id === classId)?.name || 'N/A';
    },
    getTeacherName(teacherId) {
      return MOCK_TEACHERS.find(t => t.id === teacherId)?.name || 'N/A';
    },
    getStatusClass(status) {
      if (status === 'APPROVED') return 'bg-[var(--color-main-50)] text-[var(--color-main-600)]';
      if (status === 'REJECTED') return 'bg-red-50 text-red-600';
      return 'bg-blue-50 text-blue-600';
    },
    openAssignModal(id) {
      this.selectedRequestId = id;
      this.isAssignModalOpen = true;
    }
  }
};
</script>
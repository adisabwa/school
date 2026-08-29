<template>
  <div class="space-y-5 pt-4 p-2 pb-7 bg-white/[0.6]">
    
		<div class="space-y-1">
			<div class="text-2xl lg:text-4xl font-black text-slate-900 tracking-tight">Izin Tidak Mengajar</div>
			<div class="text-slate-500 text-base font-medium">Manajemen ketidakhadiran & tugas guru.</div>
		</div>

    <div v-if="currentRole === 'guru'" class="bg-[var(--color-main-600)] p-5 rounded-3xl text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-4 relative overflow-hidden">
      <div class="relative z-10">
        <div class="text-2xl font-black mb-1 leading-tight">Berhalangan Mengajar?</div>
        <div class="opacity-80 text-base italic">"Pilih jadwal mengajar dan kirimkan tugas untuk santri."</div>
      </div>
      <el-button @click="openRequestModal" type="default" class="!bg-white !text-[var(--color-main-600)] !px-7 !py-5 !rounded-xl !font-black !text-md !flex !items-center !justify-center !gap-2 !shadow-xl hover:scale-105 transition-all relative z-10">
        <icons icon="mdi:plus" class="m-0 text-lg mr-2"/> Buat Permohonan
      </el-button>
      <icons icon="mdi:shield-alert-outline" class="text-[130px] absolute -right-6 -bottom-6 opacity-10 pointer-events-none" />
    </div>

    <div class="space-y-2 bg-white p-2 rounded-xl pb-8">
      <div class="flex items-center justify-between px-1">
        <div class="font-black text-slate-800 text-xl flex items-center gap-2">
          <icons :icon="activeIcon" :class="[`${currentRoleConfigs[currentRole].color}`,`text-2xl m-0`]" />
          Daftar Perizinan {{ currentRole === 'guru' ? 'Saya' : currentRole === 'admin' ?  'Penugasan Pengganti'  : 'Menunggu Persetujuan' }}
        </div>
      </div>

      <div v-if="filteredRequests.length === 0" class="bg-white p-14 rounded-[2rem] border border-slate-100 text-center flex flex-col items-center opacity-30">
        <icons icon="lucide:file-text" class="text-[70px] mb-3" />
        <div class="font-black text-2xl">Tidak ada data permohonan</div>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
        <div v-for="req in filteredRequests" :key="req.id" class="bg-white p-4 rounded-[1.6rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all group 
          flex flex-col justify-between cursor-pointer">
          <div>
            <div class="grid grid-cols-[55px_1fr_1fr] items-start justify-between mb-2">
              <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-slate-50 text-slate-400 group-hover:bg-slate-900 group-hover:text-white transition-all">
                <icons icon="lucide:clock" class="text-[24px] m-0"/>
              </div>
              <div class="leading-[1]">
                <span class="text-[12px] font-black text-slate-400 uppercase tracking-widest bg-slate-100 px-1 py-0.5 rounded-md">
                  Kelas {{ req.kelas }}
                </span>
                <div class="font-black text-slate-800 text-xl group-hover:text-[var(--color-main-600)] transition-colors truncate mt-1">
                  {{ req.nama_mapel }}
                </div>
              </div>
              <div class="flex flex-col items-end gap-2">
                <div :class="['px-2 py-1 rounded-md text-[10px] font-black uppercase tracking-widest w-fit ', getStatusClass(req.status_izin)]">
                  {{ getStatusText(req.status_izin) }}
                </div>
              </div>
              <div class="col-start-2 col-span-2 flex items-center gap-2">
                <span class="text-[12px] font-bold text-slate-400">{{ dateIndo(req.tanggal) }}</span>
                <span class="text-[12px] font-bold text-slate-400 flex items-center gap-1">
                  <icons icon="lucide:clock" class="text-[12px] m-0" /> {{ req.waktu_mulai.slice(0, 5) }} - {{ req.waktu_selesai_akhir.slice(0, 5) }}
                </span>
              </div>
            </div>
            <div class="space-y-2 mb-2">
              <div class="bg-slate-100 p-3 py-2 rounded-md">
                <div class="text-[12px] font-black text-slate-400 uppercase tracking-widest
                  flex items-center gap-1">
                  Alasan :
                  <span class="text-[14px] font-black text-[var(--color-main-600)] uppercase tracking-widest">
                    {{ (req.kehadiran) }}
                  </span>
                </div>
                <div class="text-sm text-slate-700 italic font-medium">"{{ req.alasan }}"</div>
              </div>
              <div class="bg-slate-100 border border-slate-100 p-3 py-2 rounded-md">
                <div class="text-[12px] font-black text-[var(--color-main-600)] uppercase tracking-widest">Tugas Untuk Santri:</div>
                <div class="text-sm text-slate-600 font-medium line-clamp-3" v-html="req.keterangan_tugas.replaceAll('\n','<br/>')" />
              </div>
            </div>
          </div>
          <div class="pt-1 border-t border-slate-50 flex flex-col gap-y-3">
            <div class="text-[13px] font-black">
              <div class="text-slate-400 uppercase tracking-widest flex items-center gap-1 mb-1">
                <icons icon="mdi:user" class="m-0"/>Pemohon : {{ req.nama_guru }}
              </div>
              <div v-if="req.id_pengganti > 0" class="text-[var(--color-main-600)] flex items-center gap-1 uppercase tracking-widest">
                <icons icon="mdi:user-check" class="m-0"/> Pengganti : {{ req.nama_guru_pengganti }}
              </div>
            </div>
            <el-button class="rounded-[10px] w-full font-bold flex items-center " @click="selectedSchedule = req; isDetailModel = true;">
              <icons icon="mdi:eye" /> Lihat Detail Tugas
            </el-button>
            <div class="flex w-full gap-2 justify-end">
              <template v-if="currentRole === 'head' && req.status_izin === '1'" >
                <el-button type="danger" @click="handleUpdateStatus(req.id, '-1')" class="rounded-[10px] w-full font-bold h-10 flex items-center m-0">
                  <icons icon="mdi:cancel-bold" class="text-[20px] mr-1 m-0" /> Tolak
                </el-button>
                <el-button type="success" @click="handleUpdateStatus(req.id, '2')" class="rounded-[10px] w-full font-bold h-10 flex items-center m-0">
                  <icons icon="mdi:check-bold" class="text-[20px] mr-1 m-0" /> Setujui
                </el-button>
              </template>

              <template v-if="currentRole === 'admin' && req.status_izin === '2'" >
                <el-button @click="isAssignModalOpen = true; selectedRequestId = req.id;
                  selectedSchedule = schedules.find(d => d.hari == dayIndo(req.tanggal).toLowerCase() && d.id_sesi == req.id_sesi && d.id_kelas == req.id_kelas);" 
                  class="rounded-[10px] w-full font-bold " type="info" effect="dark">
                  <icons icon="mdi:user-plus" class="mr-2"/> Pilih Guru Pengganti
                </el-button>
              </template>
              <div v-if="currentRole === 'guru'" class="grid grid-cols-1 lg:grid-cols-2 w-full gap-3 gap-y-1">
                <el-button type="info" class="rounded-[10px] w-full font-bold  flex items-center m-0"
                  @click="isRequestModalOpen = true; 
                  isMultiple = false;
                  selectedRequestId = req.id; 
                  selectedSchedule = schedules.find(d => d.hari == dayIndo(req.tanggal).toLowerCase() && d.id_sesi == req.id_sesi && d.id_kelas == req.id_kelas); 
                  requestStep = 2;">
                  <icons icon="lucide:edit" class="text-[20px] mr-1 m-0" /> Edit
                </el-button>
                <el-button type="danger" class="rounded-[10px] w-full font-bold  flex items-center m-0"
                  @click="deleteRequest(req)">
                  <icons icon="mdi:trash" class="text-[20px] mr-1 m-0" /> Hapus
                </el-button>
                <!-- <el-button type="primary" class="rounded-[10px] w-full font-bold h-10 flex items-center m-0 col-span-2 lg:col-span-1"
                  @click="handleUpdateStatus(req.id, '1')">
                  <icons icon="lucide:send" class="text-[20px] mr-1 m-0" /> Ajukan
                </el-button> -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ASK PERMISSION MODAL -->
		<el-dialog v-model="isRequestModalOpen" 
      append-to-body
      header-class="bg-slate-900 p-5 lg:p-7 text-white "
      footer-class="p-5"
      class="mt-10 min-w-[300px] bg-white max-w-5xl rounded-[1rem] shadow-2xl relative p-0 overflow-hidden">
				<template #header>
          <div class="flex justify-between items-center shrink-0">
            <div>
              <div class="text-1xl lg:text-4xl font-black">
                {{ requestStep === 1 ? 'Pilih Jadwal' : 'Detail Izin' }}
              </div>
              <div class="flex items-center gap-2 mt-2">
                <span :class="['w-2 h-2 rounded-full', requestStep === 1 ? 'bg-[var(--color-main-400)]' : 'bg-white/20']"></span>
                <span :class="['w-2 h-2 rounded-full', requestStep === 2 ? 'bg-[var(--color-main-400)]' : 'bg-white/20']"></span>
                <div class="text-[var(--color-main-400)] text-[13px] font-black uppercase tracking-widest ml-2">
                  Langkah {{ requestStep }} dari 2
                </div>
              </div>
            </div>
          </div>
        </template>

        <div class="py-1 px-3 lg:px-5 lg:py-3">
          <div class="text-[13px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-3">
            Pilih jenis izin:
          </div>
          <el-radio-group v-model="isMultiple">
            <el-radio-button :value="true" label="Satu Hari Penuh" />
            <el-radio-button :value="false" label="Per Pelajaran" />
          </el-radio-group>
        </div>
				<div v-if="isMultiple" class="flex-1 overflow-y-auto py-1 px-3 lg:px-5 lg:py-3 space-y-4 bg-slate-50">
					
					<div v-if="requestStep === 1" class="space-y-3">
            <div class="text-[13px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-3">
              Pilih tanggal izin:
            </div>
            <el-select v-model="tanggalIzin" class="w-full"
                placeholder="Pilih tanggal izin">
              <el-option v-for="(tanggal, itx) in tanggalOptions"
                :key="itx"
                :value="tanggal.value"
                :label="tanggal.label" />
            </el-select>
            <div class="text-[13px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-3">
              Ada {{ selectedSchedules?.length }} Mata Pelajaran pada hari ini
            </div>
					</div>

					<div v-else class="space-y-2 animate-in slide-in-from-right-4 duration-300">
            <form-comp ref="formData" :fields="fieldsData" v-model:formValue="formData" v-model:id="selectedRequestId"
              :pass-columns="['keterangan_tugas']"
              class=" [--el-color-primary:var(--color-main-600)] mb-0"
              :addValues="addValues"
              label-class="block text-[12px] font-black text-slate-400 uppercase tracking-widest ml-1"
              form-item-class="mb-2"
              label-position="top"
              @saved="isRequestModalOpen = false;getRequest();resetForm();"
              :show-submit="false" :show-required-text="false">
            </form-comp>

						<div class="bg-[var(--color-main-50)] p-4 rounded-[1.5rem] border border-[var(--color-main-100)]">
              <div class="text-[12px] font-black text-[var(--color-main-600)] uppercase tracking-widest">Jadwal Terpilih:</div>
              <div class="space-y-3">
                <template  v-for="selSch in selectedSchedules">
                  <div class="flex items-start gap-3 w-full">
                    <div class="w-10 h-10 mt-1 bg-[var(--color-main-600)] text-white rounded-xl flex items-center justify-center">
                      <icons icon="lucide:book-open" class="text-[30px] m-0" />
                    </div>
                    <div class="w-full leading-[1]">
                      <div class="text-xl font-black text-slate-800">
                        {{ selectedSchedule?.nama_mapel }} - Kelas {{ selectedSchedule?.kelas }}
                      </div>
                      <div class="text-[12px] font-black text-[var(--color-main-600)] uppercase tracking-widest w-full flex gap-4 mt-1">
                        <span class="text-[12px] font-bold">{{ dateIndo(selectedSchedule?.date) }}</span>
                        <span class="text-[12px] font-bold flex items-center gap-1">
                          <icons icon="lucide:clock" class="text-[12px] m-0" /> {{ selectedSchedule?.waktu_mulai.slice(0, 5) }} - {{ selectedSchedule?.waktu_selesai_akhir.slice(0, 5) }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="">
                    <div class="uppercase font-bold text-slate-400 mb-1">Daftar Tugas</div>
                    <el-input type="textarea" rows="5"
                      v-model="selSch.keterangan_tugas"
                      class="mb-3" 
                      placeholder="Masukkan tugas disini...." />
                  </div>
                </template>
              </div>
						</div>

					</div>
				</div>
				<div v-else class="flex-1 overflow-y-auto py-1 px-3 lg:px-5 lg:py-3 space-y-4 bg-slate-50">
					
					<div v-if="requestStep === 1" class="space-y-3">
            <div class="text-[13px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-3">
              Pilih sesi mengajar yang akan diizinkan:
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
              <el-button 
                v-for="(s, idx) in schedules" 
                :key="idx"
                @click="selectSchedule(s)"
                :class="['p-5 m-0 h-fit w-full *:w-full !bg-white !rounded-[1.5rem] !border-slate-100 hover:!border-[var(--color-main-500)] hover:!shadow-xl !transition-all !group !text-left',
                  s.id == selectedSchedule?.id ? 'border-[var(--color-main-500)] bg-[var(--color-main-50)]' : '']"
              >
                <div class="!w-full !h-auto !flex !items-center !gap-3  ">
                  <icons icon="lucide:clock" class="w-7 h-7 text-slate-400 group-hover:!text-[var(--color-main-600)] text-[18px] m-0" />
                  <div class="flex-1">
                    <span class="text-[12px] font-black text-slate-400 uppercase tracking-widest bg-slate-100 px-1 py-0.5 rounded-md">
                      Kelas {{ s.kelas }}
                    </span>
                    <div class="font-black text-slate-800 text-xl group-hover:text-[var(--color-main-600)] transition-colors truncate my-1.5">
                      {{ s.nama_mapel }}
                    </div>
                    <div class="flex items-center gap-2 mt-0.5">
                      <span class="text-[12px] font-bold text-slate-400">{{ dateIndo(s.date) }}</span>
                      <span class="text-[12px] font-bold text-slate-400 flex items-center gap-1">
                        <icons icon="lucide:clock" class="text-[12px] m-0" /> {{ s.waktu_mulai.slice(0, 5) }} - {{ s.waktu_selesai_akhir.slice(0, 5) }}
                      </span>
                    </div>
                  </div>
                  <icons icon="lucide:chevron-right" class="text-[20px] text-slate-400 group-hover:text-[var(--color-main-500)] group-hover:translate-x-1 transition-all" />
                </div>
              </el-button>
            </div>
					</div>

					<div v-else class="space-y-4 animate-in slide-in-from-right-4 duration-300">
						<div class="bg-[var(--color-main-50)] p-4 rounded-[1.5rem] border border-[var(--color-main-100)] flex items-center justify-between">
							<div class="flex items-start gap-3 w-full">
								<div class="w-10 h-10 mt-1 bg-[var(--color-main-600)] text-white rounded-xl flex items-center justify-center">
									<icons icon="lucide:book-open" class="text-[30px] m-0" />
								</div>
								<div class="w-full leading-[1]">
									<div class="text-[12px] font-black text-[var(--color-main-600)] uppercase tracking-widest">Jadwal Terpilih:</div>
									<div class="text-xl font-black text-slate-800">
										{{ selectedSchedule?.nama_mapel }} - Kelas {{ selectedSchedule?.kelas }}
									</div>
									<div class="text-[12px] font-black text-[var(--color-main-600)] uppercase tracking-widest w-full flex gap-4 mt-1">
                    <span class="text-[12px] font-bold">{{ dateIndo(selectedSchedule?.date) }}</span>
                    <span class="text-[12px] font-bold flex items-center gap-1">
                      <icons icon="lucide:clock" class="text-[12px] m-0" /> {{ selectedSchedule?.waktu_mulai.slice(0, 5) }} - {{ selectedSchedule?.waktu_selesai_akhir.slice(0, 5) }}
                    </span>
                  </div>
								</div>
							</div>
							<el-button @click="requestStep = 1" type="text" class="!text-[var(--color-main-600)] hover:!underline !text-[12px] !font-black !uppercase">Ubah</el-button>
						</div>

            <form-comp ref="formData" :fields="fieldsData" v-model:formValue="formData" v-model:id="selectedRequestId"
              href="presensi/mengajar/store"
              href-get="presensi/mengajar/get"
              class=" [--el-color-primary:var(--color-main-600)] mb-0"
              :addValues="addValues"
              label-class="block text-[12px] font-black text-slate-400 uppercase tracking-widest ml-1"
              form-item-class="mb-2"
              label-position="top"
              @saved="isRequestModalOpen = false;getRequest();resetForm();"
              :show-submit="false" :show-required-text="false">
            </form-comp>
					</div>
				</div>

				<template #footer>
					<div v-if="requestStep === 1" class="flex flex-col gap-2">
						<el-button @click="isRequestModalOpen = false" class="!w-full h-[40px] !py-2 !text-slate-400 !text-[14px] !font-black !uppercase !tracking-widest hover:!text-slate-600 !border-none">
							Batalkan
						</el-button>
						<el-button 
							@click="requestStep++" 
							:disabled="!tanggalIzin" 
							class="m-0 !bg-[var(--color-main-600)] !text-white h-[40px] !py-2 !rounded-xl !font-black !text-[16px] !flex !items-center !justify-center !gap-2 !shadow-xl hover:!bg-[var(--color-main-700)] !transition-all active:scale-95 disabled:!opacity-50 !border-none"
						>
							Lanjutkan <icons icon="lucide:arrow-right" class="text-[14px] ml-3" />
						</el-button>
					</div>
					<div v-else class="flex flex-col gap-2">
						<el-button @click="requestStep = 1" class="m-0 h-[45px] !py-2 !bg-slate-100 !text-slate-600 !rounded-xl !font-black !text-md !uppercase !tracking-widest hover:!bg-slate-200 !border-none !flex !items-center !justify-center !gap-2">
							<icons icon="lucide:arrow-left" class="text-[14px] mr-3" /> Kembali
						</el-button>
						<el-button 
							@click="handleRequestSubmit" 
							:disabled="isMultiple ? 
                !formData.alasan || (selectedSchedules.filter(d => !d?.keterangan_tugas)?.length > 0) :
                !formData.alasan || !formData.keterangan_tugas
              " 
							class="m-0 !bg-[var(--color-main-600)] !text-white h-[45px] !py-2 !rounded-xl !font-black !text-xl !flex !items-center !justify-center !gap-2 !shadow-xl hover:!bg-[var(--color-main-700)] !transition-all active:scale-95 disabled:!opacity-50 !border-none"
						>
							<icons icon="lucide:send" class="text-[20px] mr-3" /> Kirim Izin
						</el-button>
					</div>
        </template>
    </el-dialog>

    <!-- DETAIL TUGAS -->
    <!-- VIEW DETAIL MODAL -->
    <el-dialog v-model="isDetailModel" class="mt-10 p-0 min-w-[300px] max-w-4xl"
      header-class="bg-slate-900 p-6 lg:p-8 text-white "
      footer-class="p-6 border-t bg-white flex flex-col sm:flex-row justify-end gap-2"
      append-to-body>
      <template #header>
        <div>
          <div class="text-[11px] font-black text-[var(--color-main-400)] uppercase tracking-widest mb-1">
            {{ selectedSchedule?.tanggal }} • KELAS {{ (selectedSchedule?.kelas) }}
          </div>
          <h3 class="text-3xl font-black">Detail Tugas & Perizinan</h3>
        </div>
      </template>

      <!-- CONTENT -->
      <div class="flex-1 overflow-y-auto p-4 lg:p-5 bg-slate-50">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-3">
          <div class="lg:col-span-2 bg-white p-3 lg:p-4 rounded-3xl shadow-sm border">
            <div class="flex items-center gap-2 mb-3 text-[var(--color-main-600)] font-black text-xs uppercase tracking-widest border-b pb-1">
              <icons icon="lucide:file-badge"/> Instruksi Pembelajaran
            </div>
            <p class="whitespace-pre-wrap font-medium text-slate-700 text-base leading-[1]"
              v-html="selectedSchedule?.keterangan_tugas?.replaceAll('\n','<br/>') || 'Tidak ada detail tugas.' "/>
          </div>

          <div class="space-y-4">
            <div class="bg-white p-3 rounded-xl border shadow-sm">
              <div class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1">Informasi Guru</div>
              <div class="flex items-center gap-1">
                <icons icon="mdi:user" class="w-11 h-11 rounded-lg" />
                <div>
                  <div class="font-black text-slate-800 text-base leading-[1.2] mb-1">{{ selectedSchedule?.nama_guru }}</div>
                  <div class="text-[13px] text-[var(--color-main-600)] font-bold uppercase leading-[1.2] ">{{ selectedSchedule?.nama_mapel }}</div>
                </div>
              </div>
            </div>

            <div class="bg-[var(--color-main-50)] p-3 rounded-xl border border-[var(--color-main-100)]">
              <div class="text-[11px] font-black text-[var(--color-main-600)] uppercase tracking-widest mb-1">Alasan Izin</div>
              <div class="text-sm italic text-slate-700 font-medium">" {{ capitalizeEachWord(selectedSchedule?.kehadiran) }} - {{ selectedSchedule?.alasan }}"</div>
            </div>

            <div v-if="selectedSchedule?.id_pengganti" class="bg-slate-900 p-3 rounded-xl text-white">
              <div class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1">Guru Pengganti</div>
              <div class="flex items-center gap-1">
                <icons icon="lucide:user-check" class="text-[var(--color-main-400)] w-6 h-6" />
                <div class="text-base font-bold">{{ selectedSchedule?.nama_guru_pengganti }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- FOOTER -->
      <template #footer>
        <el-button size="large" @click="handleDownloadRequest(selectedSchedule)" class="rounded-2xl font-bold m-0">
         <icons icon="mdi:download" /> Unduh DOCX
        </el-button>
        <el-button size="large" type="primary" @click="handlePrintRequest(selectedSchedule)" class="rounded-2xl font-bold m-0" color="#0f766e" >
          <icons icon="mdi:printer" /> Cetak Tugas
        </el-button>
      </template>
    </el-dialog>

    <!-- ASSIGN GURU PENGGANTI -->
		<el-dialog v-model="isAssignModalOpen" 
      append-to-body
      header-class="bg-slate-900 p-5 lg:p-7 pb-2 lg:pb-3 text-white "
      footer-class="p-5"
      class="min-w-[300px] bg-white max-w-5xl rounded-[2rem] shadow-2xl relative p-0 overflow-hidden">
				<template #header>
          <div class="flex items-center gap-2 text-[var(--color-main-400)] font-black text-md uppercase tracking-widest mb-2 lg:mb-4">
            <icons icon="mdi:calendar" class="m-0 text-[16px] shrink-0" /> 
            <span class="leading-[1.2]">{{ dateDayIndo(selectedSchedule?.tanggal) }}</span>
          </div>

          <div class="text-2xl lg:text-3l font-black m-0 mb-1 leading-tight">{{ selectedSchedule?.nama_mapel }} • KELAS {{ selectedSchedule?.kelas }}</div>
        </template>

				<div class="p-5">
          <div class="block text-[12px] font-black text-slate-400 uppercase tracking-widest ml-1">
            Nama Guru Pengajar :
          </div>
          <div class="text-[18px] font-bold text-[var(--color-main-700)] ml-1">{{ selectedSchedule?.nama_guru }}</div>
          <form-comp ref="formDataPengganti" :fields="fieldsDataPengganti" v-model:formValue="formDataPengganti" v-model:id="selectedRequestId"
              href="presensi/mengajar/store"
              href-get="presensi/mengajar/get"
              form-class=" [--el-color-primary:var(--color-main-600)] mb-0"
              :addValues="addValues"
              label-class="block text-[12px] font-black text-slate-400 uppercase tracking-widest ml-1"
              form-item-class="mb-0"
              label-position="top"
              @saved="isAssignModalOpen = false;getRequest();resetForm();"
              :show-submit="false" :show-required-text="false">
            </form-comp>
        </div>

        <template #footer>
          <div class="flex gap-y-3 h-10">
            <el-button @click="isAssignModalOpen = false" class="!w-full h-full !py-2 !text-slate-400 !text-[12px] !font-black !uppercase !tracking-widest hover:!text-slate-600 !border-none">
              Tutup
            </el-button>
            <el-button @click="$refs.formDataPengganti.submitForm()" class="!w-full h-full !py-2 hover:bg-[var(--color-main-800)]  hover:text-white !text-[12px] !font-black !uppercase !tracking-widest  text-[var(--color-main-800)] bg-slate-100 !border-none">
              Simpan
            </el-button>
          </div>
        </template>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'pinia';
import { useAuthStore } from '@/config/stores/authStore';
import { useDataStore } from '@/config/stores/dataStore';

export default {
  name: 'TeacherPermissionsView',
  emits: ['update:requests'],
  setup() {
    return {
      dateIndo, dayIndo, dateDayIndo, dateNow, capitalizeEachWord,
    }
  },
  data() {
    return {
			requests:[],
      schedules: [],
      isRequestModalOpen: false,
      requestStep: 1,
      isAssignModalOpen: false,
      isDetailModel: false,
      isMultiple: true,
      tanggalIzin:null,
      selectedSchedule: null,
      selectedSchedules: [],
      selectedRequestId:'-1',
      formData: {
        alasan:'',
        kehadiran:'sakit',
        keterangan_tugas:'',
      },
      fieldsData:{
        kehadiran:{
          nama_kolom:'kehadiran',
          label:'Jenis Izin',
          input:'radio',
          options:[
            {value:'sakit', label:'Sakit'},
            {value:'pribadi', label:'Acara Pribadi'},
            {value:'dinas', label:'Tugas Dinas'},
            {value:'persyarikatan', label:'Tugas Persyarikatan'},
          ],
          placeholder:'Pilih jenis izin...',
        },
        alasan:{
          nama_kolom:'alasan',
          label:'Alasan Berhalangan',
          input:'textarea',
          placeholder:'Jelaskan alasan Anda...',
        },
        keterangan_tugas:{
          nama_kolom:'keterangan_tugas',
          label:'Tugas yang Diberikan',
          input:'textarea',
          placeholder:'Tuliskan tugas Anda...',
        },
      },
      formDataPengganti: {
        id_pengganti:'',
      },
      fieldsDataPengganti:{
        id_pengganti:{
          nama_kolom:'id_pengganti',
          label:'Guru Pengganti',
          input:'select',
          options:[],
          placeholder:'Pilih Guru Pengganti ...',
        },
      },
      currentRoleConfigs: {
        guru: { label: 'Menu Guru', color: 'text-emerald-600', icon:'mdi:graduation-cap' },
        head: { label: 'Akademik (Head)', color: 'text-blue-600', icon:'mdi:shield-alert' },
        admin: { label: 'Akademik (Staff)', color: 'text-amber-600', icon:'mdi:user-plus' }
      },
      idSemester:-1,
      idGuru:-1,
      isHead: false,
      dateNow: dateNow(),
      dayNow: dayIndo(dateNow()),
      currentRole:'guru',
    };
  },
  watch:{
    role(val){
      this.currentRole = val
    },
    currentRole(val){
      console.log(val)
    },
    tanggalIzin(val){
      this.selectedSchedules = this.schedules.filter(d => d.date == val)
    },
    isMultiple(val){
      this.requestStep = 1
    }
  },
  computed: {
		...mapState(useAuthStore,{
			user: 'loggedUser',
			role: 'role',
		}),
    filteredRequests() {
      if (this.currentRole === 'head') return this.requests.filter(r => ['1'].includes(r.status_izin));
      if (this.currentRole === 'admin') return this.requests.filter(r => ['1','2'].includes(r.status_izin));
      return this.requests;
    },
    activeIcon() {
      return this.currentRoleConfigs[this.currentRole].icon;
    },
    tanggalOptions(){
      let tanggals = []
      let minTgl = '9999-01-01'
      let maxTgl = '1000-01-01'
      this.schedules.forEach(val => {
        console.log(val.date)
        if (minTgl > val.date) minTgl = val.date
        if (maxTgl < val.date) maxTgl = val.date
      })
      do {
        tanggals.push({
          value:minTgl,
          label: this.dateDayIndo(minTgl)
        })
        minTgl = addDay(minTgl, 1)
      } while (minTgl <= maxTgl)

      return tanggals
    },
    addValues(){
      return {
        id_semester: this.idSemester,
        id_guru: this.idGuru,
        id_kelas: this.selectedSchedule?.id_kelas ?? null,
        id_mapel: this.selectedSchedule?.id_mapel ?? null,
        tanggal: this.selectedSchedule?.date ?? null,
        id_sesi: this.selectedSchedule?.id_sesi ?? null,
        jam: this.selectedSchedule?.jam ?? null,
        tugas: this.formData.keterangan_tugas?.length > 0 ? 1 : 0,
        status_izin:'2',
      }
    }
  },
  methods: {
    async getSchedule(){
      this.currentRole = this.role
      this.idGuru = this.user.id ?? -1
      if (this.role == 'admin' && (this.user?.id_jabatan ?? []).includes('head-kmi'))
        this.currentRole = 'head'

      await this.$http.get('data/semester/semester_now')
        .then(res => this.idSemester = res.data?.id)
      
      await this.$http.get('data/guru/options')
        .then(res => this.fieldsDataPengganti.id_pengganti.options = res.data)

      await this.$http.get('mapel/penjadwalan', {
        params:{
          where:{
            '{n}id_semester':this.idSemester,
            '{n}id_guru':this.idGuru
          },
          order:['id_sesi']
        }
      }).then(res => {
        this.schedules = res.data 
      })

      await this.getRequest()
    },
    async getRequest(){
      let params = {
          where:{
            '{n}id_semester':this.idSemester,
            '{n}kehadiran!= ':'hadir',
            '{n}kehadiran !=':'tidak hadir',
          },
          order:['status_izin, tanggal desc']
        }
      switch (this.currentRole) {
        case 'guru':
          params.where['{n}id_guru'] = this.idGuru
          break;
        default:
          break;
      }
      await this.$http.get('presensi/mengajar/get_all', {
          params:params
      }).then(res => {
          this.requests = res.data 
        })
    },
    resetForm() {
      this.requestStep = 1;
      this.selectedSchedule = null;
      this.selectedSchedules = []
    },
    openRequestModal() {
      this.resetForm();
      this.selectedRequestId = '-1';
      this.isRequestModalOpen = true;
    },
    handleRequestSubmit() {
      if (this.isMultiple) {
        let forms = []
        this.selectedSchedules.forEach(d => {
          forms.push({
            id:-1,
            id_semester: this.idSemester,
            id_guru: this.idGuru,
            id_kelas: d?.id_kelas ?? null,
            id_mapel: d?.id_mapel ?? null,
            tanggal: d?.date ?? null,
            id_sesi: d?.id_sesi ?? null,
            jam: d?.jam ?? null,
            tugas: d?.keterangan_tugas?.length > 0 ? 1 : 0,
            status_izin:'2',
            kehadiran: this.formData.kehadiran,
            alasan: this.formData.kehadiran,
            keterangan_tugas: d?.keterangan_tugas
          })
        })

        this.$http.post('presensi/mengajar/store_many', window.jsonToFormData(forms))
          .then(res => {
            this.getRequest();
            this.resetForm();
            this.isRequestModalOpen = false;
          })
      } else {
        if (!this.selectedSchedule) return;
        this.$refs.formData.submitForm();
      }
    },
    deleteRequest(req){
      useDataStore().deleteData({ href:'presensi/mengajar/delete/', id:req.id})
        .then(res => {
          this.getRequest()
        })
    },
    handleUpdateStatus(id, status) {
      const request = this.requests.find(r => r.id === id);
      if (!request) return;

      let text = ''
      switch (status) {
        case '1':
          text = 'Apakah anda yakin untuk mengajukan permohonan izin ini? Anda tidak bisa lagi mengubah data yang sudah diajukan?'
          break;
        case '-1':
          text = 'Apakah anda yakin untuk menolak permohonan ini?'
          break;
        case '2':
          text = 'Apakah anda yakin untuk menyetujui permohonan ini?'
          break;
        default:
          text = 'Apakah anda yakin untuk mengubah status data ini'
          break;
      }
      this.$confirm(text,
        'Konfirmasi',
        {
          confirmButtonText: 'OK',
          cancelButtonText: 'Batal',
          type: 'warning',
        })
        .then(() => {
          // console.log(this.href)
          let formData = window.jsonToFormData({
            id: id,
            status_izin: status,
          })
          this.$http.post('presensi/mengajar/store', formData)
            .then(result => {
              this.getRequest();
              this.$notify({
                type:'success',
                title: 'Berhasil',
                message: 'Data berhasil diubah',
                position: 'bottom-right'
              });
            })
            .catch(err => {
              this.$notify({
                type:'error',
                title: 'Gagal',
                message: 'Tidak dapat mengubah status data',
                position: 'bottom-right'
              });
            });
          })
          .catch(err => {
            console.log(err)
          });
      // this.addNotification({
      //   title: status === 'APPROVED' ? 'Izin Disetujui' : 'Izin Ditolak',
      //   message: `Permohonan izin Anda untuk ${request.nama_mapel} telah ${status === 'APPROVED' ? 'disetujui' : 'ditolak'}.`,
      //   type: status === 'APPROVED' ? 'SUCCESS' : 'ALERT',
      //   targetRole: 'guru'
      // });
    },
    getClassName(classId) {
      return MOCK_CLASSES.find(c => c.id === classId)?.name || 'N/A';
    },
    getTeacherName(teacherId) {
      return MOCK_guruS.find(t => t.id === teacherId)?.name || 'N/A';
    },
    getStatusText(status) {
      if (status === '2') return 'Disetujui';
      if (status === '1') return 'Diajukan';
      if (status === '-1') return 'Ditolak';
      return 'Draf';
    },
    getStatusClass(status) {
      if (status === '2') return 'bg-[var(--color-main-50)] text-[var(--color-main-600)] group-hover:bg-[var(--color-main-800)] group-hover:text-white';
      if (status === '-1') return 'bg-red-50 text-red-600 group-hover:bg-red-800 group-hover:text-white';
      if (status === '1') return 'bg-blue-50 text-blue-600 group-hover:bg-blue-800 group-hover:text-white';
      return 'bg-slate-50 text-slate-600 group-hover:bg-slate-800 group-hover:text-white';
    },
		selectSchedule(schedule) {
			this.selectedSchedule = schedule;
			this.requestStep = 2;
		},
		handleAssignSubstitute(requestId, substituteId) {
			const substitute = MOCK_TEACHERS.find(t => t.id === substituteId);
			if (!substitute) return;

			const updatedRequests = this.requests.map(r => 
				r.id === requestId ? { ...r, id_pengganti: substituteId } : r
			);
			this.$emit('update:requests', updatedRequests);

			this.addNotification({
				title: 'Guru Pengganti Ditetapkan',
				message: `${substitute.name} telah ditetapkan sebagai pengganti.`,
				type: 'SUCCESS',
				targetRole: 'TEACHER'
			});

			this.isAssignModalOpen = false;
			this.selectedRequestId = null;
		},
    handlePrintRequest(req) {
      const printWindow = window.open('', '_blank');
      if (!printWindow) return;

      printWindow.document.write(`
        <html>
          <head>
            <title>Surat Tugas Santri - ${req.nama_guru}</title>
            <style>
              body { font-family: 'Times New Roman', serif; padding: 40px; line-height: 1.6; }
              .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 20px; margin-bottom: 30px; }
              .header h1 { margin: 0; font-size: 20pt; text-transform: uppercase; }
              .header p { margin: 5px 0; font-size: 11pt; }
              .meta { margin-bottom: 30px; }
              .meta table { width: 100%; border-collapse: collapse; }
              .meta td { padding: 5px 0; vertical-align: top; }
              .meta td.label { font-weight: bold; width: 150px; }
              .content { border: 1px solid #ccc; padding: 20px; border-radius: 10px; min-height: 300px; margin-bottom: 40px; }
              .content h2 { font-size: 14pt; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: 0; }
              .footer { display: flex; justify-content: space-between; margin-top: 50px; }
              .sign-box { text-align: center; width: 200px; }
              .sign-space { height: 80px; }
              @media print { .no-print { display: none; } }
            </style>
          </head>
          <body>
            <div class="header">
              <h1>Pondok Pesantren Muhammadiyah Darul Arqom Patean</h1>
              <p>Jl. Pendidikan No. 123, Indonesia | Telp: (021) 1234567</p>
              <p>SISTEM PRESENSI DIGITAL - SURAT TUGAS MANDIRI SANTRI</p>
            </div>
            <div class="meta">
              <table>
                <tr><td class="label">Nama Guru</td><td>: ${req.nama_guru}</td></tr>
                <tr><td class="label">Mata Pelajaran</td><td>: ${req.nama_mapel}</td></tr>
                <tr><td class="label">Kelas</td><td>: Kelas ${req.kelas}</td></tr>
                <tr><td class="label">Tanggal</td><td>: ${req.tanggal}</td></tr>
                <tr><td class="label">Alasan Izin</td><td>: ${ucFirst(req.kehadiran)} - ${req.alasan}</td></tr>
              </table>
            </div>
            <div class="content">
              <h2>INSTRUKSI TUGAS UNTUK SANTRI</h2>
              <div style="white-space: pre-wrap;">${req.keterangan_tugas}</div>
            </div>
            <div class="footer">
              <div class="sign-box">
                <p>Mengetahui,</p>
                <p>Kepala KMI</p>
                <div class="sign-space"></div>
                <p>Agus Budi Utomo</p>
              </div>
              <div class="sign-box">
                <p>Tertanda,</p>
                <p>Guru Pengampu</p>
                <div class="sign-space"></div>
                <p><b>${req.nama_guru}</b></p>
              </div>
            </div>
          </body>
        </html>
      `);
      printWindow.onload = () => {
        printWindow.print();
        printWindow.close();
      };
      printWindow.document.close();
    },
    handleDownloadRequest(req) {
      // 1. Definisikan CSS dan Meta secara terpisah agar rapi
      const styles = `
        <style>
          body { font-family: 'Times New Roman', serif; line-height: 1.5; padding: 40pt; }
          .header { text-align: center; border-bottom: 2pt double #000; padding-bottom: 15pt; margin-bottom: 20pt; }
          .header h1 { margin: 0; font-size: 18pt; text-transform: uppercase; }
          .meta { margin-bottom: 20pt; }
          .meta table { width: 100%; border-collapse: collapse; }
          .meta td { padding: 3pt 0; vertical-align: top; font-size: 11pt; }
          .meta td.label { font-weight: bold; width: 140pt; }
          .content-box { border: 1pt solid #ccc; padding: 15pt; min-height: 250pt; margin-bottom: 30pt; }
          .content-box h2 { font-size: 13pt; border-bottom: 1pt solid #eee; padding-bottom: 8pt; margin-top: 0; color: #000; }
          .footer-section { margin-top: 40pt; }
          .sign-box { width: 200pt; text-align: center; }
          .sign-space { height: 60pt; }
        </style>
      `;

      // 2. Header HTML (Tag <html> dan <head> TIDAK BOLEH ditutup sebelum body)
      const htmlHeader = `
        <html xmlns:o='urn:schemas-microsoft-com:office:office' 
              xmlns:w='urn:schemas-microsoft-com:office:word' 
              xmlns='http://www.w3.org/TR/REC-html40'>
          <head>
            <meta charset='utf-8'>
            <title>Surat Tugas Santri</title>
            ${styles}
          </head>
          <body>
      `;

      // 3. Konten Dokumen
      const htmlBody = `
        <div class="header">
          <h1>Pondok Pesantren Muhammadiyah Darul Arqom Patean</h1>
          <p>Jln. Tugas Mas, Pagersari, Patean, Kendal 51364</p>
          <p><b>SISTEM PRESENSI DIGITAL - SURAT TUGAS MANDIRI SANTRI</b></p>
        </div>
        <div class="meta">
          <table>
              <tr><td class="label">Nama Guru</td><td>: ${req.nama_guru}</td></tr>
              <tr><td class="label">Mata Pelajaran</td><td>: ${req.nama_mapel}</td></tr>
              <tr><td class="label">Kelas</td><td>: Kelas ${req.kelas}</td></tr>
              <tr><td class="label">Tanggal</td><td>: ${req.tanggal}</td></tr>
              <tr><td class="label">Alasan Izin</td><td>: ${ucFirst(req.kehadiran)} - ${req.alasan}</td></tr>
          </table>
        </div>
        <div class="content-box">
          <h2>INSTRUKSI TUGAS UNTUK SANTRI</h2>
          <div style="white-space: pre-wrap; font-size: 11pt;">${req.keterangan_tugas?.replaceAll('\n','<br/>') || 'Belum ada instruksi tugas.'}</div>
        </div>
        <div class="footer-section">
          <table style="width: 100%;">
            <tr>
              <td class="sign-box">
                <p>Mengetahui,</p>
                <p>Kepala KMI</p>
                <div class="sign-space"></div>
                <p>Agus Budi Utomo</p>
              </td>
              <td></td>
              <td class="sign-box">
                <p>Tertanda,</p>
                <p>Guru Pengampu</p>
                <div class="sign-space"></div>
                <p><b>${req.nama_guru}</b></p>
              </td>
            </tr>
          </table>
        </div>
      `;

      const htmlFooter = "</body></html>";

      // Gabungkan semua komponen
      const source = htmlHeader + htmlBody + htmlFooter;
      
      // Gunakan Blob dengan BOM (\ufeff) agar karakter khusus/UTF-8 terbaca dengan benar
      const blob = new Blob(['\ufeff', source], { type: 'application/msword' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      
      // Format nama file
      const fileName = `Surat_Tugas_${req.nama_mapel.replace(/\s+/g, '_')}_${req.tanggal}.doc`;
      link.download = fileName;
      
      document.body.appendChild(link); // Penting untuk beberapa browser
      link.click();
      document.body.removeChild(link);
      
      URL.revokeObjectURL(url);
    }
  },
  mounted() {
    this.getSchedule()
  }
};
</script>
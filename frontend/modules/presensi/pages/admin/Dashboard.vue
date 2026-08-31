<template>
  <div class="px-2 md:px-3 pt-12 lg:pt-4 bg-white/[0.6]">
		<div class=" mb-4 lg:mb-6 flex justify-between flex-col lg:flex-row" >
			<div v-if="showPage == 'dashboard'">
				<div class="text-4xl font-black text-slate-900 tracking-tight m-0">Academic Monitor</div>
				<div class="text-lg text-slate-500 font-medium italic">Pemantauan real-time kehadiran guru </div>
			</div>
			<div class="space-y-1" v-if="showPage == 'qr'">
				<div class="flex items-center gap-2 text-[var(--color-main-600)] font-black text-[13px] uppercase tracking-widest mb-1">
					<icons icon="bxs:layer"/> Academic QR Management
				</div>
				<div class="text-4xl font-black text-slate-900 tracking-tight">
					Manajemen QR Kelas
				</div>
				<div class="text-slate-500 font-medium italic text-lg">Pilih untuk mencetak QR Code (6 per halaman A4).</div>
			</div>
			<div class="order-first lg:order-last text-center mb-2">
				<el-radio-group v-model="showPage" class="p-2 rounded-xl bg-slate-100 font-bold" fill="#0f766e"  >
					<el-radio-button value="dashboard" class="*:rounded-xl px-1 *:px-6 *:font-bold *:text-[16px]">Dashboard</el-radio-button>
					<el-radio-button value="qr" class="*:rounded-xl px-1 *:px-6 *:font-bold *:text-[16px]">Print QR Code</el-radio-button>
				</el-radio-group>
			</div>
		</div>
		<div v-if="showPage == 'qr'">
			<div class="order-first lg:order-last text-left mb-2">
				<el-radio-group v-model="showDashboard" class="p-2 rounded-xl bg-slate-100 font-bold" fill="#2e81f2"  >
					<el-radio-button value="kedatangan" class="*:rounded-xl px-1 *:px-6 *:font-bold *:text-[16px]">Kode Kedatangan</el-radio-button>
					<el-radio-button value="presensi" class="*:rounded-xl px-1 *:px-6 *:font-bold *:text-[16px]">Kode Mengajar</el-radio-button>
				</el-radio-group>
			</div>
			<div class="space-y-5 pb-20">
				
				<div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4" v-if="showDashboard == 'presensi'">

					<div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
						<el-button 
							@click="selectAll"
							class="!w-full sm:!w-auto !px-4 !py-5 !rounded-xl !text-[13px] !font-black !uppercase !tracking-widest"
						>
							{{ selectedIds.size === filteredClasses.length ? 'Batalkan Semua' : 'Pilih Semua' }}
						</el-button>
						<div class="relative w-full md:w-64">
							<icons icon="mdi:search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]" />
							<input 
								type="text" 
								placeholder="Cari kelas..." 
								v-model="searchQuery"
								class="w-full bg-white border border-slate-200 rounded-xl pl-10 pr-3 py-2 text-base font-bold shadow-sm outline-none focus:ring-2 focus:ring-[var(--color-main-500)] transition-all"
							/>
						</div>
					</div>
				</div>

				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
					<div 
						v-for="cls in filteredClasses" 
						:key="cls.id" 
						@click="toggleSelect(cls.id)"
						:class="[
							'bg-white p-4 rounded-[2rem] border transition-all group overflow-hidden relative cursor-pointer',
							selectedIds.has(cls.id) ? 'border-[var(--color-main-500)] shadow-xl ring-2 ring-[var(--color-main-500)]' : 'border-slate-100 shadow-sm hover:shadow-lg'
						]"
					>
						<div :class="[
							'absolute top-4 left-4 w-8 h-8 rounded-lg flex items-center justify-center transition-all border-2',
							selectedIds.has(cls.id) ? 'bg-[var(--color-main-500)] border-[var(--color-main-500)] text-white' : 'bg-slate-50 border-slate-100 text-transparent'
						]">
							<icons icon="mdi:check" class="text-[20px] font-bold m-0" />
						</div>

						<div class="space-y-1">
							<div class="text-center">
								<div class="text-2xl font-black text-slate-900 mb-1" v-if="showDashboard == 'presensi'">KELAS {{ cls.kelas }}</div>
								<div class="text-[12px] font-black text-slate-400  tracking-widest bg-slate-100 px-3 py-1 rounded-full w-fit mx-auto">
									{{ cls.kode }}
								</div>
							</div>

							<div class="flex items-center justify-center p-2 bg-slate-50 rounded-[1.5rem] border border-slate-100 group-hover:bg-white transition-colors relative">
								<div class="relative">
									<img 
										:src="getQRUrl(cls.kode, 200)" 
										:alt="`QR for ${cls.kelas}`" 
										class="w-36 h-36 transition-all"
										:class="selectedIds.has(cls.id) ? 'opacity-100 scale-105' : 'opacity-60 group-hover:opacity-100'"
									/>
									<div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-6 h-6 bg-white p-[2px] rounded shadow-sm border border-slate-100">
										<img :src="PONDOK_LOGO_URL" class="w-full h-full object-contain" />
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div v-if="selectedIds.size > 0" class="absolute left-1/2 -translate-x-1/2 bottom-0" v-fixed-to-position="'100vh - 200px'">
					<div class="bg-slate-900 text-white p-4 rounded-[1.5rem] shadow-2xl border border-white/10 flex flex-col md:flex-row items-center justify-between gap-4">
							<div class="flex items-center gap-3 ml-2 w-full">
								<div class="bg-[var(--color-main-500)] rounded-xl flex items-center justify-center shadow-lg p-2">
									<icons icon="lucide:file-check" class="text-[24px] m-0" />
								</div>
								<div class="w-[180px]">
										<div class="font-black text-xl leading-tight">{{ selectedIds.size }} Kode Terpilih</div>
										<div class="text-[12px] text-slate-400 font-medium">Siap dicetak ({{ Math.ceil(selectedIds.size / 6) }} Halaman A4)</div>
								</div>
							</div>
							<div class="flex items-center gap-2 w-full md:w-auto">
								<el-button @click="selectedIds.clear()" class="!bg-white/5 hover:!bg-white/10 !text-white !border-none !rounded-xl !px-6 !py-5 !text-[12px] !font-black !uppercase">
										Batal
								</el-button>
								<el-button @click="handleBatchPrint" type="success" class="!bg-[var(--color-main-500)] !rounded-xl !py-5 !px-8 !font-black !uppercase !text-[12px] !flex !items-center !gap-2">
									<icons icon="material-symbols:print" class="text-[18px]" /> Cetak Kolektif
								</el-button>
							</div>
					</div>
				</div>

			</div>
		</div>
		<div v-else-if="showPage == 'dashboard'">
			<div class="order-first lg:order-last text-left mb-2">
				<el-radio-group v-model="showDashboard" class="p-2 rounded-xl bg-slate-100 font-bold" fill="#2e81f2"  >
					<el-radio-button value="kedatangan" class="*:rounded-xl px-1 *:px-6 *:font-bold *:text-[16px]">Presensi Kedatangan</el-radio-button>
					<el-radio-button value="presensi" class="*:rounded-xl px-1 *:px-6 *:font-bold *:text-[16px]">Presensi Mengajar</el-radio-button>
				</el-radio-group>
			</div>
			<DashboardKedatangan class="mb-3" v-if="showDashboard == 'kedatangan'"/>
			
			<DashboardPresensi v-else :classes="classes"/>
		</div>
	</div>
</template>

<script>
import DashboardKedatangan from './components/DashboardKedatangan.vue'
import DashboardPresensi from './components/DashboardPresensi.vue'

export default {
	name:'dashboard-admin',
	components:{
		DashboardKedatangan,
		DashboardPresensi,
	},
	setup(){
		const { openLink } = useBrowserActions()
		return {
			dateDayIndo, dateNow, openLink,
		}
	},
	data() {
		return {
			showPage:'dashboard',
			showDashboard:'presensi',
			editSesi:false,
      searchQuery: '',
      selectedIds: new Set(),
      PONDOK_LOGO_URL: this.$logoDefault,
			classes:[],
		}
	},
	watch:{
		showDashboard(val){
			if (val == 'kedatangan')
				this.classes = [
					{id:1, kode:'PresensiHarian'}
				]
			else
				this.getClass()
		}
	},
  computed: {
    filteredClasses() {
      const query = this.searchQuery.toLowerCase();
      return this.classes.filter(c => 
        c?.kelas?.toLowerCase()?.includes(query) || 
        c?.kode?.toLowerCase()?.includes(query)
      );
    }
  },
	methods:{
    toggleSelect(id) {
      // Pada Options API, kita harus memicu reaktivitas untuk Set secara manual atau mengganti Set
      const newSet = new Set(this.selectedIds);
      if (newSet.has(id)) newSet.delete(id);
      else newSet.add(id);
      this.selectedIds = newSet;
    },
    selectAll() {
      if (this.selectedIds.size === this.filteredClasses.length) {
        this.selectedIds = new Set();
      } else {
        this.selectedIds = new Set(this.filteredClasses.map(c => c.id));
      }
    },
		getQRUrl(code, size = 200) {
      return `https://api.qrserver.com/v1/create-qr-code/?size=${size}x${size}&data=${code}`;
    },
    handleBatchPrint() {
      const classesToPrint = this.classes.filter(c => this.selectedIds.has(c.id));
      const printWindow = window.open('', '_blank');
      if (!printWindow) return;

      let cardsHtml = '';
      classesToPrint.forEach(cls => {
        cardsHtml += `
          <div style="border:2px dashed #10b981; border-radius:15mm; padding:4mm; text-align:center; position:relative; break-inside:avoid; margin-bottom:10mm">`;

				if (this.showDashboard == 'presensi')
          cardsHtml += `<h2 style="font-size:24pt; margin-bottom:4mm">KELAS ${cls.kelas}</h2>`

        cardsHtml += `<div style="position:relative; display:inline-block; margin-bottom:3mm;">
              <img src="${this.getQRUrl(cls.kode, 500)}" style="width:50mm">
              <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:7mm; background:white; padding:0.5mm; border-radius:1mm">
                <img src="${this.PONDOK_LOGO_URL}" style="width:100%">
              </div>
            </div>
          </div>
        `;
      });

      printWindow.document.write(`
        <html>
          <head><style>@page { size: A4; margin: 10mm; } .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10mm; }</style></head>
          <body>
            <div class="grid">${cardsHtml}</div>
            <script>window.onload=()=>{window.print();window.close()}<\/script>
          </body>
        </html>
      `);
      printWindow.document.close();
    },
		getClass(){
			this.$http.get('data/kelas')
				.then(res => {
					this.classes = res.data
					this.classes.forEach(d => {
						d.kode = 'SI/KMI-' + d.id
					})
				}
			)
		},
	},
	created(){
		this.getClass()
	}
}
</script>
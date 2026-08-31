<template>
    <div id="nilai" class="py-2">
      <el-card class="bg-white/[0.7] mb-2">
        <form-comp ref="formFilter"
          :key="formKey"
          :fields="filterFields"
          size="default"
          label-position="left"
          :show-label="showLabel"
          class="max-sm:mt-4"
          form-class="m-0"
          form-item-class="mb-2"
          label-width="150px"
          v-model:form-value="filter"
          :pass-columns="[]"
          :show-submit="false"
          text-submit="Cari"
          error-submit-text="Tidak dapat mengambil data"
          :show-required-text="false"
          >
        </form-comp>
      </el-card>
      <el-card class="bg-white/[0.7]"
        body-class="p-0">
        <div :class="['animate px-3 pt-3 pb-2 flex justify-between']">
          <div class="text-md font-bold mb-2 flex items-center gap-4">
            Tanggal Cetak : <date-wheel-picker v-model:value="tanggal" :placeholder="'Pilih tanggal'" :clearable="false" :disabled="generating" />
          </div>
          <div class="text-right md:block hidden">  
            <el-button size="small" type="success" @click="downloadLedger">
              <icons icon="ri:file-excel-2-fill" /> Ledger Raport
            </el-button>
            <el-button v-if="idUnit == 3" size="small" type="primary" @click="downloadRaport('smk')">
              <icons icon="ri:file-pdf-2-fill" /> Unduh Raport SMK
            </el-button>
            <el-button size="small" type="primary" @click="downloadRaport(false)">
              <icons icon="ri:file-pdf-2-fill" /> Unduh Raport
            </el-button>
            <el-button size="small" type="primary" @click="downloadRaport('pengasuhan')">
              <icons icon="ri:file-pdf-2-fill" /> Unduh Raport Pengasuhan
            </el-button>
          </div>
          <el-dropdown
            trigger="click"
            class="md:hidden block text-right">
            <el-button class="" type="success" size="small">
              Aksi <icons icon="mdi:arrow-down" class="m-0 ml-2"/>
            </el-button>
            <template #dropdown>
              <el-dropdown-menu>
                <el-dropdown-item @click="downloadLedger">
                  <icons icon="ri:file-excel-2-fill" /> Ledger Raport
                </el-dropdown-item>
                <el-dropdown-item @click="downloadRaport('smk')">
                  <icons icon="ri:file-pdf-2-fill" /> Unduh Raport SMK
                </el-dropdown-item>
                <el-dropdown-item @click="downloadRaport(false)">
                  <icons icon="ri:file-pdf-2-fill" /> Unduh Raport
                </el-dropdown-item>
                <el-dropdown-item @click="downloadRaport('pengasuhan')">
                  <icons icon="ri:file-pdf-2-fill" /> Unduh Raport Pengasuhan
                </el-dropdown-item>
              </el-dropdown-menu>
            </template>
          </el-dropdown>
        </div>

        <el-dialog
          title="Unduh Raport"
          append-to-body
          v-model="showDownload"
          class="max-w-[500px]"
          :close-on-click-modal="false">
          <div class="text-center p-5">
            <div class="text-blue-500 mb-3">
              <icons v-if="generating" icon="line-md:downloading-loop" class="text-[50px]"/>
              <icons v-else icon="fa7-solid:compress-arrows-alt" class="text-[50px]"/>
            </div>
            <div class="text-lg mb-2">Sedang menyiapkan file raport</div>
            <div class="mb-5">
              <span v-if="generating">
                Tunggu hingga proses selesai, jangan tutup halaman ini<br/>
                File Raport {{ dataNilai[(files.length - 1)]?.nama ?? '' }} sedang disiapkan...
              </span>
              <span v-else>Membuat File Zip. Halaman Unduh akan muncul sebentar lagi. Jika tidak, maka klik 
                <a @click="downloadRaport" class="cursor-pointer underline">disini</a> </span>
            </div>
            <el-progress :percentage="percentage"
              :stroke-width="24"
              :show-text="true"
              striped
              striped-flow
              >
              {{ files.length }} / {{ dataNilai.length }} file
            </el-progress>
          </div>
        </el-dialog>

        <div class="relative bg-white" v-loading="loading">
          <div v-if="dataNilai.length == 0"
            class="text-center text-gray-500 text-lg p-5">
            <icons icon="mdi:alert" class="text-[50px] mb-3" />
            <div class="text-[18px]">Tidak ada data nilai</div>
          </div>
          <table-freeze v-else
            ref="tableFreeze"
            :data="dataNilai">
            <template #header="{data}">
              <tr class="*:border *:border-solid *:border-slate-300">
                <th width="20px" class="fixed-col">No</th>
                <th class="fixed-col min-w-[100px]">Nama</th>
                <th align="center" class="text-center" width="60px" v-if="filter.id_semester % 2 == 0">Naik Kelas</th>
                <th align="center" class="text-center">Unduh Rapor</th>
              </tr>
            </template>
            <template #body="{data}">
              <tr v-for="(d, key) in data"
                class="*:border *:border-solid *:border-slate-300">
                <td>{{ key + 1 }}</td>
                <td>{{ d.nama }}</td>
                <td class="text-center" v-if="filter.id_semester % 2 == 0">
                  <el-checkbox v-model="d.naik_kelas"
                    @change="toggleNaikKelas(d)"
                    true-value="1" false-value="0"/>
                </td>
                <td align="center" class="text-center">
                  <el-button type="primary" size="small" @click="fetchAndDownloadRaport(d.id_santri, '', '1')">
                    <icons icon="mdi:file-pdf" /> Rapor KMI
                  </el-button>
                  <el-button type="primary" size="small" @click="fetchAndDownloadRaport(d.id_santri, 'pengasuhan', '1')">
                    <icons icon="mdi:file-pdf" /> Rapor Pengasuhan
                  </el-button>
                  <el-button v-if="idUnit == 3" type="primary" size="small" @click="fetchAndDownloadRaport(d.id_santri, 'smk', '1')">
                    <icons icon="mdi:file-pdf" /> Rapor SMK
                  </el-button>
                </td>
              </tr>
            </template>
          </table-freeze>
        </div>
      </el-card>
    </div>
</template>
  
<script>
  import { mapState } from 'pinia';
  import { useAuthStore } from '@/config/stores/authStore'
  import { useDataStore } from '@/config/stores/dataStore'
  
  
  export default {
    name: "mapel",
    setup(){
      const { openLink, openPost } = useBrowserActions()
      return {
        openLink, openPost, dateNow, dateIndo,
      }
    },
    components: {
      
    },
    data: function() {
      return {
        showDownload:false,
        generating:true,
        progress:0,
        downloadStatus:'',
        files:[],
        loading: false,
        saving: false,
        filterFields: {
          id_semester:{
            label:'Semester',
            nama_kolom:'id_semester',
            input:'select',
            options:[],
          },
          id_kelas:{
            label:'Kelas',
            nama_kolom:'id_kelas',
            input:'select',
            input_only:'1',
            options:[],
          },
        },
        filter:{
          id_semester:'',
          id_kelas:'',
        },
        params:{
          where:[],
        },
        tanggal: dateNow(),
        editId:-1,
        ids:[],
        formKey:0,
        dataNilai:[],
        scrollY:0,
        showHidden: 286,
        PembagianMapel:{},
        newTab:'',
        // role:'guru',
      };
    },
    watch: {
      
      'filter.id_semester' (val){
        if (!isEmpty(val)) {
          this.getKelasOptions()
        }
      },
      'filter.id_kelas' (val){
        if (!isEmpty(val))
          this.getData()
      },
    },  
    computed: {
      ...mapState(useAuthStore, {
        user: 'loggedUser',
        role: 'role',
      }),
      ...mapState(useDataStore, {
        storeFilters: 'filters',
      }),
      showLabel(){
        return this.$windowWidth.value > 800
      },
      idUnit(){
        let options = this.filterFields.id_kelas.options
        if (options) {
          if (typeof options == 'object') options = Object.values(options)
          let kelas = options.find(d => d.value == this.filter.id_kelas)
          return kelas?.id_unit ?? null
        } else {
          return null 
        }
      },  
      tahun_ajaran(){
        let options = this.filterFields.id_semester.options
        if (options) {
          if (typeof options == 'object') options = Object.values(options)
          let semester = options.find(d => d.value == this.filter.id_semester)
          return semester?.tahun_ajaran ?? null
        } else {
          return null 
        }
      },
      percentage(){
        if (this.files.length == 0) return 0
        let all = this.dataNilai.length
        return Math.round((this.files.length / all) * 100)
      },
    },
    methods: {
      
      getInitial: async function() {
          this.loading = true;
          this.$http.get('data/semester/options')
            .then(res => {
              this.loading = false;
              let options = res.data
              this.filterFields.id_semester.options = options
              this.filter.id_semester = this.storeFilters?.id_semester ? this.storeFilters?.id_semester : this.user.id_semester?? Object.values(options)[0]?.value
            })
        },
      getKelasOptions(){
        this.$http.get('data/kelas-ajar/options',{
          params:{
            where:{
              tahun_ajaran:this.tahun_ajaran
            }
          }
        })
          .then(res => {
            this.loading = false;
            let options = res.data
            this.filterFields.id_kelas.options = options
            this.filterFields.id_kelas.readonly = this.role != 'admin'
            this.filter.id_kelas = this.storeFilters?.id_kelas ? this.storeFilters?.id_kelas : this.user.id_kelas?? Object.values(options)[0]?.value
          })

      },
      getData(){
        this.loading = true
        this.$http.get('rapor/rekapitulasi',{
          params: {
            id_semester: this.filter.id_semester,
            id_kelas: this.filter.id_kelas
          }
        }).then(result => {
          this.loading = false
          this.dataNilai = result.data
        })
      },
      downloadLedger(){
        this.openLink(this.$siteUrl + `rapor/download_ledger_akhir?id_semester=${this.filter.id_semester}&id_kelas=${this.filter.id_kelas}&ujian=nilai_rapor`)
      },
      // downloadRaport(){
      //   this.this.openLink(this.$siteUrl + `rapor/download_raport?id_semester=${this.filter.id_semester}&id_kelas=${this.filter.id_kelas}`)
      // },
      async downloadRaport(type = false) {
        this.showDownload = true;
        this.generating = true;
        this.files = [];
        let semesterText = this.filterFields.id_semester.options.find(opt => opt.value == this.filter.id_semester)?.label.replace(/\s+/g, '-').toUpperCase() || 'SEMESTER';
        let kelasText = this.filterFields.id_kelas.options.find(opt => opt.value == this.filter.id_kelas)?.label.replace(/\s+/g, '-').toUpperCase() || 'KELAS';
        // for (const santri of this.dataNilai) {
        //   const id = santri.id_santri;
        //   try {
        //     await this.fetchAndDownloadRaport(id);
        //     console.log(`Report ${id} downloaded.`);
        //   } catch (error) {
        //     console.error(`Failed to download report ${id}:`, error);
        //   }
        // }

        let i = 0;
        // console.log(type)
        // return

        // do {
        //   const id = this.dataNilai[i].id_santri;

        //   try {
        //     // tunggu sampai satu download selesai
        //     await this.fetchAndDownloadRaport(id, type);
        //     console.log(`Report ${id} selesai`);
        //     i++;
        //   } catch (e) {
        //     console.error(`Gagal download ${id}:`, e);
        //   }

        // } while (i < this.dataNilai.length);
        
        for (i = 0; i < this.dataNilai.length; i++) {
          const id = this.dataNilai[i].id_santri;
          try {
            await this.fetchAndDownloadRaport(id, type);
            console.log(`Report ${id} selesai`);
          } catch (e) {
            console.error(`Gagal download ${id}:`, e);
          }
        }
        console.log("All reports processed. Sending to zip...");
        this.generating = false;
        // Convert to FormData
        const formData = window.jsonToFormData({ 
          files: this.files,
          delete_original: true,
        });

        
        try {
          console.log(this.files, this.$siteUrl + 'download_zip?' + objectToQueryParams({files:this.files}))
          this.openPost(this.$siteUrl + 'download_zip', {
            files:this.files,
            delete_original: true,
            zip_name: ('RAPORT-AKHIR-'+ (type ? (type + '-') : '') + semesterText + '-' + kelasText+'.zip').replace(' ', '-').replace('_', '-').replace('/','-').toUpperCase(),
          });
          this.showDownload = false;
          return
          const zipResponse = await this.$http.post('download_zip', formData, {
            responseType: 'blob', // if your backend returns a zip file
            onUploadProgress: (progressEvent) => {
              if (progressEvent.lengthComputable) {
                const percent = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                this.progress = percent;
                this.downloadStatus = `Uploading file list... ${percent}%`;
              }
            }
          });

          // ✅ Trigger download of the zip file
          const blob = new Blob([zipResponse.data], { type: 'application/zip' });
          const link = document.createElement('a');
          link.href = URL.createObjectURL(blob);
          link.download = 'RAPORT-AKHIR-'+ (type ? (type + '-') : '') + semesterText + '-' + kelasText+'.zip';
          link.download = link.download.replace(' ', '-').replace('_', '-').replace('/','-');
          document.body.appendChild(link);
          link.click();
          URL.revokeObjectURL(link.href);
          document.body.removeChild(link);
          // this.showDownload = false;
        } catch (error) {
          console.error("Failed to zip/download files:", error);
        }
      },

      async fetchAndDownloadRaport(id, type, download = null) {
        if (download) {
          return this.openPost(this.$siteUrl + 'rapor/download_raport' + (type ? `_${type}` : ''), {
              id_semester: this.filter.id_semester,
              id_kelas: this.filter.id_kelas,
              tanggal: this.tanggal,
              id_santri: id,
              ujian:'nilai_rapor',
              download:download,
          },'_blank');
        }
        try {
          const result = await this.$http.get('rapor/download_raport' + (type ? `_${type}` : ''), {
            params: {
              id_semester: this.filter.id_semester,
              id_kelas: this.filter.id_kelas,
              id_santri: id,
              tanggal: this.tanggal,
              ujian:'nilai_rapor',
              download:download,
            }
          });
          
          // Add the file path to this.files
          this.files.push(result.data);

          return result.data; // ⬅️ Return the path for consistency
        } catch (error) {
          console.error(`Error downloading report for santri ID ${id}:`, error);
          throw error; // allow caller to handle it
        }
      },
      toggleNaikKelas(santri){
        this.$http.post('rapor/store', window.jsonToFormData({
          id: santri.id_rapor,
          naik_kelas: santri.naik_kelas,
        })).then(res => {
          // console.log(res.data)
        })
      }
    },
    created: function() {
      this.getInitial()
      // console.log(this.$router);
    },
    mounted(){
    },
    beforeUnmount() {
      let dataStore = useDataStore()
      Object.entries(this.filter).forEach(([index, val]) =>
        dataStore.setFilter({
          key:index,
          val:val
        })
      )
      // console.log('change-filter', dataStore.filters)
    },
  }
  </script>
  
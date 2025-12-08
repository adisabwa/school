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
        <div :class="['animate px-3 pt-3 pb-2']">
          <div class="text-right md:block hidden">
            <el-button size="small" type="success" @click="downloadLedger">
              <icons icon="ri:file-excel-2-fill" /> Ledger Raport
            </el-button>
            <el-button size="small" type="primary" @click="downloadRaport">
              <icons icon="ri:file-pdf-2-fill" /> Unduh Raport
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
                <el-dropdown-item @click="downloadRaport">
                  <icons icon="ri:file-pdf-2-fill" /> Unduh Raport
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
                <th width="20px" class="fixed-col" rowspan="2">No</th>
                <th class="fixed-col min-w-[160px]" rowspan="2">Nama</th>
                <th v-for="(mapel, key) in data[0].mapel"
                  :key="key"
                  colspan="4"
                  class="text-center">
                  {{ mapel.nama_mapel ?? mapel }}
                </th>
                <th rowspan="2"
                  class="text-center px-2">Jml
                </th>
                <th rowspan="2"
                  class="text-center px-2">Rata2
                </th>
                <th rowspan="2"
                  class="text-center px-2">Ranking
                </th>
              </tr>
              <tr class="*:border *:border-solid *:border-slate-300 *:px-2">
                <template v-for="(mapel, key) in data[0].mapel">
                  <th>NH</th>
                  <th>UTS</th>
                  <th>UAS</th>
                  <th>Raport</th>
                </template>
              </tr>
            </template>
            <template #body="{data}">
              <tr v-for="(d, key) in data"
                class="*:border *:border-solid *:border-slate-300">
                <td>{{ key + 1 }}</td>
                <td>{{ d.nama }}</td>
                <template v-for="(mapel, mKey) in d.mapel">
                  <td v-for="ujian in ['nilai_harian', 'uts', 'uas', 'nilai_rapor']"
                    :key="mKey"
                    :class="['text-center px-2',
                      mapel[ujian] > 0 && mapel[ujian] < 100 ? '' : 'bg-red-700 text-white']">
                    {{ mapel[ujian] }}
                  </td>
                </template>
                <td class="text-center font-bold">{{ d.total_nilai_rapor }}</td>
                <td class="text-center font-bold">{{ (d.total_nilai_rapor / d.mapel.length).toFixed(2) }}</td>
                <td class="text-center font-bold">{{ d.ranking }}</td>
              </tr>
            </template>
          </table-freeze>
        </div>
      </el-card>
    </div>
</template>
  
<script>
  import { zip } from 'lodash';
import { mapState } from 'pinia';
  
  
  export default {
    name: "mapel",
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
        editId:-1,
        ids:[],
        formKey:0,
        dataNilai:[],
        scrollY:0,
        showHidden: 286,
        PembagianMapel:{},
        // role:'guru',
      };
    },
    watch: {
      'filter.id_semester' (val){
        if (!this.isEmpty(val))
          this.getData()
      },
      'filter.id_kelas' (val){
        if (!this.isEmpty(val))
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
        return this.$windowWidth > 800
      },
      percentage(){
        if (this.files.length == 0) return 0
        let all = this.dataNilai.length
        return Math.round((this.files.length / all) * 100)
      }
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
          this.$http.get('data/kelas/options')
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
          setTimeout(() => {
            this.$refs.tableFreeze?.getFreezeHeader()
          }, 300)
        })
      },
      downloadLedger(){
        this.openLink(this.$siteUrl + `rapor/download_ledger?id_semester=${this.filter.id_semester}&id_kelas=${this.filter.id_kelas}`)
      },
      // downloadRaport(){
      //   this.openLink(this.$siteUrl + `rapor/download_raport?id_semester=${this.filter.id_semester}&id_kelas=${this.filter.id_kelas}`)
      // },
      async downloadRaport() {
        this.showDownload = true;
        this.generating = true;
        this.files = [];
        let semesterText = this.filterFields.id_semester.options.find(opt => opt.value == this.filter.id_semester)?.label.replace(/\s+/g, '-').toUpperCase() || 'SEMESTER';
        let kelasText = this.filterFields.id_kelas.options.find(opt => opt.value == this.filter.id_kelas)?.label.replace(/\s+/g, '-').toUpperCase() || 'KELAS';
        for (const santri of this.dataNilai) {
          const id = santri.id_santri;
          try {
            await this.fetchAndDownloadRaport(id);
            console.log(`Report ${id} downloaded.`);
          } catch (error) {
            console.error(`Failed to download report ${id}:`, error);
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
          // console.log(this.files, this.$siteUrl + 'download_zip?' + this.objectToQueryParams({files:this.files}))
          this.openPost(this.$siteUrl + 'download_zip', {
            files:this.files,
            delete_original: true,
            zip_name: ('RAPORT-AKHIR-'+ semesterText + '-' + kelasText+'.zip').replace(' ', '-').replace('_', '-').replace('/','-'),
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
          link.download = 'RAPORT-MID-SEMESTER-'+ semesterText + '-' + kelasText+'.zip';
          link.download.replace(' ', '-');
          link.download.replace('_', '-');
          document.body.appendChild(link);
          link.click();
          URL.revokeObjectURL(link.href);
          document.body.removeChild(link);
          // this.showDownload = false;
        } catch (error) {
          console.error("Failed to zip/download files:", error);
        }
      },
      async fetchAndDownloadRaport(id) {
        try {
          const result = await this.$http.get('rapor/download_raport', {
            params: {
              id_semester: this.filter.id_semester,
              id_kelas: this.filter.id_kelas,
              id_santri: id,
              ujian:'nilai_rapor',
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
  
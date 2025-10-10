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
              <span v-if="generating">Tunggu hingga proses selesai, jangan tutup halaman ini</span>
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
          <div v-else>
            <div id="freeze-container" class="mx-3 overflow-x-hidden w-full h-full">
              <div></div>
            </div>
            <div class="mx-3 overflow-x-auto mb-5" @scroll="(event) => {
              let tFreezeHead = jquery('#table-freeze-head')
              let target = event.target
              let scrollLeft = target.scrollLeft
              let left = scrollLeft - 13
              tFreezeHead.css({left: -left + 'px'})
            }">
              <table id="table-base" class=" table mt-1 md:text-[14px] text-[12px] leading-[1.5] mb-4">
                <thead class="bg-slate-100 ">
                  <tr class="*:border *:border-solid *:border-slate-300">
                    <th width="20px" class="fixed-col">No</th>
                    <th class="fixed-col min-w-[160px]">Nama</th>
                    <th v-for="(mapel, key) in [...dataNilai[0].mapel,...['Jumlah','Rata-Rata','Ranking']]"
                      :key="key"
                      class="">
                      <div
                        class="relative text-center h-[150px] w-[18px]
                          overflow-hidden">
                        <div class="absolute bottom-1/2 translate-y-1/2 left-1/2 -translate-x-1/2 rotate-90 flex flex-col justify-end items-center p-1
                          w-[200px]">
                          {{ mapel.nama_mapel ?? mapel }}
                        </div>
                      </div>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(data, key) in dataNilai"
                    class="*:border *:border-solid *:border-slate-300">
                    <td>{{ key + 1 }}</td>
                    <td>{{ data.nama }}</td>
                    <td v-for="(mapel, mKey) in data.mapel"
                      :key="mKey"
                      class="text-center ">
                      {{ mapel.uts }}
                    </td>
                    <td class="text-center font-bold">{{ data.total_uts }}</td>
                    <td class="text-center font-bold">{{ (data.total_uts / data.mapel.length).toFixed(2) }}</td>
                    <td class="text-center font-bold">{{ data.ranking }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </el-card>
    </div>
</template>
  
<script>
  import { data, event } from 'jquery';
import { head } from 'lodash';
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
              this.filter.id_kelas = this.storeFilters?.id_kelas ? this.storeFilters?.id_kelas : this.user.id_kelas?? Object.values(options)[0]?.value
            })
        },
      getData(){
        this.loading = true
        this.$http.get('mapel/nilai/rekapitulasi',{
          params: {
            id_semester: this.filter.id_semester,
            id_kelas: this.filter.id_kelas
          }
        }).then(result => {
          this.loading = false
          this.dataNilai = result.data
          setTimeout(() => {
            this.getFreezeHeader()
          }, 300)
        })
      },
      downloadLedger(){
        this.openLink(this.$siteUrl + `mapel/nilai/download_ledger?id_semester=${this.filter.id_semester}&id_kelas=${this.filter.id_kelas}`)
      },
      // downloadRaport(){
      //   this.openLink(this.$siteUrl + `mapel/nilai/download_raport?id_semester=${this.filter.id_semester}&id_kelas=${this.filter.id_kelas}`)
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

        this.openLink(this.$siteUrl + 'download_zip?' + this.objectToQueryParams({files:this.files}), '_blank');
        return
        try {
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
          const result = await this.$http.get('mapel/nilai/download_raport', {
            params: {
              id_semester: this.filter.id_semester,
              id_kelas: this.filter.id_kelas,
              id_santri: id,
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
      getFreezeHeader(){
        // return;
        console.log('freeze-header')
        let tBase = jquery('#table-base')
        let tFreezeContainer = jquery('#freeze-container')
        tFreezeContainer.empty() 
        // console.log(tBase, tFreezeContainer)

        let tBodyFreeze = tBase.clone(true)
        this.removeColumnByClass(tBodyFreeze, [], 'fixed-col')
        tBodyFreeze.attr('id', 'table-freeze-body')
        tBodyFreeze.appendTo(tFreezeContainer)
        tBodyFreeze.css({position:'absolute'})

        let tHeadFreeze = tBase.clone(true).find('tbody').remove().end()
        tHeadFreeze.attr('id', 'table-freeze-head')
        tHeadFreeze.appendTo(tFreezeContainer) 
        tHeadFreeze.css({position:'absolute'})

        let tHeadBodyFreeze = tBodyFreeze.clone().find('tbody').remove().end()
        tHeadBodyFreeze.attr('id', 'table-freeze-head-body')
        tHeadBodyFreeze.appendTo(tFreezeContainer) 
        tHeadBodyFreeze.css({position:'absolute'})
        // console.log(tBodyFreeze)
        // console.log(tHeadFreeze.width(), tBase.width())

        let thBase = tBase.find('th')
        let trBaseHead = tBase.find('thead tr')
        let trBaseBody = tBase.find('tbody tr')
        let thBodyFreeze = tBodyFreeze.find('th')
        
        let thFreeze = tHeadFreeze.find('th')
        let thHeadBodyFreeze = tHeadBodyFreeze.find('th')
        // // console.log(tHeadFreeze, tBase, tFreeze, thFreeze)
        let trBodyFreezeHead = tBodyFreeze.find('thead tr')
        let trBodyFreezeBody = tBodyFreeze.find('tbody tr')

        let keys = Object.keys(thFreeze)
        
        for (let i = 0; i < keys.length; i++) {
          const key = keys[i]
          let elBase = jquery(thBase[key])
          let elFreeze = jquery(thFreeze[key])
          let elBodyFreeze = jquery(thBodyFreeze[key])
          let elHeadBodyFreeze = jquery(thHeadBodyFreeze[key])
          // console.log(elBase, elFreeze)
          // continue;
          try {
            console.log(elBase.width(), elFreeze.width())
            elFreeze.width(elBase.width())
            elBodyFreeze.width(elBase.width())
            elHeadBodyFreeze.width(elBase.width())
            
            // jquery(elFreeze).width(elBase.width)
        //     elFreeze.style.width = elBase.outerWidth + 'px'
        //     // let baseF = Math.ceil(elFreeze.width)
        //     // elBase.width(baseF)
        //     console.log(elBase.width, elFreeze.width)
          } catch(err) {
            // console.log(err)
          }
        }

        trBodyFreezeHead.each((index, tr) => {
          let trF = jquery(jquery(tr).find('th')[0])
          let trB = jquery(jquery(trBaseHead[index]).find('th')[0])
          trF.height(trB.height()) 
        })

        trBodyFreezeBody.each((index, tr) => {
          let trF = jquery(jquery(tr).find('td')[0])
          let trB = jquery(jquery(trBaseBody[index]).find('td')[0])
          // console.log(trF, trB)
          trF.height(trB.height())
          // console.log(trF.height(), trB.height())
        })
          // var targetOffset = tBase[0].getBoundingClientRect(); // relative to viewport
          // tFreeze.css({
          //   left: targetOffset.left + 'px'
          // });
        this.addStyletoHeader()
        
      },
      addStyletoHeader(){
        let tHeadFreeze = jquery('#table-freeze-head')
        let tHeadBodyFreeze = jquery('#table-freeze-head-body')
        let tBase = jquery('#table-base')
        let tBodyFreeze = jquery('#table-freeze-body')
        tBodyFreeze.css({
          zIndex: 9997,
          width: 'auto',
          height: 'auto',
          // height: tBase.height() + 'px',
        })
        tHeadFreeze.css({
          zIndex: 9998,
          top: (this.scrollY - 210) + 'px',
          width: tBase.width() + 'px',
          opacity: this.scrollY < this.showHidden ? 0 : 1,
          visibility: this.scrollY < this.showHidden ? 'hidden' : 'visible',
        })
        tHeadBodyFreeze.css({
          zIndex: 9999,
          top: (this.scrollY - 210) + 'px',
          width: tBodyFreeze.width() + 'px',
          height: tHeadFreeze.height() + 'px',
          opacity: this.scrollY < this.showHidden ? 0 : 1,
          visibility: this.scrollY < this.showHidden ? 'hidden' : 'visible',
        })
      },
    },
    created: function() {
      this.getInitial()
      // console.log(this.$router);
    },
    mounted(){
      window.addEventListener('scroll', () => {
        this.scrollY = window.scrollY
        this.addStyletoHeader()
        // console.log(this.scrollY)
      })
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
  
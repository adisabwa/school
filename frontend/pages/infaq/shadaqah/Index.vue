<template>
  <div id="infaq" class="pt-[50px] translate-y-[-10px] px-0">
    <FilterAnggota v-if="user.role != 'user'" 
      v-model:id-anggota="idAnggota" @change="submittedData"/>
    <lazismu v-model:show="showLazismu"/>
    <teleport to="body">
      <div class="fixed z-[99] scale-x-[1.1] top-[70px] left-[20px]
          opacity-[0.4]
          w-[45px] h-[45px]
            active:scale-90 active:opacity-[0.8]
          p-4
          rounded-full bg-teal-100
          flex flex-col items-center justify-center
          text-center"
        @click="showLazismu = true">
        <img :src="$baseUrl+'assets/images/logo-lazismu.png'" height="35px"
          class="mb-4"/>
        <div class="absolute bottom-0
          bg-teal-100 rounded-[15px] px-2 py-1 w-[70px]
          font-semibold text-[12px] leading-[1]">Rek. Infaq</div>
      </div>
    </teleport>
    <el-card class="relative overflow-hidden
        bg-gradient-to-tr from-white/[0.8] from-40% to-teal-200/[0.7] rounded-[10px]
      z-[0]
        font-montserrat
      mb-3 p-0" 
      header-class="px-6 pt-6 pb-2 text-[15px] font-montserrat font-bold text-center"
      body-class="py-4 px-0">
      <template #header>
        <span>Data Infaq Anda</span>
        <img :src="infaq.image" height="90px" width="90px"
            class="absolute z-[-1] top-[-10px] right-[-15px]
              opacity-[0.5]"/>
      </template>
      <div class="px-8"
        v-if="['user','super-admin'].includes(user.role)">
        <el-button class="rounded-full w-full
          font-montserrat
          mb-4
          bg-teal-700
          text-white
          active:scale-90"
          @click="showAdd = true; dataId = -1">
          <icons icon="mdi:plus" />Tambah Data
        </el-button>
      </div>
      
      <ListData ref="infaqListData"
        class="[--text-color:theme(colors.teal.900)]
          [--bg-color:theme(colors.teal.50)]
          [--border-color:theme(colors.teal.400)]
          [--bg-button-color:theme(colors.teal.100)]
          [--button-color:theme(colors.teal.200)]
          max-h-[70vh]
        "
        :id-anggota="idAnggota"
        href="infaq/shadaqah"
        href-delete="infaq/shadaqah/delete"
        @edit-data="(({id}) => {
          dataId = id
          showAdd = true
        })">
        <template #subtitle="{ data }">
          {{ dateDayIndo(data.tanggal)}}
        </template>
        <template #title="{ data }">
          <div class="text-[20px]">
            {{ data.tipe == '0' ? 'Tanpa Nominal' : toIDR(data.jumlah) }}
          </div>
        </template>
        <template #content="{ data }">
          {{ data.keterangan }}
        </template>
      </ListData>
    </el-card>
    <el-dialog v-model="showAdd" draggable
      :append-to-body="true"
      class="w-fit max-w-[90%] py-3
        bg-gradient-to-tr from-white from-50% to-teal-100"
      header-class="font-bold text-[16px]"
      body-class="">
      <template #header>
        <div>Data Shadaqah</div>
      </template>
      <form-comp ref="formInfaq"
        class=""
        :key="'form-shadaqah-'+formKey"
        :fields="fields" 
        v-model:id="dataId"
        v-model:form-value="formValue" 
        href="infaq/shadaqah/store"
        href-get="infaq/shadaqah/get"
        :show-columns="[...['tipe','tanggal'],
          ...(formValue.tipe == '1' ? ['jumlah','keterangan'] : [])]"
        @saved="submittedData" 
        @error="saving=false"
        size="large"
        :show-submit="false"
        label-position="top"
        :show-required-text="false">
      </form-comp>  
      <template #footer>
        <div class="dialog-footer">
          <el-button @click="showAdd = false">Batal</el-button>
          <el-button type="primary" @click="$refs.formInfaq.submitForm()"
            class="bg-teal-700">
            Simpan
          </el-button>
        </div>
      </template>
    </el-dialog>
    <el-card class="bg-white/[0.9] rounded-[10px] mb-3 p-0"
      body-class="py-3 px-5"
      header="Statistik Sadaqah"
      header-class="py-3 font-bold text-[18px] text-center" >
    <chart ref="infaqChartData" 
      :href="href"
      :add-options="{
        scales: {
          y: {
            title:{
              display:true, 
              text: (chartType=='dashboard' ? 'Nominal Infaq' : 'Jumlah Infaq'),
            },
            ticks: {
              font: {
                size: 10
              },
              stepSize:(chartType=='dashboard' ? 100000 : 1),
              callback: function(value) {
                // Custom formatting: add a dollar sign
                let val = ''
                if (value > 1000000)
                  val = Math.floor(value / 1000000) + ' Juta' 
                else if (value > 1000)
                  val = Math.floor(value / 1000) + ' Ribu' 
                else
                  val = Math.floor(value)
                return val;
              }
            }
          }
        },
        plugins:{
          tooltip: {
            callbacks: {
              label: (tooltipItem) => {
                const label = tooltipItem.dataset.label || '';
                const value = tooltipItem.raw || 0;
                return `${label}: ${toIDR(value)}`;
              },
            },
          }
        }
      }"
      :id-anggota="idAnggota"
        :key="'infaqChartData'+formKey">
        <template #filter="{filter}">
          <el-select size="small" v-model="chartType" placeholder="Jenis Grafik"
            @change="formKey++; $refs.infaqChartData.getChart()">
            <el-option value="dashboard" label="Nominal Infaq" />
            <el-option value="dashboard_count" label="Jumlah Infaq" />
          </el-select>
        </template>
      </chart>
    </el-card>
  </div>
</template>
  
  <script>
  import { mapState } from 'pinia';
  import FilterAnggota from '@/pages/components/FilterAnggota.vue';
  import ListData from '@/pages/components/ListData.vue';
  import Chart from '@/pages/components/DataChart.vue'
  import Lazismu from '@/pages/infaq/LazisPage.vue'
  import { topMenu } from '@/helpers/menus.js'
  
  export default {
    name: "infaq",
    components: {
      Chart,
      ListData,
      FilterAnggota,
      Lazismu,
    },
    data: function() {
      return {
        chartType:'dashboard',
        showLazismu:false,
        loading: false,
        showAdd: false,
        tipeInfaq:'0',
        idAnggota:'',
        formKey:1,
        dataId:-1,
        lastData:{
          tanggal:'',
          surat_mulai:'',
          surat_selesai:'',
          nama_surat_mulai:'',
          nama_surat_selesai:'',
          ayat_mulai:'',
          ayat_selesai:'',
        },
        fields:{
          tipe:'',
          tanggal:'',
          jumlah:'',
          keterangan:'',
        },
        formValue:{},
        showCreate:false,
        success:false,
        saving:false,
        infaq: topMenu.infaqShadaqah,
      };
    },
    watch: {
     
    },  
    computed: {
      ...mapState({
        user: 'loggedUser',
        anggotas:'data/anggotas'
      }),
      href(){
        return "infaq/shadaqah/" + this.chartType
      }
      
    },
    methods: {
      getInitial: async function() {
        this.loading = true;
        // await this.$http.get('/infaq/shadaqah/get_last')
        //   .then(result => {
        //     var res = result.data;
        //     this.lastData = this.fillAndAddObjectValue(this.lastData, res)
        //   });
        
        await this.$http.get('/kolom/preparation?table=mu_infaq_shadaqah&grouping=0&input=0')
          .then(result => {
            var res = result.data;
            this.dataId = -1
            this.fields = this.fillAndAddObjectValue(this.fields, res)
            this.fields.tanggal.default = this.dateNow()
            this.fields.id_anggota.default = useAuthStore()?.loggedUser?.id_anggota
            this.formKey++
            this.loading = false
          });
        // this.datas = [];
        // await this.getData();
      },
      submittedData(){
        this.saving = false;
        this.showCreate = false
        this.success = true
        this.showAdd = false;
        setTimeout(this.updateChart(), 1000)
      },
      updateChart(){
        console.log('chart')
        this.$refs.infaqListData?.getData?.()
        this.$refs.infaqChartData?.getChart?.()

      }
    },
    created: function() {
      this.idAnggota = useAuthStore()?.loggedUser?.id_anggota
      this.getInitial()
      this.updateChart()
      // console.log(this.$router);
    },
    mounted: function() {
      
    },
  }
  </script>
  
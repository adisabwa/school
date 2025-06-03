<template>
  <div id="persyarikatan" class="pt-[50px] translate-y-[-10px] px-0">
    <FilterAnggota v-if="user.role != 'user'" 
      v-model:id-anggota="idAnggota" @change="submittedData"/>
    <el-card class="relative overflow-hidden
        bg-gradient-to-tr from-white/[0.8] from-40% to-indigo-200/[0.7] rounded-[10px]
      z-[0]
        font-montserrat
      mb-3 p-0" 
      header-class="px-6 pt-6 pb-2 text-[15px] font-montserrat font-bold text-center"
      body-class="py-4 px-0">
      <template #header>
        <span>Kegiatan Persyarikatan</span>
        <img :src="persyarikatan.image" height="90px" width="90px"
            class="absolute z-[-1] top-[-10px] right-[-15px]
              opacity-[0.5]"/>
      </template>
      <div class="px-8"
        v-if="['user','super-admin'].includes(user.role)">
        <el-button class="rounded-full w-full
          font-montserrat
          mb-4
          bg-indigo-700
          text-white
          active:scale-90"
          @click="showAdd = true; dataId = -1;
          this.getInitial()">
          <icons icon="mdi:plus" />Tambah Data
        </el-button>
      </div>
      
      <ListData ref="persyarikatanListData"
        class="[--text-color:theme(colors.indigo.900)]
          [--bg-color:theme(colors.indigo.50)]
          [--border-color:theme(colors.indigo.400)]
          [--bg-button-color:theme(colors.indigo.100)]
          [--button-color:theme(colors.indigo.200)]
          max-h-[70vh]
        "
        :id-anggota="idAnggota"
        href="persyarikatan"
        href-delete="persyarikatan/delete"
        @edit-data="(({id}) => {
          dataId = id
          showAdd = true
        })">
        <template #subtitle="{ data }">
          {{ dateDayIndo(data.tanggal)}}
        </template>
        <template #title="{ data }">
          <div class="text-[16px] ">
            {{ ucFirst(data.kegiatan) }}
          </div>
          <div class="text-[13px]">
            {{ ucFirst(data.lokasi) }}
          </div>
        </template>
        <template #content="{ data }">
          <div class="text-[12px]">
            Diselenggarakan oleh {{ ucFirst(data.penyelenggara) }}
          </div>
          <div class="text-[12px]">
            Materi : {{ ucFirst(data.isi) }}
          </div>
        </template>
      </ListData>
    </el-card>
    <el-dialog v-model="showAdd" draggable
      :append-to-body="true"
      class="w-fit max-w-[90%] py-3
        bg-gradient-to-tr from-white from-50% to-indigo-100"
      header-class="font-bold text-[16px]"
      body-class="">
      <template #header>
        <div>Data </div>
      </template>
      <form-comp ref="formKajian"
        class="min-w-[280px]"
        :key="'form-shadaqah-'+formKey"
        :fields="fields" 
        v-model:id="dataId"
        v-model:form-value="formValue" 
        href="persyarikatan/store"
        href-get="persyarikatan/get"
        :pass-columns="['id_anggota']"
        @saved="submittedData();updateChart();" 
        @error="saving=false"
        size="large"
        :show-submit="false"
        label-position="top"
        :show-required-text="false">
      </form-comp>  
      <template #footer>
        <div class="dialog-footer">
          <el-button @click="showAdd = false">Batal</el-button>
          <el-button type="primary" @click="$refs.formKajian.submitForm()"
            class="bg-indigo-700">
            Simpan
          </el-button>
        </div>
      </template>
    </el-dialog>
    <el-card class="bg-white/[0.9] rounded-[10px] mb-3 p-0"
      body-class="py-3 px-5"
      header="Statistik Sadaqah"
      header-class="py-3 font-bold text-[18px] text-center" >
    <chart ref="persyarikatanChartData" 
      href="persyarikatan/dashboard"
      :add-options="{
        scales: {
          y: {
            title:{
              display:true, 
              text: 'Jumlah Kegiatan',
            },
            ticks: {
              font: {
                size: 10
              },
              stepSize:1,
            }
          }
        },
      }"
      :id-anggota="idAnggota"
        :key="'persyarikatanChartData'+formKey">
      </chart>
    </el-card>
  </div>
</template>
  
  <script>
  import { mapState } from 'pinia';
  import FilterAnggota from '@/pages/components/FilterAnggota.vue';
  import ListData from '@/pages/components/ListData.vue';
  import Chart from '@/pages/components/DataChart.vue'
  import { organizationMenu } from '@/helpers/menus.js'
  
  export default {
    name: "persyarikatan",
    components: {
      Chart,
      ListData,
      FilterAnggota,
    },
    data: function() {
      return {
        loading: false,
        showAdd: false,
        tipeInfaq:'0',
        idAnggota:'-1',
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
        },
        formValue:{},
        showCreate:false,
        success:false,
        saving:false,
        persyarikatan: organizationMenu.persyarikatan,
      };
    },
    watch: {
      showAdd(val){
        if (val) {
          // console.log(val, this.idAnggota)
          this.$nextTick(() => {
            this.$refs.formKajian.changeData({
              field:'id_anggota', 
              value:this.idAnggota
            })
          })
        }
      }
    },  
    computed: {
      ...mapState({
        user: 'loggedUser',
        anggotas:'data/anggotas'
      }),
      
    },
    methods: {
      getInitial: async function() {
        let vm = this
        this.loading = true;
        // await this.$http.get('/persyarikatan/get_last')
        //   .then(result => {
        //     var res = result.data;
        //     this.lastData = this.fillAndAddObjectValue(this.lastData, res)
        //   });
        
        await this.$http.get('/kolom/preparation?table=mu_kegiatan_persyarikatan&grouping=0&input=0')
          .then(result => {
            var res = result.data;
            this.dataId = -1
            this.fields = this.fillAndAddObjectValue(this.fields, res)
            this.fields.tanggal.default = this.dateNow()
            // console.log(this.idAnggota)
            this.fields.id_anggota.default = this.idAnggota
            // console.log(this.fields)
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
        // setTimeout(this.updateChart(), 1000)
      },
      updateChart(){
        console.log('chart')
        this.$refs.persyarikatanListData?.getData?.(true)
        this.$refs.persyarikatanChartData?.getChart?.()

      }
    },
    created: function() {
      
    },
    mounted: function() {
      this.idAnggota = useAuthStore()?.loggedUser?.id_anggota
      this.getInitial()
    },
  }
  </script>
  
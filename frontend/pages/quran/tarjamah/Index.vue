<template>
  <div id="quran" class="pt-[50px]">
    <FilterAnggota v-if="user.role != 'user'" 
      v-model:id-anggota="idAnggota"/>
    <el-card v-if="['user','super-admin'].includes(user.role)"
      class="relative overflow-hidden
       bg-gradient-to-tr from-white/[0.8] from-50% to-sky-200/[0.7] rounded-[10px]
      z-[0] font-montserrat text-[14px] leading-[1.3]
      mb-3 p-0" 
      body-class="py-4 px-6">
      <div class="z-[10] text-gray-500">Terakhir Membaca : </div>
      <template v-if="!isEmpty(lastData?.tanggal)">
        <div class="z-[10] text-[24px] font-bold">{{ lastData.nama_surat_selesai }} ({{ lastData.surat_selesai }}) : {{ lastData.ayat_selesai }}
        </div>
        <div class="z-[10] text-gray-500 mt-1"><b>{{ dateDayIndo(lastData.tanggal) }}</b></div>
      </template>
      <template v-else>
        <div class="z-[10] text-2xl font-bold mb-3">
          Belum ada data
        </div>
      </template>
    
      <img :src="quran.image" height="90px" width="90px"
          class="absolute z-[0] top-[-10px] right-[-15px]
            opacity-[0.5]"/>
    </el-card>
    <form-quran v-if="['user','super-admin'].includes(user.role)"
      :id-anggota="idAnggota"
      v-model:show="showCreate"
      v-model:id="dataId"
      href="quran/tarjamah/store"
      href-get="quran/tarjamah/get"
      table="mu_quran_tarjamah"
      @saved="$refs.statisticDataQuran.updateChart()">
      <template #header>
       Setoran Hafalan Hari Ini
      </template>
    </form-quran>
    <statistic-data ref="statisticDataQuran"
      class="[--text-color:theme(colors.sky.900)]
        [--bg-color:theme(colors.sky.50)]
        [--border-color:theme(colors.sky.400)]
        [--bg-button-color:theme(colors.sky.100)]
        [--button-color:theme(colors.sky.200)]"
      :id-anggota="idAnggota"
      href-dashboard="quran/hafal/dashboard"
      href="quran/hafal"
      href-delete="quran/hafal/delete"
      @edit-data="(({id}) => {
        console.log('edit data', id)
        dataId = id;
        showCreate = true;
      })"
      >
      <template #header>
        <div>Data Baca Tarjamah Qur'an</div>
      </template>
    </statistic-data>
  </div>
</template>

<script>
import { mapState } from 'pinia';
import FilterAnggota from '../../components/FilterAnggota.vue';
import FormQuran from '@/pages/quran/components/FormQuran.vue';
import StatisticData from '@/pages/quran/components/StatisticData.vue';
import { topMenu } from '@/helpers/menus.js'

export default {
  name: "quran",
  components: {
    FilterAnggota,
    FormQuran,
    StatisticData,
  },
  data: function() {
    return {
      loading: false,
      formKey:1,
      idAnggota:null,
      lastData:{
        tanggal:'',
        surat_mulai:'',
        surat_selesai:'',
        nama_surat_mulai:'',
        nama_surat_selesai:'',
        ayat_mulai:'',
        ayat_selesai:'',
      },
      showCreate:false,
      dataId:-1,
      quran: topMenu.quranTarjamah,
    };
  },
  watch: {
   
  },  
  computed: {
    ...mapState(useAuthStore, {
      user: 'loggedUser',
      anggotas:'data/anggotas'
    }),
    
  },
  methods: {
    getInitial: async function() {
        this.loading = true;
        this.resetObjectValue(this.lastData)
        // console.log(this.lastData)
        await this.$http.get('/quran/tarjamah/get_last',{
          params:{
            id_anggota: this.idAnggota
          }
        })
          .then(result => {
            var res = result.data;
            this.fillAndAddObjectValue(this.lastData, res)
          });
      },
  },
  created: function() {
    useDataStore()?.getAllAnggotaInGroup()
    this.idAnggota = useAuthStore()?.loggedUser?.id_anggota
    this.getInitial();
  },
}
</script>

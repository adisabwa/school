<template>
  <div id="quran" class="pt-[50px]">
    <FilterAnggota v-if="user.role != 'user'" 
    v-model:id-anggota="idAnggota"/>
    <el-card v-if="['user','super-admin'].includes(user.role)"
      class="relative overflow-hidden
       bg-gradient-to-tr from-white/[0.8] from-50% to-yellow-200/[0.7] rounded-[10px]
      z-[0] font-montserrat
      mb-3 p-0" 
      body-class="relative p-0 ">
      <img :src="quran.image" height="90px" width="90px"
          class="absolute z-[0] top-[-10px] right-[-20px]
            opacity-[0.5]"/>
      <div class="relative w-fit overflow-x-scroll z-[10]
        snap-mandatory snap-x">
        <div class="w-[200%] flex h-[80px] py-4">
          <div class="snap-center w-full px-6 h-[90px] text-[14px] leading-[1.5]">
              <template v-if="!isEmpty(lastData.tanggal)">
              <div class="z-[10] text-gray-500">Setoran Terakhir : </div>
              <div class="z-[10] text-[22px] font-bold">{{ lastData.nama_surat_selesai }} ({{ lastData.surat_selesai }}) : {{ lastData.ayat_selesai }}
              </div>
              <div class="z-[10] text-gray-500 text-[15px]"><b>{{ dateDayIndo(lastData.tanggal) }}</b></div>
            </template>
            <template v-else>
              <div class="z-[10] text-2xl font-bold mb-3">
                Belum ada data
              </div>
            </template>
          </div>
          <div class="snap-center w-full px-6 h-[90px] overflow-scroll">
            <div class="z-[10] mb-2 text-md font-bold italic 
              py-2
              bg-white/[0.8]">
              <div >Total Hafalan :</div> 
              <ol class="m-0 pl-4 text-[90%] font-normal">
                <template v-for="data in juz">
                  <li class="py-[3px]">{{ data.nama_surat_mulai }} :{{ data.ayat_mulai }} ( Juz {{ data.juz_mulai }} ) - {{ data.nama_surat_selesai }} : {{ data.ayat_selesai }} ( Juz {{ data.juz_selesai }} )</li>
                </template>
              </ol>
            </div>
          </div>
        </div>
      </div>
    </el-card>
    <form-quran v-if="['user','super-admin'].includes(user.role)"
      :id-anggota="idAnggota"
      v-model:show="showCreate"
      v-model:id="dataId"
      href="quran/hafal/store"
      href-get="quran/hafal/get"
      table="mu_quran_hafal"
      @saved="$refs.statisticDataQuran.updateChart()">
      <template #header>
       Setoran Hafalan Hari Ini
      </template>
    </form-quran>
    <statistic-data ref="statisticDataQuran"
      class="[--text-color:theme(colors.orange.900)]
        [--bg-color:theme(colors.orange.50)]
        [--border-color:theme(colors.orange.400)]
        [--bg-button-color:theme(colors.orange.100)]
        [--button-color:theme(colors.orange.200)]"
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
        <div>Data Setoran Hafalan Qur'an</div>
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
      quran: topMenu.quranHafal,
    };
  },
  watch: {
   
  },  
  computed: {
    ...mapState(useAuthStore, {
      user: 'loggedUser',
    }),
    ...mapState(useDataStore, {
      anggotas:'data/anggotas'
    }),
  },
  methods: {
    getInitial: async function() {
        this.loading = true;
        this.resetObjectValue(this.lastData)
        await this.$http.get('/quran/hafal/get_last',{
          params:{
            id_anggota: this.idAnggota
          }
        })
          .then(result => {
            var res = result.data;
            this.fillAndAddObjectValue(this.lastData, res.last)
            this.juz = res.juz
          });
      },

  },
  created: function() {
    this.getInitial()
    useDataStore()?.getAllAnggotaInGroup()
    this.idAnggota = useAuthStore()?.loggedUser?.id_anggota
  },
}
</script>

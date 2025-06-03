<template>
  <div id="quran" class="pt-[50px]">
    <FilterAnggota v-if="user.role != 'user'" 
      v-model:id-anggota="idAnggota"/>
    <el-card v-if="['user','super-admin'].includes(user.role)"
       class="relative overflow-hidden
       bg-gradient-to-tr from-yellow-50/[0.8] from-50% to-lime-200/[0.7] rounded-[10px]
      z-[0] font-montserrat text-[13px]
      mb-3 p-0" 
      body-class="py-4 px-6">
      <div class="z-[10] text-gray-500">Terakhir Membaca : </div>
      <template v-if="!isEmpty(lastData.tanggal)">
        <div class="z-[10] text-[24px] font-bold">{{ lastData.nama_surat_selesai }} ({{ lastData.surat_selesai }}) : {{ lastData.ayat_selesai }}
        </div>
        <div class="z-[10] text-gray-500"><b>{{ dateDayIndo(lastData.tanggal) }}</b></div>
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
      href="quran/baca/store"
      href-get="quran/baca/get"
      table="mu_quran_baca"
      @saved="$refs.statisticDataQuran.updateChart()">
      <template #header>
        Bacaan Al-Qur'an Hari Ini
      </template>
    </form-quran>
    <statistic-data ref="statisticDataQuran"
      class="[--text-color:theme(colors.lime.900)]
        [--bg-color:theme(colors.lime.50)]
        [--border-color:theme(colors.lime.400)]
        [--bg-button-color:theme(colors.lime.100)]
        [--button-color:theme(colors.lime.200)]"
      :id-anggota="idAnggota"
      href-dashboard="quran/baca/dashboard"
      href="quran/baca"
      href-delete="quran/baca/delete"
      @edit-data="(({id}) => {
        console.log('edit data', id)
        dataId = id;
        showCreate = true;
      })"
      >
      <template #header>
        <div>Data Membaca Al-Qur'an</div>
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
    StatisticData,
    FilterAnggota,
    FormQuran,
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
      quran: topMenu.quranBaca,
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
      await this.$http.get('/quran/baca/get_last',{
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
    this.getInitial()
    useDataStore()?.getAllAnggotaInGroup()
    this.idAnggota = useAuthStore()?.loggedUser?.id_anggota
  },
}
</script>

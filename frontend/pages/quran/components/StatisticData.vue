<template>
  <div>
    <el-card class="bg-white/[0.9] rounded-[10px] mb-3 p-0"
      body-class="py-3 px-0"
      header-class="py-3 font-bold text-[16px]
        text-lime-800
        flex justify-between items-center" >
      <template #header v-if="$slots.header">
        <div>
          <slot name="header"/>
        </div>
        <div class="flex items-center gap-1
          [&_*]:text-[20px] text-emerald-900/[0.4]">
          <icons icon="fa6-solid:chart-line" 
            @click="showData='chart'"
            :class="`cursor-pointer ${showData == 'chart' ? 'text-emerald-900' : ''}`"/>
          <icons icon="material-symbols:view-list" 
            @click="showData='list'"
            :class="`cursor-pointer ${showData == 'list' ? 'text-emerald-900' : ''}`"/>
        </div>
      </template>
      <chart v-if="showData == 'chart'" 
        ref="quranChartData" 
        :href="hrefDashboard"
        :id-anggota="idAnggota"
        :add-options="{scales:{y:{
          title:{display:true, text:'Jumlah Ayat'},
          ticks: {stepSize:50},
        }}}"
        class="px-4"/>
      <ListData ref="quranListData"
        :id-anggota="idAnggota"
        :href="href"
        :href-delete="hrefDelete"
        v-if="showData =='list'"
        @edit-data="(({id}) => {
          $emit('editData', {id})
        })">
        <template #subtitle="{ data }">
          {{ dateDayIndo(data.tanggal)}}
        </template>
        <template #title="{ data }">
          <div class="mt-1 text-[16px] leading-[1]">Dari : 
						<span class="font-bold">
						{{ data.nama_surat_mulai }} ({{ data.surat_mulai }}) : {{ data.ayat_mulai }}
						</span>
					</div>
					<div class="text-[16px]">Sampai : 
						<span class="font-bold">
						{{ data.nama_surat_selesai }} ({{ data.surat_selesai }}) : {{ data.ayat_selesai }}
						</span>
					</div>
        </template>
      </ListData>
    </el-card>
  </div>
</template>

<script>
import ListData from '@/pages/components/ListData.vue';
import Chart from '@/pages/components/DataChart.vue'

export default {
  name: "statistic-quran",
  emits: ['editData'],
  components: {
    Chart,
    ListData,
  },
  props:{
    idAnggota:{type:[String, Number],default:'-1'},
    href:{type:String,default:''},
    hrefDashboard:{type:String,default:''},
    hrefDelete:{type:String,default:''},
  },
  data: function() {
    return {
      showData:'list',
    };
  },
  watch: {
    showData: {
      immediate: true,
      handler(val){
        this.updateChart()
      }
    },
    idAnggota: {
      immediate: true,
      handler(val){
        this.updateChart()
      }
    },
  },  
  computed: {
    
  },
  methods: {
    updateChart(){
      if (this.showData == 'chart') this.$refs.quranChartData?.getChart();
      if (this.showData == 'list') this.$refs.quranListData?.getData(true);
    }
  },
  created: function() {
    
  },
}
</script>

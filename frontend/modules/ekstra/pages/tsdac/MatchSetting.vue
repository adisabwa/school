<template>
  <div class="relative bg-white/70 sm:overflow-hidden w-screen h-screen flex flex-col">
    <Winner v-if="showWinner" :winner-name="winnerName" @click="showWinner = false;finishMatch();"/>
    <div class="bg-slate-700 flex justify-end h-12 p-2 gap-x-5 items-center">
      <img :src="$baseUrl + 'assets/images/ekstra/ts.png'" 
        class="absolute left-0 top-0 w-[70px] sm:w-[60px] z-[99]"
        @click="$router.replace({name:'tsdac-dashboard'})"/>
      <div class="shadow-md flex max-sm:absolute left-4 top-[47%] max-sm:-translate-y-1/2 z-[100] flex-col sm:flex-row">
        <div class="bg-cyan-600 text-white text-center font-bold text-[18px] py-1 px-3
            w-[80px] sm:w-[70px]">Partai</div>
        <div class="bg-white text-[var(--color-main-950)]
          w-[80px] sm:w-[60px]
          leading-[1] px-3 pb-1 flex justify-center text-[32px] font-bold relative">
          <icons v-if="currentMatch.partai > 1 && currentMatch.status == 'finish'"
            icon="ri:arrow-left-s-line" class="absolute pointer text-[20px] m-0 left-0 top-1/2 -translate-y-1/2
              opacity-80"
              @click="currentMatch.partai--"/>
          <span class="font-sans text-center"> 
            <!-- <span>{{ currentMatch.partai }}</span> -->
            <el-input v-model="currentMatch.partai" type="text" class="[&_*]:text-[var(--color-main-950)] text-[32px] [&_*]:font-bold w-full [&_*]:text-center
              focus:[outline:none] border-0 [&_*]:shadow-none border-b-2 border-solid border-[var(--color-main-950)]" />
          </span>
          <icons v-if="currentMatch.status == 'finish'"
            icon="ri:arrow-right-s-line" class="absolute pointer text-[20px] m-0 right-0 top-1/2 -translate-y-1/2
            opacity-80"
            @click="currentMatch.partai++"/>
        </div>
      </div>
      <div class="shadow-md flex gap-x-2 gap-y-1 max-sm:absolute right-4 top-[47%] max-sm:-translate-y-1/2 z-[100] flex-col sm:flex-row">
        <div v-for="b in range(3, 1)" 
          :class="[b != currentMatch.babak ? 'bg-[var(--color-main-900)] text-white' : 'bg-white text-[var(--color-main-900)]',
            'flex-none px-3 border border-solid border-[var(--color-main-700)] rounded-md py-1',
            'leading-[1.2] text-center text-[18px] font-bold mb-1 w-[80px] sm:w-[70px]']"
          @click="currentMatch.status == 'finish' ? currentMatch.babak = b : ''">
          Babak {{ b}}
        </div>
      </div>
      <div class="*:leading-none">
        <template v-if="currentMatch.status == 'finish'">
          <div 
            @click="startMatch"
            :class="['px-5 py-3 text-white font-bold text-center pointer hover:scale-90',
            rematch ? 'bg-sky-600' : 'bg-orange-600']">
            {{rematch ? 'Re -' : 'Start'}} Match
          </div>
        </template>
        <!-- <div v-if="currentMatch.status == ''"
          @click="startMatch"
          class="px-5 py-3  text-cyan-300 border border-solid border-cyan-300 rounded-sm
            font-bold text-center pointer hover:scale-90">
          Match Running
        </div> -->
        <el-button v-else class="px-5 py-3 h-fit
          border-[var(--color-main-600)]
          text-white text-[17px] font-bold leading-[1.3]
          bg-[var(--color-main-800)] pointer hover:scale-90"
          @click="finishMatch">
          Finish Match
        </el-button>
      </div>
    </div>
    <div class="h-full bg-gradient-to-b sm:bg-gradient-to-r 
      from-yellow-900 from-[43%] sm:from-[50%] to-[44%] sm:to-[50%] to-blue-950">
      <img :src="$baseUrl + 'assets/images/ekstra/winner.png'" width="50px"  v-if="currentMatch.status == 'finish'"
        :class="['absolute z-[99] translate-y-[7px]',
        mostJuri.biru == mostJuri.kuning ? 'hidden':
        mostJuri.biru > mostJuri.kuning ? '-right-2 max-sm:top-[calc(50%+30px)]' : '-left-2 -scale-x-100']"/>
      <div class="relative w-full h-[85%] sm:h-fit flex flex-col sm:flex-row items-center">
        <div class="w-full sm:w-fit sm:absolute left-1/2 sm:-translate-x-1/2 top-2 z-[99] h-[140px] sm:h-[50px]
          flex items-center justify-center">
          <img :src="$baseUrl + 'assets/images/ekstra/vs.png'" width="40px" 
            class="hover:scale-90"/>
        </div>
        <template v-for="i in ['kuning','biru']">
          <div :class="['md:w-1/2 h-1/2 flex flex-col gap-y-3 px-4 py-3',
            i == 'kuning' ? 'order-first' : 'order-last']">
            <div class="h-[40px] flex relative ">
              <div :class="[' w-[170px] text-white h-full',
                'text-center font-bold flex items-center justify-center',
                i == 'kuning' ? 'order-first bg-yellow-500 pl-1' : 'order-last bg-blue-500 pr-1']">Sudut {{ ucFirst(i) }}</div>
              <div class="w-full h-full">
                <floating-select v-model:value="currentMatch[i]" placeholder="Pilih Partisipan"  filterable clearable
                  size="large" class="h-full"
                  :options="dataPartisipans"/>
              </div>
            </div>
            <div class="grid grid-cols-2 font-bold text-center gap-2">
              <div v-for="no in range(4, 1)">
                <div :class="['w-full rounded-[5px]',
                  'border-2 border-solid leading-none py-2 text-white',
                isEmpty(currentMatch[`key_juri_${no}`]) ? 'bg-slate-700 !text-slate-400' :
                nilaiJuri?.[i]?.[no] != mostJuri[i] ? 'bg-red-700/40 border-red-600 hover:bg-red-600/50 ' : 
                    (i == 'kuning' ? 'bg-yellow-800/40 border-yellow-700 hover:bg-yellow-700/60' : 'bg-blue-800/20 border-blue-700/40 hover:bg-blue-700/40'),]">
                  <div :class="[' text-[14px]']">Juri {{ no }}</div>
                  <div class="text-[25px] mt-1 ">
                    {{ nilaiJuri?.[i]?.[no] ?? 0 }}
                  </div>
                </div>
                <div :class="['text-[12px] mt-1 leading-none cursor-pointer hover:scale-80',
                  isEmpty(currentMatch[`key_juri_${no}`]) ? '!text-slate-300 ' : '!text-white']"
                  @click="resetKey(no)">
                  {{ isEmpty(currentMatch[`key_juri_${no}`]) ? 'Juri Belum Masuk' : 'Juri sudah masuk'}}
                </div>
              </div>
            </div>
          </div>
        </template>
      </div>
      <div class="grid grid-cols-[1fr_100px_1fr] text-white font-bold text-center">
        <div v-if="currentMatch.status == 'run'" class=" col-span-3">
          <div class="border border-solid border-cyan-300 bg-cyan-800/30 rounded-md text-xl w-fit px-5 py-3 mx-auto">Pertandingan sedang Berjalan</div>
        </div>
        <template v-else>
          <div class="text-sm col-span-3">Last Match (Partai {{ oldMatch.partai }} - Babak {{ oldMatch.babak }})</div>
          <template v-if="oldMatch?.kuning < 0">
            <div class="col-start-1 col-span-3  py-0 pb-3">Belum Ada Pertandingan</div>
          </template>
          <template v-else>
            <div class="flex justify-end items-center col-start-1 pl-5">{{ oldMatch.nama_kuning }} ({{ oldMatch.kelas_kuning }})</div>
            <div>
              <img :src="$baseUrl + 'assets/images/ekstra/vs.png'" width="30px"/>
            </div>
            <div class="flex justify-start items-center pr-5">{{ oldMatch.nama_biru }} ({{ oldMatch.kelas_biru }})</div>
        </template>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
import Winner from './Winner.vue';

export default {
  name: "default",
  components: {
    Winner,
  },
  data: function() {
    return {
      dataPartisipans: [],  
      dataId:{
        biru:[],
        kuning:[],
      },
      currentMatch:{
        biru:'-1',
        kuning:'-1',
        partai:'0',
        babak:'0',
        status:'finish',
      },
      oldMatch:{},
      nilaiJuri:{
        biru:[],
        kuning:[],
      },
      intervalId:null,
      rematch:false,
      showWinner:false,
      winnerName:'',
    };
  },
  watch: {
    'currentMatch.partai'(val){
      this.getAllNilai()
    },
    'currentMatch.babak'(val){
      this.getAllNilai()
    },
    'currentMatch.biru'(val){
      this.getAllNilai()
    },
    'currentMatch.kuning'(val){
      this.getAllNilai()
    },
  },
  computed:{
    mostJuri(){
      let mostJuri = []
      for(let sisi of ['biru','kuning']){
        mostJuri[sisi] = this.getMostFrequent(Object.values(this.nilaiJuri[sisi]));
      }
      return mostJuri;
    },
  },
  methods:{
    getInitial(){
      this.$http.get('/ekstra/ts/tsdac/peserta/options')
        .then(res => {
          this.dataPartisipans = res.data;
        }).catch(err => {
          console.log(err)
        });
      this.$http.get('/ekstra/ts/tsdac/penilaian/get_current_match')
        .then(res => {
          let data = res.data
          this.currentMatch.biru = data?.biru ?? -1
          this.currentMatch.kuning = data?.kuning ?? -1
          this.currentMatch.partai = data?.partai ?? 0
          this.currentMatch.babak = data?.babak ?? 0
          this.currentMatch.status = data?.status ?? 'finish'
          this.currentMatch.nama_biru = data?.nama_biru ??''
          this.currentMatch.kelas_biru = data?.kelas_biru ??''
          this.currentMatch.nama_kuning = data?.nama_kuning ??''
          this.currentMatch.kelas_kuning = data?.kelas_kuning ??''
          this.currentMatch.key_juri_1 = data?.key_juri_1 ??''
          this.currentMatch.key_juri_2 = data?.key_juri_2 ??''
          this.currentMatch.key_juri_3 = data?.key_juri_3 ??''
          this.currentMatch.key_juri_4 = data?.key_juri_4 ??''
          this.oldMatch = JSON.parse(JSON.stringify(this.currentMatch))
          if (data?.status == 'run')
            this.intervalId = setInterval(this.getAllNilai, 2000);
          this.getAllNilai();
        }).catch(err => {
          console.log(err)
        });
    },
    startMatch(){
      let postData = window.jsonToFormData({
        biru:this.currentMatch.biru,
        kuning:this.currentMatch.kuning,
        partai:this.currentMatch.partai,
        babak:this.currentMatch.babak,
        status:'run',
        rematch:this.rematch,
      })
      this.$http.post('ekstra/ts/tsdac/penilaian/set_current_match', postData)
        .then(() => {
          this.getInitial()
          this.intervalId = setInterval(this.getAllNilai, 2000);
        })
        .catch(err => {
          console.log(err)
          this.$notify.error({
            title: 'Gagal',
            message: 'Peserta sudah bertanding pada partai dan babak ini',
            position: 'bottom-right'
          });
        });
    },
    finishMatch(){
      let postData = window.jsonToFormData({
        status:'finish',
      })
      this.$http.post('ekstra/ts/tsdac/penilaian/set_current_match', postData)
        .then(() => {
          clearInterval(this.intervalId);
          this.getInitial()
          this.$notify.success({
            title: 'Sukses',
            message: 'Match telah diakhiri',
            position: 'bottom-right'
          });
        }).catch(err => {
          console.log(err)
          this.$notify.error({
            title: 'Gagal',
            message: 'Terjadi kesalahan pada server',
            position: 'bottom-right'
          });
        });
    },
    getAllNilai(){
      this.$http.get('/ekstra/ts/tsdac/penilaian',{
        params:{
          where:{
            partai:this.currentMatch.partai,
            babak:this.currentMatch.babak,
          },
          in:{
            id_peserta:[this.currentMatch.biru,this.currentMatch.kuning],
          }
        }
      }).then(res => {
        let datas = res.data
        this.rematch = datas.length > 0
        console.log(this.range(4, 1), this.dataId)
        if (datas.length > 0)
          datas.forEach(d => {
            this.dataId[d.sisi][d.no_juri] = d.id ?? -1;
            this.nilaiJuri[d.sisi][d.no_juri] = d.total_nilai ?? 0;
          })
        else
          for(const sisi of ['kuning','biru']){
            for(const no_juri of this.range(4, 1)){
              // console.log(sisi, no_juri)
              this.dataId[sisi][no_juri] = -1;
              this.nilaiJuri[sisi][no_juri] = 0;
            }
          }
        this.checkWinner()
      }).catch(err => {
        console.log(err)
      });
    },
    checkWinner(){
      if (this.currentMatch.status == 'finish') {
        this.showWinner = false
        return
      }
      if (this.mostJuri['biru'] >= 210) {
        this.showWinner = true
        this.winnerName = `${this.currentMatch.nama_biru} (${this.currentMatch.kelas_biru})`
      } else if (this.mostJuri['kuning'] >= 210) {
        this.showWinner = true
        this.winnerName = `${this.currentMatch.nama_kuning} (${this.currentMatch.kelas_kuning})`
      }
    },
    resetKey(no){
      this.$confirm(`Apakah anda yakin untuk mereset akses juri no-${no}?`,
        'Konfirmasi',
        {
          confirmButtonText: 'OK',
          cancelButtonText: 'Batal',
          type: 'warning',
        })
        .then(() => {
          let key_juri = `key_juri_${no}`
          let set = {}
          set[key_juri] = ''
          set = window.jsonToFormData(set)
          this.$http.post('ekstra/ts/tsdac/penilaian/set_current_match', set)
            .then(res => {
              this.getInitial()
            })
        })
    }
  },
  mounted(){
    let query = this.$route.query;
  },
  beforeDestroy() {
    clearInterval(this.intervalId);
  },
  created(){
    this.getInitial()
  }
}
</script>
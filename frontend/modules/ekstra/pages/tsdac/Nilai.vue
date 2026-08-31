<template>
  <div class="relative bg-white/70 sm:overflow-hidden">
    <Winner v-if="showWinner" :winner-name="winnerName" @click="showWinner = false;"/>
    <img :src="$baseUrl + 'assets/images/ekstra/winner.png'" width="50px" v-if="currentMatch.status == 'finish'"
      :class="['absolute z-[99] -translate-y-[5px]',
      mostJuri.biru == mostJuri.kuning ? 'hidden':
      mostJuri.biru > mostJuri.kuning ? 'sm:right-[calc(50%-80px)] max-sm:left-3 -scale-x-100 max-sm:top-[calc(50%+40px)]' : 'sm:left-[calc(50%-80px)] max-sm:right-3']"/>
    <div class="w-screen sm:h-screen flex flex-col sm:flex-row">
      <div class="relative sm:w-[70px] flex-none bg-gradient-to-b sm:bg-gradient-to-r from-yellow-900 from-40% to-blue-950 to-60% px-[8px]
        flex flex-row sm:flex-col gap-x-6 gap-y-4 items-center">
        <div class="relative h-[62px] w-[75px] sm:w-full flex items-center justify-center" >
          <img :src="$baseUrl + 'assets/images/ekstra/vs.png'" width="40px" 
            class="absolute left-1/2 -translate-x-1/2 top-0 hover:scale-90"/>
        </div>
        <div class="sm:w-full shadow-md max-sm:order-first">
          <div class="bg-cyan-600 text-white text-center font-bold text-[12px] py-[2px]
             w-full">Partai</div>
          <div class="bg-[var(--color-main-900)] text-white border-2 border-solid border-[var(--color-main-600)]
            leading-[1] px-6 py-1 pb-2 flex justify-center text-[32px] font-bold relative">
            <span class="font-sans"> {{ currentMatch.partai }}</span>
          </div>
        </div>
        <div class="sm:w-full shadow-md max-sm:order-last">
          <div class="bg-cyan-600 text-white text-center font-bold text-[12px] py-[2px]
             w-full">Babak</div>
          <div class="bg-[var(--color-main-900)] text-white border-2 border-solid border-[var(--color-main-600)]
            leading-[1] px-6 py-1 pb-2 flex justify-center text-[32px] font-bold relative">
            <span class="font-sans"> {{ currentMatch.babak }}</span>
          </div>
        </div>
        <img :src="$baseUrl + 'assets/images/ekstra/ts.png'" 
          class="hover:scale-90 
            w-[70px] sm:w-[80px]"
          @click="$router.replace({name:'tsdac-dashboard'})"/>
      </div>
      <template v-for="i in ['kuning','biru']">
        <div :class="['sm:w-[calc(50%-58px)] flex flex-col gap-y-3 *:shadow-md p-2 pb-4',
          i == 'kuning' ? 'order-first bg-yellow-900' : 'order-last bg-blue-950']">
          <div :class="['h-[40px] flex relative border-0 border-b-2 border-solid',
            i == 'kuning' ? 'border-yellow-500' : 'border-blue-500']">
            <div :class="[' w-[120px] text-white h-full flex-none',
              'text-center font-bold flex items-center justify-center',
              i == 'kuning' ? 'order-first bg-yellow-500' : 'order-last bg-blue-500']">Sudut {{ ucFirst(i) }}</div>
            <div :class="['w-full h-full text-xl font-bold text-white px-4 ',
              i == 'kuning' ? 'text-left' : 'text-right']">
              {{ currentMatch[`nama_${i}`] }} ({{ currentMatch[`kelas_${i}`] }})
            </div>
          </div>
          <div class="h-[70px] flex font-bold text-center gap-x-1">
            <template v-for="no in range(4, 1)">
              <div :class="['w-full rounded-[5px]',
                'border-2 border-solid leading-none pt-1 pb-2 text-white',
                nilaiJuri[i][no] != mostJuri[i] ? 'bg-red-700/40 border-red-600 hover:bg-red-600/50 ' : 
                  (i == 'kuning' ? 'bg-yellow-800/40 border-yellow-700 hover:bg-yellow-700/60' : 'bg-blue-800/20 border-blue-700/40 hover:bg-blue-700/40'),
                no == noJuri ? 'scale-105 ring-4 ' + (i == 'kuning' ? 'ring-yellow-300/50' : 'ring-blue-300/50') : '']">
                <div :class="[' text-[14px]']">Juri {{ no }}</div>
                <div class="text-[24px] mt-1 ">
                  {{ nilaiJuri[i][no] ?? 0 }}
                </div>
              </div>
            </template>
          </div>
          <div class="h-[30px] relative">
            <div class="w-full bg-white flex *:text-[15px]">
              <span class="bg-[var(--color-main-600)] text-white px-2 py-1 w-[80px] flex-none">Daftar Nilai : </span>
              <span class="text-center h-full w-full overflow-hidden px-2 py-1
                flex justify-end
                bg-[linear-gradient(to_top,theme(colors.teal.200),white_30%,white_70%,theme(colors.teal.200))]
                ">
                <div class="whitespace-nowrap text-right min-h-[22px]">{{ nilaiList[i].join(' / ') }}</div>
              </span>
            </div>
          </div>
          <div class="grid grid-cols-4 grid-rows-3 h-full gap-2 *:text-white ">
            <el-button v-for="b in listButton"
              :disabled="currentMatch.status == 'finish'"
              :class="[`m-0 h-full
                text-[16px] font-bold
                border-[1px] border-b-4
                rounded-xl `,
                b.penalty ? 'bg-red-500 border-red-700 hover:bg-red-400' : (i == 'kuning' ? 'bg-yellow-950 border-yellow-600 hover:bg-yellow-500' 
                : 'bg-blue-950 border-blue-600 hover:bg-blue-400'),
                '[&.is-disabled]:bg-slate-600 [&.is-disabled]:text-slate-400']"
                @click="runButton(i, b)">
                <icons v-if="b.icon" :icon="b.icon" class="text-[28px]"/>
                {{b.value}}
              </el-button>
          </div>
        </div>
      </template>
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
        kuning:'-1',
        biru:'-1',
      },
      currentMatch:{
        partai:'0',
        babak:'0',
        status:'finish',
      },
      noJuri:'',
      nilaiJuri:{
        kuning:[],
        biru:[],
      },
      nilaiList:{
        kuning:[],
        biru:[],
      },
      listButton:[
        {
          value:'+10'
        },
        {
          value:'+20'
        },
        {
          value:'+30'
        },
        {
          value:'',
          icon:'mdi:reload',
        },
        {
          value:'+10+10'
        },
        {
          value:'+20+10'
        },
        {
          value:'+30+10'
        },
        {
          value:'',
          icon:'mdi:backspace-outline',
          action:'delete',
        },
        {
          value:'-10',
          penalty: true,
        },
        {
          value:'-20',
          penalty: true,
        },
        {
          value:'-30',
          penalty: true,
        },
        {
          value:'-40',
          penalty: true,
        },
      ],
      intervalId:null,
      showWinner:false,
      winnerName:'',
    };
  },
  watch: {
    currentMatch:{
      deep:true,
      handler(){
        this.getData()
      }
    }
  },
  computed:{
    mostJuri(){
      let mostJuri = []
      for(let sisi of ['kuning','biru']){
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
      this.getCurrent()
    },
    async getCurrent(){
      return new Promise((resolve, reject) => {
        this.$http.get('/ekstra/ts/tsdac/penilaian/get_current_match')
          .then(res => {
            let data = res.data
            this.currentMatch.kuning = data?.kuning ?? -1
            this.currentMatch.biru = data?.biru ?? -1
            this.currentMatch.partai = data?.partai ?? 0
            this.currentMatch.babak = data?.babak ?? 0
            this.currentMatch.status = data?.status ?? 'finish'
            this.currentMatch.nama_kuning = data?.nama_kuning ??''
            this.currentMatch.kelas_kuning = data?.kelas_kuning ??''
            this.currentMatch.nama_biru = data?.nama_biru ??''
            this.currentMatch.kelas_biru = data?.kelas_biru ??''
            this.currentMatch.key_juri_1 = data?.key_juri_1 ??''
            this.currentMatch.key_juri_2 = data?.key_juri_2 ??''
            this.currentMatch.key_juri_3 = data?.key_juri_3 ??''
            this.currentMatch.key_juri_4 = data?.key_juri_4 ??''
            this.checkKey()
            resolve(data)
          }).catch(err => {
            reject(err)
          });
        });
    },
    checkKey(){
      let key = this.getDataFromStorage('key_juri')
      if (key != this.currentMatch[`key_juri_${this.noJuri}`]) {
      // if (key == key) {
        this.$alert('Akses anda sudah dicabut admin ',
          'Peringatan',
          {
            confirmButtonText: 'OK',
            type: 'warning',
            showClose:false,
            showCancel:false,
            closeOnClickModal:false,
            closeOnPressEscape:false,
          })
          .then(() => {
            this.resetStorage('key_juri')
            this.$router.replace({name:'tsdac-dashboard'})
            clearInterval(this.intervalId);
          })
      }
    },
    getData(){
      for (let sisi of ['kuning','biru']){
        this.$http.get('/ekstra/ts/tsdac/penilaian/get_where',{
          params: {
            where:{
              sisi:sisi,
              partai:this.currentMatch.partai,
              babak:this.currentMatch.babak,
              id_peserta:this.currentMatch[sisi],
              no_juri:this.noJuri,
            }
          }
        }).then(res => {
          let data = res.data;
          this.dataId[sisi] = data?.id ?? -1;
          this.nilaiList[sisi] = data?.daftar_nilai ? data.daftar_nilai.split(',') : [];
          this.nilaiJuri[sisi][this.noJuri] = data.total_nilai
          this.getAllNilai();
        }).catch(err => {
          console.log(err)
        });
      }
    },
    runButton(i, b){
      if(b.action == 'delete'){
        this.nilaiList[i].pop();
      } else if (b.value != '') {
        this.nilaiList[i].push(b.value);
      }
      const sum = this.nilaiList[i].reduce((acc, curr) => {
        // console.log(curr, eval(curr))
        return acc + eval(curr)
      }, 0);
      let formData = window.jsonToFormData({
        id:this.dataId[i],
        daftar_nilai:this.nilaiList[i].join(','),
        total_nilai:sum,
      })
      this.$http.post('/ekstra/ts/tsdac/penilaian/store',formData).then(res => {
        this.dataId[i] = res.data.id;
        this.nilaiJuri[i][this.noJuri] = sum;
        this.getAllNilai()
      }).catch(err => {
        console.log(err)
      });
    },
    getAllNilai(){
      this.$http.get('/ekstra/ts/tsdac/penilaian',{
        params:{
          where:{
            partai:this.currentMatch.partai,
            babak:this.currentMatch.babak,
          }
        }
      }).then(res => {
        let datas = res.data
        datas.forEach(d => {
          if (d.no_juri != this.noJuri)
            this.nilaiJuri[d.sisi][d.no_juri] = d.total_nilai ?? 0;
        })
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
    }
  },
  mounted(){
    let query = this.$route.query;
    this.noJuri = parseInt(query?.juri ?? 1);
    this.intervalId = setInterval(() => {
      this.getCurrent()
      this.getAllNilai()
    }, 5000);
  },
  beforeDestroy() {
    clearInterval(this.intervalId);
  },
  created(){
    this.getInitial()
  }
}
</script>
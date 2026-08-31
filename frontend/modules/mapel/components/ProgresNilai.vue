<template>
  <div v-loading="loading">
    <div class="p-3 border border-solid border-slate-200 shadow-md">
      <h2 class="text-lg font-bold m-0 mb-1">Total Progress Pengisian Nilai {{ labelProgress }}</h2>
      <table class="w-full">
        <tbody>
          <tr v-for="u in rekapUjians">
            <td width="100">{{ getLabel(u) }} </td>
            <td width="10">:</td>
            <td>
              <el-progress :percentage="percentage[`persentase_${u}`]"
                class="w-full"
                :stroke-width="18"
                :show-text="true"
                :status="percentage[`persentase_${u}`] < 100 ? 'warning' : ''"
                striped
                >
                {{ percentage[`persentase_${u}`] }} % 
              </el-progress>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="m-2 mt-5 p-3 border border-solid border-slate-200 shadow-md text-md ">
      <div class="font-bold m-0 text-[16px]">Progress Pengisian Nilai per Mapel</div>
      <div :class="['grid gap-x-5 grid-cols-1 md:grid-cols-2']">
        <div  v-for="(mapel, key) in percentageMapel"
          :class="['my-1 mb-3 p-2 border border-solid border-slate-200']">
          <table class="w-full text-sm">
            <tbody>
              <tr>
                <td colspan="3" class="font-bold text-[15px]">
                  <div class="mb-2 bg-[var(--color-main-50)] px-2 py-1">
                    <el-button link type="primary" size="small" class="p-0 mr-1"
                      @click="nama_mapel = mapel.nama_mapel;
                        id_kelas = mapel.id_kelas;
                        loading = true;
                        $router.push({name:'nilai-mapel'})">
                        <icons v-if="role == 'guru'" icon="bxs:edit" class="m-0"/>
                        <icons v-else icon="mdi:eye" class="m-0"/>
                    </el-button>
                    {{ role == 'guru' ? 'Kelas ' + mapel.kelas + ' - ': ''}} {{ mapel.nama_mapel }} | {{ mapel.nama_guru }}
                  </div>
                </td>
              </tr>
              <tr v-for="u in showUjians(id_semester, mapel.kelas[0], mapel.is_praktik)"> 
                <td width="100" class="px-2">{{ getLabel(u) }}</td>
                <td width="10">:</td>
                <td>
                  <el-progress :percentage="mapel[`persentase_${u}`]"
                    class="w-full *:text-sm"
                    :stroke-width="18"
                    :show-text="true"
                    :status="mapel[`persentase_${u}`] < 100 ? 'warning' : ''"
                    striped
                    >
                    {{ mapel[`persentase_${u}`] }} %
                  </el-progress>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
  
<script>
  
  export default {
    name: "mapel",
    setup(){
      return {
        runFunction, isEmpty,
      }
    },
    props:{
      role:{
        type:String,
        default:'admin',
      },
      id_semester:{
        type:String,
        default:'',
      },
      id_kelas:{
        type:String,
        default:'',
      },
      tingkat:{
        type:String,
        default:'',
      },
      labelProgress:{
        type:String,
        default:'Mapel',
      },
    },
    emits: ['update:role', 'update:id_semester', 'update:id_kelas'],
    components: {
      
    },
    data: function() {
      return {
        initial:true,
        loading: false,
        params:{
          where:[],
        },
        editId:-1,
        ids:[],
        formKey:0,
        nama_mapel:'',
        percentage:{
          persentase_nilai_harian:0,
          persentase_uts:0,
          persentase_um:0,
          persentase_uas:0,
        },
        percentageMapel:[],
        getLabel: function(u) {
          switch (u) {
            case 'nilai_harian':
              return 'Nilai Harian'
            case 'uts':
              return 'UTS'
            case 'um':
              return 'UM'
            case 'uas':
              return 'UAS'
            default:
              break;
          }
        }
        // role:'guru',
      };
    },
    watch: {
      id_semester(val){
        this.getData()
      },
      id_kelas(val){
        this.getData()
      },
    },  
    computed: {
      showLabel(){
        return this.$windowWidth.value > 800
      },
      rekapUjians(){
        let ujians = []
        this.percentageMapel.forEach(m => {
          let ujiansMapel = this.showUjians(this.id_semester, m.kelas[0])
          ujians = [...new Set([...ujians, ...ujiansMapel])]
        })
        return ujians
      }
    },
    methods: {
      async getData(){
        this.percentageMapel = []
        this.percentagePengasuhan = []
        await this.$http.get('nilai/get_progres',{
          params: {
            id_semester: this.id_semester,
            id_kelas: this.id_kelas,
            id_guru: this.role == 'guru' ? this.user.id_guru : null,
          }
        }).then(result => {
          this.loading = false
          let res = result.data
          this.percentageMapel = res
        })
          this.countPercentage()
      },
      showUjians(semester = '', tingkat = '', is_praktik = false){
        if (is_praktik == '1') return ['uas']
        if (!tingkat)
          return ['nilai_harian', 'uts','uas', 'um']
        else if ((semester % 2) == 0)
          if (tingkat == '3')
            return ['nilai_harian', 'uas', 'um']
          else if (tingkat == '6')
            return ['nilai_harian', 'uas']
          else
            return ['nilai_harian', 'uts','uas']
        else
          return ['nilai_harian', 'uts','uas']
      },
      countPercentage(){
        let count = {
          nilai_harian:0,
          uts:0,
          um:0,
          uas:0,
        }
        this.percentage.persentase_nilai_harian = 0
        this.percentage.persentase_uts = 0
        this.percentage.persentase_um = 0
        this.percentage.persentase_uas = 0

        this.percentageMapel.forEach(m => {
          let ujians = this.showUjians(this.id_semester, m.kelas[0], m.is_praktik)
          ujians.forEach(u => {
            m[`persentase_${u}`] = Math.floor(m[`persentase_${u}`] * 100)
            this.percentage[`persentase_${u}`] += m[`persentase_${u}`]
            count[u]++
          })
        })

        this.percentage.persentase_nilai_harian = Math.floor(this.percentage.persentase_nilai_harian / count.nilai_harian)
        this.percentage.persentase_uts = Math.floor(this.percentage.persentase_uts / count.uts)
        this.percentage.persentase_um = Math.floor(this.percentage.persentase_um / count.um)
        this.percentage.persentase_uas = Math.floor(this.percentage.persentase_uas / count.uas)
        console.log(this.percentage)
      }
    },
    mounted(){
      this.getData()
    },
  }
  </script>
  
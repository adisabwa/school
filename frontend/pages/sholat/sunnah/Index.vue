<style lang="postcss" scoped>
.el-input-number {
  :deep(.el-input__wrapper){
    @apply px-1 shadow-none
      border border-solid border-emerald-700;
  }
  :deep([role="button"]) {
    @apply shadow-none w-fit px-1 border-0;
  }
  :deep(.el-input__inner) {
    @apply w-[20px] text-left pl-2;
  }
}
</style>
<template>
  <div id="sholat" class="pt-[50px] sm:pt-5">
    <FilterAnggota v-if="user.role != 'user'" 
      v-model:id-anggota="idAnggota" @change="reloadData"/>
    
      <el-card v-show="['user','super-admin'].includes(user.role)"
      class="rounded-[10px]
      bg-gradient-to-tr from-white/[0.8] from-30% to-rose-200/[0.7] 
      mb-3 p-0"
      body-class="relative p-0"
      header-class="relative p-0">
      <template #header>
        <img :src="sholat.image" height="90px" width="90px"
            class="absolute z-[0] top-[0px] right-[-20px]
              opacity-[0.5]"/>
        <DragScroll id="header-scroll" ref="headerScroll" 
          :syncWith="syncWith?.header" class="relative px-0 py-4 font-bold text-[18px] overflow-x-scroll" >
          <div v-if="editTanggal" class="text-center">
            <date-wheel-picker
              ref="editTanggal"
              id="editTanggal"
              class="w-fit mx-auto"
              v-model:value="tanggal"
              value-format="YYYY-MM-DD"
              format="DD MMMM YYYY"
              clearable 
              size="large"
              @change="editTanggal = false;
                setTanggalInitial();
                setDataInitiall();"
            />
          </div>
          <div v-else class="w-[300%] flex">
            <div v-for="(t, key) in tanggals" 
              :id="'header'+key"
              class="snap-center w-full px-4 text-center">
              <div @click="changeTanggal">
                <span 
                  >{{ dateDayIndo(t) }}</span>
              </div>
            </div>
          </div>
        </DragScroll>
        <icons @click="scrollHeader(-1)"
          class="m-0 text-[35px] pointer z-[999]
            absolute top-1/2 -translate-y-1/2 left-5"
          icon="iconamoon:arrow-left-2-bold"/>
        <icons @click="scrollHeader(1)"
          class="m-0 text-[35px] pointer z-[999]
            absolute top-1/2 -translate-y-1/2 right-5" 
          icon="iconamoon:arrow-right-2-bold"/>
      </template>
      <div class="mt-1 text-center active:scale-90 cursor-pointer"
        @click="collapseInput = !collapseInput">
        <icons v-if="collapseInput" icon="fe:arrow-down" class="scale-x-[1.5] text-rose-900/[0.4]"/>
        <icons v-else icon="fe:arrow-up" class="scale-x-[1.5] text-rose-900/[0.4]"/>
      </div>
      <DragScroll id="body-scroll" ref="bodyScroll"
        :syncWith="syncWith?.body" :class="[collapseInput ? 'max-h-0 py-0' : 'max-h-screen pt-0 pb-6',
        `relative px-0 w-full
        animate
        flex    
        overflow-x-scroll`]"
        @scrollEnd="handleAfterScroll">
        <template v-for="(_data, ind) in datas">
          <el-container :id="'body'+ind" class="font-montserrat shrink-0 snap-center
            px-7 w-full
            relative
            grid sm:grid-cols-[repeat(auto-fit,minmax(300px,1fr))]"
            v-loading="loadings[ind]">
            <template v-for="(sholat, key) in _data">
              <div :class="`${ sholat.do ? 'bg-emerald-200 text-emerald-900  [&_*]:bg-emerald-200 [&_*]:text-emerald-900 ' : 'bg-white text-gray-400 [&_*]:bg-white [&_*]:text-gray-400 active:scale-90' }
                pt-4 pb-3 mb-4 px-5 mx-auto
                rounded-[15px] w-[-webkit-fill-available]
                shadow-md
                relative flex gap-x-2 items-center
                animate [--duration:0.5s]
                cursor-pointer`">
                <div class="w-full">
                  <div class="text-[18px] font-semibold w-full
                    flex gap-2 items-center"
                      @click="sholat.do = !sholat.do;
                      sholat.edit = false;
                      saveData(ind, key)">
                    <icons v-if="sholat.do"
                      class="m-0 text-[30px]"
                      icon="material-symbols:check-circle-outline"/>
                    <div v-else
                      class="w-[20px] h-[20px] rounded-full shrink-0
                      border border-solid border-gray-400 leading-0
                      "/>
                    <div class="leading-[1.2]"
                      >{{ ucFirst(sholat.nama_sholat) }}
                    </div>
                  </div>
                  <div v-if="sholat.do"
                    class="ml-10 font-bold leading-[1]
                      text-sm w-fit">
                    <div v-if="!sholat.edit"
                      @click="sholat.edit = true"
                      class="flex items-center translate-y-[-3px]">
                      <span class="mr-1 text-[13px] translate-y-[1px]"> {{ sholat.rakaat }} Rakaat</span>
                      <icons 
                        icon="flowbite:edit-solid" class="text-[20px] mr-1"/>
                    </div>
                  </div>
                </div>
                  <div v-if="sholat.edit"
                    class="flex flex-col items-center grow-0
                      rounded-[5px] leading-[1]"> 
                      <el-input-number v-model="sholat.rakaat" controls-position="right"
                        class="h-full border-0 w-[83px]"
                        :min="sholat.min" step="2">
                        <template #decrease-icon>
                          <icons icon="mdi:minus" class="text-sm"/>
                        </template>
                        <template #increase-icon>
                          <icons icon="mdi:plus" class="text-sm"/>
                        </template>
                        <template #prefix>
                          <icons icon="mdi:check" class="text-[16px] m-0
                            px-1 active:scale-75"
                            @click="saveData(ind, key); sholat.edit = false"/>  
                        </template>
                      </el-input-number> 
                      <div class="text-[12px] mt-1 font-semibold">Raka'at</div>     
                  </div>                  
              </div>
            </template>
            <div class="text-center text-slate-400
                py-2 mb-5 px-5 mx-auto
                rounded-[15px] min-w-[240px] max-w-[300px]
                shadow-md
                relative flex items-center justify-center
                animate [--duration:0.5s]
                cursor-pointer
                active:scale-75"
                @click="showAdd = true">
                <icons icon="mdi:plus" class="text-[18px]" />
                <span class="font-semibold">Sholat Lainnya</span>
            </div>
          </el-container>
        </template>
      </DragScroll>
      <div class="flex items-center gap-x-4 px-4 my-2">
        <div class="h-[2px] bg-slate-400 w-full"/>
        <div class="text-md font-montserrat text-center min-w-1/2 shrink-0">Penilaian Sholat Sunnah</div>
        <div class="h-[2px] bg-slate-400 w-full"/>
      </div>
      <star class="mx-auto mt-0 mb-5 relative gap-x-2" width="40px" :count="getCountSunnah(totals[1])" />
    </el-card>
    <teleport to="body">
      <el-dialog v-model="showAdd"
        width="80%" 
        lock-scroll
        header-class="text-center text-[17px]"
        body-class="relative">
        <template #header>
          <div>Tambah Data Sholat Baru</div>
        </template>
        <el-input v-model="filterSunnah" size="large"
          class="[&_*]:text-center mb-2"
          placeholder="Cari Sholat Sunnah">
        </el-input>
        <div class="relative flex flex-col
          max-h-[250px] overflow-auto">
          <template v-for="so in sholatSunnah.filter((res) => {
              let q = filterSunnah.toLowerCase()
              return res.nama_sholat.toLowerCase().includes(q)
            })">
            <div :class="[`px-4 py-2 mb-2
              text-center text-[16px]
              border border-solid border-slate-200
              rounded-xl
              shadow-md
              active:scale-[0.8]`,
              so.id == sunnahAdd ? 'bg-emerald-200 text-emerald-900 font-bold' : '']"
              @click="sunnahAdd = so.id">
              {{ so.nama_sholat }}
            </div>
          </template>
        </div>
        <div>
          <div v-if="showInput">
            <el-input v-model="sunnahInput" size="large"
              class="[&_*]:text-center"
              placeholder="Sholat Sunnah Baru">
              <template #prepend>
                <icons icon="mdi:close" class="m-0 active:scale-75"
                  @click="showInput = false"/>
              </template>
              <template #append>
                <icons icon="mdi:check" class="m-0 active:scale-75"
                  @click="saveSunnah"/>
              </template>
            </el-input>
          </div>
          <div v-else
            class="px-4 py-2 mb-1
            text-center text-[16px] text-white
            bg-slate-700
            border border-solid border-slate-200
            rounded-xl
            shadow-md
            flex items-center justify-center
            active:scale-[0.8]"
            @click="showInput = true">
            <icons icon="mdi:plus" />
            Tambah Baru
          </div>
        </div>
        <template #footer>
            <el-button @click="showAdd = false">Batal</el-button>
            <el-button @click="addInput" type="success">Simpan</el-button>
        </template>
      </el-dialog>
    </teleport>
    <el-card class="bg-white/[0.9] rounded-[10px] mb-3 p-0"
      body-class="py-3 px-0"
      header-class="py-3 font-bold text-[16px]
        text-lime-800
        flex justify-between items-center" >
      <template #header>
        <div>Data Sholat Sunnah</div>
        <div class="flex items-center gap-1
          [&_*]:text-[20px] text-emerald-900/[0.4]">
          <icons icon="fa6-solid:chart-line" 
            @click="showData='chart'"
            :class="` ${showData == 'chart' ? 'text-emerald-900 pointer' : ''}`"/>
          <icons icon="material-symbols:view-list" 
            @click="showData='list'"
            :class="` ${showData == 'list' ? 'text-emerald-900 pointer' : ''}`"/>
        </div>
      </template>
      <chart ref="sunnahChartData" 
        href="sholat/sunnah/dashboard"
         :id-anggota="idAnggota"
          v-if="showData == 'chart'" 
        :add-options="{
          scales:{
            y:{
              title:{display:true, text:'Jml Rakaat'},
              ticks: {stepSize:2}
            }}}"
        class="px-4"/>
      <ListData ref="sunnahListData"
        class="[--text-color:theme(colors.rose.900)]
          [--bg-color:theme(colors.rose.50)]
          [--border-color:theme(colors.rose.400)]
          [--bg-button-color:theme(colors.rose.100)]
          [--button-color:theme(colors.rose.200)]
        "
        :id-anggota="idAnggota"
        href="sholat/sunnah"
        href-delete="sholat/sunnah/delete"
        :group-by="['tanggal']"
        v-if="showData =='list'"
        @edit-data="((res) => {
          tanggal = res.tanggal;
          setTanggalInitial();
          setDataInitiall();
        })">
        <template #title="{ data }">
          {{ dateDayIndo(data.tanggal)}}
        </template>
        <template #content="{ data }">
          <div class="flex items-center"
            @click="data.show_detail = !data.show_detail">
            <icons v-if="data.show_detail" icon="fe:arrow-down" class="text-[12px]"/>
            <icons v-else icon="fe:arrow-up" class="text-[12px]"/>
            Sholat Sunnah {{ data.total_rakaat }} Raka'at
            <star :count="getCountSunnah(data.total_rakaat)" width="12px"
              class="ml-3 gap-x-[2px]"/>
          </div>
          <ol v-show="data.show_detail"
            class="pl-[30px] italic mt-0 mb-1">
            <li v-for="(j) in data.daftar_sholat.split('/')"
              class="pl-1">
              {{ getLabelSholat(j) }}
            </li>
          </ol>
        </template>
      </ListData>
    </el-card>
  </div>
</template>

<script setup>
 import { getCountSunnah } from '@/helpers/sholat.js'
</script>

<script>
import { mapState } from 'pinia';
import Chart from '@/pages/components/DataChart.vue'
import FilterAnggota from '../../components/FilterAnggota.vue';
import ListData from '@/pages/components/ListData.vue';
import DragScroll from '@/components/DragScroll.vue';
import { topMenu } from '@/helpers/menus.js'

export default {
  name: "sholat",
  components: {
    Chart,
    ListData,
    FilterAnggota,
    DragScroll,
  },
  data: function() {
    return {
      loading: false,
      editTanggal:false,
      showAdd:false,
      dataId:-1,
      idAnggota:null,
      formKey:1,
      tanggals:[],
      tanggal:'',
      datas:[],
      loadings:[false, false, false],
      afterScroll: true,
      tipe:'',
      hideOnClick:true,
      sizeWindow:window.innerWidth,
      sholat: topMenu.sholatSunnah,
      showData:'list',
      filterSunnah:'',
      sholatSunnah:[],
      showInput:false,
      sunnahAdd:'',
      sunnahInput:'',
      collapseInput:false,
      syncWith:{},
    };
  },
  watch: {
    editTanggal(val){
      let vm = this
      setTimeout(() => {
        vm.setHeaderToCenter()
      }, 50);
    },
    afterScroll(val){
      console.log('after-scroll', val)
    },
  },  
  computed: {
    ...mapState(useAuthStore, {
      user: 'loggedUser',
    }),
    totals(){
      let total = []
      this.datas.forEach((data, i) => {
        // console.log(data)
        total[i]= Object.values(data)?.reduce((sum, item) => sum + (item.do ? item.rakaat : 0), 0)
      })
      // console.log(total)
      return total
    },
  },
  methods: {
    getLabelSholat(sholat){
      // console.log(sholat)
      let data = sholat.split('-')
      return `Sholat ${data[0]} - ${data[1]} Raka'at`
    },
    getData: async function(index) {
      let vm = this
      vm.loading = true;
      vm.loadings[index] = true;
      let tanggal = vm.tanggals[index]
      // setTimeout(() => {
        vm.$http.get('sholat/sunnah/get_initial', {
            params: {
              id_anggota:vm.idAnggota,
              tanggal:tanggal,
            }
          })
            .then(res => {
              let data = res.data
              this.datas[index] = data
              setTimeout(() => {
                vm.loading = false
                vm.loadings[index] = false;
              }, 300);
            })
            .catch(err => {
              console.log(err, index, vm.datas[index])
              vm.loadings[index] = false;
              vm.loading = false
              vm.$notify({
                type:'error',
                title: 'Gagal',
                message: 'Tidak dapat mengambil data',
                position: 'bottom-right',
              });
            })
      // }, 1000)
    },
    getAllSunnah(initial = true){
      this.$http.get('data/sholat-sunnah', {
            params: {
              where:{
                type:'additional'
              },
              limit:0,
            }
          })
            .then(res => {
              let data = res.data
              this.sholatSunnah = data
              if (!initial)
                this.sunnahAdd = data[data.length - 1]?.id
            })
    },
    saveSunnah()
    {
      let form = {
        id:-1,
        nama_sholat:this.sunnahInput,
      }
      var formData = window.jsonToFormData(form); 
      this.$http.post('data/sholat-sunnah/store', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      } )
        .then(result => {
          this.getAllSunnah(false);
          this.showInput = false
        })
        .catch(err => {
          
        });
    },
    addInput(){
      let ind = 1
      let kolom = this.sholatSunnah.findIndex(res => res.id == this.sunnahAdd)
      let sholat = this.sholatSunnah[kolom]
      console.log(sholat)
      this.datas[ind][sholat.id] = {
        "id_sholat": sholat.id,
        "nama_sholat": sholat.nama_sholat,
        "do": true,
        "edit": false,
        "rakaat": 2,
        "min": 2,
      }
      this.saveData(1, sholat.id)
      this.showAdd = false
    },
    saveData(ind, kolom){
      let data = this.datas[ind]
      console.log(data)
      let form = {
        id_anggota:this.idAnggota,
        tanggal:this.tanggals[ind],
        id_sholat:data[kolom].id_sholat,
        rakaat:data[kolom].rakaat,
        insert:data[kolom].do,
      }
      console.log(form)
      var formData = window.jsonToFormData(form); 
      this.$http.post('sholat/sunnah/store', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      } )
        .then(result => {
          // this.getData()
          this.updateChart()
        })
        .catch(err => {
          
        });
    },
    changeTanggal(){
      let vm = this
      vm.editTanggal = true;
      setTimeout(() => {
        vm.jquery('#editTanggal.el-input__inner')[0].focus();
      }, 300);
    },
    setHeaderToCenter(){
      let vm = this
      let body = vm.jquery('#body-scroll');
      let bcenter = vm.jquery('#body1');
      body[0].scrollLeft = bcenter[0].offsetLeft

      if (vm.editTanggal) return
      let header = vm.jquery('#header-scroll');
      let center = vm.jquery('#header1');
      header[0].scrollLeft = center[0].offsetLeft

      console.log('center', body[0].scrollLeft,  bcenter[0].offsetLeft, header[0].scrollLeft)
    },
    scrollHeader(course = -1){
      let duration = 0.7
      let vm = this
      // console.log(course)
      // vm.removeClass('#header-scroll','snap-x snap-mandatory')
      if (course == -1) {
        vm.scrollElement('#header-scroll','#header0',duration)
      } else {
        vm.scrollElement('#header-scroll','#header2',duration)
      }
      // setTimeout(() => {
      //   vm.addClass('#header-scroll','snap-x snap-mandatory')
      // }, duration * 1000 + 100);
    },
    handleAfterScroll(){
      // console.log('handle-after')
      let vm = this
      if (vm.editTanggal == true)
        return
      let header = this.jquery('#header-scroll')[0]
      let right = this.jquery('#header2')[0]
      // console.log(header.scrollLeft)
      if (header.scrollLeft == 0) {
        this.changeTanggalData(-1)
        this.setHeaderToCenter()
      } else if (header?.scrollLeft == right?.offsetLeft) {
        this.changeTanggalData(1)
        this.setHeaderToCenter()
      }
      // setTimeout(() => {
        vm.loadings[0] = vm.loadings[1] = vm.loadings[2] = false
      // }, 500);
    }, 
    setTanggalInitial(){
      this.tanggals = [
        this.addDay(this.tanggal, -1),
        this.tanggal,
        this.addDay(this.tanggal, 1),
      ]
    },
    setDataInitiall(){
      this.datas = [];
      for (let index = 0; index < this.tanggals.length; index++) {
        this.datas[index] = {}
        this.getData(index)
      }
    },
    changeTanggalData(course = -1){
      console.log('crs', course)
      let vm = this
      vm.tanggal = vm.addDay(vm.tanggal, course)
      for (let i = 0; i < vm.tanggals.length; i++) {
        vm.tanggals[i] = vm.addDay(vm.tanggals[i], course)
      }
      // unset(vm.datas[-1])
      // unset(vm.datas[3])
      let n_data = {}
      if (course == -1) {
        vm.datas.pop()
        vm.datas.unshift(n_data)
        vm.getData(1)
      } else {
        vm.datas.shift()
        vm.datas.push(n_data)
        vm.getData(2)
      }
      // this.setHeaderToCenter()
    },  
    reloadData(){
      this.setTanggalInitial()
      this.setDataInitiall()
      this.getAllSunnah()
      setTimeout(this.updateChart(), 500);
    },
    updateChart(){
      if (this.showData == 'chart') this.$refs.sunnahChartData?.getChart();
      if (this.showData == 'list') this.$refs.sunnahListData?.getData(true);
    }
  },
  created: function() {
    this.tanggal = this.dateNow()
    // this.tanggal = '2025-05-01'
    this.idAnggota = useAuthStore()?.loggedUser?.id_anggota
    useDataStore()?.getAllAnggotaInGroup()
    this.setTanggalInitial()
    this.setDataInitiall()
    this.getAllSunnah()
  },

  mounted: function() {
    let vm = this
    this.updateChart()
    this.setHeaderToCenter()
    // window.addEventListener('scroll', this.handleScroll);
    this.syncWith = {
      header:[this.$refs.bodyScroll],
      body:[this.$refs.headerScroll],
    }
    // window.addEventListener('scroll', this.handleScroll);
  },
  unmounted(){
    // window.removeEventListener('scroll', this.handleScroll);
  }
}
</script>

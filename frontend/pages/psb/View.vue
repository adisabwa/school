<template>
    <div id="psb" class="flex flex-col">
      <el-card class="mx-0 sm:mx-20 md:mx-auto mt-10 bg-white/[0.9]
        md:min-w-[900px]">
        <div class="text-center">
          <div class="text-center text-2xl font-500 mt-2">Pencarian Data Calon Santri</div>
          <el-input v-model="keyword" placeholder="Masukkan NISN Calon Santri / NIK Calon Wali Santri"
            class="w-full mt-4 mx-auto max-w-[500px]" @change="inputKeyword = keyword"
            size="large"/>
        </div>
        
        <loading v-show="loading" />
        <div v-show="!loading">
          <div v-if="empty"
            class="h-[150px] my-6 mx-7
              border border-solid border-gray-500
              flex flex-col items-center">
            <div class="text-center m-auto text-xls
              font-[500]"> - Data Tidak Ada - </div>
          </div>
  
          <div v-show="!empty" 
            class="mt-8">
            <div id="steps" class="w-full overflow-x-auto custom-scrollbar-horizontal">
              <el-steps class="py-4 mb-6 min-w-[900px] max-w-full" :active="active" 
                style="max-width: 600px"
                :space="500" align-center
                finish-status="success" process-status="finish">
                <el-step v-for="(el, ind) in groups" class="[&_*]:leading-[1.5] pointer"
                  :id="'step'+ind"
                  @click="active=ind">
                  <template #icon>
                    <icons :icon="el.group_icon" class="leading-0 text-[50px] m-0"/>
                  </template>
                  <template #title><span class="leading-[1]">{{ el.label }}</span></template>
                </el-step>
              </el-steps>
            </div>
            <div class="p-5 border-solid border-2 border-gray-200 max-w-[700px] mx-auto">
              <h2 class="text-center m-0">{{ !this.isEmpty(groups[active]) ? groups[active].label : '' }}</h2>
              <transition name="slide-in" mode="out-in"
                  enter-active-class="transition-all ease-in-out duration-[400ms]"
                  leave-active-class="transition-all ease-in-out duration-[400ms]"
                  enter-from-class="translate-x-full opacity-0"
                  enter-to-class="opacity-50"
                  leave-from-class="opacity-50"
                  leave-to-class="-translate-x-full opacity-0">
                <view-table ref="viewPsb"
                  :fields="fields" 
                  :key="'from'+active"
                  :keyword="inputKeyword"
                  :label-position="labelPosition"
                  class="mt-6"
                  label-width="250px"
                  href-get="/psb/search"
                  :search-columns="['nisn','ayah_nik','ibu_nik','wali_nik']"
                  @saved="submitted"  
                  @change-id="changeId"
                  v-model:empty="empty"
                  v-model:loading="loading"
                  v-model:id="dataId"
                  />
              </transition>
              <div class="flex justify-between mt-2 mb-2">
                <el-button size="large" type="primary" :disable="saving"
                  class="p-3"
                  :style="{visibility: active > 0 ? 'visible' : 'hidden'}"
                  @click="active--">
                  <icons icon="mdi:arrow-left-bold" class="m-0 mr-2"/>
                  Sebelumnya
                </el-button>
                <el-button size="large" type="success" :disable="saving"
                  class="p-3"
                  @click="active++"
                  :style="{visibility: active < fields.length ? 'visible' : 'hidden'}"
                  >
                  Selanjutnya
                  <icons icon="mdi:arrow-right-bold" class="m-0 ml-2"/>
                </el-button>
              </div>
            </div>
          </div>
          
          <div class="mt-4 mb-16 flex flex-row space-x-5 items-center justify-center">
            <el-button size="large"
              class="m-0 mt-2 text-white font-bold rounded-[10px]
                bg-[linear-gradient(to_right,theme(colors.cyan.500),theme(colors.emerald.500)_50%,theme(colors.red.500)_50%,theme(colors.yellow.500))]
                bg-[length:200%_200%] bg-left-bottom hover:bg-right-top py-2 px-4
                transition-all duration-500 ease-in-out"
              @click="$router.push({name:'psb-start'});">
                Halaman Utama
            </el-button>
            <el-button size="large"
              class="m-0 mt-2 text-white font-bold rounded-[10px]
                bg-[linear-gradient(to_right,theme(colors.cyan.500),theme(colors.emerald.500)_50%,theme(colors.red.500)_50%,theme(colors.yellow.500))]
                bg-[length:200%_200%] bg-left-bottom hover:bg-right-top py-2 px-4
                transition-all duration-500 ease-in-out"
              @click="$router.push({name:'psb-create'});">
                Daftar Baru
            </el-button>
            <el-button size="large"
              class="m-0 mt-2 text-white font-bold rounded-[10px]
                bg-[linear-gradient(to_right,theme(colors.cyan.500),theme(colors.emerald.500)_50%,theme(colors.red.500)_50%,theme(colors.yellow.500))]
                bg-[length:200%_200%] bg-left-bottom hover:bg-right-top py-2 px-4
                transition-all duration-500 ease-in-out"
              @click="$router.push({name:'psb-create',query:{id:dataId}});">
                <icons icon="mdi:edit"/>
                Edit Data
            </el-button>
          </div>
        </div>
      </el-card>
    </div>
  </template>
  
  <script>
  import { mapGetters } from 'vuex';
  import ViewTable from '@/components/ViewTable.vue'
  
  export default {
    name: "psb",
    components: {
      ViewTable,
    },
    data: function() {
      return {
        fields:[],
        groups:[],
        active:-1,
        dataId:'-1',
        saving:false,
        sizeWindow:'',
        inputKeyword:'',
        keyword:'',
        empty: true,
        loading:false,
      };
    },
    watch: {
      active(val){
        this.fields = this.groups[val].children
        this.scrollElement("#steps", "#step" + val) 
        this.scrollToElement("#psb")
      },
      inputKeyword(val){
        console.log('cek', val)
      }
    },  
    computed: {
      ...mapGetters({
        user: 'loggedUser',
      }),
      labelPosition(){
        return this.sizeWindow < 800 ? 'top' : 'left'
      }
    },
    methods: {
      getInitial: async function() {
        this.loading = true
        await this.$http.get('/kolom/preparation?table=da_psb&input=0')
          .then(result => {
            var res = result.data;
            this.groups = Object.values(res)
            this.active = 0
            this.loading = false
          });
      },
      submitted(psb){
        this.saving = false
        this.dataId = psb.id
        let a = this.active + 1;
        if (this.groups[a] == undefined) {
          this.$router.push({name:'psb-finish'})
        } else {
          this.active++
        }
      },
      changeId(id){
        this.dataId = id
      }
    },
    created: function() {
      this.getInitial();
      // console.log(this.$router);
    },
    mounted: function() {
      let vm = this
      vm.sizeWindow = window.innerWidth
      window.addEventListener('resize', () => {
        vm.sizeWindow = window.innerWidth
      });
      let filter = this.$store.getters.filter
      this.inputKeyword = this.isEmpty(filter.keyword) ? '' : filter.keyword
    },
    beforeRouteLeave(){
      console.log('leave')
      this.$store.dispatch('saveFilter', {ref:'keyword', data: ''})
    }
  }
  </script>
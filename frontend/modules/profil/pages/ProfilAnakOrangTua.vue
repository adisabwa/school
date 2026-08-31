<style lang="postcss" scoped>
  :deep(.button-menu) {
    @apply m-0 mt-2 text-white font-bold rounded-[10px]
      bg-[linear-gradient(to_right,theme(colors.cyan.500),theme(colors.emerald.500)_50%,theme(colors.red.500)_50%,theme(colors.yellow.500))]
      bg-[length:200%_200%] bg-left-bottom hover:bg-right-top py-2 px-4
      transition-all duration-500 ease-in-out
    !important;
  }
</style>
<template>
    <div id="santri" class="flex flex-col">
      <el-card class="mx-0 sm:mx-20 md:mx-auto bg-white/[0.9]
        md:min-w-[900px]">
        
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
              <h2 class="text-center m-0">{{ !isEmpty(groups[active]) ? groups[active].label : '' }}</h2>
              <transition name="slide-in" mode="out-in"
                  enter-active-class="transition-all ease-in-out duration-[400ms]"
                  leave-active-class="transition-all ease-in-out duration-[400ms]"
                  enter-from-class="translate-x-full opacity-0"
                  enter-to-class="opacity-50"
                  leave-from-class="opacity-50"
                  leave-to-class="-translate-x-full opacity-0">

                <view-table ref="viewData"
                  :fields="fields" 
                  :key="'from'+active"
                  :params="params"
                  :label-position="labelPosition"
                  class="mt-6"
                  label-width="250px"
                  href-get="/data/santri/get"
                  :set-status-text="setStatusText"
                  :set-status-type="setStatusType"
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
                  :style="{visibility: active < (groups.length - 1) ? 'visible' : 'hidden'}"
                  >
                  Selanjutnya
                  <icons icon="mdi:arrow-right-bold" class="m-0 ml-2"/>
                </el-button>
              </div>
            </div>
          </div>
          
          <!-- <div class="mt-4 mb-16 flex flex-row space-x-5 items-center justify-center">
            <el-button size="large"
              class="button-menu"
              @click="$router.push({name:'santri-start'});">
                Halaman Utama
            </el-button>
            <el-button size="large"
              class="button-menu"
              @click="$router.push({name:'santri-create'});">
                Daftar Baru
            </el-button>
            <el-button size="large" v-if="dataId > 0"
              class="button-menu"
              @click="$router.push({name:'santri-create',query:{id:dataId}});">
                Edit Data
            </el-button>
            <el-button size="large" v-if="dataId > 0"
              class="button-menu"
              @click="openLink($siteUrl+'/santri/admin/download/'+dataId)">
                Unduh Kartu Pendaftaran
            </el-button>
          </div> -->
        </div>
      </el-card>
    </div>
</template>

<script>
  import { setStatusText, setStatusType } from '@/modules/profil/helpers/santri'
  import { mapState } from 'pinia';
  import ViewTable from '@/components/form/ViewTable.vue'
  import { useAuthStore } from '@/config/stores/authStore'
  import { useDataStore } from '@/config/stores/dataStore'
  
  export default {
    name: "santri",
    setup(){
      return {
        setStatusText, setStatusType,
        isEmpty,
      }
    },
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
        params:{},
      };
    },
    watch: {
      active(val){
        this.fields = this.groups[val].children
        scrollElement("#steps", "#step" + val) 
        scrollToElement("#santri")
      },
      dataId(val){
        this.getParams()
      },
      inputKeyword(val){
        this.getParams()
      }
    },  
    computed: {
      ...mapState(useAuthStore, {
        user: 'loggedUser',
      }),
      labelPosition(){
        return this.$windowWidth.value < 800 ? 'top' : 'left'
      }
    },
    methods: {
      getParams(){
          this.params = {
            id:this.user.id_santri
          }
      },
      getInitial: async function() {
        this.loading = true
        await this.$http.get('/kolom/preparation?table=' + this.$prefixTable + '_santri&input=0&grouping=1')
          .then(result => {
            var res = result.data;
            this.groups = Object.values(res)
            this.active = 0
            this.loading = false
            this.getParams()
          });
      },
    },
    created: function() {
      this.getInitial();
      // console.log(this.$router);
    },
    beforeRouteLeave(){
      console.log('leave')
      useDataStore().setFilter({key:'keyword', val:''})
    }
  }
  </script>
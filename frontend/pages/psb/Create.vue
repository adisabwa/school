<template>
    <div id="psb" class="flex flex-col mx-0 sm:mx-20 md:mx-auto mt-10 bg-white/[0.9]
        md:min-w-[900px]">
      <loading class="mt-48"
        v-if="loading" />
      <el-card v-else class="">
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
        <div class="p-5 border-solid border-2 border-gray-200">
          <h2 class="text-center m-0">{{ groups[active].label}}</h2>
          <transition name="slide-in" mode="out-in"
              enter-active-class="transition-all ease-in-out duration-[400ms]"
              leave-active-class="transition-all ease-in-out duration-[400ms]"
              enter-from-class="translate-x-full opacity-0"
              enter-to-class="opacity-50"
              leave-from-class="opacity-50"
              leave-to-class="-translate-x-full opacity-0">
            <form-comp ref="formPsb"
              :fields="fields" 
              :key="'from'+active"
              :id="dataId"
              :label-position="labelPosition"
              class="mt-6"
              label-width="250px"
              href="/psb/store"
              href-get="/psb/get"
              @saved="submitted"  
              @error="saving=false"
              @change-id="changeId"
              :show-submit="false"
              ></form-comp>
            </transition>
        </div>
        <div class="flex justify-between mt-2 mb-2">
          <el-button size="large" type="primary" :disabled="saving"
            :style="{visibility: active > 0 ? 'visible' : 'hidden'}"
            @click="active--">Kembali</el-button>
          <el-button size="large" type="success" :disabled="saving"
            @click="saving=true; $refs.formPsb.submitForm(); "
            >Simpan {{ active < (groups.length - 1) ? 'dan Lanjut' : '' }}</el-button>
        </div>
        <div class="mb-16 flex justify-center space-x-5">
          <el-button size="large"
            class="m-0 mt-2 text-white font-bold rounded-[10px]
              bg-[linear-gradient(to_right,theme(colors.cyan.500),theme(colors.emerald.500)_50%,theme(colors.amber.500)_50%,theme(colors.yellow.500))]
              bg-[length:200%_200%] bg-left-bottom hover:bg-right-top 
              py-2 px-4 
              transition-all duration-500 ease-in-out"
            @click="$router.push({name:'psb-start'});">
              Halaman Utama
          </el-button>
          <el-button size="large"
            class="m-0 mt-2 text-white font-bold rounded-[10px]
              bg-[linear-gradient(to_right,theme(colors.cyan.500),theme(colors.emerald.500)_50%,theme(colors.amber.500)_50%,theme(colors.yellow.500))]
              bg-[length:200%_200%] bg-left-bottom hover:bg-right-top 
               py-2 px-4 
              transition-all duration-500 ease-in-out"
            @click="$router.push({name:'psb-view'});">
              Cek Data Pendaftaran
          </el-button>
        </div>
      </el-card>
    </div>
  </template>
  
  <script>
  import { mapGetters } from 'vuex';
  import Form from '@/components/Form.vue'
  
  export default {
    name: "psb",
    components: {
      'form-comp' : Form,
    },
    data: function() {
      return {
        fields:[],
        groups:[],
        keys:[],
        active:-1,
        loading:true,
        dataId:'-1',
        saving:false,
        sizeWindow:'',
      };
    },
    watch: {
      active(val){
        this.fields = this.groups[val].children
        this.scrollElement("#steps", "#step" + val) 
        this.scrollToElement("#main-layout")
      },
      groups(val){
        console.log(val, this.active)
      },
      dataId(val){
        console.log(val)
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
        this.loading = true;
        await this.$http.get('/kolom/preparation?table=da_psb')
          .then(result => {
            var res = result.data;
            this.groups = Object.values(res)
            this.active = 0
            this.loading = false
          });
      },
      submitted(psb){
        // this.saving = false
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
      this.dataId = this.coalesce([this.$route.query.id, -1]);
      // console.log(this.$router);
    },
    mounted: function() {
      let vm = this
      vm.sizeWindow = window.innerWidth
      window.addEventListener('resize', () => {
        vm.sizeWindow = window.innerWidth
      });
    },
    beforeRouteEnter(to, from, next) {
      // Redirect to another route when this page is accessed after a refresh
      next()
      return;
      if (from.path != '/') {
        next()
      } else {
        console.log(to, from, next)
        next(vm => {
          vm.$router.push({name:'psb-start'}); // Change '/login' to the desired route
        });
      }
    }
  }
  </script>
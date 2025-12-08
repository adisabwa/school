
<template>
	<div class="[--color-border:theme(colors.cyan.400)]">
		<div class="relative md:pt-32 pt-[80px] bg-white/[0.8] mx-auto w-[90vw] md:w-[1000px] min-h-[calc(100vh-200px)]">
        <div class="max-w-[95%] md:max-w-[80%] mx-auto relative h-auto w-full pt-3 pb-24 ">
        <div class=" mx-auto leading-[1.2]
          text-2xl text-center font-bold text-cyan-800">Sistem Informasi PPM Darul Arqam</div>
          <el-dialog v-model="showRole"
            append-to-body
            class="[&_*]:font-montserrat text-teal-800 w-[280px]">
            <template #header>
              <div>Masuk Sebagai</div>
            </template>
            <el-radio-group class="flex flex-col gap-2"
              v-model="selectedRole">
              <el-radio-button v-for="rl in roles"
                :value="rl" class="
                border border-solid border-teal-700/[0.5]
                text-teal-800 
                [&_*]:w-full w-full
                [&_*]:border-0">
                {{ ucFirst(rl) }}</el-radio-button>
            </el-radio-group>
            <template #footer>
              <div class="dialog-footer flex justify-between">
                <el-button @click="showRole = false">Batal</el-button>
                <el-button type="primary" @click="showRole = false;
                  authStore.changeRole({
                    app:'all',
                    role:selectedRole
                  })"
                  class="bg-teal-700 border-0">
                  Ubah
                </el-button>
              </div>
            </template>
          </el-dialog>
        <div v-if="!user.id"
          class="text-center mt-6 *:w-[250px]
            flex flex-col gap-3 items-center">
          <GoogleLogin :callback="doGLogin" prompt
            class="w-full" />
          <!-- <div class="text-cyan-700 text-md w-full
            cursor-pointer"
            @click="$router.push({name:'login'})"> 
            Login dengan Username dan Password
          </div> -->
        </div>
        <template v-else>
          <div class="mx-auto relative leading-[1.3]
            text-xl text-center font-bold text-cyan-800 mt-3 mb-5">
            Assalamualaikum,<br/>
            {{ user.nama }} <br/>
            <span class="flex items-center gap-1 justify-center cursor-pointer"
              @click="showRole = true; selectedRole = role">
              ( {{ ucFirst(role) }} )
              <icons icon="fe:arrow-down" class="text-[90%]" />
            </span>
            <div class="text-[15px] w-fit mx-auto mt-1
              md:absolute md:top-0 md:right-0
              flex items-center cursor-pointer
              text-white bg-cyan-700 px-4 py-1 font-semibold"
                @click="doLogout">
              <icons class="text-[17px]" icon="mdi:logout"/>
              Keluar
            </div>
          </div>
          <div class="  mx-auto mt-3">
            <el-input v-model="keyword" placeholder="Cari Aplikasi" 
              class="w-full *:text-[14px] *:px-1"
              :input-style="{
                padding:'10px 3px',
              }">
              <template #prefix>
                <icons icon="mdi:search"
                  class="text-[25px] px-1 m-0" />
              </template>
            </el-input>
          </div>
          <div class="flex flex-col gap-y-8 mt-5">
            <template v-for="menuFilter in menusFilter">
              <div v-if="menuFilter?.datas?.length > 0" class="w-full mx-auto">
                <div class="text-center bg-orange-100
                  text-cyan-800 font-bold md:text-xl
                  md:py-1 md:mb-3 py-[2px] mb-3 " >{{ menuFilter?.label }}</div>
                <div 
                  class="grid grid-cols-[repeat(auto-fit,_minmax(60px,_60px))] md:grid-cols-[repeat(auto-fit,_minmax(100px,_100px))]
                  gap-[calc(30px)] gap-y-[25px] justify-center
                  ">
                  <template v-for="(menu, ind) in menuFilter?.datas">
                    <div class="grid-item group/menu
                    [--duration:0.5s] relative">
                      <icons icon="ant-design:star-filled"
                        :class="[`absolute z-[999]
                          top-[-10px] right-[-20px]
                          md:text-[40px] text-[30px]
                          animate cursor-pointer hover:scale-[1.2]`,
                          menuFilter.fav ? 'text-yellow-500' : 'text-slate-300']"
                        @click="toggleFavorites(menu.app)"/>
                      <div class="relative animate pointer duration-500 group/app
                        hover:scale-90 " 
                        @click="goToRoute(menu)">
                        <div class="relative bg-transparent
                          rounded-full animate
                          flex items-center justify-center">
                          <div class="m-auto md:h-[150px] h-[100px] rounded-[10px] 
                            bg-contain bg-no-repeat
                            overflow-hidden
                            flex items-center justify-center align-middle"
                            :style="{maskImage:`url(${menu.image})`,
                            maskSize:'contain',
                            maskRepeat:'no-repeat',
                            backgroundImage:`url(${menu.image})`}">
                            <icons class="text-[#f3b76f] md:text-[90px] text-[60px]" :icon="menu.icon" />
                            <div class="absolute w-[100%] h-[200%] rotate-[-25deg] top-[-50%]
                              bg-gradient-to-r from-transparent via-white/[0.5] to-transparent
                              animate-[fly-in-absolute_5s_infinite_ease-in-out] [--from-left:-100%] [--left:350%] "
                              :style="{animationDelay: ind + 's !important'}"/>
                          </div>
                        </div>
                        <div class="animate
                          absolute md:top-[90px] top-[55px] left-1/2 -translate-x-1/2
                          md:font-bold md:px-3 py-1 px-2 rounded-[5px]
                          bg-gradient-to-l from-[var(--color-form)] from-[50%] to-[var(--color-to)] to-[50%]
                          bg-[length:200%_200%] bg-right-bottom 
                          group-hover/menu:bg-left-top
                          text-center mt-3 md:text-[15px] text-[13px] text-[var(--color-text)] leading-[1.2]
                          group-hover/menu:text-white"
                          :style="{
                            '--color-form':getColor(menu.color),
                            '--color-to':getColor(menu.darkColor),
                            '--color-text':getColor(menu.textColor),
                          }">{{ menu.label }}
                        </div>
                      </div>
                    </div>
                  </template>
                </div>
              </div>
            </template>
          </div>
        </template>
      </div>
		</div>
	</div>
</template>

<script setup>
  const authStore = useAuthStore()   
</script>

<script>
import Psb from '@/pages/psb/Start.vue'
import { topMenu } from '@/helpers/menus.js'
import { mapState } from 'pinia'

export default {
  name: "default",
  components: {
    Psb,
  },
  computed:{
    ...mapState(useAuthStore,{
      user:'loggedUser',
      role:'role',
      roles:'roles',
    }),
  },
  data: function() {
    return {
      topMenu:topMenu,
      keyword:null,
      recents:[],
      favorites:[],
      others:[],
      menusFilter:{
        fav: {
          label: 'Aplikasi Favorit',
          datas:[],
          fav:true,
        },
        // recent: {
        //   label: 'Recent Apps',
        //   datas:[],
        // },
        all: {
          label: 'Daftar Aplikasi',
          datas:[],
        },
      },
      showRole:false,
      selectedRole:'',
    };
  },
  watch:{
    keyword(val){
      this.getDataFilter()
    }
  },
  methods:{
    doGLogin(result) {
      this.loading = true;
      useAuthStore().gLogin({credential:result.credential}, true)
        .then(res => {
          this.loading = false;
          this.getDataFilter()
        }).catch(err => {
          this.loading = false;
          // console.log(err)
          const res = err.response;
          if (res.status == 401) {
            useDataStore().setFilter({key:'email', val:res?.data?.email})
            this.$router.push({name:'register'})
          } else {
            this.$notify.error({
              title: 'Gagal',
              message: 'Terjadi kesalahan pada server',
              position: 'bottom-right'
            });
          }
        });
    },
    doLogout: function() {
      useAuthStore().logout()
    },
    goToRoute(menu){
      // this.resetStorage('recent-apps')
      let app = menu.app
      // this.recents.unshift(app);
      this.saveToStorage('recent-apps', app)
      this.$router.push({name:menu.route[this.user.app_roles[menu.app] ?? this.user.app_roles.all]})
    },
    toggleFavorites(app){
      // console.log(this.favorites)
      if (this.favorites.includes(app)) {
        this.favorites.splice(this.favorites.findIndex(a => a == app), 1)
        this.removeFromStorage('favorite-apps', app)
      } else {
        this.favorites.unshift(app)
        this.saveToStorage('favorite-apps', app)
      }
      this.getDataFilter()
    },
    getDataFilter(){
      let q = this.keyword?.toLowerCase()
      let menus = this.topMenu.filter((d) => {
        // console.log(this.user)
        if (!this.user.app_roles)
          return false
        let role = this.user?.app_roles[d.app] ?? this.user.app_roles.all
        // console.log(app, role)
        if (d.route[role]) {
          return true
        } else {
          return false
        }
      })
      if (q) 
        menus = menus?.filter?.(d => {
            return d.label.toLowerCase().includes(q)
        })
      // this.menusFilter.recent.datas = []
      this.menusFilter.all.datas = []
      this.menusFilter.fav.datas = []
      let favApp = []
      // let recentApp = []
      // console.log(menus)
      menus.forEach(d => {
        if (this.favorites.includes(d.app)) {
          this.menusFilter.fav.datas.push(d)
          favApp.push(d.app)
        }

        // else if (this.recents.includes(d.app)) {
        //   this.menusFilter.recent.datas.push(d)
          // recentApp.push(d.app)
        // }
      })
      this.menusFilter.fav.datas.splice(6)
      // this.menusFilter.recent.datas.splice(6)
      this.menusFilter.all.datas = menus.filter(d => {
        return !favApp.includes(d.app) 
          // && !recentApp.includes(d.app)
      })
    }
  },
  mounted(){
    // this.recents = this.getDataFormStorage('recent-apps');
    // if (this.isEmpty(this.recents))
    //   this.recents = []
    this.favorites = this.getDataFormStorage('favorite-apps')
    if (this.isEmpty(this.favorites))
      this.favorites = []
    this.getDataFilter()
    // console.log(localStorage, 'recents', this.recents, this.favorites)
  }
	
}
</script>
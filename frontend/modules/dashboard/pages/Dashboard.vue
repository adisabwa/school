<style lang="postcss">
  .dropdown-user {
    @apply bg-[var(--color-main-600)] text-white !important;
  }
</style>
<template>
	<div class="mt-[20px] sm:mt-0 relative">
    <div class="sm:hidden absolute w-screen h-[200px]
      left-1/2 -translate-x-1/2 -top-[110px] z-[0]
      bg-[var(--color-main-700)]">
      <div class="w-full h-full opacity-20
        bg-[length:320px] bg-repeat bg-bottom"
        :style="{
          backgroundImage:`url('${$baseUrl}/assets/images/menu.png')`,
        }"/>
    </div>
    <div class="sm:hidden absolute w-full h-[70px] px-2 mt-0 z-[3]
      text-white leading-[1.3]">
      Assalamu'alaikum,<br/>
      <div class="text-xl font-semibold leading-none">{{ isEmpty(user.nama) ? 'Tamu' : user.nama }}</div>
      <div class="text-md font-semibold truncate">{{ user.unit_kerja }}</div>
      <div class="text-md leading-[1]">
        <div v-if="role == ''">
          <el-button class="bg-white text-[var(--color-main-700)] border-0 h-auto p-[7px] px-3 mt-1"
            @click="$router.push({name:'default'})">
            <span class="text-[13px] font-bold">MASUK</span>
          </el-button>
        </div>
        <span v-else class="el-dropdown-link text-white flex items-end gap-1 mt-1"  @click="showRole = true" >
          {{ getRoleLabel(role) }}
          <icons icon="fe:arrow-down" class="text-[90%]" />
        </span>
      </div>
      <el-dialog v-model="showRole"
        append-to-body
        class="[&_*]:font-montserrat text-[var(--color-main-800)] w-[280px]">
        <template #header>
          <div>Masuk Sebagai</div>
        </template>
        <el-radio-group class="flex flex-col gap-2"
          v-model="selectedRole">
          <el-radio-button v-for="rl in roles"
            :value="rl" class="
            border border-solid border-[var(--color-main-400)]
            text-[var(--color-main-800)] 
            [&_*]:w-full w-full
            [&_*]:border-0">
            {{ getRoleLabel(rl) }}</el-radio-button>
        </el-radio-group>
        <template #footer>
          <div class="dialog-footer flex justify-between">
            <el-button @click="showRole = false">Batal</el-button>
            <el-button type="primary" @click="showRole = false;
              changeRole({
                app:'all',
                role:selectedRole
              })"
              class="bg-[var(--color-main-700)] border-0">
              Ubah
            </el-button>
          </div>
        </template>
      </el-dialog>
    </div>
		<div id="management" class="flex flex-col justify-center mx-0 sm:mx-auto pt-20 md:pt-4 pb-20
      overflow-hidden">
      <div class="w-full mx-auto mt-5">
        <el-input v-model="keyword" placeholder="Cari Layanan" 
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
      <div class="flex flex-col gap-y-4 md:gap-y-8 mt-3">
        <template v-for="menuFilter in menusFilter">
          <div v-if="menuFilter?.datas?.length > 0" class="w-full mx-auto">
            <div class="text-center bg-orange-100
              text-cyan-800 font-bold md:text-xl
              md:py-1 md:mb-3 py-[2px] " >{{ menuFilter?.label }}</div>
            <div 
              class="grid grid-cols-[repeat(auto-fit,_minmax(60px,_1fr))] md:grid-cols-[repeat(auto-fit,_minmax(100px,_100px))]
              gap-[calc(20px)] gap-y-[35px] justify-center
            bg-white/[0.7] 
              w-full
              py-5 px-4
              ">
              <template v-for="(menu, ind) in menuFilter?.datas">
                <div class="grid-item group/menu
                [--duration:0.5s] relative"
                  :style="{
                    '--color-from':getColor(menu.color),
                    '--color-to':getColor(menu.darkColor),
                    '--color-text':getColor(menu.textColor),
                  }">
                  <icons icon="ant-design:star-filled"
                    :class="[`absolute z-[999]
                      top-[-5px] right-[-10px]
                      md:text-[30px] text-[20px]
                      animate cursor-pointer hover:scale-[1.2]`,
                      menuFilter.fav ? 'text-yellow-500' : 'text-slate-400']"
                    @click="toggleFavorites(menu.app)"/>
                  <!-- <icons icon="mdi:info"
                    :class="[`absolute z-[999]
                      top-[-5px] left-[0px]
                      md:text-[30px] text-[20px]
                      animate cursor-pointer hover:scale-[1.2]
                      text-[var(--color-to)]`]"
                    @click="toggleFavorites(menu.app)"/> -->
                  <div class="relative animate pointer duration-500 group/app
                    hover:scale-90 " 
                    @click="goToRoute(menu)">
                    <div class="relative bg-transparent
                      rounded-full animate
                      flex items-center justify-center">
                      <div class="m-auto md:h-[110px] h-[60px] rounded-[10px] 
                        bg-contain bg-no-repeat
                        overflow-hidden
                        flex items-center justify-center align-middle"
                        :style="{maskImage:`url(${menu.image})`,
                        maskSize:'contain',
                        maskRepeat:'no-repeat',
                        backgroundImage:`url(${menu.image})`}">
                        <icons class="text-[#f3b76f] md:text-[70px] text-[50px]" :icon="menu.icon" />
                        <div class="absolute w-[100%] h-[200%] rotate-[-25deg] top-[-50%]
                          bg-gradient-to-r from-transparent via-white/[0.5] to-transparent
                          animate-[fly-in-absolute_5s_infinite_ease-in-out] [--from-left:-100%] [--left:350%] "
                          :style="{animationDelay: ind + 's !important'}"/>
                      </div>
                    </div>
                    <div class="animate w-full
                      absolute md:top-[75px] top-[55px] left-1/2 -translate-x-1/2
                      md:font-bold md:px-3
                      text-center mt-1 md:text-[14px] text-[12px] leading-[1.2]">
                        <span class="text-[var(--color-text)] group-hover/menu:text-[var(--color-to)]">
                          {{ menu.label }}
                        </span>
                    </div>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </template>
      </div>
      <div v-if="canInstall && !isStandalone()"
        class="w-full text-center">
        <el-button class="
          font-montserrat
          shadow-md shadow-[var(--color-main-900)]
          bg-brand text-[var(--color-main-50)] mt-5
          px-4 py-5
          active:scale-95
          [&>*]:w-full" @click="installApp">
          Tambah ke Halaman Utama
        </el-button>
      </div>
    </div>
		<!-- <div class="translate-y-[-40px]">
			<div id="bottom" class="bg-cover bg-top bg-repeat
				h-[60px] min-w-[600px] w-full
				filter hue-rotate-[349deg] brightness-[.9]"
				:style="`background-image:url('${$baseUrl}assets/images/bottom.png')`"></div>
			
			<div class="absolute bg-[#f3b76f] h-[100px] w-full" />
		</div> -->
	</div>
</template>


<script>
import { mapState, mapActions } from 'pinia';
import { topMenu } from '@/helpers/menus.js'
import { getRoleLabel } from '@/modules/dashboard/helpers/functionHelper'

export default {
  name: "default",
  setup(){

    const { canInstall, promptInstall, isStandalone } = useA2HS();

    async function installApp() {
      const result = await promptInstall();
      if (result?.outcome === 'accepted') {
        console.log('User accepted the install prompt');
      } else {
        console.log('User dismissed the install prompt');
      }
    }
    const { getDataFromStorage, saveToStorage, removeFromStorage } = useStorage()
    const { openLink } = useBrowserActions()
    // console.log(openLink)
    return {
      isEmpty, ucFirst, 
      getRoleLabel, getDataFromStorage, saveToStorage, removeFromStorage, getColor, openLink,
    }
  },
  components: {
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
          label: 'Pilih Layanan',
          datas:[],
        },
      },
      showRole:false,
      selectedRole:'',
    };
  },
  computed:{
    ...mapState(useAuthStore,{
      user:'loggedUser',
      role:'role',
      roles:'roles',
    }),
  },
  watch:{
    keyword(val){
      this.getDataFilter()
    }
  },
	methods:{
    ...mapActions(useAuthStore,{
      changeRole: 'changeRole',
    }),
		doLogout: function() {
      useAuthStore().logout().then(() => {
        this.$router.replace({name:'default'})
      })
    },
    goToRoute(menu){
      // this.resetStorage('recent-apps')
      let app = menu.app
      // this.recents.unshift(app);
      this.saveToStorage('recent-apps', app)
      if (menu.defaultRole)
        this.changeRole({
          app: app,
          role: menu.defaultRole,
        })
        .then(res => {
          this.goToLink(menu)
        })
      else
        this.goToLink(menu)
    },
    goToLink(menu){
      if (menu.link)
        this.openLink(menu.link, '_blank')
      else 
        this.$router.push({name:menu?.route?.[this.user?.app_roles?.[menu?.app] ?? this.user?.app_roles?.all] ?? menu?.route?.all})
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
        if (d.guest) {
          return true
        }
        if (!this.user.app_roles)
          return false
        let role = this.user?.app_roles?.[d?.app] ?? this.user.app_roles.all
        // console.log(app, role)
        if (d.route[role] ?? d.route.all) {
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
        if (this.favorites.includes(d?.app)) {
          this.menusFilter.fav.datas.push(d)
          favApp.push(d?.app)
        }

        // else if (this.recents.includes(d.app)) {
        //   this.menusFilter.recent.datas.push(d)
          // recentApp.push(d.app)
        // }
      })
      this.menusFilter.fav.datas.splice(6)
      // this.menusFilter.recent.datas.splice(6)
      this.menusFilter.all.datas = menus.filter(d => {
        return !favApp.includes(d?.app) 
          // && !recentApp.includes(d.app)
      })
    },
	},
  mounted(){
    // this.recents = this.getDataFromStorage('recent-apps');
    // if (isEmpty(this.recents))
    //   this.recents = []
    this.favorites = this.getDataFromStorage('favorite-apps')
    if (isEmpty(this.favorites))
      this.favorites = []
    this.getDataFilter()
    this.selectedRole = this.role
    // if (this.role == '') 
    //   this.$router.replace({name:'default'})
    // console.log(localStorage, 'recents', this.recents, this.favorites)
  }
	
}
</script>
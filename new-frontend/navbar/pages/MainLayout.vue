<template>
  <div id="main-layout" class="bg-white 
    [--width-menu:230px]">
    <div class="z-[20] w-full">
      <transition name="slide-in" mode="out-in"
        enter-active-class="transition-all ease-in-out duration-500"
        leave-active-class="transition-all ease-in-out duration-500"
        enter-from-class="translate-x-[-100%]"
        enter-to-class="translate-x-0"
        leave-to-class="translate-x-[-100%]"
        leave-from-class="translate-x-0">
        <component :is="MenuComponent" :key="MenuComponent"
          @function="(func) => {
            this[func]?.();
          }"
          :active-menu="activeMenu" :menus="menus" @action="handleActionClick" @toggle="toggleMenu"/>
      </transition>
      <!-- <el-button v-if="route.name != 'default' "
        class="fixed z-[200] top-12 left-3
        rounded-full
        w-[40px] h-[40px] p-3
        sm:hidden flex justify-center items-center
        opacity-[0.7]
        bg-yellow-50/[0.7]"  
        @click="router.back()">
        <icons icon="mdi:arrow-left" class="m-0 text-2xl text-teal-700 font-bold"/>
      </el-button> -->
      <!-- <el-button class="fixed z-[200] bottom-7 right-7 rounded-full
        w-[70px] h-[70px] p-3
        sm:hidden flex justify-center items-center
        bg-yellow-50/[0.7]"  
        @click="toggleClass('#menu-vertical','-translate-x-full')">
        <icons icon="mdi:menu" class="m-0 text-4xl text-teal-700"/>
      </el-button> -->
    </div>
    <el-container>
      <!-- <el-footer height="auto" class="h-[20px] px-0 z-[99999] relative">
        <div class="overflow-hidden h-[45px] w-screen
          absolute bottom-0 left-1/2 -translate-x-1/2 -translate-y-[20px]">
          <div id="bottom" class="bg-cover bg-top bg-repeat 
            h-full min-w-[600px] w-full"
            :style="`background-image:url('${$baseUrl}assets/images/bottom.png')`">
          </div>
        </div>
        <div class="text-[12px] text-center h-full bg-gray-100 flex items-center justify-center gap-2">
          &copy; 2023 by <a href="https://codev-app.my.id/" target="_blank" class="no-underline text-green-900"> Codev-App</a>
        </div>
      </el-footer> -->
    </el-container>
  </div>
</template>
<script setup>
  // Tidak perlu import, tidak perlu mendaftarkan apa pun.
  // Vite & Vue otomatis mengenali formatDate di sini.
</script>
<script>
import { mapState } from 'pinia';
import VerticalMenu from './components/VerticalMenu.vue';
import HorizontalMenu from './components/HorizontalMenu.vue';
import { useAuthStore } from '@2/shared/config/stores/authStore'
import { useDataStore } from '@2/shared/config/stores/dataStore';
import MobileMenu from './components/MobileMenu.vue';
import { APP_ROUTES } from '@2/shared/router/manifest';

export default {
  name: 'default-layout',
  // 1. Definisikan setup untuk memanggil Composable
  setup() {
    const { useLocalStorage, saveToStorage } = useStorage();

    // Membuat state reaktif yang terhubung ke localStorage
    const menu = useLocalStorage('menu', 'admin');

    return {
      menu, // Sekarang bisa diakses via this.menu
      saveToStorage  // Sekarang bisa diakses via this.saveToStorage
    };
  },
  data: function() {
    return {
      activeMenu: '',
      showMenu: false,
      showMenu2: true,
      scrollPosition:0,
      menus:[],
      routerViewKey: 0,
    };
  },
  components: {
    VerticalMenu,
    HorizontalMenu,
    MobileMenu,
  },
  computed: {
    ...mapState(useAuthStore, {
      user: 'loggedUser',
      role:'role',
      app:'app',
      pageTitle: 'pageTitle',
      pageSubTitle: 'pageSubTitle',
      route: 'route',
    }),
    ...mapState(useDataStore, {
      layout: 'layout',
      template: 'template'
    }),
    MenuComponent(){
      return window.innerWidth <= 500 ? MobileMenu : (this.layout == 'horizontal' ? HorizontalMenu : VerticalMenu)
    },
  },
  watch: {
    route(to, from){
      this.setActiveMenu()
    },
    role(newRole, oldRole) {
      this.routerViewKey++ // Trigger re-render
    },
    app(val){
      console.log('app changed', app)
      this.getMenus(val)
    }
  },
  methods: {
    setActiveMenu: function() {
      let vm = this
      let index = false
      this.menus.forEach(m => {
        if (m.type == 'submenu') {
          m?.children?.forEach(c => {
            if (!index)
              index = vm.checkIndex(vm.route, c)
          })
        } else {
          if (!index)
            index = vm.checkIndex(vm.route, m)
        }
      })
      // console.log(index)
      if (!index) index = ''
      
      this.activeMenu = index
      
    },
    async getMenus(app = 'admin'){
      this.menu = ''
      this.menu = app
      let vm = this
      console.log(app);
      // let index = vm.coalesce([vm.route.meta.app, 'default'])
      await import(`@2/${app}/helpers/menus.js`)
        .then(res => {
          console.log(res.default)
          this.menus = res.default
          this.setActiveMenu()
        })
        .catch( (err) => {
          // console.log(err)
          this.menus = []
        })
    },
    checkIndex(route, menu){
      // console.log(route, menu)
      let name = getObjectValueByPath(APP_ROUTES, menu.route)?.name
      // console.log(route, name)
      if (route.name == name) {
        // console.log(route.params, menu.params)
        if (isEmpty(menu.params))
          return menu.index
        else {
          if (JSON.stringify(route.params) == JSON.stringify(menu.params) )
            return menu.index
        }
      }
      return false
    },
    handleActionClick(obj){
      let action = obj.action
      if (action == 'edit')
        this.router.push({name:'account'})
      else if (action == 'logout')
        this.doLogout()
    },
    doLogout: function() {
      useAuthStore().logout()
        .then(res => {
          window.location.href = this.$siteUrl
        })
        .catch(err => {
          // console.log(err)
          this.$notify({
            type:'error',
            title: 'Gagal',
            message: 'Terjadi kesalahan pada server',
            position: 'bottom-right'
          });
        });
    },
    toggleMenu(to){
      useDataStore().setLayout(to)
      // useDataStore().filters.layout = to
    }
  },
  created: async function() {
    // this.resetStorage('menu')
    this.getMenus(useAuthStore().getApp())
    this.scrollPosition = window.scrollY;
  },
  mounted(){
    // useAuthStore().getApp()
  },
  beforeRouteLeave(to, from){
  }
}
</script>

<style lang="scss">
  .el-sub-menu__title {
    &:hover {
      background-color: transparent;
    }
  }
</style>
<template>
  <div class="">
    <div class="z-[99] h-[40px]">
      <div class="absolute sm:fixed z-[10] top-0 overflow-visible w-full max-w-[100vw] h-[30px]"> 
        <div class="relative overflow-hidden h-[100px]">
          <el-header class="bg-orange-300 h-[40px] w-full relative"></el-header>
          <div id="top" class="add-play bg-cover bg-bottom
            h-[70px] w-[1400px] absolute z-[51] top-[1px]
            translate-x-[calc(50vw-50%)] sm:-translate-x-[calc(575px)]"
              :style="`background-image:url('${$baseUrl}assets/images/top.png')`"></div>
        </div>
        <img id="logo" :src="$baseUrl + 'assets/images/logo-kecil.png'" height="90px" 
          @click="$router.push({name:defaultRoute})"
          class="pointer animate hover:scale-[0.8]
          absolute z-[53] top-[0px]
          mt-2
          translate-x-[calc(50vw-50%)]
          sm:-translate-x-[calc(50%-125px)]"/>
      </div>
    </div>
    <div id="menu-vertical" class="h-screen w-[--width-menu] bg-white
      animate
      -translate-x-full sm:translate-x-0
      fixed left-0 top-0
      z-[50] sm:z-[1]
      flex flex-col justify-between">
      <div class="mt-[100px] mb-6 mx-5 text-white z-[2]
        flex flex-col items-center">
        <div class="w-full px-6 mt-0 z-[1]
          text-white leading-[1.3]">
          Assalamu'alaikum,<br/>
          <div class="text-xl font-semibold truncate">{{ user.nama }}</div>
          <div class="text-md font-semibold truncate">{{ user.unit_kerja }}</div>
          <div class="mt-1 text-md leading-[1] cursor-pointer"
            @click="showRole = true">
            <span class="el-dropdown-link text-white flex items-end gap-1">
              {{ ucFirst(user.role) }}
              <icons icon="fe:arrow-down" class="text-[90%]" />
            </span>
          </div>
          <teleport to="body">
            <el-dialog v-model="showRole"
              class="[&_*]:font-montserrat text-teal-800 w-[280px]">
              <template #header>
                <div>Masuk Sebagai</div>
              </template>
              <el-radio-group class="flex flex-col gap-2"
                v-model="role">
                <el-radio-button v-for="rl in user.allowed_roles"
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
                    authStore.changeRole({role:role})"
                    class="bg-teal-700 border-0">
                    Ubah
                  </el-button>
                </div>
              </template>
            </el-dialog>
          </teleport>
        </div>
      </div>
      <div class="absolute w-full h-[285px] left-0 top-[0px] z-[0]
        bg-[length:345px] bg-repeat bg-bottom"
        :style="{
          backgroundImage:`url('${$baseUrl}/assets/images/dashboard.png')`,
        }"/>
      <el-menu :default-active="activeMenu"
        @select="handleSelect"
        class="el-menu-vertical-demo bg-transparent
          w-full h-full
          pt-11 ">
        <template v-for="menu in menus">
          <template v-if="menu.type == 'submenu' && (isEmpty(menu.roles) || menu?.roles?.includes(user.role))">
            <el-sub-menu :index="menu.index" class="pl-5 [&>*]:p-0 text-left menu-item-custom title">
              <template #title>
                <icons v-if="!isEmpty(menu.icon)" class="mr-2" :icon="menu.icon" />
                <span class="">{{ menu.label }}</span>
              </template>
              <template v-for="child in menu.children">
                <el-menu-item @click="$router.push({name:child.route})"
                  v-if="(isEmpty(child.roles) || child?.roles?.includes(user.role))"
                  :index="child.index" class="pl-6 menu-item-custom title">
                  <icons v-if="!isEmpty(child.icon)" class="mr-2" :icon="child.icon" />
                  <span class="">{{ child.label }}</span>
                </el-menu-item>
              </template>
            </el-sub-menu>
          </template>
          <template v-else-if="(isEmpty(menu.roles) || menu?.roles?.includes(user.role))">
            <el-menu-item @click="isEmpty(menu.route) ?
              $emit('function', menu.function) :
              $router.push({name:menu.route, params: menu.params})"
              :index="menu.index" class="pl-5 text-left menu-item-custom title">
              <icons v-if="!isEmpty(menu.icon)" class="mr-2" :icon="menu.icon" />
              <span class="">{{ menu.label }}</span>
            </el-menu-item>
          </template>
        </template>
        <el-menu-item @click="$emit('function', 'doLogout')"
          class="pl-5 text-left menu-item-custom title">
          <icons icon="mdi:logout" />
          <span class="">Keluar</span>
        </el-menu-item>
      </el-menu>
      <div class="text-teal-700
        text-center px-2 pb-10">
        <div class="mb-2 text-[12px]">Ubah Menu</div>
        <div class="flex items-center justify-center
          w-[150px] py-1 px-1 mx-auto
          text-teal-700 bg-teal-50 pointer text-[13px]
          border border-teal-700 border-solid
          transitian-all duration-300 hover:scale-90"
          @click="$emit('toggle', '0')">
          <icons icon="tdesign:menu-filled"/>
          <span>Menu Horizontal</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  const authStore = useAuthStore()
   
</script>

<script>
import { mapState } from 'pinia';

export default {
  name: 'vertical-menu',
  emits:['function'],
  components:{
  },
  props:{
    activeMenu: {
      type:String,
      default:'',
    },
    menus:{
      type:[Array, Object],
      default:[],
    }
  },
  data: function() {
    return {
      role:'',
      showRole:false
    };
  },
  watch: {

  },
  computed: {
    ...mapState(useAuthStore, {
      user: 'loggedUser',
    }),
  },
  methods: {
    handleActionClick(val){
      this.$emit('action', val)
    },
    handleSelect: function(action) {
      this.addClass('.el-menu-vertical-demo','-translate-x-full sm:translate-x-0');
    },
  },
  updated: function() {
    
  },
  beforeRouteLeave(to, from){
    
  }
}
</script>

<style lang="postcss" scoped>
  :deep(.menu-item-custom) {
		@apply 
      transition-all ease-in-out duration-300
      bg-gradient-to-l from-transparent from-50% to-teal-700 to-50%
      bg-[length:200%_200%] bg-right-bottom 
      text-[15px]
      leading-[0]
      border-0
      [--el-menu-item-height:40px]
      [--el-menu-sub-item-height:40px]
      hover:bg-left-top
		!important;
	}
  :deep(.menu-item-custom.is-active) {
    @apply
      bg-teal-100
    !important;
  }
  :deep(.menu-item-custom) {
    li, span, div {
      @apply 
        text-teal-700
      !important;
    }
    svg {
      @apply fill-current text-teal-700 !important;
    }
  }
  :deep(.menu-item-custom):hover {
    > li, > span, > div * {
      @apply 
        text-white
      !important;
    }
    > svg {
      @apply fill-white text-white !important;
    }
  }
</style>
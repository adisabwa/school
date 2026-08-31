<template>
  <div class="">
    <div class="z-[99] h-[40px] relative">
      <div class="absolute sm:fixed z-[10] top-0 overflow-visible w-full max-w-[100vw] h-[30px]"> 
        <div class="relative overflow-hidden w-full h-[70px]">
          <el-header class="bg-orange-300 h-[40px] w-full relative"></el-header>
          <div id="top" class="add-play bg-cover bg-bottom
            h-[70px] w-[1400px] absolute z-[51] top-[1px]
            translate-x-[calc(50vw-50%)] sm:-translate-x-[calc(590px)]
            translate-y-[-5px]"
            :style="{ 
              'background-image': `url('${$topElementBackground}')`, 
            }"></div>
        </div>
        <img id="logo" :src="$logoDefault" height="80px" 
          @click="$router.push({name:defaultRoute})"
          class="pointer animate hover:scale-[0.8]
          absolute z-[53] top-0
          mt-2
          translate-x-[calc(50vw-50%)]
          sm:-translate-x-[calc(50%-110px)]"/>
      </div>
    </div>
    <div id="menu-vertical" class="h-screen w-[90%] sm:w-[--width-menu]
      animate
      -translate-x-full sm:translate-x-0
      fixed left-0 top-0
      z-[2] sm:z-[1]
      flex flex-col justify-between
      bg-[var(--color-main-700)]">
      <div class="absolute w-full h-full z-[-1]
        bg-cover opacity-20"
        :style="{
          backgroundImage:`url('${$baseUrl}/assets/images/menu.png')`,
        }"></div>
      <div>
        <div class="mt-[110px] mx-5 text-white z-[2]
          flex flex-col items-center">
          <div class="w-full mt-0 z-[1]
            text-white leading-[1.3]">
            Assalamu'alaikum,<br/>
            <div class="text-xl font-semibold leading-none">{{ isEmpty(user.nama) ? 'Tamu' : user.nama }}</div>
            <div class="text-md font-semibold truncate">{{ user.unit_kerja }}</div>
            <div class="text-md leading-[1] mt-1">
              <div v-if="role == ''">
                <el-button class="bg-white text-[var(--color-main-700)] border-0 h-auto p-[7px] px-3 mt-1"
                  @click="$router.push({name:'default'})">
                  <span class="text-[13px] font-bold">MASUK</span>
                </el-button>
              </div>
              <span v-else class="el-dropdown-link text-white flex items-end gap-1"  @click="showRole = true" >
                {{ getRoleLabel(role) }}
                <icons icon="fe:arrow-down" class="text-[90%]" />
              </span>
            </div>
            <teleport to="body">
              <el-dialog v-model="showRole"
                class="[&_*]:font-montserrat text-[var(--color-main-800)] w-[280px]">
                <template #header>
                  <div>Masuk Sebagai</div>
                </template>
                <el-radio-group class="flex flex-col gap-2"
                  v-model="selectedRole">
                  <el-radio-button v-for="rl in roles"
                    :value="rl" class="
                    border border-solid border-[var(--color-main400)]
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
                        app:$route?.meta?.app ?? 'all',
                        role:selectedRole
                      })"
                      class="bg-[var(--color-main-700)] border-0">
                      Ubah
                    </el-button>
                  </div>
                </template>
              </el-dialog>
            </teleport>
          </div>
        </div>
        <el-menu :default-active="activeMenu" unique-opened
          @select="handleSelect"
          class="el-menu-vertical-demo bg-transparent
            border-0
            w-full max-h-[calc(100vh-150px)] overflow-auto
            text-[16px]
            pt-4 ">
          <template v-for="menu in menus">
            <template v-if="menu.type == 'submenu' && (isEmpty(menu.roles) || menu?.roles?.includes(role))">
              <el-sub-menu :index="menu.index" class="pl-5 [&>*]:p-0 text-left title">
                <template #title>
                  <icons v-if="!isEmpty(menu.icon)" class="mr-2" :icon="menu.icon" />
                  <span class="">{{ menu.label }}</span>
                </template>
                <template v-for="child in menu.children">
                  <el-menu-item @click="$router.push({name:child.route})"
                    v-if="(isEmpty(child.roles) || child?.roles?.includes(role))"
                    :index="child.index" class="pl-6 title
                      text-[14px] h-[34px]">
                    <icons v-if="!isEmpty(child.icon)" class="mr-2" :icon="child.icon" />
                    <span class="">{{ child.label }}</span>
                  </el-menu-item>
                </template>
              </el-sub-menu>
            </template>
            <template v-else-if="(isEmpty(menu.roles) || menu?.roles?.includes(role))">
              <el-menu-item @click="isEmpty(menu.route) ?
                $emit('function', menu.function) :
                $router.push({name:menu.route, params: menu.params})"
                :index="menu.index" class="pl-5 text-left title">
                <icons v-if="!isEmpty(menu.icon)" class="mr-2" :icon="menu.icon" />
                <span class="">{{ menu.label }}</span>
              </el-menu-item>
            </template>
          </template>
          <el-menu-item @click="$emit('function', 'doLogout')"
            class="pl-5 text-left title">
            <icons icon="mdi:logout" />
            <span class="">Keluar</span>
          </el-menu-item>
        </el-menu>
      </div>
      <div class="text-white
        text-center px-2 pb-10">
        <div class="mb-2 text-[12px]">Ubah Menu</div>
        <div class="flex items-center justify-center
          w-[150px] py-1 px-1 mx-auto
          text-white bg-transparent pointer text-[13px]
          border border-white border-solid
          transitian-all duration-300 hover:scale-90"
          @click="$emit('toggle', 'horizontal')">
          <icons icon="tdesign:menu-filled"/>
          <span>Menu Horizontal</span>
        </div>
      </div>
    </div>
    <div class="bg-white rounded-full w-[60px] h-[60px] opacity-50 hover:opacity-80 md:hidden
      fixed z-[99999] bottom-5 right-5 flex items-center justify-center"
      @click="handleSelect">
      <icons icon="mdi:menu" class="text-4xl m-0 text-[var(--color-main-900)]
        " />
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'pinia';
import { useAuthStore } from '@/config/stores/authStore'
import { getRoleLabel } from '@/modules/dashboard/helpers/functionHelper'

export default {
  name: 'vertical-menu',
  setup(){
    return {
      ucFirst,
      getRoleLabel,
      isEmpty,
      toggleClass,
    }
  },
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
      selectedRole:'',
      showRole:false,
    };
  },
  watch: {
    showRole(val){
      this.selectedRole = this.role
    }
  },
  computed: {
    ...mapState(useAuthStore, {
      user: 'loggedUser',
      role: 'role',
      roles:'roles',
    }),
  },
  methods: {
    ...mapActions(useAuthStore,{
      changeRole: 'changeRole',
    }),
    handleActionClick(val){
      this.$emit('action', val)
    },
    handleSelect: function(action) {
      toggleClass('#menu-vertical','-translate-x-full sm:translate-x-0');
    },
  },
  updated: function() {
    
  },
  beforeRouteLeave(to, from){
    
  }
}
</script>

<style lang="postcss" scoped>
  :deep(.el-menu) {
    @apply bg-transparent w-full !important;
  }
  :deep([role="menuitem"]) {
		@apply 
      transition-all ease-in-out delay-[400] duration-500 hover:delay-0
      [&_*]:delay-[400] [&_*]:hover:delay-0 
      bg-gradient-to-l from-transparent from-50% to-[var(--color-main-100)] to-50%
      bg-[length:200%_200%] bg-right-bottom 
      leading-[0]
      border-0
      [--el-menu-item-height:40px]
      [--el-menu-sub-item-height:40px]
      hover:bg-left-top
      hover:shadow-md
      hover:-translate-y-[2px]
      pl-5
		!important;
    li, span, div {
      @apply 
        transition-all ease-in-out delay-[400] duration-500 hover:delay-0
        text-white
      !important;
    }
    svg {
      @apply fill-current text-white
      transition-all ease-in-out delay-[400] duration-500 hover:delay-0
      !important;
    }
  }
  :deep(.el-sub-menu.is-active [role="menuitem"]):not(.is-active) {
    @apply
      bg-[var(--color-main-700)]
    !important;
    * {
      @apply text-[var(--color-main-50)];
    }
    > svg {
      @apply bg-transparent !important;
    }
  }
  :deep(.el-sub-menu.is-active [role="menuitem"]):hover {
    > svg {
      @apply text-[var(--color-main-700)] fill-[var(--color-main-700)] !important;
    }
  }
  :deep(.el-sub-menu.is-active [role="menuitem"]).is-active {
    @apply bg-[var(--color-main-50)] !important;
    > svg {
      @apply bg-transparent text-[var(--color-main-700)] fill-[var(--color-main-700)] !important;
    }
  }
  :deep([role="menuitem"]):hover {
    .el-menu {
      @apply bg-[var(--color-main-700)] !important;
    }
    > li, > span, > div * {
      @apply 
        text-[var(--color-main-700)]
      !important;
    }
    > svg {
      @apply fill-[var(--color-main-700)] text-[var(--color-main-700)] !important;
    }
  }
  :deep([role="menuitem"].is-active) {
    @apply bg-[var(--color-main-50)] !important;
    > li, > span, > div * {
      @apply 
         text-[var(--color-main-700)]
      !important;
    }
    > svg {
      @apply fill-[var(--color-main-700)] text-[var(--color-main-700)] !important;
    }
  }
</style>
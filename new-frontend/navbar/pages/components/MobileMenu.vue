<template>
  <div class="">
		<div class="z-[99] h-[60px] relative">
			<div class="bg-teal-700 z-[100] h-[60px] w-full flex items-center px-4 fixed">
				<div class="absolute w-full h-full z-[-1]
				bg-cover opacity-20"
				:style="{
					backgroundImage:`url('${$baseUrl}/assets/images/menu.png')`,
				}"></div>
				<div class="rounded-md p-[1px] bg-white flex items-center gap-1">
					<img id="logo" :src="$baseUrl + 'assets/images/logo-kecil.png'" height="30px" 
					
          @click="defaultPage(); handleSelect('hide');"
					class="pointer"/>
				</div>
				<div class="leading-[1.3] font-montserrat ml-2 h-full flex flex-col justify-center shrink-0">
					<div class="text-white font-bold text-[14px]">Sistem Informasi</div>
					<div class="text-white font-bold text-[14px]">Darul Arqom</div>
				</div>
				<div class="w-full flex items-center justify-end gap-4">
          <NotificationList />
          <NavMenu class="w-[22px] h-[20px] *:bg-white mr-2" 
            ref="navMenu"
            @click="handleSelect"/>
				</div>
			</div>
		</div>
    <div id="menu-wrapper" class="h-screen w-screen 
      fixed left-0 top-0
      z-[2] invisible
      bg-slate-900/[0.8]">
      <div class="absolute h-full w-full"
        @click="handleSelect('hide')" />
      <div id="menu-mobile" class="relative 
        animate
        -translate-x-full
        h-full w-[85%]
        z-[2] overflow-hidden
        flex flex-col justify-between
        bg-teal-700">
        <div class="absolute w-full h-full
          bg-cover opacity-20"
          :style="{
            backgroundImage:`url('${$baseUrl}/assets/images/menu.png')`,
          }"></div>
        <div>
          <div class="mt-20 mx-5 text-white z-[2]
            flex flex-col items-center">
            <div class="w-full mt-0 z-[1]
              text-white leading-[1.3]">
              <div class="h-[90px] w-[90px] mx-auto z-[2] mb-3
                rounded-full overflow-hidden relative
                flex items-center justify-center
                border-3 border-solid border-white"
                @click="showEdit = true;showColumns=['photo']">
                <div v-if="!isEmpty(user.photo)"
                  class="w-full h-full bg-cover bg-top"
                  :style="`background-image:url('${user.photo}')`"
                  />
                <icons v-else 
                  icon="mdi:user" class="mr-0  text-[100px]"/>
              </div>
              <div class="text-xl font-semibold truncate">{{ user.nama }}</div>
              <div class="text-md font-semibold truncate">{{ user.unit_kerja }}</div>
              <div class="mt-1 text-md leading-[1] cursor-pointer"
                @click="showRole = true">
                <span class="el-dropdown-link text-white flex items-end gap-1">
                  {{ ucFirst(role) }}
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
                        changeRole({
                          app:$route?.meta?.app ?? 'all',
                          role:selectedRole
                        })"
                        class="bg-teal-700 border-0">
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
                  goTo(menu.route, menu.params)"
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
            @click="$emit('toggle', '0')">
            <icons icon="tdesign:menu-filled"/>
            <span>Menu Horizontal</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'pinia';
import { useAuthStore } from '@2/shared/config/stores/authStore'
import NavMenu from '@2/shared/components/NavMenu.vue';
import NotificationList from './NotificationList.vue';

export default {
  name: 'vertical-menu',
  setup(){
    return {
      ucFirst,
      isEmpty,
      goTo,
      defaultPage,
    }
  },
  emits:['function'],
  components:{
    NavMenu,
    NotificationList,
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
    handleSelect: function(action = 'toggle') {
      console.log(action)
      switch (action) {
        case 'hide':
          this.addClass('#menu-wrapper', 'invisible');
          this.addClass('#menu-mobile','-translate-x-full');
          this.$refs?.navMenu?.changeNav('remove')
          break;
        case 'show':
          this.removeClass('#menu-wrapper', 'invisible');
          this.removeClass('#menu-mobile','-translate-x-full');
          this.$refs?.navMenu?.changeNav('add')
          break;
        default:
          this.toggleClass('#menu-wrapper', 'invisible');
          this.toggleClass('#menu-mobile','-translate-x-full');
          this.$refs?.navMenu?.changeNav()
          break;
      }
    },
  },
  updated: function() {
    
  },
  beforeRouteLeave(to, from){
    
  },
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
      bg-gradient-to-l from-transparent from-50% to-teal-100 to-50%
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
        text-white
      !important;
    }
    svg {
      @apply fill-current text-white !important;
    }
  }
  :deep(.el-sub-menu.is-active .el-menu) :not(.is-active) {
    * {
      @apply text-teal-50;
    }
    @apply
      bg-teal-700
    !important;
  }
  :deep([role="menuitem"]):hover {
    .el-menu {
      @apply bg-teal-700 !important;
    }
    > li, > span, > div * {
      @apply 
        text-teal-700
      !important;
    }
    > svg {
      @apply fill-teal-700 text-teal-700 !important;
    }
  }
  :deep([role="menuitem"].is-active) {
    @apply bg-teal-50 !important;
    > li, > span, > div * {
      @apply 
         text-teal-700
      !important;
    }
    > svg {
      @apply fill-teal-700 text-teal-700 !important;
    }
  }
</style>
<template>
  <div class="">
    <div class="z-[99] h-[40px]">
      <div class="absolute sm:fixed z-[10] top-0 overflow-visible w-full max-w-[100vw] h-[30px]"> 
        <div class="relative overflow-hidden h-[100px]">
          <el-header class="bg-white h-[40px] w-full relative
            flex justify-end
            shadow-md">
            <el-menu :default-active="activeMenu"
              @select="handleSelect"
              ellipsis
              mode="horizontal"
              class="el-menu-demo bg-transparent
                h-full
                justify-end
                w-full
                max-w-[calc(100vw-500px)]">
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
            </el-menu>
            <div class="menu-item-custom title p-2">
              <el-popover placement="bottom-end" :visible="showAccount"
                :show-arrow="false"
                popper-class="text-white w-[250px] h-fit rounded-lg overflow-hidden">
                <template #reference>
                  <icons id="icon-account" icon="mdi:user-circle" class="mr-0 text-2xl text-teal-700 cursor-pointer"
                    @click="showAccount = true"/>
                </template>
                <div class="absolute w-[103%] h-[270px] left-[-3px] -top-[120px] z-[0]
                  bg-[length:340px] bg-repeat bg-bottom"
                  :style="{
                    backgroundImage:`url('${$baseUrl}/assets/images/dashboard.png')`,
                  }"/>
                <div class="mt-2 mb-1 mx-3 z-[2]
                  flex flex-col items-center"
                  v-click-exclude-id:icon-account="() => showAccount = false">
                  <div class="h-[90px] w-[90px] mx-auto z-[2] mb-3
                    rounded-full overflow-hidden relative
                    flex items-center justify-center
                    border-3 border-solid border-teal-700"
                    @click="showEdit = true;showColumns=['photo']">
                    <div v-if="!isEmpty(user.photo)"
                      class="w-full h-full bg-cover bg-top"
                      :style="`background-image:url('${user.photo}')`"
                      />
                    <icons v-else 
                      icon="mdi:user" class="mr-0 text-teal-700 text-[100px]"/>
                  </div>
                  <div class="w-full px-0 mt-0 z-[1]
                    text-teal-700 leading-[1.3]">
                    Assalamu'alaikum,<br/>
                    <div class="text-xl font-semibold truncate">{{ user.nama }}</div>
                    <div class="text-md font-semibold truncate">{{ user.unit_kerja }}</div>
                    <div class="mt-1 mb-2 text-md leading-[1] cursor-pointer">
                        {{ ucFirst(user.role) }}
                    </div>
                    <div class="border border-solid border-teal-700/[0.2]
                      mt-1">
                      <div class="py-1 flex items-center justify-center
                          cursor-pointer
                          menu-item-custom title w-full border-0"
                        @click="showRole = true">
                        <icons icon="ph:user-switch" />
                        <span>Masuk Sebagai</span>
                      </div>
                    </div>
                    <div class="border border-solid border-teal-700/[0.2]
                      mt-1">
                      <div  @click="$emit('function', 'doLogout')"
                        class="py-1 flex items-center justify-center
                          cursor-pointer
                          menu-item-custom title w-full border-0">
                        <icons icon="mdi:logout" />
                        <span class="">Keluar</span>
                      </div >
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
              </el-popover>
            </div>
            <div class="menu-item-custom title p-2">
              <el-popover placement="bottom-end">
                <template #reference>
                  <icons icon="zondicons:show-sidebar" class="mr-0  text-2xl text-teal-700 cursor-pointer"
                    @click="$emit('toggle','1')"/>
                </template>
                <div>
                  Menu Sidebar
                </div>
              </el-popover>
            </div>
          </el-header>
          <div id="top" class="add-play bg-cover bg-bottom
            h-[90px] w-[1400px] absolute z-[51] top-[0px]
            scale-x-[0.4]
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
  </div>
</template>

<script setup>
  const authStore = useAuthStore()
   
</script>

<script>
import { mapState } from 'pinia';

export default {
  name: 'horizontal-menu',
  emits:['function'],
  setup(){
    return {
      authStore: useAuthStore(),
    }
  },
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
      showRole:false,
      showAccount:false,
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
      this.addClass('.el-menu-demo','-translate-x-full sm:translate-x-0');
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
      bg-gradient-to-l from-transparent from-[51%] to-teal-700 to-[51%]
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
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
					@click="$router.push({name:defaultRoute})"
					class="pointer"/>
				</div>
				<div class="leading-[1.3] font-montserrat ml-2 h-full flex flex-col justify-center shrink-0">
					<div class="text-white font-bold text-[14px]">Sistem Informasi</div>
					<div class="text-white font-bold text-[14px]">Darul Arqom</div>
				</div>
				<div class="w-full flex items-center justify-end gap-2">
					<icons icon="mdi:bell" class="pointer text-white text-[22px]" />
					<div class="h-[30px] w-[30px] cursor-pointer
						rounded-full overflow-hidden relative
						flex items-center justify-center"
						@click="showAccount = true">
						<div v-if="!isEmpty(user.photo)"
							class="w-full h-full bg-cover bg-top"
							:style="`background-image:url('${user.photo}')`"
							/>
						<icons v-else 
							icon="mdi:user-circle" class="mr-0 text-[30px] text-white"/>
					</div>
					<el-dialog append-to-body v-model="showAccount"
						class="p-4 *:text-white w-[250px] h-fit rounded-lg overflow-hidden bg-teal-700">
						<div class="absolute w-full h-full z-[0] top-0 left-0
							bg-[length:340px] bg-repeat bg-bottom
							opacity-20"
							:style="{
								backgroundImage:`url('${$baseUrl}/assets/images/menu.png')`,
							}"/>
						<div class="mt-2 mb-1 mx-1 z-[2]
							flex flex-col items-center">
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
							<div class="w-full px-0 mt-0 z-[1]
									leading-[1]">
								Assalamu'alaikum,
								<div class="text-xl font-semibold truncate">{{ user.nama }}</div>
								<div class="text-md font-semibold truncate">{{ user.unit_kerja }}</div>
								<div class="mb-2 text-md leading-[1] cursor-pointer">
										{{ ucFirst(role) }}
								</div>
								<div class="border border-solid border-white/[0.5] rounded-lg
									mt-2">
									<div class="py-1 flex items-center justify-center
											cursor-pointer
											menu-item-custom-custom title w-full border-0"
										@click="showRole = true">
										<icons icon="ph:user-switch" />
										<span>Masuk Sebagai</span>
									</div>
								</div>
								<div class="border border-solid border-white/[0.5] rounded-lg
									mt-2">
									<div  @click="$emit('function', 'doLogout')"
										class="py-1 flex items-center justify-center
											cursor-pointer
											menu-item-custom-custom title w-full border-0">
										<icons icon="mdi:logout" />
										<span class="">Keluar</span>
									</div >
								</div>
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
							</div>
						</div>
					</el-dialog>
				</div>
			</div>
		</div>
		<div v-if="!['dashboard','default'].includes($route.name)">
			<div class="h-[70px] w-full fixed bottom-0 z-[100] bg-white flex justify-between px-2">
				<template v-for="menu in menus">
					<template v-if="menu.type == 'submenu' && (isEmpty(menu.roles) || menu?.roles?.includes(role))">
						<!-- <el-sub-menu :index="menu.index" class="pl-5 [&>*]:p-0 text-left title">
						<template #title>
							<icons v-if="!isEmpty(menu.icon)" class="mr-2" :icon="menu.icon" />
							<span class="">{{ menu.label }}</span>
						</template>
						<template v-for="child in menu.children">
							<el-menu-item-custom @click="$router.push({name:child.route})"
							v-if="(isEmpty(child.roles) || child?.roles?.includes(role))"
							:index="child.index" class="pl-6 title
								text-[14px] h-[34px]">
							<icons v-if="!isEmpty(child.icon)" class="mr-2" :icon="child.icon" />
							<span class="">{{ child.label }}</span>
							</el-menu-item-custom>
						</template>
						</el-sub-menu> -->
					</template>
					<template v-else-if="(isEmpty(menu.roles) || menu?.roles?.includes(role))">
						<div @click="isEmpty(menu.route) ?
							$emit('function', menu.function) :
							$goTo(menu.route, menu.params)"
							:index="menu.index" 
							:class="['text-left menu-item-custom title', $route.name == menu.route ? 'is-active' : '']">
							<icons v-if="!isEmpty(menu.icon)" class="mr-2" :icon="menu.icon" />
							<div class="">{{ menu.label }}</div>
						</div>
					</template>
				</template>
				<div @click="$emit('function', 'doLogout')"
					class="text-left menu-item-custom title">
					<icons icon="mdi:logout" />
					<div class="">Keluar</div>
				</div>
			</div>
		</div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'pinia';
import { useAuthStore } from '@2/shared/config/stores/authStore'

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
	  selectedRole:'',
	  showRole:false,
		showAccount:false,
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
	  this.toggleClass('#menu-vertical','-translate-x-full sm:translate-x-0');
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
			flex flex-col items-center justify-start
			w-full h-full
			p-3 gap-[3px]
			transition-all ease-in-out delay-[400] duration-500 hover:delay-0
			[&_*]:delay-[400] [&_*]:hover:delay-0 
			bg-gradient-to-l from-transparent from-50% to-teal-100 to-50%
			bg-[length:200%_200%] bg-right-bottom 
			leading-[1]
			border-0
			[--el-menu-item-custom-height:40px]
			[--el-menu-sub-item-height:40px]
			hover:bg-left-top
			hover:shadow-md
			hover:-translate-y-[2px]
		!important;
		li, span, div {
			@apply 
				w-full h-full text-center text-[9px] leading-[1.3] uppercase
			!text-slate-500
			!important;
		}
		svg {
			@apply m-0 shrink-0 h-[25px] w-[25px] fill-current !text-slate-500 !important;
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
  :deep(.menu-item-custom):hover {
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
  :deep(.menu-item-custom.is-active) {
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
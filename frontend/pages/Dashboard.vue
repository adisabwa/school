<style lang="postcss">
  .dropdown-user {
    @apply bg-teal-600 text-white !important;
  }
</style>
<template>
	<div class="mt-[50px] sm:mt-0">
		<div class="sm:hidden absolute w-screen h-[250px]
      left-1/2 -translate-x-1/2 -top-[110px] z-[2]
			bg-[length:320px] bg-repeat bg-bottom"
			:style="{
				backgroundImage:`url('${$baseUrl}/assets/images/dashboard.png')`,
			}"/>
    <div class="sm:hidden absolute w-full h-[40px] px-2 mt-0 z-[3]
      text-white leading-[1.3]">
      Assalamu'alaikum,<br/>
      <div class="text-xl font-semibold">{{ user.nama }}</div>
      <div class="text-md leading-[1]"
        @click="showRole = true">
        <span class="el-dropdown-link text-white flex items-end gap-1">
          {{ ucFirst(user.role) }}
          <icons icon="fe:arrow-down" class="text-[90%]" />
        </span>
      </div>
        <el-dialog v-model="showRole" width="60%"
          class="[&_*]:font-montserrat text-teal-800 ">
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
    </div>
		<div id="management" class="flex flex-col justify-center max-w-[1100px] mx-1 sm:mx-auto pt-12 pb-20">
			<div class="bg-white rounded-xl shadow-md shadow-emerald-700/[0.2]
				overflow-hidden
				mb-3 pt-0 pb-2
				flex flex-col md:flex-row items-center
				w-full">
				<div class="font-montserrat px-10 py-3 pt-[70px] *:w-fit
          sm:pt-4 md:w-full
					rounded-xl
					flex flex-col items-center
					bg-emerald-100/[0.3]">
					<div>Selamat Datang di</div>
					<div class="font-bold">Aplikasi Ashoi-Mu</div>
				</div>
				<div class="w-full h-full font-montserrat">
					<div class="px-3 py-2 flex">
						<div class="w-1/2 text-center">
							<div class="text-[20px] font-bold">{{ dayIndo(dateNow()) }}</div>
							<div class="text-[15px]">{{ dateIndo(dateNow()) }}</div>
						</div>
						<div class="text-center
							border-0 border-r border-solid border-emerald-700/[0.4]"></div>
						<div class="w-1/2 leading-[1.4]">
							<div class="text-[14px] text-center">Belum mengisi :</div>
							<div class="text-[21px] text-center font-bold">{{ beforeMax }} Hari</div>
						</div>
					</div>
				</div>
			</div>
			<el-button class="mb-2
				font-montserrat
				shadow-md shadow-emerald-900/[0.2]
				bg-brand text-emerald-50
				px-4 py-5
				active:scale-95
				[&>*]:w-full"
				@click="$router.push({name:'activity'})">
				<div class="flex justify-between w-full">
					<div class="flex items-center">
						<icons icon="uil:notes"></icons>Aktivitas Hari Ini
					</div>
					<icons icon="mingcute:square-arrow-right-fill"
						class="grow-0 text-xl text-emerald-50"/>
				</div>
			</el-button>
			<el-card v-for="_menu in menus"
				class="relative h-auto w-full
				bg-white/[0.8] shadow-md
				rounded-[20px]
				mb-4"
				header-class="pt-4 pb-3">
					<template #header>
						<div class="font-montserrat font-bold text-xl text-emerald-900">
							{{ _menu.title }}
						</div>
					</template>
					<div class="grid grid-cols-[repeat(auto-fit,_65px)] gap-[25px] gap-y-[15px]
						justify-center
						px-1 sm:max-w-[80%] mx-auto pb-5">
						<template v-for="(menu, ind) in _menu.menu">
							<div class="grid-item h-[110px] cursor-pointer"
								@click="$router.push({name:menu.route})">
								<div class="animate pointer duration-500 group/app
									hover:scale-90"	>
									<el-badge :value="menu.before" :offset="['-5','10']"
										:show-zero="false">
										<div :class="`${menu.color} ${menu.textColor} ${menu.shadowColor}
											shadow-md
											h-[65px] w-[65px] rounded-full
											relative overflow-hidden
											flex items-center justify-center align-middle`">
											<img :src="menu.image" height="45px" width="45px"
												class="rounded-full"/>
											<div class="absolute w-[90px] h-[200%] rotate-[-25deg] top-[-50%]
												bg-gradient-to-r from-transparent via-white/[0.2] to-transparent
												animate-[fly-in-absolute_4s_infinite_ease-in-out] [--from-left:-70%] [--left:350%] "
												:style="{animationDelay: ind + 's !important'}"/>
										</div>
									</el-badge>
									<div class="text-center mt-2 text-[15px] text-emerald-800 w-fit
										absolute -translate-x-1/2 left-1/2
										leading-[1.1]">{{ menu.label }}</div>
								</div>
							</div>
						</template>
						
					</div>
			</el-card>
      <div class="
				bg-white/[0.8] shadow-md
				rounded-[20px]
        py-5 px-7 mb-6
        text-center text-[13px] italic">
        Sesungguhnya orang yang selalu <b>membaca kitab Allah</b> dan <b>mendirikan shalat</b> dan <b>menafkahkan sebahagian dari rezeki yang Kami anugerahkan kepada mereka</b> dengan diam-diam dan terang-terangan, mereka itu mengharapkan perniagaan yang tidak akan merugi, <br/>
        <b>- &nbsp; Fatir: 29 &nbsp; -</b>
      </div>
      <div v-if="canInstall && !isStandalone()"
        class="w-full text-center">
        <el-button class="
          font-montserrat
          shadow-md shadow-emerald-900/[0.2]
          bg-brand text-emerald-50
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


<script setup>
import { useA2HS } from '@/composables/useA2HS';
import { useAddToHomescreen } from '@owliehq/vue-addtohomescreen';

useAddToHomescreen({ buttonColor: 'red' });
const { canInstall, promptInstall, isStandalone } = useA2HS();

async function installApp() {
  const result = await promptInstall();
  if (result?.outcome === 'accepted') {
    console.log('User accepted the install prompt');
  } else {
    console.log('User dismissed the install prompt');
  }
}
const authStore = useAuthStore()
 
</script>

<script>
import { mapState } from 'pinia';
import { organizationMenu, facilityMenu, topMenu } from '@/helpers/menus.js'
import { meanBy } from 'lodash';

export default {
  name: "default",
  components: {
  },
  data: function() {
    return {
			beforeMax:-1,
      showRole:false,
      role:'',
			menus: [
				{
					title: 'Catatan Ibadah',
					menu: topMenu,
				},
				{
					title: 'Kajian / Organisasi',
					menu: organizationMenu,
				},
				{
					title: 'Fasilitas Ibadah',
					menu: facilityMenu,
				},
			],
			information:{

			}
    };
  },
  computed: {
    ...mapState(useAuthStore, {
      user: 'loggedUser',
    }),
  },
	methods:{
		getBefore(){
			this.beforeMax = 0
			this.menus.forEach(menu => {
				let keys = Object.keys(menu.menu)
				for (let i = 0; i < keys.length; i++) {
					const key = keys[i];
					const d = menu.menu[key]
					if (!d?.url || !d?.before) continue
					this.$http.get(d.url+'/get_before')
						.then(res => {
							d.before = res?.data
							if (this.beforeMax < d?.before)
								this.beforeMax = d?.before
						})
				}
			});
		}
	},
	mounted(){
		this.getBefore()
    this.role = this.user.role
	}
	
}
</script>
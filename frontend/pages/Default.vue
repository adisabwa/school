
<template>
	<div class="[--color-border:theme(colors.cyan.400)]">
		<div class="md:pt-32 pt-[80px] bg-white/[0.8] mx-auto w-[90vw] md:w-[1000px] min-h-[calc(100vh-200px)]">
      <div class="relative h-auto w-full pt-3 pb-10
        bg-scroll bg-repeat bg-top
        md:max-w-[80%] max-w-[90%] mx-auto ">
        <div class="mx-auto leading-[1.2]
          text-2xl text-center font-bold text-cyan-800">Sistem Informasi PPM Darul Arqam</div>
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
            Assalamualaikum, <br/>{{ user.nama }}
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
          <div class="grid grid-cols-[repeat(auto-fit,_minmax(60px,_60px))] md:grid-cols-[repeat(auto-fit,_minmax(100px,_100px))]
            gap-[calc(30px)] gap-y-[65px] justify-center
            mx-auto mt-5">
            <template v-for="(menu, ind) in topMenuFilter">
              <div class="grid-item group/menu
              [--duration:0.5s]">
                <div class="relative animate pointer duration-500 group/app
                  hover:scale-90 " 
                  @click="$router.push({name:menu.route[user.app_roles[menu.app] ?? user.app_roles.all]})">
                <div class="relative bg-transparent
                  rounded-full animate
                  flex items-center justify-center">
                  <div class="m-auto h-full rounded-[10px] 
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
                  absolute left-1/2 -translate-x-1/2
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
        </template>
      </div>
		</div>
	</div>
</template>

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
      user:'loggedUser'
    }),
    topMenuFilter(){
      let q = this.keyword?.toLowerCase()
      let menus = this.topMenu.filter((d) => {
        let role = this.user.app_roles[d.app] ?? this.user.app_roles.all
        // console.log(app, role)
        if (d.route[role]) {
          return true
        } else {
          return false
        }
      })
      if (q) 
        return menus?.filter?.(d => {
            return d.label.toLowerCase().includes(q)
        })
      return menus
    }
  },
  data: function() {
    return {
		topMenu:topMenu,
		keyword:'',
    };
  },
  methods:{
    doGLogin(result) {
      this.loading = true;
      useAuthStore().gLogin({credential:result.credential}, true)
        .then(res => {
          this.loading = false;
          this.redirect();
        }).catch(err => {
          this.loading = false;
          console.log(err)
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
  }
	
}
</script>
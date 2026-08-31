
<template>
	<div class="[--color-border:theme(colors.cyan.400)] bg-teal-700 h-full">
    <div :style="{
      backgroundImage: `url('${$baseUrl}assets/images/menu.png')`
      }" 
      class="absolute z-[0] h-full w-full
        repeat bg-contain opacity-30"/>
		<div class="relative md:pt-32 pt-[80px] bg-white/[0.1] mx-auto w-[90vw] md:w-[1000px] min-h-[calc(100vh-200px)]
      text-center">
        <div class="max-w-[95%] md:max-w-[80%] mx-auto relative h-auto w-full pt-3 pb-24 ">
        <div class=" mx-auto leading-[1.2] mt-6
          text-3xl text-center font-bold text-white">Sistem Informasi <br/> PPM Darul Arqam</div>
				<img id="logo" :src="$baseUrl + 'assets/images/vector-2.png'" width="280px" 
					class="mt-8  mx-auto
						"/>
        <div 
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
      </div>
		</div>
	</div>
</template>

<script>
import { mapState } from 'pinia'
import { useAuthStore } from '@2/shared/config/stores/authStore'
import { useDataStore } from '@2/shared/config/stores/dataStore'

export default {
  name: "default",
  components: {
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
    };
  },
  methods:{
    doGLogin(result) {
      this.loading = true;
      useAuthStore().gLogin({credential:result.credential}, true)
        .then(res => {
          this.loading = false;
          this.$router.replace({name:'dashboard'});
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
    
  },
	
}
</script>
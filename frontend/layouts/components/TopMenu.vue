<template>
	<el-container id="menu-hidden" class="animate fixed top-0 z-50 w-full">
    <div class="absolute h-[30px] w-full bg-yellow-50 left-0 z-[100]  [&_*]:text-teal-700" >
      <div class="px-2 sm:px-6 h-full flex justify-between items-start">
        <div class="h-full w-auto">
          <div class="animate pointer menu-item reverse "
            @click="$router.push({name:defaultRoute})">
            <icons icon="mdi:home" />
            <span>Home</span>
          </div>
        </div>
        <div class="w-fit flex flex-row items-start">
          <div class="group/item h-full w-auto ">
            <div class=" bg-yellow-50 h-fit flex gap-3 flex-col w-[200px]">
              <div class="py-[5px] animate pointer duration-200 menu-item">
                <icons icon="tdesign:app" />
                <span>Daftar Aplikasi</span>
              </div>
              <template v-for="(menu, ind) in topMenu">
                <div class="py-[5px] animate pointer duration-200 menu-item hidden group-hover/item:flex"
                  @click="$router.push({name:menu.route})">
                  <icons :icon="menu.icon" />
                  <span>{{ menu.label }}</span>
                </div>
              </template>
              <div class="h-1 hidden group-hover/item:flex"></div>
            </div>
          </div>
          <el-button v-if="isEmpty(user.nama)"
            @click="$router.push({name:'login'})"
            type="primary" link size="small" class="py-[5px] animate pointer duration-200 menu-item border-0"> 
            <icons icon="mdi:user" /> Login
          </el-button>
          <el-dropdown v-else 
            trigger="click" @command="handleActionClick">
            <el-button type="primary" link size="small" class="py-[5px] animate pointer duration-200 menu-item border-0"> 
              <icons icon="mdi:user" /> {{ user.nama }}
              <i class="el-icon-arrow-down el-dropdown__icon"></i>
            </el-button>
            <template #dropdown>
              <el-dropdown-menu slot="dropdown" class=" bg-yellow-50">
                <!-- <div class="mx-7">
                  <div class="font-semibold text-[17px]">{{ user.nama }}</div>
                  <div class="font-semibold text-[14px]
                    mt-2 leading-[1.3] whitespace-normal" >{{ user.unit }}</div>
                </div> -->
                <el-dropdown-item 
                  :command="{action: 'edit'}">
                  <icons icon="material-symbols:edit-outline"/> Ubah Password</el-dropdown-item>
                <el-dropdown-item 
                  :command="{action: 'logout'}">
                  <icons icon="material-symbols:power-settings-new"/> Logout</el-dropdown-item>
              </el-dropdown-menu>
            </template>
          </el-dropdown>
        </div>
      </div>
    </div>
		<div class="mt-[30px] w-full">
			<slot></slot>
		</div>
	</el-container>
</template>

<script>
import { topMenu } from '@/helpers/menus.js'
import { mapState } from 'pinia';

export default {
  data: function() {
    return {
			topMenu:topMenu,
    };
  },
  computed: {
    ...mapState(useAuthStore, {
      user: 'loggedUser',
    }),
  },
  methods: {
    handleActionClick(obj){
      let action = obj.action
      if (action == 'edit')
        this.$router.push({name:'account'})
      else if (action == 'logout')
        this.doLogout()
    },
    doLogout: function() {
      this.$store.dispatch('logout')
        .then(res => {
          this.$router.replace({ name: 'login' });
        })
        .catch(err => {
          this.$notify({
            type:'error',
            title: 'Gagal',
            message: 'Terjadi kesalahan pada server',
            position: 'bottom-right'
          });
        });
    },
  },
  mounted(){
    const menu = document.getElementById('menu-hidden');
    const rect = menu.getBoundingClientRect();
    const proximityThreshold = 10; // distance in pixels

    document.addEventListener('mousemove', (e) => {
      const distanceX = Math.max(rect.left - e.clientX, e.clientX - rect.right, 0);
      const distanceY = Math.max(rect.top - e.clientY, e.clientY - rect.bottom, 0);
      const distance = Math.sqrt(distanceX * distanceX + distanceY * distanceY);
      // console.log(distanceX, distanceY, distance, proximityThreshold)
        if (distance < proximityThreshold) {
          menu.classList.add('translate-y-0', 'pointer-events-auto');
          menu.classList.remove('-translate-y-[30px]', 'pointer-events-none');
        } else {
          setTimeout(() => {
            menu.classList.remove('translate-y-0', 'pointer-events-auto');
            menu.classList.add('-translate-y-[30px]', 'pointer-events-none');
          }, 1000);
        }
    });
  },
}
</script>
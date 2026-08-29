<template>
  <div id="dashboard" class="flex items-center md:px-10 min-w-[200px] max-w-[500px] mx-auto" style="min-height: calc(100vh - 300px);" >
    <div class="w-full
      grid grid-cols-1 gap-y-4">
       <template v-for="menu in menus">
        <template v-if="menu.type == 'submenu'">
          <el-dropdown @command="go" class="w-full m-0 h-12"
            popper-class="min-w-[33.33%]"
            placement="bottom">
            <el-button class="bg-cyan-900 text-white font-bold w-full h-full">
              <div class="el-dropdown-link flex items-center">
                <icons :icon="menu.icon" class="text-current mr-2" />
                <div>{{ menu.title }}</div>
                <icons icon="fe:arrow-down" class="el-icon--right" />
              </div>
            </el-button>
            <template #dropdown>
              <el-dropdown-menu slot="dropdown" class="bg-cyan-900 w-full">
                <template v-for="child in menu.children">
                  <el-dropdown-item :command="child.route"
                    class="text-white hover:bg-blue-500 py-3">
                    {{ child.title }}
                  </el-dropdown-item>
                </template>
              </el-dropdown-menu>
            </template>
          </el-dropdown>
        </template>
        <template v-else>
          <el-button class="bg-cyan-900 text-white font-bold w-full m-0 h-12 flex"
            @click="go(menu.route)">
            <span class="flex items-center">
              <icons :icon="menu.icon" class="text-current mr-2" />
              <span>{{ menu.title }}</span>
            </span>
          </el-button>
        </template>
      </template>
    </div>
  </div>
</template>

<script>
import { mapState } from 'pinia';
import menus from '@/helpers/menus.js';

export default {
	name: 'dashboard',
  computed:{
    ...mapGetters({
      user: 'loggedUser',
    }),
  },
  data: function() {
    return {
      menus: menus,
    };
  },
  methods: {
    go(route) {
      this.$router.push(route);
    },
  }
}
</script>

<style lang="sass" scoped>
.dashboard 
  display: flex 
  justify-content: center 
  align-items: center 
  flex-direction: column
  height: calc(100vh - 100px)
  .icon 
    font-size: 100px
    height: 100px
  .menu-wrap
    margin-top: 20px
    display: flex
    justify-content: center
    flex-wrap: wrap
    .el-button
      margin: 4px
  .note
    color: #999

</style>
<template>
  <el-popover placement="bottom-end" :visible="showNotif"
    :show-arrow="false"
    popper-class="text-white w-[250px] h-fit rounded-lg overflow-hidden bg-teal-700 p-1">
    <template #reference>
      <el-badge :value="unreadNotification" :max="99" :show-zero="false"
      badge-class="border-0 aspect-square text-[10px] w-fit top-1 leading-none">
        <icons id="icon-notif" icon="mdi:bell" class="mr-0 text-2xl text-white cursor-pointer"
          @click="showNotif = true"/>
      </el-badge>
    </template>
    <div class="absolute w-full h-full z-[-1] top-0 left-0
      bg-[length:340px] bg-repeat bg-bottom
      opacity-20"
      :style="{
        backgroundImage:`url('${$baseUrl}/assets/images/menu.png')`,
      }"/>
    <div class="my-1 z-[2]
      flex flex-col items-center"
      v-click-exclude-id:icon-notif="() => showNotif = false">
      <div class="text-[13px] w-full space-y-1">
        <div v-for="notif in notifications.slice(0, 5)"
          :class="['w-full cursor-pointer px-2 py-1 hover:bg-orange-50/[0.2] rounded-lg ',
            notif.status == '0' ? 'bg-cyan-300/[0.2]' : '']"
            @click="clickNotif(notif)">
          <div class="font-bold">{{ notif.judul }}</div>
          <div class="line-clamp-3" v-html="notif.pesan" />
        </div>
      </div>
    </div>
  </el-popover>
</template>

<script>
import { useNotifStore } from '@2/shared/config/stores/notifStore'
import { useAuthStore } from '@2/shared/config/stores/authStore'
import { mapState, mapActions } from 'pinia';

export default {
  name: 'NotificationList',
  data() {
    return {
      showNotif: false,
    }
  },
  computed: {
    ...mapState(useAuthStore, {
      user: 'loggedUser',
      role:'role',
      roles:'roles',
    }),
    ...mapState(useNotifStore, {
      notifications: 'allNotifications',
      unreadNotification:'unread',
    }),
  },
  methods: {
    ...mapActions(useNotifStore,{
      getAllNotification: 'getAllNotification',
      toggleRead: 'toggleRead',
    }),
    getAllNotif(){
      this.getAllNotification({
        where:{
          id_guru:this.user.id,
          send:'1',
        },
        order: ['send_at desc, status']
      })
    },
    async clickNotif(notif){
      await this.toggleRead(notif.id).then(
        res => {
          console.log(res, notif)
          this.getAllNotif()
          this.showNotif = false;
          this.$router.replace({name:notif.next_route, query: this.isEmpty(notif?.query) ? '' : JSON.parse(notif?.query)}); 
        } )
    }
  },
  mounted() {
  },
  created(){
    this.getAllNotif()
  },
}
</script>

<style scoped>
.notification-list {
  padding: 16px;
}
</style>
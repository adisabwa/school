import { defineStore } from 'pinia';
import { ref } from 'vue';
import { siteUrl } from "@2/shared/config/url"
import axios from "axios";
import jsonToFormData from 'json-form-data'


export const useNotifStore = defineStore('notif', {
  state: () => ({
    notifications:[],
    unread:0,
  }),
  getters: {
    allNotifications: state => state.notifications,
    unread() {
      return this.getUnread()
    },
  },
  actions: {
    getAllNotification(payload: any) {
      return new Promise((resolve, reject) => {
        axios({
          method: "POST",
          url: siteUrl + "/notification",
          data: jsonToFormData(payload),
        }).then(response => {
          this.notifications = response?.data
          resolve(response);
        }).catch(error => {
          this.notifications = []
          reject(error);
        });
      });
    },
    getUnread(){
      return this.notifications.filter(f => f?.status == '0')?.length ?? 0
    },
    toggleRead(id: any){
      return new Promise((resolve, reject) => {
        axios({
          method: "POST",
          url: siteUrl + "/notification/store",
          data: jsonToFormData({
            id:id,
            status:'1',
          }),
        }).then(response => {
          resolve(response);
        }).catch(error => {
          reject(error);
        });
      });
    }
  },
  persist: {
    key: 'school_app_notif_state',
    storage: localStorage, // atau sessionStorage
    sync: true,
    debug: true,
  },
})
import { defineStore } from 'pinia'
import { siteUrl } from "@/config/url"
import { listFunction } from "@/config/plugins/data-functions"
import axios from "axios";
import jsonToFormData from 'json-form-data'

let { setCookie, getCookie, deleteCookie } = listFunction

const AUTH_USER = 'logged_user';
const DEFAULT = JSON.stringify({nama:'',role:''});
const COOKIE_NAME = 'userData'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    userData: localStorage.getItem(AUTH_USER) ?? DEFAULT,
  }),
  getters: {
    loggedUser: state  => state?.userData ? JSON.parse(state.userData) : DEFAULT,
  },
  actions: {
    async login(payload: any, save = true) {
      return new Promise((resolve, reject) => {
        axios({
          method: "POST",
          url: siteUrl + "/auth/login",
          data: jsonToFormData(payload),
        }).then(response => {
          deleteCookie(COOKIE_NAME)
          const userData = JSON.stringify(response.data);
          this.setUserData(userData, save)
          resolve(response);
        }).catch(error => {
          this.clearUserData()
          reject(error);
        });
      }); 
    },
    changeRole(payload: any, save = true) {
      return new Promise((resolve, reject) => {
        axios({
          method: "POST",
          url: siteUrl + "/auth/change_role",
          data: jsonToFormData(payload),
        }).then(response => {
          deleteCookie(COOKIE_NAME)
          const userData = JSON.stringify(response.data);
          this.setUserData(userData, save)
          resolve(response);
        }).catch(error => {
          this.clearUserData()
          reject(error);
        });
      }); 
    },
    logout(payload: any) {
      return new Promise((resolve, reject) => {
        axios({
          method: "GET",
          url: siteUrl + "/auth/logout",
        }).then(response => {
          this.clearUserData()
          resolve(response);
        }).catch(error => {
          reject(error);
        });
      }); 
    },
    async checkUser(payload: any) {
      try {
        const response = await axios({
          method: "GET",
          url: siteUrl + "/auth/user",
        });
        const userData = JSON.stringify(response.data);
        this.setUserData(userData);
      } catch (error) {
        console.log(error);
        let data_1 = getCookie(COOKIE_NAME);
        if (data_1 !== null) {
          this.login(data_1);
        } else {
          this.clearUserData();
        }
      } 
    },
    setUserData(userData: string, save = true) {
      if (save) {
        setCookie(COOKIE_NAME, userData, 30)
        localStorage.setItem(AUTH_USER, userData);
      }
      this.userData = userData
    },
    clearUserData() {
      localStorage.removeItem(AUTH_USER);
      deleteCookie(COOKIE_NAME)
      this.userData = DEFAULT
    },
    switchAccount(payload: any) {
      let formData = jsonToFormData(payload)
      return new Promise((resolve, reject) => {
        axios({
          method: "POST",
          url: siteUrl + "/auth/switch_account",
          data: formData,
        }).then(response => {
          const userData = JSON.stringify(response.data);
          this.setUserData(userData)
          resolve(response);
        }).catch(error => {
          this.clearUserData()
          reject(error);
        });
      });
    },
    resetAccount(payload: any) {
      return new Promise((resolve, reject) => {
        axios({
          method: "GET",
          url: siteUrl + "/auth/reset",
        }).then(response => {
          const userData = JSON.stringify(response.data);
          this.setUserData(userData)
          resolve(response);
        }).catch(error => {
          this.clearUserData()
          reject(error);
        });
      });
    },
    restoreAccount(payload: any) {
      return new Promise((resolve, reject) => {
        axios({
          method: "GET",
          url: siteUrl + "/auth/restore_account",
        }).then(response => {
          const userData = JSON.stringify(response.data);
          this.setUserData(userData)
          resolve(response);
        }).catch(error => {
          this.clearUserData()
          reject(error);
        });
      });
    }
  },
})

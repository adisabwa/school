import { defineStore } from 'pinia'
import { siteUrl } from "@2/shared/config/url"
import { listFunction } from "@2/shared/config/plugins/data-functions"
import axios from "axios";
import jsonToFormData from 'json-form-data'
import { topMenu } from '@2/shared/helpers/menus'

let { setCookie, getCookie, deleteCookie, getDataFromStorage } = listFunction

const AUTH_USER = 'logged_user';
const DEFAULT = JSON.stringify({nama:''});
const COOKIE_NAME = 'userData'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    userData: localStorage.getItem(AUTH_USER) ?? DEFAULT,
    route: {},
    app:'admin',
  }),
  getters: {
    loggedUser: state  => state?.userData ? JSON.parse(state.userData) : DEFAULT,
    role() {
      return this.getRole()
    },
    roles() {
      return this.getRoles()
    },
  },
  actions: {
    setRoute(route: any) {
      this.route = route
    },
    async login(payload: any, save = true) {
      return new Promise((resolve, reject) => {
        axios({
          method: "POST",
          url: siteUrl + "/auth/g_login",
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
    async gLogin(payload: any, save = true) {
      return new Promise((resolve, reject) => {
        axios({
          method: "POST",
          url: siteUrl + "/auth/g_login",
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
    getRole(){
      let user = this.loggedUser
      let app = this.getApp()
      // console.log(user.app_roles, user.app_roles[app], app)
      if (!user.app_roles) return ''
      let role = user.app_roles[app] ?? user.app_roles['all'] ?? ''
      // console.log(role)
      return role
    },
    getRoles(){
      let user = this.loggedUser
      let _roles = []
      this.getApp()
      if (user.akses) {
        Object.values(user.akses).forEach((akses: any) => {
          akses.forEach((rl: any) => {
            _roles.push(rl.role)
          })
        })
      }
      _roles = [...new Set(_roles)]
      return _roles
    },
    getApp(app = null){
      const route =  this.route
      // console.log(route)
      console.log(route, app, route?.meta?.app , getDataFromStorage('menu') , 'admin')
      this.app = app ?? route?.meta?.app ?? getDataFromStorage('menu') ?? 'admin'
      console.log(this.app)
      return this.app
    },
    changeRole(payload: any, save = true) {
      const route =  this.route
      return new Promise((resolve, reject) => {
        axios({
          method: "POST",
          url: siteUrl + "/auth/change_role",
          data: jsonToFormData(payload),
        }).then(response => {
          deleteCookie(COOKIE_NAME)
          const userData = JSON.stringify(response.data);
          this.setUserData(userData, save)
          this.getApp()
          let menu = topMenu?.find(m => m.app == this.app)
          let nextRoute = menu?.route?.[this.getRole()] ?? 'dashboard'
          goTo(nextRoute, {
            _reload: Date.now() // dummy param to change URL
          })
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
        // console.log(error);
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
  },// Gunakan persist agar data tetap ada saat berpindah aplikasi
  persist: {
    key: 'school_app_auth_state',
    storage: localStorage, // atau sessionStorage
    // paths: ['userData'], // HANYA simpan userData, abaikan router & route
    sync: true,
    debug: true,
  },
})

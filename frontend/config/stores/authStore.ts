import { defineStore } from 'pinia'
import { siteUrl } from "@/config/url"
import axios from "axios";
import jsonToFormData from 'json-form-data'
import { isEmpty } from 'lodash';

let { setCookie, getCookie, deleteCookie, saveToStorage, getDataFromStorage, resetStorage } = useStorage()

const AUTH_USER = 'logged_user';
const DEFAULT = JSON.stringify({nama:'',role:''});
const COOKIE_NAME = 'userData'
const TOKEN = 'a28541aee1bb6660f4a7e91793a1ce91'

export const useAuthStore = defineStore('auth', 
  {
    state: () => ({
      userData: localStorage.getItem(AUTH_USER) ?? DEFAULT,
      route: {},
      router: {},
    }),
    getters: {
      loggedUser: state  => state?.userData ? JSON.parse(state.userData) : DEFAULT,
      role() {
        return this.getRole()
      },
      roles() {
        return this.getRoles()
      },
      app() {
        return this.getApp()
      }
    },
    actions: {
      setRoute(route: any) {
        this.route = route
      },
      setRouter(router: any) {
        this.route = router
      },
      async gLogin(payload: any, save = true) {
        this.clearUserData()
        return new Promise((resolve, reject) => {
          axios({
            method: "POST",
            url: siteUrl + "/auth/g_login",
            data: jsonToFormData(payload),
          }).then(response => {
            const userData = JSON.stringify(response.data);
            this.setUserData(userData, save)
            resolve(response);
          }).catch(error => {
            reject(error);
          });
        }); 
      },
      getRole(){
        let user = this.loggedUser
        let app = this.getApp()
        // console.log(user.app_roles, user.app_roles[app], app)
        if (isEmpty(user.nama)) return ''
        if (!user.app_roles) return 'guest'
        let role = user.app_roles[app] ?? user.app_roles['all'] ?? 'guest'
        // console.log(role)
        return role
      },
      getRoles(){
        let user = this.loggedUser
        let _roles = []
        let app = this.getApp()
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
      getApp(){
        const route =  this.route
        // console.log(route)
        // console.log(route, route?.meta?.app , getDataFromStorage('menu') , 'admin')
        return route?.meta?.app ?? getDataFromStorage('menu') ?? 'admin'
      },
      changeRole(payload: any, save = true) {
        const route =  this.route
          this.clearUserData()
        return new Promise((resolve, reject) => {
          axios({
            method: "POST",
            url: siteUrl + "/auth/change_role",
            data: jsonToFormData(payload),
          }).then(response => {
            const userData = JSON.stringify(response.data);
            this.setUserData(userData, save)
            // if (this.router) {
            //   this.router.replace({
            //     path: route.path,
            //     query: {
            //       ...route.query,
            //       _reload: Date.now() // dummy param to change URL
            //     }
            //   })
            // }
            resolve(response);
          }).catch(error => {
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
          let data_2 = getDataFromStorage(AUTH_USER)[0];
          if (!isEmpty(data_1)) {
            data_1 = JSON.parse(data_1)
            console.log('data', data_1)
            this.gLogin({id: data_1?.id, token: TOKEN});
          } else if (!isEmpty(data_2)) {
            console.log('data 2', data_2)
            data_2 = JSON.parse(data_2)
            this.gLogin({id: data_2?.id, token: TOKEN});
          } else {
            this.clearUserData();
          }
        } 
      },
      setUserData(userData: string, save = true) {
        if (save) {
          setCookie(COOKIE_NAME, userData, 30)
          saveToStorage(AUTH_USER, userData)
        }
        this.userData = userData
      },
      clearUserData() {
        resetStorage(AUTH_USER)
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
  }
)

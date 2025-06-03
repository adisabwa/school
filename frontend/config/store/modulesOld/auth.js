import axios from "axios";
import { siteUrl } from "@/config/url"
import { listFunction } from "@/config/plugins/data-functions"

let { setCookie, getCookie, deleteCookie } = listFunction

const AUTH_USER = 'logged_user';
const DEFAULT = JSON.stringify({nama:'',role:''});
const COOKIE_NAME = 'userData'

const state = {
  loggedUser: localStorage.getItem(AUTH_USER) ? localStorage.getItem(AUTH_USER) : DEFAULT,
};

const actions = {
  login: (context, payload, save = true) => {
    return new Promise((resolve, reject) => {
      axios({
        method: "POST",
        url: siteUrl + "/auth/login",
        data: window.jsonToFormData(payload),
      }).then(response => {
        deleteCookie(COOKIE_NAME)
        const loggedUser = JSON.stringify(response.data);
        if (save)
          setCookie(COOKIE_NAME, payload, 30)
        localStorage.setItem(AUTH_USER, loggedUser);
        context.commit("LOGGED_USER_UPDATE", loggedUser);
        resolve(response);
      }).catch(error => {
        localStorage.removeItem(AUTH_USER);
        reject(error);
      });
    }); 
  },
  changeRole: (context, payload, save = true) => {
    return new Promise((resolve, reject) => {
      axios({
        method: "POST",
        url: siteUrl + "/auth/change_role",
        data: window.jsonToFormData(payload),
      }).then(response => {
        deleteCookie(COOKIE_NAME)
        const loggedUser = JSON.stringify(response.data);
        if (save)
          setCookie(COOKIE_NAME, payload, 30)
        localStorage.setItem(AUTH_USER, loggedUser);
        context.commit("LOGGED_USER_UPDATE", loggedUser);
        resolve(response);
      }).catch(error => {
        localStorage.removeItem(AUTH_USER);
        reject(error);
      });
    }); 
  },
  logout: (context, payload) => {
    return new Promise((resolve, reject) => {
      axios({
        method: "GET",
        url: siteUrl + "/auth/logout",
      }).then(response => {
        localStorage.removeItem(AUTH_USER);
        context.commit("LOGGED_USER_UPDATE", DEFAULT);
        deleteCookie(COOKIE_NAME)
        resolve(response);
      }).catch(error => {
        reject(error);
      });
    }); 
  },
  checkUser: (context) => {
    return axios({
      method: "GET",
      url: siteUrl + "/auth/user",
    }).then(response => {
      const loggedUser = JSON.stringify(response.data);
      localStorage.setItem(AUTH_USER, loggedUser);
      context.commit("LOGGED_USER_UPDATE", loggedUser);
    }).catch(error => {
      console.log(error);
      localStorage.removeItem(AUTH_USER);
      context.commit("LOGGED_USER_UPDATE", DEFAULT);
      let data = getCookie(COOKIE_NAME)
      if (data !== null) {
        context.dispatch('login', data)
      }
    }); 
  },
  setUserData: (context, loggedUser) => {
    localStorage.setItem(AUTH_USER, loggedUser);
    context.commit("LOGGED_USER_UPDATE", loggedUser);
  },
  clearUserData: (context) => {
    localStorage.removeItem(AUTH_USER);
    context.commit("LOGGED_USER_UPDATE", DEFAULT);
  },
  switchAccount: (context, id) => {
    const formData = new FormData();
    
    formData.append('id', id);

    return new Promise((resolve, reject) => {
      axios({
        method: "POST",
        url: siteUrl + "/auth/switch_account",
        data: formData,
      }).then(response => {
        const loggedUser = JSON.stringify(response.data);

        localStorage.setItem(AUTH_USER, loggedUser);

        context.commit("LOGGED_USER_UPDATE", loggedUser);
        resolve(response);
      }).catch(error => {
        localStorage.removeItem(AUTH_USER);
        reject(error);
      });
    });
  },
  resetAccount: (context) => {
    return new Promise((resolve, reject) => {
      axios({
        method: "GET",
        url: siteUrl + "/auth/reset",
      }).then(response => {
        const loggedUser = JSON.stringify(response.data);

        localStorage.setItem(AUTH_USER, loggedUser);

        context.commit("LOGGED_USER_UPDATE", loggedUser);
        resolve(response);
      }).catch(error => {
        localStorage.removeItem(AUTH_USER);
        reject(error);
      });
    });
  },
  restoreAccount: (context) => {
    return new Promise((resolve, reject) => {
      axios({
        method: "GET",
        url: siteUrl + "/auth/restore_account",
      }).then(response => {
        const loggedUser = JSON.stringify(response.data);

        localStorage.setItem(AUTH_USER, loggedUser);

        context.commit("LOGGED_USER_UPDATE", loggedUser);
        resolve(response);
      }).catch(error => {
        localStorage.removeItem(AUTH_USER);
        reject(error);
      });
    });
  }
};

const mutations = {
  LOGGED_USER_UPDATE: (state, loggedUser) => {
    state.loggedUser = loggedUser;
  },
  LOGIN_DIALOG_UPDATE: (state, status) => {
    state.loginDialog = status;
  },
};

const getters = {
  loggedUser: state  => state.loggedUser ? JSON.parse(state.loggedUser) : DEFAULT,
};

export default {
  state,
  getters,
  actions,
  mutations,
};

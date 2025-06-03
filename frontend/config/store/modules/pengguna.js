import axios from "axios";

import { siteUrl } from "@/config/url"

const namespaced = true;

const state = {
  penggunas: [],
  pengguna: {},
};

const actions = {
  getAll: (context) => {
    return new Promise((resolve, reject) => {
      axios({
        method: "GET",
        url: siteUrl + "pengguna/index",
      }).then(response => {
        context.commit("USERS_UPDATE", response.data);
        resolve(response);
      }).catch(error => {
        reject(error);
      });
    }); 
  },
  get: (context, { id }) => {
    return new Promise((resolve, reject) => {
      axios({
        method: "GET",
        url: siteUrl + "pengguna/get",
        params: {id : id}
      }).then(response => {
        context.commit("USER_UPDATE", response.data);
        resolve(response);
      }).catch(error => {
        reject(error);
      });
    }); 
  },
  store: (context, data) => {
    return new Promise((resolve, reject) => {
      axios({
        method: "POST",
        url: siteUrl + "pengguna/store",
        data: window.jsonToFormData(data),
      }).then(response => {
        context.commit("USER_UPDATE", response.data);
        resolve(response);
      }).catch(error => {
        reject(error);
      });
    }); 
  },
};

const mutations = {
  USERS_UPDATE: (state, penggunas) => {
    state.penggunas = penggunas;
  },
  USER_UPDATE: (state, pengguna) => {
    state.pengguna = pengguna;
  }
};

const getters = {
  penggunas: state  => state.penggunas,
  pengguna: state  => state.pengguna,
};

export default {
  namespaced,
  state,
  getters,
  actions,
  mutations,
};

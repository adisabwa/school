import { defineStore } from 'pinia'
import { siteUrl } from "@2/shared/config/url"
import { ElMessage, ElMessageBox } from 'element-plus'
import { ElNotification } from 'element-plus'
import axios from "axios";

export const useDataStore = defineStore('data', {
  state: () => ({
    anggota: {},
    anggotas: [],
    filters:{},
    datas:{},
    template:'main',
    layout:'vertical',
  }),
  getters: {
    
  },
  actions: {
    setTemplate(type) {
      this.template = type;
    },
    setLayout(type) {
      this.layout = type;
    },
    async getAllAnggotaInGroup(payload) {
      // console.log(payload);
      return new Promise((resolve, reject) => {
        axios({
          method: "GET",
          url: siteUrl + "data/group/get_anggota",
          params: payload,
        }).then(response => {
          this.anggotas = response?.data
          resolve(response);
        }).catch(error => {
          reject(error);
        });
      }); 
    },
    getAnggota(payload) {
      return new Promise((resolve, reject) => {
        axios({
          method: "GET",
          url: siteUrl + "data/anggota/get/",
          params: payload,
        }).then(response => {
          this.anggota = response?.data
          resolve(response);
        }).catch(error => {
          reject(error);
        });
      }); 
    },
    deleteData({ href, id }) {
      return new Promise((resolve, reject) => {
        ElMessageBox.confirm('Apakah anda yakin untuk menghapus data ini?',
          'Konfirmasi',
          {
            confirmButtonText: 'OK',
            cancelButtonText: 'Batal',
            type: 'warning',
          })
          .then(() => {
            axios({
              method: "GET",
              url: siteUrl + href + '/' + id,
              params: {
                id:id
              },
            }).then(result => {
                ElNotification({
                  type:'success',
                  title: 'Berhasil',
                  message: 'Data berhasil dihapus',
                  position: 'bottom-right'
                });
                resolve(result);
              })
              .catch(err => {
                ElNotification({
                  type:'error',
                  title: 'Gagal',
                  message: 'Tidak dapat menghapus data',
                  position: 'bottom-right'
                });
                reject(err);
              });
            })
            .catch(err => {
              console.log(err)
              reject(err);
            });
      }); 
    },
    setFilter({ key, val}){
      // console.log(this.filter)
      this.filters[key] = val
    },
    setDatas({ key, val}){
      // console.log(this.filter)
      this.datas[key] = val
    }
  },
  persist: {
    key: 'school_app_data_state',
    storage: localStorage, // atau sessionStorage
    sync: true,
    debug: true,
  },
})


<template>
	<div class="p-10  justify-center items-center bg-white/[0.7]
    flex flex-col gap-6 ">
      <div class="flex flex-col sm:flex-row gap-6 w-full
        *:h-20 *:w-full *:text-[24px] *:m-0 *:font-bold">
        <el-button @click="$router.replace({name:'tsdac-sekretaris'})" type="primary">Sekretaris</el-button>
        <el-button @click="setMatch" type="primary">Match Setting</el-button>
      </div>
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 *:m-0 *:h-20 *:w-full *:text-[24px] w-full">
        <el-button @click="setJuri(1)" type="success">Juri 1</el-button>
        <el-button @click="setJuri(2)" type="success">Juri 2</el-button>
        <el-button @click="setJuri(3)" type="success">Juri 3</el-button>
        <el-button @click="setJuri(4)" type="success">Juri 4</el-button>
      </div>
  </div>
</template>

<script>
import { mapState } from 'pinia'
import md5 from 'js-md5'; // or require('js-md5')
import { useAuthStore } from '@/config/stores/authStore'
import { useDataStore } from '@/config/stores/dataStore'

export default {
  name: "default",
  components: {
  },
  computed:{
    ...mapState(useAuthStore,{
      user:'loggedUser',
      role:'role',
      roles:'roles',
    }),
  },
  data: function() {
    return {
    };
  },
  methods:{
    doGLogin(result) {
      this.loading = true;
      useAuthStore().gLogin({credential:result.credential}, true)
        .then(res => {
          this.loading = false;
          this.$router.replace({name:'dashboard'});
          this.getDataFilter()
        }).catch(err => {
          this.loading = false;
          // console.log(err)
          const res = err.response;
          if (res.status == 401) {
            useDataStore().setFilter({key:'email', val:res?.data?.email})
            this.$router.push({name:'register'})
          } else {
            this.$notify.error({
              title: 'Gagal',
              message: 'Terjadi kesalahan pada server',
              position: 'bottom-right'
            });
          }
        });
    },
    setMatch(){
      this.$router.replace({name:'tsdac-setting'})
      return
      this.$http.post('ekstra/ts/tsdac/penilaian/get_current_match')
        .then(res => {
          let data = res.data
          let key = this.getDataFromStorage('key_match')
          if (!isEmpty(data.key_match)) {
            if (key == data.key_match)
              this.$router.replace({name:'tsdac-setting'})
            else
              this.$notify({
                type:'error',
                title:'Kesalahan Input',
                message:'Admin Pertandingan sudah masuk ke aplikasi',
              })
          } else {
            let set = {}
            set.key_match = md5(this.dateNow() + ' ' + this.timeNow());
            this.resetStorage('key_match')
            this.saveToStorage('key_match',set.key_match)
            set = window.jsonToFormData(set)
            this.$http.post('ekstra/ts/tsdac/penilaian/set_current_match', set)
              .then(res => {
                this.$router.replace({name:'tsdac-setting'})
              })
            }
        })
    },
    setJuri(no){
      this.goTo(no)
      return
      let key_juri = `key_juri_${no}`
      this.$http.post('ekstra/ts/tsdac/penilaian/get_current_match')
        .then(res => {
          let data = res.data
          let key = this.getDataFromStorage('key_juri')
          let exist = -1
          for(let no_juri in this.range(4,1 )) {
            if (data[`key_juri_${no_juri}`])
              exist = no_juri
          }
          // console.log(key_juri, data[key_juri], isEmpty(data[key_juri]))
          if (!isEmpty(data[key_juri])) {
            if (key == data[key_juri])
              this.goTo(no)
            else
              this.$notify({
                type:'error',
                title:'Kesalahan Input',
                message:'Juri ini sudah masuk ke aplikasi',
              })
          } else {
            if (exist > 0) {
              this.$notify({
                type:'error',
                title:'Kesalahan Input',
                message:'Anda sudah Masuk Sebagai Juri ke-' + exist,
              })
            } else {
              let set = {}
              set[key_juri] = md5(no + ' ' + this.dateNow() + ' ' + this.timeNow());
              this.resetStorage('key_juri')
              this.saveToStorage('key_juri',set[key_juri])
              set = window.jsonToFormData(set)
              this.$http.post('ekstra/ts/tsdac/penilaian/set_current_match', set)
                .then(res => {
                  this.goTo(no)
                })
              }
            }
        })
    },
    goTo(no){
      this.$router.replace({name:'tsdac-nilai', query:{juri:no}})
      // console.log('go to')
    }
  },
	
}
</script>
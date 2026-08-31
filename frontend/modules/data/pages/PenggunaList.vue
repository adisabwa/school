<template>
  <div id="pengguna-list" >
    <div class="w-full mt-3
      grid grid-cols-2 [&_*]:m-0
      gap-x-3 gap-y-2
      sm:block sm:[&_*]:mr-3">
      <el-button class="py-[15px]" type="success" size="small" @click="$refs.tableData.handleActionClick({action:'add'})">
        <icons icon="mdi:plus"/>
        Buat Baru</el-button>
      <el-button class="py-[15px]" type="primary" size="small" @click="$refs.tableData.handleActionClick({action:'edit-all', pass:['password','passwordconf']})">
        <icons icon="mdi:edit"/>
        Edit Bersama</el-button>
      <el-button class="py-[15px]" type="danger" size="small" @click="$refs.tableData.deleteMany()">
        <icons icon="mdi:trash"/>
        Delete Checklist</el-button>
    </div>
    <div class="pt-1">
      <table-data ref="tableData" :fields="fields" href="data/pengguna"
        :checked="true" 
        :params="{
          offset:0, limit:0,
        }"
        :show-dropdown="true" :show-create="false">
      </table-data>
    </div>
  </div>
</template>
  
  <script>
    
    import { reactive } from 'vue';
    import { mapActions, mapState } from 'pinia';
import { useAuthStore } from '@/config/stores/authStore'
  
  export default {
    name: "pengguna-list",
    props:{
      type:'',
      showCreate:{
        type:Boolean,
        default: true,
      },
      showSearch:{
        type:Boolean,
        default: true,
      },
    },
    components: {
      
    },
    data: function() {
      return {
        data:{},
        fields:[],
        state: reactive({
          passColumns : [],
          showColumns : [],
        })
      };
    },
    provide() {
      return {
        sharedState: this.state
      }
    },
    watch: {
     
      
    },
    computed: {
      ...mapState(useAuthStore,{
        user:'loggedUser'
      })
    },
    methods: {
      getInitial: async function() {
          this.loading = true;
          await this.$http.get('/kolom/preparation?table=' + this.$prefixTable + '_pengguna_akses&grouping=0&input=0')
            .then(result => {
              var res = result.data;
              this.fields = res
              this.fields.id_akses.span = 4
              this.fields.role.span = 2
              this.fields.id_guru.defaultData = 'Semua Guru'
              this.fields.id_guru.emptyValue = 'Semua Guru'
              this.loading = false
             //  this.$refs.tableData.getData()
            });
        },
    },
    created: function() {
      this.getInitial();}
  }
  </script>
  
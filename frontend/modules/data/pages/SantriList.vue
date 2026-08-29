<template>
  <div id="santri-list" class="pt-1" v-loading="loading">
    <table-data ref="tableData" :fields="fields" href="data/santri"
      :checked="true"  :show-columns="['nama','nisn','status','gender']"
      :params="tableParams">
      <el-table-column label="Foto Santri" width="200px" align="center">
        <template #default="scope">
          <div v-if="isEmpty(scope.row.foto)"
            class="text-center">
            <el-button type="primary" @click="$refs.tableData.handleActionClick({action:'edit', id:scope.row.id}, ['foto'])"
              size="small">
              Upload Foto
            </el-button>
          </div>
          <div v-else
            class="text-center">
            <img :src="scope.row.foto" height="40px" />
          </div>
        </template>
      </el-table-column>
    </table-data>
  </div>
</template>
  
  <script>
    
    import { reactive } from 'vue';
    import { mapActions, mapState } from 'pinia';
import { useAuthStore } from '@/config/stores/authStore'
  
  export default {
    name: "santri-list",
    setup(){
      return {
        isEmpty,
      }
    },
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
        loading:false,
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
      }),
      tableParams() {
        return {
          where: this.user.role === 'super-admin' ? {} : { bidang: this.user.bidang },
          offset: 0,
          limit: 0
        };
      }
    },
    methods: {
      getInitial: async function() {
          this.loading = true;
          await this.$http.get('/kolom/preparation?table=' + this.$prefixTable + '_santri&grouping=0&input=0')
            .then(result => {
              var res = result.data;
              this.fields = res
              this.loading = false
              this.$nextTick(() => {
               //  this.$refs.tableData.getData()
              })
            });
        },
    },
    created: function() {
      this.getInitial();
    }
  }
  </script>
  
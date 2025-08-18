<template>
  <div id="guru-list" class="pt-1" v-loading="loading">
    <table-data ref="tableData" :fields="fields" href="data/guru"
      :checked="true"  :pass-columns="['prefix','suffix']"
      :pass-columns-input="[]"
      :params="tableParams">
      <template #nama-inside="{ scope }">
       {{ scope.row.prefix }}
       {{ scope.row.nama }}
       {{ scope.row.suffix }}
      </template>
    </table-data>
  </div>
</template>
  
  <script>
    
    import { reactive } from 'vue';
    import { mapActions, mapState } from 'pinia';
  
  export default {
    name: "guru-list",
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
          await this.$http.get('/kolom/preparation?table=sch__guru&grouping=0&input=0')
            .then(result => {
              var res = result.data;
              this.fields = res
              this.fields.nama.hide_content = true
              this.fields.prefix.span = 3
              this.fields.suffix.span = 3
              this.loading = false
              this.$nextTick(() => {
                this.$refs.tableData.getData()
              })
            });
        },
    },
    created: function() {
      this.getInitial();
    }
  }
  </script>
  
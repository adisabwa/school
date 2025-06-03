<template>
  <div id="unit-list" class="pt-1" v-loading="loading">
    <table-data ref="tableData" :fields="fields" href="data/unit"
      :checked="true" :upload="false" :pass-columns="[]"
      :params="tableParams">
    </table-data>
  </div>
</template>
  
  <script>
    import TableData from '@/components/TableData.vue'
    import { reactive } from 'vue';
    import { mapActions, mapState } from 'pinia';
  
  export default {
    name: "unit-list",
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
      TableData,
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
          await this.$http.get('/kolom/preparation?table=mu__unit_kerja&grouping=0&input=0')
            .then(result => {
              var res = result.data;
              this.fields = res
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
  
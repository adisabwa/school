<template>
  <div class="mx-3 sm:mx-10 pt-6">
    <div id="pengguna-list" class="py-6">
      <table-data ref="tableData" :fields="fields" href="pengguna"
      :checked="true" :upload="false"
      :pass-columns="['username','password']"
      title="Data Pengguna">
      </table-data>
    </div>
  </div>
</template>
  
  <script>
    import TableData from '@/components/TableData.vue'
  
  export default {
    name: "data-list",
    props:{
      type:'',
    },
    components: {
      TableData,
    },
    data: function() {
      return {
        fields:[],
      };
    },
    watch: {
     
      
    },
    computed: {
      
    },
    methods: {
      getInitial: async function() {
          this.loading = true;
          await this.$http.get('/kolom/preparation?table=pdm_pengguna&grouping=0&input=0')
            .then(result => {
              var res = result.data;
              this.fields = res
              this.loading = false
              this.$refs.tableData.getData()
            });
        },
    },
    mounted: function() {
      this.getInitial();
    }
  }
  </script>
  
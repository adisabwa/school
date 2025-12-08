<template>
  <div id="guru-list" class="pt-1" v-loading="loading">
    <el-dialog
      
      append-to-body
      title="Upload Tanda Tangan"
      v-model="showUpload"
      class="min-w-[400px] max-w-1/2"
      @opened="$refs.signaturePad.resizeCanvas()"
      >
      <SignaturePad ref="signaturePad"
        @saved="showUpload=false;
          $refs.tableData.getData();"
        @error="showUpload = false"
        href="data/guru/store" :params="{
            id:dataId,
            old_signature:signature,
          }"/>
    </el-dialog>
    <table-data ref="tableData" :fields="fields" href="data/guru"
      :checked="true"  :pass-columns="['prefix','suffix','signature']"
      :showCreate="role == 'admin'" :showUpload="role == 'admin'"
      :pass-columns-input="[]"
      :dropdownItemProps="{
        delete: { show: false }
      }"
      :params="tableParams">
      <template #nama-inside="{ scope }">
       {{ scope.row.prefix }}
       {{ scope.row.nama }}
       {{ scope.row.suffix }}
      </template>
      <el-table-column
        label="Tanda Tangan"
        width="180"
        align="center">
        <template #default="{ row }">
          <img v-if="row.signature"
            :src="row.signature" 
            :alt="`Tanda Tangan`" 
            class="max-h-12 mx-auto"/>
        </template>
      </el-table-column>
      <template #button-addition="{ scope, field}">
        <el-button class="mt-2" size="small" type="success" @click="showUpload=true;dataId=scope.row.id;signature=scope.row.signature;"
          >
          <icons icon="mdi:upload"/> Gambar TTD</el-button>
      </template>
    </table-data>
  </div>
</template>
  
  <script>
    
    import { reactive } from 'vue';
    import { mapActions, mapState } from 'pinia';
  import SignaturePad from '@/components/SignaturePad.vue';
  
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
      SignaturePad,
    },
    data: function() {
      return {
        showUpload:false,
        loading:false,
        data:{},
        fields:[],
        state: reactive({
          passColumns : [],
          showColumns : [],
        }),
        dataId:-1,
        signature:'',
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
        user:'loggedUser',
        role:'role',
      }),
      tableParams() {
        return {
          where: this.role === 'admin' ? {} : { id: this.user.id },
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
  
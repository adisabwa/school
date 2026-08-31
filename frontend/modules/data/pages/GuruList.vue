<template>
  <div id="guru-list" class="pt-1" v-loading="loading">
    <el-dialog
      
      append-to-body
      title="Gambar Tanda Tangan"
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
    <el-dialog
      
      append-to-body
      title="Upload Tanda Tangan"
      v-model="showUploadAuto"
      class="min-w-[400px] max-w-1/2"
      >
      <SignatureAutoDetect ref="signatureAutoDetect"
        @saved="showUploadAuto=false;
          $refs.tableData.getData();"
        @error="showUploadAuto = false"
        href="data/guru/store" :params="{
            id:dataId,
            old_signature:signature,
          }"/>
    </el-dialog>
    <table-data ref="tableData" :fields="fields" href="data/guru"
      :checked="true"  :pass-columns="['prefix','suffix','signature']"
      :showCreate="role == 'admin'" :showUpload="role == 'admin'"
      :pass-columns-input="['signature']"
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
        <el-dropdown trigger="click" class="mt-2">
          <el-button size="small" type="primary">
            Tambah TTD <icons icon="mdi:chevron-down" class="ml-1"/>
          </el-button>
          <template #dropdown>
            <el-dropdown-menu>
              <el-dropdown-item @click="showUpload=true;dataId=scope.row.id;signature=scope.row.signature;">
                <icons icon="mdi:pen" class="mr-2"/> Gambar Tanda Tangan
              </el-dropdown-item>
              <el-dropdown-item @click="showUploadAuto=true;dataId=scope.row.id;signature=scope.row.signature;">
                <icons icon="mdi:upload" class="mr-2"/> Upload Tanda Tangan
              </el-dropdown-item>
            </el-dropdown-menu>
          </template>
        </el-dropdown>
      </template>
    </table-data>
  </div>
</template>
  
<script>
    
  import { reactive } from 'vue';
  import { mapActions, mapState } from 'pinia';
  import SignaturePad from '@/components/SignaturePad.vue';
import { useAuthStore } from '@/config/stores/authStore'
import SignatureAutoDetect from '@/components/SignatureAutoDetect.vue';
  
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
      SignatureAutoDetect,
    },
    data: function() {
      return {
        showUpload:false,
        showUploadAuto:false,
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
          await this.$http.get('/kolom/preparation?table=' + this.$prefixTable + '_guru&grouping=0&input=0')
            .then(result => {
              var res = result.data;
              this.fields = res
              this.fields.nama.hide_content = true
              this.fields.prefix.span = 3
              this.fields.suffix.span = 3
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
  
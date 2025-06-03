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
      <table-data ref="tableData" :fields="fields" href="data/anggota"
        :checked="true" :upload="false" :pass-columns="['tempat_lahir','tanggal_lahir','password','passwordconf','alamat','photo','id_unit','no_hp','email']"
        :params="{where: user.role == 'super-admin' ? {} : {id_unit:user.id_unit},
          offset:0, limit:0
        }"
        :show-dropdown="false" :show-create="false">
        <el-table-column label="Foto Anggota" width="200px">
          <template #default="scope">
            <div v-if="isEmpty(scope.row.photo)"
              class="text-center">
              <el-button type="primary" @click="$refs.tableData.handleActionClick({action:'edit', id:scope.row.id,cols: ['photo']});"
                size="small">
                Upload Foto
              </el-button>
            </div>
            <div v-else
              class="text-center">
              <img :src="scope.row.photo" height="40px" />
            </div>
          </template>
        </el-table-column>
        <el-table-column
          width="120">
          <template #default="scope">
            <el-dropdown trigger="click" @command="$refs.tableData.handleActionClick">
              <el-button type="primary" size="small"> 
                Aksi <icons icon="fe:arrow-down" class="el-icon--right" />
              </el-button>
              <template #dropdown>
                <el-dropdown-menu slot="dropdown">
                  <el-dropdown-item 
                    :command="{action: 'edit', id: scope.row.id, pass:['password','passwordconf']}"
                    @click="">
                    <icons icon="material-symbols:edit-outline"/> Ubah</el-dropdown-item>
                  <el-dropdown-item 
                    :command="{action: 'edit', id: scope.row.id, pass:[], cols: ['password','passwordconf']}">
                    <icons icon="mdi:restart"/> Reset Password</el-dropdown-item>
                  <el-dropdown-item 
                    :command="{action: 'delete', id: scope.row.id}">
                    <icons icon="material-symbols:delete-outline"/> Hapus</el-dropdown-item>
                </el-dropdown-menu>
              </template>
            </el-dropdown>
          </template>
        </el-table-column>
      </table-data>
    </div>
  </div>
</template>
  
  <script>
    import TableData from '@/components/TableData.vue'
    import { reactive } from 'vue';
    import { mapActions, mapState } from 'pinia';
  
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
      TableData,
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
          await this.$http.get('/kolom/preparation?table=mu_anggota&grouping=0&input=0')
            .then(result => {
              var res = result.data;
              this.fields = res
              this.loading = false
              this.$refs.tableData.getData()
            });
        },
    },
    created: function() {
      this.getInitial();}
  }
  </script>
  
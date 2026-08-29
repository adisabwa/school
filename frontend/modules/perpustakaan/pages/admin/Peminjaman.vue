<template>
  <div id="peminjaman-list" class="pt-1" v-loading="loading">
    <table-data ref="tableData" :fields="fields" href="perpustakaan/admin/peminjaman"
      :show-upload="false" :show-create="false" :show-dropdown="false"
      v-model:form-value="formValue"
      :checked="true"  :pass-columns="['id_guru','id_santri','id_tamu']" :pass-columns-input="passColumnInput"
      :params="tableParams"
      @changedFormValue="changedFormValue"
      label-width="170px"
      :addValues="addValues"
      >
      <template #menu="{ action }">
        <el-button class="py-[15px]" type="success" size="small" @click="action({action:'add'}); show='borrow'; addValues = {}">
          <icons icon="mdi:plus"/>
          Tambah Peminjaman Baru</el-button>
      </template>
      <template #before-id_buku="{ fields, field }">
        <el-table-column min_width="160px" label="Nama Peminjam">
            <template #default="scope">
              {{ runFunction({
                data: scope.row.peminjam == 'guru' ? scope.row.id_guru : scope.row.peminjam == 'santri' ? scope.row.id_santri : scope.row.id_tamu,
                options: scope.row.peminjam == 'guru' ? fields.id_guru.options : scope.row.peminjam == 'santri' ? fields.id_santri.options : fields.id_tamu.options,
              }) }}
            </template>
        </el-table-column>
      </template>
      <template #status-inside="{ scope }">
        <el-tag :type="scope.row.status == '1' ? 'success' : scope.row.status == '-1' ? 'danger' :  'primary'"  effect="dark">
          {{ scope.row.status == '1' ? 'Dikembalikan' : scope.row.status == '-1' ? 'Hilang' :  'Dipinjam'}}
        </el-tag>
      </template>
      <template #default="{ handleActionClick }">
        <el-table-column
          width="150px" align="center">
          <template #default="scope">
            <el-dropdown trigger="click" @command="handleActionClick" class="max-sm:[&_*]:text-[11px] ">
              <el-button type="primary" size="small" class=""> 
                Aksi <icons icon="fe:arrow-down" class="el-icon--right text-[11px] m-0 ml-1" />
              </el-button>
              <template #dropdown>
                <el-dropdown-menu slot="dropdown" class="max-sm:[&_*]:text-[12px]">
                  <el-dropdown-item class="py-[2px] text-success"
                    @click="show = 'return';addValues.status = '1'"
                    :command="{action: 'edit', id: scope.row.id}">
                    <icons icon="fontisto:arrow-return-left"/> Kembalikan Buku</el-dropdown-item>
                  <el-dropdown-item class="py-[2px]"
                    @click="show = 'return'; showPeminjamOnly(scope.row.peminjam)"
                    :command="{action: 'view', id: scope.row.id}">
                    <icons icon="lsicon:view-filled"/> Detail Peminjaman</el-dropdown-item>
                  <el-dropdown-item class="py-[2px]"
                    v-if="scope.row.status == '0'"
                    @click="show = 'borrow'; showPeminjamOnly(scope.row.peminjam); addValues = {}"
                    :command="{action: 'edit', id: scope.row.id}">
                    <icons icon="material-symbols:edit-outline"/> Ubah</el-dropdown-item>
                  <el-dropdown-item class="py-[2px]"
                    v-if="scope.row.status == '0'"
                    :command="{action: 'delete', id: scope.row.id}">
                    <icons icon="material-symbols:delete-outline"/> Hapus</el-dropdown-item>
                  <el-dropdown-item class="py-[2px] text-red-500"
                    @click="show = 'return'; addValues.status = '-1'"
                    :command="{action: 'edit', id: scope.row.id}">
                    <icons icon="material-symbols:computer-cancel-rounded"/> Buku Hilang</el-dropdown-item>
                </el-dropdown-menu>
              </template>
            </el-dropdown>
          </template>
        </el-table-column>
      </template>
    </table-data>
  </div>
</template>
  
  <script>
    
    import { reactive } from 'vue';
    import { mapActions, mapState } from 'pinia';
import { useAuthStore } from '@/config/stores/authStore'
  
  export default {
    name: "peminjaman-list",
    setup() {
      return {
        runFunction,
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
        formValue:{},
        show:'',
        state: reactive({
          passColumns : [],
          showColumns : [],
        }),
        addValues:{},
      };
    },
    provide() {
      return {
        sharedState: this.state
      }
    },
    watch: {
      show(val) {
        if (val == 'return') {
          this.fields.peminjam.readonly = true
          this.fields.id_guru.readonly = true
          this.fields.id_santri.readonly = true
          this.fields.id_tamu.readonly = true
          this.fields.id_buku.readonly = true
          this.fields.tanggal_peminjaman.readonly = true
          
        } else {
          this.fields.peminjam.readonly = false
          this.fields.id_guru.readonly = false
          this.fields.id_santri.readonly = false
          this.fields.id_tamu.readonly = false
          this.fields.id_buku.readonly = false
          this.fields.tanggal_peminjaman.readonly = false
        }
      }
    },
    computed: {
      ...mapState(useAuthStore,{
        user:'loggedUser'
      }),
      tableParams() {
        return {
        
          offset: 0,
          limit: 0
        };
      },
      passColumnInput(){
        let pass = [];
        if (this.formValue.peminjam == 'guru') pass = ['id_santri','id_tamu'];
        if (this.formValue.peminjam == 'santri') pass = ['id_guru','id_tamu'];
        if (this.formValue.peminjam == 'tamu') pass = ['id_santri','id_guru'];

        pass = [...pass, ...(this.show == 'borrow' ? ['tanggal_pengembalian','denda'] : [])]
        return pass
      }
    },
    methods: {
      getInitial: async function() {
          this.loading = true;
          await this.$http.get('/kolom/preparation?table=' + this.$prefixTable + 'lib_peminjaman&grouping=0&input=0')
            .then(result => {
              var res = result.data;
              this.fields = res
              this.fields.status.hide_content = true
              this.fields.id_santri.hidden = true
              this.fields.id_tamu.hidden = true
              this.formValue.id_santri = -1
              this.formValue.id_tamu = -1
              this.loading = false
              this.$nextTick(() => {
                this.$refs.tableData.getData()
              })
            });
        },
      changedFormValue({field, value}){
        console.log(field, value)
        if (field == 'peminjam') {
          this.showPeminjamOnly(value)
        }
      },
      showPeminjamOnly(value)
      {
        if (value == 'guru') {
          this.formValue.id_tamu = this.formValue.id_santri = null
          this.formValue.id_guru = null
          this.fields.id_guru.hidden = false
          this.fields.id_tamu.hidden = this.fields.id_santri.hidden = true
        } else if (value == 'santri') {
          this.formValue.id_tamu = this.formValue.id_guru = null
          this.formValue.id_santri = null
          this.fields.id_santri.hidden = false
          this.fields.id_tamu.hidden = this.fields.id_guru.hidden = true
        } else if (value == 'tamu') {
          this.formValue.id_guru = this.formValue.id_santri = null
          this.formValue.id_tamu = null
          this.fields.id_tamu.hidden = false
          this.fields.id_guru.hidden = this.fields.id_santri.hidden = true
        }
      }
    },
    created: function() {
      this.getInitial();
    }
  }
  </script>
  
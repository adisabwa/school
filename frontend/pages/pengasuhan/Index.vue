<template>
    <div id="nilai" class="py-2">
      <el-card class="bg-white/[0.7] mb-2">
        <form-comp ref="formFilter"
          :key="formKey"
          :fields="filterFields"
          size="default"
          label-position="left"
          :show-label="showLabel"
          class="max-sm:mt-4"
          form-class="m-0"
          form-item-class="mb-2"
          label-width="150px"
          v-model:form-value="filter"
          :pass-columns="[]"
          :show-submit="false"
          text-submit="Cari"
          error-submit-text="Tidak dapat mengambil data"
          :show-required-text="false"
          >
        </form-comp>
      </el-card>
      <el-card class="bg-white/[0.7]"
        body-class="p-0">
        <div :class="[scrollY > showHidden ? 'opacity-0' : 'opacity-100',
          'animate px-3 pt-3 pb-2']">
          <div class="text-right">
            <el-button size="small" type="success" @click="saveScore">
            <icons icon="fluent:save-20-filled" /> Simpan
            </el-button>
          </div>
        </div>
        <div class="relative bg-white">
          <div :class="[scrollY > showHidden ? 'opacity-100 z-[9999]' : 'opacity-0 z-[-1]',
            'animate fixed right-0 bg-white/[0.7] h-fit',
            'px-3 pt-3 pb-2']"
            v-fixed-to-position="50">
              <div class="text-right">
                <el-button size="small" type="success" @click="saveScore">
                <icons icon="fluent:save-20-filled" /> Simpan
                </el-button>
              </div>
          </div>
          <div v-if="loading"
            class="text-center text-gray-500 text-lg p-5">
            <icons icon="eos-icons:loading" class="text-[50px] mb-3" />
            <div class="text-[18px]">Mengambil Data ... </div>
          </div>
          <div v-else-if="dataNilai.length == 0"
            class="text-center text-gray-500 text-lg p-5">
            <icons icon="mdi:alert" class="text-[50px] mb-3" />
            <div class="text-[18px]">Tidak ada data nilai</div>
          </div>
          <table-freeze
            ref="tableFreeze"
            :data="dataNilai"
            v-else class="mb-12">
            <template #header="{data}">
              <tr class="*:border *:border-solid *:border-slate-300">
                <th width="15px" class="fixed-col">No</th>
                <th class="fixed-col min-w-[150px]">Nama</th>
                <template v-for="(ujian, key) in data[0].nilai">
                  <th class="text-center">
                    <div class="flex items-center justify-center">
                      <el-tooltip content="Reset Data" placement="bottom-start" class="shrink-0"
                        v-if="allowEdit">
                        <icons icon="ri:reset-left-line" class="border-0 cursor-pointer text-[15px] shrink-0" @click="resetData(key)"/>
                      </el-tooltip>
                      {{ ujian.kategori }}
                    </div>
                  </th>
                </template>
              </tr>
            </template>
            <template #body="{data}">
              <tr v-for="(d, key) in data">
                <td>{{ key + 1 }}</td>
                <td>{{ d.nama }}</td>
                <td v-for="(ujian) in d.nilai" class="text-center">
                  <template v-if="allowEdit">
                    <el-input v-if="ujian.type == 'number'"
                      v-model="ujian.nilai" size="large" 
                      placeholder="Masukkan Nilai"
                      :class="[allowEdit ? 'ml-[20px]' : '',
                        'w-[100px]']"
                      />
                    <el-select v-else
                      v-model="ujian.nilai" size="large" filterable clearable
                      placeholder="Pilih Nilai"
                      :class="[allowEdit ? 'ml-[20px]' : '',
                        'w-[100px]']">
                          <el-option value="4" label="Sangat Baik"/>
                          <el-option value="3" label="Baik"/>
                          <el-option value="2" label="Cukup"/>
                          <el-option value="1" label="Kurang"/>
                          <el-option value="0" label="-"/>
                      </el-select> 
                  </template>
                  <span v-else>
                    {{ ujian.nilai }}
                  </span>
                </td>
              </tr>
            </template>
          </table-freeze>
        </div>
      </el-card>
    </div>
</template>
  
<script>
import { head } from 'lodash';
import { mapState } from 'pinia';
import { useAuthStore } from '@/config/stores/authStore'
import { useDataStore } from '@/config/stores/dataStore'
  
  
  export default {
    name: "mapel",
    components: {
      
    },
    data: function() {
      return {
        initial:false,
        loading: false,
        saving: false,
        filterFields: {
          id_semester:{
            label:'Semester',
            nama_kolom:'id_semester',
            input:'select',
            options:[],
          },
          id_kamar:{
            label:'Kamar',
            nama_kolom:'id_kamar',
            input:'select',
            options:[],
          },
        },
        filter:{
          id_semester:'-1',
          id_kelas:'-1',
        },
        params:{
          where:[],
        },
        editId:-1,
        ids:[],
        formKey:0,
        dataNilai:[],
        scrollY:0,
        showHidden: 206,
        ListNilai:{},
        // role:'guru',
      };
    },
    watch: {
      'filter.id_semester' (val){
        if (!this.initial) {
            this.getKamar(val)
        }
      },
      'filter.id_kamar' (val){
        this.getData()
      },
    },  
    computed: {
      ...mapState(useAuthStore, {
        user: 'loggedUser',
        role: 'role',
      }),
      ...mapState(useDataStore, {
        storeFilters: 'filters',
      }),
      showLabel(){
        return this.$windowWidth > 800
      },
      allowEdit(){
        console.log(this.role)
        let access = (this.role == 'wamar' || this.role == 'admin' )
        // access = true
        // console.log(access)
        return access
      }
    },
    methods: {
      searchData(){
        this.params.where = Object.fromEntries(
          Object.entries(this.filter).filter(([key, value]) => value)
        )
      },
      getInitial: async function() {
          this.loading = true;
          await this.$http.get('/data/semester/options')
            .then(result => {
              this.loading = false;
            this.initial = false;
              var res = result.data;
              this.filterFields.id_semester.options = res;
              if (this.role != 'admin') this.filterFields.id_kamar.readonly = true;
              this.filter.id_semester = this.storeFilters?.id_semester ? this.storeFilters?.id_semester : Object.values(res)[0]?.value
            });
        },
        getKamar(id_semester){
          this.$http.get('/data/kamar/options',{
            params: {
              id_semester: id_semester
            }
          })
          .then(result => {
            this.loading = false;
            var res = result.data;
            this.filterFields.id_kamar.options = res;
              this.filter.id_kamar = this.storeFilters?.id_kamar ? this.storeFilters?.id_kamar : this.user.id_kamar ?? Object.values(this.filterFields.id_kamar.options)[0]?.value
          });
        },
      getData(){
        this.loading = true;
        this.$http.get('pengasuhan/nilai',{
          params: {
            id_kamar: this.filter.id_kamar,
            id_semester: this.filter.id_semester,
          }
        }).then(result => {
          this.dataNilai = result.data
          setTimeout(() => {
            this.$refs.tableFreeze?.getFreezeHeader()
            this.loading = false;
          }, 300)
        })
      },
      resetData(key){
        this.dataNilai.forEach(d => {
          d.nilai[key].nilai = null
        })
      },
      saveScore() {
        let form = []
        this.dataNilai.forEach(d => {
          // console.log(d)
          let data = {
            id:d.id,
            id_semester: this.filter.id_semester,
            id_kamar: this.filter.id_kamar,
            id_santri: d.id_santri,
          }

            d.nilai.forEach((ujian, key) => {
                data[ujian.col] = ujian.nilai
            })
            form.push(data)
        })
        form = window.jsonToFormData(form)
        this.$http.post('pengasuhan/nilai/store_many', form)
          .then(res => {
            this.getData()
            this.$notify.success({
              title: 'Berhasil',
              message: 'Nilai berhasil disimpan',
              position: 'bottom-right'
            });
          })
          .catch(err => {
            console.log(err)
            this.$notify.error({
              title: 'Gagal',
              message: 'Nilai tidak berhasil disimpan',
              position: 'bottom-right'
            });
          })
      },
      downloadDinas(){
        
      }
    },
    created: function() {
      this.getInitial()
      // console.log(this.$router);
    },
    mounted(){
    },
    beforeUnmount() {
      let dataStore = useDataStore()
      Object.entries(this.filter).forEach(([index, val]) =>
        dataStore.setFilter({
          key:index,
          val:val
        })
      )
      // console.log('change-filter', dataStore.filters)
    },
  }
  </script>
  
<template>
    <div id="nilai" class="py-2">
      <el-card class="bg-white/[0.7]">
        <form-comp ref="formFilter"
          :key="formKey"
          :fields="filterFields"
          :label-position="labelPosition"
          class="mt-2 "
          label-width="150px"
          v-model:form-value="filter"
          :pass-columns="[]"
          :show-submit="false"
          text-submit="Cari"
          error-submit-text="Tidak dapat mengambil data"
          :show-required-text="false"
          >
        </form-comp>
        <teleport to="body">
          <div :class="[scrollY > 286 ? 'opacity-100' : 'opacity-0',
            'animate fixed top-[50px] flex z-[9999] right-11 bg-white/[0.7]']">
            <div class="py-2 px-4">
              <el-button size="default" type="success" @click="downloadDinas">
                <icons icon="ri:file-excel-2-fill" /> Template Dinas
              </el-button>
              <el-button size="default" type="primary" @click="promptDinas = true">
                <icons icon="ic:twotone-create" /> Generate Raport Dinas
              </el-button>
              <el-divider direction="vertical" />
              <el-button size="default" type="success" @click="saveScore">
                <icons icon="fluent:save-20-filled" /> Simpan
              </el-button>
            </div>
          </div>
        </teleport>
        <el-card class="">
          <div :class="[scrollY > 286 ? 'opacity-0' : 'opacity-100'],
            'animate'">
            <div class="text-right">
              <el-button size="default" type="success" @click="downloadDinas">
                <icons icon="ri:file-excel-2-fill" /> Template Dinas
              </el-button>
              <el-button size="default" type="primary" @click="promptDinas = true">
                <icons icon="ic:twotone-create" /> Generate Raport Dinas
              </el-button>
              <el-divider direction="vertical" />
              <el-button size="default" type="success" @click="saveScore">
                <icons icon="fluent:save-20-filled" /> Simpan
              </el-button>
            </div>
          </div>
          <teleport to="body">
            <el-dialog  
              v-model="promptDinas"
              class="p-7 w-[400px]"
              :close-on-click-modal="true">
              <template #header>
                <b>Setting Raport Dinas</b>
              </template>
              <b>Masukkan nilai minimal dan nilai maksimal terlebih dahulu</b>
              <div class="flex gap-4 mt-4">
                <div class="flex flex-col">
                  <label class="font-semibold mb-1">Nilai Minimal</label>
                  <el-input size="large" v-model="nilaiMin"
                    placeholder="Nilai Terkecil" />
                </div>
                <div class="flex flex-col">
                  <label class="font-semibold mb-1">Nilai Maksimal</label>
                  <el-input size="large" v-model="nilaiMax"
                    placeholder="Nilai Terbesar" />
                </div>
              </div>
              <template #footer>
                <el-button @click="promptDinas = false">Batal</el-button>
                <el-button 
                  type="success" 
                  @click="generateDinas()" :icon="saving ? 'el-icon-loading' : ''" 
                  :disabled="saving">Generate</el-button>
              </template>
            </el-dialog>
          </teleport>
          <table class="table  mt-3">
            <thead>
              <tr>
                <th rowspan="2" width="20px">No</th>
                <th rowspan="2">Nama</th>
                <th rowspan="2" width="80px" class="text-center">Nilai Harian</th>
                <th rowspan="2" width="80px" class="text-center">UTS</th>
                <th rowspan="2" width="80px" class="text-center">UAS</th>
                <th rowspan="2" width="80px" class="text-center">Raport</th>
                <th colspan="2" class="text-center">Nilai Raport Dinas</th>
              </tr>
              <tr>
                <th width="80px" class="text-center">Nilai 1</th>
                <th width="80px" class="text-center">Nilai 2</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(data, key) in dataNilai">
                <td>{{ key + 1 }}</td>
                <td>{{ data.nama }}</td>
                <td v-for="ujian in ['nilai_harian','uts','uas']">
                  <el-input v-model="data.nilai[ujian]" size="large"
                    @change="data.nilai[ujian] = checkMinMax(rounding(data.nilai[ujian],2), 0, 100)
                      countRapor(key);"
                    class="w-full" />
                </td>
                <td class="text-center">{{ data.nilai.nilai_rapor }}</td>
                <td class="text-center">{{ data.nilai.katrol1 }}</td>
                <td class="text-center">{{ data.nilai.katrol2 }}</td>
              </tr>
            </tbody>
          </table>
        </el-card>
      </el-card>
    </div>
</template>
  
<script>
  import { mapState } from 'pinia';
  
  
  export default {
    name: "mapel",
    components: {
      
    },
    data: function() {
      return {
        loading: false,
        saving: false,
        filterFields: {
          id_semester:{
            label:'Semester',
            nama_kolom:'id_semester',
            input:'select',
            options:[],
          },
          id_kelas:{
            label:'Kelas',
            nama_kolom:'id_kelas',
            input:'select',
            input_only:'1',
            options:[],
          },
          id_pembagian_mapel:{
            label:'Mata Pelajaran',
            nama_kolom:'id_pembagian_mapel',
            input:'select',
            options:[],
          },
        },
        fields:{},
        filter:{
          id_semester:'',
          id_kelas:'',
          id_pembagian_mapel:'',
        },
        params:{
          where:[],
        },
        editId:-1,
        ids:[],
        formKey:0,
        dataNilai:[],
        scrollY:0,
        promptDinas:false ,
        nilaiMin:78,
        nilaiMax:0,
        // role:'walas',
      };
    },
    watch: {
      'paging.currentPage': function(val) {
        this.paging.offset = val * this.paging.perPage - this.paging.perPage;
      },
      'filter.id_semester' (val){
        this.filterFields.id_kelas.options = this.filterFields.id_semester.options[val]?.options ?? []
        this.filter.id_kelas = -1
        setTimeout(() => {
          this.filter.id_kelas = Object.values(this.filterFields.id_kelas.options)[0]?.value
        }, 100)
      },
      'filter.id_kelas' (val){
        this.filterFields.id_pembagian_mapel.options = this.filterFields.id_kelas.options[val]?.options ?? []
        this.filter.id_pembagian_mapel = Object.values(this.filterFields.id_pembagian_mapel.options)[0]?.value
      },
      'filter.id_pembagian_mapel' (val) {
        console.log('id',val)
        this.getData()
      },
      promptDinas(val){
        if (val) {
          let max = 0
          this.dataNilai.forEach(d => {
            let rap = d.nilai.nilai_rapor
            if (rap > max) max = rap
          })
          this.nilaiMax = max
        }
      }
    },  
    computed: {
      ...mapState(useAuthStore, {
        user: 'loggedUser',
        role: 'role',
      }),
      ...mapState(useDataStore, {
        storeFilters: 'filters',
      }),
      labelPosition(){
        return this.$windowWidth < 800 ? 'top' : 'left'
      },
    },
    methods: {
      searchData(){
        this.params.where = Object.fromEntries(
          Object.entries(this.filter).filter(([key, value]) => value)
        )
      },
      getInitial: async function() {
          this.loading = true;
          console.log(this.storeFilters)
          let where = {}
          switch (this.role) {
            case 'guru':
              where.id_guru = this.user.id_guru
              break;
            case 'walas':
              where.id_kelas = this.user.id_kelas
              // where.id_kelas = 1
              break;
            default:
              break;
          }
          this.$http.get('mapel/admin/pembagian/options',{
            params:{
              where:where
            }
          })
            .then(res => {
              let data = res.data
              this.filterFields.id_semester.options = data
              this.filter.id_semester = this.storeFilters?.id_semester ? this.storeFilters?.id_semester : Object.values(data)[0]?.value
              this.filterFields.id_kelas.options = data[this.filter.id_semester]?.options ?? {}
              this.filter.id_kelas = this.storeFilters?.id_kelas ? this.storeFilters?.id_kelas : this.user.id_kelas ?? Object.values(this.filterFields.id_kelas.options)[0]?.value
              this.filterFields.id_pembagian_mapel.options = this.filterFields.id_kelas.options[this.filter.id_kelas]?.options
              this.filter.id_pembagian_mapel = this.storeFilters?.id_mapel ? this.storeFilters?.id_mapel : Object.values(this.filterFields.id_pembagian_mapel.options)[0]?.value
            })
        },
      getData(){
        this.$http.get('mapel/nilai',{
          params: {
            id_pembagian_mapel: this.filter.id_pembagian_mapel
          }
        }).then(result => {
          this.dataNilai = result.data
        })
      },
      countRapor(key){
        let nilai = this.dataNilai[key].nilai
        this.dataNilai[key].nilai.nilai_rapor = Math.round((nilai.nilai_harian + nilai.uts * 2 + nilai.uas * 3) / 6 * 100)  / 100
      },
      generateDinas(){
        let max = this.nilaiMax
        let min = this.nilaiMin
        let real_min = 999
        let real_max = -1
        this.dataNilai.forEach(d => {
          let rap = d.nilai.nilai_rapor
          if (rap < real_min) real_min = rap
          if (rap > real_max) real_max = rap
        })

        this.dataNilai.forEach(d => {
          let rap = d.nilai.nilai_rapor
          let katrol1 = min + ( ( rap - real_min ) / ( real_max - real_min ) * ( max - min ) )
          let katrol2 = katrol1 + 1
          d.nilai.katrol1 = this.rounding(katrol1, 2)
          d.nilai.katrol2 = this.rounding(katrol2, 2)
        })

        this.promptDinas = false
      },
      saveScore() {
        let form = []
        this.dataNilai.forEach(d => {
          console.log(d)
          form.push({
            id:d.id,
            id_pembagian_mapel: d.id_pembagian_mapel,
            id_santri: d.id_santri,
            nilai_harian: d.nilai.nilai_harian,
            uts: d.nilai.uts,
            uas: d.nilai.uas,
            nilai_rapor: d.nilai.nilai_rapor,
            katrol1: d.nilai.katrol1,
            katrol2: d.nilai.katrol2,
          })
        })
        form = window.jsonToFormData(form)
        this.$http.post('mapel/nilai/store_many', form)
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
      }
    },
    created: function() {
      this.getInitial()
      // console.log(this.$router);
    },
    mounted(){
      window.addEventListener('scroll', () => {
        this.scrollY = window.scrollY
        // console.log(this.scrollY)
      })
    },
    beforeUnmount() {
      let dataStore = useDataStore()
      Object.entries(this.filter).forEach(([index, val]) =>
        dataStore.setFilter({
          key:index,
          val:val
        })
      )
      console.log('change-filter', dataStore.filters)
    },
  }
  </script>
  
<template>
    <div id="nilai" class="py-2">
      <el-card class="bg-white/[0.7] mb-2"
        body-class="max-md:p-3">
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
        <el-collapse accordion v-model="activeName">
          <el-collapse-item name="mapel">
            <template #title="{ isActive }">
              <div :class="['font-bold text-[17px] px-2 pt-3 pb-2 title-wrapper', { 'is-active': isActive }]">
                Proges Pengisian Nilai Mata Pelajaran
              </div>
            </template>
            <ProgresNilai ref="progresNilai"
              :role="role"
              v-model:id_semester="filter.id_semester"
              v-model:id_kelas="filter.id_kelas"
              :tingkat="tingkat"
              :labelProgress="role == 'guru' ? user.nama : runFunction({
                data: this.id_kelas, 
                options: this.filterFields.id_kelas.options,
              })"
            />
          </el-collapse-item>
          <el-collapse-item name="pengasuhan">
            <template #title="{ isActive }">
              <div :class="['font-bold text-[17px] px-2 pt-3 pb-2 title-wrapper', { 'is-active': isActive }]">
                Proges Pengisian Nilai Pengasuhan
              </div>
            </template>
            <ProgresNilaiPengasuhan ref="progresNilaiPengasuhan" v-if="filter.id_semester > 0 && filter.id_kelas > 0"
              :role="role"
              v-model:id_semester="filter.id_semester"
              v-model:id_kelas="filter.id_kelas"
              v-model:id_kamar="filter.id_kamar"
              :tingkat="tingkat"
              :labelProgress="role == 'guru' ? user.nama : runFunction({
                data: this.filter.id_kelas, 
                options: this.filterFields.id_kelas.options,
              })"
            />
          </el-collapse-item>
        </el-collapse>
      </el-card>
    </div>
</template>
  
<script>
import { mapState } from 'pinia';
import { useAuthStore } from '@/config/stores/authStore'
import { useDataStore } from '@/config/stores/dataStore'
import ProgresNilai from '@/modules/mapel/components/ProgresNilai.vue'
import ProgresNilaiPengasuhan from '@/modules/pengasuhan/components/ProgresNilai.vue'
  
  
  export default {
    name: "rapor",
    setup(){
      return {
        runFunction, isEmpty,
      }
    },
    components: {
      ProgresNilai,
      ProgresNilaiPengasuhan,
    },
    data: function() {
      return {
        activeName: 'pengasuhan',
        initial:true,
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
            options:{},
            hidden:false,
          },
        },
        filter:{
          id_semester:'',
          id_kelas:'',
          id_kamar:'',
        },
        params:{
          where:[],
        },
        // role:'guru',
      };
    },
    watch: {
      'filter.id_semester' (val){
        this.$emit('update:id_semester', val)
        if (val > 0) {
          if (this.role == 'admin') 
            return this.filter.id_kelas = this.filterFields.id_kelas.options[0].value
          if (this.role == 'guru') {
            this.filter.id_kelas = ''
          }
          else this.filter.id_kelas = this.user.id_kelas
          this.$refs.progresNilai.getData()
        }
      },
      'filter.id_kelas' (val){
        this.$emit('update:id_kelas', val)
        if (val > 0)
          this.$refs.progresNilai.getData()
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
      tingkat(){
        let options = this.filterFields.id_kelas.options
        if (options) {
          if (typeof options == 'object') options = Object.values(options)
          let kelas = options.find(d => d.value == this.id_kelas)
          return kelas?.label[0] ?? false
        } else {
          return false  
        }
      },
      showLabel(){
        return this.$windowWidth.value > 800
      },
    },
    methods: {
      getInitial: async function() {
        this.initial = true
        await this.$http.get('data/kelas/options')
          .then(res => {
            this.initial = false
            let data = res.data
            this.filterFields.id_kelas.options = data
          })
        await this.$http.get('data/semester/options')
          .then(res => {
            let data = res.data
            this.filterFields.id_semester.options = data  
            // console.log(this.storeFilters?.id_semester, data[0]?.value)
            this.filter.id_semester = this.storeFilters?.id_semester ?? data[0]?.value
            // console.log('id_semester', this.filter.id_semester)
            if (this.role != 'admin') this.filterFields.id_kelas.hidden = true;
          })
      },
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
  
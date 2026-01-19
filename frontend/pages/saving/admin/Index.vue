<template>
  <div id="iqab" class="py-2">
    <el-card class="bg-white/[0.7]">
      <form-comp ref="formFilter"
        :fields="filterFields"
        :label-position="labelPosition"
        class="mt-2"
        label-width="180px"
        :pass-columns="['start','end',
          filter.sumber == 'kas' ? 'kelas' : ''
        ]"
        v-model:form-value="filter"
        :show-submit="false"
        text-submit="Cari"
        error-submit-text="Tidak dapat mengambil data"
        :show-required-text="false"
        >
        <template #default="{ errors, form}">
          <el-form-item label="Bulan" :errors="errors.start + ' ' + errors.end"
            class="col-span-6">
            <div class="w-full flex flex-col md:flex-row gap-y-0 gap-x-3">
              <el-date-picker
                v-model="form.start"
                type="month"
                value-format="YYYY-MM-01"
                format="MMMM YYYY"
                placeholder="Pilih Bulan Mulai"
                size="large"
                :append-to-body="false"
              />
              <div class="text-center"> sampai </div>
              <el-date-picker
                v-model="form.end"
                type="month"
                value-format="YYYY-MM-DD"
                format="MMMM YYYY"
                placeholder="Pilih Bulan Selesai"
                size="large"
              />
            </div>
          </el-form-item>
        </template>
      </form-comp>
      <table-data ref="tableData" href="saving/admin" :params="params"
        :show-search="false"
        :title="'Data Tabungan Santri'"
        v-model:checked-id="ids"
        v-model:form-value="formValue"
        :fields="fields"
        :pass-columns="['jenis','nominal','id_santri','id_kas']"
        :pass-columns-input="passColumnsInput"
        class="p-0">
        <template #before-keterangan>
          <el-table-column label="Nama" width="200" align="left">
            <template #default="scope">
              {{ scope.row.sumber == 'kas' ? scope.row.nama_kas : scope.row.kelas + ' - ' + scope.row.nama_santri }}
            </template>
          </el-table-column>
        </template>
        <el-table-column label="Pemasukan" width="150" align="center">
          <template #default="scope">
            {{ scope.row.jenis == '1' ? toIDR(scope.row.nominal) : '-' }}
          </template>
        </el-table-column>
        <el-table-column label="Pengeluaran" width="150" align="center">
          <template #default="scope">
            {{ scope.row.jenis == '-1' ? toIDR(scope.row.nominal) : '-' }}
          </template>
        </el-table-column>
        <template #menu>
          <el-button type="primary" class="float-right"
            @click="searchData"
            ><icons icon="mdi:search"/>Cari</el-button>
        </template>
      </table-data>
    </el-card>
  </div>
</template>

<script>
import { mapState } from 'pinia';

import { filter } from 'lodash';
import { useAuthStore } from '@/config/stores/authStore'
import { useDataStore } from '@/config/stores/dataStore'

export default {
  name: "iqab",
  components: {
    
  },
  data: function() {
    return {
      loading: false,
      showAdd: false,
      filterFields: {
        'sumber' : {
          nama_kolom:'sumber',
          label:'Pemilik Tabungan',
          input:'select',
          options: [
            {value:'',label:'Semua'},
            {value:'kas',label:'Kas'},
            {value:'santri',label:'Santri'},
          ],
        },
        'nama' : {
          nama_kolom:'nama',
          label:'Nama',
          placeholder:'Ketikkan Nama yang dicari',
        },
        'start' : {
          nama_kolom:'start',
          label:'Tanggal Mulai'
        },
        'end' : {
          nama_kolom:'end',
          label:'Tanggal Selesai'
        },
        'kelas' : {
          nama_kolom:'kelas',
          label:'Kelas',
          input:'select',
          options: [],
        },
      },
      fields:{},
      filter:{
        sumber:'',
        nama:'',
        kelas:'',
        start:'',
        end:'',
      },
      params:{},
      editId:-1,
      ids:[],
      formValue: {},
    };
  },
  watch: {
    'formValue.sumber': function(val) {
      // console.log(val);
      if (val == 'kas') {
        this.fields.id_santri = 0;
      } else {
        this.fields.id_kas = 0;
      }
    }
  },  
  computed: {
    ...mapState(useAuthStore, {
      user: 'loggedUser',
    }),
    labelPosition(){
      return this.$windowWidth < 800 ? 'top' : 'left'
    },
    passColumnsInput(){
      if (this.formValue.sumber == 'kas') {
        return ['id_santri'];
      } else {
        return ['id_kas'];
      }
    }
  },
  methods: {
    searchData(){
      // console.log(this.filter)
      this.params = {
        where: {
          'tanggal >= ' : this.filter.start,
          'tanggal <= ' : this.setLastDateOfMonth(this.filter.end),
        },
        or: {},
        order:['tanggal desc'],
      }
      if (this.filter.sumber) 
        this.params.where.sumber = this.filter.sumber

      if (this.filter.kelas) 
        this.params.where.id_kelas = this.filter.kelas

      if (this.filter.nama) {
        this.params.or[`nama_santri LIKE '%${this.filter.nama}%'`] = ''
        this.params.or[`nama_kas LIKE '%${this.filter.nama}%'`] = ''
      }
      console.log(this.params)
    },
    getInitial: async function() {
        this.loading = true;
        await this.$http.get('/kolom/preparation?table=sch_sav_tabungan&grouping=0&input=0')
          .then(result => {
            var res = result.data;
            this.fields = this.fillAndAddObjectValue(this.fields, res)
            this.loading = false
          });
        await this.$http.get('/data/kelas/options')
          .then(result => {
            var res = result.data;
            this.filterFields.kelas.options = res
            this.loading = false
          });
      },
  },
  created: function() {
    let filter = useDataStore().filters
    this.filter.nama =  filter?.nama ?? null
    this.filter.kelas =   filter?.kelas ?? null
    this.filter.start =   filter?.start ?? null
    this.filter.end = filter?.end ?? this.setLastDateOfMonth(this.dateNow())
    this.getInitial()
    console.log(this.setLastDateOfMonth(this.dateNow()));
  },
  mounted: function() {
    this.searchData()
  },
}
</script>

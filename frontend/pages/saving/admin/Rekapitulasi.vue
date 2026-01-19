<template>
  <div id="rekapitulasi" class="py-6">
    <el-card>
      <el-form label-width="120px" label-position="left">
        <div class="flex gap-x-20">
          <el-form-item label="Jenis Tabel">
            <el-radio-group v-model="filter.jenis">
              <el-radio value="daterange">Tanggal</el-radio>
              <el-radio value="monthrange">Bulan</el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item label="Grouping Data">
            <el-radio-group v-model="filter.group">
              <el-radio value="santri">Santri</el-radio>
              <el-radio value="kelas">Kelas</el-radio>
            </el-radio-group>
          </el-form-item>
        </div>
        <el-form-item label="Bulan">
            <el-date-picker
              v-model="filter.bulan"
              :type="filter.jenis"
              range-separator="s/d"
              value-format="YYYY-MM-DD"
              :format="filter.jenis == 'daterange' ? 'DD MMMM YYYY' : 'MMMM YYYY'"
              placeholder="Pilih Rentang Waktu"
            />
        </el-form-item>
      </el-form>
      <div class="flex flex-col md:flex-row gap-3">
        <div class="w-full md:w-2/3">
          <el-button type="primary" size="small" round @click="download">
            <icons icon="mdi:download"/>
            Download Excel</el-button>
        </div>
        <div class="w-full md:w-1/3">
          
        </div>
      </div>
      <el-row>
        <div :class="getDataFormStorage('vertical-menu') == '1' ? 'max-w-[calc(100vw-85px-var(--width-menu))]' : 'max-w-[calc(100vw-95px)]'">
          <loading v-if="loading" title="Mengambil data..."
            subtitle="Silahkan tunggu, sedang mengambil data." />
          <loading v-else-if="searching" title="Mencari data..."
            subtitle="Silahkan tunggu, sedang mencari data." />
          <loading v-else-if="rekapitulasi.length == 0"
            icon="el-icon-s-order" title="Tidak ada data"
            subtitle="Silahkan ubah santri atau bulan untuk melihat data yang lain." />
          <div v-else class="w-full text-[14px]">
            <div class="mt-5">
              <div class="flex font-bold gap-3">
                <div class="bg-emerald-50 py-2 px-3">Pengeluaran</div>
                <div class="bg-red-50 py-2 px-3">Pemasukan</div>
              </div>
            </div>
            <div class="my-5 overflow-x-scroll">
              <table class="table border align-top fixed-column w-max min-w-full">
                <thead class="bg-sky-900">
                  <tr>
                    <th width="30" class="h-[70px] text-white fixed-col " height="50"  rowspan="2">No</th>
                    <th width="170" class="text-white fixed-col text-left border-r-0" rowspan="2">Nama</th>
                    <th class="text-white text-center" rowspan="2">Saldo Awal</th>
                    <template v-for="col in rekapitulasi[0].column">
                      <th class="text-white text-center" nowrap colspan="2">{{ col.text }}</th>
                    </template>
                    <th class="text-white" rowspan="2">Total Pemasukan</th>
                    <th class="text-white" rowspan="2">Total Pengeluaran</th>
                    <th class="text-white" rowspan="2">Total</th>
                  </tr>
                  <tr>
                    <template v-for="col in rekapitulasi[0].column">
                      <th class="text-white text-center">Pemasukan</th>
                      <th class="text-white text-center">Pengeluaran</th>
                    </template>
                  </tr>
                </thead>
               <tbody >
                  <template  v-for="(santri, ind) in rekapitulasi">
                    <tr>
                      <td nowrap valign="top" class="h-[35px] bg-white fixed-col">{{ ind + 1 }}</td>
                      <td nowrap valign="top" class="bg-white fixed-col text-left">{{ santri.text }}</td>
                      <td nowrap valign="top" class="bg-cyan-50 text-right font-bold">
                        {{ toIDR(santri.saldo) }}
                      </td>
                      <template v-for="(col, key) in santri.column">
                        <td class="bg-emerald-50 text-right hover:bg-emerald-200 pointer" valign="top" 
                          @click="getDetail(ind, key)" nowrap>
                          {{ col.pemasukan == 0 ? '-' : toIDR(col.pemasukan) }}
                        </td>
                        <td class="bg-red-50 text-right hover:bg-emerald-200 pointer" valign="top" 
                          @click="getDetail(ind, key)" nowrap>
                          {{ col.pengeluaran == 0 ? '-' : toIDR(col.pengeluaran) }}
                        </td>
                      </template>
                      <td class="bg-emerald-100 text-right"
                        valign="top" nowrap>
                         {{ toIDR(santri.total_pemasukan) }}
                      </td>
                      <td class="bg-red-100 text-right" 
                        valign="top" nowrap>
                         {{ toIDR(santri.total_pengeluaran) }}
                      </td>
                      <td nowrap valign="top" class="bg-cyan-100 text-right font-bold">
                        {{ toIDR(santri.total) }}
                      </td>
                    </tr>
                  </template>
                  <tr class="font-bold">
                    <td nowrap valign="top" class="h-[35px] bg-white fixed-col"></td>
                    <td nowrap valign="top" class="bg-white fixed-col text-left">Total</td>
                    <td nowrap valign="top" class="bg-cyan-50 text-right font-bold">
                      {{ toIDR(total.saldo) }}
                    </td>
                    <template v-for="(col, key) in total.column">
                      <td class=" bg-emerald-50 text-right hover:bg-emerald-200 pointer" valign="top"  nowrap>
                        {{ col.pemasukan == 0 ? '-' : toIDR(col.pemasukan) }}
                      </td>
                      <td class=" bg-red-50 text-right hover:bg-emerald-200 pointer" valign="top" nowrap>
                        {{ col.pengeluaran == 0 ? '-' : toIDR(col.pengeluaran) }}
                      </td>
                    </template>
                      <td class=" bg-emerald-100 text-right"
                        valign="top" nowrap>
                         {{ toIDR(total.total_pemasukan) }}
                      </td>
                      <td class=" bg-red-100 text-right" 
                        valign="top" nowrap>
                         {{ toIDR(total.total_pengeluaran) }}
                      </td>
                    <td nowrap valign="top" class="bg-cyan-100 text-right font-bold">
                      {{ toIDR(total.total) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </el-row>
    </el-card>

    <el-dialog 
      title="Detail Pengeluaran dan Pemasukan" 
      class="w-fit"
      v-model="showDetail">
      <table class="table mb-0 w-full">
        <thead>
          <tr>
            <th width="20" class="text-center">#</th>
            <th class="text-center">Tanggal</th>
            <th class="text-center">Nama</th>
            <th class="text-left" width="200">Keterangan</th>
            <th class="text-center" width="100">Pengeluaran</th>
            <th class="text-center" width="100">Pemasukan</th>
          </tr>
        </thead>
        <tbody>
          <template v-if="dataDetail.length == 0">
            <tr>
              <td colspan="6" class="text-center"> - Tidak ada data - </td>
            </tr>
          </template>
          <template v-else>
            <tr v-for="(list, ind) in dataDetail">
              <td class="text-center" nowrap>{{ind+1}}</td>
              <td class="text-center" nowrap>
                {{ dateIndo(list.tanggal) }}
              </td>
              <td nowrap>
                {{ list.kelas }} - {{ list.nama }}
              </td>
              <td nowrap>
                {{ list.keterangan }}
              </td>
              <td nowrap class="text-center">
                {{ list.jenis == '-1' ? toIDR(list.nominal) : '-' }}
              </td>
              <td nowrap class="text-center">
                {{ list.jenis == '1' ? toIDR(list.nominal) : '-' }}
              </td>
            </tr>
            <tr class="font-bold">
              <td></td>
              <td colspan="3">Total</td>
              <td nowrap>{{ toIDR(totalPengeluaran) }}</td>
              <td nowrap>{{ toIDR(totalPemasukan) }}</td>
            </tr>
          </template>
        </tbody>
      </table>
      <template #footer>
        <el-button @click="showDetail = false">Batal</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'pinia';
import { useAuthStore } from '@/config/stores/authStore'
import { useDataStore } from '@/config/stores/dataStore'

export default {
  name: "rekapitulasi",
  components: {
  },
  data: function() {
    return {
      loading: false,
      showAdd: false,
      showUpload: false,
      searching: false,
      saving: false,
      filter: {
        jenis:'monthrange',
        group:'kelas',
        bulan: '',
      },
      editId:-1,
      type:'create',
      rekapitulasi:[],
      total:{},
      searchKeyword: '',
      isInitial: true,
      showDetail: false,
      dataDetail:[],
      totalPengeluaran:0,
      totalPemasukan:0,
    };
  },
  watch: {
    filter:{
      deep:true,
      handler(val){
        this.getFinance();
      }
    }

  },  
  computed: {      

    ...mapState(useAuthStore, {
      user: 'loggedUser',
    }),

  },
  methods: {
    getInitial: async function() {
      this.loading = true;
      let filter = useDataStore()?.filters
      this.filter.bulan = this.isEmpty(filter.bulan) ? [this.dateNow(), this.dateNow()] : filter.bulan
      // this.filter.bulan = ['2023-10-01','2023-11-30']
      await this.getFinance();
    },
    getFinance: function(){
      let vm = this;
      this.loading = true;
      var urlParams = new URLSearchParams();
      urlParams.append('start', this.filter.bulan[0]);
      urlParams.append('end', this.filter.bulan[1]);
      urlParams.append('jenis', this.filter.jenis);
      urlParams.append('group', this.filter.group);

      this.$http.get('saving/admin/rekapitulasi?'+ urlParams)
        .then(result => {
          this.loading = false;
          this.isInitial = false;
          var res = result.data;
          this.rekapitulasi = res.data;
          this.total = res.total;
          setTimeout(function(){
            vm.getFixedColumn()
          },1000);
        })
        .catch(err => {
          console.log(err)
          this.loading = false;
          this.$notify.error({
            title: 'Gagal',
            message: 'Tidak dapat mengambil data',
            position: 'bottom-right'
          });
        });
    },
    download(){
      var urlParams = new URLSearchParams();
      urlParams.append('start', this.filter.bulan[0]);
      urlParams.append('end', this.filter.bulan[1]);
      urlParams.append('jenis', this.filter.jenis);
      urlParams.append('group', this.filter.group);
      window.open(this.$siteUrl + '/saving/admin/rekapitulasi/download?'+ urlParams,'_blank')
    },
    handleActionClick: function(obj) {
      var action = obj.action;
      var id = obj.id;
      var index = obj.index;
      if (action == 'add') {
        this.showAdd = true;
        this.type = 'create';
        this.editId = -1;
      } if (action == 'edit') {
        this.showAdd = true;
        this.type = 'update';
        this.editId = id;
      } else  if (action == 'delete') {
        this.deleteSchedule(id, index);
      }
    },
    deleteSchedule: function(id, index) {
      this.$confirm('Yakin menghapus jadwal ini?', 'Konfirmasi', {
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
        type: 'error'
      }).then(() => {
        this.$http.post('/rekapitulasi/delete/' + id)
          .then(result => {
            this.$notify.success({
              title: 'Berhasil',
              message: 'Jadwal berhasil dihapus',
              position: 'bottom-right'
            });
            this.rekapitulasi.splice(index,1);
          })
          .catch(err => {
            this.$notify.error({
              title: 'Gagal',
              message: 'Tidak dapat menghapus',
              position: 'bottom-right'
            });
          });        
        }).catch(() => {
        // Do nothing          
      });
    },
    getDetail(ind_santri, ind_tanggal){
      let santri = this.rekapitulasi[ind_santri]
      let column = santri.column[ind_tanggal]

      this.$http.get('/finance',
        { params:{
            santri:santri.id_santri,
            kelas:santri.kelas,
            start:column.date_start,
            end:column.date_end,
            type:this.filter.jenis.replace('range','')
          } })
          .then(res => {
            this.totalPengeluaran = this.totalPemasukan = 0
            this.showDetail = true
            this.dataDetail = res.data
            for (var i = 0; i < res.data.length; i++) {
              let data = res.data[i]
              if (data.jenis == '-1') {
                this.totalPengeluaran -= parseInt(data.nominal)
              }
              if (data.jenis == '1') {
                this.totalPemasukan += parseInt(data.nominal)
              }
            }
          });

    },
    getFixedColumn() {
      let vm = this;
      vm.jquery('table.fixed-column-generate').remove();
      let fixed = vm.jquery('table.fixed-column').toArray();
      console.log(vm.jquery('table.fixed-column'))
      fixed.forEach(e => {
        let clone = vm.jquery(e).clone();
        clone.find('thead, thead tr').children('th:not(.fixed-col)' ).remove();
        clone.find('tr').each(function(){
           vm.jquery(this).find("td:not(.fixed-col)").remove();
        });
        clone.css('width','fit-content');
        clone.css('position','absolute');
        clone.addClass('fixed-column-generate');
        clone.removeClass('min-w-full');
        var column = vm.jquery(e).find('tr');
        var column2 = vm.jquery(clone).find('tr');

        vm.jquery(clone).height(vm.jquery(e).height());
        console.log(vm.jquery(e).height(), vm.jquery(clone).height(), );
        for (var i = 0; i < column.length; i++) {
          // console.log(i, vm.jquery(column[i]).height())
          vm.jquery(column2[i]).height(vm.jquery(column[i]).height());
        }
        vm.jquery(e).parent().prepend(clone);
      });
    },
  },
  updated(){
    console.log('updated')
    this.showAdd = false;
    this.showUpload = false;
    this.getFinance();
  },
  created: function() {
    this.getInitial();
    // console.log(this.$router);
  },
  mounted: function() {

  },
}
</script>
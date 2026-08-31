<template>
  <div v-loading="loading">
    <table class="table text-[13px]">
      <thead>
        <tr class="bg-[var(--color-main-200)]">
          <th width="30px">No</th>
          <th class="min-w-[200px]">{{ labelTitle }}</th>
          <th class="text-center" width="70px">Jumlah Transaksi</th>
          <th width="180px" class="font-bold text-right text-emerald-700">Total Masuk</th>
          <th width="180px" class="font-bold text-right text-red-600">Total Keluar</th>
          <th width="180px" class="font-bold text-right text-orange-600">Saldo</th>
        </tr>
      </thead>
      <tbody>
        <template v-for="summary in dataSummary">
          <template v-if="summary.id == 'before'">
            <tr class="bg-cyan-100 font-bold uppercase text-slate-600">
              <td></td>
              <td class="">Saldo Awal</td>
              <td></td>
              <td class="bg-emerald-100 text-emerald-800 text-right">+ {{ toIDR(summary.datas?.reduce((a, b) => a + b.nominal_masuk, 0)) }}</td>
              <td class="bg-red-100 text-red-800 text-right">- {{ toIDR(summary.datas?.reduce((a, b) => a + b.nominal_keluar, 0)) }}</td>
              <td class="bg-orange-100 text-orange-800 text-right">{{ toIDR(summary.datas?.reduce((a, b) => a + b.saldo, 0)) }}</td>
            </tr>
          </template>
          <template v-else>
            <tr v-if="summary.label">
              <td colspan="6" class="font-bold uppercase text-left text-slate-600 bg-emerald-50">
                {{ summary.label }}
              </td>
            </tr>
            <tr v-for="(item, index) in summary.datas" class="cursor-pointer hover:brightness-[0.95] animate [--duration:300ms]"
              @click="showDialog = true; transaksi = Object.values(item.transaksis)">
              <td>{{ index + 1 }}</td>
              <td class="uppercase text-slate-700">{{ item.label }}</td>
              <td class="uppercase text-slate-500 text-center">{{ item.jml_transaksi }}</td>
              <td class="text-right text-emerald-700">+ {{ toIDR(item.nominal_masuk) }}</td>
              <td class="text-right text-red-600">- {{ toIDR(item.nominal_keluar) }}</td>
              <td class="text-right text-orange-600">{{ toIDR(item.saldo) }}</td>
            </tr>
            <tr v-if="summary.label">
              <td></td>
              <td class="font-bold">Total per {{ summary.label }}</td>
              <td class="text-center font-bold">{{ summary.datas?.reduce((a, b) => a + b.jml_transaksi, 0) }}</td>
              <td class="text-emerald-700 text-right font-bold">+ {{ toIDR(summary.datas?.reduce((a, b) => a + b.nominal_masuk, 0)) }}</td>
              <td class="text-red-700 text-right font-bold">- {{ toIDR(summary.datas?.reduce((a, b) => a + b.nominal_keluar, 0)) }}</td>
              <td class="text-orange-700 text-right font-bold">{{ toIDR(summary.datas?.reduce((a, b) => a + b.saldo, 0)) }}</td>
            </tr>
          </template>
        </template>
        <tr v-if="dataSummary?.length <= 0" class="text-center">
          <td colspan="6" >- Tidak ada data -</td>
        </tr>
        <tr v-else class="bg-slate-700 text-white font-bold">
          <td></td>
          <td class="">TOTAL</td>
          <td nowrap class="text-center">{{ dataSummary?.reduce((a, b) => a + b.jml_transaksi, 0) }}</td>
          <td nowrap class="bg-emerald-700 text-right">+ {{ toIDR(dataSummary?.reduce((a, b) => a + b.nominal_masuk, 0)) }}</td>
          <td nowrap class="bg-red-700 text-right">- {{ toIDR(dataSummary?.reduce((a, b) => a + b.nominal_keluar, 0)) }}</td>
          <td nowrap class="bg-orange-700 text-right">{{ toIDR(dataSummary?.reduce((a, b) => a + b.saldo, 0)) }}</td>
        </tr>

      </tbody>
    </table>

		<el-dialog v-model="showDialog"
			append-to-body
			class="p-0 w-[90%] md:w-[70%]"
			header-class="bg-slate-900 text-slate-100 p-3"
			body-class="p-3">
			<template #header>
				<spav class="uppercase font-bold">Detail Transaksi</spav>
			</template>
			<el-table :data="transaksi">
        <el-table-column label="No" width="50px" >
					<template #default="scope">
						{{ scope.$index + 1 }}
					</template>
				</el-table-column>
				<el-table-column label="Tanggal Transaksi" width="150px">
					<template #default="scope">
						{{ dateIndo(scope.row.tanggal) }}
					</template>
				</el-table-column>
				<el-table-column label="Keterangan" class="font-bold" min-width="200px">
					<template #default="scope">
						<div class="font-bold leading-[1.3]">{{ ucFirst(scope.row.keterangan)}}</div>
					</template>
				</el-table-column>
				<el-table-column label="Nominal Masuk" width="160px" align="right">
					<template #default="scope">
            <div class="text-emerald-600">{{ toIDR(scope.row.nominal_masuk) }}</div>
					</template>
				</el-table-column>
        <el-table-column label="Nominal Keluar" width="160px" align="right">
          <template #default="scope">
            <div class="text-red-600">{{ toIDR(scope.row.nominal_keluar) }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Saldo" width="160px" align="right">
          <template #default="scope">
            <div class="text-orange-600">{{ toIDR(scope.row.saldo) }}</div>
          </template>
        </el-table-column>
			</el-table>
		</el-dialog>
  </div>
</template>

<script>

  export default {
    setup(){
      return {
        toIDR, dateIndo, ucFirst,
      }
    },
    props: {
      periodType: { type: String, default: '' },
      startDate: { type: String, default: '' },
      endDate: { type: String, default: '' },
      selectedMonth: { type: String, default: '' },
      selectedYear: { type: Number, default: '' },
      dimension: { type: String, default: '' },
      subtotalGroup: { type: String, default: '' },
      reportSearchQuery: { type: String, default: '' },
      selectedStudent: { type: String, default: '' },
      selectedUnit: { type: String, default: '' },
      selectedClass: { type: String, default: '' },
      selectedStatus: { type: String, default: '' },
      selectedCategory: { type: String, default: '' },
      selectedPos: { type: String, default: '' },
      selectedCashAccount: { type: String, default: '' },
      selectedTxType: { type: String, default: '' },
    },
    data(){
      return {
        showDialog:false,
        transaksi:[],
        dataSummary:[],
        fields:{
          label:{
            nama_kolom:'label',
            label:'',
          }
        },
        listFields:{

        }
      }
    },
    computed:{
      labelTitle(){
        switch(this.dimension){
          case 'santri':
            return 'Nama Santri'
          case 'pos':
            return 'Pos Anggaran'
          case 'kas':
            return 'Nama Kas'
          default:
            return ''
        }
      },
      tableParams(){
        let where = {
          period_type:this.periodType,
          start_date:this.startDate,
          end_date:this.endDate,
          selected_month:this.selectedMonth,
          selected_year:this.selectedYear,
          dimension:this.dimension,
          subtotal_group:this.subtotalGroup,
          report_search_query:this.reportSearchQuery,
          where:{
          }
        }
        if (!(this.selectedStudent == 'ALL' || this.selectedStudent == '')) where.where['{n}id_santri'] = this.selectedStudent
        if (!(this.selectedUnit == 'ALL' || this.selectedUnit == '')) where.where['{n}id_unit'] = this.selectedUnit
        if (!(this.selectedClass == 'ALL' || this.selectedClass == '')) where.where['{n}id_kelas'] = this.selectedClass
        if (!(this.selectedStatus == 'ALL' || this.selectedStatus == '')) where.where['status'] = this.selectedStatus
        if (!(this.selectedCategory == 'ALL' || this.selectedCategory == '')) where.where['id_kategori'] = this.selectedCategory
        if (!(this.selectedPos == 'ALL' || this.selectedPos == '')) where.where['id_pos'] = this.selectedPos
        if (!(this.selectedCashAccount == 'ALL' || this.selectedCashAccount == '')) where.where['id_kas'] = this.selectedCashAccount
        if (!(this.selectedTxType == 'ALL' || this.selectedTxType == '')) where.where['jenis'] = this.selectedTxType
        return where
      }
    },
    watch:{
      tableParams(val){
        this.getData()
      }
    },
    methods:{
      getData(){
        this.loading = true
        this.$http.get('/keuangan/admin/transaksi/summary', {params:this.tableParams})
          .then(result => {
            this.loading = false
            this.dataSummary = result?.data
          })
          .catch(err => {
            console.log(err)
          } )
      }
    },
    mounted(){
      this.getData()
    }
  }
</script>

<style scoped lang="postcss">
  :deep(.table) {
    th, td {
      @apply px-4 py-2;
    }
  }
</style>

<template>
  <div class="space-y-2 mx-auto animate-in fade-in slide-in-from-bottom-8">
    <el-button text class="text-xs font-black text-gray-500 uppercase flex items-center gap-1" @click="$router.replace({name:'keuangan-iuran-tagihan'})">
      <icons icon="mdi:arrow-left"/> Kembali
    </el-button>

    <div class="bg-white p-5 rounded-[40px] border shadow-sm space-y-4">
      <div class="text-2xl font-black text-gray-900">Input Pembayaran Siswa</div>

      <!-- Pilih Siswa -->
      <div class="mb-3">
        <label class="block text-xs font-black uppercase text-slate-500 mb-1">Pilih Siswa</label>
        <floating-select v-model:value="selectedStudentId" filterable placeholder="-- Cari Nama Siswa --" class="w-full"
          :options="students"/>
      </div>

      <!-- Detail -->
      <div v-if="selectedStudentId" class="space-y-4 animate-in fade-in slide-in-from-top-2">
        <!-- Saldo & Tunggakan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="p-3 bg-blue-50 rounded-2xl border border-blue-100 flex items-center gap-2">
            <div class="p-2 bg-blue-600 text-white rounded-lg flex items-center justify-center aspect-square">
              <icons icon="mdi:wallet" class="m-0 text-3xl" />
            </div>
            <div>
              <div class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-0.5">Saldo Simpanan</div>
              <div class="text-xl font-black text-blue-700">{{ toIDR(studentBalance ?? 0) }}</div>
            </div>
          </div>

          <div class="p-3 bg-rose-50 rounded-2xl border border-rose-100 flex items-center gap-2">
            <div class="p-2 bg-rose-600 text-white rounded-lg flex items-center justify-center aspect-square">
              <icons icon="lucide:receipt" class="m-0 text-3xl" />
            </div>
            <div>
              <div class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-0.5">Total Tunggakan</div>
              <div class="text-xl font-black text-rose-700">{{ toIDR(totalUnpaidAmount) }}</div>
            </div>
          </div>
        </div>

        <!-- Daftar Tagihan -->
        <div class="space-y-2">
          <div class="text-xs font-black text-gray-500 uppercase tracking-widest px-1 flex items-center gap-1">
            <icons icon="mdi:clock" class="" /> Daftar Tagihan Belum Lunas
          </div>

          <div class="bg-gray-50 rounded-2xl overflow-hidden border border-gray-100">
            <div v-if="unpaidBills.length" class="divide-y divide-gray-100">
              <div v-for="(bill, ind) in unpaidBills" :key="bill.id" class="px-6 py-2 flex gap-4 justify-between items-center hover:bg-white transition-colors">
                <div class="grow-0">
                  <el-checkbox v-model="bill.checked" class="border border-solid border-[var(--color-main-200)] h-fit" 
                    @change="changePaidAmount(ind)" />
                </div>
                <div class="w-full">
                  <div class="text-lg text-gray-500 font-bold uppercase">{{ bill.nama_iuran }}</div>
                  <div class="text-sm font-black text-gray-900">
                    <span class="font-normal">Semester {{ ucFirst(bill.semester) }} {{ bill.tahun_ajaran }}</span> 
                    (
                      <template v-if="bill.tipe == 'rutin'">Bulanan. Periode {{ monthIndo(bill.periode) }}</template>
                      <template v-else>Non-Bulanan</template>
                    )
                  </div>
                </div>
                <div class="text-right w-full">
                  <div class="text-sm font-black text-gray-900">{{ toIDR(bill.nominal) }}</div>
                  <span v-if="bill.checked" class="text-[9px] font-black text-[var(--color-main-600)] bg-[var(--color-main-50)] px-1 py-0.5 rounded-full uppercase">Bisa Dibayar</span>
                  <span v-else class="text-[9px] font-black text-amber-600 bg-amber-50 px-1 py-0.5 rounded-full uppercase">Menunggu</span>
                </div>
              </div>
            </div>

            <div v-else class="p-5 text-center space-y-1">
              <icons icon="material-symbols:check-circle-outline" class="text-[64px] mx-auto text-[var(--color-main-500)]" />
              <div class="text-sm font-black text-gray-900">Semua Tagihan Lunas</div>
              <div class="text-xs text-gray-500 font-medium">Siswa ini tidak memiliki tunggakan saat ini.</div>
            </div>
          </div>
        </div>

        <!-- Pembayaran -->
        <div class="space-y-2 pt-2 border-t border-gray-100" v-if="unpaidBills.length > 0">
          <form-comp  ref="formPembayaran" :fields="fieldsForm" v-model:formValue="formValue"
            form-class="mb-1"
            :show-submit="false" :show-required-text="false"
            label-position="top"
            input-class="[&_*]:font-black [&_*]:text-[var(--color-main-900)]"
            each-input-class="[&_*]:font-black [&_*]:text-[var(--color-main-900)]"
            form-item-class="mb-2"
            @changedValue="changedValue"
            labelClass="text-xs font-black uppercase text-slate-500 mb-1"/>
          <el-button type="primary" size="large" class="w-full py-2 rounded-2xl font-black uppercase tracking-widest shadow-xl transition-all"
            :disabled="unpaidBills.filter(d => d.checked).length == 0"
            @click="savedPembayaran">
            Konfirmasi & Bayar
          </el-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { data } from 'jquery';


export default {
  name: 'PaymentEntry',
  setup() { 
    return {
      toIDR, monthIndo, runFunction, setCurrency, toNumber, ucFirst,
    }
  },
  props: {
  },
  data() {
    return { 
      selectedStudentId: '', 
      paymentAmount: 0,
      students: [],
      unpaidBills: [],
      formValue:{},
      fieldsForm:{
        nominal:{
          nama_kolom:'nominal',
          label:'Nominal Pembayaran (Rp)',
          placeholder:'Masukkan jumlah yang dibayar...',
          function_input:'setCurrency',
          function_submit:'toNumber',
          prepend:'Rp. ',
        },
        id_metode:{
          nama_kolom:'id_metode',
          label:'Metode Pembayaran',
          placeholder:'Pilih metode pembayaran',
          input:'select',
          options:[],
        },
        tanggal_masuk:{
          nama_kolom:'tanggal_masuk',
          label:'Tanggal Pembayaran',
          placeholder:'Pilih tanggal pembayaran',
          input:'date-wheel',
        },
      },
      studentBalance:0,
      paidByPos:[],
    }
  },
  computed: {
    totalUnpaidAmount() {
      return this.unpaidBills.reduce((sum, bill) => sum + parseInt(bill.nominal || 0), 0);
    }
  },
  watch:{
    selectedStudentId(val){
      this.getTagihanDetail(val)
      this.getSaldo(val)
    },
    unpaidBills(val){
      this.paidByPos = []
      val.forEach(d => {
        if (!this.paidByPos[d.id_pos])
          this.paidByPos[d.id_pos] = []

        this.paidByPos[d.id_pos][d.id] = 0
      })
    }
  },
  methods:{
    changedValue({value, field}){
      if (field == 'nominal') {
        this.calculatePaidBill()
      }
    },
    async getInitial(){
      await this.$http.get('data/santri/options')
        .then(res => this.students = res?.data)
      await this.$http.get('keuangan/admin/data/metode/options')
        .then(res => {
          this.fieldsForm.id_metode.options = res?.data
          this.formValue.id_metode = res?.data?.[0].value
        })
      // this.selectedStudentId = 290
      this.selectedStudentId = this.$route.query?.id_santri ?? this.students[0].value
      if (typeof this.selectedStudentId == 'number')
        this.selectedStudentId = this.selectedStudentId.toString()
    },
    getSaldo(id_santri){
      this.$http.get('keuangan/admin/iuran-saldo/get_saldo',{
        params:{
          id_santri:id_santri,
        }
      })
      .then(res => {
        let data = res?.data ?? []
        if (data?.length > 0) {
          this.studentBalance = (data[0]?.total_saldo_masuk ?? 0) - (data[0]?.total_saldo_keluar ?? 0)
        } else {
          this.studentBalance = 0
        }
      })
    },
    getTagihanDetail(id_santri){
      this.$http.get('keuangan/admin/iuran/tagihan',{
        params:{
          where:{
            id_santri:id_santri,
            status:'0',
          },
          order:[
            'id_semester, periode asc'
          ]
        }
      })
      .then(res => {
        this.unpaidBills = res?.data
      })
    },
    calculatePaidBill(){
      let nom = this.toNumber(this.formValue.nominal)
      console.log(nom)
      this.unpaidBills.forEach(b => {
        console.log(nom, b.nominal)
        if (nom >= b.nominal) {
          b.checked = true
          this.paidByPos[b.id_pos][b.id] = b.nominal
          nom = nom - b.nominal
        } else {
          b.checked = false
        }
      })
    },
    changePaidAmount(ind){
      let bill = this.unpaidBills[ind]
      let nom = toNumber(this.formValue.nominal)
      console.log(nom)
      if (bill.checked) {
        nom += parseInt(bill.nominal)
        this.paidByPos[bill.id_pos][bill.id] = bill.nominal
      } else {
        nom -= parseInt(bill.nominal)
        this.paidByPos[bill.id_pos][bill.id] = 0
      }
      this.formValue.nominal = nom - parseInt(this.studentBalance ?? 0)
      if (this.formValue.nominal < 0) {
        this.formValue.nominal = 0
      }
    },
    savedPembayaran(){
      const promiseArray = []

      let setor = toNumber(this.formValue.nominal)
      let total = this.unpaidBills.filter( b => b.checked).reduce((sum, b) => sum + parseInt(b.nominal), 0)
      let saldo = (setor + this.studentBalance) - total 
      if (total > 0) {
        let formData = {
          jenis:'iuran',
          tanggal:this.formValue.tanggal_masuk,
          id_santri:this.selectedStudentId,
          id_metode:this.formValue.id_metode,
          keterangan:'Pembayaran Iuran dari Santri a.n. ' + this.students.find(d => d.value == this.selectedStudentId)?.label,
          tujuan: this.students.find(d => d.value == this.selectedStudentId)?.label,
          nominal_disetor: setor,
          nominal_alokasi: total,
          nama_fk:{
            sch_keu_alokasi_transaksi:'id_transaksi'
          },
          tables:{
            sch_keu_alokasi_transaksi: [],
          }
        }
        this.unpaidBills.forEach((d)=> {
          if (d.checked) {
            formData.tables.sch_keu_alokasi_transaksi.push({
              id_iuran_santri: d.id,
              nominal_alokasi: d.nominal,
            })
          }
        })
        this.$http.post('keuangan/admin/transaksi/store', window.jsonToFormData(formData)).then(res => {
          let data = res?.data
          let id_transaksi = data?.id
          if (this.studentBalance > 0) {
            this.$http.post('keuangan/admin/iuran-saldo/store', window.jsonToFormData({
              id_santri:this.selectedStudentId,
              id_transaksi:id_transaksi,
              jenis_mutasi:'out',
              keterangan:'Saldo keluar ke pembayaran ' + this.students.find(d => d.value == this.selectedStudentId)?.label,
              nominal: this.studentBalance > total ? total : this.studentBalance,
            }))
          }
          if (setor > 0 && saldo > 0) {
            this.$http.post('keuangan/admin/iuran-saldo/store', window.jsonToFormData({
              id_santri:this.selectedStudentId,
              id_transaksi:id_transaksi,
              jenis_mutasi:'in',
              keterangan:'Saldo iuran dari pembayaran ' + this.students.find(d => d.value == this.selectedStudentId)?.label,
              nominal: saldo,
            }))
          } 
          
          this.savedIuran()
        }).catch(err => {
          console.log(err)
        })
      }
    },
    savedIuran(){  
      let datas = this.unpaidBills.filter(d => d.checked).map(d => {
        return {
          id:d.id,
          status:'1',
        }
      })
      datas = window.jsonToFormData(datas)
      this.$http.post('keuangan/admin/iuran/tagihan/store_many', datas)
        .then(res => {
          this.$alert('Pembayaran berhasil disimpan', 'Berhasil', {
						type: 'success',
						confirmButtonText: 'Konfirmasi',
					})
					.then(res => {
							this.$router.replace({name:'keuangan-iuran-tagihan'})
						})
					.catch(res => {
							
						})
        })
    }
  },
  created(){
    this.getInitial()
  }
}
</script>

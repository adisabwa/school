<template>
  <div class="bg-gray-50 p-3 rounded-[20px] border border-gray-100 space-y-2">
    <!-- Student Specific Filters -->
    <div v-if="dimension === 'santri'" class="grid grid-cols-2 md:grid-cols-4 gap-2">
      <!-- 1. Floating Select Siswa -->
      <div class="relative">
        <label class="text-[11px] font-black text-gray-400 uppercase">Pilih Siswa</label>
        <floating-select filterable size="small"
          v-model:value="localSelectedStudent"
          class="peer"
          :options="[
            {value:'ALL', label:'Semua Siswa'},
            ...students,
          ]"
        >
        </floating-select>
      </div>

      <!-- 2. Floating Select Unit Sekolah -->
      <div class="relative">
        <label class="text-[11px] font-black text-gray-400 uppercase">Pilih Unit</label>
        <floating-select filterable size="small"
          v-model:value="localSelectedUnit"
          class="peer"
          :options="[
            {value:'ALL', label:'Semua Unit'},
            ...units,
          ]"
        >
        </floating-select>
      </div>

      <!-- 3. Floating Select Kelas -->
      <div class="relative">
        <label class="text-[11px] font-black text-gray-400 uppercase">Pilih Kelas</label>
        <floating-select filterable size="small"
          v-model:value="localSelectedClass"
          class="peer"
          :options="[
            {value:'ALL', label:'Semua Kelas'},
            ...classes,
          ]"
        >
        </floating-select>
      </div>
    </div>

    <!-- Transactions Specific Filters -->
    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-2">
      <!-- Floating Select Kategori -->      
      <div class="relative" v-if="dimension === 'category'">
        <label class="text-[11px] font-black text-gray-400 uppercase">Filter Kategori</label>
        <floating-select filterable size="small"
          v-model:value="localSelectedCategory"
          class="peer"
          :options="[
            {value:'ALL', label:'Semua Kategori'},
            ...categories,
          ]"
        >
        </floating-select>
      </div>

      <!-- Floating Select Pos Anggaran -->
      
      <div class="relative" v-if="dimension === 'pos'">
        <label class="text-[11px] font-black text-gray-400 uppercase">Filter Pos Anggaran</label>
        <floating-select filterable size="small"
          v-model:value="localSelectedPos"
          class="peer"
          :options="[
            {value:'ALL', label:'Semua Pos'},
            ...budgetPos,
          ]"
        >
        </floating-select>
      </div>

      <!-- Floating Select Akun Kas -->
      <div class="relative" v-if="dimension === 'cash'">
        <label class="text-[11px] font-black text-gray-400 uppercase">Filter Akun Kas</label>
        <floating-select filterable size="small"
          v-model:value="localSelectedCashAccount"
          class="peer"
          :options="[
            {value:'ALL', label:'Semua Kas'},
            ...cashAccounts,
          ]"
        >
        </floating-select>
      </div>

      <!-- Floating Select Tipe Transaksi -->
      <div class="relative" >
        <label class="text-[11px] font-black text-gray-400 uppercase">Tipe Transaksi</label>
        <floating-select filterable size="small"
          v-model:value="localSelectedTxType"
          class="peer"
          :options="[
            {value:'ALL', label:'Semua Tipe (Masuk & Keluar)'},
            {value:'iuran', label:'Iuran'},
            {value:'pemasukan', label:'Pemasukan'},
            {value:'pengeluaran', label:'Pengeluaran'},
          ]"
        >
        </floating-select>
      </div>
    </div>
  </div>
</template>

<script>
import { Filter } from '@element-plus/icons-vue';

export default {
  name: 'FilterSpesifik',
  components: { Filter },
  props: {
    dimension: { type: String, required: true },
    selectedStudent: { type: String, required: true },
    selectedUnit: { type: String, required: true },
    selectedClass: { type: String, required: true },
    selectedStatus: { type: String, required: true },
    selectedCategory: { type: String, required: true },
    selectedPos: { type: String, required: true },
    selectedCashAccount: { type: String, required: true },
    selectedTxType: { type: String, required: true },
    filteredPaymentsCount: { type: Number, required: true },
    filteredTransactionsCount: { type: Number, required: true },
  },
  emits: [
    'update:selectedStudent',
    'update:selectedUnit',
    'update:selectedClass',
    'update:selectedStatus',
    'update:selectedCategory',
    'update:selectedPos',
    'update:selectedCashAccount',
    'update:selectedTxType',
  ],
  data() {
    return {
      // Local mirrored variables
      localSelectedStudent: this.selectedStudent,
      localSelectedUnit: this.selectedUnit,
      localSelectedClass: this.selectedClass,
      localSelectedStatus: this.selectedStatus,
      localSelectedCategory: this.selectedCategory,
      localSelectedPos: this.selectedPos,
      localSelectedCashAccount: this.selectedCashAccount,
      localSelectedTxType: this.selectedTxType,
      students: [],
      categories: [],
      budgetPos: [],
      cashAccounts: [],

      // Options
      classes: [],
      units: [],
    };
  },
  watch: {
    // Parent Props -> Local Mirror
    selectedStudent(val) { this.localSelectedStudent = val; },
    selectedUnit(val) { this.localSelectedUnit = val; },
    selectedClass(val) { this.localSelectedClass = val; },
    selectedStatus(val) { this.localSelectedStatus = val; },
    selectedCategory(val) { this.localSelectedCategory = val; },
    selectedPos(val) { this.localSelectedPos = val; },
    selectedCashAccount(val) { this.localSelectedCashAccount = val; },
    selectedTxType(val) { this.localSelectedTxType = val; },

    // Local Mirror -> Parent Updates
    localSelectedStudent(val) { this.$emit('update:selectedStudent', val); },
    localSelectedUnit(val) { this.$emit('update:selectedUnit', val); },
    localSelectedClass(val) { this.$emit('update:selectedClass', val); },
    localSelectedStatus(val) { this.$emit('update:selectedStatus', val); },
    localSelectedCategory(val) { this.$emit('update:selectedCategory', val); },
    localSelectedPos(val) { this.$emit('update:selectedPos', val); },
    localSelectedCashAccount(val) { this.$emit('update:selectedCashAccount', val); },
    localSelectedTxType(val) { this.$emit('update:selectedTxType', val); },
  },
  methods:{
    getInitial(){
      this.$http.get('keuangan/admin/data/pos/options')
        .then((res) => {
          this.budgetPos = res.data
        }).catch((err) => {
          console.log(err)
        })
        
      this.$http.get('keuangan/admin/data/kategori/options')
        .then((res) => {
          this.categories = res.data
        }).catch((err) => {
          console.log(err)
        })
        
      this.$http.get('keuangan/admin/data/kas/options')
        .then((res) => {
          this.cashAccounts = res.data
        }).catch((err) => {
          console.log(err)
        })

      this.$http.get('data/santri/options')
        .then((res) => {
          this.students = res.data
        }).catch((err) => {
          console.log(err)
        })
      this.$http.get('data/unit/options')
        .then((res) => {
          this.units = res.data
        }).catch((err) => {
          console.log(err)
        })
      this.$http.get('data/kelas/options')
        .then((res) => {
          this.classes = res.data
        }).catch((err) => {
          console.log(err)
        })
    } 
  },
  mounted(){
    this.getInitial();
  }
};
</script>

<style scoped>
</style>
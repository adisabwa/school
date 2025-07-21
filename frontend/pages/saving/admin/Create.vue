<template>
  <div class="finance-create-dialog">
    <el-dialog 
      :title="(type == 'create' ? 'Tambah' : 'Edit') + ' Finance'" 
      v-model="showDialog"
      width="400px"
      :close-on-click-modal="false">
      <el-form :model="form" ref="formForm" label-position="top">
        <el-form-item class="mb-4" label="Nama Santri" :error="errors.id_santri">
          <el-select v-model="form.id_santri" placeholder="Nama Santri" class="w-full"
            filterable>
            <el-option
              v-for="item in santriList"
              :key="item.id"
              :value="item.id"
              :label="item.nama">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item class="mb-4" label="Jenis Pengeluaran" :error="errors.jenis">
          <el-radio-group v-model="form.jenis">
            <el-radio-button label="1">Pemasukan</el-radio-button>
            <el-radio-button label="-1">Pengeluaran</el-radio-button>
          </el-radio-group>
        </el-form-item>
        <el-form-item class="mb-4" label="Keterangan" :error="errors.keterangan">
          <el-input v-model="form.keterangan" type="textarea" rows="3"/>
        </el-form-item>
        <el-form-item class="mb-4" label="Tanggal" :error="errors.tanggal">
          <el-date-picker
            class="w-full"
            v-model="form.tanggal"
            value-format="YYYY-MM-DD"
            format="DD MMMM YYYY"
            placeholder="Pilih Tanggal"
          />
        </el-form-item>
        <el-form-item class="mb-4" label="Nominal" :error="errors.nominal">
          <el-input v-model="form.nominal" @input="form.nominal = formatRupiah(form.nominal)" 
            placeholder="Masukkan nominal">
            <template #prepend>
              Rp. 
            </template>
          </el-input>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="show = false">Batal</el-button>
        <el-button 
          type="success" 
          @click="save()" :icon="saving ? 'el-icon-loading' : ''" 
          :disabled="saving">Simpan</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script>

export default {
  name: 'finance-create-dialog',
  props: {
    show: {
      type: Boolean,
      default: false,
    },
    type: {
      type: String,
      default: 'create'
    },
    dataId: {
      type: [Number, String],
      default: -1,
    }
  },
  data: function() {
    return {
      saving: false,
      showDialog: false,
      form: {
        id_santri:'',
        tanggal:'',
        nominal:'',
        keterangan:'',
        jenis:'1',
      },
      errors: {
        id_santri:'',
        tanggal:'',
        nominal:'',
        keterangan:'',
        jenis:'',
      },
      santriList:[],

    };
  },
  watch: {
    show: {
      immediate: true,
      async handler(val) {
        this.showDialog = val;
      }
    },
    showDialog: function(val, oldVal) {
      this.$emit('update:show', val);
      // console.log(this.show, val);
      if (val==true) {
        this.resetData()
        this.getSantri()
        this.getDataToEdit();
      }
    },
  },
  computed: {
  },
  methods: {
    getDataToEdit() {
      console.log(this.dataId)
      if (this.dataId == -1 || this.isEmpty(this.dataId)) return;
      this.$http.get('finance/get',
        {
          params:{ 
            id: this.dataId
          }
        }
      )
        .then(res => {
          let data = res.data;
          data.nominal = this.formatRupiah(data.nominal)
          this.fillObjectValue(this.form, data);
        })
        .catch(err => {
          this.$notify.error({
            title: 'Gagal',
            message: 'Data finance gagal didapatkan',
            position: 'bottom-right'
          });
        });
    },
    getSantri(){
      this.$http.get('/data/santri')
        .then(result => {
          var res = result.data;
          this.santriList = res;
          // this.filter.santri = res[0].id;
          // this.filter.santri = '1';
        });
    },
    save: function() {
      this.saving = true;

      this.form.nominal = this.toNumber(this.form.nominal)
      var formData = window.jsonToFormData(this.form);
      formData.append('id', this.dataId);     
      formData.append('type', this.type);     

      this.$http.post('/finance/store', formData)
        .then(result => {
          this.saving = false;
          var finance = result.data.data;
          
          this.$emit('saved', finance);
        })
        .catch(err => {
          
          this.saving = false;
          var res = err.response;
          var code = res.status;
          
          if (code == '400') {
            // Populating error message
            // console.log(res)
            this.fillObjectValue(this.errors, res.data.messages);
            this.$notify.error({
              title: 'Gagal',
              message: 'Data belum benar',
              position: 'bottom-right'
            });
          } else {
            this.$notify.error({
              title: 'Gagal',
              message: 'Tidak dapat menyimpan',
              position: 'bottom-right'
            });
          }
        });
    },
    resetData: function() {
      this.resetObjectValue(this.form,['jenis']);
    },
  },
  created() {

  }
}
</script>
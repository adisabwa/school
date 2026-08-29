<template>
  <el-dialog
    :model-value="show"
    title="Tambah Bab Materi Baru"
    class="min-w-[300px] max-w-[500px]"
    append-to-body
    :before-close="close"
    @close="close"
    destroy-on-close
  >
    <el-form label-position="top" size="default" class="*:m-0">
      <el-form-item required class="m-0">
        <template #label>Semester</template>
        <floating-select v-model:value="form.id_semester" :options="semesterOptions"
          class="w-full"/>
      </el-form-item>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-x-3 *:m-1">
        <el-form-item required>
          <template #label>No. Bab</template>
          <el-input-number
            v-model="form.no"
            :min="1"
            class="w-full"
            controls-position="right"
          />
        </el-form-item>

        <el-form-item required>
          <template #label>Jml Pertemuan</template>
          <el-input-number
            v-model="form.pertemuan"
            :min="1"
            class="w-full"
            controls-position="right"
          />
        </el-form-item>

        <el-form-item required>
          <template #label>Alokasi JP</template>
          <el-input-number
            v-model="form.jam"
            :min="2"
            class="w-full"
            controls-position="right"
          />
        </el-form-item>
      </div>

      <el-form-item required>
        <template #label>Judul Bab Materi *</template>
        <el-input
          v-model="form.materi"
          placeholder="Misal: Ekosistem dan Rantai Makanan"
        />
      </el-form-item>

      <el-form-item>
        <template #label>Capaian Pembelajaran</template>
        <el-input
          v-model="form.cp"
          placeholder="Capaian pembelajaran...."
        />
      </el-form-item>
      
      <div class="font-bold !pt-5">Sub Materi & Tujuan Pembelajaran</div>
      <template v-for="(m, ind) in form.sch_aka_sub_materi">
        <div 
          class="flex flex-row gap-3 m-0">
          <el-form-item class="w-full m-0 mb-1">
            <template #label>Sub-materi {{ ind + 1 }}</template>
            <el-input
              v-model="m.sub_materi"
              type="textarea"
              :rows="2"
              placeholder="Misal: Produsen & Konsumen; Daur Materi"
            />
          </el-form-item>
          <el-form-item class="w-full m-0 mb-1">
            <template #label>Tujuan Pembelajaran {{ ind + 1 }}</template>
            <el-input
              v-model="m.tujuan_pembelajaran"
              type="textarea"
              :rows="2"
              placeholder="Mengidentifikasi rantai makanan..."
            />
          </el-form-item>
        </div>
        <div >
          <el-button v-if="!emptyMateriExist"
            type="text" size="small" class="p-0" @click="form.sch_aka_sub_materi.push({sub_materi:'', tujuan_pembelajaran:''})">Tambah Data</el-button>
          <template  v-if="form.sch_aka_sub_materi.length > 1">
            <el-divider direction="vertical"/>
            <el-button
              type="text" size="small" class="p-0 text-red-500" @click="form.sch_aka_sub_materi.splice(ind, 1)">Hapus Data</el-button>
          </template>
        </div>
      </template>
    </el-form>

    <template #footer>
      <div class="flex items-center justify-end space-x-2 mt-2">
        <el-button @click="close" size="small">Batal</el-button>
        <el-button type="primary" size="small" @click="handleSubmit" class="!font-bold">
          Simpan Bab Materi
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script>
export default {
  name: 'AddChapterModal',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    defaultChapterNumber: {
      type: Number,
      default: 1
    },
    subject: {
      type: Object,
      default: () => ({})
    },
    id:{
      type:String,
      default:''
    },
    semesterOptions: {
      type: Array,
      default: () => ([])
    },   
  },
  emits: ['close', 'saved'],
  data() {
    return {
      dataId:'',
      form: {
        id_semester:'',
        no: 1,
        materi: '',
        pertemuan: '1',
        jam: 18,
        cp:'',
        sch_aka_sub_materi:[
          {sub_materi:'', tujuan_pembelajaran:''}
        ],
      }
    };
  },
  watch: {
    defaultChapterNumber: {
      immediate: true,
      handler(newVal) {
        this.form.no = newVal || 1;
      }
    },
    'form.pertemuan'(val){
      this.form.jam = this.subject.jam * val;
    },
    show(val){
      if (val){
        this.resetForm()
        this.getData(this.id)
      }
    },
    id(val){
      this.getData(val)
    }
  },
  computed:{
    emptyMateriExist(){
      let filter = this.form.sch_aka_sub_materi.filter(d => {
        return d.sub_materi == '' || d.tujuan_pembelajaran == ''
      })
      console.log(filter)
      return filter?.length > 0
    }
  },
  methods: {
    close() {
      this.$emit('close');
    },
    getData(val){
      if (val > 0) {
        this.$http.get('mapel/materi/get?id=' + val)
          .then(res => { 
            let data = res?.data
            this.form.id_semester = data?.id_semester
            this.form.no = data?.no
            this.form.materi = data?.materi
            this.form.pertemuan = data?.pertemuan
            this.form.jam = data?.jam
            this.form.cp = data?.cp
            this.$http.post('mapel/sub-materi',window.jsonToFormData({
                where: {id_materi:val}
              }))
              .then(res => {
                let data = res?.data
                console.log(data)
                if (data?.length > 0)
                  this.form.sch_aka_sub_materi = data.map(d => {
                    return {sub_materi:d.sub_materi, tujuan_pembelajaran:d.tujuan_pembelajaran}
                  })
                else
                  this.form.sch_aka_sub_materi = [
                    {sub_materi:'', tujuan_pembelajaran:''}
                  ]
                console.log(this.form)
              })
          })
        
      }
    },
    resetForm() {
      this.form.no =this.defaultChapterNumber || 1
      this.form.id_semester = this.semesterOptions?.[0]?.value || 1
      this.form.materi =''
      this.form.pertemuan ='1'
      this.form.cp =''
      this.form.sch_aka_sub_materi = [
        {sub_materi:'', tujuan_pembelajaran:''}
      ]
    },
    handleSubmit() {
      if (!this.form.materi) return;
      let form = JSON.parse(JSON.stringify(this.form))
      form.tables = {
        sch_aka_sub_materi : form.sch_aka_sub_materi
      }
      delete  form.sch_aka_sub_materi
      form = window.jsonToFormData(form)
      form.delete('sch_aka_sub_materi')
      form.append('id', this.id)
      form.append('id_semester', this.subject.id_semester)
      form.append('id_mapel', this.subject.id_mapel)
      form.append('id_guru', this.subject.id_guru)
      form.append('tingkat', this.subject.tingkat)
      form.append('nama_fk[sch_aka_sub_materi]', 'id_materi')
      this.$http.post('mapel/materi/store', form)
        .then(res => {
          this.$emit('saved')
          this.$emit('close')
        })
    }
  },
};
</script>

<style scoped lang="postcss">
:deep(.el-form-item__label){
  @apply m-1 mt-2;
}
</style>
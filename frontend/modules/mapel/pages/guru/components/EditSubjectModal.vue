<template>
   <el-dialog
    :model-value="show"
    title="Edit Mata Pelajaran"
    class="min-w-[300px] max-w-[500px]"
    append-to-body
    :before-close="close"
    @close="close"
    destroy-on-close
  >
    <form-comp :fields="fields" v-model:formValue="form"
      cols="7 gap-y-2"
      href="mapel/pembagian/store" href-get="mapel/pembagian/get"
      :show-columns="['nama_mapel','kode_mapel','tingkat','minggu','jam','jam_per_pertemuan','pertemuan']"
      :pass-columns="['nama_mapel','kode_mapel','tingkat','minggu']"
      v-model:id="dataId"
      :show-required-text="false"
      label-position="top"
      submit-text="Simpan Data"
      @saved="$emit('saved'); close()"
      />
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
        nama_mapel:'',
        kode_mapel:'',
        tingkat:'',
        jam:'',
        jam_per_pertemuan:'',
        pertemuan:'',
        mingguL:'',
      },
      fields:{
        nama_mapel:{
          nama_kolom:'nama_mapel',
          label:'Mata Pelajaran',
          readonly: true,
          colspan:3,
        },
        kode_mapel:{
          nama_kolom:'kode_mapel',
          label:'Kode Mapel',
          readonly: true,
          colspan:2,
        },
        tingkat:{
          nama_kolom:'tingkat',
          label:'Kelas',
          readonly: true,
          colspan:2,
        },
        minggu:{
          nama_kolom:'minggu',
          label:'Minggu Aktif',
          readonly: true,
          colspan:2,
        },
        jam:{
          nama_kolom:'jam',
          readonly: true,
          label:'Jam ',
          colspan:2,
        },
        pertemuan:{
          nama_kolom:'pertemuan',
          label:'Pertemuan / Minggu ',
          function:'toNumber',
          colspan:3,
        },
        jam_per_pertemuan:{
          nama_kolom:'jam_per_pertemuan',
          label:"Jam / Pertemuan ( Jika beda, pisahkan dengan ';' ) ",
        },
      }
    };
  },
  watch: {
    show(val){
      
    },
    id(val){
      this.dataId = val
    }
  },
  computed:{
    
  },
  methods: {
    close() {
      this.$emit('close');
    },
  },
};
</script>

<template>
	<div
    class="bg-white/[0.9] rounded-[10px] shadow-md
    mb-3 p-4">
    <div class="text-[15px] mb-2">Nama Anggota :</div>
    <floating-select v-model:value="id" placeholder="Pilih Anggota"
      @change="$emit('change')"
      size="large"
      filterable
      :options="[
        ...[{ 
          value: anggotas.map(user => user.id_anggota).join(','),
          label:'Semua'
        }],
        ...anggotas.map((val) => {
          return {
            value:val.id_anggota,
            label:val.nama,
          }
        })
      ]">
    </floating-select>
  </div>
</template>

<script setup>

</script>

<script>
import { mapState, mapActions } from 'pinia';

export default {
  name: "filter-anggota",
	emits:['update:idAnggota','change'],
  props:{
    idAnggota:{
      type:[String, Number],
      default:null,
    }
  },
  computed: {
    ...mapState(useAuthStore, {
      user: 'loggedUser',
      anggotas:'data/anggotas'
    }),
  },
  data: function() {
    return {
      id:'',
		}
	},
  watch:{
    idAnggota:{
      immediate:true,
      handler(val){ this.id = val }
    },
    id(val){ 
      console.log(val)
      this.$emit('update:idAnggota', val)
     }
  },
	methods:{
    ...mapActions(useDataStore,['getAllAnggotaInGroup']),

	},
	mounted(){
    this.getAllAnggotaInGroup()
	}
}
</script>
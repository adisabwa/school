<template>
  <el-dialog v-model="showModal"
    :append-to-body="true"
    class="w-fit max-w-[80%] px-6
      bg-gradient-to-tl from-white/[0.8] from-30% to-teal-200/[0.4]">
    <template #header>
      <div class="text-center font-bold text-teal-700">Rekening Infak Lazismu</div>
    </template>
    <div class="mt-2 mb-5 text-teal-700">Silahkan transfer infaq anda ke salah satu rekening di bawah ini</div>
    <div v-for="(rek, key) in rekening" class="relative mb-5
      flex gap-3 items-center"
      @click="copyText(rek)">
      <img :src="`${$baseUrl}/assets/images/banks/${key}.png`" width="50px" 
        class="h-fit"/>
      <div class="leading-[1]" >
        <div class="italic text-md text-teal-700">{{ banks[key].label }}</div>
        <div class="text-xl font-bold text-teal-700">{{ rek }}</div>
      </div>
    </div>
    <div class="mt-0 mb-5 text-teal-700">
      Tambahkan angka 29 di akhir nominal infaq. Contoh :<br/>
      <span class="mt-1 text-xl font-bold">Rp.100.029</span>
    </div>
  </el-dialog>
</template>

<script setup>
  import banks from '@/helpers/bank'
</script>
<script>

export default {
  name:'lazismu',
  emits:['update:show'],
  props:{
    show:{type:Boolean, default:true},
  },
  data: function() {
    return {
      showModal:true,
      rekening:{
        bsi:7447778887,
        bprs:1221200065,
        bm:5040015881,
        bmt:1110116088,
        bjs:5032287174,
      }
    }
  },
  watch:{
    show:{
      immediate: true,
      handler(val){
        this.showModal = val
      }
    },
    showModal(val){
      this.$emit('update:show', val)
    }
  },
  methods:{

  }
}
</script>
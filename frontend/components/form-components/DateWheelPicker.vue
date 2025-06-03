<template>
  <div>
    <el-input v-model="labelModel" :placeholder="placeholder" 
      :clearable="clearable"
      :size="size"
      @clear="resetData"
      @click="showModal = true">
      <template #prepend v-if="prefix">
        {{ prefix }}
      </template>
    </el-input>
    <teleport to="body">
      <el-dialog v-model="showModal"
          :class="['max-w-[80%] p-0 py-4 mt-40 w-fit']"
          header-class="flex items-center"
          body-class="relative px-0 text-[16px] ">
        <div :class="['flex justify-center items-center gap-4 p-4 rounded-xl']" >
          <div class="shrink-0 relative h-[200px] w-[60px]">
            <icons icon="fe:arrow-up" class="absolute z-[20] left-1/2 -translate-x-1/2"/>
            <icons icon="fe:arrow-down" class="absolute z-[20] bottom-0 left-1/2 -translate-x-1/2"/>
            <ScrollPicker :options="days" v-model:modelValue="day" />
          </div>
          <div class="shrink-0 relative h-[200px] w-[60px]">
              <icons icon="fe:arrow-up" class="absolute z-[20] left-1/2 -translate-x-1/2"/>
              <icons icon="fe:arrow-down" class="absolute z-[20] bottom-0 left-1/2 -translate-x-1/2"/>
            <ScrollPicker :options="months" v-model:modelValue="month" />
          </div>
          <div class="shrink-0 relative h-[200px] w-[80px]">
            <icons icon="fe:arrow-up" class="absolute z-[20] left-1/2 -translate-x-1/2"/>
            <icons icon="fe:arrow-down" class="absolute z-[20] bottom-0 left-1/2 -translate-x-1/2"/>
            <ScrollPicker :options="years" v-model:modelValue="year" />
          </div>
        </div>
      </el-dialog>
    </teleport>
  </div>
</template>

<script>
import  { VueScrollPicker } from 'vue-scroll-picker'

export default {
  name: 'DateWheelPicker',
  components: { ScrollPicker:VueScrollPicker },
  emits:['update:value','change'],
  props:{
    value:{type:[String, Number], default: () => {
        const today = new Date()
        return today.toISOString().slice(0, 10) // YYYY-MM-DD
    },},
    placeholder:{type:[String], default:'',},
    size:{type:[String], default:'',},
    clearable:{type:[Boolean], default:false,},
    prefix:{type:[String], default:'',},
    valueFormat:{type:[String], default:'YYYY-MM-DD',},
    format:{type:[String], default:'DD MMMM YYYY',},
  },
  data() {
    const currentYear = new Date().getFullYear()

    return {
      vModel:'',
      showModal:false,
      labelModel:'',
      day: '',
      month: '',
      year: '',
      months: Array.from({ length: 12 }, (_, i) => {
        return {
          name:(i + 1).toString().padStart(2, '0'),
          value:(i + 1).toString().padStart(2, '0'),
        }
      }),
      years: Array.from({ length: 125 }, (_, i) => {
        return {
          name: (currentYear - i + 25).toString(),
          value: (currentYear - i + 25).toString(),
        }
      }),
    }
  },
  computed: {
    days() {
      const y = parseInt(this.year)
      const m = parseInt(this.month)
      const maxDays = new Date(y, m, 0).getDate()
      return Array.from({ length: maxDays }, (_, i) => (i + 1).toString().padStart(2, '0'))
    }
  },
  watch: {
    vModel(val){
      // console.log(val)
      this.selectOption(val)
      this.$emit('update:value', val)
    },
    value: {
      immediate: true,
      async handler(val) {
        // console.log('val', val)
        this.vModel = val;
      },
    },
    day:'emitDate',
    month(newMonth, oldMonth) {
      this.adjustDayIfNeeded()
      this.emitDate()
    },
    year(newYear, oldYear) {
      this.adjustDayIfNeeded()
      this.emitDate()
    },
    showModal(val){
      // console.log('show', val)
      if (!val)
        this.changedValue(this.vModel)
    }
  },
  methods: {
    resetData(){
      [this.year, this.month, this.day] = (this.isEmpty(this.value) ? this.dateNow() : this.value).split('-')
      this.emitDate()
    },
    changedValue(val){
      this.$emit('change',val)
    },
    emitDate() {
      const fullDate = `${this.year}-${this.month}-${this.day}`
      this.vModel = fullDate
    },
    adjustDayIfNeeded() {
      const maxDay = this.days.length
      if (parseInt(this.day) > maxDay) {
        this.day = maxDay.toString().padStart(2, '0')
      }
    },
    selectOption(val){
      this.labelModel = this.formatDate(val, this.format)
    }
  },
  created(){
    this.resetData()
  }
}
</script>

<style lang="postcss">
.vue-scroll-picker {
  height: 100px;
}
.vue-scroll-picker{
  @apply text-[20px];
  position:relative;
  width:100%;
  height:100%;
  overflow:hidden;
  -webkit-user-select:none;
  user-select:none}
  .vue-scroll-picker-rotator{position:absolute;
    left:0;
    right:0;
    top:calc(50% - 18px)
}
.vue-scroll-picker-rotator-transition{
    transition:top ease .15s
  }
  .vue-scroll-picker-item{
    text-align:center;
    line-height:36px;
    color:var(--text-color, #000);
}
.vue-scroll-picker-item[aria-selected=true]{
    @apply text-teal-500;
  }
  .vue-scroll-picker-item[data-value=""], .vue-scroll-picker-item[aria-disabled=true]{
      color:var(--disabled-text-color, #ccc);
  }
  .vue-scroll-picker-item[data-value=""][aria-selected=true],.vue-scroll-picker-item[aria-disabled=true][aria-selected=true]{
      color:var(--disabled-text-color, #cac);
  }
  .vue-scroll-picker-layer{
    position:absolute;
    left:0;
    right:0;
    top:0;
    bottom:0
}
.vue-scroll-picker-layer-top,.vue-scroll-picker-layer-selection,.vue-scroll-picker-layer-bottom{
    position:absolute;
    left:0;
    right:0
}
.vue-scroll-picker-layer-top{
    box-sizing:border-box;
    border-bottom:1px solid #c8c7cc;
    top:0;
    height:calc(50% - 1em);
    cursor:pointer;
    @apply bg-gradient-to-b from-white from-[30%] to-white/[0.5];
}
.vue-scroll-picker-layer-selection{
    top:calc(50% - 1em);
    bottom:calc(50% - 1em)
}
.vue-scroll-picker-layer-bottom{
    border-top:1px solid #c8c7cc;
    bottom:0;
    height:calc(50% - 1em);
    cursor:pointer;
    @apply bg-gradient-to-t from-white from-[30%] to-white/[0.5];
}


</style>

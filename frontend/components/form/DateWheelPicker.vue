<template>
  <div>
    <el-input v-model="labelModel" :placeholder="placeholder" 
      :clearable="clearable"
      :readonly="readonly"
      :size="size"
      @clear="resetData"
      @click="readonly ? true : (showModal = true)">
      <template #prepend v-if="prefix">
        {{ prefix }}
      </template>
    </el-input>
      <el-dialog v-model="showModal" 
        append-to-body
        @closed="$emit('close')"
          :class="['max-w-[80%] p-0 py-4 mt-40 w-fit']"
          header-class="flex items-center"
          body-class="relative px-0 text-[16px] ">
        <div :class="['flex justify-center items-center gap-4 p-4 rounded-xl']" >
          <div class="shrink-0 relative h-[200px] w-[60px]">
            <icons icon="fe:arrow-up" class="cursor-pointer absolute z-[20] left-1/2 -translate-x-1/2" @click="day = moveOptions(day, -1, days)"/>
            <icons icon="fe:arrow-down" class="cursor-pointer absolute z-[20] bottom-0 left-1/2 -translate-x-1/2" @click="day = moveOptions(day, +1, days)"/>
            <span class="locked-value" v-if="dayLocked">{{ day }}</span>
            <ScrollPicker v-else :options="days" v-model:modelValue="day" />
          </div>
          <div class="shrink-0 relative h-[200px] w-[60px]">
            <icons icon="fe:arrow-up" class="cursor-pointer absolute z-[20] left-1/2 -translate-x-1/2" @click="month = moveOptions(month, -1, months)"/>
            <icons icon="fe:arrow-down" class="cursor-pointer absolute z-[20] bottom-0 left-1/2 -translate-x-1/2" @click="month = moveOptions(month, 1, months)"/>
            <span class="locked-value" v-if="monthLocked">{{ month }}</span>
            <ScrollPicker v-else :options="months" v-model:modelValue="month" />
          </div>
          <div class="shrink-0 relative h-[200px] w-[80px]">
            <icons icon="fe:arrow-up" class="cursor-pointer absolute z-[20] left-1/2 -translate-x-1/2" @click="year = moveOptions(year, -1, years)"/>
            <icons icon="fe:arrow-down" class="cursor-pointer absolute z-[20] bottom-0 left-1/2 -translate-x-1/2" @click="year = moveOptions(year, 1, years)"/>
            <span class="locked-value" v-if="yearLocked">{{ year }}</span>
            <ScrollPicker v-else :options="years" v-model:modelValue="year" />
          </div>
        </div>
      </el-dialog>
  </div>
</template>

<script>
import { readonly } from 'vue';
import  { VueScrollPicker } from 'vue-scroll-picker'

export default {
  name: 'DateWheelPicker',
  components: { ScrollPicker:VueScrollPicker },
  emits:['update:value','change','close'],
  props:{
    value:{type:[String, Number], default: () => {
        const today = new Date()
        return today.toISOString().slice(0, 10) // YYYY-MM-DD
    },},
    placeholder:{type:[String], default:'',},
    size:{type:[String], default:'',},
    clearable:{type:[Boolean], default:false,},
    readonly:{type:[Boolean], default:false,},
    prefix:{type:[String], default:'',},
    valueFormat:{type:[String], default:'YYYY-MM-DD',},
    format:{type:[String], default:'DD MMMM YYYY',},
    dayLocked:{type:[Boolean], default:false,},
    monthLocked:{type:[Boolean], default:false,},
    yearLocked:{type:[Boolean], default:false,},
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
      [this.year, this.month, this.day] = (isEmpty(this.value) ? dateNow() : this.value).split('-')
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
    moveOptions(val, direction, options){
      // console.log(val, options)
      let ind = options.findIndex(d => typeof d == 'string' ? d == val : d.value == val)
      // console.log(ind)
      let newInd = (ind + direction)
      // console.log(newInd)
      let data = options[newInd]
      if (newInd >= 0 && newInd <= (options.length - 1))
        return typeof data == 'string' ? data : data.value
      else
        return val
    },
    selectOption(val){
      this.labelModel = formatDate(val, this.format)
      let days = val?.split('-') ?? []
      this.year = days[0] ?? ''
      this.month = days[1] ?? ''
      this.day = days[2] ?? ''
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
.locked-value {
  @apply absolute z-[20] top-1/2 -translate-y-1/2 left-1/2 -translate-x-1/2;
  @apply text-[var(--color-main-500)];
  font-size: 20px;
}
.vue-scroll-picker-item[aria-selected=true]{
    @apply text-[var(--color-main-500)];
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

<template>
  <div ref="floatingSelect">
    <el-input v-model="labelModel" :placeholder="placeholder" 
      :clearable="clearable"
      :size="size"
      @clear="clearData"
      @click="showModal = true"
      :input-style="{color: multiple ? 'lightgray' : 'black'}">
      <template #prepend v-if="prefix">
        {{ prefix }}
      </template>
    </el-input>
    <teleport to="body">
      <el-dialog v-model="showModal"
        :class="['min-w-[250px] max-w-[80%] p-0 py-4 mt-28',
          type == 'scroll' ? 'mt-40' : '',
        ]"
        header-class="flex items-center"
        body-class="relative px-0  text-[16px]">
        <template #header v-if="filterable">
          <el-input id="filterSelect" v-model="searchData" :placeholder="'Cari Data' + (allowCreate ? ' / Tambah Baru' : '')" clearable
            class="px-5" size="large"/>
        </template>
        <div v-if="type =='select'"
          ref="floatingScroll" class="relative max-h-[65vh] overflow-y-auto">
          <div v-for="o in optionsFilter"
            :data-id="o.value"
            :class="[`cursor-pointer px-5 py-1 active:bg-teal-100
              border-0 border-b border-solid border-teal-700/[0.3]`,
              (isClick(o.value) ? 'bg-teal-100' : '')
            ]"
            @click="clickData(o.value, multiple)">
            <template v-if="$slots.prefix">
              <slot name="prefix" />
            </template>
            {{ o.label }}
          </div>
          <div v-if="optionsFilter.length == 0"
            class="text-center text-[14px]">
            - Tidak ada data -
          </div>
          <template v-if="$slots.footer">
            <el-divider class="my-2"/>
            <slot name="footer" />
          </template>
        </div>
        <div v-else-if="type=='scroll'"
        :class="['flex justify-center items-center gap-4 p-4 rounded-xl ']">
          <div class="cursor-pointer relative h-[130px] w-[150px] mx-auto">
            <icons icon="fe:arrow-up" class="absolute z-[20] left-1/2 -translate-x-1/2"/>
            <ScrollPicker :options="optionsFilter" v-model:modelValue="vModel" />
            <icons icon="fe:arrow-down" class="absolute z-[20] bottom-0 left-1/2 -translate-x-1/2"/>
          </div>
        </div>
      </el-dialog>
    </teleport>
  </div>
</template>

<script>

import { h } from 'vue';
import  { VueScrollPicker } from 'vue-scroll-picker'

export default {
  name:'floating-select',
  components:{
    ScrollPicker:VueScrollPicker,
  },
  emits:['update:value','change'],
  props:{
    dataInput:{type:[String, Number, Array], default:'',},
    value:{type:[String, Number, Array], default:'',},
    placeholder:{type:[String], default:'',},
    size:{type:[String], default:'',},
    filterable:{type:[Boolean], default:false,},
    clearable:{type:[Boolean], default:false,},
    multiple:{type:[Boolean], default:false,},
    allowCreate:{type:[Boolean], default:false,},
    options:{type:[Array, Object], default:[],},
    type:{type:[String], default:'select',},
    prefix:{type:[String], default:'',},
  },
  data: function() {
    return {
      vModel:'',
      showModal:false,
      searchData:'',
      labelModel:'',
      listOptions:[],
      newOption:{},
    }
  },
  computed:{
    optionsFilter: function() {
      var q = this.searchData.toLowerCase();
      if (q.length > 0) {
        let exact = this.listOptions.filter(data => {
            return data.label == q
          });
        let data = this.listOptions.filter(data => {
            return data.label.toLowerCase().includes(q)
          });
        if (this.allowCreate && exact.length == 0) {
          this.newOption =  {
            value: q,
            label:this.ucFirst(q),
          }
          return [...data, ...[this.newOption]]
        } else {
          return data
        }
      }
      return this.listOptions;
    },  
  },
  watch:{
    showModal:{
      immediate: true,
        async handler(val) {
        // console.log(val, this.vModel)
        let vm = this
        if (val) {
          setTimeout(() => {
            vm.jquery('#filterSelect.el-input__inner')[0]?.focus();
            // console.log(jquery(this.$refs.floatingScroll))
            this.scrollElement(jquery(this.$refs.floatingScroll),`[data-id="${vm.vModel}"]`,1,'top', false)
          }, 500)
          vm.searchData = ''
          if (vm.isEmpty(vm.vModel)) {
            vm.clickData(vm.listOptions[0]?.value, true)
          }
        } else {
          vm.changedValue(vm.vModel)
        }
      },
    },
    dataInput(val){
      if (val) {
        this.vModel = val
        // this.changedValue(val)
      }
    },
    vModel:{
      deep: true,
      handler(val) {
        // console.log('model', val)
        this.selectOption(val)
        this.$emit('update:value', val)
      },
    },
    value: {
      immediate: true,
      deep: true,
      async handler(val) {
        // console.log(this.placeholder, val)
        this.vModel = val;
      },
    },
    options: {
      immediate: true,
      deep:true,
      async handler(val) {
      // console.log(val)
        let pre = this.prefix
        let opt = typeof val == 'object' ? Object.values(val) : val
        opt.forEach(d => {
          d.name = pre + ' ' + d.label
        })
        this.listOptions = opt
        this.selectOption(this.vModel)
      }
    }
  },
  methods:{
    changedValue(val){
      // console.log('change float')
      this.$emit('change',val)
    },
    selectOption(val){
      // console.log('selectOption', val)
      let array = []
      if (Array.isArray(val)) {
        array = val
      } else {
        array = [val]
      }
      let filter = this.listOptions.filter(d => {
        return array.includes(d.value)
      })
      this.labelModel = filter[0]?.label ?? this.newOption?.label
      if (Array.isArray(val)) {
        if (val.length > 1) {
          this.labelModel = `[${(filter?.length - 1)}+] - ` + this.labelModel
        }
      }
      // console.log('selectOption', this.labelModel)
    },
    clickData(val, show = false){
      // console.log('clickData', val)
      if (this.multiple) {
        let index = this.vModel.indexOf(val)
        if (index > -1) {
          this.vModel.splice(index, 1)
        } else {
          this.vModel.push(val)
        }
      } else {
        this.vModel = val
      }
      this.showModal = show
    },
    isClick(val){
      // console.log('isClick', val)
      if (this.multiple) {
        return this.vModel.indexOf(val) > -1
      } else {
        return this.vModel == val
      }
    },
    clearData(){
      this.searchData = ''
      this.labelModel = ''
      if (this.multiple) {
        this.vModel = []
      } else {
        this.vModel = ''
      }
      this.changedValue(this.vModel)
    }
  },
  mounted(){
    // console.log('mounted', this.vModel)
    if (this.multiple && !Array.isArray(this.vModel)) {
      this.vModel = []
    }
  },

}
</script>
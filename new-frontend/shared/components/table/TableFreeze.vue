<template>
  <div class="w-full">
    <div id="freeze-container" class="mx-3 overflow-x-hidden w-full h-full">
      <div></div>
    </div>
    <floating-scroll :key="floatKey"
      v-fixed-to-position="'90vh'"
      target="#table-base-wrapper"
      offset-y="90vh"
      width="95%"/>
    <div id="table-base-wrapper" class="mx-3 overflow-x-auto mb-5" @scroll="(event) => {
      let tFreezeHead = jquery('#table-freeze-head')
      let target = event.target
      let scrollLeft = target.scrollLeft
      let left = scrollLeft - 13
      tFreezeHead.css({left: -left + 'px'})
    }">
      <table id="table-base" class=" table mt-1 md:text-[14px] text-[12px] leading-[1.5] mb-4">
        <thead class="bg-slate-100">
          <slot name="header" :data="data"></slot>
        </thead>
        <tbody>
          <slot name="body" :data="data"></slot>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TableFreeze',
  props: {
    data: {
      type: Array,
      required: true,
      default: () => [],
    },
    showHidden: {
      type: Number,
      required: false,
      default: 150,
    },
  },
  data() {
    return {
      scrollY: 0,
      floatKey: 0,
    }
  },
  watch: {
    data: {
      handler() {
        this.$nextTick(() => {
          this.getFreezeHeader()
          this.floatKey++
        })
      },
      deep: true
    }
  },
  methods: {
    getFreezeHeader(){
      // return;
      // console.log('freeze-header')
      let tBase = jquery('#table-base')
      let tFreezeContainer = jquery('#freeze-container')
      tFreezeContainer.empty() 
      // console.log(tBase, tFreezeContainer)

      let tBodyFreeze = tBase.clone(true)
      this.removeColumnByClass(tBodyFreeze, [], 'fixed-col')
      tBodyFreeze.attr('id', 'table-freeze-body')
      tBodyFreeze.appendTo(tFreezeContainer)
      tBodyFreeze.css({position:'absolute'})

      let tHeadFreeze = tBase.clone(true).find('tbody').remove().end()
      tHeadFreeze.attr('id', 'table-freeze-head')
      tHeadFreeze.appendTo(tFreezeContainer) 
      tHeadFreeze.css({position:'absolute'})

      let tHeadBodyFreeze = tBodyFreeze.clone().find('tbody').remove().end()
      tHeadBodyFreeze.attr('id', 'table-freeze-head-body')
      tHeadBodyFreeze.appendTo(tFreezeContainer) 
      tHeadBodyFreeze.css({position:'absolute'})
      // console.log(tBodyFreeze)
      // console.log(tHeadFreeze.width(), tBase.width())

      let thBase = tBase.find('th')
      let trBaseHead = tBase.find('thead tr')
      let trBaseBody = tBase.find('tbody tr')
      let thBodyFreeze = tBodyFreeze.find('th')
      
      let thFreeze = tHeadFreeze.find('th')
      let thHeadBodyFreeze = tHeadBodyFreeze.find('th')
      // // console.log(tHeadFreeze, tBase, tFreeze, thFreeze)
      let trBodyFreezeHead = tBodyFreeze.find('thead tr')
      let trBodyFreezeBody = tBodyFreeze.find('tbody tr')

      let keys = Object.keys(thFreeze)
      
      for (let i = 0; i < keys.length; i++) {
        const key = keys[i]
        let elBase = jquery(thBase[key])
        let elFreeze = jquery(thFreeze[key])
        let elBodyFreeze = jquery(thBodyFreeze[key])
        let elHeadBodyFreeze = jquery(thHeadBodyFreeze[key])
        // console.log(elBase, elFreeze)
        // continue;
        try {
          // console.log(elBase.width(), elFreeze.width())
          elFreeze.width(elBase.width())
          elBodyFreeze.width(elBase.width())
          elHeadBodyFreeze.width(elBase.width())
          
          // jquery(elFreeze).width(elBase.width)
      //     elFreeze.style.width = elBase.outerWidth + 'px'
      //     // let baseF = Math.ceil(elFreeze.width)
      //     // elBase.width(baseF)
      //     console.log(elBase.width, elFreeze.width)
        } catch(err) {
          // console.log(err)
        }
      }

      trBodyFreezeHead.each((index, tr) => {
        let trF = jquery(jquery(tr).find('th')[0])
        let trB = jquery(jquery(trBaseHead[index]).find('th')[0])
        trF.height(trB.height()) 
      })

      trBodyFreezeBody.each((index, tr) => {
        let trF = jquery(jquery(tr).find('td')[0])
        let trB = jquery(jquery(trBaseBody[index]).find('td')[0])
        // console.log(trF, trB)
        trF.height(trB.height())
        // console.log(trF.height(), trB.height())
      })
        // var targetOffset = tBase[0].getBoundingClientRect(); // relative to viewport
        // tFreeze.css({
        //   left: targetOffset.left + 'px'
        // });
      this.addStyletoHeader()
      
    },
    addStyletoHeader(){
      let tHeadFreeze = jquery('#table-freeze-head')
      let tHeadBodyFreeze = jquery('#table-freeze-head-body')
      let tBase = jquery('#table-base')
      let tBodyFreeze = jquery('#table-freeze-body')
      tBodyFreeze.css({
        zIndex: 9997,
        width: 'auto',
        height: 'auto',
        // height: tBase.height() + 'px',
      })
      tHeadFreeze.css({
        zIndex: 9998,
        top: (this.scrollY - 210) + 'px',
        width: tBase.width() + 'px',
        opacity: this.scrollY < this.showHidden ? 0 : 1,
        visibility: this.scrollY < this.showHidden ? 'hidden' : 'visible',
      })
      tHeadBodyFreeze.css({
        zIndex: 9999,
        top: (this.scrollY - 210) + 'px',
        width: tBodyFreeze.width() + 'px',
        height: tHeadFreeze.height() + 'px',
        opacity: this.scrollY < this.showHidden ? 0 : 1,
        visibility: this.scrollY < this.showHidden ? 'hidden' : 'visible',
      })
    },
  },
  mounted() {
    window.addEventListener('scroll', () => {
      this.scrollY = window.scrollY
      this.addStyletoHeader()
      // console.log(this.scrollY)
    })
  },
}
</script>
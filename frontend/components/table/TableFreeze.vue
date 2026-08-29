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
      let tFreezeHead = getEl('#table-freeze-head')
      let target = event.target
      let scrollLeft = target.scrollLeft
      let left = scrollLeft - 13
      tFreezeHead.style.left = -left + 'px'
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
  setup() {
    return {
      getEl, removeColumnByClass
    }
  },
  props: {
    data: { type: Array, required: true, default: () => [] },
    showHidden: { type: Number, required: false, default: 150 },
    topOffset: { type: Number, required: false, default: 100 },
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
          setTimeout(() => {
            this.getFreezeHeader()
            this.floatKey++
          }, 200)
        })
      },
      deep: true
    }
  },
  methods: {
    // Handler Scroll Tabel Horizontal
    handleTableScroll(event) {
      const tHeadFreeze = document.querySelector('#table-freeze-head');
      if (!tHeadFreeze) return;
      
      const scrollLeft = event.target.scrollLeft;
      const left = scrollLeft - 13;
      tHeadFreeze.style.left = `-${left}px`;
    },

    getFreezeHeader() {
      const tBase = document.querySelector('#table-base');
      const tFreezeContainer = document.querySelector('#freeze-container');
      if (!tBase || !tFreezeContainer) return;

      // Kosongkan container (Ganti tFreezeContainer.empty())
      tFreezeContainer.innerHTML = '';

      // 1. Clone Body Freeze (Kolom Tetap)
      const tBodyFreeze = tBase.cloneNode(true);
      removeColumnByClass(tBodyFreeze, [], 'fixed-col'); // Helper kita
      tBodyFreeze.id = 'table-freeze-body';
      tBodyFreeze.style.position = 'absolute';
      tFreezeContainer.appendChild(tBodyFreeze);

      // 2. Clone Head Freeze (Header Melayang)
      const tHeadFreeze = tBase.cloneNode(true);
      const tbodyInHead = tHeadFreeze.querySelector('tbody');
      if (tbodyInHead) tbodyInHead.remove();
      tHeadFreeze.id = 'table-freeze-head';
      tHeadFreeze.style.position = 'absolute';
      tFreezeContainer.appendChild(tHeadFreeze);

      // 3. Clone Head Body Freeze (Pojok Kiri Atas Tetap)
      const tHeadBodyFreeze = tBodyFreeze.cloneNode(true);
      const tbodyInHeadBody = tHeadBodyFreeze.querySelector('tbody');
      if (tbodyInHeadBody) tbodyInHeadBody.remove();
      tHeadBodyFreeze.id = 'table-freeze-head-body';
      tHeadBodyFreeze.style.position = 'absolute';
      tFreezeContainer.appendChild(tHeadBodyFreeze);

      // Sinkronisasi Lebar dan Tinggi (Ganti loop jQuery)
      this.syncDimensions(tBase, tHeadFreeze, tBodyFreeze, tHeadBodyFreeze);
      
      this.addStyletoHeader();
    },

    syncDimensions(tBase, tHeadFreeze, tBodyFreeze, tHeadBodyFreeze) {
      const baseThs = tBase.querySelectorAll('thead th');
      const headFreezeThs = tHeadFreeze.querySelectorAll('thead th');
      const bodyFreezeThs = tBodyFreeze.querySelectorAll('thead th');
      const headBodyFreezeThs = tHeadBodyFreeze.querySelectorAll('thead th');

      // Sync Width
      baseThs.forEach((th, i) => {
        const width = th.getBoundingClientRect().width + 'px';
        if (headFreezeThs[i]) headFreezeThs[i].style.width = width;
        if (bodyFreezeThs[i]) bodyFreezeThs[i].style.width = width;
        if (headBodyFreezeThs[i]) headBodyFreezeThs[i].style.width = width;
      });

      // Sync Height Header
      const baseHeadTrs = tBase.querySelectorAll('thead tr');
      const bodyFreezeHeadTrs = tBodyFreeze.querySelectorAll('thead tr');
      const height = tBase.querySelector('thead').getBoundingClientRect().height + 'px';
      const targetHead = tBodyFreeze.querySelector('thead');
      if (targetHead) targetHead.style.height = height;
      bodyFreezeHeadTrs.forEach((tr, i) => {
        const height = baseHeadTrs[i].getBoundingClientRect().height + 'px';
        const targetTd = tr.querySelector('th') || tr.querySelector('td');
        if (targetTd) targetTd.style.height = height;
      });

      // Sync Height Body
      const baseBodyTrs = tBase.querySelectorAll('tbody tr');
      const bodyFreezeBodyTrs = tBodyFreeze.querySelectorAll('tbody tr');
      bodyFreezeBodyTrs.forEach((tr, i) => {
        if (!baseBodyTrs[i]) return;
        const height = baseBodyTrs[i].getBoundingClientRect().height + 'px';
        const targetTd = tr.querySelector('td') || tr.querySelector('th');
        if (targetTd) targetTd.style.height = height;
      });
    },

    addStyletoHeader() {
      const tHeadFreeze = document.querySelector('#table-freeze-head');
      const tHeadBodyFreeze = document.querySelector('#table-freeze-head-body');
      const tBase = document.querySelector('#table-base');
      const tBodyFreeze = document.querySelector('#table-freeze-body');

      if (!tBase || !tHeadFreeze) return;

      const isHidden = this.scrollY < this.showHidden;
      const topPos = `${this.scrollY - this.showHidden - this.topOffset}px`;

      // Style Body Freeze
      if (tBodyFreeze) {
        Object.assign(tBodyFreeze.style, {
          zIndex: '9997',
          width: 'auto',
          height: 'auto'
        });
      }

      // Style Head Freeze
      Object.assign(tHeadFreeze.style, {
        zIndex: '9998',
        top: topPos,
        width: `${tBase.offsetWidth}px`,
        opacity: isHidden ? '0' : '1',
        visibility: isHidden ? 'hidden' : 'visible'
      });

      // Style Head Body Freeze
      if (tHeadBodyFreeze) {
        Object.assign(tHeadBodyFreeze.style, {
          zIndex: '9999',
          top: topPos,
          width: `${tBodyFreeze.offsetWidth}px`,
          height: `${tHeadFreeze.offsetHeight}px`,
          opacity: isHidden ? '0' : '1',
          visibility: isHidden ? 'hidden' : 'visible'
        });
      }
    }
  },
  mounted() {
    window.addEventListener('scroll', () => {
      this.scrollY = window.scrollY;
      this.addStyletoHeader();
    });
    
    // Initial build
    if (this.data.length > 0) {
      this.getFreezeHeader();
    }
  },
  unmounted() {
    // Jangan lupa hapus event listener saat komponen dihancurkan
    window.removeEventListener('scroll', this.addStyletoHeader);
  }
}
</script>
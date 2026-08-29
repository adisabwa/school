<template>
  <div class="flex items-center justify-center gap-x-1 max-w-[300px]">
    <div
      v-for="i in 3"
      :key="i"
      class="relative inline-block leading-[0]"
    >
      <!-- Colored Star -->
      <img
        :id="`#${i}${id}`"
        :src="`${$baseUrl}assets/images/star.png`"
        :width="width"
        :class="getClass(i)"
        class="absolute z-[1] block w-full h-full top-0 left-0"
      />

      <!-- Gray Star Background -->
      <img
        :id="`#${i}2${id}`"
        :src="`${$baseUrl}assets/images/star-gray.png`"
        :width="width"
        :class="classBack"
      />
    </div>
  </div>
</template>

<script>
  export default {
    name: 'Star',
    props: {
      width: {
        type: String,
        default: '20px',
      },
      id: {
        type: String,
        default: 'star',
      },
      count: {
        type: [String, Number],
        default: -1,
      },
    },
    data() {
      return {
        defaultClass: 'animate scale-0 h-full',
      };
    },
    computed: {
      classBack() {
        return this.count >= 0
          ? [this.defaultClass, 'scale-100'].join(' ')
          : this.defaultClass;
      },
    },
    methods: {
      getClass(index) {
        let cls = '';
        if (this.count >= 4 - index) {
          cls = 'scale-100';
        } else if (this.count >= 0) {
          cls = 'scale-100 opacity-0';
        }
        return [this.defaultClass, cls].join(' ');
      },
    },
  };

</script>
<template>
  <div ref="floatBar" class="floating-scroll custom-scrollbar">
    <div ref="floatInner"></div>
  </div>
</template>

<script>
export default {
  name: "FloatingScroll",

  props: {
    target: { type: String, required: true }, // CSS selector
    offsetY: { type: String, default: '90vh' },   // distance from bottom
    width: { type: String, default: "80vw" }  // floating bar width
  },

  mounted() {
    const target = document.querySelector(this.target);
    const floatBar = this.$refs.floatBar;
    const floatInner = this.$refs.floatInner;

    if (!target) {
      console.error(`FloatingScroll: target "${this.target}" not found.`);
      return;
    }

    // Apply initial style
    floatBar.style.top = `calc(${this.offsetY})`;
    floatBar.style.width = this.width;

    // Set inner width
    const updateWidth = () => {
      floatInner.style.width = target.scrollWidth + "px";
    };

    updateWidth();

    // Scroll sync → floating bar → target
    floatBar.addEventListener("scroll", () => {
      target.scrollLeft = floatBar.scrollLeft;
    });

    // Scroll sync → target → floating bar
    target.addEventListener("scroll", () => {
      floatBar.scrollLeft = target.scrollLeft;
    });

    // Recalculate width on resize
    window.addEventListener("resize", updateWidth);
  }
};
</script>

<style lang="postcss">
.floating-scroll {
  position: fixed;
  height: 8px;
  overflow-x: auto;
  overflow-y: hidden;
  background: #ddd;
  border-radius: 4px;
  z-index: 9999;
  &::-webkit-scrollbar {
    height: 8px;
    background-color:   white;
  }
  &::-webkit-scrollbar-thumb {
    width: 100px;
    background-color: #c6dbdb;
    border-radius: 5px;
  }
}

.floating-scroll > div {
  height: 1px;
}
</style>

<template>
  <div
    ref="container"
    :class="containerClass"
    :style="{ cursor: isDragging ? 'grabbing' : 'grab', scrollBehavior: 'auto', userSelect: isDragging ? 'none' : '' }"
    @mousedown="onMouseDown"
    @mousemove="onMouseMove"
    @mouseup="onMouseUp"
    @mouseleave="onMouseUp"
    @touchstart.passive="onTouchStart"
    @touchmove.passive="onTouchMove"
    @touchend="onTouchEnd"
  >
    <slot />
  </div>
</template>
  
<script>
export default {
  name: 'DragScroll',
  emits:['drag-start','drag-move','drag-end','snap','momentum-end','scroll','scroll-start','scroll-end'],
  props: {
    axis: {
      type: String,
      default: 'x', // 'x', 'y', or 'both'
    },
    friction: {
      type: Number,
      default: 0.9,
    },
    snap: {
      type: Boolean,
      default: true,
    },
    syncWith: {
      // Accept a single ref or an array of refs
      type: [Object, Array],
      default: null,
    },
    disableEmit: {
      type: Boolean,
      default: false,
    }
  },
  data() {
    return {
      isDragging: false,
      isScrolling: false,
      startX: 0,
      startY: 0,
      scrollLeft: 0,
      scrollTop: 0,
      velocityX: 0,
      velocityY: 0,
      lastX: 0,
      lastY: 0,
      lastTime: 0,
      animationFrame: null,
    };
  },
  computed: {
    containerClass() {
      const classes = [];
      if (this.axis === 'x') classes.push('overflow-x-auto');
      if (this.axis === 'y') classes.push('overflow-y-auto');
      if (this.axis === 'both') classes.push('overflow-auto');
      return classes;
    }
  },
  methods: {
    cancelMomentum() {
      cancelAnimationFrame(this.animationFrame);
    },
    applyMomentum() {
      const step = () => {
        const el = this.$refs.container;
        if (this.axis === 'x' || this.axis === 'both') el.scrollLeft -= this.velocityX * 20;
        if (this.axis === 'y' || this.axis === 'both') el.scrollTop -= this.velocityY * 20;

        this.velocityX *= this.friction;
        this.velocityY *= this.friction;

        const moving = Math.abs(this.velocityX) > 0.02 || Math.abs(this.velocityY) > 0.02;

        const atXEdge =
          el.scrollLeft <= 0 ||
          el.scrollLeft >= el.scrollWidth - el.clientWidth;

        const atYEdge =
          el.scrollTop <= 0 ||
          el.scrollTop >= el.scrollHeight - el.clientHeight;

        const hitBoundary =
          (this.axis === 'x' && atXEdge) ||
          (this.axis === 'y' && atYEdge) ||
          (this.axis === 'both' && (atXEdge || atYEdge));

        if (moving && !hitBoundary) {
          this.animationFrame = requestAnimationFrame(step);
        } else {
          // 👇 Only snap if enabled and we've reached end of momentum
          if (this.snap) {
            this.snapToChild();
          } else {
            this.$emit('momentum-end');
          }
        }
      };

      step();
    },
    onMouseDown(e) {
      this.startDrag(e.pageX, e.pageY);
    },
    onMouseMove(e) {
      this.handleDrag(e.pageX, e.pageY);
    },
    onMouseUp() {
      this.endDrag();
    },
    onTouchStart(e) {
      const touch = e.touches[0];
      this.startDrag(touch.pageX, touch.pageY);
    },
    onTouchMove(e) {
      const touch = e.touches[0];
      this.handleDrag(touch.pageX, touch.pageY);
    },
    onTouchEnd() {
      this.endDrag();
    },
    startDrag(x, y) {
      this.isDragging = true;
      this.cancelMomentum();
      this.startX = this.lastX = x;
      this.startY = this.lastY = y;
      this.scrollLeft = this.$refs.container.scrollLeft;
      this.scrollTop = this.$refs.container.scrollTop;
      this.lastTime = Date.now();
      this.$emit('drag-start');
    },
    handleDrag(x, y) {
      if (!this.isDragging) return;
      const now = Date.now();
      const dx = x - this.lastX;
      const dy = y - this.lastY;
      const dt = now - this.lastTime;

      this.velocityX = dx / dt;
      this.velocityY = dy / dt;

      if (this.axis === 'x' || this.axis === 'both') this.$refs.container.scrollLeft -= dx;
      if (this.axis === 'y' || this.axis === 'both') this.$refs.container.scrollTop -= dy;

      this.lastX = x;
      this.lastY = y;
      this.lastTime = now;
      this.$emit('drag-move', { dx, dy });
    },
    endDrag() {
      if (!this.isDragging) return;
      this.isDragging = false;
      this.$emit('drag-end');
      this.applyMomentum();
    },
    snapToChild() {
      const el = this.$refs.container;
      const children = Array.from(el.children);
      const isSnapChild = (child) => {
        const align = getComputedStyle(child).scrollSnapAlign;
        return ['start', 'center', 'end'].includes(align);
      };

      const snapChildren = children.filter(isSnapChild);
      if (!snapChildren.length) return;

      const scroll = this.axis === 'y' ? el.scrollTop : el.scrollLeft;

      let closestChild = null;
      let closestDist = Infinity;
      for (const child of snapChildren) {
        const offset = this.axis === 'y' ? child.offsetTop : child.offsetLeft;
        const dist = Math.abs(offset - scroll);
        if (dist < closestDist) {
          closestDist = dist;
          closestChild = child;
        }
      }

      // console.log(closestChild)
      if (!closestChild) return;
      const targetOffset = this.axis === 'y' ? closestChild.offsetTop : closestChild.offsetLeft;
      const direction = this.axis === 'y' ? 'top' : 'left'

      // console.log(targetOffset, this.axis)
      this.scrollToCoordinate(el, targetOffset, 0.3, direction, false, () => {
        this.$emit('snap', {
          offset: targetOffset,
          el: closestChild
        });
      });
    },
    onScroll(e) {
      // console.log('scroll', this.syncWith, this.$refs)
      if (this.disableEmit) return;

      const el = this.$refs.container;

      // 🔄 Emit scroll position
      this.$emit('scroll', {
        scrollLeft: el.scrollLeft,
        scrollTop: el.scrollTop,
        event: e
      });

      // Scroll sync logic:
      if (!this._syncing && this.syncWith) {
        this._syncing = true;

        const targets = Array.isArray(this.syncWith)
          ? this.syncWith
          : [this.syncWith];

        targets.forEach(targetRef => {
          if (
            targetRef &&
            targetRef !== this &&
            typeof targetRef.setScroll === 'function'
          ) {
            targetRef.setScroll({
              left: el.scrollLeft,
              top: el.scrollTop,
            });
          }
        });

        requestAnimationFrame(() => {
          this._syncing = false;
        });
      }
      // 🟢 First scroll frame
      if (!this.isScrolling) {
        this.isScrolling = true;
        this.$emit('scroll-start');
      }

      // 🛑 Reset scroll-end timeout
      clearTimeout(this.scrollTimeout);
      this.scrollTimeout = setTimeout(() => {
        this.isScrolling = false;
        this.$emit('scroll-end');
      }, 150);
    },
    setScroll({ left, top }) {
      const el = this.$refs.container;
      if (typeof left === 'number') el.scrollLeft = left;
      if (typeof top === 'number') el.scrollTop = top;
    },
  },
  mounted() {
    this.$refs.container.addEventListener('scroll', this.onScroll);
  },
  beforeUnmount() {
    this.cancelMomentum();
    this.$refs.container.removeEventListener('scroll', this.onScroll);
    clearTimeout(this.scrollTimeout);
  }
};
</script>
  
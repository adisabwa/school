import { listFunction} from '@/config/plugins/ui-functions'

// plugins/directives.js
const clickOutsideDirective = {
  beforeMount(el, binding) {
    el.__clickOutsideHandler__ = (event) => {
      // Check if element is visible in DOM
      const isVisible = el.offsetParent !== null;
      // Only trigger if the element is visible AND the click was outside
      const clickedOutside = !el.contains(event.target);

      // console.log(isVisible, clickedOutside)
      if (isVisible && clickedOutside) {
        // console.log(binding.value)
        binding.value(event);
      }
    };

    document.addEventListener('click', el.__clickOutsideHandler__);
  },
  unmounted(el) {
    document.removeEventListener('click', el.__clickOutsideHandler__);
    delete el.__clickOutsideHandler__;
  }
};

const clickExcludeIdDirective = {
  beforeMount(el, binding) {
    const handler = binding.value
    const excludeIds = binding.arg ? binding.arg.split(',') : [];

    el.__clickOutsideHandler__ = (event) => {
      

      const isClickInside = el.contains(event.target)
      const isClickOnExcludedId = excludeIds.some(id =>
        event.target.closest(`#${id}`)
      )

      if (handler === undefined) {
        console.warn('clickExcludeIdDirective: handler is not defined')
        return
      }

      if (!isClickInside && !isClickOnExcludedId) {
        handler(event)
      }
    }

    document.addEventListener('click', el.__clickOutsideHandler__)
  },
  unmounted(el) {
    document.removeEventListener('click', el.__clickOutsideHandler__)
    delete el.__clickOutsideHandler__
  }
};

// vue-drag-scroll.js
const dragScroll = {
    mounted(el) {
      let isDragging = false;
      let startX = 0,
        startY = 0;
      let scrollLeft = 0,
        scrollTop = 0;
      let lastX = 0,
        lastY = 0;
      let velocityX = 0,
        velocityY = 0;
      let lastTime = 0;
      let animationFrame;

      const friction = 0.9;
      const minVelocity = 0.02;

      function cancelMomentum() {
        cancelAnimationFrame(animationFrame);
      }

      function applyMomentum() {
        function momentumStep() {
          el.scrollLeft -= velocityX * 20;
          el.scrollTop -= velocityY * 20;

          velocityX *= friction;
          velocityY *= friction;

          if (Math.abs(velocityX) > minVelocity || Math.abs(velocityY) > minVelocity) {
            animationFrame = requestAnimationFrame(momentumStep);
          } else {
            snapToNearestChild(el)
          }
        }
        momentumStep();
      }

      // Snap to nearest child after momentum ends
      function snapToNearestChild(el) {
        const children = Array.from(el.children);
        if (!children.length) return;

        const scrollLeft = el.scrollLeft;
        let closestOffset = null;
        let closestDist = Infinity;

        for (const child of children) {
          const offset = child.offsetLeft;
          const dist = Math.abs(offset - scrollLeft);
          if (dist < closestDist) {
            closestDist = dist;
            closestOffset = offset;
          }
        }

        if (closestOffset != null) {
          console.log('offset', closestOffset)
          listFunction.scrollToCoordinate(el, closestOffset, 0.5);
        }
      }

      function startDrag(e) {
        isDragging = true;
        cancelMomentum();
        startX = lastX = e.pageX;
        startY = lastY = e.pageY;
        scrollLeft = el.scrollLeft;
        scrollTop = el.scrollTop;
        lastTime = Date.now();

        // Change cursor and prevent text selection
        el.style.cursor = 'grabbing';
        el.style.userSelect = 'none';
        el.classList.remove('snap-x')
        el.classList.remove('snap-mandatory')
      }

      function onDrag(e) {
        if (!isDragging) return;
        e.preventDefault();

        const now = Date.now();
        const dx = e.pageX - lastX;
        const dy = e.pageY - lastY;

        velocityX = dx / (now - lastTime);
        velocityY = dy / (now - lastTime);

        lastTime = now;
        lastX = e.pageX;
        lastY = e.pageY;

        el.scrollLeft -= dx;
        el.scrollTop -= dy;
      }

      function endDrag() {
        if (isDragging) {
          isDragging = false;
          applyMomentum();

          // Restore cursor and selection
          el.style.cursor = 'grab';
          el.style.userSelect = '';
        }
      }

      function startTouch(e) {
        if (e.touches.length !== 1) return;
        const touch = e.touches[0];
        isDragging = true;
        cancelMomentum();
        startX = lastX = touch.pageX;
        startY = lastY = touch.pageY;
        scrollLeft = el.scrollLeft;
        scrollTop = el.scrollTop;
        lastTime = Date.now();
      }

      function onTouch(e) {
        if (!isDragging || e.touches.length !== 1) return;

        const touch = e.touches[0];
        const now = Date.now();
        const dx = touch.pageX - lastX;
        const dy = touch.pageY - lastY;

        velocityX = dx / (now - lastTime);
        velocityY = dy / (now - lastTime);

        lastTime = now;
        lastX = touch.pageX;
        lastY = touch.pageY;

        el.scrollLeft -= dx;
        el.scrollTop -= dy;
      }

      function endTouch() {
        if (isDragging) {
          isDragging = false;
          applyMomentum();
        }
      }

      // Set initial cursor style
      el.style.cursor = 'grab';

      // Add event listeners
      el.addEventListener('mousedown', startDrag);
      el.addEventListener('mousemove', onDrag);
      el.addEventListener('mouseup', endDrag);
      el.addEventListener('mouseleave', endDrag);

      el.addEventListener('touchstart', startTouch);
      el.addEventListener('touchmove', onTouch);
      el.addEventListener('touchend', endTouch);

      // Cleanup on unmount
      el._dragScrollCleanup = () => {
        el.classList.remove('active:cursor-grab')
        cancelMomentum();
        el.removeEventListener('mousedown', startDrag);
        el.removeEventListener('mousemove', onDrag);
        el.removeEventListener('mouseup', endDrag);
        el.removeEventListener('mouseleave', endDrag);

        el.removeEventListener('touchstart', startTouch);
        el.removeEventListener('touchmove', onTouch);
        el.removeEventListener('touchend', endTouch);
      };
    },

    unmounted(el) {
      el._dragScrollCleanup && el._dragScrollCleanup();
    },
  }

  export default {
    install(app) {
      app.directive('click-outside', clickOutsideDirective);
      app.directive('click-exclude-id', clickExcludeIdDirective);
      app.directive('drag-scroll', dragScroll);
      // Add more directives here if needed
    }
  };
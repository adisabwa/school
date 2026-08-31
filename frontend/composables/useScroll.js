import { reactive, onMounted, onUnmounted } from 'vue';

export function useScroll() {
  const x = ref(0);
  const y = ref(0);

  const update = () => {
    x.value = window.scrollX;
    y.value = window.scrollY;
    // console.log('update;', x.value, y.value)
  };

  window.addEventListener('scroll', update, { passive: true });

  return { x, y };
}
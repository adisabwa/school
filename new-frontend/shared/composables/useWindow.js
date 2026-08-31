import { ref, onMounted, onUnmounted, readonly, computed } from 'vue';

export function useWindow() {
  const width = ref(window.innerWidth);
  const height = ref(window.innerHeight);

  const updateSize = () => {
    width.value = window.innerWidth;
    height.value = window.innerHeight;
  };

  onMounted(() => window.addEventListener('resize', updateSize));
  onUnmounted(() => window.removeEventListener('resize', updateSize));

  const windowSize = computed(() => ({
    width: width.value,
    height: height.value
  }));

  return {
    windowWidth: readonly(width),
    windowHeight: readonly(height),
    windowSize
  };
}
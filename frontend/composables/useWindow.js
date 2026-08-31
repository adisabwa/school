import { ref, computed } from 'vue';

// 1. Deklarasikan state di LUAR fungsi agar menjadi data tunggal (Global State)
const width = ref(window.innerWidth);
const height = ref(window.innerHeight);

const updateSize = () => {
  width.value = window.innerWidth;
  height.value = window.innerHeight;
  console.log('updateSize;', width.value, height.value)
};

// 2. Pasang listener langsung di level file agar aktif sejak aplikasi dimuat
window.addEventListener('resize', updateSize, { passive: true });

export function useWindow() {
  // Return readonly agar komponen tidak bisa mengubah ukuran window secara tidak sengaja
  return {
    windowWidth: computed(() => width.value),
    windowHeight: computed(() => height.value)
  };
}
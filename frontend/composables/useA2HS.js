// frontend/composables/useA2HS.js
import { ref, onMounted, onUnmounted } from 'vue';

export function useA2HS() {
  const deferredPrompt = ref(null);
  const canInstall = ref(false);

  const isStandalone = () =>
    window.matchMedia('(display-mode: standalone)').matches ||
    window.navigator.standalone === true;

  const handleBeforeInstallPrompt = (e) => {
    e.preventDefault();
    deferredPrompt.value = e;
    canInstall.value = true;
  };

  const promptInstall = async () => {
    if (!deferredPrompt.value) return;

    deferredPrompt.value.prompt();
    const choiceResult = await deferredPrompt.value.userChoice;
    deferredPrompt.value = null;
    canInstall.value = false;

    return choiceResult;
  };

  onMounted(() => {
    window.addEventListener('beforeinstallprompt', handleBeforeInstallPrompt);
  });

  onUnmounted(() => {
    window.removeEventListener('beforeinstallprompt', handleBeforeInstallPrompt);
  });

  return {
    canInstall,
    promptInstall,
    isStandalone
  };
}

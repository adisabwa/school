// src/composables/useStorage.js
import { ref, watch } from 'vue';

export const useStorage = () => {
  // --- COOKIE ---
  const setCookie = (name, value, days) => {
    let expires = "";
    if (days) {
      const date = new Date();
      date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
      expires = "; expires=" + date.toUTCString();
    }
    const val = JSON.stringify(value);
    document.cookie = name + "=" + (val || "") + expires + "; path=/";
  };

  const getCookie = (name) => {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) === ' ') c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) === 0) {
        let value = c.substring(nameEQ.length, c.length);
        return value !== '' ? JSON.parse(value) : null;
      }
    }
    return null;
  };

  const deleteCookie = (name) => {
    document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
  };

  // --- LOCAL STORAGE ---
  const getDataFromStorage = (index) => {
    let data = localStorage.getItem(index);
    return data ? JSON.parse(data) : '';
  };

  const saveToStorage = (index, value) => {
    let old_data = getDataFromStorage(index);
    if (!Array.isArray(old_data)) old_data = old_data ? [old_data] : [];
    
    // Gabungkan dan hilangkan duplikat
    const combined = [value, ...old_data];
    const uniqueData = [...new Set(combined)];
    
    localStorage.setItem(index, JSON.stringify(uniqueData));
  };

  const removeFromStorage = (index, value) => {
    let old_data = getDataFromStorage(index) ?? [];
    if (Array.isArray(old_data)) {
      let new_data = old_data.filter(d => d !== value);
      localStorage.setItem(index, JSON.stringify(new_data));
    }
  };

  const resetStorage = (index) => {
    localStorage.removeItem(index);
  };

  // Reactive version
  const useLocalStorage = (index, defaultValue) => {
    const storedValue = localStorage.getItem(index);
    const data = ref(storedValue ? JSON.parse(storedValue) : defaultValue);

    watch(data, (newVal) => {
      localStorage.setItem(index, JSON.stringify(newVal));
    }, { deep: true });

    return data;
  };

  return { 
    setCookie, getCookie, deleteCookie,
    saveToStorage, getDataFromStorage, removeFromStorage, resetStorage, 
    useLocalStorage 
  };
}
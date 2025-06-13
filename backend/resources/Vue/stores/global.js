// stores/globalStore.js
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useGlobalStore = defineStore('global', () => {
  // State
  const isSidebarVisible = ref(false);
  const isDarkMode = ref(false);

  // Actions
  function toggleSidebar() {
    isSidebarVisible.value = !isSidebarVisible.value;
  }

  function toggleDarkMode() {
    isDarkMode.value = !isDarkMode.value;
  }

  function setSidebarVisible(value) {
    isSidebarVisible.value = value;
  }

  function setDarkMode(value) {
    isDarkMode.value = value;
  }

  return {
    isSidebarVisible,
    isDarkMode,
    toggleSidebar,
    toggleDarkMode,
    setSidebarVisible,
    setDarkMode
  };
});

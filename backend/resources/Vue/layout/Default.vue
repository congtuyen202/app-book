<template>
  <div class="min-h-screen bg-slate-50 dark:bg-navy-900">
    <Header />
    <Sidebar />
    <main class="w-full h-auto px-4" :class="{'pl-[calc(var(--main-sidebar-width)_+_var(--sidebar-panel-width))]': globalStore.isSidebarVisible}">
      <slot></slot>
    </main>
  </div>
</template>
<script setup>
import Header from '../components/layouts/Header.vue';
import Sidebar from '../components/layouts/Sidebar.vue';
import { useGlobalStore } from '../stores/global';

const globalStore = useGlobalStore();
</script>
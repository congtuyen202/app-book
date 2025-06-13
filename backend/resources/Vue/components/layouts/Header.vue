<template>
  <nav class="flex w-full bg-white h-16 border-b border-slate-100">
      <div class="hidden md:block w-20 h-full"></div>
      <div class="w-full h-full px-4 md:px-16 flex items-center gap-5" 
      :class="{'pl-[calc(var(--main-sidebar-width)_+_var(--sidebar-panel-width))]': globalStore.isSidebarVisible}">
        <div class="size-11">
          <button @click="() => globalStore.setSidebarVisible(!globalStore.isSidebarVisible)" class="h-full w-full cursor-pointer flex flex-col items-left justify-center text-primary-600 transition-all" :class="{'gap-1': !globalStore.isSidebarVisible, 'gap-1.5': globalStore.isSidebarVisible}">
            <span class="transition-all duration-300 ease-in-out" :class="{'w-5 h-0.5 bg-primary-900': !globalStore.isSidebarVisible, 'w-3 h-0.5 bg-primary-900 -rotate-45': globalStore.isSidebarVisible}"></span>
            <span class="transition-all duration-300 ease-in-out" :class="{'w-3 h-0.5 bg-primary-900': !globalStore.isSidebarVisible, 'hidden': globalStore.isSidebarVisible}"></span>
            <span class="transition-all duration-300 ease-in-out" :class="{'w-5 h-0.5 bg-primary-900': !globalStore.isSidebarVisible, 'w-3 h-0.5 bg-primary-900 rotate-45': globalStore.isSidebarVisible}"></span>
          </button>
        </div>
      </div>
    </nav>
</template>
<script setup>
import { useGlobalStore } from '../../stores/global';

const globalStore = useGlobalStore();
</script>
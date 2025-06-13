<template>
  <div
      class="-translate-x-full w-auto flex-shrink-0 h-full left-0 fixed top-0 transition-transform duration-200 ease-in-out z-40 md:translate-x-0 md:z-50">
      <div class="fixed top-0 left-0 z-50 flex h-full w-(--main-sidebar-width) flex-col items-center border-r border-slate-100 bg-white dark:border-navy-700 dark:bg-navy-800">
        <div class="flex pt-4">
          <a href="#">
            <img class="size-11 transition-transform duration-500 ease-in-out hover:rotate-[360deg]"
              src="../../assets/images/app-logo.png" alt="logo">
          </a>
        </div>
        <div class="is-scrollbar-hidden flex grow flex-col space-y-4 overflow-y-auto pt-6">
          <a href=""
            class="flex size-11 items-center justify-center rounded-lg bg-primary-600/10 text-primary-600 outline-none transition-colors duration-200 group/icon-menu hover:bg-primary-600/20 focus:primary-600/20 active:primary-600/25 dark:bg-navy-600 dark:text-accent-light dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90">
            <svg class="size-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <path fill="currentColor" fill-opacity=".3"
                d="M5 14.059c0-1.01 0-1.514.222-1.945.221-.43.632-.724 1.453-1.31l4.163-2.974c.56-.4.842-.601 1.162-.601.32 0 .601.2 1.162.601l4.163 2.974c.821.586 1.232.88 1.453 1.31.222.43.222.935.222 1.945V19c0 .943 0 1.414-.293 1.707C18.414 21 17.943 21 17 21H7c-.943 0-1.414 0-1.707-.293C5 20.414 5 19.943 5 19v-4.94Z">
              </path>
              <path fill="currentColor"
                d="M3 12.387c0 .267 0 .4.084.441.084.041.19-.04.4-.204l7.288-5.669c.59-.459.885-.688 1.228-.688.343 0 .638.23 1.228.688l7.288 5.669c.21.163.316.245.4.204.084-.04.084-.174.084-.441v-.409c0-.48 0-.72-.102-.928-.101-.208-.291-.355-.67-.65l-7-5.445c-.59-.459-.885-.688-1.228-.688-.343 0-.638.23-1.228.688l-7 5.445c-.379.295-.569.442-.67.65-.102.208-.102.448-.102.928v.409Z">
              </path>
              <path fill="currentColor" d="M11.5 15.5h1A1.5 1.5 0 0 1 14 17v3.5h-4V17a1.5 1.5 0 0 1 1.5-1.5Z"></path>
              <path fill="currentColor"
                d="M17.5 5h-1a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5Z"></path>
            </svg>
            <div
              class="absolute -z-10 left-full opacity-0 group-hover/icon-menu:left-[calc(100%_-_20px)] group-hover/icon-menu:opacity-100 transition-all duration-200 group-hover/icon-menu:z-10 text-sm rounded-lg pl-5 pr-3 py-1 text-slate-800 ">
              <div class="flex items-center relative"> <span class="bg-gray-200 px-3 py-1 rounded">Dashboard</span>
                <span class="size-3 absolute bg-gray-200 rotate-45 -left-1 rounded"></span>
              </div>
            </div>
          </a>
        </div>
      </div>
      <div v-show="globalStore.isSidebarVisible" class="fixed top-0 left-0 z-30 w-[calc(var(--main-sidebar-width)_+_var(--sidebar-panel-width))] h-full pl-(--main-sidebar-width) shadow bg-white flex flex-col grow">
          <div class="w-full h-(--sidebar-panel-min-width) flex justify-between items-center text-xl pl-4 pr-1">
            <p>Dashboard</p>
            <button class="text-primary-900 md:hidden">
              <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
          </div>
      </div>
    </div>
</template>
<script setup>
import { useGlobalStore } from '../../stores/global';

const globalStore = useGlobalStore();
</script>
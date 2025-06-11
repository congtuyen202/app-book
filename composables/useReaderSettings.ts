import { useState } from '#app'; // Nuxt 3 auto-import
import { watch } from 'vue';

export interface ReaderSettings {
  fontFamily: 'Inter' | 'Merriweather' | 'Roboto Mono';
  fontSize: number; // in px
  lineHeight: number; // as a multiplier, e.g., 1.5
  theme: 'light' | 'dark' | 'system';
}

const defaultSettings: ReaderSettings = {
  fontFamily: 'Inter',
  fontSize: 18,
  lineHeight: 1.6,
  theme: 'system',
};

const settingsKey = 'readerAppSettings';

export const useReaderSettings = () => {
  // Initialize with defaults or from localStorage
  const getInitialSettings = (): ReaderSettings => {
    if (typeof window !== 'undefined') {
      const stored = localStorage.getItem(settingsKey);
      if (stored) {
        try {
          // Ensure all keys from defaultSettings are present and have correct types
          const parsed = JSON.parse(stored);
          const validatedSettings = { ...defaultSettings };
          if (parsed.fontFamily && ['Inter', 'Merriweather', 'Roboto Mono'].includes(parsed.fontFamily)) {
            validatedSettings.fontFamily = parsed.fontFamily;
          }
          if (typeof parsed.fontSize === 'number' && parsed.fontSize >= 10 && parsed.fontSize <= 40) {
            validatedSettings.fontSize = parsed.fontSize;
          }
          if (typeof parsed.lineHeight === 'number' && parsed.lineHeight >= 1.0 && parsed.lineHeight <= 3.0) {
            validatedSettings.lineHeight = parsed.lineHeight;
          }
          if (parsed.theme && ['light', 'dark', 'system'].includes(parsed.theme)) {
            validatedSettings.theme = parsed.theme;
          }
          return validatedSettings;
        } catch (e) {
          console.error("Failed to parse settings from localStorage or invalid data", e);
        }
      }
    }
    return { ...defaultSettings }; // Return a copy
  };

  const settings = useState<ReaderSettings>(settingsKey, getInitialSettings);

  const applyTheme = (themeValue: ReaderSettings['theme']) => {
    if (themeValue === 'dark' || (themeValue === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  };

  // Persist to localStorage whenever settings change
  if (typeof window !== 'undefined') {
    watch(settings, (newSettings) => {
      localStorage.setItem(settingsKey, JSON.stringify(newSettings));
      applyTheme(newSettings.theme);
    }, { deep: true });

    // Initial theme application
    applyTheme(settings.value.theme);

    // Listen for system theme changes
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    const systemThemeListener = (e: MediaQueryListEvent) => {
        if (settings.value.theme === 'system') {
             applyTheme('system'); // Re-apply system to react to change
        }
    };
    mediaQuery.addEventListener('change', systemThemeListener);

    // Nuxt specific: clean up event listener when app is unmounted (though composable might outlive component)
    // For a true SPA lifecycle, this might need to be in a plugin or on a root component.
    // However, useState and watch are managed by Nuxt/Vue lifecycle.
  }

  const resetSettings = () => {
    settings.value = { ...defaultSettings }; // Assign a copy to trigger reactivity
  };

  return { settings, resetSettings };
};

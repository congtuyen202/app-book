import { useState } from '#app'; // Nuxt 3 auto-import
import { watch } from 'vue';

export interface ReaderSettings {
  fontFamily: 'Inter' | 'Merriweather' | 'Roboto Mono';
  fontSize: number; // in px
  lineHeight: number; // as a multiplier, e.g., 1.5
  theme: 'light' | 'dark';
}

const defaultSettings: ReaderSettings = {
  fontFamily: 'Inter',
  fontSize: 18,
  lineHeight: 1.6,
  theme: 'light',
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
          if (parsed.theme && ['light', 'dark'].includes(parsed.theme)) { // Only light/dark
            validatedSettings.theme = parsed.theme;
          }
          // If stored theme was 'system', it will default to 'light' from defaultSettings spread
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
    if (themeValue === 'dark') {
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
    // System theme media query listener is removed
  }

  const resetSettings = () => {
    settings.value = { ...defaultSettings }; // Assign a copy to trigger reactivity
  };

  return { settings, resetSettings };
};

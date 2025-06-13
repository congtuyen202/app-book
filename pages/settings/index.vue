<template>
  <div class="container mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold mb-8">Application Settings</h1>

    <Card class="max-w-2xl mx-auto">
      <CardHeader>
        <CardTitle>Reading Experience</CardTitle>
        <CardDescription>Customize how your books are displayed.</CardDescription>
      </CardHeader>
      <CardContent class="space-y-6 pt-6">
        <!-- Font Family -->
        <div class="space-y-2">
          <Label for="fontFamily">Font Family</Label>
          <Select v-model="currentSettings.fontFamily">
            <SelectTrigger id="fontFamily">
              <SelectValue placeholder="Select font family" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="Inter">Inter (Sans-Serif)</SelectItem>
              <SelectItem value="Merriweather">Merriweather (Serif)</SelectItem>
              <SelectItem value="Roboto Mono">Roboto Mono (Monospace)</SelectItem>
            </SelectContent>
          </Select>
        </div>

        <!-- Font Size -->
        <div class="space-y-2">
          <Label :for="fontSizeId" class="block mb-1">Font Size: {{ currentSettings.fontSize }}px</Label>
          <Slider
            :id="fontSizeId"
            v-model="fontSizeArray"
            :min="12"
            :max="32"
            :step="1"
            class="my-2"
          />
        </div>

        <!-- Line Height -->
        <div class="space-y-2">
          <Label :for="lineHeightId" class="block mb-1">Line Height: {{ currentSettings.lineHeight.toFixed(1) }}</Label>
           <Slider
            :id="lineHeightId"
            v-model="lineHeightArray"
            :min="1.2"
            :max="2.2"
            :step="0.1"
            class="my-2"
          />
        </div>

        <!-- Theme Selection -->
        <div class="space-y-2">
          <Label class="block mb-2">Theme</Label>
          <RadioGroup v-model="currentSettings.theme" class="flex space-x-2 sm:space-x-4">
            <div class="flex items-center space-x-2">
              <RadioGroupItem :id="themeLightId" value="light" />
              <Label :for="themeLightId" class="font-normal">Light</Label>
            </div>
            <div class="flex items-center space-x-2">
              <RadioGroupItem :id="themeDarkId" value="dark" />
              <Label :for="themeDarkId" class="font-normal">Dark</Label>
            </div>
            <!-- System theme option removed -->
          </RadioGroup>
        </div>
         <Button variant="outline" @click="handleResetSettings" class="mt-4">Reset to Defaults</Button>
      </CardContent>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted } from 'vue'; // watch not needed here directly
import { useReaderSettings } from '~/composables/useReaderSettings';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Slider } from '@/components/ui/slider';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';

const { settings, resetSettings } = useReaderSettings();

// Use the settings ref directly for v-model bindings.
// Nuxt's useState and Vue's ref system handle reactivity appropriately.
const currentSettings = settings;

// Slider component expects an array for v-model
const fontSizeArray = computed({
  get: () => [currentSettings.value.fontSize],
  set: (val: number[]) => { if (val.length > 0) currentSettings.value.fontSize = val[0]; }
});
const lineHeightArray = computed({
  get: () => [currentSettings.value.lineHeight],
  set: (val: number[]) => { if (val.length > 0) currentSettings.value.lineHeight = val[0]; }
});

// Unique IDs for accessibility
const fontSizeId = ref('font-size-slider-' + Math.random().toString(36).substring(2,9));
const lineHeightId = ref('line-height-slider-' + Math.random().toString(36).substring(2,9));
const themeLightId = ref('theme-light-' + Math.random().toString(36).substring(2,9));
const themeDarkId = ref('theme-dark-' + Math.random().toString(36).substring(2,9));
const themeSystemId = ref('theme-system-' + Math.random().toString(36).substring(2,9));

const handleResetSettings = () => {
  resetSettings();
};

// The theme application logic is primarily handled within the composable.
// An onMounted hook here to ensure initial theme on page load might be redundant
// if the composable handles it effectively. The composable should handle initial load.
onMounted(() => {
  // if (typeof window !== 'undefined') {
  //   const applyCurrentTheme = () => {
  //     if (currentSettings.value.theme === 'dark') { // Simplified: no 'system'
  //       document.documentElement.classList.add('dark');
  //     } else {
  //       document.documentElement.classList.remove('dark');
  //     }
  //   };
  //   applyCurrentTheme();
  // }
  // The watcher in the composable will handle changes.
  // The initial application is also in the composable.
});
</script>

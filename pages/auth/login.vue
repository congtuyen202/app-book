<template>
  <div class="flex items-center justify-center min-h-screen bg-background px-4">
    <Card class="w-full max-w-md">
      <CardHeader>
        <CardTitle class="text-2xl font-bold text-center">Login</CardTitle>
        <CardDescription class="text-center">
          Enter your credentials to access your account.
        </CardDescription>
      </CardHeader>
      <CardContent class="space-y-4">
        <div class="space-y-2">
          <Label for="email">Email</Label>
          <Input id="email" v-model="email" type="email" placeholder="m@example.com" required />
          <p v-if="emailError" class="text-destructive text-sm">{{ emailError }}</p>
        </div>
        <div class="space-y-2">
          <Label for="password">Password</Label>
          <Input id="password" v-model="password" type="password" required />
          <p v-if="passwordError" class="text-destructive text-sm">{{ passwordError }}</p>
        </div>
      </CardContent>
      <CardFooter class="flex flex-col gap-4">
        <Button class="w-full" @click="handleLogin">Login</Button>
        <div class="text-center text-sm">
          Don't have an account?
          <NuxtLink to="/auth/register" class="underline hover:text-primary">
            Register
          </NuxtLink>
        </div>
      </CardFooter>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
// useRouter from 'vue-router' is for Vue Router in general, Nuxt 3 uses navigateTo or useNuxtApp().$router
// For this mock, direct navigation or alert is fine. In a real app, use navigateTo.

definePageMeta({
  layout: 'blank', // Assuming you might want a blank layout for auth pages
});

const email = ref('');
const password = ref('');
const emailError = ref('');
const passwordError = ref('');

// const router = useRouter(); // Not directly used for navigation in this mock example.
// For Nuxt 3, prefer navigateTo() for page navigation.

const validateEmail = (): boolean => {
  if (!email.value) {
    emailError.value = 'Email is required.';
    return false;
  }
  if (!/\S+@\S+\.\S+/.test(email.value)) {
    emailError.value = 'Please enter a valid email address.';
    return false;
  }
  emailError.value = '';
  return true;
};

const validatePassword = (): boolean => {
  if (!password.value) {
    passwordError.value = 'Password is required.';
    return false;
  }
  // Add more password rules if needed (e.g., length)
  passwordError.value = '';
  return true;
};

const handleLogin = async () => {
  const isEmailValid = validateEmail();
  const isPasswordValid = validatePassword();

  if (isEmailValid && isPasswordValid) {
    console.log('Logging in with:', email.value, password.value);
    // Mock login logic:
    // In a real app, call your auth API here.
    // For mock, just navigate to homepage or dashboard.
    // Example: await navigateTo('/');
    alert('Mock login successful! Navigation would occur here using navigateTo("/").');
  }
};
</script>

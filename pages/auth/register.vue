<template>
  <div class="flex items-center justify-center min-h-screen bg-background px-4">
    <Card class="w-full max-w-md">
      <CardHeader>
        <CardTitle class="text-2xl font-bold text-center">Create Account</CardTitle>
        <CardDescription class="text-center">
          Fill in the details below to create your new account.
        </CardDescription>
      </CardHeader>
      <CardContent class="space-y-4">
        <div class="space-y-2">
          <Label for="name">Name</Label>
          <Input id="name" v-model="name" type="text" placeholder="Your Name" required />
          <p v-if="nameError" class="text-destructive text-sm">{{ nameError }}</p>
        </div>
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
        <div class="space-y-2">
          <Label for="confirmPassword">Confirm Password</Label>
          <Input id="confirmPassword" v-model="confirmPassword" type="password" required />
          <p v-if="confirmPasswordError" class="text-destructive text-sm">{{ confirmPasswordError }}</p>
        </div>
      </CardContent>
      <CardFooter class="flex flex-col gap-4">
        <Button class="w-full" @click="handleRegister">Register</Button>
        <div class="text-center text-sm">
          Already have an account?
          <NuxtLink to="/auth/login" class="underline hover:text-primary">
            Login
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
// NuxtLink is auto-imported by Nuxt
// For Nuxt 3, navigateTo() is globally available for page navigation.

definePageMeta({
  layout: 'blank', // Use the same blank layout
});

const name = ref('');
const email = ref('');
const password = ref('');
const confirmPassword = ref('');

const nameError = ref('');
const emailError = ref('');
const passwordError = ref('');
const confirmPasswordError = ref('');

// const router = useRouter(); // Not needed, use navigateTo for Nuxt 3

const validateName = (): boolean => {
  if (!name.value.trim()) {
    nameError.value = 'Name is required.';
    return false;
  }
  nameError.value = '';
  return true;
};

const validateEmail = (): boolean => {
  if (!email.value.trim()) {
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
  if (password.value.length < 6) {
    passwordError.value = 'Password must be at least 6 characters long.';
    return false;
  }
  passwordError.value = '';
  return true;
};

const validateConfirmPassword = (): boolean => {
  if (!confirmPassword.value) {
    confirmPasswordError.value = 'Please confirm your password.';
    return false;
  }
  if (confirmPassword.value !== password.value) {
    confirmPasswordError.value = 'Passwords do not match.';
    return false;
  }
  confirmPasswordError.value = '';
  return true;
};

const handleRegister = async () => {
  const isNameValid = validateName();
  const isEmailValid = validateEmail();
  const isPasswordValid = validatePassword();
  const isConfirmPasswordValid = validateConfirmPassword();

  if (isNameValid && isEmailValid && isPasswordValid && isConfirmPasswordValid) {
    console.log('Registering with:', name.value, email.value, password.value);
    // Mock registration logic:
    // In a real app, call your registration API here.
    // For mock, navigate to login page or homepage.
    // Example: await navigateTo('/auth/login?registered=true');
    alert('Mock registration successful! Navigation would occur here using navigateTo("/auth/login").');
  }
};
</script>

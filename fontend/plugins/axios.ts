import axios, { type AxiosInstance } from 'axios';

export default defineNuxtPlugin((nuxtApp) => {
  const axiosInstance: AxiosInstance = axios.create({
    // You can set a base URL if all your mock APIs are under a specific path
    // For example, if your mock APIs are under /api (which $fetch already handles well)
    // you might not need a baseURL here, or set it to '/' if you want to construct
    // full paths like '/api/users' when calling axiosInstance.get('/api/users')
    // baseURL: '/api/', // Example, adjust if needed or omit
    headers: {
      'Content-Type': 'application/json',
    },
  });

  // Optional: Add request interceptor for logging or adding tokens
  axiosInstance.interceptors.request.use(config => {
    // console.log('Requesting URL:', config.url);
    return config;
  });

  // Optional: Add response interceptor for error handling
  axiosInstance.interceptors.response.use(
    response => response,
    error => {
      // console.error('Axios error:', error.response?.data || error.message);
      // Handle errors globally if needed
      return Promise.reject(error);
    }
  );

  // Provide the instance to the Nuxt app.
  // You can use it via nuxtApp.$axios or directly with useNuxtApp().$axios
  // Or, if you want it available as $axios directly in components:
  return {
    provide: {
      axios: axiosInstance,
    },
  };
});

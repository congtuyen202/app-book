import tailwindcss from "@tailwindcss/vite";

// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: "2024-11-01",
  devtools: { enabled: true },
  css: ["~/assets/css/main.css"],

  vite: {
    plugins: [tailwindcss()],
  },

  modules: ["nuxt-lucide-icons"],
  lucide: {
    namePrefix: "Icon",
  },
  runtimeConfig: {
    // Keys khai báo ở đây chỉ có ở server-side
    googleAiApiKey: '', // Sẽ được ghi đè bởi biến môi trường NUXT_GOOGLE_AI_API_KEY
    // public: {
    //   // Keys khai báo ở đây sẽ có cả ở client-side (không nên để API key ở đây)
    // }
  },
});

import tailwindcss from "@tailwindcss/vite";

// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: "2024-11-01",
  devtools: { enabled: true },
  css: ["~/assets/css/main.css"],

  vite: {
    plugins: [tailwindcss()],
  },

  modules: ["@nuxtjs/apollo", "nuxt-lucide-icons"],
  lucide: {
    namePrefix: "Icon",
  },
  apollo: {
    clients: {
      default: {
        httpEndpoint: process.env.APOLLO_CLIENT_API_URL || "fallback_url_if_not_set"
      }
    },
  },
  runtimeConfig: {
    // Keys khai báo ở đây chỉ có ở server-side
    googleAiApiKey: "", // Sẽ được ghi đè bởi biến môi trường NUXT_GOOGLE_AI_API_KEY
    // public: {
    //   // Keys khai báo ở đây sẽ có cả ở client-side (không nên để API key ở đây)
    // }
  },
});

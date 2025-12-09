// frontend/nuxt.config.ts

export default defineNuxtConfig({
  srcDir: 'app/',

  devtools: { enabled: true },

  runtimeConfig: {
    public: {
      apiBase: 'http://localhost:8000/api', // şimdilik sabit
    },
  },

  css: ['~/assets/css/tailwind.css'],
});


/*
export default defineNuxtConfig({
  devtools: { enabled: true },

  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000/api',
    },
  },

  css: [
    '@/assets/css/tailwind.css',
  ],
});
*/




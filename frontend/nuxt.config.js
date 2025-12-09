// frontend/nuxt.config.js

export default defineNuxtConfig({
  srcDir: 'app/',

  devtools: { enabled: true },

  modules: ['@nuxtjs/tailwindcss'],

  runtimeConfig: {
    public: {
      //sade birakiyorum .env ile ugrasmamak icin 
      apiBase: 'http://localhost:8000/api',
    },
  },

  css: ['~/assets/css/tailwind.css'],
});

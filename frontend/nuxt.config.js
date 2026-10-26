export default defineNuxtConfig({
  srcDir: 'app/',

  devtools: { enabled: true },

  modules: ['@nuxtjs/tailwindcss'],

  runtimeConfig: {
    public: {
      apiBase: 'http://127.0.0.1:8000/api',
    },
  },

  css: ['~/assets/css/tailwind.css'],
});
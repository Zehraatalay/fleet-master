// frontend/nuxt.config.js

export default defineNuxtConfig({ // ana config fonk buraya verilen object tum frontend projesinin davranisini belirler 
  srcDir: 'app/', // source code burada 

  devtools: { enabled: true },

  modules: ['@nuxtjs/tailwindcss'], //tailwind Nuxt'e entegre eden resmi module 

  runtimeConfig: {
    public: {
      //sade birakiyorum .env ile ugrasmamak icin 
      apiBase: 'http://localhost:8000/api', // frontend backend ile konusurken bu base URL'yi kullanir 
    },
  },

  css: ['~/assets/css/tailwind.css'], //tailwind giris noktasi 
});

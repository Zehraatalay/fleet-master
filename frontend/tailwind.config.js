/** @type {import('tailwindcss').Config} */
export default {
  content: [ //hangi dosyalar taraniyor/kullaniliyor 
    './app/components/**/*.{vue,js}',
    './app/layouts/**/*.{vue,js}',
    './app/pages/**/*.{vue,js}',
    './app/plugins/**/*.{js,ts}',
    './app/App.{js,vue}',
    './app/error.{js,vue}',
  ],
  theme: { //marka renkleri, font vs eklenebilir 
    extend: {},
  },
  plugins: [], //tailwind plugin'i eklemek icin 
};

import tailwindcss from '@tailwindcss/vite';

export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },

  css: ['./app/assets/css/app.css'],

  vite: {
    plugins: [tailwindcss()],
  },

  runtimeConfig: {
    // サーバーサイド用 (Docker内部通信)
    apiBase: process.env.NUXT_API_BASE || 'http://nginx/api',
    public: {
      // クライアントサイド用 (ブラウザからのアクセス)
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8080/api',
    },
  },
});

import tailwindcss from '@tailwindcss/vite';

export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },

  css: ['./app/assets/css/app.css'],

  vite: {
    plugins: [tailwindcss()],
  },

  runtimeConfig: {
    apiBase:
      process.env.NUXT_API_BASE || 'https://jisui-backend.onrender.com/api',
    public: {
      apiBase:
        process.env.NUXT_PUBLIC_API_BASE ||
        'https://jisui-backend.onrender.com/api',
    },
  },
});

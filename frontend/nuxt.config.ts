// https://nuxt.com/docs/api/configuration/nuxt-config
import tailwindcss from "@tailwindcss/vite";

export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },
  css: ['~/assets/css/main.css'],
  vite: {
    plugins: [
      tailwindcss(),
    ],
  },

  modules: [
    '@nuxt/icon',
    '@pinia/nuxt',
    'nuxt-lodash',
    'pinia-plugin-persistedstate/nuxt',
    '@primevue/nuxt-module',
    '@nuxtjs/color-mode',
    '@vee-validate/nuxt',
  ],
  runtimeConfig: {
    public: {
      backendBase: 'http://localhost:8000',
      backendApi: 'http://localhost:8000/api/v1',
    }
  },
  app: {
    head: {
      htmlAttrs: {
        lang: 'en',
      },
      link: [
        { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
      ],
      charset: 'utf-8',
      viewport: 'width=device-width, initial-scale=1, maximum-scale=1',
    }
  },
  colorMode: {
    preference: 'light',
    classSuffix: '',
  },
  icon: {
    size: '1.5em',
  },
  primevue: {
    autoImport: true,
    importTheme: { from: './assets/theme/theme.ts' },
  },
})
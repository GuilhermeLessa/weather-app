import { fileURLToPath, URL } from 'node:url';

import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    vue()
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  envDir: './env',
  server: {
    host: true,
    port: 5173
  },
  build: {
    rollupOptions: {
      output: {
        globals: {
          jquery: 'window.$'
        }
      }
    }
  }
});
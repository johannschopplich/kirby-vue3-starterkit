import 'dotenv/config'
import path from 'path'
import Vue from '@vitejs/plugin-vue'

process.env.VITE_BACKEND_URL = `http://${process.env.KIRBY_DEV_HOSTNAME}:${process.env.KIRBY_DEV_PORT}`
process.env.VITE_BACKEND_API_SLUG = process.env.CONTENT_API_SLUG
process.env.VITE_MULTILANG = process.env.KIRBY_MULTILANG

export default ({ command, mode }) => ({
  root: 'src',
  base: mode === 'development' ? '/' : '/dist/',

  build: {
    outDir: path.resolve(process.cwd(), 'public/dist'),
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: '/index.js'
    }
  },

  resolve: {
    alias: {
      '~/': `${path.resolve(process.cwd(), 'src')}/`
    }
  },

  plugins: [
    Vue()
  ],

  server: {
    cors: true,
    port: 3000,
    strictPort: true
  },

  optimizeDeps: {
    include: [
      'vue',
      'vue-router'
    ]
  }
})

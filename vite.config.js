import 'dotenv/config.js'
import { resolve } from 'path'
import vue from '@vitejs/plugin-vue'

process.env.VITE_BACKEND_API_SLUG = process.env.CONTENT_API_SLUG
process.env.VITE_MULTILANG = process.env.KIRBY_MULTILANG

export default ({ command, mode }) => ({
  root: 'src',
  base: mode === 'development' ? '/' : '/dist/',

  build: {
    outDir: resolve(process.cwd(), 'public/dist'),
    emptyOutDir: true,
    manifest: true,
    target: 'es2018',
    rollupOptions: {
      input: '/index.js'
    }
  },

  plugins: [
    vue()
  ],

  server: {
    cors: true,
    port: 3000,
    strictPort: true
  }
})

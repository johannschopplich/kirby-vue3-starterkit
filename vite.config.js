import 'dotenv/config.js'
import vue from '@vitejs/plugin-vue'

const kirbyUrl = `http://${process.env.KIRBY_SERVER_HOSTNAME}:${process.env.KIRBY_SERVER_PORT}`
const apiLocation = `/${process.env.CONTENT_API_SLUG}`

/**
 * type {import('vite').UserConfig}
 */
export default {
  root: 'frontend',
  build: {
    assetsDir: process.env.VITE_ASSETS_DIR,
    manifest: true,
    target: 'es2018'
  },
  plugins: [
    vue()
  ],

  server: {
    proxy: {
      [`*${apiLocation}/*.json`]: {
        target: kirbyUrl,
        changeOrigin: true
      }
    }
  }
}

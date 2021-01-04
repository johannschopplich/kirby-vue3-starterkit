import 'dotenv/config.js'
import vue from '@vitejs/plugin-vue'

const kirbyUrl = `http://${process.env.KIRBY_SERVER_HOSTNAME}:${process.env.KIRBY_SERVER_PORT}`
const apiLocation = `/${process.env.CONTENT_API_SLUG}`

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

  env: {
    VITE_BACKEND_URL: kirbyUrl,
    VITE_BACKEND_API_LOCATION: apiLocation,
    VITE_MULTILANG: process.env.KIRBY_MULTILANG,
    VITE_MULTILANG_DETECT: process.env.KIRBY_MULTILANG_DETECT
  },

  proxy: {
    [`*${apiLocation}/*.json`]: {
      target: kirbyUrl,
      changeOrigin: true
    }
  }
}

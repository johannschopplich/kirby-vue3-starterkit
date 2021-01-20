import 'dotenv/config.js'
import vue from '@vitejs/plugin-vue'

const kirbyUrl = `http://${process.env.KIRBY_SERVER_HOSTNAME}:${process.env.KIRBY_SERVER_PORT}`
const proxyPath = '/viteproxy'

process.env.VITE_BACKEND_URL = kirbyUrl
process.env.VITE_BACKEND_API_SLUG = process.env.CONTENT_API_SLUG
process.env.VITE_PROXY_PATH = proxyPath
process.env.VITE_MULTILANG = process.env.KIRBY_MULTILANG

export default {
  root: 'frontend',
  build: {
    assetsDir: 'assets',
    manifest: true
  },
  plugins: [
    vue()
  ],

  server: {
    proxy: {
      [`^${proxyPath}/.*`]: {
        target: kirbyUrl,
        changeOrigin: true,
        rewrite: path => path.replace(new RegExp(`^${proxyPath}`), '')
      }
    }
  }
}

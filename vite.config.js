import 'dotenv/config.js'
import vue from '@vitejs/plugin-vue'

const kirbyUrl = `http://${process.env.KIRBY_SERVER_HOSTNAME}:${process.env.KIRBY_SERVER_PORT}`
const proxyPath = '/viteproxy'

export default ({ command, mode }) => ({
  root: 'frontend',
  build: {
    assetsDir: process.env.ASSETS_DIR,
    manifest: true,
    target: 'es2018'
  },
  define: {
    'window.vite.backendUrl': kirbyUrl,
    'window.vite.backendApiPath': `${command === 'serve' ? proxyPath : ''}/${process.env.CONTENT_API_SLUG}`,
    'window.vite.multilang': process.env.KIRBY_MULTILANG === 'true'
  },
  plugins: [
    vue()
  ],

  server: {
    proxy: {
      [proxyPath]: {
        target: kirbyUrl,
        changeOrigin: true,
        rewrite: path => path.replace(new RegExp(`^${proxyPath}`), '')
      }
    }
  }
})

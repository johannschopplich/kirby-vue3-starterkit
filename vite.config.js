require('dotenv').config()

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
    VITE_BACKEND_URL: kirbyUrl,
    VITE_BACKEND_API_PATH: `${command === 'serve' ? proxyPath : ''}/${process.env.CONTENT_API_SLUG}`,
    VITE_MULTILANG: process.env.KIRBY_MULTILANG
  },
  plugins: [
    vue()
  ],

  server: {
    proxy: {
      [proxyPath]: {
        target: kirbyUrl,
        changeOrigin: true,
        rewrite: path => path.replace(proxyPath, '')
      }
    }
  }
})

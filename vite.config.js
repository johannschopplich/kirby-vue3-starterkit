require('dotenv').config()

const kirbyUrl = `http://${process.env.KIRBY_SERVER_HOSTNAME}:${process.env.KIRBY_SERVER_PORT}`
const apiLocation = `/${process.env.CONTENT_API_SLUG}`

module.exports = {
  root: 'frontend',
  assetsDir: process.env.VITE_ASSETS_DIR,
  emitIndex: false,
  emitManifest: true,
  esbuildTarget: 'es2018',

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

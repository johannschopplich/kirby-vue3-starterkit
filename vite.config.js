const { resolve } = require('path')
const { useApiLocation } = require('./scripts/useApiLocation')
require('dotenv').config()

const kirbyUrl = `http://${process.env.KIRBY_SERVER_HOSTNAME}:${process.env.KIRBY_SERVER_PORT}`
const apiLocation = useApiLocation(process.env.CONTENT_API_SLUG)

module.exports = {
  root: 'frontend',
  alias: {
    '/~/': resolve(__dirname, 'src')
  },
  assetsDir: process.env.VITE_ASSETS_DIR,
  emitIndex: false,
  esbuildTarget: 'es2017',

  env: {
    VITE_BACKEND_URL: kirbyUrl,
    VITE_BACKEND_API_LOCATION: apiLocation
  },

  proxy: {
    [`${apiLocation}/*.json`]: {
      target: kirbyUrl,
      changeOrigin: true
    }
  }
}

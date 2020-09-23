const { resolve } = require('path')
const { useApiLocation } = require('./scripts/useApiLocation')
require('dotenv').config()

const kirbyUrl = `http://${process.env.KIRBY_SERVER_HOSTNAME}:${process.env.KIRBY_SERVER_PORT}`
const kirbyApiLocation = useApiLocation(process.env.KIRBY_API_LOCATION)

module.exports = {
  root: 'frontend',
  alias: {
    '/~/': resolve(__dirname, 'src')
  },
  assetsDir: process.env.VITE_ASSETS_DIR,
  emitIndex: false,
  esbuildTarget: 'es2017',

  env: {
    VITE_KIRBY_URL: kirbyUrl,
    VITE_KIRBY_API_LOCATION: kirbyApiLocation
  },

  proxy: {
    [`${kirbyApiLocation}/*.json`]: {
      target: kirbyUrl,
      changeOrigin: true
    }
  }
}

require('dotenv').config()

const { useApiLocation } = require('./scripts/useApiLocation')
const kirbyUrl = `http://${process.env.KIRBY_SERVER_HOSTNAME}:${process.env.KIRBY_SERVER_PORT}`

module.exports = {
  root: 'frontend',
  assetsDir: 'assets',
  emitIndex: false,

  env: {
    VITE_KIRBY_API_LOCATION: useApiLocation()
  },

  proxy: {
    '/img/': {
      target: kirbyUrl
    },
    '/*.json': {
      target: kirbyUrl,
      changeOrigin: true
    }
  }
}

require('dotenv').config()
const { useApiLocation } = require('./scripts/useApiLocation')

module.exports = {
  root: 'frontend',
  assetsDir: 'assets',
  emitIndex: false,

  env: {
    VITE_KIRBY_API_LOCATION: useApiLocation()
  },

  proxy: {
    '/*.json': {
      target: `http://${process.env.KIRBY_SERVER_HOSTNAME}:${process.env.KIRBY_SERVER_PORT}`,
      changeOrigin: true
    }
  }
}

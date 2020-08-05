require('dotenv').config()

const { useLocation } = require('./scripts/useLocation')
const kirbyUrl = `http://${process.env.KIRBY_SERVER_HOSTNAME}:${process.env.KIRBY_SERVER_PORT}`

module.exports = {
  root: 'frontend',
  assetsDir: 'assets',
  emitIndex: false,
  esbuildTarget: 'es2017',

  env: {
    VITE_KIRBY_API_LOCATION: useLocation(process.env.KIRBY_API_LOCATION)
  },

  proxy: {
    '/*.json': {
      target: kirbyUrl,
      changeOrigin: true
    }
  }
}

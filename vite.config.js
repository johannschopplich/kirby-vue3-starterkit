require('dotenv').config()

module.exports = {
  root: 'frontend',
  assetsDir: 'assets',
  emitIndex: false,

  env: {
    VITE_KIRBY_API_LOCATION: process.env.KIRBY_API_LOCATION
  },

  proxy: {
    '/*.json': {
      target: `http://${process.env.KIRBY_SERVER_HOSTNAME}:${process.env.KIRBY_SERVER_PORT}`,
      changeOrigin: true
    }
  }
}

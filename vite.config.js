require('dotenv').config()

module.exports = {
  root: 'frontend',
  assetsDir: 'assets',
  emitIndex: false,

  proxy: {
    '*.json': {
      target: `http://${process.env.KIRBY_SERVER_HOSTNAME}:${process.env.KIRBY_SERVER_PORT}`,
      changeOrigin: true
    }
  }
}

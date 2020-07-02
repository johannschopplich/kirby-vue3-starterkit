require('dotenv').config()

module.exports = {
  root: 'frontend',
  env: {
    KIRBY_URL: `http://${process.env.KIRBY_SERVER_HOSTNAME}:${process.env.KIRBY_SERVER_PORT}`
  },
  assetsDir: 'assets',
  emitIndex: false
}

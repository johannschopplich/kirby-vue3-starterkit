require('dotenv').config()
const phpServer = require('php-server')

;(async () => {
  const server = await phpServer({
    binary: 'php',
    hostname: process.env.KIRBY_SERVER_HOSTNAME,
    port: process.env.KIRBY_SERVER_PORT,
    base: 'public',
    router: 'server.php'
  })

  console.log('\x1b[32m%s\x1b[0m', `Kirby backend running at ${server.url}`)
})()

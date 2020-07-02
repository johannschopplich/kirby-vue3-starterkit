const phpServer = require('php-server')
require('dotenv').config()

const baseDir = 'public'
const hostname = process.env.KIRBY_SERVER_HOSTNAME
const port = process.env.KIRBY_SERVER_PORT

;(async () => {
  const server = await phpServer({
    binary: 'php',
    hostname: hostname,
    port: port,
    base: baseDir,
    router: 'server.php'
  })

  console.log('\x1b[32m%s\x1b[0m', `Kirby backend running at ${server.url}\n`)
})()

require('dotenv').config()
const { resolve } = require('path')
const { readdir, readFile, writeFile } = require('fs/promises')
const { useLocation } = require('./useLocation')
const { minify: _minify } = require('terser')

const assetsDir = 'public/assets'
const assetFiles = []
const swSrcPath = 'frontend/src/serviceWorker.js'
const swDistPath = 'public/service-worker.js'
const apiLocation = useLocation(process.env.KIRBY_API_LOCATION)

/**
 * Extract basename from path and add to asset files array
 *
 * @param {object} absolutePath Path to asset file
 */
function addAsset (absolutePath) {
  // Extract path relative to installable service worker
  const path = absolutePath.split('/public')[1]

  // Add asset to files to precache array
  assetFiles.push(path)
}

/**
 * Get list of pathnames in a specific directory
 *
 * @param {string} dir Directory to list
 */
async function * getFiles (dir) {
  const dirents = await readdir(dir, { withFileTypes: true })
  for (const dirent of dirents) {
    const res = resolve(dir, dirent.name)
    if (dirent.isDirectory()) {
      yield * getFiles(res)
    } else {
      yield res
    }
  }
}

/**
 * Generates a random string like `af51-7184-69cd`
 *
 * @returns {string} Random string
 */
function random () {
  const segment = () => (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1)
  return `${segment()}-${segment()}-${segment()}`
}

/**
 * Minifies ES6+ code with Terser
 *
 * @param {string} input Script to minify
 * @returns {string} Minified code
 */
async function minify (input) {
  try {
    const { code } = await _minify(input)
    return code
  } catch (error) {
    throw new Error(error)
  }
}

;(async () => {
  for await (const file of getFiles(assetsDir)) {
    addAsset(file)
  }

  const bundle = `
    self.__PRECACHE_ASSET_URLS = [${assetFiles.map(i => `'${i}'`).join(',')}]
    const VERSION = '${random()}'
    const API_LOCATION = '${apiLocation}'
    ${await readFile(swSrcPath)}
  `

  const minified = await minify(bundle)
  await writeFile(swDistPath, minified)

  console.log('\x1b[32m%s\x1b[0m', `Generated service worker to ${swDistPath} with ${assetFiles.length} additional assets to precache`)
})()

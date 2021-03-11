/* eslint-env serviceworker */
/* global VERSION, KIRBY_API_SLUG, CONTENT_API_SLUG */

const MAX_CACHED_PAGES = false
const MAX_CACHED_IMAGES = 50
const FETCH_TIMEOUT = 5000

const CACHE_KEYS = {
  STATIC: `static-${VERSION}`,
  PAGES: `pages-${VERSION}`,
  IMAGES: 'images'
}

const ALLOWED_HOSTS = [
  self.location.host
]

const EXCLUDED_PATH_PREFIXES = [
  `/${KIRBY_API_SLUG}/`,
  '/panel/',
  '/media/panel/',
  '/media/plugins/'
]

const PRECACHE_URLS = [
  ...(self.__PRECACHE_MANIFEST || []),
  '/',
  `/${CONTENT_API_SLUG}/home.json`,
  `/${CONTENT_API_SLUG}/error.json`,
  `/${CONTENT_API_SLUG}/offline.json`
]

/**
 * Stash an item in specified cache
 *
 * @param {string} cacheName Name of cache
 * @param {object} request Request data
 * @param {object} response Cloned fetch response
 */
async function stashInCache (cacheName, request, response) {
  const cache = await caches.open(cacheName)
  cache.put(request, response)
}

/**
 * Limit the number of items in a specified cache
 *
 * @param {string} cacheName Name of cache
 * @param {number} maxItems Limit of images to cache
 * @returns {Function} Run until limit is fullfilled
 */
async function trimCache (cacheName, maxItems) {
  const cache = await caches.open(cacheName)
  const keys = await cache.keys()
  if (keys.length > maxItems) {
    await cache.delete(keys[0])
    trimCache(cacheName, maxItems)
  }
}

self.addEventListener('message', ({ data }) => {
  if (typeof data !== 'object' || data === null) return
  const { command } = data

  if (command === 'skipWaiting') {
    self.skipWaiting()
  }

  if (command === 'trimCaches') {
    if (MAX_CACHED_PAGES) trimCache(CACHE_KEYS.PAGES, MAX_CACHED_PAGES)
    if (MAX_CACHED_IMAGES) trimCache(CACHE_KEYS.IMAGES, MAX_CACHED_IMAGES)
  }
})

self.addEventListener('install', event => {
  // These items must be cached for the service worker to complete installation
  event.waitUntil(
    (async () => {
      const cache = await caches.open(CACHE_KEYS.STATIC)
      await cache.addAll(PRECACHE_URLS.map(url => new Request(url, { credentials: 'include' })))
    })()
  )
})

self.addEventListener('activate', event => {
  // Remove caches whose name is no longer valid
  event.waitUntil(
    (async () => {
      const keys = await caches.keys()
      for (const key of keys) {
        if (!Object.values(CACHE_KEYS).includes(key)) {
          await caches.delete(key)
        }
      }
    })()
  )
})

self.addEventListener('fetch', event => {
  const { request } = event
  const url = new URL(request.url)
  const destination = request.headers.get('Accept')

  if (request.method !== 'GET') return
  if (!ALLOWED_HOSTS.find(host => url.host === host)) return
  if (EXCLUDED_PATH_PREFIXES.some(path => url.pathname.startsWith(path))) return

  const isHTML = destination.startsWith('text/html')
  const isImage = destination.startsWith('image')
  const isAsset = /^\/(assets|dist)\//.test(url.pathname)
  const isJSON = url.pathname.endsWith('.json')

  // Cache-first strategy for static assets and images,
  // network-first strategy for everything else
  event.respondWith(async function () {
    // Lookup cached response of the given request
    const cachedResponse = await caches.match(request)

    // Return cached HTML, asset or image, if available
    if (cachedResponse && (isHTML || isImage || isAsset)) return cachedResponse

    // Create a controller to abort fetch requests after timeout
    const controller = new AbortController()
    const { signal } = controller

    const timeoutId = setTimeout(() => controller.abort(), FETCH_TIMEOUT)

    try {
      const response = await fetch(request, { signal })
      const copy = response.clone()
      clearTimeout(timeoutId)

      if (
        PRECACHE_URLS.includes(url.pathname) ||
        PRECACHE_URLS.includes(url.pathname + '/')
      ) {
        stashInCache(CACHE_KEYS.STATIC, request, copy)
      } else if (isJSON) {
        stashInCache(CACHE_KEYS.PAGES, request, copy)
      } else if (isImage) {
        stashInCache(CACHE_KEYS.IMAGES, request, copy)
      }

      return response
    } catch (error) {
      if (error.name === 'AbortError') console.log('Fetch aborted after timeout for', request.url)

      // Return cached response, if available
      if (cachedResponse) return cachedResponse

      // Return HTML of index page, the frontend will handle redirecting
      // to offline page if applicable
      if (isHTML) return await caches.match('/')

      // When offline and JSON data for the requested page wasn't cached
      // before, return a fallback JSON
      if (isJSON) {
        return new Response(
          JSON.stringify({ __isOffline: true }),
          {
            headers: {
              'Content-Type': 'application/json',
              'Cache-Control': 'no-store'
            }
          }
        )
      }

      console.error(error)
    }
  }())
})

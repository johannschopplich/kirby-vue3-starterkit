/* eslint-env serviceworker */
/* global API_LOCATION, VERSION */

const MAX_CACHED_IMAGES = 50

const CACHE_KEYS = {
  STATIC: `static-${VERSION}`,
  PAGES: `pages-${VERSION}`,
  IMAGES: `images-${VERSION}`
}

const EXCLUDED_URLS = [
  '/media/plugins/',
  '/panel/'
]

const PRECACHE_URLS = [
  '/',
  '/index.html',
  `${API_LOCATION}/home.json`,
  `${API_LOCATION}/error.json`,
  `${API_LOCATION}/offline.json`,
  ...(self.__PRECACHE_ASSET_URLS || [])
]

const ALLOWED_HOSTS = [
  location.host // or predefined host like `example.com`
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
    return trimCache(cacheName, maxItems)
  }
}

self.addEventListener('message', ({ data }) => {
  if (data.command === 'trimCaches') {
    trimCache(CACHE_KEYS.IMAGES, MAX_CACHED_IMAGES)
  }
})

self.addEventListener('install', event => {
  self.skipWaiting()

  // These items must be cached for the service worker to complete installation
  event.waitUntil(
    (async () => {
      const cache = await caches.open(CACHE_KEYS.STATIC)
      return cache.addAll(PRECACHE_URLS.map(url => new Request(url, { credentials: 'include' })))
    })()
  )
})

self.addEventListener('activate', event => {
  self.clients.claim()

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
  const hasAcceptHeader = type => request.headers.get('Accept').includes(type)

  if (request.method !== 'GET') return
  if (!ALLOWED_HOSTS.find(host => url.hostname === host)) return
  if (EXCLUDED_URLS.some(page => request.url.includes(page))) return

  // Cache-first strategy for images, network-first strategy
  // for everything else
  event.respondWith(async function () {
    const isHTML = hasAcceptHeader('text/html')
    const isJSON = request.url.endsWith('.json')
    const isImage = hasAcceptHeader('image')

    // Lookup cached response of the given request
    const cachedResponse = await caches.match(request)

    // Return cached image, if available
    if (isImage && cachedResponse) return cachedResponse

    try {
      const response = await fetch(request)
      const copy = response.clone()

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
    } catch (fetchError) {
      // Return cached response, if available
      if (cachedResponse) return cachedResponse

      // Return HTML of index page, the frontend will handle redirecting
      // to offline page if applicable
      if (isHTML) return await caches.match('/')

      // When offline and JSON data for the requested page wasn't cached
      // before, return a fallback JSON
      if (isJSON) {
        return new Response(JSON.stringify({ error: 'offline' }), {
          headers: {
            'Content-Type': 'application/json',
            'Cache-Control': 'no-store'
          }
        })
      }

      console.error(fetchError)
    }
  }())
})
import { useRouter } from 'vue-router'
import { kirbyStore } from '../store/kirbyStore'
import { log } from '../helpers'

/**
 * Location of the Kirby API backend
 *
 * @constant {string}
 */
const apiLocation = import.meta.env.VITE_KIRBY_API_LOCATION

/**
 * Transform a path to a Kirby-compatible page id
 *
 * @param {string} path The path to parse and transform
 * @returns {string} The corresponding page id
 */
const toPageId = path => {
  if (path.startsWith('/')) path = path.slice(1)
  if (path.endsWith('/')) path = path.slice(0, -1)
  return path || 'home'
}

/**
 * Retrieve a path or page id from either store or network
 *
 * @param {string} path Path or page id to retrieve
 * @param {object} [options] Optional options
 * @param {boolean} options.force Skip page lookup in store and fetch page freshly
 * @returns {object} The page data
 */
const getPage = async (path, { force = false } = {}) => {
  const id = toPageId(path)
  const router = useRouter()

  // Try to get cached page from api store, except when `force` is `true`
  if (!force) {
    const storedPage = kirbyStore.getPage(id)

    // Use cached page if already fetched once
    if (storedPage) {
      log(`[kirby] Pulling ${id} page data from store.`)
      return storedPage
    }
  }

  // Otherwise fetch page for the first time
  log(`[kirby] Fetching ${apiLocation}/${id}.json…`)

  let page
  try {
    const response = await fetch(`${apiLocation}/${id}.json`)
    page = await response.json()
  } catch (error) {
    console.error(error)
    log(`[kirby] ${id} page data couldn't be fetched. Redirecting to error page…`)
    router.push({ path: '/error' })
    return
  }

  // Redirect to offline page if no stored data was found and no data for the
  // page id has been cached by the service worker
  // Note: data for `home` and `offline` pages are always available since they
  // are precached by service worker
  if ('status' in page && page.status === 'offline') {
    log('[kirby] Device seems to be offline. Redirecting to offline page…')
    router.push({ path: '/offline' })
    return
  }

  log(`[kirby] Fetched ${id} page data:`, page)

  // Add `site` object provided via `home` page to api store
  if (id === 'home') {
    kirbyStore.setSite(page.site)
  }

  // Add page data to api store
  kirbyStore.addPage({ id, data: page })

  return page
}

/**
 * Hook for API location and handling methods of the Kirby API
 *
 * @returns {object} Object containing API-related methods
 */
export const useKirbyApi = () => {
  return {
    apiLocation,
    getPage
  }
}

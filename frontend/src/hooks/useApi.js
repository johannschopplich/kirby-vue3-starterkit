import { apiStore } from '../store/apiStore'
import { router } from '../router'
import { log } from '../helpers'

/**
 * Location of the Kirby API backend
 *
 * @constant {string}
 */
const apiLocation = import.meta.env.VITE_KIRBY_API_LOCATION

/**
 * Transform a path to its Kirby page id
 *
 * @param {string} path The path to parse
 * @returns {string} The corresponding page id
 */
const toPageId = path => {
  if (path.startsWith('/')) path = path.slice(1)
  if (path.endsWith('/')) path = path.slice(0, -1)
  return path || 'home'
}

/**
 * Retrieve a page by paht or id from either store or network
 *
 * @param {string} path Path or page id to retrieve
 * @param {object} [options] Optional options
 * @param {boolean} options.force Skip page lookup in store and fetch page freshly
 * @returns {object} The page data
 */
const getPage = async (path, { force = false } = {}) => {
  const id = toPageId(path)

  // Try to get cached page from api store, except when `force` is `true`
  if (!force) {
    const storedPage = apiStore.getPage(id)

    // Use cached page if already fetched once
    if (storedPage) {
      log(`[useApi] Pulling ${id} page data from store.`)
      return storedPage
    }
  }

  // Otherwise fetch page for the first time
  log(`[useApi] Fetching ${apiLocation}/${id}.json…`)

  let page
  try {
    const response = await fetch(`${apiLocation}/${id}.json`)
    page = await response.json()
  } catch (error) {
    console.error(error)
    log(`[useApi] ${id} page data couldn't be fetched. Redirecting to error page…`)
    router.push({ path: '/error' })
    return
  }

  // Redirect to offline page if no stored data was found and no data for the
  // page id has been cached by the service worker
  // Note: data for `home` and `offline` pages are always available since they
  // are precached by service worker
  const { status } = page
  if (status === 'offline') {
    log('[useApi] Device seems to be offline. Redirecting to offline page…')
    router.push({ path: '/offline' })
    return
  }

  log(`[useApi] Fetched ${id} page data:`, page)

  // Add `site` object provided via `home` page to api store
  if (id === 'home') {
    apiStore.setSite(page.site)
  }

  // Add page data to api store
  apiStore.addPage({ id, data: page })

  return page
}

/**
 * Hook for API location and handling methods
 *
 * @returns {object} Object containing API-related methods
 */
export const useApi = () => {
  return {
    apiLocation,
    getPage
  }
}

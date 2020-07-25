import { apiStore } from '../store/apiStore'
import { router } from '../router'
import { devLog } from '../helpers'

/**
 * Location of the Kirby API backend
 *
 * @constant {string}
 */
const apiLocation = import.meta.env.VITE_KIRBY_API_LOCATION

/**
 * Retrieve a page by id from either store or fetch it freshly
 *
 * @param {string} id Page id to retrieve
 * @param {object} options Set of options
 * @param {boolean} options.force Skip page lookup in store and fetch page freshly
 * @returns {object} The page data
 */
const getPage = async (id, { force = false } = {}) => {
  await apiStore.init()

  // Try to get cached page from api store, except when `force` is `true`
  if (!force) {
    const storedPage = apiStore.getPage(id)

    // Use cached page if already fetched once
    if (storedPage) {
      devLog(`[useApi] Pulling ${id} page data from store.`)
      return storedPage
    }
  }

  // Otherwise fetch page for the first time
  devLog(`[useApi] Fetching ${apiLocation}/${id}.json…`)

  let page
  try {
    const response = await fetch(`${apiLocation}/${id}.json`)
    page = await response.json()
  } catch (error) {
    console.error(error)
    devLog(`[useApi] ${id} page data couldn't be fetched. Redirecting to error page…`)
    router.push({ path: '/error' })
    return
  }

  // Redirect to offline page if no stored data was found and no data for the
  // page id has been cached by the service worker
  // This could be simplified in a `router.beforeEach` hook, but if IndexedDB
  // is disabled we have to rely on the service worker to return cached data
  // or a fallback response (see below)
  // Note: data for `home` and `offline` pages are always available since they
  // are precached by service worker
  const { status } = page
  if (status === 'offline') {
    devLog('[useApi] Device seems to be offline. Redirecting to offline page…')
    router.push({ path: '/offline' })
    return
  }

  devLog(`[useApi] Fetched ${id} page data:`, page)

  // Add `site` object provided via `home` page to api store
  if (id === 'home') {
    apiStore.addSite(page.site)
  }

  // Make sure page gets stored freshly if `force` is `true`
  if (force) {
    apiStore.removePage(id)
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

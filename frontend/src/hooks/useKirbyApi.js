import { kirbyApiStore } from '../store/kirbyApiStore'
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
  await kirbyApiStore.init()

  // Try to get cached page from api store, except when `force` is `true`
  if (!force) {
    const storedPage = kirbyApiStore.getPage(id)

    // Use cached page if already fetched once
    if (storedPage) {
      devLog(`[KirbyAPI] Pulling ${id} page data from store.`)
      return storedPage
    }
  }

  // Otherwise fetch page for the first time
  devLog(`[KirbyAPI] Fetching ${apiLocation}/${id}.json…`)

  let page
  try {
    const resp = await fetch(`${apiLocation}/${id}.json`)
    page = await resp.json()
  } catch (error) {
    console.error(error)
    devLog(`[KirbyAPI] ${id} page data couldn't be fetched. Redirecting to offline page…`)
    router.push({ path: '/error' })
    return
  }

  devLog(`[KirbyAPI] Fetched ${id} page data:`, page)

  // Redirect to offline page if fetched page data indicates
  // the response JSON was serverd by the service worker
  // Note: `home.json` and `offline.json` are always available since
  // they are precached
  if ('error' in page && page.error === 'offline') {
    devLog('[KirbyAPI] Device seems to be offline. Redirecting to offline page…')
    router.push({ path: '/offline' })
    return
  }

  // Add `site` object provided via `home` page to api store
  if (id === 'home') {
    kirbyApiStore.addSite(page.site)
  }

  // Make sure page gets stored freshly if `force` is `true`
  if (force) {
    kirbyApiStore.removePage(id)
  }

  // Add page data to api store
  kirbyApiStore.addPage({ id, data: page })

  return page
}

/**
 * Hook containing API the methods
 *
 * @returns {object} Object with API location and page methods
 */
export const useKirbyAPI = () => {
  return {
    apiLocation,
    getPage
  }
}

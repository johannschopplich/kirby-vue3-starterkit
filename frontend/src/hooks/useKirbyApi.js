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
 * @param {string} path Path to parse and transform
 * @returns {string} Corresponding page id
 */
const toPageId = path => {
  if (path.startsWith('/')) path = path.slice(1)
  if (path.endsWith('/')) path = path.slice(0, -1)
  return path || 'home'
}

/**
 * Retrieve a page data by id from either store or network
 *
 * @param {string} path Path or page id to retrieve
 * @param {object} [options] Optional options
 * @param {boolean} options.revalidate Skip cached page lookup and fetch page freshly
 * @returns {object} The page's data
 */
const getPage = async (
  path,
  { revalidate = false } = {}
) => {
  const id = toPageId(path)
  let page

  // Try to get cached page from api store, except when `revalidate` is `true`
  if (!revalidate) {
    const cachedPage = kirbyStore.getPage(id)

    // Use cached page if already fetched once
    if (cachedPage) {
      log(`[getPage] Pulling ${id} page data from cache.`)
      return cachedPage
    }
  }

  // Otherwise fetch page for the first time
  log(`[getPage] Fetching ${apiLocation}/${id}.json…`)

  try {
    const response = await fetch(`${apiLocation}/${id}.json`)
    page = await response.json()
  } catch (error) {
    console.error(error)
    return
  }

  log(`[getPage] Fetched ${id} page data:`, page)

  // Add `site` object provided via `home` page to api store
  if (id === 'home') {
    kirbyStore.setSite(page.site)
  }

  // Add page data to api store
  kirbyStore.setPage({ id, data: page })

  return page
}

/**
 * Hook for handling Kirby API data retrieval and its location
 *
 * @returns {object} Object containing API-related methods
 */
export const useKirbyApi = () => {
  return {
    apiLocation,
    getPage
  }
}

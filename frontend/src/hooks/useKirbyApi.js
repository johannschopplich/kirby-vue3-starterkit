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
 * @param {boolean} options.revalidate Skip page lookup in cache and fetch page freshly
 * @returns {object} The page's data
 */
const getPage = async (
  path,
  { revalidate = false } = {}
) => {
  let page
  const id = toPageId(path)

  // Try to get cached page from api store, except when `revalidate` is `true`
  if (!revalidate) {
    const cachedPage = kirbyStore.getPage(id)

    // Use cached page if already fetched once
    if (cachedPage) {
      log(`[getPage] Pulling ${id} page data from cache.`)
      return {
        data: cachedPage,
        servedFromCache: true
      }
    }
  }

  // Otherwise fetch page for the first time
  log(`[getPage] ${revalidate ? 'Revalidating' : 'Fetching'} ${apiLocation}/${id}.json…`)

  try {
    const response = await fetch(`${apiLocation}/${id}.json`)
    page = await response.json()
  } catch (error) {
    console.error(error)
    return
  }

  !revalidate && log(`[getPage] Fetched ${id} page data:`, page)

  // Add page data to api store
  kirbyStore.setPage({ id, data: page })

  return {
    data: page,
    servedFromCache: false
  }
}

/**
 * Fetch global `site` object and add it to the store
 */
const fetchSite = async () => {
  // `site` is provided by `home` page
  const { data: home } = await getPage('home')
  kirbyStore.setSite(home.site)
}

/**
 * Hook for handling Kirby API data retrieval and its location
 *
 * @returns {object} Object containing API-related methods
 */
export const useKirbyApi = () => {
  return {
    apiLocation,
    fetchSite,
    getPage
  }
}

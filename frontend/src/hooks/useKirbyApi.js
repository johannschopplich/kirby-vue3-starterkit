import { kirbyStore } from '../store/kirbyStore'
import { log } from '../helpers'

/**
 * Location of the Kirby API backend
 *
 * @constant {string}
 */
const apiLocation = import.meta.env.VITE_KIRBY_API_LOCATION

/**
 * Check if a page has been cached
 *
 * @param {string} id Page id to look up
 * @returns {boolean} `true` if the page exists
 */
const hasPage = id => kirbyStore.hasPage(id)

/**
 * Retrieve a page data by id from either store or network
 *
 * @param {string} id Page id to retrieve
 * @param {object} [options] Optional options
 * @param {boolean} options.revalidate Skip cache look-up and fetch page freshly
 * @returns {(object|boolean)} The page's data or `false` if fetch request failed
 */
const getPage = async (
  id,
  {
    revalidate = false
  } = {}
) => {
  let page
  const cachedPage = kirbyStore.getPage(id)
  const targetUrl = `${apiLocation}/${id}.json`

  // Use cached page if present in the store, except when revalidating
  if (!revalidate && cachedPage) {
    log(`[getPage] Pulling ${id} page data from cache.`)
    return cachedPage
  }

  // Otherwise fetch page for the first time
  log(`[getPage] ${revalidate ? `Revalidating ${id} page data.` : `Fetching ${targetUrl}…`}`)

  try {
    const response = await fetch(targetUrl)

    if (!response.ok) {
      throw new Error(`The requested URL ${targetUrl} failed with response error \`${response.statusText}\`.`)
    }

    const contentType = response.headers.get('Content-Type')
    if (!contentType || !contentType.includes('application/json')) {
      throw new TypeError('The response is not a valid JSON response.')
    }

    page = await response.json()
  } catch (error) {
    console.error(error)
    return false
  }

  if (!revalidate) {
    log(`[getPage] Fetched ${id} page data:`, page)
  }

  // Add page data to the store
  if (JSON.stringify(cachedPage) !== JSON.stringify(page)) {
    kirbyStore.setPage({ id, data: page })
  }

  return page
}

/**
 * Fetch global `site` object and save it to the store
 */
const initSite = async () => {
  // `site` is provided by `home` page
  const home = await getPage('home')
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
    initSite,
    hasPage,
    getPage
  }
}

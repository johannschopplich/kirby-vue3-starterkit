import { kirbyStore } from '../store/kirbyStore'
import { log } from '../helpers'

/**
 * Location of the Kirby API backend
 *
 * @constant {string}
 */
const apiLocation = import.meta.env.VITE_BACKEND_API_LOCATION

/**
 * Fetcher function to request JSON data from the server
 *
 * @param {string} url Url to fetch data from
 * @returns {object} Extracted JSON body content from the response
 */
const fetcher = async url => {
  const response = await fetch(url)

  if (!response.ok) {
    throw new Error(`The requested URL ${url} failed with response error "${response.statusText}".`)
  }

  const contentType = response.headers.get('Content-Type')
  if (!contentType || !contentType.includes('application/json')) {
    throw new TypeError('The response is not a valid JSON response.')
  }

  return await response.json()
}

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
  const isCached = kirbyStore.hasPage(id)
  const targetUrl = `${apiLocation}/${id}.json`

  // Use cached page if present in the store, except when revalidating
  if (!revalidate && isCached) {
    log(`[getPage] Pulling ${id} page data from cache.`)
    return kirbyStore.getPage(id)
  }

  // Otherwise fetch page for the first time
  log(`[getPage] ${revalidate ? `Revalidating ${id} page data.` : `Fetching ${targetUrl}…`}`)

  try {
    page = await fetcher(targetUrl)
  } catch (error) {
    console.error(error)
    return false
  }

  if (!revalidate) {
    log(`[getPage] Fetched ${id} page data:`, page)
  }

  // Add page data to the store
  if (!isCached || revalidate) {
    kirbyStore.setPage(id, page)
  }

  return page
}

/**
 * Initialize global `site` object and save it to the store
 */
const initSite = async () => {
  const site = process.env.NODE_ENV === 'development'
    ? await fetcher(`${apiLocation}/__site.json`)
    : JSON.parse(document.getElementById('site-data').textContent)

  kirbyStore.setSite(site)
}

/**
 * Hook for handling Kirby API data retrieval and its location
 *
 * @returns {object} Object containing API-related methods
 */
export const useKirbyApi = () => ({
  apiLocation,
  getPage,
  initSite
})

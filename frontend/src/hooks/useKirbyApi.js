import { reactive, toRaw } from 'vue'

/**
 * Location of the Kirby API backend
 *
 * @constant {string}
 */
const apiLocation = import.meta.env.VITE_BACKEND_API_LOCATION

/**
 * Reactive map to store pages in
 *
 * @constant {object}
 */
const pages = reactive(new Map())

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
  const __DEV__ = import.meta.env.DEV
  const isCached = pages.has(id)
  const targetUrl = `${apiLocation}/${id}.json`

  // Use cached page if present in the store, except when revalidating
  if (!revalidate && isCached) {
    if (__DEV__) {
      console.log(`[getPage] Pulling ${id} page data from cache.`)
    }

    return toRaw(pages.get(id))
  }

  // Otherwise retrieve page data for the first time
  if (__DEV__) {
    console.log(`[getPage] ${revalidate ? `Revalidating ${id} page data.` : `Fetching ${targetUrl}…`}`)
  }

  try {
    page = await fetcher(targetUrl)
  } catch (error) {
    console.error(error)
    return false
  }

  if (!revalidate) {
    if (__DEV__) {
      console.log(`[getPage] Fetched ${id} page data:`, page)
    }
  }

  // Add page data to the store
  if (!isCached || revalidate) {
    pages.set(id, page)
  }

  return page
}

/**
 * Check if a page has been cached
 *
 * @param {string} id Id of page to look up
 * @returns {boolean} `true` if the page exists
 */
const hasPage = id => pages.has(id)

/**
 * Hook for handling Kirby API data retrieval and its location
 *
 * @returns {object} Object containing API-related methods
 */
export const useKirbyApi = () => ({
  apiLocation,
  fetcher,
  pages,
  hasPage,
  getPage
})

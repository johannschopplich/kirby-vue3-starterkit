import { useLanguages } from './'

const cache = new Map()

/**
 * Fetch wrapper to request JSON data
 *
 * @param {string} url The URL to fetch data from
 * @returns {object} The JSON content from the response
 */
const fetcher = async url => {
  const response = await fetch(url)

  if (!response.ok) {
    throw new Error(`Request ${url} failed with "${response.statusText}".`)
  }

  return await response.json()
}

/**
 * Builds an API URL for a specific file and language
 *
 * @param {string} path The path to the file desired
 * @returns {string} The final URL
 */
const apiUri = path => {
  const { isMultilang, languageCode } = useLanguages()
  let result = ''

  // Add language path in multi-language setup
  if (isMultilang) {
    result += `/${languageCode}`
  }

  // Add the API path
  result += `/${import.meta.env.VITE_BACKEND_API_SLUG}`

  // Add the file to fetch
  result += `/${path}`

  return result
}

/**
 * Retrieves page data by id from either network or store
 *
 * @param {string} id The page to retrieve
 * @param {object} [options] Optional options
 * @param {boolean} [options.revalidate=false] Skip cache look-up and fetch page freshly
 * @returns {object|boolean} The page's data or `false` if fetch request failed
 */
const getPage = async (
  id,
  {
    revalidate = false
  } = {}
) => {
  let page
  const isCached = cache.has(id)
  const targetUrl = apiUri(`${id}.json`)

  // Use cached page if present in the store, except when revalidating
  if (!revalidate && isCached) {
    if (import.meta.env.DEV) {
      console.log(`[getPage] Pulling ${id} page data from cache.`)
    }

    return cache.get(id)
  }

  // Otherwise retrieve page data for the first time
  if (import.meta.env.DEV) {
    console.log(`[getPage] ${revalidate ? `Revalidating ${id} page data.` : `Fetching ${targetUrl}…`}`)
  }

  try {
    page = await fetcher(targetUrl)
  } catch (error) {
    console.error(error)
    return false
  }

  if (import.meta.env.DEV && !revalidate) {
    console.log(`[getPage] Fetched ${id} page data:`, page)
  }

  // Add page data to the store, respectively overwrite it
  if (!isCached || revalidate) {
    cache.set(id, page)
  }

  return page
}

/**
 * Checks if a page has been cached already
 *
 * @param {string} id The page id to look up
 * @returns {boolean} `true` if the page exists
 */
const hasPage = id => cache.has(id)

/**
 * Returns methods for communication with the backend API
 *
 * @returns {object} API related methods
 */
export default () => ({
  apiUri,
  fetcher,
  cache,
  hasPage,
  getPage
})

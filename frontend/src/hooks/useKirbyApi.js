import { kirbyApiStore } from '../store/kirbyApiStore'

/**
 * API location of the Kirby backend
 *
 * @constant {string}
 */
let apiLocation = import.meta.env.VITE_KIRBY_API_LOCATION || ''

if (apiLocation) {
  // Add leading slash if not given
  if (!apiLocation.startsWith('/')) apiLocation = '/' + apiLocation
  // Remove trailing slash if present
  if (apiLocation.endsWith('/')) apiLocation = apiLocation.slice(0, -1)
}

if (apiLocation === '/api') {
  if (process.env.NODE_ENV === 'development') {
    throw new Error('Environment variable `KIRBY_API_LOCATION` mustn\'t be the same as Kirby\'s internal API endpoint.')
  }
}

/**
 * Retrieve a page by id from either store or fetch it freshly
 *
 * @param {string} id Page id to retrieve
 * @param {object} options Set of options
 * @param {boolean} options.force Skip store and fetch page freshly
 * @returns {object} Page data
 */
const getPage = async (id, { force = false } = {}) => {
  await kirbyApiStore.init()

  // Try to get cached page from api store, except when `force` is `true`
  if (!force) {
    const storedPage = kirbyApiStore.getPage(id)

    // Use cached page if already fetched once
    if (storedPage) {
      if (process.env.NODE_ENV === 'development') {
        console.log(`[KirbyAPI] Pulling ${id} page data from store.`)
      }

      return storedPage
    }
  }

  // Otherwise fetch page for the first time
  if (process.env.NODE_ENV === 'development') {
    console.log(`[KirbyAPI] Fetching ${apiLocation}/${id}.json…`)
  }

  const resp = await fetch(`${apiLocation}/${id}.json`)
  const page = await resp.json()

  if (process.env.NODE_ENV === 'development') {
    console.log(`[KirbyAPI] Fetched ${id} page data:`, page)
  }

  // Make sure page gets stored freshly if `force` is `true`
  if (force) {
    kirbyApiStore.removePage(id)
  }

  // Add page data to api store
  kirbyApiStore.addPage({ id, data: page })

  // Add `site` object provided via `home` page to api store
  if (id === 'home') {
    kirbyApiStore.addSite(page.site)
  }

  return page
}

/**
 * Hook to handle the Kirby API
 *
 * @returns {object} Object with constants and methods
 */
export const useKirbyAPI = () => {
  return {
    apiLocation,
    getPage
  }
}

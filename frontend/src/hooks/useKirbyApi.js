import { kirbyApiStore } from '../store/kirbyApiStore'

/**
 * @const {string}
 */
const apiUrl = process.env.NODE_ENV === 'development'
  ? import.meta.env.KIRBY_API_URL
  : window.location.origin

/**
 * Retrieve a page by id from either store or fetch it freshly
 *
 * @param {string} id Page id to retrieve
 * @param {Object} options Set of options
 * @returns {Object}
 */
const getPage = async (id, { force = false } = {}) => {
  await kirbyApiStore.init()

  // Try to get cached page from api store, except when `force` is `true`
  if (!force) {
    const storedPage = kirbyApiStore.getPage(id)

    // Use cached page if already fetched once
    if (storedPage) {
      if (process.env.NODE_ENV === 'development') {
        console.log(`[KirbyAPI] Use ${apiUrl}/${id}.json from store`)
      }

      return storedPage
    }
  }

  // Otherwise fetch page for the first time
  const resp = await fetch(`${apiUrl}/${id}.json`)
  const page = await resp.json()

  if (process.env.NODE_ENV === 'development') {
    console.log(`[KirbyAPI] Fetch ${apiUrl}/${id}.json`)
    console.log(page)
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

export const useKirbyAPI = () => {
  return {
    getPage
  }
}

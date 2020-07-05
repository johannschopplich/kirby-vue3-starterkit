import { PersistentStore } from './PersistentStore'
import { routes } from '../router'

/**
 * Centralized `site` and pages data
 *
 * @extends PersistentStore
 */
class KirbyApiStore extends PersistentStore {
  /**
   * The state object
   */
  data () {
    return {
      site: [],
      pages: []
    }
  }

  /**
   * Gets a cached page from store if found and if its last modified
   * timestamp matches the content's actual timestamp in backend
   *
   * @param {string} id Page id to retrieve
   * @returns {Object}
   */
  getPage (id) {
    const page = this.getState().pages.find(i => i.__id === id)
    if (!page) return

    // Check if stored page is outdated if state is persisted,
    // except for homepage which has always latest data
    if (this.persistState && id !== 'home') {
      // Get the current page meta from router which gets initialized
      // with the up-to-date last modified timestamps
      const routerPageData = routes.find(i => i.path === `/${id}`)
      if (!routerPageData) return
      if (!('meta' in routerPageData) || !('modified' in page)) return

      // Bail if the timestamp doesn't match
      if (routerPageData.meta.modified !== page.modified) return
    }

    // Deep clone to return object, not proxy, for Safari support
    // This works fine, as long as no `Date`s, functions, `undefined`, [NaN], Maps, Sets
    // and other complex types appear within the object – which is the case
    // for fetched JSON from Kirby backend anyway
    return JSON.parse(JSON.stringify(page))
  }

  /**
   * Adds a page to the store
   *
   * @param {object} data Includes page `id` and `data` object
   */
  addPage ({ id, data }) {
    this.state.pages.push({ __id: id, ...data })
  }

  /**
   * Removes a page from the store
   *
   * @param {string} id Page id to remove
   */
  removePage (id) {
    const index = this.getState().pages.findIndex(i => i.__id === id)
    if (index === -1) return
    this.state.pages.splice(index, 1)
  }

  /**
   * Gets the global `site` data
   *
   * @returns {Object}
   */
  getSite () {
    return this.getState().site
  }

  /**
   * Adds the global `site` data to the store
   *
   * @param {object} data Global `site` object
   */
  addSite (data) {
    this.state.site = data
  }
}

/**
 * Internal store name
 * @const {string}
 */
const storeName = 'KIRBY_API_STORE'

/**
 * Ready-to-use Kirby API store
 * @const {class}
 */
export const kirbyApiStore = new KirbyApiStore(storeName)

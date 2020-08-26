import { Store } from './base/Store'

/**
 * Centralized `site` and pages data
 *
 * @augments Store
 */
class KirbyStore extends Store {
  /**
   * The state object
   *
   * @returns {object} The central state
   */
  data () {
    return {
      site: [],
      pages: new Map()
    }
  }

  /**
   * Gets a cached page from store if present
   *
   * @param {string} id Page id to retrieve
   * @returns {(object|undefined)} Corresponding page object
   */
  getPage (id) {
    const page = this.getState().pages.get(id)
    if (page) return page
  }

  /**
   * Adds a page to the store or overwrites it
   *
   * @param {object} options Set of options
   * @param {string} options.id Page id
   * @param {object} options.data Page data
   */
  setPage ({ id, data }) {
    this.state.pages.set(id, data)
  }

  /**
   * Removes a page from the store
   *
   * @param {string} id Page id to remove
   */
  deletePage (id) {
    this.state.pages.delete(id)
  }

  /**
   * Gets the global `site` data
   *
   * @returns {object} Readonly `site` state
   */
  getSite () {
    return this.getState().site
  }

  /**
   * Sets the global `site` data to the store
   *
   * @param {object} data Global `site` object
   */
  setSite (data) {
    this.state.site = data
  }
}

/**
 * Internal store name
 *
 * @constant {string}
 */
const storeName = 'KIRBY_API_STORE'

/**
 * Ready-to-use Kirby API store
 *
 * @constant {object}
 */
export const kirbyStore = new KirbyStore(storeName)

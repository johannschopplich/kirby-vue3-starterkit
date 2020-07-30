import { Store } from './base/Store'
import { toRaw } from 'vue'

/**
 * Centralized `site` and pages data
 *
 * @augments Store
 */
class ApiStore extends Store {
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
   * @returns {(object|undefined)} Current page object
   */
  getPage (id) {
    const page = this.getState().pages.get(id)
    // Return the raw, original object of athe reactive `page` object for Safari support
    if (page) return toRaw(page)
  }

  /**
   * Adds a page to the store
   *
   * @param {object} options Set of options
   * @param {string} options.id Page id
   * @param {object} options.data Page data
   */
  addPage ({ id, data }) {
    this.state.pages.set(id, data)
  }

  /**
   * Removes a page from the store
   *
   * @param {string} id Page id to remove
   */
  removePage (id) {
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
 *
 * @constant {string}
 */
const storeName = 'KIRBY_API_STORE'

/**
 * Ready-to-use Kirby API store
 *
 * @constant {object}
 */
export const apiStore = new ApiStore(storeName)

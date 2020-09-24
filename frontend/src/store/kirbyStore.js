import { Store } from './base/Store'
import { toRaw } from 'vue'

/**
 * Centralized `site` and pages data
 *
 * @augments Store
 */
class KirbyStore extends Store {
  /**
   * Data later wrapped as reactive object
   *
   * @returns {object} Data for central state
   */
  data () {
    return {
      site: null,
      pages: new Map()
    }
  }

  /**
   * Checks if a page has been cached
   *
   * @param {string} id Id of page to look up
   * @returns {boolean} `true` if the page exists
   */
  hasPage (id) {
    return this.state.pages.has(id)
  }

  /**
   * Gets a cached page from store if present
   *
   * @param {string} id Page id to retrieve
   * @returns {(object|undefined)} Corresponding page object
   */
  getPage (id) {
    const page = this.state.pages.get(id)
    if (page) return toRaw(page)
  }

  /**
   * Adds a page to the store or overwrites it
   *
   * @param {string} id Page id
   * @param {any} data Page data
   */
  setPage (id, data) {
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

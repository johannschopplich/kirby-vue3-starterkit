import { watch, toRaw } from 'vue'
import { PersistentStore } from './base/PersistentStore'
import { routes } from '../router'
import { set, get } from 'idb-keyval'

/**
 * Centralized `site` and pages data
 *
 * @augments PersistentStore
 */
class ApiStore extends PersistentStore {
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
   * Gets a cached page from store if found and if its last modified
   * timestamp matches the content's actual timestamp in backend
   *
   * @param {string} id Page id to retrieve
   * @returns {(object|undefined)} Current page object
   */
  getPage (id) {
    const page = this.state.pages.get(id)
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

    // Return the raw, original object of athe reactive `page` object for Safari support
    return toRaw(page)
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

  /**
   * Optionally persists the pages state between sessions
   * (Overwrites the base class's function)
   */
  async init () {
    // `VITE_PERSIST_API_STORE` returns a string, but we need a boolean
    if (import.meta.env.VITE_PERSIST_API_STORE !== 'true') return

    // Catch multiple initialization calls
    if (this.isInitialized) return

    // Mutation operations on a database aren't allowed in incognito mode
    try {
      // Check if persisted state exists and if so, use it
      const stateFromIndexedDB = await get(this.storeName)
      if (stateFromIndexedDB) {
        this.state.pages = new Map(JSON.parse(stateFromIndexedDB))
      }

      // Watch for pages state changes and immediately save it to IndexedDB
      watch(() => this.state.pages, val => {
        set(this.storeName, JSON.stringify([...val]))
      }, { deep: true })
    } catch (error) {
      console.error(error)
    }

    this.isInitialized = true
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

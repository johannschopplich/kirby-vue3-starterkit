import { PersistentStore } from './PersistentStore'

const storeName = 'KIRBY_API_STORE'

class KirbyApiStore extends PersistentStore {
  data () {
    return {
      site: [],
      pages: []
    }
  }

  /**
   * Gets a page from store and compares its modified timestamp with the latest one
   * @param {string} id Page id to retrieve
   */
  getPage (id) {
    const page = this.getState().pages.find(i => i.__id === id)
    if (!page) return

    // Check if stored page is outdated and was changed since last cached
    if (this.persistState) {
      const indexedPage = this.getState().site.index.find(i => i.id === id)
      if (!indexedPage || !('modified' in page)) return
      if (indexedPage.modified !== page.modified) return
    }

    return page
  }

  /**
   * Adds a page to the store
   * @param {object} data Includes page `id` and `data` object
   */
  addPage ({ id, data }) {
    this.state.pages.push({ __id: id, ...data })
  }

  /**
   * Removes a page from the store
   * @param {string} id Page id to remove
   */
  removePage (id) {
    const index = this.getState().pages.findIndex(i => i.__id === id)
    if (index === -1) return
    this.state.pages.splice(index, 1)
  }

  /**
   * Gets the global `site` data
   */
  getSite () {
    return this.getState().site
  }

  /**
   * Adds the global `site` data to the store
   * @param {object} data Global `site` object
   */
  addSite (data) {
    this.state.site = data
  }
}

export const kirbyApiStore = new KirbyApiStore(storeName)

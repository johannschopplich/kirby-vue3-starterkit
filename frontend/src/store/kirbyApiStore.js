import { PersistentStore } from './PersistentStore'

const storeName = 'KIRBY_API_STORE'

class KirbyApiStore extends PersistentStore {
  data () {
    return {
      pages: [],
      modifiedIndex: []
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
    if (import.meta.env.VITE_PERSIST_API_STORE) {
      const indexedPage = this.getState().modifiedIndex.find(i => i.id === id)
      if (!('modified' in indexedPage) || !('modified' in page)) return
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
   * Adds the time modified timestamp pages index to the store
   * @param {array} data
   */
  addModifiedIndex (data) {
    this.state.modifiedIndex = data
  }

  /**
   * Removes a page from the store
   * @param {string} id Page id to remove
   */
  removePage (id) {
    const index = this.getState().pages.findIndex(i => i.__id === id)
    if (!index) return
    this.state.pages.splice(index, 1)
  }
}

export const kirbyApiStore = new KirbyApiStore(storeName)

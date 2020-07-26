import { Store } from './Store'
import { watch } from 'vue'
import { set, get } from 'idb-keyval'

/**
 * Centralized state management with persisting state between sessions
 *
 * @augments Store
 */
export class PersistentStore extends Store {
  /**
   * Constructor
   *
   * @param {string} storeName Name of the store
   */
  constructor (storeName) {
    super(storeName)
    this.storeName = storeName
    this.isInitialized = false
  }

  /**
   * Persists the state between sessions
   * Fills the state from IndexedDB and watches for store changes
   */
  async init () {
    // Catch multiple initialization calls
    if (this.isInitialized) return

    // Mutation operations on a database aren't allowed in incognito mode
    try {
      // Check if persisted state exists and if so, use it
      const stateFromIndexedDB = await get(this.storeName)
      if (stateFromIndexedDB) {
        Object.assign(this.state, JSON.parse(stateFromIndexedDB))
      }

      // Watch for state changes and immediately save it to IndexedDB
      watch(() => this.state, val => {
        set(this.storeName, JSON.stringify(val))
      }, { deep: true })
    } catch (error) {
      console.error(error)
    }

    this.isInitialized = true
  }
}

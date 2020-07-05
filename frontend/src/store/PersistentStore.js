import { Store } from './Store'
import { watch } from 'vue'
import { set, get } from 'idb-keyval'

/**
 * Centralized state management with persisting state between sessions
 *
 * @extends Store
 */
export class PersistentStore extends Store {
  /**
   * Constructor
   *
   * @param {string} storeName Name of the store
   * @constructor
   */
  constructor (storeName) {
    super(storeName)
    this.storeName = storeName
    this.isInitialized = false

    // Optionally persist state between browser sessions
    // `VITE_PERSIST_API_STORE` returns a string, but we need a boolean
    this.persistState = JSON.parse(import.meta.env.VITE_PERSIST_API_STORE || false)
  }

  /**
   * Fills the state from IndexedDB and watches for store changes
   */
  async init () {
    // Initialization is only needed for persisting the state
    if (!this.persistState) return

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
      if (process.env.NODE_ENV === 'development') {
        console.error(error)
      }

      this.persistState = false
    }

    this.isInitialized = true
  }
}

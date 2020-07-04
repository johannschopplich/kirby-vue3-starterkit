import { Store } from './Store'
import { ref, watch } from 'vue'
import { set, get } from 'idb-keyval'

export class PersistentStore extends Store {
  constructor (storeName) {
    super(storeName)
    this.storeName = storeName
    this.isInitialized = ref(false)

    // Optionally persist state between browser sessions
    // `VITE_PERSIST_API_STORE` returns a string, but we need a boolean
    this.persistState = JSON.parse(import.meta.env.VITE_PERSIST_API_STORE ?? false)
  }

  async init () {
    // Initialization is only needed for persisting the state
    if (!this.persistState) return

    // Catch multiple initialization calls
    if (this.isInitialized.value) return

    // Check if persisted state exists and if so, use it
    const stateFromIndexedDB = await get(this.storeName)
    if (stateFromIndexedDB) {
      Object.assign(this.state, JSON.parse(stateFromIndexedDB))
    }

    // Watch for state changes and immediately save it to `IndexedDB`
    watch(() => this.state, val => {
      set(this.storeName, JSON.stringify(val))
    }, { deep: true })

    this.isInitialized.value = true
  }

  getIsInitialized () {
    return this.isInitialized
  }
}

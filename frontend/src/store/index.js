import { reactive, readonly, ref, watch } from 'vue'
import { set, get } from 'idb-keyval'

export class Store {
  constructor (storeName) {
    this.storeName = storeName
    const data = this.data()
    this.state = reactive(data)
  }

  getState () {
    return readonly(this.state)
  }
}

export class PersistentStore extends Store {
  constructor (storeName) {
    super(storeName)
    this.storeName = storeName
    this.isInitialized = ref(false)
  }

  async init () {
    if (this.isInitialized) {
      const stateFromIndexedDB = await get(this.storeName)
      if (stateFromIndexedDB) {
        Object.assign(this.state, JSON.parse(stateFromIndexedDB))
      }

      watch(() => this.state, val => set(this.storeName, JSON.stringify(val)), { deep: true })
      this.isInitialized.value = true
    }
  }

  getIsInitialized () {
    return this.isInitialized
  }
}

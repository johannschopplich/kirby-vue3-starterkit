import { reactive, readonly } from 'vue'

/**
 * Centralized state management
 */
export class Store {
  /**
   * Constructor
   *
   * @param {string} storeName Name of the store
   */
  constructor (storeName) {
    this.storeName = storeName
    const data = this.data()
    this.state = reactive(data)
  }

  /**
   * Returns the current state readonly
   *
   * @returns {*} Readonly state
   */
  getState () {
    return readonly(this.state)
  }
}

import { reactive, readonly } from 'vue'

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

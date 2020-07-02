import { Store } from '.'
import { API_STORE_NAME } from './store-names'

class ApiStore extends Store {
  data () {
    return {
      pages: []
    }
  }

  getPage (id) {
    return this.state.pages.find(i => i.__id === id)
  }

  addPage (page) {
    this.state.pages.push(page)
  }
}

export const apiStore = new ApiStore(API_STORE_NAME)

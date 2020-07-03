import { Store } from '..'
import { API_STORE_NAME } from '../names'

class ApiStore extends Store {
  data () {
    return {
      pages: []
    }
  }

  getPage (id) {
    return this.state.pages.find(i => i.__id === id)
  }

  addPage ({ id, data }) {
    this.state.pages.push({ __id: id, ...data })
  }
}

export const apiStore = new ApiStore(API_STORE_NAME)

import { Store } from '..'
import { KIRBY_API_STORE_NAME } from '../names'

class KirbyApiStore extends Store {
  data () {
    return {
      pages: []
    }
  }

  getPage (id) {
    if (!id) return
    return this.state.pages.find(i => i.__id === id)
  }

  addPage ({ id, data }) {
    this.state.pages.push({ __id: id, ...data })
  }
}

export const kirbyApiStore = new KirbyApiStore(KIRBY_API_STORE_NAME)

import { reactive } from 'vue'
import { useKirbyAPI } from './kirby-api'
import { useRoute } from 'vue-router'

export const usePage = () => {
  const { path } = useRoute()
  const { getPage } = useKirbyAPI()

  // Transform route `path` to `pageId` for use with api
  const pageId = (path.endsWith('/') ? path.slice(0, -1) : path).slice(1) || 'home'

  // Setup reactive `page` object with some commonly used keys
  const page = reactive({
    title: null,
    metaTitle: null,
    children: null,
    text: null
  })

  ;(async () => {
    let pageData
    try {
      // Get page from cache or freshly fetch it
      pageData = await getPage(pageId)
    } catch (error) {
      pageData = await getPage('error')
    }

    Object.assign(page, { ...pageData })
    document.title = page.metaTitle

    // TODO: Use `keep-alive` once Vue Router 4 supports it
    // onActivated(() => {
    //   document.title = page.metaTitle
    // })
  })()

  return {
    page
  }
}

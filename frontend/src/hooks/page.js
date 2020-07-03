import { reactive } from 'vue'
import { useKirbyAPI } from './kirby-api'
import { useRoute } from 'vue-router'

export const usePage = () => {
  const { path } = useRoute()

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
    const { getPage } = useKirbyAPI()
    let pageData

    try {
      // Get page from cache or freshly fetch it
      pageData = await getPage(pageId)
    } catch (error) {
      if (process.env.NODE_ENV === 'development') {
        console.error('[KirbyAPI] Failed to fetch page id:', pageId)
      }
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

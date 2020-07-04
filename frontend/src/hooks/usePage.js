import { reactive } from 'vue'
import { useRoute } from 'vue-router'
import { useKirbyAPI } from './useKirbyApi'

/**
 * Return the corressponding page object for the current path
 *
 * @returns {object}
 */
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
    // Get page from cache or freshly fetch it
    Object.assign(page, { ...(await getPage(pageId)) })

    // Set document title
    document.title = page.metaTitle
  })()

  return page
}

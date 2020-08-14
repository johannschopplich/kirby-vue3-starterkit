import { reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useKirbyApi } from './useKirbyApi'
import { useAnnouncer } from './useAnnouncer'

/**
 * Hook for the page data of a given page id or the current route path
 *
 * @param {string} [id] Page id to fetch or current path if not present
 * @returns {object} Reactive page object
 */
export const usePage = id => {
  const router = useRouter()
  const { path } = useRoute()
  const { getPage } = useKirbyApi()
  const { setAnnouncer } = useAnnouncer()

  // Setup reactive page object
  const page = reactive({
    // State
    status: 'pending',
    isLoading: true,

    // Commonly used keys
    title: null,
    metaTitle: null,
    children: null,
    text: null
  })

  // Setup up page waiter and flush method
  let resolve
  let promise = new Promise(r => { resolve = r }) // eslint-disable-line promise/param-names

  /**
   * Define a promise to wait for until the page data is available
   *
   * @example
   * const page = usePage()
   * ;(async () => {
   *   await page.hasLoaded
   *   console.log(page.title)
   * })()
   */
  Object.defineProperty(page, 'hasLoaded', {
    get: () => promise
  })

  ;(async () => {
    // Get page from cache or freshly fetch it
    const data = await getPage(id || path)

    if (!data) {
      page.status = 'error'
    }

    // Append page data to reactive page object
    Object.assign(page, data)

    // Redirect to offline page if page hasn't been cached either in-memory or
    // by the service worker and the offline fallback JSON was returned
    // Note: data for `home` and `offline` pages are always available since they
    // are precached by the service worker
    if (!id && page.status === 'offline') {
      router.replace({ path: '/offline' })
      return
    }

    page.status = 'success'
    page.isLoading = false

    // Flush page waiter
    resolve && resolve()
    resolve = undefined
    promise = undefined

    // Further actions only if the hook was called for the current route
    if (!id) {
      // Set document title
      document.title = page.metaTitle

      // Announce new route
      setAnnouncer(`Navigated to ${page.title}`)
    }
  })()

  return page
}

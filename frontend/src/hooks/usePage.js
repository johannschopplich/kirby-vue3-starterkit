import { reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useKirbyApi } from './useKirbyApi'
import { useAnnouncer } from './useAnnouncer'

/**
 * Hook for the page data of a given page id or the current route path
 *
 * @param {string} [path] Optional path or page id to retrieve
 * @returns {object} Reactive page object
 */
export const usePage = path => {
  const router = useRouter()
  const { path: currentPath } = useRoute()
  const { hasPage, getPage } = useKirbyApi()
  const { setAnnouncer } = useAnnouncer()
  const enableSWR = import.meta.env.VITE_ENABLE_SWR === 'true'
  const id = path || currentPath

  // Setup page waiter promise
  let resolve
  let promise = new Promise(r => { resolve = r }) // eslint-disable-line promise/param-names

  // Setup reactive page object
  const page = reactive({
    __status: 'loading',
    isReady: false,
    isReadyPromise: () => promise
  })

  ;(async () => {
    // Check if cached page exists (used later for SWR)
    const isCached = hasPage(id)
    // Get page from cache or freshly fetch it
    const data = await getPage(id)

    if (!data) {
      page.__status = 'error'
      return
    }

    // Redirect to offline page if page hasn't been cached either in-memory or
    // by the service worker and the offline fallback JSON was returned
    // Note: data for `home` and `offline` pages are always available since they
    // are precached by the service worker
    if (!path && data.__isOffline === true) {
      router.replace({ path: '/offline' })
      page.__status = 'offline'
      return
    }

    // Append page data to reactive page object
    Object.assign(page, data)

    page.__status = 'success'
    page.isReady = true

    // Flush page waiter
    resolve && resolve()
    resolve = undefined
    promise = undefined

    // Further actions only if the hook was called for the current route
    if (!path) {
      // Set document title
      document.title = page.metaTitle

      // Announce new route
      setAnnouncer(`Navigated to ${page.title}`)
    }

    // Revalidate the stale asset asynchronously when SWR is enabled
    if (enableSWR && isCached) {
      const data = await getPage(id, { revalidate: true })

      if (data && data.__isOffline !== true) {
        Object.assign(page, data)
      }
    }
  })()

  return page
}

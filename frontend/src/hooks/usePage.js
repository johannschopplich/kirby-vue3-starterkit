import { reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useKirbyApi } from './useKirbyApi'
import { useAnnouncer } from './useAnnouncer'

/**
 * Transform a path to a Kirby-compatible page id
 *
 * @param {string} path Path to parse and transform
 * @returns {string} Corresponding page id
 */
const toPageId = path => {
  if (path.startsWith('/')) path = path.slice(1)
  if (path.endsWith('/')) path = path.slice(0, -1)
  return path || 'home'
}

/**
 * Hook for the page data of a given page id or the current route path
 *
 * @param {string} [path] Optional path or page id to retrieve
 * @returns {object} Reactive page object
 */
export const usePage = path => {
  const enableSWR = import.meta.env.VITE_ENABLE_SWR === 'true'
  const router = useRouter()
  const { path: currentPath } = useRoute()
  const { hasPage, getPage } = useKirbyApi()
  const { setAnnouncer } = useAnnouncer()
  const id = toPageId(path || currentPath)

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
    // Check if cached page exists (otherwise skip SWR)
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

    // Revalidate the stale asset asynchronously
    if (enableSWR && isCached && navigator.onLine) {
      const data = await getPage(id, { revalidate: true })
      if (data) Object.assign(page, data)
    }
  })()

  return page
}

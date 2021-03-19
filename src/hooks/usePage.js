import { reactive, readonly } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAnnouncer, useKirbyApi } from './'

/**
 * Returns page data by id or the current route path
 *
 * @param {string} [path] The ptional page id (path) to retrieve
 * @returns {reactive} The readonly reactive page object
 */
export default path => {
  const router = useRouter()
  const { path: currentPath } = useRoute()
  const { hasPage, getPage } = useKirbyApi()
  const { setAnnouncer } = useAnnouncer()

  // Build page id and trim leading or trailing slashes
  let id = (path ?? currentPath).replace(/^\/|\/$/g, '')

  // Fall back to homepage if id is empty
  if (!id) id = 'home'

  // Setup page waiter promise
  let resolve
  const promise = new Promise(r => { resolve = r }) // eslint-disable-line promise/param-names

  // Setup reactive page object
  const page = reactive({
    __status: 'pending',
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

    // Check data origin when the hook is used on the current route
    if (!path) {
      // Redirect to error page if data returned *is* the error page
      if (data.__isErrorPage && currentPath !== '/error') {
        router.replace({ path: '/error' })
        return
      }

      // Redirect to offline page if page hasn't been cached either in-store or
      // by the service worker and the offline fallback JSON was returned
      // Note: data for `home` and `offline` pages are always available since they
      // are precached by the service worker
      if (data.__isOffline) {
        router.replace({ path: '/offline' })
        return
      }
    }

    // Append page data to reactive page object
    Object.assign(page, data)

    page.__status = 'resolved'
    page.isReady = true
    resolve && resolve()

    // Further actions only if the hook was called for the current route
    if (!path) {
      // Set document title
      document.title = page.metaTitle

      // Announce new route
      setAnnouncer(`Navigated to ${page.title}`)
    }

    // Revalidate the stale asset asynchronously
    if (
      import.meta.env.VITE_STALE_WHILE_REVALIDATE === 'true' &&
      isCached &&
      navigator.onLine
    ) {
      const newData = await getPage(id, { revalidate: true })

      if (JSON.stringify(newData) !== JSON.stringify(data)) {
        Object.assign(page, newData)
      }

      page.__status = 'revalidated'
    }
  })()

  return readonly(page)
}

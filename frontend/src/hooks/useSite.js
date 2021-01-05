import { reactive, readonly } from 'vue'
import { useKirbyApi } from './'

const { apiLocation, fetcher } = useKirbyApi()

/**
 * Reactive object for the global `site` data
 *
 * @constant {object}
 */
const site = reactive({})

/**
 * Initialize global `site` object and store it
 */
export const initSite = async () => {
  const __DEV__ = import.meta.env.DEV
  let base = ''

  // Parse language from path for multi-language setups
  // in development environment
  if (import.meta.env.DEV && window.vite.multilang) {
    const lang = window.location.pathname.split('/')[1]
    if (lang) base = `/${lang}`
  }

  const data = __DEV__
    ? await fetcher(`${base}${apiLocation}/__site.json`)
    : JSON.parse(document.getElementById('site-data').textContent)

  Object.assign(site, data)
}

/**
 * Hook for the global `site` object
 *
 * @returns {object} Readonly `site` state
 */
export default () => readonly(site)

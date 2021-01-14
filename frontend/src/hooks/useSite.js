import { reactive, readonly } from 'vue'
import { useKirbyApi } from './'

const { apiUri, fetcher } = useKirbyApi()

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
  let languageCode = ''

  // Parse language from path for multi-language setups
  // in development environment
  if (__DEV__ && import.meta.env.VITE_MULTILANG === 'true') {
    languageCode = window.location.pathname.split('/')[1] || ''
  }

  const data = __DEV__
    ? await fetcher(apiUri('__site.json', languageCode))
    : JSON.parse(document.getElementById('site-data').textContent)

  Object.assign(site, data)
}

/**
 * Hook for the global `site` object
 *
 * @returns {object} Readonly `site` state
 */
export default () => readonly(site)

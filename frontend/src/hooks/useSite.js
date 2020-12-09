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
 * Initialize global `site` object and save it to the store
 */
export const initSite = async () => {
  const languageCode = window.location.pathname.split('/')[1]
  const data = import.meta.env.DEV
    ? await fetcher(`${apiLocation}/${languageCode ? `${languageCode}/` : ''}__site.json`)
    : JSON.parse(document.getElementById('site-data').textContent)

  Object.assign(site, data)
}

/**
 * Hook for the global `site` object
 *
 * @returns {object} Readonly `site` state
 */
export default () => readonly(site)

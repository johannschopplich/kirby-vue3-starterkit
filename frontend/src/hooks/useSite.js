import { reactive, readonly } from 'vue'
import { useKirbyApi } from './'

const { apiLocation, fetcher } = useKirbyApi()

/**
 * Reactive object for theglobal `site` data
 *
 * @constant {object}
 */
const site = reactive({})

/**
 * Initialize global `site` object and save it to the store
 */
export const initSite = async () => {
  const data = import.meta.env.DEV
    ? await fetcher(`${apiLocation}/__site.json`)
    : JSON.parse(document.getElementById('app').dataset.site)

  Object.assign(site, data)
}

/**
 * Hook for the global `site` object
 *
 * @returns {object} Readonly `site` state
 */
export const useSite = () => readonly(site)

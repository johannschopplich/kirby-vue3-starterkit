import { reactive, readonly } from 'vue'

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
  const data = JSON.parse(document.getElementById('site-data').textContent)
  Object.assign(site, data)
}

/**
 * Hook for the global `site` object
 *
 * @returns {object} Readonly `site` state
 */
export default () => readonly(site)

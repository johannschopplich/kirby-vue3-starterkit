import { reactive, readonly } from 'vue'

const data = JSON.parse(
  document.getElementById('site-data').textContent
)

/**
 * Reactive object for the global `site` data
 *
 * @constant {reactive}
 */
const site = reactive(data)

/**
 * Hook for the global `site` object
 *
 * @returns {object} Readonly `site` state
 */
export default () => readonly(site)

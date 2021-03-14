import { reactive, readonly } from 'vue'

const data = JSON.parse(
  document.getElementById('site-data').textContent
)

const site = reactive(data)

/**
 * Returns the gloval site object
 *
 * @returns {object} The readonly site object
 */
export default () => readonly(site)

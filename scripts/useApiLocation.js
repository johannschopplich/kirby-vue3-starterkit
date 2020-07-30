require('dotenv').config()

/**
 * Build the API location string
 *
 * @returns {string} The api location like `/pagesapi`
 */
function useApiLocation () {
  let apiLocation = process.env.KIRBY_API_LOCATION
  if (!apiLocation) return ''

  // Remove any slashes and add leading slash
  apiLocation = '/' + apiLocation.replace('/', '')

  if (apiLocation === '/api') {
    throw new Error('API location mustn\'t be the same as Kirby\'s internal API endpoint.')
  }

  return apiLocation
}

module.exports = {
  useApiLocation
}

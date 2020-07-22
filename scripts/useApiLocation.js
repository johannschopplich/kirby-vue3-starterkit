require('dotenv').config()

/**
 * Build the API location string
 *
 * @returns {string} The api location like `/pagesapi`
 */
function useApiLocation () {
  let apiLocation = process.env.KIRBY_API_LOCATION
  if (!apiLocation) return ''

  // Add leading slash if not given
  if (!apiLocation.startsWith('/')) {
    apiLocation = '/' + apiLocation
  }

  // Remove trailing slash if present
  if (apiLocation.endsWith('/')) {
    apiLocation = apiLocation.slice(0, -1)
  }

  if (apiLocation === '/api') {
    throw new Error('API location mustn\'t be the same as Kirby\'s internal API endpoint.')
  }

  return apiLocation
}

module.exports = {
  useApiLocation
}

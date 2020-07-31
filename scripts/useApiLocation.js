require('dotenv').config()

/**
 * Build the API location string
 *
 * @returns {string} The api location like `/pagesapi`
 */
function useApiLocation () {
  let apiLocation = (process.env.KIRBY_API_LOCATION || '').replace('/', '')

  // Add leading slash if location contains any characters
  if (apiLocation) {
    apiLocation = '/' + apiLocation
  }

  if (apiLocation === '/api') {
    throw new Error('API location mustn\'t be the same as Kirby\'s internal API endpoint.')
  }

  return apiLocation
}

module.exports = {
  useApiLocation
}

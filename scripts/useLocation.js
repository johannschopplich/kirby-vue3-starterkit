/**
 * Build a location path for the frontend from a given string
 *
 * @param {string} [location] An optional location string to parse
 * @returns {string} The parsed location
 */
function useLocation (location = '') {
  location = location.replace('/', '')

  // Add leading slash if location contains any characters
  if (location) {
    location = '/' + location
  }

  if (location === '/api') {
    throw new Error('API location mustn\'t be the same as Kirby\'s internal API endpoint.')
  }

  return location
}

module.exports = {
  useLocation
}

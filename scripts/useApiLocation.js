require('dotenv').config()

/**
 * Parse and transform an api path for use with fetch requests etc.
 *
 * @param {string} [location] Optional path to parse
 * @returns {string} The parsed location
 */
function useApiLocation (location = '') {
  // Add leading slash if missing
  if (!location.startsWith('/')) location = `/${location}`
  // Remove trailing slash if present
  if (location.endsWith('/')) location = location.slice(0, -1)

  const kirbyApiSlug = process.env.KIRBY_API_SLUG || 'api'
  if (location === `/${kirbyApiSlug}`) {
    throw new Error('API location mustn\'t be the same as Kirby\'s internal API endpoint.')
  }

  return location
}

module.exports = {
  useApiLocation
}

/**
 * Capizalize a string
 *
 * @param {string} string Character string to capitalize
 * @returns {string} Capitalized string
 */
export default ([first, ...rest]) => first.toUpperCase() + rest.join('')

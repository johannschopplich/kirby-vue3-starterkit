/**
 * Indicates a multi-language setup
 *
 * @constant {boolean}
 */
const isMultilang = import.meta.env.VITE_MULTILANG === 'true'

/**
 * Current language code
 *
 * @constant {string}
 */
const languageCode = document.documentElement.lang

if (import.meta.env.DEV && isMultilang) {
  console.log('[useLanguages] Current language code:', languageCode)
}

/**
 * Returns methods for multi-language setups
 *
 * @returns {object} Language related methods
 */
export default () => ({
  isMultilang,
  languageCode
})

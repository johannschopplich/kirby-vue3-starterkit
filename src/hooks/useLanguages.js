import { useSite } from './'

let isInitialized = false

/**
 * Current language code in multi-language setup
 *
 * @constant {(string|null)}
 */
let languageCode = null

/**
 * Initialize language code in multi-language setup
 */
const initLanguages = () => {
  const site = useSite()
  if (!site.languages?.length) return

  languageCode = document.documentElement.lang

  if (import.meta.env.DEV) {
    console.log('[useLanguages] Current language code:', languageCode)
  }
}

/**
 * Hook for handling languages
 *
 * @returns {object} Object language-related data
 */
export default () => {
  if (!isInitialized) {
    initLanguages()
    isInitialized = true
  }

  return {
    languageCode
  }
}

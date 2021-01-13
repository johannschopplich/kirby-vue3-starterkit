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
  const __DEV__ = import.meta.env.DEV
  const site = useSite()
  if (!site.languages?.length) return

  if (__DEV__) {
    const path = window.location.pathname
    document.documentElement.lang =
      site.languages.find(({ code }) => path === `/${code}` || path.startsWith(`/${code}/`))?.code ??
      site.languages.find(lang => lang.isDefault)?.code ??
      'en'
  }

  languageCode = document.documentElement.lang

  if (__DEV__) {
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

import { useSite } from './'

/**
 * Current language code in multi-language setup
 *
 * @constant {(string|null)}
 */
let languageCode = null

/**
 * Initialize language code in multi-language setup
 */
export const initLanguages = () => {
  const __DEV__ = import.meta.env.DEV
  const site = useSite()
  if (!site.languages?.length) return

  if (__DEV__) {
    const location = window.location.href
    document.documentElement.lang =
      site.languages.find(lang => location.endsWith(`/${lang.code}`) || location.includes(`/${lang.code}/`))?.code ??
      site.languages.find(lang => lang.isDefault)?.code ??
      'en'
  }

  languageCode = document.documentElement.lang
  if (__DEV__) console.log('[useLanguages] Current language code', languageCode)
}

/**
 * Hook for handling languages
 *
 * @returns {object} Object language-related data
 */
export default () => ({
  languageCode
})

import { useSite } from './'

/**
 * Current language code in multi-language setup
 *
 * @constant {(string|null)}
 */
let currentLang = null

/**
 * Initialize language code in multi-language setup
 */
export const initLanguages = () => {
  const site = useSite()
  if (!site.languages?.length) return

  if (import.meta.env.DEV) {
    document.documentElement.lang =
      site.languages.find(language => window.location.href.includes(`/${language.code}`))?.code ??
      site.languages.find(language => language.isDefault)?.code ??
      'en'
  }

  currentLang = document.documentElement.lang
}

/**
 * Hook for handling languages
 *
 * @returns {object} Object language-related data
 */
export default () => ({
  currentLang
})

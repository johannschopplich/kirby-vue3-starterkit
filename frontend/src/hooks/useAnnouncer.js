import { reactive } from 'vue'

const defaultOptions = {
  politeness: 'polite',
  complementRoute: 'has loaded'
}

const announcer = reactive({
  content: '',
  politeness: defaultOptions.politeness,
  complementRoute: defaultOptions.complementRoute
})

/**
 * Reset the announcer text content and politeness
 */
const resetAnnouncer = () => {
  announcer.content = ''
  announcer.politeness = defaultOptions.politeness
}

/**
 * Announce any useful information for screen readers
 *
 * @param {string} message The text content to announce
 * @param {string} politeness The degree of importance
 */
const setAnnouncer = (message, politeness = defaultOptions.politeness) => {
  resetAnnouncer()
  announcer.politeness = politeness
  announcer.content = message
}

/**
 * Hook for announce object and methods
 *
 * @returns {object} Object with announcer hook methods
 */
export const useAnnouncer = () => {
  return {
    announcer,
    resetAnnouncer,
    setAnnouncer
  }
}

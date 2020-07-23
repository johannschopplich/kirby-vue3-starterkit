import { reactive } from 'vue'

const defaultOptions = {
  politeness: 'polite'
}

const announcer = reactive({
  content: '',
  politeness: defaultOptions.politeness
})

/**
 * Announce any useful information for screen readers
 *
 * @param {string} message The content to announce
 * @param {string} politeness The degree of importance
 */
const setAnnouncer = (message, politeness = defaultOptions.politeness) => {
  resetAnnouncer()
  announcer.politeness = politeness
  announcer.content = message
}

/**
 * Announce the information politely
 *
 * @param {string} message The content to announce
 */
const announcePolite = message => {
  setAnnouncer(message, 'polite')
}

/**
 * Announce the information assertively
 *
 * @param {string} message The content to announce
 */
const announceAssertive = message => {
  setAnnouncer(message, 'assertive')
}

/**
 * Reset the announcer content and politeness
 */
const resetAnnouncer = () => {
  announcer.content = ''
  announcer.politeness = defaultOptions.politeness
}

/**
 * Hook for announce object and methods
 *
 * @returns {object} Object with announcer hook methods
 */
export const useAnnouncer = () => {
  return {
    announcer,
    setAnnouncer,
    announcePolite,
    announceAssertive,
    resetAnnouncer
  }
}

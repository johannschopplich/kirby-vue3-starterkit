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
 * @param {string} message Content to announce
 * @param {string} politeness Degree of importance
 */
const setAnnouncer = (message, politeness = defaultOptions.politeness) => {
  resetAnnouncer()
  announcer.politeness = politeness
  announcer.content = message
}

/**
 * Announce the information politely
 *
 * @param {string} message Content to announce
 */
const announcePolite = message => {
  setAnnouncer(message, 'polite')
}

/**
 * Announce the information assertively
 *
 * @param {string} message Content to announce
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
 * Hook for announce reactive object and methods
 *
 * @returns {object} Object containing announcer-related methods
 */
export default () => ({
  announcer,
  setAnnouncer,
  announcePolite,
  announceAssertive,
  resetAnnouncer
})

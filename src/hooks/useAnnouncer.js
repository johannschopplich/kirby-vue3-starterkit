import { reactive } from 'vue'

const defaultOptions = {
  politeness: 'polite'
}

const announcer = reactive({
  content: '',
  politeness: defaultOptions.politeness
})

/**
 * Announces any useful information for screen readers
 *
 * @param {string} message The content to announce
 * @param {string} [politeness] The degree of importance
 */
const setAnnouncer = (message, politeness = defaultOptions.politeness) => {
  resetAnnouncer()
  announcer.politeness = politeness
  announcer.content = message
}

/**
 * Announces the information politely
 *
 * @param {string} message The content to announce
 */
const announcePolite = message => {
  setAnnouncer(message, 'polite')
}

/**
 * Announces the information assertively
 *
 * @param {string} message The content to announce
 */
const announceAssertive = message => {
  setAnnouncer(message, 'assertive')
}

/**
 * Resets the announcer content and politeness
 */
const resetAnnouncer = () => {
  announcer.content = ''
  announcer.politeness = defaultOptions.politeness
}

/**
 * Returns common announcer methods
 *
 * @returns {object} Announcer related methods
 */
export default () => ({
  announcer,
  setAnnouncer,
  announcePolite,
  announceAssertive,
  resetAnnouncer
})

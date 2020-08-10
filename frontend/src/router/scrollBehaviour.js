/**
 * Handles scroll behaviour for Vue Router
 *
 * @param {object} to Route object of next page
 * @param {object} from Route object of previous page
 * @param {object} savedPosition Used by popstate navigations
 * @returns {(object|boolean)} Scroll position or `false`
 */
export const scrollBehavior = async function (to, from, savedPosition) {
  // Can be `auto` or `smooth`, defaults to no scroll behaviour
  const behavior = document.documentElement.style.scrollBehavior || 'auto'

  // `savedPosition` is only available for popstate navigations
  if (savedPosition) {
    return { ...savedPosition, behavior }
  }

  // Scroll to anchor by returning the selector
  if (to.hash) {
    return { el: decodeURI(to.hash), behavior }
  }

  // Check if any matched route config has meta that discourages scrolling to top
  if (to.matched.some(m => m.meta.scrollToTop === false)) {
    // Leave scroll as it is by not returning anything
    return false
  }

  // Always scroll to top
  return { left: 0, top: 0, behavior }
}

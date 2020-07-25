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

  if (savedPosition) {
    // `savedPosition` is only available for popstate navigations
    return { ...savedPosition, behavior }
  } else {
    // Scroll to anchor by returning the selector
    if (to.hash) {
      return { el: decodeURI(to.hash), behavior }
    }

    // Check if any matched route config has meta that requires scrolling to top
    if (to.matched.some(m => m.meta.scrollToTop)) {
      // Coordinates will be used if no selector is provided,
      // or if the selector didn't match any element
      return { left: 0, top: 0, behavior }
    }

    // Prevent scroll
    return false
  }
}

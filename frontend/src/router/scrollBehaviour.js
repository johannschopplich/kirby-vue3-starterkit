/**
 * Handles scroll behaviour
 *
 * @param {object} to Route object of next page
 * @param {object} from Route object of previous page
 * @param {object} savedPosition Used by popstate navigations
 * @returns {(object|boolean)} Scroll position or `false`
 */
export const scrollBehavior = async function (to, from, savedPosition) {
  // Defaults to no scroll behaviour; may be `auto` or `smooth`
  const behavior = document.documentElement.style.scrollBehavior || 'auto'

  if (savedPosition) {
    // `savedPosition` is only available for popstate navigations
    return { ...savedPosition, behavior }
  } else {
    let position

    // Scroll to anchor by returning the selector
    if (to.hash) {
      position = { el: decodeURI(to.hash), behavior }
      return position
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

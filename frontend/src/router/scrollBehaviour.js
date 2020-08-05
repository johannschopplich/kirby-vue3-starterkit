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

  // Check if any matched route config has meta that requires scrolling to top
  if (to.matched.some(m => m.meta.scrollToTop)) {
    // Coordinates will be used if no selector is provided,
    // or if the selector didn't match any element
    return { left: 0, top: 0, behavior }
  }

  // Either leave scroll as it is or …
  // return false

  // … always scroll to top
  return { left: 0, top: 0, behavior }
}

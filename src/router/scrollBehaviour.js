/**
 * Handles the scroll behavior on route navigation
 *
 * @type {import('vue-router').RouterScrollBehavior}
 */
export function scrollBehavior (to, from, savedPosition) {
  // Use predefined scroll behavior if defined, defaults to no scroll behavior
  const behavior = document.documentElement.style.scrollBehavior || 'auto'

  // Returning the `savedPosition` (if available) will result in a native-like
  // behavior when navigating with back/forward buttons
  if (savedPosition) {
    return { ...savedPosition, behavior }
  }

  // Scroll to anchor by returning the selector
  if (to.hash) {
    return { el: decodeURI(to.hash), behavior }
  }

  // Always scroll to top
  return { top: 0, behavior }
}

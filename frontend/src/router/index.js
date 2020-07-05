import { createRouter, createWebHistory } from 'vue-router'
import { scrollBehavior } from './scrollBehaviour'
import Default from '../views/Default.vue'

/**
 * Capizalizes a string
 *
 * @param {String} string Character string to capitalize
 * @returns {String}
 */
const capitalize = ([first, ...rest]) => first.toUpperCase() + rest.join('')

/**
 * Routes used by Vue Router
 * @const {Array}
 */
export const routes = []

/**
 * Vue Router history
 * @const {Array}
 */
export const history = createWebHistory()

/**
 * Build history array from site tree and initialize Vue Router
 *
 * @param {Object} site Global `site` object
 */
export const initRouter = async site => {
  // Published pages
  for (const page of site.children) {
    routes.push({
      path: `/${page.id}`,
      component: () => import(`../views/${capitalize(page.template)}.vue`).catch(() => Default),
      meta: {
        modified: page.modified,
        scrollToTop: true
      }
    })

    // Page children
    for (const child of page.children) {
      routes.push({
        path: `/${child.id}`,
        component: () => import(`../views/${capitalize(child.template)}.vue`).catch(() => Default),
        meta: {
          modified: child.modified,
          scrollToTop: true
        }
      })
    }
  }

  // Redirect `/home` to `/`
  routes.find(route => route.path === '/home').path = '/'
  routes.push({ path: '/home', redirect: '/' })

  // Catch-all fallback
  routes.push({ path: '/:catchAll(.*)', component: Default })

  return createRouter({
    history,
    scrollBehavior,
    routes
  })
}

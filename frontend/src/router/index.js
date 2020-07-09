import { createRouter, createWebHistory } from 'vue-router'
import { scrollBehavior } from './scrollBehaviour'
import Default from '../views/Default.vue'

/**
 * Capizalizes a string
 *
 * @param {string} string Character string to capitalize
 * @returns {string} Capitalized string
 */
const capitalize = ([first, ...rest]) => first.toUpperCase() + rest.join('')

/**
 * Routes filled by site tree for use by Vue Router
 *
 * @constant {Array}
 */
export const routes = []

/**
 * Vue Router history
 *
 * @constant {Array}
 */
export const history = createWebHistory()

/**
 * Creates the Vue Router instance
 *
 * @param {object} site Global `site` object
 * @returns {object} Output of `createRouter`
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

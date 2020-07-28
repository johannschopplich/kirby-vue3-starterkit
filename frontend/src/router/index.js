import { createRouter, createWebHistory } from 'vue-router'
import { scrollBehavior } from './scrollBehaviour'
import { capitalize } from '../helpers'
// TODO: Use again once Vite fixes a bug with dynamic imports
// import Default from '../views/Default.vue'

/**
 * The routes record
 *
 * @constant {Array}
 */
export const routes = []

/**
 * The router history
 *
 * @constant {Array}
 */
export const history = createWebHistory()

/**
 * The Router instance
 *
 * @constant {Function|null}
 */
export let router = null

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
      component: () => import(`../views/${capitalize(page.template)}.vue`).catch(() => /* Default */ import('../views/Default.vue')),
      meta: {
        modified: page.modified,
        scrollToTop: true
      }
    })

    // Page children
    for (const child of page.children) {
      routes.push({
        path: `/${child.id}`,
        component: () => import(`../views/${capitalize(child.template)}.vue`).catch(() => /* Default */ import('../views/Default.vue')),
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
  routes.push({ path: '/:catchAll(.*)', component: /* Default */ () => import('../views/Default.vue') })

  router = createRouter({
    history,
    scrollBehavior,
    routes
  })

  return router
}

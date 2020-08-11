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
export let routes = []

/**
 * The router history
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
  // Published pages routes
  routes = site.children.flatMap(page => [
    {
      path: `/${page.id}`,
      component: () => import(`../views/${capitalize(page.template)}.vue`).catch(() => /* Default */ import('../views/Default.vue'))
    },
    // Page children routes
    ...page.children.map(child => ({
      path: `/${child.id}`,
      component: () => import(`../views/${capitalize(child.template)}.vue`).catch(() => /* Default */ import('../views/Default.vue'))
    }))
  ])

  // Redirect `/home` to `/`
  routes.find(route => route.path === '/home').path = '/'
  routes.push({ path: '/home', redirect: '/' })

  // Catch-all fallback
  routes.push({ path: '/:catchAll(.*)', component: /* Default */ () => import('../views/Default.vue') })

  return createRouter({
    history,
    routes,
    scrollBehavior
  })
}

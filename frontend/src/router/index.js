import { createRouter, createWebHistory } from 'vue-router'
import { scrollBehavior } from './scrollBehaviour'
import { useSite } from '../hooks/useSite'
import { capitalize } from '../helpers'
import Default from '../views/Default.vue'

/**
 * Creates the Vue Router instance
 *
 * @returns {object} Created router instance for the Vue app
 */
export const initRouter = () => {
  const site = useSite()

  // Published pages routes
  const routes = site.children.flatMap(page => [
    {
      path: `/${page.id}`,
      component: () => import(`../views/${capitalize(page.template)}.vue`).catch(() => Default)
    },
    // Page children routes
    ...page.children.map(child => ({
      path: `/${child.id}`,
      component: () => import(`../views/${capitalize(child.template)}.vue`).catch(() => Default)
    }))
  ])

  // Redirect `/home` to `/`
  routes.find(route => route.path === '/home').path = '/'
  routes.push({ path: '/home', redirect: '/' })

  // Catch-all fallback
  routes.push({ path: '/:catchAll(.*)', component: Default })

  return createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior
  })
}

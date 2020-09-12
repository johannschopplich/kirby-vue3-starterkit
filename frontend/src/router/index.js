import { createRouter, createWebHistory } from 'vue-router'
import { scrollBehavior } from './scrollBehaviour'
import { useSite } from '../hooks/useSite'
import { capitalize } from '../helpers'

/**
 * Creates the Vue Router instance
 *
 * @returns {object} Created router instance for the Vue app
 */
export const initRouter = () => {
  const site = useSite()
  const routes = []

  for (const page of site.children) {
    routes.push({
      path: `/${page.id}`,
      component: () => import(`../views/${capitalize(page.template)}.vue`).catch(() => import('../views/Default.vue'))
    })

    if (page.hasChildren) {
      routes.push({
        path: `/${page.id}/:id`,
        component: () => import(`../views/${capitalize(page.childTemplate)}.vue`).catch(() => import('../views/Default.vue'))
      })
    }
  }

  // Redirect `/home` to `/`
  routes.find(route => route.path === '/home').path = '/'
  routes.push({ path: '/home', redirect: '/' })

  // Catch-all fallback
  routes.push({ path: '/:catchAll(.*)', component: () => import('../views/Default.vue') })

  return createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior
  })
}

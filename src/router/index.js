import { createRouter, createWebHistory } from 'vue-router'
import { scrollBehavior } from './scrollBehaviour'
import { useSite, useLanguages } from '../hooks'
import { capitalize } from '../helpers'
import Default from '../views/Default.vue'

/**
 * Creates the Vue Router instance
 *
 * @returns {object} Created router instance for the Vue app
 */
export const initRouter = () => {
  const site = useSite()
  const { languageCode } = useLanguages()
  const base = languageCode ? `/${languageCode}/` : ''

  const routes = [
    ...site.children.map(page => ({
      path: `/${page.uri}`,
      component: () => import(`../views/${capitalize(page.template)}.vue`).catch(() => Default)
    })),
    ...site.children
      .filter(page => page.childTemplate)
      .map(page => ({
        path: `/${page.uri}/:id`,
        component: () => import(`../views/${capitalize(page.childTemplate)}.vue`).catch(() => Default)
      }))
  ]

  // Redirect `/home` to `/`
  routes.find(({ path }) => path === '/home').path = '/'
  routes.push({ path: '/home', redirect: '/' })

  // Catch-all fallback
  routes.push({ path: '/:pathMatch(.*)*', component: Default })

  return createRouter({
    history: createWebHistory(base),
    routes,
    scrollBehavior
  })
}

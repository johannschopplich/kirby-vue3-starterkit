import { createRouter, createWebHistory } from 'vue-router'
import { scrollBehavior } from './scrollBehaviour'
import Default from '../views/Default.vue'

const capitalize = ([first, ...rest]) => first.toUpperCase() + rest.join('')

export const history = createWebHistory()

export const initRouter = async site => {
  const routes = []

  // Published pages
  for (const page of site.children) {
    routes.push({
      path: `/${page.id}`,
      component: () => import(`../views/${capitalize(page.template)}.vue`).catch(() => Default),
      meta: {
        scrollToTop: true
      }
    })

    for (const child of page.children) {
      routes.push({
        path: `/${child.id}`,
        component: () => import(`../views/${capitalize(child.template)}.vue`).catch(() => Default),
        meta: {
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

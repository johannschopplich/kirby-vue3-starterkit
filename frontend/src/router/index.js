import { createRouter, createWebHistory } from 'vue-router'
import Default from '../views/Default.vue'

const capitalize = ([first, ...rest]) => first.toUpperCase() + rest.join('')

const scrollBehavior = async function (to, from, savedPosition) {
  // Defaults to no scroll behaviour; other value: `smooth`
  const behavior = document.documentElement.style.scrollBehavior || 'auto'

  if (savedPosition) {
    // SavedPosition is only available for popstate navigations
    return { ...savedPosition, behavior }
  } else {
    let position

    // Scroll to anchor by returning the selector
    if (to.hash) {
      position = { el: decodeURI(to.hash), behavior }
      return position
    }

    // Check if any matched route config has meta that requires scrolling to top
    if (to.matched.some(m => m.meta.scrollToTop)) {
      // Coordinates will be used if no selector is provided,
      // or if the selector didn't match any element
      return { left: 0, top: 0, behavior }
    }

    // Prevent scroll
    return false
  }
}

export const history = createWebHistory()

export const initRouter = async site => {
  const routes = []

  // Published pages
  for (const page of site.children) {
    routes.push({
      path: `/${page.id}`,
      component: () => import(`../views/${capitalize(page.template)}.vue`).catch(() => Default)
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

import { createRouter, createWebHistory } from "vue-router";
import { useSite, useLanguages } from "~/hooks";
import Default from "~/views/Default.vue";

const capitalize = ([first, ...rest]) => first.toUpperCase() + rest.join("");

/** @type {import('vue-router').RouterScrollBehavior} */
const scrollBehavior = (to, from, savedPosition) => {
  // Use predefined scroll behavior if defined, defaults to no scroll behavior
  const behavior = document.documentElement.style.scrollBehavior || "auto";

  // Returning the `savedPosition` (if available) will result in a native-like
  // behavior when navigating with back/forward buttons
  if (savedPosition) {
    return { ...savedPosition, behavior };
  }

  // Scroll to anchor by returning the selector
  if (to.hash) {
    return { el: decodeURI(to.hash), behavior };
  }

  // Always scroll to top
  return { top: 0, behavior };
};

export const install = ({ app }) => {
  const site = useSite();
  const { isMultilang, languageCode } = useLanguages();
  const base = isMultilang ? `/${languageCode}/` : "";

  /** @type {import('vue-router').RouteRecordRaw[]} */
  const routes = [
    ...site.children.map((page) => ({
      path: `/${page.uri}`,
      component: () =>
        import(`../views/${capitalize(page.template)}.vue`).catch(
          () => Default
        ),
    })),
    ...site.children
      .filter((page) => page.childTemplate)
      .map((page) => ({
        path: `/${page.uri}/:id`,
        component: () =>
          import(`../views/${capitalize(page.childTemplate)}.vue`).catch(
            () => Default
          ),
      })),
  ];

  // Redirect `/home` to `/`
  routes.find(({ path }) => path === "/home").path = "/";
  routes.push({ path: "/home", redirect: "/" });

  // Catch-all fallback
  routes.push({ path: "/:pathMatch(.*)*", component: Default });

  /** @type {import('vue-router').Router} */
  const router = createRouter({
    history: createWebHistory(base),
    routes,
    scrollBehavior,
  });

  app.use(router);
};

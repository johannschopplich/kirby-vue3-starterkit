let router;
const origin = import.meta.env.DEV
  ? import.meta.env.VITE_BACKEND_URL
  : window.location.origin;

/**
 * Handle absolute links inside dynamically added HTML with Vue Router
 *
 * @param {Event} event The event taking place in the DOM
 */
function navigate(event) {
  const link = event.target.closest("a");

  if (
    link &&
    link.href.startsWith(origin) &&
    link.target !== "_blank" &&
    !event.defaultPrevented &&
    event.button === 0 &&
    !event.metaKey &&
    !event.altKey &&
    !event.ctrlKey &&
    !event.shiftKey
  ) {
    event.preventDefault();
    router.push({ path: link.href.slice(origin.length) });
  }
}

/** @param {import("vue").App} app The Vue app instance */
export const install = (app) => {
  app.directive("kirbytext", {
    mounted(el, binding) {
      router = binding.instance.$router;
      el.addEventListener("click", navigate);
    },

    beforeUnmount(el) {
      el.removeEventListener("click", navigate);
    },
  });
};

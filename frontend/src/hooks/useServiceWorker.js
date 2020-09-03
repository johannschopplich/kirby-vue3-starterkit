import { ref } from 'vue'

/**
 * The new service worker waiting to be installed (if any)
 */
let newWorker

/**
 * Reactive boolean indicating if a service worker update is available
 */
const hasNewWorker = ref(false)

/**
 * Register the default service worker when `VITE_ENABLE_SW` is enabled,
 * otherwise unregister active service workers
 */
const handleRegistration = async () => {
  if (process.env.NODE_ENV === 'development') return
  if (!('serviceWorker' in navigator)) return

  const enableWorker = import.meta.env.VITE_ENABLE_SW === 'true'
  const hasExistingWorker = !!navigator.serviceWorker.controller

  if (enableWorker) {
    try {
      const registration = await navigator.serviceWorker.register('/service-worker.js')

      // Handle service worker updates
      // @see https://developers.google.com/web/fundamentals/primers/service-workers/lifecycle#handling_updates
      registration.addEventListener('updatefound', () => {
        newWorker = registration.installing // The installing worker

        // Handle state changes of new service worker
        newWorker.addEventListener('statechange', () => {
          // Make sure new service worker installation is complete
          if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
            // Notify the user that a new service worker is ready to be installed
            hasNewWorker.value = true
          }
        })
      })
    } catch (error) {
      console.log('Failed to register service worker:', error)
    }

    // Wait for the current service worker controlling this page to change,
    // specifically when the new worker has skipped waiting and become
    // the new active worker
    let isRefreshing
    navigator.serviceWorker.addEventListener('controllerchange', () => {
      if (isRefreshing) return
      window.location.reload()
      isRefreshing = true
    })

    if (hasExistingWorker) {
      navigator.serviceWorker.controller.postMessage({ command: 'trimCaches' })
    }
  } else if (hasExistingWorker) {
    // Unregister existing service workers
    for (const reg of await navigator.serviceWorker.getRegistrations()) {
      await reg.unregister()
    }
  }
}

/**
 * Activate the new service worker (like after an update notification)
 */
const activateNewWorker = () => {
  if (!newWorker) return
  newWorker.postMessage({ command: 'skipWaiting' })
}

/**
 * Hook for handling service worker registrations and updates
 *
 * @returns {object} Object containing service worker-related methods
 */
export const useServiceWorker = () => {
  return {
    handleRegistration,
    newWorker,
    hasNewWorker,
    activateNewWorker
  }
}

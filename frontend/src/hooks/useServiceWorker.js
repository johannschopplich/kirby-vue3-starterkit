import { ref } from 'vue'

/**
 * Indicates if a service worker is installed
 */
const hasExistingWorker = !!navigator.serviceWorker.controller

/**
 * Reactive boolean indicating if a service worker update is available
 */
const hasNewWorker = ref(false)

/**
 * The new service worker waiting to be installed (if any)
 */
let newWorker

/**
 * Register a service worker
 *
 * @param {string} [swUrl] Absolute URL for the worker to register
 * @param {object} [hooks] Object of hooks for registration events
 */
const register = async (swUrl = '/service-worker.js', hooks = {}) => {
  const { registrationOptions = {} } = hooks
  delete hooks.registrationOptions

  const emit = (hook, ...args) => {
    if (hooks && hooks[hook]) {
      hooks[hook](...args)
    }
  }

  try {
    const registration = await navigator.serviceWorker.register(swUrl, registrationOptions)
    emit('registered', registration)

    if (registration.waiting) {
      emit('updated', registration)
      newWorker = registration.waiting
      hasNewWorker.value = true
    } else {
      // Handle service worker updates
      // @see https://developers.google.com/web/fundamentals/primers/service-workers/lifecycle#handling_updates
      registration.addEventListener('updatefound', () => {
        emit('updatefound', registration)
        newWorker = registration.installing // The installing worker

        // Handle state changes of new service worker
        newWorker.addEventListener('statechange', () => {
          // Make sure new service worker installation is complete
          if (newWorker.state !== 'installed') return

          if (navigator.serviceWorker.controller) {
            // At this point, the old content will have been purged and
            // the fresh content will have been added to the cache
            // Perfect time to notify the user that a new service worker
            // is ready to be installed
            emit('updated', registration)
            hasNewWorker.value = true
          } else {
            // At this point, everything has been precached
            // Perfect time to notify the user that content is cached
            // for offline use
            emit('cached', registration)
          }
        })
      })
    }
  } catch (error) {
    if (!navigator.onLine) emit('offline')
    emit('error', error)
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

  navigator.serviceWorker.ready.then(registration => {
    emit('ready', registration)
  })
}

// Thanks to Evan You for this pattern
/*
register('/service-worker.js', {
  registrationOptions: { scope: './' },
  ready (registration) {
    console.log('Service worker is active.')
  },
  registered (registration) {
    console.log('Service worker has been registered.')
  },
  cached (registration) {
    console.log('Content has been cached for offline use.')
  },
  updatefound (registration) {
    console.log('New content is downloading.')
  },
  updated (registration) {
    console.log('New content is available; please refresh.')
  },
  offline () {
    console.log('No internet connection found. App is running in offline mode.')
  },
  error (error) {
    console.error('Error during service worker registration:', error)
  }
})
*/

/**
 * Unregister existing service workers
 */
const unregister = async () => {
  if (hasExistingWorker) {
    for (const reg of await navigator.serviceWorker.getRegistrations()) {
      await reg.unregister()
    }
  }
}

/**
 * Activate the new service worker (like after an update notification)
 */
const activateNewWorker = () => {
  if (newWorker) {
    newWorker.postMessage({ command: 'skipWaiting' })
  }
}

/**
 * Handle service worker registrations
 */
const initSw = async () => {
  if (process.env.NODE_ENV === 'development') return
  if (!('serviceWorker' in navigator)) return

  const enableWorker = import.meta.env.VITE_ENABLE_SW === 'true'

  if (enableWorker) {
    await register()
    if (hasExistingWorker) {
      navigator.serviceWorker.controller.postMessage({ command: 'trimCaches' })
    }
  } else {
    unregister()
  }
}

/**
 * Hook for handling service worker registrations and updates
 *
 * @returns {object} Object containing service worker-related methods
 */
export const useServiceWorker = () => ({
  hasExistingWorker,
  register,
  unregister,
  hasNewWorker,
  newWorker,
  activateNewWorker,
  initSw
})

import { ref } from 'vue'

/**
 * Reactive boolean indicating if a service worker update is available
 */
const hasNewWorker = ref(false)

/**
 * The new service worker waiting to be installed (if any)
 */
let newWorker

/**
 * A promise resolving when the document and all sub-resources have
 * finished loading
 */
const documentLoaded = new Promise(resolve => {
  if (document.readyState === 'complete') {
    resolve()
  } else {
    window.addEventListener('load', resolve)
  }
})

/**
 * Registers a service worker
 *
 * @param {string} [swUrl="/service-worker.js"] Absolute URL for the worker to register
 * @param {object} [hooks={}] Object of hooks for registration events
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

    // Handle service worker updates
    // @see https://developers.google.com/web/fundamentals/primers/service-workers/lifecycle#handling_updates
    if (registration.waiting) {
      newWorker = registration.waiting
      emit('updated', registration)
    } else {
      registration.addEventListener('updatefound', () => {
        emit('updatefound', registration)
        const installingWorker = registration.installing

        // Handle state changes of new service worker
        installingWorker.addEventListener('statechange', () => {
          // Make sure new service worker installation is complete
          if (installingWorker.state !== 'installed') return

          if (navigator.serviceWorker.controller) {
            // At this point, the old content will have been purged and
            // the fresh content will have been added to the cache
            // Perfect time to notify the user that a new service worker
            // is ready to be installed
            newWorker = registration.waiting
            emit('updated', registration)
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

  navigator.serviceWorker.ready.then(registration => {
    emit('ready', registration)
  })
}

/**
 * Unregisters existing service workers
 */
const unregister = async () => {
  const registration = await navigator.serviceWorker.ready
  registration.unregister()
}

/**
 * Activates the new service worker (like after an update notification)
 */
const activateNewWorker = () => {
  if (!newWorker) return
  newWorker.postMessage({ command: 'skipWaiting' })
}

/**
 * Handles the service worker registration process
 */
const initSw = async () => {
  if (!('serviceWorker' in navigator)) return

  const enableWorker = import.meta.env.VITE_SERVICE_WORKER === 'true'
  const hasExistingWorker = !!navigator.serviceWorker.controller

  if (enableWorker) {
    await documentLoaded
    await register('/service-worker.js', {
      // Thanks to Evan You for this pattern
      // @see https://github.com/yyx990803/register-service-worker

      // registrationOptions: { scope: './' },
      ready (registration) {
        if (import.meta.env.DEV) console.log('Service worker is active.')
      },
      registered (registration) {
        if (import.meta.env.DEV) console.log('Service worker has been registered.')
      },
      cached (registration) {
        if (import.meta.env.DEV) console.log('Content has been cached for offline use.')
      },
      updatefound (registration) {
        if (import.meta.env.DEV) console.log('New content is downloading.')
      },
      updated (registration) {
        if (import.meta.env.DEV) console.log('New content is available; please refresh.')
        hasNewWorker.value = true
      },
      offline () {
        if (import.meta.env.DEV) console.log('No internet connection found. App is running in offline mode.')
      },
      error (error) {
        console.error('Error during service worker registration:', error)
      }
    })

    // Wait for the current service worker controlling this page to change,
    // specifically when the new worker has skipped waiting and become
    // the new active worker
    let isRefreshing
    navigator.serviceWorker.addEventListener('controllerchange', () => {
      if (isRefreshing) return
      isRefreshing = true
      window.location.reload()
    })

    if (hasExistingWorker) {
      navigator.serviceWorker.controller.postMessage({ command: 'trimCaches' })
    }
  } else if (hasExistingWorker) {
    unregister()
  }
}

/**
 * Returns methods for handling service worker registrations and updates
 *
 * @returns {object} Service worker related methods
 */
export default () => ({
  register,
  unregister,
  hasNewWorker,
  newWorker,
  activateNewWorker,
  initSw
})

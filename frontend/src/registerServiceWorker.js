if (process.env.NODE_ENV !== 'development') {
  ;(async () => {
    if (!('serviceWorker' in navigator)) return

    // `VITE_ENABLE_SW` returns a string, but we need a boolean
    const enableSw = import.meta.env.VITE_ENABLE_SW === 'true'
    const hasExistingSw = !!navigator.serviceWorker.controller

    let newWorker
    /**
     * Notify the user that a new app version is available to install
     */
    function showUpdateNotification () {
      const element = document.querySelector('#update-notification')
      if (!element) return

      element.classList.add('show')

      // Activate the new service worker as soon as the user interacts
      element.addEventListener('click', () => {
        newWorker.postMessage({ command: 'skipWaiting' })
      })
    }

    if (enableSw) {
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
              showUpdateNotification()
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

      if (hasExistingSw) {
        navigator.serviceWorker.controller.postMessage({ command: 'trimCaches' })
      }
    } else if (hasExistingSw) {
      for (const reg of await navigator.serviceWorker.getRegistrations()) {
        await reg.unregister()
      }
    }
  })()
}

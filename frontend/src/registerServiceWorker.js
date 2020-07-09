if (process.env.NODE_ENV !== 'development') {
  ;(async () => {
    if (!('serviceWorker' in navigator)) return

    // `VITE_ENABLE_SW` returns a string, but we need a boolean
    const enableSw = JSON.parse(import.meta.env.VITE_ENABLE_SW || false)
    const hasExistingSw = !!navigator.serviceWorker.controller

    if (enableSw) {
      try {
        navigator.serviceWorker.register('/service-worker.js')
      } catch (error) {
        console.log('Failed to register service worker:', error)
      }

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

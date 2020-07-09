if (process.env.NODE_ENV !== 'development') {
  ;(async () => {
    if (!('serviceWorker' in navigator)) return

    const hasExistingSw = !!navigator.serviceWorker.controller

    if (import.meta.env.VITE_ENABLE_SW) {
      try {
        navigator.serviceWorker.register('/service-worker.js')
        console.log('Init service worker:')
      } catch (err) {
        console.log('Failed to register service worker:', err)
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

import './main.css'

import { createApp } from 'vue'
import { initRouter } from './router'
import { useKirbyApi } from './hooks/useKirbyApi'
import { useServiceWorker } from './hooks/useServiceWorker'
import KirbyTextDirective from './plugins/KirbyTextDirective'
import App from './App.vue'

const enableWorker = import.meta.env.VITE_ENABLE_SW === 'true'
const { initSite } = useKirbyApi()
const { hasExistingWorker, register, unregister } = useServiceWorker()

;(async () => {
  await initSite()
  const router = initRouter()
  const app = createApp(App)

  app.use(router)
  app.use(KirbyTextDirective)
  app.mount('#app')

  if (enableWorker) {
    await register()
    if (hasExistingWorker) {
      navigator.serviceWorker.controller.postMessage({ command: 'trimCaches' })
    }
  } else {
    unregister()
  }
})()

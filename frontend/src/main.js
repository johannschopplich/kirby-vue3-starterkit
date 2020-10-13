import './main.css'

import { createApp } from 'vue'
import { initRouter } from './router'
import { useKirbyApi } from './hooks/useKirbyApi'
import { useServiceWorker } from './hooks/useServiceWorker'
import KirbyTextDirective from './plugins/KirbyTextDirective'
import App from './App.vue'

const { initSite } = useKirbyApi()
const { initSw } = useServiceWorker()

;(async () => {
  // Router relies on children tree of `site` object
  await initSite()

  const router = initRouter()
  const app = createApp(App)

  app.use(router)
  app.use(KirbyTextDirective)
  app.mount('#app')

  initSw()
})()

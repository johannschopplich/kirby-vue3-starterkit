import './main.css'

import { createApp } from 'vue'
import { initRouter } from './router'
import { initSite, useServiceWorker } from './hooks'
import KirbyTextDirective from './plugins/KirbyTextDirective'
import App from './App.vue'

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

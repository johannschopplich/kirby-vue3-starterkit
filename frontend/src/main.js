import './main.css'

import { createApp } from 'vue'
import { initRouter } from './router'
import { initSite, initLanguages, useServiceWorker } from './hooks'
import KirbyTextDirective from './plugins/KirbyTextDirective'
import App from './App.vue'

const { initSw } = useServiceWorker()

;(async () => {
  // Router relies on children tree of `site` object
  await initSite()
  // Only actived in multi-language setups
  initLanguages()

  const router = initRouter()
  const app = createApp(App)

  app.use(router)
  app.use(KirbyTextDirective)
  app.mount('#app')

  initSw()
})()

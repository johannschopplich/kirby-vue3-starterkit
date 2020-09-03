import './main.css'

import { createApp } from 'vue'
import { initRouter } from './router'
import { useKirbyApi } from './hooks/useKirbyApi'
import { useServiceWorker } from './hooks/useServiceWorker'
import KirbyTextDirective from './plugins/KirbyTextDirective'
import App from './App.vue'

const { initSite } = useKirbyApi()
const { handleRegistration } = useServiceWorker()

;(async () => {
  await initSite()
  const router = initRouter()
  const app = createApp(App)

  app.use(KirbyTextDirective)
  app.use(router)
  await router.isReady()
  app.mount('#app')

  handleRegistration()
})()

import './main.css'

import { createApp } from 'vue'
import { initRouter } from './router'
import { useKirbyApi } from './hooks/useKirbyApi'
import { useServiceWorker } from './hooks/useServiceWorker'
import App from './App.vue'

const { getPage } = useKirbyApi()
const { register } = useServiceWorker()
register()

;(async () => {
  const home = await getPage('home')
  const router = await initRouter(home.site)

  const app = createApp(App)
  app.use(router)
  await router.isReady()
  app.mount('#app')
})()

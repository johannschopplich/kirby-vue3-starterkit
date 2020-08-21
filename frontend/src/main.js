import './main.css'

import { createApp } from 'vue'
import { initRouter } from './router'
import { useKirbyApi } from './hooks/useKirbyApi'
import { useServiceWorker } from './hooks/useServiceWorker'
import App from './App.vue'

const { getPage } = useKirbyApi()
const { register } = useServiceWorker()
window.addEventListener('load', register)

;(async () => {
  // Fetch `home` page which includes the global `site` object
  await getPage('home')
  const router = initRouter()

  const app = createApp(App)
  app.use(router)
  await router.isReady()
  app.mount('#app')
})()

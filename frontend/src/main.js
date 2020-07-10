import { createApp } from 'vue'
import { initRouter } from './router'
import { useApi } from './hooks/useApi'
import App from './App.vue'
import './registerServiceWorker'

;(async () => {
  const { getPage } = useApi()
  const home = await getPage('home', { force: true })
  const router = await initRouter(home.site)

  const app = createApp(App)
  app.use(router)
  await router.isReady()
  app.mount('#app')
})()

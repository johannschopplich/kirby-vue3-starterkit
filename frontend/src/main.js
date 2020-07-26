import { createApp } from 'vue'
import { initRouter } from './router'
import { useApi } from './hooks/useApi'
import { useServiceWorker } from './hooks/useServiceWorker'
import App from './App.vue'

const { register } = useServiceWorker()
register()

;(async () => {
  const { getPage } = useApi()
  const home = await getPage('home', { force: true })
  const router = await initRouter(home.site)

  const app = createApp(App)
  app.use(router)
  await router.isReady()
  app.mount('#app')
})()

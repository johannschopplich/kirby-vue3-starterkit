import { createApp } from 'vue'
import { initRouter } from './router'
import { useKirbyAPI } from './hooks/useKirbyApi'
import App from './App.vue'

;(async () => {
  const { getPage } = useKirbyAPI()
  const home = await getPage('home', { force: true })
  const router = await initRouter(home.site)

  const app = createApp(App)
  app.use(router)
  await router.isReady()
  app.mount('#app')
})()

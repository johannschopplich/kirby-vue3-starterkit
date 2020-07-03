import { createApp } from 'vue'
import { initRouter } from './router'
import { useKirbyAPI } from './hooks/kirby-api'
import App from './App.vue'

;(async () => {
  const { getPage } = useKirbyAPI()
  const home = await getPage('home')
  const router = await initRouter(home.site)

  window.$site = home.site

  const app = createApp(App)
  app.use(router)
  await router.isReady()
  app.mount('#app')
})()

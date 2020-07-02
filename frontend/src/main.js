import { createApp } from 'vue'
import App from './App.vue'
import Router from './router'
import { useKirbyAPI } from './hooks/kirby-api'

;(async () => {
  const { getPage } = useKirbyAPI()
  const home = await getPage('home')
  const router = await Router.init(home.site)

  window.$home = home
  window.$site = home.site

  const app = createApp(App)
  app.use(router)
  await router.isReady()
  app.mount('#app')
})()

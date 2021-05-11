import './index.css'

import { createApp } from 'vue'
import { initRouter } from './router'
import { useServiceWorker } from './hooks'
import KirbyTextDirective from './plugins/KirbyTextDirective'
import App from './App.vue'

const { initSw } = useServiceWorker()
const router = initRouter()
const app = createApp(App)

app.use(router)
app.use(KirbyTextDirective)
app.mount('#app')

initSw()

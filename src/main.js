import { createApp } from "vue";
import { useServiceWorker } from "./hooks";
import App from "./App.vue";
import "./styles/main.css";

const app = createApp(App);

for (const m of Object.values(import.meta.globEager("./modules/*.js"))) {
  m.install?.(app);
}

app.mount("#app");

const { initSw } = useServiceWorker();
initSw();

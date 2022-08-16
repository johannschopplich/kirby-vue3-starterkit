import { createApp } from "vue";
import App from "./App.vue";
import "./styles/main.css";

const app = createApp(App);

for (const m of Object.values(
  import.meta.glob("./modules/*.js", { eager: true })
)) {
  m.install?.(app);
}

app.mount("#app");

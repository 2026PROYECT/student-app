import "./bootstrap";
import { createApp } from "vue";
import router from "./routes/index";
import VueSweetalert2 from "vue-sweetalert2";
import App from "./App.vue"; // <-- use your App.vue as root
// resources/js/app.js
import '../css/tailwind.css';

createApp(App)
  .use(router)
  .use(VueSweetalert2)
  .mount("#app");


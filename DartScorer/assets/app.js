import { createApp } from 'vue';
import App from './user-app/index.vue';
import { VueSpinnersPlugin } from 'vue3-spinners';

const app = createApp(App);
app.use(VueSpinnersPlugin);
import {createApp} from "vue";
import App from './game-app/index.vue';
import PlayerCardComponent from './game-app/player-card.vue';
import NumberpadComponent from './game-app/numberpad.vue';
import CalculateCheckouts from './game-app/calculate-checkouts.vue';
import LegShutModalComponent from './game-app/leg-shut-modal.vue';

const app = createApp(App);

app.component('PlayerCardComponent', PlayerCardComponent);
app.component('NumberpadComponent', NumberpadComponent);
app.component('CalculateCheckouts', CalculateCheckouts);
app.component('LegShutModalComponent', LegShutModalComponent);

app.mount('#game-app');
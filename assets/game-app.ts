import {createApp} from "vue";
import App from './game-app/index.vue';
import PlayerCardComponent from './game-app/player-card.vue';
import PlayerCardTabletComponent from './game-app/player-card-tablet.vue';
import NumberpadComponent from './game-app/numberpad.vue';
import NumberpadTabletComponent from './game-app/numberpad-tablet.vue';
import CalculateCheckouts from './game-app/calculate-checkouts.vue';
import LegShutModalComponent from './game-app/modal/leg-shut.vue';
import GameShutModalComponent from './game-app/modal/game-shut.vue';

const app = createApp(App);

app.component('PlayerCardComponent', PlayerCardComponent);
app.component('PlayerCardTabletComponent', PlayerCardTabletComponent);
app.component('NumberpadComponent', NumberpadComponent);
app.component('NumberpadTabletComponent', NumberpadTabletComponent);
app.component('CalculateCheckouts', CalculateCheckouts);
app.component('LegShutModalComponent', LegShutModalComponent);
app.component('GameShutModalComponent', GameShutModalComponent);

app.mount('#game-app');
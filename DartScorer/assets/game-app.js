import {createApp} from "vue";
import App from './game-app/index.vue';
import GameHeaderComponent from './game-app/game-header.vue';
import Player1CardComponent from './game-app/player1-card.vue';
import Player2CardComponent from './game-app/player2-card.vue';
import NumberpadComponent from './game-app/numberpad.vue';
import CalculateCheckouts from './game-app/calculate-checkouts.vue';

const app = createApp(App);

app.component('GameHeaderComponent', GameHeaderComponent);
app.component('Player1CardComponent', Player1CardComponent);
app.component('Player2CardComponent', Player2CardComponent);
app.component('NumberpadComponent', NumberpadComponent);
app.component('CalculateCheckouts', CalculateCheckouts);

app.mount('#game-app');

<template>

    <GameHeaderComponent v-if="game" :game="game"/>

    <div class="row px-1">
      <div class="col p-1">
        <Player1CardComponent v-if="game" :game="game"/>
      </div>
      <div class="col p-1">
        <Player2CardComponent v-if="game" :game="game"/>
      </div>
    </div>

    <NumberpadComponent />

</template>

<script>
import GameHeaderComponent from './game-header.vue';
import Player1CardComponent from './player1-card.vue';
import Player2CardComponent from './player2-card.vue';
import NumberpadComponent from './numberpad.vue';
import axios from 'axios';

export default {
  name: 'GameComponent',
  components: {
    GameHeaderComponent,
    Player1CardComponent,
    Player2CardComponent,
    NumberpadComponent
  },
  data() {
    return {
      game: null,
      loading: true,
      error: false
    };
  },
  mounted() {
    this.gameId = this.getGameIdFromUrl();

    if (this.gameId) {
      this.getGameData();
    } else {
      console.error('Game ID not found in the URL');
      this.error = true;
      this.loading = false;
    }
  },
  methods: {
    getGameIdFromUrl() {
      const pathSegments = window.location.pathname.split('/');
      const lastSegment = pathSegments[pathSegments.length - 1];
      return /^\d+$/.test(lastSegment) ? parseInt(lastSegment) : null;
    },
    getGameData() {
      axios.get('/api/game/' + this.gameId)
          .then(response => {
            this.game = response.data;
            this.loading = false;
          })
          .catch(error => {
            console.error('Error fetching game data:', error);
            this.error = true;
            this.loading = false;
          });
    }
  }
}
</script>

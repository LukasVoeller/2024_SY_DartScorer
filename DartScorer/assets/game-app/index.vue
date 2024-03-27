<template>
  <!--
      <GameHeaderComponent v-if="game" :game="game"/>
  -->
  <div class="row px-1">
    <div class="col p-1 d-flex">
      <Player1CardComponent v-if="game" :game="game" :score="player1Score" :toThrow="player1ToThrow"
                            :dartsThrown="player1DartsThrown"/>
    </div>
    <div class="col p-1 d-flex">
      <Player2CardComponent v-if="game" :game="game" :score="player2Score" :toThrow="player2ToThrow"
                            :dartsThrown="player2DartsThrown"/>
    </div>
  </div>

  <NumberpadComponent v-if="game" @score-entered="processScore"/>

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
      error: false,

      player1Score: 0,
      player2Score: 0,
      player1Scores: [],
      player2Scores: [],
      player1ToThrow: false,
      player1DartsThrown: 0,
      player2DartsThrown: 0,
      player2ToThrow: false,
      scoreInput: 0
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
    // Game Logic
    processScore(score) {
      // Filter out leading zeros
      score = parseInt(score.replace(/^0+/, ''), 10);
      if (isNaN(score)) {
        score = 0;
      }

      if (this.player1ToThrow) {
        this.player1Score -= score;
        this.player1Scores.push(score);
        this.player1ToThrow = false;
        this.player2ToThrow = true;
        this.player1DartsThrown += 3;
      } else if (this.player2ToThrow) {
        this.player2Score -= score;
        this.player2Scores.push(score);
        this.player2ToThrow = false;
        this.player1ToThrow = true;
        this.player2DartsThrown += 3;
      }

      console.log("Entered Score: " + score);
      console.log("Player1Scores: " + this.player1Scores);
      console.log("Player2Scores: " + this.player2Scores);
    },

    // Persistence
    getGameIdFromUrl() {
      const pathSegments = window.location.pathname.split('/');
      const lastSegment = pathSegments[pathSegments.length - 1];
      return /^\d+$/.test(lastSegment) ? parseInt(lastSegment) : null;
    },

    getGameData() {
      axios.get('/api/game/' + this.gameId)
          .then(response => {
            this.game = response.data;
            this.player1Score = this.game.startScore;
            this.player2Score = this.game.startScore;

            if (this.game.player1.id === this.game.playerStarting.id) {
              this.player1ToThrow = true;
            } else if (this.game.player2.id === this.game.playerStarting.id) {
              this.player2ToThrow = true;
            }

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

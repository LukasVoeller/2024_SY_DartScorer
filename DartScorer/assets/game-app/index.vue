<template>
  <!--
      <GameHeaderComponent v-if="game" :game="game"/>
  -->
  <div class="row px-1">
    <div class="col p-1">
      <Player1CardComponent v-if="game" :game="game" :score="player1Score" :toThrow="player1ToThrow"
                            :dartsThrown="player1CurrentDartsThrown" :sets="player1Sets" :legs="player1Legs"/>
    </div>
    <div class="col p-1">
      <Player2CardComponent v-if="game" :game="game" :score="player2Score" :toThrow="player2ToThrow"
                            :dartsThrown="player2CurrentDartsThrown"/>
    </div>
  </div>

  <NumberpadComponent v-if="game" :game="game" @score-entered="processScore" @score-cleared="clearScore" @score-confirmed="confirmScore"/>

  <LegShutModalComponent />

</template>

<script>
import GameHeaderComponent from './game-header.vue';
import Player1CardComponent from './player1-card.vue';
import Player2CardComponent from './player2-card.vue';
import NumberpadComponent from './numberpad.vue';
import LegShutModalComponent from './leg-shut-modal.vue';
import axios from 'axios';
import { EventBus } from '../event-bus';

export default {
  name: 'GameComponent',
  components: {
    GameHeaderComponent,
    Player1CardComponent,
    Player2CardComponent,
    NumberpadComponent,
    LegShutModalComponent,
  },
  data() {
    return {
      game: null,
      loading: true,
      error: false,

      player1StartScore: 0,
      player2StartScore: 0,
      player1TempScore: 0,
      player2TempScore: 0,
      player1Score: 0,
      player2Score: 0,
      player1CurrentScores: [],
      player2CurrentScores: [],
      player1TotalScores: [],
      player2TotalScores: [],
      player1ToThrow: false,
      player2ToThrow: false,
      player1CurrentDartsThrown: 0,
      player2CurrentDartsThrown: 0,
      player1DartsPerLegThrown: [],
      player2DartsPerLegThrown: [],
      player1Sets: 0,
      player2Sets: 0,
      player1Legs: 0,
      player2LEgs: 0,
      //scoreInput: 0,
      score: 0,
    };
  },

  created() {
    EventBus.on('modal-confirmed', (dartsForCheckout) => {
      const dartsThrown = parseInt(dartsForCheckout);

      this.player1Legs += 1;

      if (this.game.matchMode === "First to Sets") {
        if (this.game.matchModeLegs === this.player1Legs) {
          alert("Set shut by " + this.game.player1.name);
          this.player1Legs = 0;
          this.player1Sets += 1;

          if (this.game.matchModeSets === this.player1Sets) {
            alert("Game shut and the match by " + this.game.player1.name);
          }
        }
      }

      this.player1CurrentScores.push(this.score);
      this.player1TotalScores.push(this.player1CurrentScores.slice());
      this.player1CurrentDartsThrown += dartsThrown;
      this.player1DartsPerLegThrown.push(this.player1CurrentDartsThrown);
      this.player1ToThrow = false;
      this.player2ToThrow = true;

      this.logInfo(this.score);

      this.resetScores();
    });
  },

  mounted() {
    // Initialize modal when component is mounted
    //const myModal = new bootstrap.Modal(document.getElementById('exampleModal'));
    //onMounted(() => myModal.show());

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
    // Game Logic ######################################################################################################
    clearScore(score) {
      score = parseInt(score.replace(/^0+/, ''), 10);
      if (isNaN(score)) {
        score = 0;
      }

      if (this.player1ToThrow) {
        this.player1Score = this.player1TempScore;
      } else if (this.player2ToThrow) {
        this.player2Score = this.player2TempScore;
      }
    },

    processScore(score) {
      score = parseInt(score.replace(/^0+/, ''), 10);
      if (isNaN(score)) {
        score = 0;
      }

      if (this.player1ToThrow) {
        this.player1Score = this.player1TempScore - score;
      } else if (this.player2ToThrow) {
        this.player2Score = this.player2TempScore - score;
      }
    },

    confirmScore(score) {
      score = parseInt(score.replace(/^0+/, ''), 10);
      if (isNaN(score)) {
        score = 0;
      }

      this.score = score;

      if (this.player1ToThrow) {
        if (this.player1Score === 0) {
          EventBus.emit('show-leg-shut-modal');
        } else {
          this.player1TempScore = this.player1Score;
          this.player1CurrentScores.push(score);
          this.player1ToThrow = false;
          this.player2ToThrow = true;
          this.player1CurrentDartsThrown += 3;
        }
      } else if (this.player2ToThrow) {
        if (this.player2Score === 0) {
          EventBus.emit('show-leg-shut-modal');
        }else {
          this.player2TempScore = this.player2Score;
          this.player2TotalScores.push(score);
          this.player2ToThrow = false;
          this.player1ToThrow = true;
          this.player2CurrentDartsThrown += 3;
        }
      }
    },

    resetScores() {
      this.player1CurrentScores = [];
      this.player1Score = this.player1StartScore;
      this.player1TempScore = this.player1StartScore;
      this.player1CurrentDartsThrown = 0;
      this.player2CurrentScores = [];
      this.player2Score = this.player2StartScore;
      this.player2TempScore = this.player2StartScore;
      this.player2CurrentDartsThrown = 0;
    },

    logInfo(score){
      console.log(" ");
      console.log("--- Entered Score: " + score + " ---");
      console.log("# Player1CurrentScores: " + this.player1CurrentScores);
      for (let i = 0; i < this.player1TotalScores.length; i++) {
        let legName = "Leg " + (i + 1);
        let legScores = this.player1TotalScores[i].join(", ");
        console.log(legName + ": " + legScores);
      }
      console.log("player1CurrentDartsThrown: " + this.player1CurrentDartsThrown);
      console.log("player1DartsPerLegThrown: " + this.player1DartsPerLegThrown);
      console.log("matchModeSets: " + this.game.matchModeSets + " matchModeLegs: " + this.game.matchModeLegs );
      console.log("player1Sets: " + this.player1Sets + " player1Legs: " + this.player1Legs );
    },





    // Persistence #####################################################################################################
    getGameIdFromUrl() {
      const pathSegments = window.location.pathname.split('/');
      const lastSegment = pathSegments[pathSegments.length - 1];
      return /^\d+$/.test(lastSegment) ? parseInt(lastSegment) : null;
    },

    getGameData() {
      axios.get('/api/game/' + this.gameId)
          .then(response => {
            this.game = response.data;
            this.player1StartScore = this.game.startScore;
            this.player2StartScore = this.game.startScore;
            this.player1TempScore = this.game.startScore;
            this.player2TempScore = this.game.startScore;
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

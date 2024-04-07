<template>
  <!--
      <GameHeaderComponent v-if="game" :game="game"/>
  -->
  <div class="row px-1">
    <div class="col p-1">
      <Player1CardComponent v-if="game" :game="game" :score="player1.score" :toThrow="player1.toThrow"
                            :dartsThrown="player1.currentDartsThrown" :sets="player1.sets" :legs="player1.tempLegs"/>
    </div>
    <div class="col p-1">
      <Player2CardComponent v-if="game" :game="game" :score="player2.score" :toThrow="player2.toThrow"
                            :dartsThrown="player2.currentDartsThrown" :sets="player2.sets" :legs="player2.tempLegs"/>
    </div>
  </div>

  <NumberpadComponent v-if="game" :game="game" @score-entered="processScore" @score-cleared="clearScore" @score-confirmed="confirmScore"/>

  <LegShutModalComponent />

</template>

<script>
/*
 #   TODO:
 # - If I start and check, Player2 is to throw first
 # - Don't allow input of impossible numbers blow 180
 # - Reset score after resume on "How many darts needed?"
 */
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

      player1: {
        name: 'player1',
        startScore: 0,
        tempScore: 0,
        score: 0,
        currentScores: [],
        totalScores: [],
        toThrow: false,
        currentDartsThrown: 0,
        dartsPerLegThrown: [],
        sets: 0,
        legs: 0,
        tempLegs: 0,
      },

      player2: {
        name: 'player2',
        startScore: 0,
        tempScore: 0,
        score: 0,
        currentScores: [],
        totalScores: [],
        toThrow: false,
        currentDartsThrown: 0,
        dartsPerLegThrown: [],
        sets: 0,
        legs: 0,
        tempLegs: 0,
      },

      //scoreInput: 0,
      score: 0,
    };
  },

  created() {
    EventBus.on('modal-confirmed', (dartsForCheckout) => {
      const numberOfDarts = parseInt(dartsForCheckout);

      if (this.player1.toThrow) {
        this.processPlayerCheckout(this.player1, numberOfDarts);
      } else if (this.player2.toThrow) {
        this.processPlayerCheckout(this.player2, numberOfDarts);
      }
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

      if (this.player1.toThrow) {
        this.player1.score = this.player1.tempScore;
      } else if (this.player2.toThrow) {
        this.player2.score = this.player2.tempScore;
      }
    },

    processScore(score) {
      score = parseInt(score.replace(/^0+/, ''), 10);
      if (isNaN(score)) {
        score = 0;
      }

      if (this.player1.toThrow) {
        this.player1.score = this.player1.tempScore - score;
      } else if (this.player2.toThrow) {
        this.player2.score = this.player2.tempScore - score;
      }
    },

    confirmScore(score) {
      score = parseInt(score.replace(/^0+/, ''), 10);
      if (isNaN(score)) {
        score = 0;
      }

      this.score = score;

      if (this.player1.toThrow) {
        if (this.player1.score === 0) {
          EventBus.emit('show-leg-shut-modal');
        } else {
          this.player1.tempScore = this.player1.score;
          this.player1.currentScores.push(score);
          this.player1.toThrow = false;
          this.player2.toThrow = true;
          this.player1.currentDartsThrown += 3;
        }
      } else if (this.player2.toThrow) {
        if (this.player2.score === 0) {
          EventBus.emit('show-leg-shut-modal');
        }else {
          this.player2.tempScore = this.player2.score;
          this.player2.totalScores.push(score);
          this.player2.toThrow = false;
          this.player1.toThrow = true;
          this.player2.currentDartsThrown += 3;
        }
      }
    },

    processPlayerCheckout(player, dartsThrown){
      console.log("Leg shut");

      player.tempLegs += 1;

      if (this.game.matchMode === "First to Sets") {
        if (this.game.matchModeLegs === player.tempLegs) {
          console.log("Set shut");
          player.tempLegs = 0;
          player.sets += 1;

          if (this.game.matchModeSets === player.sets) {
            console.log("Game shut");
          }
        }
      }

      player.currentScores.push(this.score);
      player.totalScores.push(player.currentScores.slice());
      player.currentDartsThrown += dartsThrown;
      player.dartsPerLegThrown.push(player.currentDartsThrown);

      if (player.name === 'player2'){
        this.player1.toThrow = false;
        this.player2.toThrow = true;
      } else if (player.name === 'player2') {
        this.player1.toThrow = true;
        this.player2.toThrow = false;
      }

      this.logInfo(player, this.score);

      this.resetScores();
    },

    resetScores() {
      this.player1.currentScores = [];
      this.player1.score = this.player1.startScore;
      this.player1.tempScore = this.player1.startScore;
      this.player1.currentDartsThrown = 0;
      this.player2.currentScores = [];
      this.player2.score = this.player2.startScore;
      this.player2.tempScore = this.player2.startScore;
      this.player2.currentDartsThrown = 0;
    },

    logInfo(player, score){
      console.log(" ");
      console.log("--- Entered Score: " + score + " ---");
      console.log(player.name + " CurrentScores: " + player.currentScores);

      /*
      for (let i = 0; i < player.totalScores.length; i++) {
        let legName = "Leg " + (i + 1);
        let legScores = player.totalScores[i].join(", ");
        console.log(legName + ": " + legScores);
      }
       */
      console.log(player.name + " TotalScores: " + player.totalScores);

      console.log(player.name + " CurrentDartsThrown: " + player.currentDartsThrown);
      console.log(player.name + " DartsPerLegThrown: " + player.dartsPerLegThrown);
      console.log("MatchModeSets: " + this.game.matchModeSets + " MatchModeLegs: " + this.game.matchModeLegs );
      console.log(player.name + " Sets: " + player.sets + " " + player.name + " Legs: " + player.tempLegs );
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
            this.player1.startScore = this.game.startScore;
            this.player2.startScore = this.game.startScore;
            this.player1.tempScore = this.game.startScore;
            this.player2.tempScore = this.game.startScore;
            this.player1.score = this.game.startScore;
            this.player2.score = this.game.startScore;

            if (this.game.player1.id === this.game.playerStarting.id) {
              this.player1.toThrow = true;
            } else if (this.game.player2.id === this.game.playerStarting.id) {
              this.player2.toThrow = true;
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

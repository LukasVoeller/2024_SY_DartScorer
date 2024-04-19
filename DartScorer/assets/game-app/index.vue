<template>
  <!--
      <GameHeaderComponent v-if="game" :game="game"/>
  -->
  <div class="row px-1">
    <div class="col p-1">
      <Player1CardComponent v-if="game" :player1Name="player1.name" :score="player1.score" :toThrow="player1.toThrow"
                            :dartsThrown="calculateDartsThrownSum(player1)" :sets="player1.sets" :legs="player1.tempLegs"/>
    </div>
    <div class="col p-1">
      <Player2CardComponent v-if="game" :player2Name="player2.name" :score="player2.score" :toThrow="player2.toThrow"
                            :dartsThrown="calculateDartsThrownSum(player2)" :sets="player2.sets" :legs="player2.tempLegs"/>
    </div>
  </div>

  <NumberpadComponent v-if="game" @score-entered="processScore" @score-cleared="clearScore" @score-confirmed="confirmScore" @score-undo="undoScore"/>

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
        id: 0,
        name: 'player1',
        startScore: 0,
        tempScore: 0,
        score: 0,
        currentScores: [],
        totalScores: [],
        toThrow: false,
        currentDartsThrown: [],
        totalDartsThrown: [],
        sets: 0,
        legs: 0,
        tempLegs: 0,
      },

      player2: {
        id: 0,
        name: 'player2',
        startScore: 0,
        tempScore: 0,
        score: 0,
        currentScores: [],
        totalScores: [],
        toThrow: false,
        currentDartsThrown: [],
        totalDartsThrown: [],
        sets: 0,
        legs: 0,
        tempLegs: 0,
      },

      score: 0,
      legCounter: 0,
      gameState: "",
      playerStartsLegId: null
    };
  },

  created() {
    EventBus.on('modal-confirmed', (dartsForCheckout) => {
      const numberOfDarts = parseInt(dartsForCheckout);

      if (this.player1.toThrow) {
        this.processPlayerCheckout(this.player1);

        this.player1.currentDartsThrown.push(numberOfDarts);
        this.player1.totalScores.push(this.player1.currentScores);
        this.player1.totalDartsThrown.push(this.player1.currentDartsThrown);
        this.player2.totalScores.push(this.player2.currentScores);
        this.player2.totalDartsThrown.push(this.player2.currentDartsThrown);

        this.logTotalInfo();
      } else if (this.player2.toThrow) {
        this.processPlayerCheckout(this.player2);

        this.player2.currentDartsThrown.push(numberOfDarts);
        this.player2.totalScores.push(this.player2.currentScores);
        this.player2.totalDartsThrown.push(this.player2.currentDartsThrown);
        this.player1.totalScores.push(this.player1.currentScores);
        this.player1.totalDartsThrown.push(this.player1.currentDartsThrown);

        this.logTotalInfo();
      }

      if (this.gameState !== "Finished"){
        this.resetScores();
        this.switchToThrow();
      }
    });

    EventBus.on('modal-resumed', (dartsForCheckout) => {
      //
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
    calculateDartsThrownSum(player) {
      return player.currentDartsThrown.reduce((acc, curr) => acc + curr, 0);
    },

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
          // Checkout!
          this.player1.currentScores.push(score);
          EventBus.emit('show-leg-shut-modal');
        } else {
          this.player1.tempScore = this.player1.score;
          this.player1.currentScores.push(score);
          this.player1.toThrow = false;
          this.player2.toThrow = true;
          this.player1.currentDartsThrown.push(3);
        }
      } else if (this.player2.toThrow) {
        if (this.player2.score === 0) {
          // Checkout!
          EventBus.emit('show-leg-shut-modal');
        } else {
          this.player2.tempScore = this.player2.score;
          this.player2.currentScores.push(score);
          this.player2.toThrow = false;
          this.player1.toThrow = true;
          this.player2.currentDartsThrown.push(3);
        }
      }

      this.logCurrentInfo(score);
    },

    undoScore() {
      if (this.player1.toThrow) {
        if (this.player2.currentScores[this.player2.currentScores.length - 1]) {
          this.player2.score += this.player2.currentScores[this.player2.currentScores.length - 1];
          this.player2.currentScores.pop();
          this.player2.currentDartsThrown.pop();
        } else {
          this.player2.score = this.player1.startScore;
        }

        this.player1.toThrow = false;
        this.player2.toThrow = true;
      } else if (this.player2.toThrow) {
        if (this.player1.currentScores[this.player1.currentScores.length - 1]) {
          this.player1.score += this.player1.currentScores[this.player1.currentScores.length - 1];
          this.player1.currentScores.pop();
          this.player1.currentDartsThrown.pop();
        }
        else {
          this.player2.score = this.player1.startScore;
        }

        this.player1.toThrow = true;
        this.player2.toThrow = false;
      }
    },

    processPlayerCheckout(player){
      console.log("Leg shut");
      player.tempLegs += 1;
      this.legCounter += 1;
      //console.log("his.game:", this.game);

      if (this.game.matchMode === "FirstToSets") {
        if (this.game.matchModeLegsNeeded === player.tempLegs) {
          console.log("Set shut");
          player.tempLegs = 0;
          player.sets += 1;

          if (this.game.matchModeSetsNeeded === player.sets) {
            console.log("Game shut");
            this.gameState = "Finished";
          }
        }
      }
    },

    resetScores() {
      this.player1.currentScores = [];
      this.player1.score = this.player1.startScore;
      this.player1.tempScore = this.player1.startScore;
      this.player1.currentDartsThrown = [];
      this.player2.currentScores = [];
      this.player2.score = this.player2.startScore;
      this.player2.tempScore = this.player2.startScore;
      this.player2.currentDartsThrown = [];
    },

    switchToThrow() {
      if (this.playerStartsLegId === this.player1.id) {
        this.playerStartsLegId = this.player2.id
        this.player1.toThrow = false;
        this.player2.toThrow = true;
      } else if (this.playerStartsLegId === this.player2.id) {
        this.playerStartsLegId = this.player1.id
        this.player1.toThrow = true;
        this.player2.toThrow = false;
      }
    },

    logCurrentInfo (score) {
      console.log(" ");
      console.log("----- Entered Score: " + score + " -----");
      console.log(this.player1.name + " CurrentScores: " + this.player1.currentScores);
      console.log(this.player2.name + " CurrentScores: " + this.player2.currentScores);

      console.log(this.player1.name + " CurrentDartsThrown: " + this.player1.currentDartsThrown);
      console.log(this.player2.name + " CurrentDartsThrown: " + this.player2.currentDartsThrown);
    },

    logTotalInfo () {
      console.log(" ");
      console.log("----- Total -----");

      console.log(this.player1.name + " TotalScores: " + JSON.stringify(this.player1.totalScores));
      console.log(this.player2.name + " TotalScores: " + JSON.stringify(this.player2.totalScores));

      console.log(this.player1.name + " TotalDartsThrown: " + JSON.stringify(this.player1.totalDartsThrown));
      console.log(this.player2.name + " TotalDartsThrown: " + JSON.stringify(this.player2.totalDartsThrown));
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
            this.player1.name = this.game.player1Name;
            this.player1.id = this.game.player1Id;
            this.player2.name = this.game.player2Name;
            this.player2.id = this.game.player2Id;
            this.playerStartsLegId = this.game.playerStartingId;
            this.gameState = this.game.state;

            if (this.player1.id === this.game.playerStartingId) {
              this.player1.toThrow = true;
              this.player2.toThrow = false;
            } else if (this.player2.id === this.game.playerStartingId) {
              this.player1.toThrow = false;
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

<template>
  <div class="row px-1">
    <div class="col p-1" style="max-width: 50%;">
      <PlayerCardComponent v-if="game" :playerName="player1.name" :score="player1.score" :toThrow="player1.toThrow" :lastThrow="player1.currentScores.join(', ')"
                            :dartsThrown="calculateDartsThrownSum(player1)" :sets="player1.sets" :legs="player1.tempLegs"/>
    </div>
    <div class="col p-1" style="max-width: 50%;">
      <PlayerCardComponent v-if="game" :playerName="player2.name" :score="player2.score" :toThrow="player2.toThrow" :lastThrow="player2.currentScores.join(', ')"
                            :dartsThrown="calculateDartsThrownSum(player2)" :sets="player2.sets" :legs="player2.tempLegs"/>
    </div>
  </div>

  <NumberpadComponent v-if="game" @score-entered="processScore" @score-cleared="clearScore" @score-confirmed="confirmScore" @score-undo="undoScore"/>

  <LegShutModalComponent />

  <Caller />

</template>

<script>
/*
 #   TODO:
 # - If I start and check, Player2 is to throw first
 # - Don't allow input of impossible numbers blow 180
 # - Reset score after resume on "How many darts needed?"
 */
import PlayerCardComponent from './player-card.vue';
import NumberpadComponent from './numberpad.vue';
import LegShutModalComponent from './leg-shut-modal.vue';
import Caller from './caller.vue';
import axios from 'axios';
import { EventBus } from '../event-bus';

export default {
  name: 'GameComponent',
  components: {
    PlayerCardComponent,
    NumberpadComponent,
    LegShutModalComponent,
    Caller
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
      gameState: "",
      playerStartsLegId: null
    };
  },

  created() {
    this.onModalConfirmed = (dartsForCheckout) => {
      const numberOfDarts = parseInt(dartsForCheckout);

      if (this.player1.toThrow) {
        this.processPlayerCheckout(this.player1);

        this.player1.currentDartsThrown.unshift(numberOfDarts);
        this.player1.totalScores.unshift(this.player1.currentScores);
        this.player1.totalDartsThrown.unshift(this.player1.currentDartsThrown);
        this.player2.totalScores.unshift(this.player2.currentScores);
        this.player2.totalDartsThrown.unshift(this.player2.currentDartsThrown);

        this.logTotalInfo();
      } else if (this.player2.toThrow) {
        this.processPlayerCheckout(this.player2);

        this.player2.currentDartsThrown.unshift(numberOfDarts);
        this.player2.totalScores.unshift(this.player2.currentScores);
        this.player2.totalDartsThrown.unshift(this.player2.currentDartsThrown);
        this.player1.totalScores.unshift(this.player1.currentScores);
        this.player1.totalDartsThrown.unshift(this.player1.currentDartsThrown);

        this.logTotalInfo();
      }

      if (this.gameState !== "Finished"){
        this.resetScores();
        this.switchToThrow();
      }
    };

    this.onModalResumed = (dartsForCheckout) => {
      if (this.player1.toThrow) {
        this.player1.score += this.player1.currentScores[0];
        this.player1.currentScores.shift();
      } else if (this.player2.toThrow) {
        this.player2.score += this.player2.currentScores[0];
        this.player2.currentScores.shift();
      }
    };

    EventBus.on('modal-confirmed', this.onModalConfirmed);
    EventBus.on('modal-resumed', this.onModalResumed);
  },

  beforeDestroy() {
    // Remove event listeners to avoid memory leaks
    EventBus.off('modal-confirmed', this.onModalConfirmed);
    EventBus.off('modal-resumed', this.onModalResumed);
  },

  mounted() {
    // Initialize modal when component is mounted
    //const myModal = new bootstrap.Modal(document.getElementById('exampleModal'));
    //onMounted(() => myModal.show());

    this.gameId = this.getGameIdFromUrl();

    if (this.gameId) {
      this.getGameData();
      //console.log("Game loaded!");
      //EventBus.emit('play-gameOn-sound');
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
        //console.log("Player Score: " + this.player1.score + " Temp Score:  " + this.player1.tempScore + " Score: " + score)
        this.player1.score = this.player1.tempScore - score;
      } else if (this.player2.toThrow) {
        this.player2.score = this.player2.tempScore - score;
      }
    },

    // TODO: Don't add the score if score is bust
    // Impossible scores:   179, 178, 176, 175, 173, 172, 169, 166, 163, 162, ...
    // Possible scores:     180, 177, 174, 171, 170, 168, 167, 165, 164, 161, ...
    confirmScore(score) {
      score = parseInt(score.replace(/^0+/, ''), 10);
      if (isNaN(score)) {
        score = 0;
      }

      EventBus.emit('play-score-sound', score);

      this.score = score;

      if (this.player1.toThrow) {
        if (this.player1.score === 0) {
          // Checkout!
          this.player1.currentScores.unshift(score);
          //EventBus.emit('play-gameShut-sound');
          EventBus.emit('show-leg-shut-modal');
        } else {
          this.player1.tempScore = this.player1.score;
          this.player1.currentScores.unshift(score);
          this.player1.toThrow = false;
          this.player2.toThrow = true;
          this.player1.currentDartsThrown.unshift(3);
        }
      } else if (this.player2.toThrow) {
        if (this.player2.score === 0) {
          // Checkout!
          this.player2.currentScores.unshift(score);
          //EventBus.emit('play-gameShut-sound');
          EventBus.emit('show-leg-shut-modal');
        } else {
          this.player2.tempScore = this.player2.score;
          this.player2.currentScores.unshift(score);
          this.player2.toThrow = false;
          this.player1.toThrow = true;
          this.player2.currentDartsThrown.unshift(3);
        }
      }

      this.logCurrentInfo(score);
    },

    undoScore(enteredScore) {
      if (enteredScore) {
        this.clearScore(enteredScore);
      }

      if (this.player1.toThrow) {
        if (this.player2.currentScores.length > 0) {
          this.player2.score += this.player2.currentScores[0];
          this.player2.tempScore += this.player2.currentScores[0];
          this.player2.currentScores.shift();
          this.player2.currentDartsThrown.shift();
          this.player1.toThrow = false;
          this.player2.toThrow = true;
        } else {
          this.player2.score = this.player1.startScore;
        }
      } else if (this.player2.toThrow) {
        if (this.player1.currentScores.length > 0) {
          this.player1.score += this.player1.currentScores[0];
          this.player1.tempScore += this.player1.currentScores[0];
          this.player1.currentScores.shift();
          this.player1.currentDartsThrown.shift();
          this.player1.toThrow = true;
          this.player2.toThrow = false;
        }
        else {
          this.player2.score = this.player1.startScore;
        }
      }
    },

    processPlayerCheckout(player){
      if (this.game.matchMode === "FirstToSets") {
        console.log("Leg shut");
        player.tempLegs += 1;

        if (this.game.matchModeLegsNeeded === player.tempLegs) {
          console.log("Set shut");
          player.tempLegs = 0;
          player.sets += 1;

          if (this.game.matchModeSetsNeeded === player.sets) {
            console.log("Game shut");
            EventBus.emit('play-gameShutAndTheMatch-sound');
            this.gameState = "Finished";
          }
        }
      } else if (this.game.matchMode === "FirstToLegs") {
        console.log("Leg shut");
        player.tempLegs += 1;

        if (this.game.matchModeLegsNeeded === player.tempLegs) {
          console.log("Game shut");
          EventBus.emit('play-gameShutAndTheMatch-sound');
          this.gameState = "Finished";
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
  },
}
</script>

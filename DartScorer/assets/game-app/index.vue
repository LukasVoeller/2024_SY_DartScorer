<template>
  <div class="row px-1">
    <div class="col p-1" style="max-width: 50%;">
      <PlayerCardComponent v-if="game" :playerName="player1.name" :playerScore="player1.score" :toThrow="player1.toThrow" :lastThrows="player1.currentScores.join(', ')"
                            :dartsThrown="calculateDartsThrownSum(player1)" :sets="player1.sets" :legs="player1.tempLegs" :legAverage="player1LegAverage" :gameAverage="player1.gameAverage" :scoreBusted="player1.scoreBusted"/>
    </div>
    <div class="col p-1" style="max-width: 50%;">
      <PlayerCardComponent v-if="game" :playerName="player2.name" :playerScore="player2.score" :toThrow="player2.toThrow" :lastThrows="player2.currentScores.join(', ')"
                            :dartsThrown="calculateDartsThrownSum(player2)" :sets="player2.sets" :legs="player2.tempLegs" :legAverage="player2LegAverage" :gameAverage="player2.gameAverage" :scoreBusted="player2.scoreBusted"/>
    </div>
  </div>

  <NumberpadComponent v-if="game" @score-entered="processScore" @score-cleared="clearScore" @score-confirmed="confirmScore" @score-undo="undoScore" @score-left="leftScore"
                      :player1Score="this.player1.tempScore" :player2Score="this.player2.tempScore" :player1ToThrow="this.player1.toThrow" :player2ToThrow="this.player2.toThrow"/>

  <LegShutModalComponent />

  <GameShutModalComponent />

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
import GameShutModalComponent from './game-shut-modal.vue';
import Caller from './caller.vue';
import axios from 'axios';
import { EventBus } from '../event-bus';

axios.interceptors.request.use(config => {
  const token = localStorage.getItem('jwt_token');  // Retrieve JWT token from localStorage
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;  // Add JWT token to Authorization header
  }
  return config;
}, error => {
  return Promise.reject(error);
});

export default {
  name: 'GameComponent',

  components: {
    PlayerCardComponent,
    NumberpadComponent,
    LegShutModalComponent,
    GameShutModalComponent,
    Caller
  },

  data() {
    return {
      game: null,
      loading: true,
      error: false,
      gameWinnerPlayerId: null,
      legWinnerPlayerIds: [],
      setWinnerPlayerIds: [],
      score: 0,
      gameState: "",
      legsPlayed: 0,
      legsPerSetPlayed: [],
      startsLegPlayerId: null,

      player1: {
        id: 0,
        name: '',
        startScore: 0,
        tempScore: 0,
        score: 0,
        scoreBusted: false,
        currentScores: [],
        totalScores: [],
        toThrow: false,
        currentDartsThrown: [],
        totalDartsThrown: [],
        legAverages: [],
        gameAverage: 0,
        sets: 0,
        legs: 0,
        tempLegs: 0,
      },

      player2: {
        id: 0,
        name: '',
        startScore: 0,
        tempScore: 0,
        score: 0,
        scoreBusted: false,
        currentScores: [],
        totalScores: [],
        toThrow: false,
        currentDartsThrown: [],
        totalDartsThrown: [],
        legAverages: [],
        gameAverage: 0,
        sets: 0,
        legs: 0,
        tempLegs: 0,
      },
    };
  },

  computed: {
    player1LegAverage() {
      const totalScores = this.player1.currentScores.reduce((acc, score) => acc + score, 0);  // Sum the scores
      const scoreCount = this.player1.currentScores.length;  // Get the count of scores

      if (scoreCount === 0) {  // Avoid division by zero
        return 0;
      }

      const average = totalScores / scoreCount;
      return average.toFixed(1);  // Return with two decimal places
    },

    player2LegAverage() {
      const totalScores = this.player2.currentScores.reduce((acc, score) => acc + score, 0);
      const scoreCount = this.player2.currentScores.length;

      if (scoreCount === 0) {
        return 0;  // Avoid division by zero
      }

      return (totalScores / scoreCount).toFixed(1);  // Calculate and format
    },
  },

  created() {
    this.onLegShutModalConfirmed = (dartsForCheckout, average) => {
      const numberOfDarts = parseInt(dartsForCheckout);

      if (this.player1.toThrow) {
        this.processPlayerCheckout(this.player1);

        this.player1.legAverages.push(parseFloat(average));

        if (this.player2.currentScores.length > 0) {
          const sum = this.player2.currentScores.reduce((total, score) => total + score, 0);
          const average = (sum / this.player2.currentScores.length).toFixed(1);
          this.player2.legAverages.push(parseFloat(average));
        }

        const sumLegAveragesPlayer1 = this.player1.legAverages.reduce((sum, current) => sum + current, 0);
        this.player1.gameAverage = (sumLegAveragesPlayer1 / this.player1.legAverages.length).toFixed(1);

        const sumLegAveragesPlayer2 = this.player2.legAverages.reduce((sum, current) => sum + current, 0);
        this.player2.gameAverage = (sumLegAveragesPlayer2 / this.player2.legAverages.length).toFixed(1);

        this.player1.currentDartsThrown.unshift(numberOfDarts);
        this.player1.totalScores.push(this.player1.currentScores);
        this.player1.totalDartsThrown.push(this.player1.currentDartsThrown);
        this.player2.totalScores.push(this.player2.currentScores);
        this.player2.totalDartsThrown.push(this.player2.currentDartsThrown);

        this.logTotalInfo();
        if (this.gameState === "Finished") {
          this.saveGameData();
        }
      } else if (this.player2.toThrow) {
        this.processPlayerCheckout(this.player2);

        this.player2.legAverages.push(parseFloat(average));

        if (this.player1.currentScores.length > 0) {
          const sum = this.player1.currentScores.reduce((total, score) => total + score, 0);
          const average = (sum / this.player1.currentScores.length).toFixed(1);
          this.player1.legAverages.push(parseFloat(average));
        }

        const sumLegAveragesPlayer2 = this.player2.legAverages.reduce((sum, current) => sum + current, 0);
        this.player2.gameAverage = (sumLegAveragesPlayer2 / this.player2.legAverages.length).toFixed(1);

        const sumLegAveragesPlayer1 = this.player1.legAverages.reduce((sum, current) => sum + current, 0);
        this.player1.gameAverage = (sumLegAveragesPlayer1 / this.player1.legAverages.length).toFixed(1);

        this.player2.currentDartsThrown.unshift(numberOfDarts);
        this.player2.totalScores.push(this.player2.currentScores);
        this.player2.totalDartsThrown.push(this.player2.currentDartsThrown);
        this.player1.totalScores.push(this.player1.currentScores);
        this.player1.totalDartsThrown.push(this.player1.currentDartsThrown);

        this.logTotalInfo();
        if (this.gameState === "Finished") {
          this.saveGameData();
        }
      }

      if (this.gameState !== "Finished"){
        this.resetScores();
        this.switchToThrow();
      }
    };

    this.onLegShutModalResumed = () => {
      if (this.player1.toThrow) {
        this.player1.score += this.player1.currentScores[0];
        this.player1.currentScores.shift();
      } else if (this.player2.toThrow) {
        this.player2.score += this.player2.currentScores[0];
        this.player2.currentScores.shift();
      }
    };


    this.onGameShutModalConfirmed = () => {
      window.location.reload();
    };

    this.onGameShutModalResumed = () => {
      window.location.href = `/new-game`;
    };

    EventBus.on('leg-shut-modal-confirmed', this.onLegShutModalConfirmed);
    EventBus.on('leg-shut-modal-resumed', this.onLegShutModalResumed);
    EventBus.on('game-shut-modal-confirmed', this.onGameShutModalConfirmed);
    EventBus.on('game-shut-modal-resumed', this.onGameShutModalResumed);
  },

  beforeDestroy() {
    // Remove event listeners to avoid memory leaks
    EventBus.off('leg-shut-modal-confirmed', this.onLegShutModalConfirmed);
    EventBus.off('leg-shut-modal-resumed', this.onLegShutModalResumed);
    EventBus.off('game-shut-modal-confirmed', this.onGameShutModalConfirmed);
    EventBus.off('game-shut-modal-resumed', this.onGameShutModalResumed);
  },

  mounted() {
    this.gameId = this.getGameIdFromUrl();
    //EventBus.emit('show-game-shut-modal');
    //EventBus.emit('show-leg-shut-modal', this.player1.currentScores);

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
        this.player1.scoreBusted = false;
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
        if (this.player1.tempScore - score === 0 || this.player1.tempScore - score >= 2) {
          this.player1.scoreBusted = false;
          this.player1.score = this.player1.tempScore - score;
        } else {
          this.player1.scoreBusted = true;
          this.player1.score = this.player1.tempScore;
        }
      } else if (this.player2.toThrow) {
        if (this.player2.tempScore - score === 0 || this.player2.tempScore - score >= 2) {
          this.player2.scoreBusted = false;
          this.player2.score = this.player2.tempScore - score;
        } else {
          this.player2.scoreBusted = true;
          this.player2.score = this.player2.tempScore;
        }
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

      if (!this.scoreIsImpossible(score)) {
        //EventBus.emit('play-score-sound', score);
      }

      this.score = score;
      console.log("XXX Score: ", this.score);

      if (this.player1.toThrow) {
        if (this.player1.score === 0) {
          // Checkout!
          this.player1.currentScores.unshift(score);
          //EventBus.emit('play-gameShut-sound');
          EventBus.emit('show-leg-shut-modal', this.player1.currentScores);
        } else {
          this.player1.scoreBusted = false;
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
          EventBus.emit('show-leg-shut-modal', this.player2.currentScores);
        } else {
          this.player2.scoreBusted = false;
          this.player2.tempScore = this.player2.score;
          this.player2.currentScores.unshift(score);
          this.player2.toThrow = false;
          this.player1.toThrow = true;
          this.player2.currentDartsThrown.unshift(3);
        }
      }

      //this.logCurrentInfo(score);
    },

    undoScore(score) {
      this.clearScore(score);

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

    leftScore(score){
      score = parseInt(score.replace(/^0+/, ''), 10);
      if (isNaN(score)) {
        score = 0;
      }

      if (this.player1.toThrow) {
        if (this.scoreIsImpossible(this.player1.tempScore - score)) {
          this.player1.score = this.player1.tempScore;
        } else {
          EventBus.emit('play-score-sound', this.player1.tempScore - score);

          if (this.player1.score - this.player1.tempScore - score === 0) {
            this.player1.currentScores.unshift(this.player1.tempScore);
            this.player1.score = 0;
            EventBus.emit('show-leg-shut-modal', this.player1.currentScores);
          } else {
            this.player1.score = score
            this.player1.currentScores.unshift(this.player1.tempScore - score);
            this.player1.tempScore = score;
            this.player1.toThrow = false;
            this.player2.toThrow = true;
            this.player1.currentDartsThrown.unshift(3);
          }
        }
      } else if (this.player2.toThrow) {
        if (this.scoreIsImpossible(this.player2.tempScore - score)) {
          this.player2.score = this.player2.tempScore;
        } else {
          EventBus.emit('play-score-sound', this.player2.tempScore - score);

          if (this.player2.score - this.player2.tempScore - score === 0) {
            this.player2.currentScores.unshift(this.player2.tempScore);
            this.player2.score = 0;
            EventBus.emit('show-leg-shut-modal', this.player2.currentScores);
          } else {
            this.player2.score = score
            this.player2.currentScores.unshift(this.player2.tempScore - score);
            this.player2.tempScore = score;
            this.player2.toThrow = false;
            this.player1.toThrow = true;
            this.player2.currentDartsThrown.unshift(3);
          }
        }
      }
    },

    processPlayerCheckout(player){
      if (this.game.matchMode === "FirstToSets") {
        console.log("Leg shut");
        this.legsPlayed += 1;
        player.tempLegs += 1;
        this.legWinnerPlayerIds.push(player.id);

        if (this.game.matchModeLegsNeeded === player.tempLegs) {
          console.log("Set shut");
          this.legsPerSetPlayed.push(this.legsPlayed);
          this.legsPlayed = 0;
          this.resetLegs();
          player.sets += 1;
          this.setWinnerPlayerIds.push(player.id);

          if (this.game.matchModeSetsNeeded === player.sets) {
            console.log("Game shut");
            this.gameWinnerPlayerId = player.id;
            EventBus.emit('play-gameShutAndTheMatch-sound');
            this.gameState = "Finished";
          }
        }
      } else if (this.game.matchMode === "FirstToLegs") {
        console.log("Leg shut");
        this.legsPlayed += 1;
        player.tempLegs += 1;
        this.legWinnerPlayerIds.push(player.id);

        if (this.game.matchModeLegsNeeded === player.tempLegs) {
          console.log("Game shut");
          this.gameWinnerPlayerId = player.id;
          EventBus.emit('play-gameShutAndTheMatch-sound');
          this.gameState = "Finished";
        }
      }
    },

    scoreIsImpossible(score) {
      const impossibleScores = [179, 178, 176, 175, 173, 172, 169, 166, 163, 162];
      score = parseInt(score, 10);

      if (score > 180) {
        return true;
      } else return impossibleScores.includes(score);
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

    resetLegs() {
      this.player1.tempLegs = 0;
      this.player2.tempLegs = 0;
    },

    switchToThrow() {
      if (this.startsLegPlayerId === this.player1.id) {
        this.startsLegPlayerId = this.player2.id
        this.player1.toThrow = false;
        this.player2.toThrow = true;
      } else if (this.startsLegPlayerId === this.player2.id) {
        this.startsLegPlayerId = this.player1.id
        this.player1.toThrow = true;
        this.player2.toThrow = false;
      }
    },

    logTotalInfo () {
      console.log(" ");
      console.log("Game state: ", this.gameState);
      console.log("Game winner id: ", this.gameWinnerPlayerId);
      console.log("Leg winner Ids: ", JSON.stringify(this.legWinnerPlayerIds));
      console.log("Set winner Ids: ", JSON.stringify(this.setWinnerPlayerIds));
      console.log("Legs per set played: ", JSON.stringify(this.legsPerSetPlayed));
      console.log("#################### LOG TOTAL ####################");

      console.log("----------", this.player1.name, " / Player 1 ----------");
      console.log("Scores: " + JSON.stringify(this.player1.totalScores));
      console.log("Darts: " + JSON.stringify(this.player1.totalDartsThrown));
      console.log("Leg Averages: ", JSON.stringify(this.player1.legAverages));
      console.log("Game Average: ", JSON.stringify(this.player1.gameAverage));

      console.log(" ");

      console.log("----------", this.player2.name, " / Player 2 ----------");
      console.log("Scores: " + JSON.stringify(this.player2.totalScores));
      console.log("Darts: " + JSON.stringify(this.player2.totalDartsThrown));
      console.log("Leg Averages: ", JSON.stringify(this.player2.legAverages));
      console.log("Game Average: ", JSON.stringify(this.player2.gameAverage));
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
            //this.player1.name = this.game.player1Id;
            this.player1.id = this.game.player1Id;
            //this.player2.name = this.game.player2Id;
            this.player2.id = this.game.player2Id;
            this.startsLegPlayerId = this.game.startingPlayerId;
            this.gameState = this.game.state;

            if (this.player1.id === this.game.startingPlayerId) {
              this.player1.toThrow = true;
              this.player2.toThrow = false;
            } else if (this.player2.id === this.game.startingPlayerId) {
              this.player1.toThrow = false;
              this.player2.toThrow = true;
            }

            this.loading = false;
            this.getPlayerData();
          })
          .catch(error => {
            console.error('Error fetching game data:', error);
            this.error = true;
            this.loading = false;
          });
    },

    getPlayerData() {
      axios.get('/api/player/id/' + this.player1.id)
          .then(response => {
            this.player1.name = response.data.name;
          })
          .catch(error => {
            console.error('Error fetching player data:', error);
            this.error = true;
          });

      axios.get('/api/player/id/' + this.player2.id)
          .then(response => {
            this.player2.name = response.data.name;
          })
          .catch(error => {
            console.error('Error fetching player data:', error);
            this.error = true;
          });
    },

    saveGameData() {
      const postData = {
        gameId: this.game.id,
        gameState: this.gameState,
        legsPerSetPlayed: this.legsPerSetPlayed,

        gameWinnerPlayerId: this.gameWinnerPlayerId,
        legWinnerPlayerIds: this.legWinnerPlayerIds,
        setWinnerPlayerIds: this.setWinnerPlayerIds,

        player1Id: this.player1.id,
        player1TotalScores: this.player1.totalScores,
        player1DartsThrown: this.player1.totalDartsThrown,
        player1legAverages: this.player1.legAverages,
        player1gameAverage: this.player1.gameAverage,

        player2Id: this.player2.id,
        player2TotalScores: this.player2.totalScores,
        player2DartsThrown: this.player2.totalDartsThrown,
        player2legAverages: this.player2.legAverages,
        player2gameAverage: this.player2.gameAverage,
      };

      axios.post('/api/game/save', postData)
          .then(response => {
            console.log("Game saved successfully.");
            EventBus.emit('show-game-shut-modal');
          })
          .catch(error => {
            console.error('Error saving the game:', error);
          });
    }
  },
}
</script>

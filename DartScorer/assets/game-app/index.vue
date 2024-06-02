<template>
  <span v-if="eventSourceState === 0" class="dot"
        style="position: absolute; top: 35px; left: 25px; height: 10px; width: 10px; background-color: yellow; border-radius: 50%; z-index: 2000; display: inline-block;"></span>
  <span v-else-if="eventSourceState === 1" class="dot"
        style="position: absolute; top: 35px; left: 25px; height: 10px; width: 10px; background-color: #50BE96; border-radius: 50%; z-index: 2000; display: inline-block;"></span>
  <span v-else-if="eventSourceState === 2" class="dot"
        style="position: absolute; top: 35px; left: 25px; height: 10px; width: 10px; background-color: red; border-radius: 50%; z-index: 2000; display: inline-block;"></span>

  <div class="row px-1">
    <div class="col p-1" style="max-width: 50%;">
      <PlayerCardComponent v-if="game" :playerName="player1.name" :playerScore="player1.displayScore"
                           :toThrow="toThrowPlayerId === player1.id" :lastThrows="player1.lastScores.join(', ')"
                           :dartsThrown="calculateDartsThrownSum(player1)" :sets="player1.sets" :legs="player1.tempLegs"
                           :legAverage="player1LegAverage" :gameAverage="player1.gameAverage"
                           :scoreBusted="player1.scoreBusted"/>
    </div>
    <div class="col p-1" style="max-width: 50%;">
      <PlayerCardComponent v-if="game" :playerName="player2.name" :playerScore="player2.displayScore"
                           :toThrow="toThrowPlayerId === player2.id" :lastThrows="player2.lastScores.join(', ')"
                           :dartsThrown="calculateDartsThrownSum(player2)" :sets="player2.sets" :legs="player2.tempLegs"
                           :legAverage="player2LegAverage" :gameAverage="player2.gameAverage"
                           :scoreBusted="player2.scoreBusted"/>
    </div>
  </div>

  <NumberpadComponent v-if="game" @score-entered="processScore" @score-cleared="clearScore"
                      @score-confirmed="confirmScore" @score-undo="undoScore" @score-left="leftScore"
                      :player1Score="this.player1.totalScore" :player2Score="this.player2.totalScore"
                      :player1ToThrow="toThrowPlayerId === player1.id"
                      :player2ToThrow="toThrowPlayerId === player2.id"
                      :player1LastScores="this.player1.lastScores"
                      :player2LastScores="this.player2.lastScores"/>

  <LegShutModalComponent/>

  <GameShutModalComponent/>

  <Caller/>

</template>

<script>
/*
 #   TODO:
 # - If I start and check, Player2 is to throw first
 # - Don't allow input of impossible numbers blow 180
 # - Reset score after resume on "How many darts needed?"
 */

axios.interceptors.request.use(config => {
  const token = localStorage.getItem('jwt_token');  // Retrieve JWT token from localStorage
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;  // Add JWT token to Authorization header
  }
  return config;
}, error => {
  return Promise.reject(error);
});

import PlayerCardComponent from './player-card.vue';
import NumberpadComponent from './numberpad.vue';
import LegShutModalComponent from './leg-shut-modal.vue';
import GameShutModalComponent from './game-shut-modal.vue';
import Caller from './caller.vue';
import axios from 'axios';
import {EventBus} from '../event-bus';

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
      toThrowPlayerId: null,
      eventSourceState: 0,
      //eventSource: null,
      eventSources: [],

      player1: {
        id: 0,
        name: '',
        startScore: 0,
        totalScore: 0,
        displayScore: 0,
        scoreBusted: false,
        //currentScores: [],
        lastScores: [],
        totalScores: [],
        toThrow: false,
        currentDartsThrown: [],
        totalDartsThrown: [],
        legAverages: [],
        gameAverage: "0",
        sets: 0,
        legs: 0,
        tempLegs: 0,
      },

      player2: {
        id: 0,
        name: '',
        startScore: 0,
        totalScore: 0,
        displayScore: 0,
        scoreBusted: false,
        //currentScores: [],
        lastScores: [],
        totalScores: [],
        toThrow: false,
        currentDartsThrown: [],
        totalDartsThrown: [],
        legAverages: [],
        gameAverage: "0",
        sets: 0,
        legs: 0,
        tempLegs: 0,
      },
    };
  },

  computed: {
    player1LegAverage() {
      const totalScores = this.player1.lastScores.reduce((acc, score) => acc + score, 0);  // Sum the scores
      const scoreCount = this.player1.lastScores.length;  // Get the count of scores

      if (scoreCount === 0) {  // Avoid division by zero
        return "0";
      }

      const average = totalScores / scoreCount;
      return average.toFixed(1);
    },

    player2LegAverage() {
      const totalScores = this.player2.lastScores.reduce((acc, score) => acc + score, 0);
      const scoreCount = this.player2.lastScores.length;

      if (scoreCount === 0) {
        return "0";  // Avoid division by zero
      }

      return (totalScores / scoreCount).toFixed(1);
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
          this.apiSaveGameData();
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
          this.apiSaveGameData();
        }
      }

      if (this.gameState !== "Finished") {
        this.resetScores();
        this.switchToThrow();
      }
    };

    this.onLegShutModalResumed = () => {
      if (this.player1.toThrow) {
        this.player1.displayScore += this.player1.currentScores[0];
        this.player1.currentScores.shift();
      } else if (this.player2.toThrow) {
        this.player2.displayScore += this.player2.currentScores[0];
        this.player2.currentScores.shift();
      }
    };

    this.onGameShutModalConfirmed = () => {
      window.location.reload();
    };

    this.onGameShutModalResumed = () => {
      window.location.href = `/new-game`;
    };

    this.onGameShutModalHome = () => {
      window.location.href = `/darts`;
    };

    EventBus.on('leg-shut-modal-confirmed', this.onLegShutModalConfirmed);
    EventBus.on('leg-shut-modal-resumed', this.onLegShutModalResumed);
    EventBus.on('game-shut-modal-confirmed', this.onGameShutModalConfirmed);
    EventBus.on('game-shut-modal-new-game', this.onGameShutModalResumed);
    EventBus.on('game-shut-modal-home', this.onGameShutModalHome);
  },

  beforeUnmount() {
    EventBus.off('leg-shut-modal-confirmed', this.onLegShutModalConfirmed);
    EventBus.off('leg-shut-modal-resumed', this.onLegShutModalResumed);
    EventBus.off('game-shut-modal-confirmed', this.onGameShutModalConfirmed);
    EventBus.off('game-shut-modal-new-game', this.onGameShutModalResumed);
    EventBus.off('game-shut-modal-home', this.onGameShutModalHome);

    this.eventSources.forEach(es => es.close());
  },

  mounted() {
    this.gameId = this.getGameIdFromUrl();

    if (this.gameId) {
      this.apiGetGameData();

      // TODO: I would highly recommend, IMHO, that you have one EventSource object per SSE-providing service, and then emit the messages using different types.
      this.mercurePublishConfirm(this.gameId);
      //this.mercurePublishUndo(this.gameId);
      //this.mercurePublishToThrow(this.gameId);

      console.log(this.eventSources);
    } else {
      console.error('Game ID not found in the URL');
      this.error = true;
      this.loading = false;
    }

    //EventBus.emit('show-game-shut-modal');
    //EventBus.emit('show-leg-shut-modal', this.player1.currentScores);
  },

  methods: {
    mercurePublishConfirm(gameId) {
      const mercureUrl = process.env.MERCURE_PUBLIC_URL;
      const eventSource = new EventSource(`${mercureUrl}?topic=https://vllr.lu/score/confirm/${gameId}`);
      this.eventSources.push(eventSource);

      eventSource.onopen = () => {
        this.eventSourceState = eventSource.readyState;
      };

      eventSource.onerror = () => {
        this.eventSourceState = eventSource.readyState;
      };

      eventSource.onmessage = event => {
        const data = JSON.parse(event.data);
        console.log("PUBLISH RECEIVED - CONFIRM: ", data);
        this.setPlayerScore(data.playerId, data.newTotalScore);
        console.log("ADDING: ", data.thrownScore)
        this.addPlayerLastScore(data.playerId, data.thrownScore)
        this.switchToThrow();
      };
    },

    mercurePublishUndo(gameId) {
      const mercureUrl = process.env.MERCURE_PUBLIC_URL;
      const eventSource = new EventSource(`${mercureUrl}?topic=https://vllr.lu/score/undo/${gameId}`);
      this.eventSources.push(eventSource);

      eventSource.onmessage = event => {
        const data = JSON.parse(event.data);
        console.log("PUBLISH RECEIVED - UNDO: ", data);
        this.setPlayerScore(data.playerId, data.newTotalScore);
        this.removePlayerLastScore(data.playerId);
        this.switchToThrow();
      };
    },

    mercurePublishToThrow(gameId) {
      const mercureUrl = process.env.MERCURE_PUBLIC_URL;
      const eventSource = new EventSource(`${mercureUrl}?topic=https://vllr.lu/game/throw/${gameId}`);
      this.eventSources.push(eventSource);

      eventSource.onmessage = event => {
        const data = JSON.parse(event.data);
        console.log("PUBLISH RECEIVED - THROW: ", data);
        this.switchToThrow();
      };
    },

    formatScores(scores) {
      return scores.reverse().map(score => score.value);
    },

    // Game Logic ######################################################################################################
    calculateDartsThrownSum(player) {
      return player.currentDartsThrown.reduce((acc, curr) => acc + curr, 0);
    },

    clearScore(score) {
      //this.addPlayerLastScore(this.player1.id, -1)

      score = parseInt(score.replace(/^0+/, ''), 10);
      if (isNaN(score)) {
        score = 0;
      }

      if (this.toThrowPlayerId === this.player1.id) {
        this.player1.displayScore = this.player1.totalScore;
        this.player1.scoreBusted = false;
      } else if (this.toThrowPlayerId === this.player2.id) {
        this.player2.displayScore = this.player2.totalScore;
        this.player2.scoreBusted = false;
      }
    },

    processScore(score) {
      score = parseInt(score.replace(/^0+/, ''), 10);
      if (isNaN(score)) {
        score = 0;
      }

      if (this.toThrowPlayerId === this.player1.id) {
        if (this.player1.totalScore - score === 0 || this.player1.totalScore - score >= 2) {
          this.player1.scoreBusted = false;
          this.player1.displayScore = this.player1.totalScore - score;
        } else {
          this.player1.scoreBusted = true;
          this.player1.displayScore = this.player1.totalScore;
        }
      } else if (this.toThrowPlayerId === this.player2.id) {
        if (this.player2.totalScore - score === 0 || this.player2.totalScore - score >= 2) {
          this.player2.scoreBusted = false;
          this.player2.displayScore = this.player2.totalScore - score;
        } else {
          this.player2.scoreBusted = true;
          this.player2.displayScore = this.player2.totalScore;
        }
      }
    },

    confirmScore(score) {
      //this.logTotalInfo()

      score = parseInt(score.replace(/^0+/, ''), 10);
      if (isNaN(score)) {
        score = 0;
      }

      const playerTotalScore = this.getPlayerTotalScore(this.toThrowPlayerId);
      //this.apiSaveScore(this.game.id, this.toThrowPlayerId, score, 3)

      if (!this.scoreIsImpossible(score)) {
        //EventBus.emit('play-score-sound', score);
      }

      // Checkout
      if (playerTotalScore - score === 0) {
        if (this.eventSourceState === 1) {
          this.apiConfirmScore(this.game.id, this.toThrowPlayerId, score, 3);
        } else if (this.eventSourceState === 0) {
          //this.addPlayerLastScore(this.toThrowPlayerId, score)
          this.switchToThrow();
        }
      }
      // Scoring
      else if (playerTotalScore - score >= 2) {
        if (this.eventSourceState === 1) {
          this.apiConfirmScore(this.game.id, this.toThrowPlayerId, score, 3);
        } else if (this.eventSourceState === 0) {
          //this.addPlayerLastScore(this.toThrowPlayerId, score)
          this.switchToThrow();
        }
      }
      // Bust
      else if (playerTotalScore - score < 2) {
        if (this.eventSourceState === 1) {
          this.apiConfirmScore(this.game.id, this.toThrowPlayerId, score, 3);
        } else if (this.eventSourceState === 0) {
          this.switchToThrow();
        }
      }

      //this.score = score;

      /*
      if (this.toThrowPlayerId === this.player1.id) {
        if (this.player1.displayScore === 0) {
          // Checkout!
          //this.player1.lastScores.unshift(score);
          //EventBus.emit('play-gameShut-sound');
          EventBus.emit('show-leg-shut-modal', this.player1.currentScores);
        } else {
          this.player1.scoreBusted = false;
          this.player1.totalScore = this.player1.displayScore;
          //this.player1.lastScores.unshift(score);
          //this.player1.toThrow = false;
          //this.player2.toThrow = true;
          this.player1.currentDartsThrown.unshift(3);
        }
      } else if (this.toThrowPlayerId === this.player2.id) {
        if (this.player2.displayScore === 0) {
          // Checkout!
          //this.player2.lastScores.unshift(score);
          //EventBus.emit('play-gameShut-sound');
          EventBus.emit('show-leg-shut-modal', this.player2.currentScores);
        } else {
          this.player2.scoreBusted = false;
          this.player2.totalScore = this.player2.displayScore;
          //this.player2.lastScores.unshift(score);
          //this.player2.toThrow = false;
          //this.player1.toThrow = true;
          this.player2.currentDartsThrown.unshift(3);
        }
      }
       */
    },

    undoScore(score) {
      if (this.player1.lastScores.length === 0 && this.player2.lastScores.length === 0) {
        //this.switchToThrow()
        if (this.eventSourceState === 1) {
          this.apiSetPlayerToThrow(this.game.id, this.getNotToThrowPlayerId())
        }
        else if (this.eventSourceState === 0) {
          this.switchToThrow();
        }
      } else {
        this.clearScore(score);
        const playerId = this.getNotToThrowPlayerId();
        this.apiUndoScore(this.game.id, playerId);
      }
    },

    // TODO: Handle via api
    leftScore(inputScore) {
      inputScore = parseInt(inputScore.replace(/^0+/, ''), 10);
      if (isNaN(inputScore)) {
        inputScore = 0;
      }

      if (this.toThrowPlayerId === this.player1.id) {
        this.processLeftScore(this.player1, inputScore);

      } else if (this.toThrowPlayerId === this.player2.id) {
        this.processLeftScore(this.player2, inputScore);
      }
    },

    processLeftScore(player, score) {
      const thrownScore = player.totalScore - score;
      const leftScore = player.totalScore - thrownScore
      console.log("leftScore: ", leftScore)

      // Reset if left score is not possible
      if (this.scoreIsImpossible(thrownScore) || leftScore < 2 && leftScore !== 0) {
        console.log("TRIGGERD")
        player.displayScore = player.totalScore;
        player.scoreBusted = false;
      }
      // Checkout
      else if (leftScore === 0) {
        EventBus.emit('play-score-sound', thrownScore);
        //player.lastScores.unshift(thrownScore);
        player.currentDartsThrown.unshift(3);
        this.apiConfirmScore(this.game.id, player.id, thrownScore, 3)
        if (this.eventSourceState === 0) {
          this.switchToThrow();
        }
        this.setPlayerScore(player.id, score);
        EventBus.emit('show-leg-shut-modal', player.lastScores);
      }
      // Left
      else if (leftScore >= 2) {
        console.log("player.totalScore: ", player.totalScore, " thrownScore: ", thrownScore)
        player.scoreBusted = false;
        EventBus.emit('play-score-sound', thrownScore);
        //player.lastScores.unshift(thrownScore);
        player.currentDartsThrown.unshift(3);
        this.apiConfirmScore(this.game.id, player.id, thrownScore, 3)
        if (this.eventSourceState === 0) {
          this.switchToThrow();
        }
        this.setPlayerScore(player.id, score);
        //this.switchToThrow();
      }
    },

    processPlayerCheckout(player) {
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

      if (score < 1) {
        return true;
      } else if (score > 180) {
        return true;
      } else return impossibleScores.includes(score);
    },

    resetScores() {
      this.player1.currentScores = [];
      this.player1.displayScore = this.player1.startScore;
      this.player1.totalScore = this.player1.startScore;
      this.player1.currentDartsThrown = [];
      this.player2.currentScores = [];
      this.player2.displayScore = this.player2.startScore;
      this.player2.totalScore = this.player2.startScore;
      this.player2.currentDartsThrown = [];
    },

    resetLegs() {
      this.player1.tempLegs = 0;
      this.player2.tempLegs = 0;
    },

    switchToThrow() {
      if (this.toThrowPlayerId === this.player1.id) {
        this.toThrowPlayerId = this.player2.id;
      } else if (this.toThrowPlayerId === this.player2.id) {
        this.toThrowPlayerId = this.player1.id;
      }
    },

    setPlayerScore(playerId, playerScore) {
      if (playerId === this.player1.id) {
        this.player1.displayScore = playerScore;
        this.player1.totalScore = playerScore;
      } else if (playerId === this.player2.id) {
        this.player2.displayScore = playerScore;
        this.player2.totalScore = playerScore;
      }
    },

    addPlayerLastScore(playerId, lastScore) {
      if (playerId === this.player1.id) {
        this.player1.lastScores.unshift(lastScore);
      } else if (playerId === this.player2.id) {
        this.player2.lastScores.unshift(lastScore);
      }

      console.log("PLAYER 1 LAST SCORES: ", this.player1.lastScores)
      console.log("PLAYER 2 LAST SCORES: ", this.player2.lastScores)
    },

    removePlayerLastScore(playerId) {
      if (playerId === this.player1.id) {
        this.player1.lastScores.shift();
        this.player1.currentDartsThrown.shift();
      } else if (playerId === this.player2.id) {
        this.player2.lastScores.shift();
        this.player2.currentDartsThrown.shift();
      }
    },

    /*
    getToThrowPlayerId() {
      if (this.player1.toThrow) {
        return this.player1.id;
      } else if (this.player2.toThrow) {
        return this.player2.id
      }
    },
     */

    getNotToThrowPlayerId() {
      if (this.toThrowPlayerId === this.player1.id) {
        return this.player2.id;
      } else if (this.toThrowPlayerId === this.player2.id) {
        return this.player1.id
      }
    },

    getPlayerTotalScore(playerId) {
      if (playerId === this.player1.id) {
        return this.player1.totalScore;
      } else if (playerId === this.player2.id) {
        return this.player2.totalScore;
      }
    },

    /*
    getPlayerLastThrownScore(playerId) {
      if (playerId === this.player1.id) {
        console.log("Last score player 1: ", this.player1.lastScores[0])
        return this.player1.lastScores[0];
      } else if (playerId === this.player2.id) {
        console.log("Last score player 2: ", this.player2.lastScores[0])
        return this.player2.lastScores[0];
      }
    },
     */

    logTotalInfo() {
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

    apiSetPlayerToThrow(gameId, playerId) {
      const postData = {
        gameId: gameId,
        playerId: playerId,
      };

      axios.post('/api/game/to-throw', postData)
          .then(response => {
            //console.log("To throw successfully.");
          })
          .catch(error => {
            console.error('Error set to throw:', error);
          });
    },

    apiGetLastScores(gameId, playerId) {
      const postData = {
        gameId: gameId,
        playerId: playerId,
      };

      axios.post('/api/score/last', postData)
          .then(response => {
            //console.log("Last scores successfully.");

            if (playerId === this.player1.id) {
              this.player1.lastScores = this.formatScores(response.data)
            } else if (playerId === this.player2.id) {
              this.player2.lastScores = this.formatScores(response.data)
            }
          })
          .catch(error => {
            console.error('Error last scores:', error);
          });
    },

    apiGetGameData() {
      axios.get('/api/game/id/' + this.gameId)
          .then(response => {
            this.game = response.data;
            this.player1.startScore = this.game.startScore;
            this.player2.startScore = this.game.startScore;
            this.player1.totalScore = this.game.player1Score;
            this.player2.totalScore = this.game.player2Score;
            this.player1.displayScore = this.game.player1Score;
            this.player2.displayScore = this.game.player2Score;
            //this.player1.name = this.game.player1Id;
            this.player1.id = this.game.player1Id;
            //this.player2.name = this.game.player2Id;
            this.player2.id = this.game.player2Id;
            this.startsLegPlayerId = this.game.startingPlayerId;
            this.gameState = this.game.state;

            this.toThrowPlayerId = this.game.toThrowPlayerId;

            /*
            if (this.player1.id === this.game.startingPlayerId) {
              this.player1.toThrow = true;
              this.player2.toThrow = false;
            } else if (this.player2.id === this.game.startingPlayerId) {
              this.player1.toThrow = false;
              this.player2.toThrow = true;
            }
             */

            this.loading = false;
            this.apiGetPlayerData();
            this.apiGetLastScores(this.gameId, this.player1.id)
            this.apiGetLastScores(this.gameId, this.player2.id)
          })
          .catch(error => {
            console.error('Error fetching game data:', error);
            this.error = true;
            this.loading = false;
          });
    },

    apiGetPlayerData() {
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

    apiSaveScore(gameId, playerId, score, darts) {
      const postData = {
        gameId: gameId,
        playerId: playerId,
        score: score,
        darts: darts
      };

      axios.post('/api/score/save', postData)
          .then(response => {
            console.log("Score saved successfully.");
          })
          .catch(error => {
            console.error('Error saving the score:', error);
          });
    },

    apiConfirmScore(gameId, playerId, thrownScore, thrownDarts) {
      const postData = {
        gameId: gameId,
        playerId: playerId,
        thrownScore: thrownScore,
        thrownDarts: thrownDarts
      };

      console.log("apiConfirmScore Posting: ", postData);

      axios.post('/api/score/confirm', postData)
          .then(response => {

          })
          .catch(error => {
            console.error('Error confirm score:', error);
          });
    },

    apiUndoScore(gameId, playerId) {
      const postData = {
        gameId: gameId,
        playerId: playerId,
      };

      console.log("apiUndoScore Posting: ", postData);

      axios.post('/api/score/undo', postData)
          .then(response => {

          })
          .catch(error => {
            console.error('Error undo score:', error);
          });
    },





/*
    apiSaveGameData() {
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
            EventBus.emit('show-game-shut-modal', this.gameWinnerPlayerId);
          })
          .catch(error => {
            console.error('Error saving the game:', error);
          });
    },
*/
  },
}
</script>

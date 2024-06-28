<template>
  <span v-if="eventSourceState === 0" class="dot"
        style="position: absolute; top: 35px; left: 25px; height: 10px; width: 10px; background-color: yellow; border-radius: 50%; z-index: 1100; display: inline-block;"></span>
  <span v-else-if="eventSourceState === 1" class="dot"
        style="position: absolute; top: 35px; left: 25px; height: 10px; width: 10px; background-color: #50BE96; border-radius: 50%; z-index: 1100; display: inline-block;"></span>
  <span v-else-if="eventSourceState === 2" class="dot"
        style="position: absolute; top: 35px; left: 25px; height: 10px; width: 10px; background-color: red; border-radius: 50%; z-index: 1100; display: inline-block;"></span>

  <div class="row px-1">
    <div class="col p-1" style="max-width: 50%;">
      <PlayerCardComponent v-if="game" :playerName="player1.name" :playerScore="player1.displayScore"
                           :startingPlayer="player1.id === startingPlayerId" :toThrow="toThrowPlayerId === player1.id" :lastThrows="player1.lastScores.join(', ')"
                           :dartsThrown="calculateDartsThrownSum(player1)" :sets="player1.sets" :legs="player1.displayLegs"
                           :legAverage="player1LegAverage" :gameAverage="player1.gameAverage"
                           :scoreBusted="player1.scoreBusted"/>
    </div>
    <div class="col p-1" style="max-width: 50%;">
      <PlayerCardComponent v-if="game" :playerName="player2.name" :playerScore="player2.displayScore"
                           :startingPlayer="player2.id === startingPlayerId" :toThrow="toThrowPlayerId === player2.id" :lastThrows="player2.lastScores.join(', ')"
                           :dartsThrown="calculateDartsThrownSum(player2)" :sets="player2.sets" :legs="player2.displayLegs"
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
                      :player2LastScores="this.player2.lastScores"
                      :disableThrowButton="disableThrowButton"/>

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
 # - ToThrow after break played?
 */

// TODO: unify this.game.id and this.gameId

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
      toThrowPlayerId: null,
      startingPlayerId: null,
      eventSourceState: 0,
      eventSource: null,

      player1: {
        id: 0,
        name: '',
        startScore: 0,
        totalScore: 0,
        displayScore: 0,
        scoreBusted: false,
        lastScores: [],
        totalScores: [],
        toThrow: false,
        currentDartsThrown: [],
        totalDartsThrown: [],
        legAverages: [],
        gameAverage: "0",
        sets: 0,
        legs: 0,
        displayLegs: 0,
        currentLegId: 0,
        currentSetId: 0,
      },

      player2: {
        id: 0,
        name: '',
        startScore: 0,
        totalScore: 0,
        displayScore: 0,
        scoreBusted: false,
        lastScores: [],
        totalScores: [],
        toThrow: false,
        currentDartsThrown: [],
        totalDartsThrown: [],
        legAverages: [],
        gameAverage: "0",
        sets: 0,
        legs: 0,
        displayLegs: 0,
        currentLegId: 0,
        currentSetId: 0,
      },
    };
  },

  computed: {
    disableThrowButton() {
      const hasLegsOrSets = (this.player1.legs > 0 || this.player2.legs > 0 || this.player1.sets > 0 || this.player2.sets > 0);
      const bothLastScoresZero = (this.player1.lastScores.length === 0 && this.player2.lastScores.length === 0);

      // Disable button if either player has legs or sets, and both players don't have scores
      return hasLegsOrSets && bothLastScoresZero;
    },

    player1LegAverage() {
      const totalScores = this.player1.lastScores.reduce((acc, score) => acc + score, 0);  // Sum the scores
      const scoreCount = this.player1.lastScores.length;  // Get the count of scores

      if (scoreCount === 0) {
        return "0";
      }

      const average = totalScores / scoreCount;
      return average.toFixed(1);
    },

    player2LegAverage() {
      const totalScores = this.player2.lastScores.reduce((acc, score) => acc + score, 0);
      const scoreCount = this.player2.lastScores.length;

      if (scoreCount === 0) {
        return "0";
      }

      return (totalScores / scoreCount).toFixed(1);
    },
  },

  created() {
    this.onLegShutModalConfirmed = (checkoutScore, checkoutDartCount, checkoutAverage, winnerPlayerId, looserPlayerId) => {
      const winnerPlayer = this.getPlayerById(winnerPlayerId);
      const looserPlayer = this.getPlayerById(looserPlayerId);

      this.processCheckout(winnerPlayer);
      this.processPlayerAverages(winnerPlayer, looserPlayer, checkoutDartCount, checkoutAverage)

      // Don't switch to throw if break of throw
      if (this.toThrowPlayerId !== this.startingPlayerId) {
        //console.log("--- BREAK OF THROW ---")
        this.startingPlayerId = this.toThrowPlayerId;
        this.apiConfirmScore(this.gameId, this.toThrowPlayerId, checkoutScore, checkoutDartCount, false, true);
      } else {
        //console.log("--- SWITCH TO THROW ---")
        this.apiConfirmScore(this.gameId, this.toThrowPlayerId, checkoutScore, checkoutDartCount, true, true);
      }

      //console.log("PLAYER 1 DISPLAY SCORE: ", this.player1.displayScore)
      //console.log("PLAYER 2 DISPLAY SCORE: ", this.player2.displayScore)

      //this.logTotalInfo()

      this.apiUpdateLeg(winnerPlayer.currentLegId, winnerPlayerId);

      //this.logTotalInfo()
    };

    this.onLegShutModalResumed = (checkoutScore) => {
      this.apiUndoScore(this.gameId, this.toThrowPlayerId, false);
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

    this.eventSource.close();
  },

  mounted() {
    this.gameId = this.getGameIdFromUrl();

    if (this.gameId) {
      this.apiFetchGameData();

      // CHECK TODO: Only use one EventSource
      // CHECK TODO: Only call api when EventSource=1
      // TODO: Display blocking modal if EventSource!=1
      // TODO: If page is reloaded currentDartsThrown gets lost

      this.subscribeToGameEvents(this.gameId);

      console.log(this.eventSources);
    } else {
      console.error('Game ID not found in the URL');
      this.error = true;
      this.loading = false;
    }
  },

  methods: {
    subscribeToGameEvents(gameId) {
      const mercureUrl = process.env.MERCURE_PUBLIC_URL;
      const eventSource = new EventSource(`${mercureUrl}?topic=https://vllr.lu/game/${gameId}`);
      this.eventSource = eventSource; // Ensure you handle closing this connection when not needed

      eventSource.onopen = () => { this.eventSourceState = eventSource.readyState; };
      eventSource.onerror = () => { this.eventSourceState = eventSource.readyState; };

      eventSource.onmessage = event => {
        const data = JSON.parse(event.data);
        const opponentPlayer = this.getOpponentPlayerById(data.playerId);

        switch (data.eventType) {
          case 'checkout':
            // Create new legs / set
            if (this.gameState !== "Finished") {
              if (this.game.matchMode === "FirstToLegs") {

                this.apiCreateLeg(this.gameId, null);
                this.apiUpdateTallyScore(this.gameId, this.player1.id, this.player1.startScore, this.player1.legs, this.player1.sets);
                this.apiUpdateTallyScore(this.gameId, this.player2.id, this.player2.startScore, this.player2.legs, this.player2.sets);
                this.resetScores();

              } else if (this.game.matchMode === "FirstToSets") {

              }
            } else {
              // Game finished
            }

            this.apiSwitchToStartLeg(this.gameId);

            if (data.switchToTrow) {
              this.apiSwitchToThrow(this.gameId,() => {
                this.startingPlayerId = this.toThrowPlayerId;
              });
            }

            break;
          case 'confirm':
            //console.log("PUBLISH RECEIVED - CONFIRM: ", data);
            this.setPlayerScore(data.playerId, data.newTotalScore);
            this.addPlayerLastScore(data.playerId, data.thrownScore);
            this.addPlayerLastThrownDarts(data.playerId, 3);
            //console.log("SWITCH TO THROW ---> ", data.switchToTrow)
            if (data.switchToTrow){
              this.apiSwitchToThrow(this.gameId)
            }
            break;
          case 'undo':
            console.log("PUBLISH RECEIVED - UNDO: ", data);
            this.setPlayerScore(data.playerId, data.newTotalScore);
            this.removePlayerLastScore(data.playerId);
            if (data.switchToThrow) {
              this.apiSwitchToThrow(this.gameId)
            }
            break;
        }
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
      const player = this.getPlayerById(this.toThrowPlayerId)
      const opponentPlayer = this.getOpponentPlayerById(this.toThrowPlayerId);

      score = parseInt(score.replace(/^0+/, ''), 10);
      if (isNaN(score)) {
        score = 0;
      }

      if (player.totalScore - score === 0) {
        player.lastScores.unshift(score);
        EventBus.emit('show-leg-shut-modal', player.lastScores, player.id, opponentPlayer.id);
        //console.log("PLAYER 1 DISPLAY SCORE: ", this.player1.displayScore)
        //console.log("PLAYER 2 DISPLAY SCORE: ", this.player2.displayScore)
      } else {
        this.apiConfirmScore(this.game.id, this.toThrowPlayerId, score, 3, true, false);
      }
    },

    undoScore(score) {
      console.log("Player 1 last scores: ", this.player1.lastScores)
      console.log("Player 2 last scores: ", this.player2.lastScores)
      // Switch to throw
      if (this.player1.lastScores.length === 0 && this.player2.lastScores.length === 0 &&
          this.player1.legs === 0 && this.player2.legs === 0) {
        if (this.eventSourceState === 1) {
          console.log("--- UNDO SWITCH TO THROW ---")
          this.apiSwitchToStartLeg(this.gameId);
          this.apiSwitchToThrow(this.gameId, () => {
            this.startingPlayerId = this.toThrowPlayerId;
          });
        }
      // Undo score
      } else if (this.player1.lastScores.length > 0 || this.player2.lastScores.length > 0) {
        console.log("--- UNDO UNDO SCORE ---")
        this.clearScore(score);
        const playerId = this.getNotToThrowPlayerId();
        this.apiUndoScore(this.game.id, playerId, true);
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
        const opponentPlayer = this.getOpponentPlayerById(player.id)
        EventBus.emit('play-score-sound', thrownScore);
        //player.lastScores.unshift(thrownScore);
        player.currentDartsThrown.unshift(3);
        if (this.eventSourceState === 1) {
          this.apiConfirmScore(this.game.id, player.id, thrownScore, 3, true, true)
        }
        this.setPlayerScore(player.id, score);
        EventBus.emit('show-leg-shut-modal', player.lastScores, player.id, opponentPlayer.id);
      }
      // Left
      else if (leftScore >= 2) {
        console.log("player.totalScore: ", player.totalScore, " thrownScore: ", thrownScore)
        player.scoreBusted = false;
        EventBus.emit('play-score-sound', thrownScore);
        //player.lastScores.unshift(thrownScore);
        player.currentDartsThrown.unshift(3);
        if (this.eventSourceState === 1) {
          this.apiConfirmScore(this.game.id, player.id, thrownScore, 3, true, false)
        }
        this.setPlayerScore(player.id, score);
      }
    },

    processCheckout(player) {
      if (this.game.matchMode === "FirstToSets") {
        //console.log("Leg shut");
        this.legsPlayed += 1;
        player.legs += 1;
        player.displayLegs += 1;
        this.legWinnerPlayerIds.push(player.id);

        if (this.game.matchModeLegsNeeded === player.legs) {
          console.log("Set shut");
          this.legsPerSetPlayed.push(this.legsPlayed);
          this.legsPlayed = 0;
          this.resetDisplayLegs();
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
        //console.log("Leg shut");
        this.legsPlayed += 1;
        player.legs += 1;
        player.displayLegs += 1;
        this.legWinnerPlayerIds.push(player.id);

        if (this.game.matchModeLegsNeeded === player.legs) {
          console.log("Game shut");
          this.gameWinnerPlayerId = player.id;
          EventBus.emit('play-gameShutAndTheMatch-sound');
          this.gameState = "Finished";
        }
      }
    },

    processPlayerAverages(winnerPlayer, looserPlayer, checkoutDartCount, checkoutAverage) {
      winnerPlayer.legAverages.push(parseFloat(checkoutAverage));

      if (looserPlayer.lastScores.length > 0) {
        const sum = looserPlayer.lastScores.reduce((total, score) => total + score, 0);
        const average = (sum / looserPlayer.lastScores.length).toFixed(1);
        looserPlayer.legAverages.push(parseFloat(average));
      }

      const sumLegAveragesWinnerPlayer = winnerPlayer.legAverages.reduce((sum, current) => sum + current, 0);
      winnerPlayer.gameAverage = (sumLegAveragesWinnerPlayer / winnerPlayer.legAverages.length).toFixed(1);
      const sumLegAveragesLooserPlayer = looserPlayer.legAverages.reduce((sum, current) => sum + current, 0);
      looserPlayer.gameAverage = (sumLegAveragesLooserPlayer / looserPlayer.legAverages.length).toFixed(1);

      winnerPlayer.currentDartsThrown.unshift(checkoutDartCount);
      winnerPlayer.totalScores.push(winnerPlayer.lastScores);
      winnerPlayer.totalDartsThrown.push(winnerPlayer.currentDartsThrown);

      //console.log("---- looserPlayer.currentDartsThrown: ", looserPlayer.currentDartsThrown);
      looserPlayer.totalScores.push(looserPlayer.lastScores);
      looserPlayer.totalDartsThrown.push(looserPlayer.currentDartsThrown);
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
      //console.log("--- RESET SCORES ---")
      this.player1.lastScores = [];
      this.player1.displayScore = this.player1.startScore;
      this.player1.totalScore = this.player1.startScore;
      this.player1.currentDartsThrown = [];
      this.player2.lastScores = [];
      this.player2.displayScore = this.player2.startScore;
      this.player2.totalScore = this.player2.startScore;
      this.player2.currentDartsThrown = [];
    },

    resetDisplayLegs() {
      this.player1.displayLegs = 0;
      this.player2.displayLegs = 0;
    },

    switchToThrow() {
      if (this.toThrowPlayerId === this.player1.id) {
        this.toThrowPlayerId = this.player2.id;
      } else if (this.toThrowPlayerId === this.player2.id) {
        this.toThrowPlayerId = this.player1.id;
      }
    },

    setPlayerScore(playerId, score) {
      if (playerId === this.player1.id) {
        this.player1.totalScore = score;
        this.player1.displayScore = score;
      } else if (playerId === this.player2.id) {
        this.player2.totalScore = score;
        this.player2.displayScore = score;
      }
    },

    setPlayerLegSetWon(playerId, legsWon, setsWon) {
      if (playerId === this.player1.id) {
        this.player1.legs = legsWon;
        this.player1.displayLegs = legsWon;
        this.player1.sets = setsWon;
      } else if (playerId === this.player2.id) {
        this.player2.legs = legsWon;
        this.player2.displayLegs = legsWon;
        this.player2.sets = setsWon;
      }
    },

    setPlayerLegSetId(playerId, legId, setId) {
      if (playerId === this.player1.id) {
        this.player1.currentLegId = legId;
        this.player1.currentSetId = setId;
      } else if (playerId === this.player2.id) {
        this.player2.currentLegId = legId;
        this.player2.currentSetId = setId;
      }
    },

    addPlayerLastScore(playerId, lastScore) {
      if (playerId === this.player1.id) {
        this.player1.lastScores.unshift(lastScore);
      } else if (playerId === this.player2.id) {
        this.player2.lastScores.unshift(lastScore);
      }
    },

    addPlayerLastThrownDarts(playerId, thrownDarts) {
      if (playerId === this.player1.id) {
        this.player1.currentDartsThrown.unshift(thrownDarts);
      } else if (playerId === this.player2.id) {
        this.player2.currentDartsThrown.unshift(thrownDarts);
      }
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

    getNotToThrowPlayerId() {
      if (this.toThrowPlayerId === this.player1.id) {
        return this.player2.id;
      } else if (this.toThrowPlayerId === this.player2.id) {
        return this.player1.id
      }
    },

    getPlayerById(playerId) {
      if (playerId === this.player1.id) {
        return this.player1;
      } else if (playerId === this.player2.id) {
        return this.player2;
      }
    },

    getOpponentPlayerById(playerId) {
      if (playerId === this.player1.id) {
        return this.player2;
      } else if (playerId === this.player2.id) {
        return this.player1;
      }
    },

    logTotalInfo() {
      console.log(" ");
      console.log("Game state: ", this.gameState);
      console.log("Game Legs played: ", this.legsPlayed);
      console.log("Game winner id: ", this.gameWinnerPlayerId);
      console.log("Leg winner Ids: ", JSON.stringify(this.legWinnerPlayerIds));
      console.log("Set winner Ids: ", JSON.stringify(this.setWinnerPlayerIds));
      console.log("Legs per set played: ", JSON.stringify(this.legsPerSetPlayed));
      console.log("");
      console.log("#################### LOG TOTAL ####################");

      console.log("----------", this.player1.name, " / Player 1 ----------");
      console.log("True score: " + this.player1.totalScore);
      console.log("Display score: " + this.player1.displayScore);
      console.log("Scores: " + JSON.stringify(this.player1.totalScores));
      console.log("Total Darts: " + JSON.stringify(this.player1.totalDartsThrown));
      console.log("Current Darts: " + JSON.stringify(this.player1.currentDartsThrown));
      console.log("Leg Averages: ", JSON.stringify(this.player1.legAverages));
      console.log("Game Average: ", this.player1.gameAverage);
      console.log("Sets: ", this.player1.sets);
      console.log("Legs: ", this.player1.legs);

      console.log(" ");

      console.log("----------", this.player2.name, " / Player 2 ----------");
      console.log("True score: " + this.player2.totalScore);
      console.log("Display score: " + this.player2.displayScore);
      console.log("Scores: " + JSON.stringify(this.player2.totalScores));
      console.log("Total Darts: " + JSON.stringify(this.player2.totalDartsThrown));
      console.log("Current Darts: " + JSON.stringify(this.player2.currentDartsThrown));
      console.log("Leg Averages: ", JSON.stringify(this.player2.legAverages));
      console.log("Game Average: ", this.player2.gameAverage);
      console.log("Sets: ", this.player2.sets);
      console.log("Legs: ", this.player2.legs);
    },


    // Persistence #####################################################################################################
    getGameIdFromUrl() {
      const pathSegments = window.location.pathname.split('/');
      const lastSegment = pathSegments[pathSegments.length - 1];
      return /^\d+$/.test(lastSegment) ? parseInt(lastSegment) : null;
    },

    apiSwitchToThrow(gameId, callback) {
      const postData = {
        gameId: gameId,
      };

      axios.post('/api/game/to-throw', postData)
          .then(response => {
            //console.log("Switch to throw successfully.");
            this.switchToThrow();
            if (callback && typeof callback === 'function') {
              callback();  // Execute the callback if provided
            }
          })
          .catch(error => {
            console.error('Error set to throw:', error);
          });
    },

    apiSwitchToStartLeg(gameId, callback) {
      const postData = {
        gameId: gameId,
      };

      axios.post('/api/game/to-start-leg', postData)
          .then(response => {

            if (callback && typeof callback === 'function') {
              callback();  // Execute the callback if provided
            }
          })
          .catch(error => {
            console.error('Error set to start leg:', error);
          });
    },

    apiCreateLeg(gameId, setId) {
      const postData = {
        gameId: gameId,
        setId: setId,
      };

      axios.post('/api/leg/create', postData)
          .then(response => {
            const legId = response.data.legId
            this.player1.currentLegId = legId
            this.player2.currentLegId = legId

            this.apiUpdateTallyLegSet(this.gameId, this.player1.id, legId, null)
            this.apiUpdateTallyLegSet(this.gameId, this.player2.id, legId, null)

            console.log("Leg created successfully.");
            console.log(response.data);
          })
          .catch(error => {
            console.error('Error creating leg:', error);
          });
    },

    apiUpdateLeg(legId, winnerPlayerId) {
      const postData = {
        legId: legId,
        winnerPlayerId: winnerPlayerId,
      };

      axios.post('/api/leg/update', postData)
          .then(response => {
            //console.log("Leg updated successfully.");
          })
          .catch(error => {
            console.error('Error updating leg:', error);
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

    apiFetchGameData() {
      axios.get('/api/game/id/' + this.gameId)
          .then(response => {
            this.game = response.data;
            this.player1.startScore = this.game.startScore;
            this.player2.startScore = this.game.startScore;
            this.player1.id = this.game.player1Id;
            this.player2.id = this.game.player2Id;
            this.gameState = this.game.state;
            this.toThrowPlayerId = this.game.toThrowPlayerId;
            this.startingPlayerId = this.game.startingPlayerId;

            this.apiGetPlayerData();
            this.apiGetLastScores(this.gameId, this.player1.id)
            this.apiGetLastScores(this.gameId, this.player2.id)
            this.apiFetchTallyGamePlayer(this.gameId, this.player1.id)
            this.apiFetchTallyGamePlayer(this.gameId, this.player2.id)
            this.loading = false;
          })
          .catch(error => {
            console.error('Error fetching game data:', error);
            this.error = true;
            this.loading = false;
          });
    },

    // TODO:
    // apiFetchGameTally(gameId, playerId) {
    //   const postData = {
    //     gameId: gameId,
    //     playerId: playerId,
    //   };
    apiFetchTallyGamePlayer(gameId, playerId) {
      axios.get('/api/game/id/' + gameId + '/player/id/' + playerId)
          .then(response => {
            this.setPlayerScore(playerId, response.data.score);
            this.setPlayerLegSetWon(playerId, response.data.legsWon, response.data.setsWon);
            this.setPlayerLegSetId(playerId, response.data.legId, response.data.setId);
            if (response.data.toThrow) {
              this.toThrowPlayerId = response.data.playerId;
            }
            if (response.data.startedLeg) {
              this.startingPlayerId = response.data.playerId;
            }
            //console.log("------ TALLY: ", response.data)
          })
          .catch(error => {
            console.error('Error fetching tally data:', error);
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

    apiUpdateTallyScore(gameId, playerId, score, legsWon, setsWon) {
      const postData = {
        gameId: gameId,
        playerId: playerId,
        score: score,
        legsWon: legsWon,
        setsWon: setsWon,
      };

      axios.post('/api/tally/update-score', postData)
          .then(response => {
            //console.log("Tally updated successfully.");
          })
          .catch(error => {
            console.error('Error updating tally score:', error);
          });
    },

    apiUpdateTallyLegSet(gameId, playerId, legId, setId) {
      const postData = {
        gameId: gameId,
        playerId: playerId,
        legId: legId,
        setId: setId,
      };

      axios.post('/api/tally/update-leg-set', postData)
          .then(response => {
            //console.log("Tally updated successfully.");
          })
          .catch(error => {
            console.error('Error updating tally leg set:', error);
          });
    },

    apiConfirmScore(gameId, playerId, thrownScore, thrownDarts, switchToTrow, isCheckout) {
      //console.log("--- CALLED apiConfirmScore ---")

      const postData = {
        gameId: gameId,
        playerId: playerId,
        thrownScore: thrownScore,
        thrownDarts: thrownDarts,
        switchToTrow: switchToTrow,
        isCheckout: isCheckout
      };

      axios.post('/api/score/confirm', postData)
          .then(response => {
            //console.log("CONFIRM RESPONSE: ", response.data);
          })
          .catch(error => {
            console.error('Error confirm score:', error);
          });
    },

    apiUndoScore(gameId, playerId, switchToThrow) {
      const postData = {
        gameId: gameId,
        playerId: playerId,
        switchToThrow: switchToThrow,
      };

      console.log("apiUndoScore Posting: ", postData);

      axios.post('/api/score/undo', postData)
          .then(response => {

          })
          .catch(error => {
            console.error('Error undo score:', error);
          });
    },
  },
}
</script>

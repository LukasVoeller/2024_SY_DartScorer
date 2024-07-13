<template>
  <!-- Loading spinner -->
  <div v-if="loading" style="display: flex; justify-content: center; align-items: center; height: 80vh;">
    <VueSpinnerGears size="100" color="white" />
  </div>

  <span v-if="eventSourceState === 0" class="dot"
        style="position: absolute; top: 35px; left: 25px; height: 10px; width: 10px; background-color: yellow; border-radius: 50%; z-index: 1100; display: inline-block;"></span>
  <span v-else-if="eventSourceState === 1" class="dot"
        style="position: absolute; top: 35px; left: 25px; height: 10px; width: 10px; background-color: #2CAB73; border-radius: 50%; z-index: 1100; display: inline-block;"></span>
  <span v-else-if="eventSourceState === 2" class="dot"
        style="position: absolute; top: 35px; left: 25px; height: 10px; width: 10px; background-color: red; border-radius: 50%; z-index: 1100; display: inline-block;"></span>

  <!-- Tablet view -->
  <div class="row d-none d-md-flex">
    <div class="col-3">
      <PlayerCardTabletComponent v-if="game && !loading && isTablet" :playerName="player1.name" :playerScore="player1.displayScore"
                           :startingPlayerLeg="player1.id === startingLegPlayerId" :startingPlayerSet="player1.id === startingSetPlayerId"
                           :toThrow="toThrowPlayerId === player1.id" :lastThrows="player1.lastScores"
                           :dartsThrown="calculateDartsThrownSum(player1)" :sets="player1.sets" :legs="player1.legs/*calculateRemainingLegs(player1)*/"
                           :legAverage="player1LegAverage" :gameAverage="player1.gameAverage"
                           :scoreBusted="player1.scoreBusted"/>
    </div>

    <div class="col-3">
      <PlayerCardTabletComponent v-if="game && !loading && isTablet" :playerName="player2.name" :playerScore="player2.displayScore"
                           :startingPlayerLeg="player2.id === startingLegPlayerId" :startingPlayerSet="player2.id === startingSetPlayerId"
                           :toThrow="toThrowPlayerId === player2.id" :lastThrows="player2.lastScores"
                           :dartsThrown="calculateDartsThrownSum(player2)" :sets="player2.sets" :legs="player2.legs/*calculateRemainingLegs(player2)*/"
                           :legAverage="player2LegAverage" :gameAverage="player2.gameAverage"
                           :scoreBusted="player2.scoreBusted"/>
    </div>

    <div class="col-6">
      <NumberpadTabletComponent v-if="game && !loading && isTablet" @score-entered="processScore" @score-cleared="clearScore"
                          @score-confirmed="confirmScore" @score-undo="undoScore" @score-left="leftScore"
                          :player1Score="this.player1.totalScore" :player2Score="this.player2.totalScore"
                          :player1ToThrow="toThrowPlayerId === player1.id"
                          :player2ToThrow="toThrowPlayerId === player2.id"
                          :player1LastScores="this.player1.lastScores"
                          :player2LastScores="this.player2.lastScores"
                          :disableThrowButton="disableThrowButton"/>
    </div>
  </div>

  <!-- Smartphone view -->
  <div class="d-md-none">
    <div class="row px-1">
      <div class="col p-1" style="max-width: 50%;">
        <PlayerCardComponent v-if="game && !loading && !isTablet" :playerName="player1.name" :playerScore="player1.displayScore"
                             :startingPlayerLeg="player1.id === startingLegPlayerId" :startingPlayerSet="player1.id === startingSetPlayerId"
                             :toThrow="toThrowPlayerId === player1.id" :lastThrows="player1.lastScores.join(', ')"
                             :dartsThrown="calculateDartsThrownSum(player1)" :sets="player1.sets" :legs="player1.legs/*calculateRemainingLegs(player1)*/"
                             :legAverage="player1LegAverage" :gameAverage="player1.gameAverage"
                             :scoreBusted="player1.scoreBusted"/>
      </div>
      <div class="col p-1" style="max-width: 50%;">
        <PlayerCardComponent v-if="game && !loading && !isTablet" :playerName="player2.name" :playerScore="player2.displayScore"
                             :startingPlayerLeg="player2.id === startingLegPlayerId" :startingPlayerSet="player2.id === startingSetPlayerId"
                             :toThrow="toThrowPlayerId === player2.id" :lastThrows="player2.lastScores.join(', ')"
                             :dartsThrown="calculateDartsThrownSum(player2)" :sets="player2.sets" :legs="player2.legs/*calculateRemainingLegs(player2)*/"
                             :legAverage="player2LegAverage" :gameAverage="player2.gameAverage"
                             :scoreBusted="player2.scoreBusted"/>
      </div>
    </div>

    <NumberpadComponent v-if="game && !loading && !isTablet" @score-entered="processScore" @score-cleared="clearScore"
                        @score-confirmed="confirmScore" @score-undo="undoScore" @score-left="leftScore"
                        :player1Score="this.player1.totalScore" :player2Score="this.player2.totalScore"
                        :player1ToThrow="toThrowPlayerId === player1.id"
                        :player2ToThrow="toThrowPlayerId === player2.id"
                        :player1LastScores="this.player1.lastScores"
                        :player2LastScores="this.player2.lastScores"
                        :disableThrowButton="disableThrowButton"/>
  </div>

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
import PlayerCardTabletComponent from './player-card-tablet.vue';
import NumberpadComponent from './numberpad.vue';
import NumberpadTabletComponent from './numberpad-tablet.vue';
import LegShutModalComponent from './leg-shut-modal.vue';
import GameShutModalComponent from './game-shut-modal.vue';
import Caller from './caller.vue';
import axios from 'axios';
import {EventBus} from '../event-bus';
import {VueSpinnerGears} from "vue3-spinners";

import {
  apiSwitchToThrow,
  apiSwitchToStartLeg,
  apiUpdateTallyScore, apiUpdateTallyLegSet, apiSwitchToStartSet,
} from './services/apiTallyService';

import {
  apiCreateLeg, apiUpdateLeg,
} from './services/apiLegService';

import {
  apiCreateSet, apiUpdateSet
} from "./services/apiSetService";

import {
  apiFetchGameData,
  apiUpdateGameShot,
} from './services/apiGameService';

import {
  handleGameShutModalConfirmed, handleGameShutModalHome, handleGameShutModalResumed,
  handleLegShutModalConfirmed,
  handleLegShutModalResumed
} from './utils/eventHandler';

import {
  confirmScore as confirmScoreAction,
  clearScore as clearScoreAction,
  leftScore as leftScoreAction,
  undoScore as undoScoreAction
} from './game/gameAction';

export default {
  name: 'GameComponent',

  components: {
    PlayerCardComponent,
    PlayerCardTabletComponent,
    NumberpadComponent,
    NumberpadTabletComponent,
    LegShutModalComponent,
    GameShutModalComponent,
    Caller,
    VueSpinnerGears
  },

  data() {
    return {
      loading: true,
      error: false,
      gameWinnerPlayerId: null,
      //legWinnerPlayerIds: [],
      //setWinnerPlayerIds: [],
      score: 0,
      gameState: "",
      //legsPlayed: 0,
      //legsPerSetPlayed: [],
      toThrowPlayerId: null,
      startingLegPlayerId: null,
      startingSetPlayerId: null,
      eventSourceState: 0,
      eventSource: null,
      isTablet: false,

      game: {
        id: null,
        player1Id: null,
        player2Id: null,
        state: null,
        matchMode: null,
        matchModeLegsNeeded: null,
        matchModeSetsNeeded: null,
        currentLegId: null,
        currentSetId: null,
        winnerPlayerId: null,
      },

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
        //displayLegs: 0,
        //currentLegId: 0,
        //currentSetId: 0,
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
        //displayLegs: 0,
        //currentLegId: 0,
        //currentSetId: 0,
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
      handleLegShutModalConfirmed(this, checkoutScore, checkoutDartCount, checkoutAverage, winnerPlayerId, looserPlayerId);
    };

    this.onLegShutModalResumed = (checkoutScore) => {
      handleLegShutModalResumed(this, checkoutScore);
    };

    this.onGameShutModalConfirmed = handleGameShutModalConfirmed;
    this.onGameShutModalResumed = handleGameShutModalResumed;
    this.onGameShutModalHome = handleGameShutModalHome;

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

    window.removeEventListener('resize', this.checkScreenSize); // Clean up resize listener
  },

  mounted() {
    this.checkScreenSize(); // Call method to check screen size on component mount
    window.addEventListener('resize', this.checkScreenSize); // Listen for resize events

    this.game.id = this.getGameIdFromUrl();

    if (this.game.id) {
      this.fetchGameData(this.game.id);

      // CHECK TODO: Only use one EventSource
      // CHECK TODO: Only call api when EventSource=1
      // TODO: Display blocking modal if EventSource!=1
      // TODO: If page is reloaded currentDartsThrown gets lost

      this.subscribeToGameEvents(this.game.id);

      //console.log(this.eventSources);
    } else {
      console.error('Game ID not found in the URL');
      this.error = true;
      this.loading = false;
    }

    //console.log("GAME")
    //console.log(this.game)
  },

  methods: {
    checkScreenSize() {
      // Update isTablet based on current screen size
      this.isTablet = window.innerWidth >= 768; // Adjust breakpoint as needed
      console.log("Is Tablet: ", this.isTablet);
    },

    confirmScore(score) {
      confirmScoreAction(this, score);
    },

    clearScore(score) {
      clearScoreAction(this, score);
    },

    leftScore(score) {
      leftScoreAction(this, score);
    },

    undoScore(score) {
      undoScoreAction(this, score);
    },

    fetchGameData(gameId) {
      apiFetchGameData(this, gameId)
          .then(() => {
            this.loading = false;
          })
          .catch(error => {
            console.error('Error fetching game or player data:', error);
            this.error = true;
            this.loading = false;
          });
    },

    subscribeToGameEvents(gameId) {
      const mercureUrl = process.env.MERCURE_PUBLIC_URL;
      const eventSource = new EventSource(`${mercureUrl}?topic=https://vllr.lu/game/${gameId}`);
      this.eventSource = eventSource; // Ensure you handle closing this connection when not needed

      eventSource.onopen = () => { this.eventSourceState = eventSource.readyState; };
      eventSource.onerror = () => { this.eventSourceState = eventSource.readyState; };

      // TODO: Dont make api calls or data creation on publish event
      eventSource.onmessage = event => {
        const data = JSON.parse(event.data);
        const opponentPlayer = this.getOpponentPlayerById(data.playerId);

        switch (data.eventType) {
          case 'checkout':
            //console.log("PUBLISH RECEIVED - CHECKOUT: ", data);
            if (this.game.state !== "Finished") {

              if (this.game.matchMode === "FirstToLegs") {

                console.log("FirstToLegs - Create new Leg")
                apiCreateLeg(this, this.game.id, null)
                this.resetScores();

              } else if (this.game.matchMode === "FirstToSets") {
                const player = this.getPlayerById(this.toThrowPlayerId)
                //console.log("---> player.legs = ", player.legs)

                if (player.legs < this.game.matchModeLegsNeeded) {

                  //console.log("FirstToSets - Create new Leg")
                  apiCreateLeg(this, this.game.id, this.game.currentSetId)
                  this.resetScores();

                } else if (player.legs === this.game.matchModeLegsNeeded) {

                  //console.log("FirstToSets - Create new Set and Leg")

                  apiCreateSet(this, this.game.id, setId => {
                    apiCreateLeg(this, this.game.id, setId);
                  });

                  this.resetLegs();
                  this.resetScores();
                  apiSwitchToStartSet(this.game.id);
                  this.switchToStartSet();

                }
              }

              apiUpdateTallyScore(this.game.id, this.player1.id, this.player1.startScore, this.player1.legs, this.player1.sets);
              apiUpdateTallyScore(this.game.id, this.player2.id, this.player2.startScore, this.player2.legs, this.player2.sets);

            } else {
              // Game finished
            }

            apiSwitchToStartLeg(this.game.id);

            if (data.switchToTrow) {
              apiSwitchToThrow(this.game.id);
              this.switchToThrow();
              this.startingLegPlayerId = this.toThrowPlayerId;
            }

            break;
          case 'confirm':
            //console.log("PUBLISH RECEIVED - CONFIRM: ", data);
            this.setPlayerScore(data.playerId, data.newTotalScore);
            this.addPlayerLastScore(data.playerId, data.thrownScore);
            this.addPlayerLastThrownDarts(data.playerId, 3);
            if (data.switchToTrow){
              apiSwitchToThrow(this.game.id)
              this.switchToThrow();
            }
            break;
          case 'undo':
            //console.log("PUBLISH RECEIVED - UNDO: ", data);
            this.setPlayerScore(data.playerId, data.newTotalScore);
            this.removePlayerLastScore(data.playerId);
            if (data.switchToThrow) {
              apiSwitchToThrow(this.game.id)
              this.switchToThrow();
            }
            break;
        }
      };
    },

    calculateDartsThrownSum(player) {
      return player.lastScores.length * 3;
      //return player.currentDartsThrown.reduce((acc, curr) => acc + curr, 0);
    },

    calculateRemainingLegs(player) {
      if (this.game.matchMode === "FirstToSets") {
        return player.legs % this.game.matchModeLegsNeeded
      } else if (this.game.matchMode === "FirstToLegs") {
        return player.legs
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

    processCheckout(player) {
      if (this.game.matchMode === "FirstToSets") {
        //console.log("FirstToSets - Leg shut");
        //this.legsPlayed += 1;
        player.legs += 1;
        //player.displayLegs += 1;
        //this.legWinnerPlayerIds.push(player.id);

        //apiUpdateTallyScore(this.game.id, this.player1.id, this.player1.startScore, this.player1.legs, this.player1.sets);
        //apiUpdateTallyScore(this.game.id, this.player2.id, this.player2.startScore, this.player2.legs, this.player2.sets);

        apiUpdateLeg(this.game.currentLegId, player.id);

        if (this.game.matchModeLegsNeeded === player.legs) {
          //console.log("FirstToSets - Set shut");
          //this.legsPerSetPlayed.push(this.legsPlayed);
          //this.legsPlayed = 0;
          //this.resetLegs();
          player.sets += 1;
          //this.setWinnerPlayerIds.push(player.id);

          //apiUpdateTallyScore(this.game.id, this.player1.id, this.player1.startScore, this.player1.legs, this.player1.sets);
          //apiUpdateTallyScore(this.game.id, this.player2.id, this.player2.startScore, this.player2.legs, this.player2.sets);

          apiUpdateSet(this.game.currentSetId, player.id);

          if (this.game.matchModeSetsNeeded === player.sets) {
            console.log("Game shut");
            EventBus.emit('play-gameShutAndTheMatch-sound');
            this.game.winnerPlayerId = player.id;
            this.game.state = "Finished";
            apiUpdateGameShot(this.game.id, this.game.winnerPlayerId, this.game.state);
            EventBus.emit('show-game-shut-modal', this.gameWinnerPlayerId);
          }
        }

        //apiUpdateTallyScore(this.game.id, this.player1.id, this.player1.startScore, this.player1.legs, this.player1.sets);
        //apiUpdateTallyScore(this.game.id, this.player2.id, this.player2.startScore, this.player2.legs, this.player2.sets);

      } else if (this.game.matchMode === "FirstToLegs") {
        console.log("FirstToLegs - Leg shut");

        player.legs += 1;
        //player.displayLegs += 1;
        // this.legsPlayed += 1;
        // this.legWinnerPlayerIds.push(player.id);

        //apiUpdateTallyScore(this.game.id, this.player1.id, this.player1.startScore, this.player1.legs, this.player1.sets);
        //apiUpdateTallyScore(this.game.id, this.player2.id, this.player2.startScore, this.player2.legs, this.player2.sets);

        apiUpdateLeg(this.game.currentLegId, player.id);

        if (this.game.matchModeLegsNeeded === player.legs) {
          console.log("Game shut");
          EventBus.emit('play-gameShutAndTheMatch-sound');
          this.game.winnerPlayerId = player.id;
          this.game.state = "Finished";
          apiUpdateGameShot(this.game.id, this.game.winnerPlayerId, this.game.state);
          EventBus.emit('show-game-shut-modal', this.gameWinnerPlayerId);
        }

        //apiUpdateTallyScore(this.game.id, this.player1.id, this.player1.startScore, this.player1.legs, this.player1.sets);
        //apiUpdateTallyScore(this.game.id, this.player2.id, this.player2.startScore, this.player2.legs, this.player2.sets);

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

    resetLegs() {
      this.player1.legs = 0;
      this.player2.legs = 0;
      //apiUpdateTallyScore(this.game.id, this.player1.id, this.player1.startScore, this.player1.legs, this.player1.sets);
      //apiUpdateTallyScore(this.game.id, this.player2.id, this.player2.startScore, this.player2.legs, this.player2.sets);
    },

    switchToThrow() {
      if (this.toThrowPlayerId === this.player1.id) {
        this.toThrowPlayerId = this.player2.id;
      } else if (this.toThrowPlayerId === this.player2.id) {
        this.toThrowPlayerId = this.player1.id;
      }
    },

    switchToStartSet() {
      if (this.startingSetPlayerId === this.player1.id) {
        this.startingSetPlayerId = this.player2.id;
      } else if (this.startingSetPlayerId === this.player2.id) {
        this.startingSetPlayerId = this.player1.id;
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
        //this.player1.displayLegs = legsWon;
        this.player1.sets = setsWon;
      } else if (playerId === this.player2.id) {
        this.player2.legs = legsWon;
        //this.player2.displayLegs = legsWon;
        this.player2.sets = setsWon;
      }
    },

    setLegSetId(legId, setId) {
      this.game.currentLegId = legId;
      this.game.currentSetId = setId;
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
      console.log("Game state: ", this.game.state);
      //console.log("Game Legs played: ", this.legsPlayed);
      console.log("Game winner id: ", this.game.winnerPlayerId);
      //console.log("Leg winner Ids: ", JSON.stringify(this.legWinnerPlayerIds));
      //console.log("Set winner Ids: ", JSON.stringify(this.setWinnerPlayerIds));
      //console.log("Legs per set played: ", JSON.stringify(this.legsPerSetPlayed));
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

    getGameIdFromUrl() {
      const pathSegments = window.location.pathname.split('/');
      const lastSegment = pathSegments[pathSegments.length - 1];
      return /^\d+$/.test(lastSegment) ? parseInt(lastSegment) : null;
    },
  },
}
</script>
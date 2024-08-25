<template>
  <div v-if="game">
    <h1 style="padding-top: 15px">ID: {{ game.id }} - Game shot!</h1>

    <div class="card" style="background-color: #3C3C3C; color: white">
      <div class="card-body">
        <div class="row">
          <div class="col-6">
            <p style="margin: 0px">{{ game.startScore }} Startscore</p>
            <p style="margin: 0px">{{ game.finishType }}-Out</p>
            <p style="margin: 0px">{{ game.matchMode }} ({{ game.matchModeLegsNeeded }})</p>
          </div>
          <div class="col-6">
            <p style="margin: 0px">{{ formatDate(game.date) }}</p>
            <p style="margin: 0px">{{ formatTime(game.date) }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="row" style="padding-top: 20px">
      <div class="col-6" style="padding-right: 3px">
        <!-- Player 1 -->
        <div class="card" :style="game.winnerPlayerId === game.player1.id ? {backgroundColor: '#2CAB73'} : {backgroundColor: 'gray'}">
          <div class="card-body">
            <p style="font-size: 20px; margin: 0px; color: white">{{ game.player1.name }}</p>
            <div v-if="game.matchMode === 'FirstToLegs'">
              <p style="font-size: 20px; margin: 0px; color: white">Legs: {{ player1LegsWon }}</p>
            </div>
            <div v-else-if="game.matchMode === 'FirstToSets'">
              <p style="font-size: 20px; margin: 0px; color: white">Sets: {{ player1SetsWon }}</p>
              <p style="font-size: 15px; margin: 0px; color: white">Legs: {{ player1LegsWon }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-6" style="padding-left: 3px">
        <!-- Player 2 -->
        <div class="card" :style="game.winnerPlayerId === game.player2.id ? {backgroundColor: '#2CAB73'} : {backgroundColor: 'gray'}">
          <div class="card-body">
            <p style="font-size: 20px; margin: 0px; color: white">{{ game.player2.name }}</p>
            <div v-if="game.matchMode === 'FirstToLegs'">
              <p style="font-size: 20px; margin: 0px; color: white">Legs: {{ player2LegsWon }}</p>
            </div>
            <div v-else-if="game.matchMode === 'FirstToSets'">
              <p style="font-size: 20px; margin: 0px; color: white">Sets: {{ player2SetsWon }}</p>
              <p style="font-size: 15px; margin: 0px; color: white">Legs: {{ player2LegsWon }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Match mode legs -->
    <div v-if="game.matchMode === 'FirstToLegs'">
    <div class="col-12">
      <div class="card" style="background-color: #3C3C3C; color: white; margin-top: 20px;">
        <div class="card-body" style="padding-top: 0px">


            <div v-for="(leg, legIndex) in game.legs" :key="legIndex">
              <div class="row" style="padding-top: 20px">

                <div class="col-2 pe-0" style="">
                  <p style="font-size: 15px">Leg {{ legIndex + 1 }}</p>
                </div>

                <!-- Player 1 -->
                <div class="col-5" style="padding-right: 3px">
                  <button class="btn btn-primary"
                          type="button"
                          @click="toggleCollapse(legIndex)"
                          :style="leg.winnerPlayerId === game.player1.id ? {backgroundColor: '#2CAB73'} : {backgroundColor: 'gray', color: 'white'}"
                          style="width: 100%; border: none;"
                  >
                    <div class="row">
                      <div class="col-7 px-1" style="padding-right: 0px; border-right: 1px solid white; font-size: 15px;">
                        {{ averageScorePerLeg(leg.scores.filter(score => score.playerId === game.player1.id)) }} &empty;
                      </div>
                      <div class="col-5 pe-0" style="padding-left: 0px">
                        {{ totalDartsThrown(game.player1.id, leg) }}
                        <img src="/homepage/assets/img/dart-arrow-32px.png"
                             alt="dart arrow"
                             style="max-width: 17px; max-height: 17px"
                             :style="{filter: 'invert(100%)'}"
                        >
                      </div>
                    </div>
                  </button>

                  <div class="collapse" :id="'player1LegsCollapse' + '_' + legIndex">
                    <div class="card" :style="leg.winnerPlayerId === game.player1.id ? {backgroundColor: '#2CAB73'} : {backgroundColor: 'gray'}">
                      <div class="card-body" style="padding: 10px; font-size: 15px; color: white;">
                        <div v-for="(score, scoreIndex) in leg.scores.filter(score => score.playerId === game.player1.id)" :key="scoreIndex"
                             :style="scoreIndex === leg.scores.filter(score => score.playerId === game.player1.id).length - 1 && leg.winnerPlayerId === game.player1.id ? {color: 'white', fontWeight: 'bold'} : {}">
                          {{ score.value }}
                          <span v-if="scoreIndex === leg.scores.filter(score => score.playerId === game.player1.id).length - 1 && leg.winnerPlayerId === game.player1.id">({{ score.dartsThrown }})</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Player 2 -->
                <div class="col-5" style="padding-left: 3px">
                  <button class="btn btn-primary"
                          type="button"
                          @click="toggleCollapse(legIndex)"
                          :style="leg.winnerPlayerId === game.player2.id ? {backgroundColor: '#2CAB73'} : {backgroundColor: 'gray', color: 'white'}"
                          style="width: 100%; border: none;"
                  >
                    <div class="row">
                      <div class="col-7 px-1" style="padding-right: 0px; border-right: 1px solid white; font-size: 15px;">
                        {{ averageScorePerLeg(leg.scores.filter(score => score.playerId === game.player2.id)) }} &empty;
                      </div>
                      <div class="col-5 pe-0" style="padding-left: 0px">
                        {{ totalDartsThrown(game.player2.id, leg) }}
                        <img src="/homepage/assets/img/dart-arrow-32px.png"
                             alt="dart arrow"
                             style="max-width: 17px; max-height: 17px"
                             :style="{filter: 'invert(100%)'}"
                        >
                      </div>
                    </div>
                  </button>

                  <div class="collapse" :id="'player2LegsCollapse' + '_' + legIndex">
                    <div class="card" :style="leg.winnerPlayerId === game.player2.id ? {backgroundColor: '#2CAB73'} : {backgroundColor: 'gray'}">
                      <div class="card-body" style="padding: 10px; font-size: 15px; color: white;">
                        <div v-for="(score, scoreIndex) in leg.scores.filter(score => score.playerId === game.player2.id)" :key="scoreIndex"
                             :style="scoreIndex === leg.scores.filter(score => score.playerId === game.player2.id).length - 1 && leg.winnerPlayerId === game.player2.id ? {color: 'white', fontWeight: 'bold'} : {}">
                          {{ score.value }}
                          <span v-if="scoreIndex === leg.scores.filter(score => score.playerId === game.player2.id).length - 1 && leg.winnerPlayerId === game.player2.id">({{ score.dartsThrown }})</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Match mode sets -->
    <div v-if="game.matchMode === 'FirstToSets'">
      <div v-for="(set, setIndex) in game.sets" :key="setIndex">
        <div class="row" style="padding-top: 20px">

          <div class="col-12">
            <div class="card" style="background-color: #3C3C3C; color: white">
              <div class="card-body">
                <!-- Set -->
                <p>Set {{ setIndex + 1 }}</p>

                <div v-for="(leg, legIndex) in set.legs" :key="legIndex">
                  <div class="row" style="padding-top: 20px">
                    <!-- Leg -->
                    <div class="col-2 pe-0" style="">
                      <p style="font-size: 15px">Leg {{ legIndex + 1 }}</p>
                    </div>

                    <!-- Player 1 -->
                    <div class="col-5" style="padding-right: 3px">
                      <button class="btn btn-primary"
                              type="button"
                              @click="toggleCollapse(setIndex, legIndex)"
                              :style="leg.winnerPlayerId === game.player1.id ? {backgroundColor: '#2CAB73'} : {backgroundColor: 'gray', color: 'white'}"
                              style="width: 100%; border: none;"
                      >
                        <div class="row">
                          <div class="col-7 px-1" style="padding-right: 0px; border-right: 1px solid white; font-size: 15px;">
                            {{ averageScorePerLeg(leg.scores.filter(score => score.playerId === game.player1.id)) }} &empty;
                          </div>
                          <div class="col-5 pe-0" style="padding-left: 0px; font-size: 15px;">
                            {{ totalDartsThrown(game.player1.id, leg) }}
                            <img src="/homepage/assets/img/dart-arrow-32px.png"
                                 alt="dart arrow"
                                 style="max-width: 17px; max-height: 17px"
                                 :style="{filter: 'invert(100%)'}"
                            >
                          </div>
                        </div>
                      </button>

                      <div class="collapse" :id="'player1SetsCollapse' + '_' + setIndex + '_' + legIndex">
                        <div class="card" :style="leg.winnerPlayerId === game.player1.id ? {backgroundColor: '#2CAB73'} : {backgroundColor: 'gray'}">
                          <div class="card-body" style="padding: 10px; font-size: 15px; color: white">
                            <div v-for="(score, scoreIndex) in leg.scores.filter(score => score.playerId === game.player1.id)" :key="scoreIndex"
                                 :style="scoreIndex === leg.scores.filter(score => score.playerId === game.player1.id).length - 1 && leg.winnerPlayerId === game.player1.id ? {color: 'white', fontWeight: 'bold'} : {}">
                              {{ score.value }}
                              <span v-if="scoreIndex === leg.scores.filter(score => score.playerId === game.player1.id).length - 1 && leg.winnerPlayerId === game.player1.id">({{ score.dartsThrown }})</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Player 2 -->
                    <div class="col-5" style="padding-left: 3px">
                      <button class="btn btn-primary"
                              type="button"
                              @click="toggleCollapse(setIndex, legIndex)"
                              :style="leg.winnerPlayerId === game.player2.id ? {backgroundColor: '#2CAB73'} : {backgroundColor: 'gray', color: 'white'}"
                              style="width: 100%; border: none;"
                      >
                        <div class="row">
                          <div class="col-7 px-1" style="padding-right: 0px; border-right: 1px solid white; font-size: 15px;">
                            {{ averageScorePerLeg(leg.scores.filter(score => score.playerId === game.player2.id)) }} &empty;
                          </div>
                          <div class="col-5 pe-0" style="padding-left: 0px; font-size: 15px;">
                            {{ totalDartsThrown(game.player2.id, leg) }}
                            <img src="/homepage/assets/img/dart-arrow-32px.png"
                                 alt="dart arrow"
                                 style="max-width: 17px; max-height: 17px"
                                 :style="{filter: 'invert(100%)'}"
                            >
                          </div>
                        </div>
                      </button>

                      <div class="collapse" :id="'player2SetsCollapse' + '_' + setIndex + '_' + legIndex">
                        <div class="card" :style="leg.winnerPlayerId === game.player2.id ? {backgroundColor: '#2CAB73'} : {backgroundColor: 'gray'}">
                          <div class="card-body" style="padding: 10px; font-size: 15px; color: white">
                            <div v-for="(score, scoreIndex) in leg.scores.filter(score => score.playerId === game.player2.id)" :key="scoreIndex"
                                 :style="scoreIndex === leg.scores.filter(score => score.playerId === game.player2.id).length - 1 && leg.winnerPlayerId === game.player2.id ? {color: 'white', fontWeight: 'bold'} : {}">
                              {{ score.value }}
                              <span v-if="scoreIndex === leg.scores.filter(score => score.playerId === game.player2.id).length - 1 && leg.winnerPlayerId === game.player2.id">({{ score.dartsThrown }})</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>


  </div>
</template>

<script>
import axios from 'axios';

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
  name: 'GameFinishedComponent',

  data() {
    return {
      game: null,
      loading: true,
      error: false,
    };
  },

  computed: {
    player1LegsWon() {
      return this.game.legs.filter(leg => leg.winnerPlayerId === this.game.player1.id).length;
    },
    player2LegsWon() {
      return this.game.legs.filter(leg => leg.winnerPlayerId === this.game.player2.id).length;
    },
    player1SetsWon() {
      return this.game.sets.filter(set => set.winnerPlayerId === this.game.player1.id).length;
    },
    player2SetsWon() {
      return this.game.sets.filter(set => set.winnerPlayerId === this.game.player2.id).length;
    }
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
    formatDate(dateString) {
      const date = new Date(dateString);
      const day = String(date.getDate()).padStart(2, '0');
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const year = date.getFullYear();
      return `${day}.${month}.${year}`;
    },
    formatTime(dateString) {
      const date = new Date(dateString);
      const hours = String(date.getHours()).padStart(2, '0');
      const minutes = String(date.getMinutes()).padStart(2, '0');
      return `${hours}:${minutes} Uhr`;
    },

    getGameIdFromUrl() {
      const pathSegments = window.location.pathname.split('/');
      const lastSegment = pathSegments[pathSegments.length - 1];
      return /^\d+$/.test(lastSegment) ? parseInt(lastSegment) : null;
    },

    getGameData() {
      axios.get('/api/game/id/' + this.gameId)
          .then(response => {
            this.game = response.data;
            this.loading = false;
          })
          .catch(error => {
            console.error('Error fetching game data:', error);
            this.error = true;
            this.loading = false;
          });
    },

    totalDartsThrown(playerId, leg) {
      return leg.scores.filter(score => score.playerId === playerId).reduce((total, score) => total + score.dartsThrown, 0);
    },

    totalScores(scores) {
      return scores.reduce((total, score) => total + score.value, 0);
    },

    averageScorePerLeg(scores) {
      const totalScores = this.totalScores(scores);
      const dartsThrown = scores.reduce((total, score) => total + score.dartsThrown, 0);
      return dartsThrown > 0 ? (totalScores / dartsThrown * 3).toFixed(1) : '0.0'; // Multiply by 3 to get the average per dart
    },

    toggleCollapse(index1, index2 = null) {
      let collapse1 = document.getElementById(`player1LegsCollapse_${index1}`);
      let collapse2 = document.getElementById(`player2LegsCollapse_${index1}`);

      if (index2 !== null) {
        collapse1 = document.getElementById(`player1SetsCollapse_${index1}_${index2}`);
        collapse2 = document.getElementById(`player2SetsCollapse_${index1}_${index2}`);
      }

      if (collapse1.classList.contains('show')) {
        console.log("COLLAPSE SHOWN")
        var bsCollapse = new bootstrap.Collapse(collapse1, {
          toggle: true
        })
        var bsCollapse = new bootstrap.Collapse(collapse2, {
          toggle: true
        })
      } else {
        var bsCollapse = new bootstrap.Collapse(collapse1, {
          toggle: true
        })
        var bsCollapse = new bootstrap.Collapse(collapse2, {
          toggle: true
        })
      }
    }
  }
}
</script>

<style scoped>
/* Add your styles here */
</style>
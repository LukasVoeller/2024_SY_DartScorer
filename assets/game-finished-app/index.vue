<template>
  <div v-if="game">
    <h1 style="padding-top: 15px">ID: {{ game.id }} - Game shot!</h1>

    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-3">Start: {{ game.startScore }}</div>
          <div class="col-4">Finish: {{ game.finishType }}</div>
          <div class="col-5">Mode: {{ game.matchMode }}, ({{ game.matchModeLegsNeeded }})</div>
        </div>
      </div>
    </div>

    <div class="row" style="padding-top: 20px">
      <div class="col-6" style="padding-right: 3px">
        <div class="card" :style="game.winnerPlayerId === game.player1.id ? {backgroundColor: '#50BE96'} : {}">
          <div class="card-body">
            <h5 :style="game.winnerPlayerId === game.player1.id ? {color: 'white'} : {color: 'black'}">{{ game.player1.name }}</h5>
          </div>
        </div>
      </div>

      <div class="col-6" style="padding-left: 3px">
        <div class="card" :style="game.winnerPlayerId === game.player2.id ? {backgroundColor: '#50BE96'} : {}">
          <div class="card-body">
            <h5 :style="game.winnerPlayerId === game.player2.id ? {color: 'white'} : {color: 'black'}">{{ game.player2.name }}</h5>
          </div>
        </div>
      </div>
    </div>

    <!-- Match mode legs -->
    <div v-if="game.matchMode === 'FirstToLegs'">
      <div v-for="(leg, legIndex) in game.legs" :key="legIndex">
        <div class="row" style="padding-top: 20px">
          <p>Leg {{ legIndex + 1 }}</p>

          <!-- Player 1 -->
          <div class="col-6" style="padding-right: 3px">
            <button class="btn btn-primary"
                    type="button"
                    data-bs-toggle="collapse"
                    :href="'#player1LegsCollapse' + '_' + legIndex"
                    aria-expanded="false"
                    :aria-controls="'player1LegsCollapse' + '_' + legIndex"
                    :style="leg.winnerPlayerId === game.player1.id ? {backgroundColor: '#50BE96'} : {backgroundColor: 'white', color: 'black'}"
                    style="width: 100%; border: none;"
            >
              <div class="row">
                <div class="col-6" style="text-align: left; padding-right: 0px">
                  {{ averageScorePerLeg(leg.scores.filter(score => score.playerId === game.player1.id)) }}
                </div>
                <div class="col-4 d-flex justify-content-end" style="padding-left: 0px">
                  {{ totalDartsThrown(game.player1.id, leg) }}
                  <img src="/homepage/assets/img/dart-arrow-32px.png"
                       alt="dart arrow"
                       style="max-width: 20px; max-height: 20px"
                       :style="leg.winnerPlayerId === game.player1.id ? {filter: 'invert(100%)'} : {}"
                  >
                </div>
              </div>
            </button>

            <div class="collapse" :id="'player1LegsCollapse' + '_' + legIndex">
              <div class="card" :style="leg.winnerPlayerId === game.player1.id ? {backgroundColor: '#50BE96'} : {}">
                <div class="card-body" style="padding: 10px">
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
          <div class="col-6" style="padding-left: 3px">
            <button class="btn btn-primary"
                    type="button"
                    data-bs-toggle="collapse"
                    :href="'#player2LegsCollapse' + '_' + legIndex"
                    aria-expanded="false"
                    :aria-controls="'player2LegsCollapse' + '_' + legIndex"
                    :style="leg.winnerPlayerId === game.player2.id ? {backgroundColor: '#50BE96'} : {backgroundColor: 'white', color: 'black'}"
                    style="width: 100%; border: none;"
            >
              <div class="row">
                <div class="col-6" style="text-align: left; padding-right: 0px">
                  {{ averageScorePerLeg(leg.scores.filter(score => score.playerId === game.player2.id)) }}
                </div>
                <div class="col-4 d-flex justify-content-end" style="padding-left: 0px">
                  {{ totalDartsThrown(game.player2.id, leg) }}
                  <img src="/homepage/assets/img/dart-arrow-32px.png"
                       alt="dart arrow"
                       style="max-width: 20px; max-height: 20px"
                       :style="leg.winnerPlayerId === game.player2.id ? {filter: 'invert(100%)'} : {}"
                  >
                </div>
              </div>
            </button>

            <div class="collapse" :id="'player2LegsCollapse' + '_' + legIndex">
              <div class="card" :style="leg.winnerPlayerId === game.player2.id ? {backgroundColor: '#50BE96'} : {}">
                <div class="card-body" style="padding: 10px">
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

    <!-- Match mode sets -->
    <div v-if="game.matchMode === 'FirstToSets'">
      <div v-for="(set, setIndex) in game.sets" :key="setIndex">
        <div class="row" style="padding-top: 20px">
          <p>Set {{ setIndex + 1 }}</p>

          <div v-for="(leg, legIndex) in set.legs" :key="legIndex">
            <div class="row" style="padding-top: 20px">
              <p>Leg {{ legIndex + 1 }}</p>

              <!-- Player 1 -->
              <div class="col-6" style="padding-right: 3px">
                <button class="btn btn-primary"
                        type="button"
                        data-bs-toggle="collapse"
                        :href="'#player1SetsCollapse' + '_' + setIndex + '_' + legIndex"
                        aria-expanded="false"
                        :aria-controls="'player1SetsCollapse' + '_' + setIndex + '_' + legIndex"
                        :style="leg.winnerPlayerId === game.player1.id ? {backgroundColor: '#50BE96'} : {backgroundColor: 'white', color: 'black'}"
                        style="width: 100%; border: none;"
                >
                  <div class="row">
                    <div class="col-6" style="text-align: left; padding-right: 0px">
                      {{ averageScorePerLeg(leg.scores.filter(score => score.playerId === game.player1.id)) }}
                    </div>
                    <div class="col-4 d-flex justify-content-end" style="padding-left: 0px">
                      {{ totalDartsThrown(game.player1.id, leg) }}
                      <img src="/homepage/assets/img/dart-arrow-32px.png"
                           alt="dart arrow"
                           style="max-width: 20px; max-height: 20px"
                           :style="leg.winnerPlayerId === game.player1.id ? {filter: 'invert(100%)'} : {}"
                      >
                    </div>
                  </div>
                </button>

                <div class="collapse" :id="'player1SetsCollapse' + '_' + setIndex + '_' + legIndex">
                  <div class="card" :style="leg.winnerPlayerId === game.player1.id ? {backgroundColor: '#50BE96'} : {}">
                    <div class="card-body" style="padding: 10px">
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
              <div class="col-6" style="padding-left: 3px">
                <button class="btn btn-primary"
                        type="button"
                        data-bs-toggle="collapse"
                        :href="'#player2SetsCollapse' + '_' + setIndex + '_' + legIndex"
                        aria-expanded="false"
                        :aria-controls="'player2SetsCollapse' + '_' + setIndex + '_' + legIndex"
                        :style="leg.winnerPlayerId === game.player2.id ? {backgroundColor: '#50BE96'} : {backgroundColor: 'white', color: 'black'}"
                        style="width: 100%; border: none;"
                >
                  <div class="row">
                    <div class="col-6" style="text-align: left; padding-right: 0px">
                      {{ averageScorePerLeg(leg.scores.filter(score => score.playerId === game.player2.id)) }}
                    </div>
                    <div class="col-4 d-flex justify-content-end" style="padding-left: 0px">
                      {{ totalDartsThrown(game.player2.id, leg) }}
                      <img src="/homepage/assets/img/dart-arrow-32px.png"
                           alt="dart arrow"
                           style="max-width: 20px; max-height: 20px"
                           :style="leg.winnerPlayerId === game.player2.id ? {filter: 'invert(100%)'} : {}"
                      >
                    </div>
                  </div>
                </button>

                <div class="collapse" :id="'player2SetsCollapse' + '_' + setIndex + '_' + legIndex">
                  <div class="card" :style="leg.winnerPlayerId === game.player2.id ? {backgroundColor: '#50BE96'} : {}">
                    <div class="card-body" style="padding: 10px">
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
    }
  }
}
</script>

<style scoped>
/* Add your styles here */
</style>
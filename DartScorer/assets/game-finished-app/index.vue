<template>
  <div v-if="game">
    <h1 style="padding-top: 15px">ID: {{ game.id }} - Game shut!</h1>

    <div class="card" >
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
        <div class="card" :style="game.winnerPlayerId === player1.id ? {backgroundColor: '#2CAB73'} : {}">
          <div class="card-body">
            <h5 :style="game.winnerPlayerId === player1.id ? {color: 'white'} : {color: 'black'}">{{ player1.name }}</h5>
          </div>
        </div>
      </div>

      <div class="col-6" style="padding-left: 3px">
        <div class="card" :style="game.winnerPlayerId === player2.id ? {backgroundColor: '#2CAB73'} : {}">
          <div class="card-body">
            <h5 :style="game.winnerPlayerId === player2.id ? {color: 'white'} : {color: 'black'}">{{ player2.name }}</h5>
          </div>
        </div>
      </div>
    </div>

    <!-- Match mode legs -->
    <div v-if="game.matchMode === 'FirstToLegs'">
      <div v-for="(leg, legIndex) in legs" :key="legIndex">
        <!-- Leg Scores -->
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
                    :style="leg.winnerPlayerId === player1.id ? {backgroundColor: '#2CAB73'} : {backgroundColor: 'white', color: 'black'}"
                    style="width: 100%; border: none;"
                    @click="leg.player1IsExpanded = !leg.player1IsExpanded"
            >
              <div class="row">
                <div class="col-6" style="text-align: left; padding-right: 0px">
                  {{ averageScorePerLeg(leg.player1Scores) }}
                  &empty;
                </div>
                <div class="col-4 d-flex justify-content-end" style="padding-left: 0px">
                  {{ totalDartsThrown(leg.player1Scores) }}
                  <img src="/homepage/assets/img/dart-arrow-32px.png"
                       alt="dart arrow"
                       style="max-width: 20px; max-height: 20px"
                       :style="leg.winnerPlayerId === player1.id ? {filter: 'invert(100%)'} : {}"
                  >
                </div>
                <div class="col-2" style="padding: 0px">
                  <i v-if="!leg.player1IsExpanded" class="bi bi-chevron-down"></i>
                  <i v-else class="bi bi-chevron-up"></i>
                </div>
              </div>
            </button>

            <div class="collapse" :id="'player1LegsCollapse' + '_' + legIndex">
              <div class="card" :style="leg.winnerPlayerId === player1.id ? {backgroundColor: '#2CAB73'} : {}">
                <div class="card-body" style="padding: 10px">
                  <div v-for="(score, scoreIndex) in leg.player1Scores" :key="score.id"
                       :style="scoreIndex === 0 && leg.winnerPlayerId === player1.id ? {color: 'white', fontWeight: 'bold'} : {}">
                    {{ score.value }}
                    <span v-if="scoreIndex === 0 && leg.winnerPlayerId === player1.id">({{ score.dartsThrown }})</span>
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
                    :style="leg.winnerPlayerId === player2.id ? {backgroundColor: '#2CAB73'} : {backgroundColor: 'white', color: 'black'}"
                    style="width: 100%; border: none;"
                    @click="leg.player2IsExpanded = !leg.player2IsExpanded"
            >
              <div class="row">
                <div class="col-6" style="text-align: left; padding-right: 0px">
                  {{ averageScorePerLeg(leg.player2Scores) }}
                  &empty;
                </div>
                <div class="col-4 d-flex justify-content-end" style="padding-left: 0px">
                  {{ totalDartsThrown(leg.player2Scores) }}
                  <img src="/homepage/assets/img/dart-arrow-32px.png"
                       alt="dart arrow"
                       style="max-width: 20px; max-height: 20px"
                       :style="leg.winnerPlayerId === player2.id ? {filter: 'invert(100%)'} : {}"
                  >
                </div>
                <div class="col-2" style="padding: 0px">
                  <i v-if="!leg.player2IsExpanded" class="bi bi-chevron-down"></i>
                  <i v-else class="bi bi-chevron-up"></i>
                </div>
              </div>
            </button>

            <div class="collapse" :id="'player2LegsCollapse' + '_' + legIndex">
              <div class="card" :style="leg.winnerPlayerId === player2.id ? {backgroundColor: '#2CAB73'} : {}">
                <div class="card-body" style="padding: 10px">
                  <div v-for="(score, scoreIndex) in leg.player2Scores" :key="score.id"
                       :style="scoreIndex === 0 && leg.winnerPlayerId === player2.id ? {color: 'white', fontWeight: 'bold'} : {}">
                    {{ score.value }}
                    <span v-if="scoreIndex === 0 && leg.winnerPlayerId === player2.id">({{ score.dartsThrown }})</span>
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
      <div v-for="(set, setIndex) in sets" :key="'set' + setIndex" class="set-container">
        <div class="row" style="padding-top: 20px">
          <p>Set {{ setIndex + 1 }}</p>

          <div v-for="(leg, legIndex) in set.legs" :key="'leg' + legIndex" class="leg-container">
            <div class="row" style="padding-bottom: 20px">
              <!--
              <p>Leg {{ legIndex + 1 }}</p>
              -->

              <!-- Player 1 -->
              <div class="col-6" style="padding-right: 3px">
                <button class="btn btn-primary"
                        type="button"
                        data-bs-toggle="collapse"
                        :href="'#player1SetsCollapse' + '_' + setIndex + '_' + legIndex"
                        aria-expanded="false"
                        :aria-controls="'player1SetsCollapse' + '_' + setIndex + '_' + legIndex"
                        :style="leg.winnerPlayerId === player1.id ? {backgroundColor: '#2CAB73'} : {backgroundColor: 'white', color: 'black'}"
                        style="width: 100%; border: none;"
                        @click="leg.player1IsExpanded = !leg.player1IsExpanded"
                >
                  <div class="row">
                    <div class="col-6" style="text-align: left; padding-right: 0px">
                      {{ averageScorePerLeg(leg.player1Scores) }}
                      &empty;
                    </div>
                    <div class="col-4 d-flex justify-content-end" style="padding-left: 0px">
                      {{ totalDartsThrown(leg.player1Scores) }}
                      <img src="/homepage/assets/img/dart-arrow-32px.png"
                           alt="dart arrow"
                           style="max-width: 20px; max-height: 20px"
                           :style="leg.winnerPlayerId === player1.id ? {filter: 'invert(100%)'} : {}"
                      >
                    </div>
                    <div class="col-2" style="padding: 0px">
                      <i v-if="!leg.player1IsExpanded" class="bi bi-chevron-down"></i>
                      <i v-else class="bi bi-chevron-up"></i>
                    </div>
                  </div>
                </button>

                <div class="collapse" :id="'player1SetsCollapse' + '_' + setIndex + '_' + legIndex">
                  <div class="card" :style="leg.winnerPlayerId === player1.id ? {backgroundColor: '#2CAB73'} : {}">
                    <div class="card-body" style="padding: 10px">
                      <div v-for="(score, index) in leg.player1Scores" :key="score.id"
                           :style="index === 0 && leg.winnerPlayerId === player1.id ? {color: 'white', fontWeight: 'bold'} : {}">
                        {{ score.value }}
                        <span v-if="index === 0 && leg.winnerPlayerId === player1.id">({{ score.dartsThrown }})</span>
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
                        :style="leg.winnerPlayerId === player2.id ? {backgroundColor: '#2CAB73'} : {backgroundColor: 'white', color: 'black'}"
                        style="width: 100%; border: none;"
                        @click="leg.player2IsExpanded = !leg.player2IsExpanded"
                >
                  <div class="row">
                    <div class="col-6" style="text-align: left; padding-right: 0px">
                      {{ averageScorePerLeg(leg.player2Scores) }}
                      &empty;
                    </div>
                    <div class="col-4 d-flex justify-content-end" style="padding-left: 0px">
                      {{ totalDartsThrown(leg.player2Scores) }}
                      <img src="/homepage/assets/img/dart-arrow-32px.png"
                           alt="dart arrow"
                           style="max-width: 20px; max-height: 20px"
                           :style="leg.winnerPlayerId === player2.id ? {filter: 'invert(100%)'} : {}"
                      >
                    </div>
                    <div class="col-2" style="padding: 0px">
                      <i v-if="!leg.player2IsExpanded" class="bi bi-chevron-down"></i>
                      <i v-else class="bi bi-chevron-up"></i>
                    </div>
                  </div>
                </button>

                <div class="collapse" :id="'player2SetsCollapse' + '_' + setIndex + '_' + legIndex">
                  <div class="card" :style="leg.winnerPlayerId === player2.id ? {backgroundColor: '#2CAB73'} : {}">
                    <div class="card-body" style="padding: 10px">
                      <div v-for="(score, index) in leg.player2Scores" :key="score.id"
                           :style="index === 0 && leg.winnerPlayerId === player2.id ? {color: 'white', fontWeight: 'bold'} : {}">
                        {{ score.value }}
                        <span v-if="index === 0 && leg.winnerPlayerId === player2.id">({{ score.dartsThrown }})</span>
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

      player1: {
        id: 0,
        name: '',
      },

      player2: {
        id: 0,
        name: '',
      },

      legs: [],
      sets: []
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
            this.player1.id = this.game.player1Id;
            this.player2.id = this.game.player2Id;
            this.extractScores();
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

    /*
    extractScores() {
      this.legs = this.game.legs.map(leg => ({
        player1Scores: leg.scores.filter(score => score.playerId === this.player1.id),
        player2Scores: leg.scores.filter(score => score.playerId === this.player2.id)
      }));
    },
    */

    extractScores() {
      if (this.game.matchMode === "FirstToLegs") {
        this.legs = this.game.legs.map(leg => ({
          winnerPlayerId: leg.winnerPlayerId,
          player1Scores: leg.scores.filter(score => score.playerId === this.player1.id),
          player2Scores: leg.scores.filter(score => score.playerId === this.player2.id)
        }));
      } else if (this.game.matchMode === "FirstToSets") {
        this.sets = this.game.sets.map(set => ({
          winnerPlayerId: set.winnerPlayerId,
          legs: set.legs.map(leg => ({
            winnerPlayerId: leg.winnerPlayerId,
            player1Scores: leg.scores.filter(score => score.playerId === this.player1.id),
            player2Scores: leg.scores.filter(score => score.playerId === this.player2.id)
          }))
        }));
      }
    },

    totalDartsThrown(scores) {
      return scores.reduce((total, score) => total + score.dartsThrown, 0);
    },

    totalScores(scores) {
      return scores.reduce((total, score) => total + score.value, 0);
    },

    averageScorePerLeg(scores) {
      const totalScores = this.totalScores(scores);
      const dartsThrown = this.totalDartsThrown(scores);
      return dartsThrown > 0 ? ((totalScores / dartsThrown) * 3).toFixed(1) : 0; // toFixed(2) to limit the decimal places to 2
    }

  }
}
</script>

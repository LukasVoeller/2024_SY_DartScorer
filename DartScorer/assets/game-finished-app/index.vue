<template>
  <h1 style="padding-top: 15px">Game shut!</h1>

  <div v-if="game">
    <div class="card" >
      <div class="card-body">
        {{ game.id }}
        {{ game.startScore }}
        {{ game.matchMode }}
      </div>
    </div>

    <div v-for="(leg, index) in legs" :key="index">
      <div class="row" style="padding-top: 20px">

        <div class="col-6">
          <div class="card" :style="leg.winnerPlayerId === player1.id ? {border: '5px solid #2CAB73'} : {}">
            <div class="card-body">
              <h5 style="color: black;">{{ player1.name }} - Leg {{ index + 1 }}</h5>
              <ul>
                <li v-for="score in leg.player1Scores" :key="score.id">
                  Score: {{ score.value }}, ({{ score.dartsThrown }})
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div class="col-6">
            <div class="card" :style="leg.winnerPlayerId === player2.id ? {border: '5px solid #2CAB73'} : {}">
            <div class="card-body">
              <h5 style="color: black;">{{ player2.name }} - Leg {{ index + 1 }}</h5>
              <ul>
                <li v-for="score in leg.player2Scores" :key="score.id">
                  Score: {{ score.value }}, ({{ score.dartsThrown }})
                </li>
              </ul>
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

      legs: []
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
      axios.get('/api/game/' + this.gameId)
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
      this.legs = this.game.legs.map(leg => ({
        winnerPlayerId: leg.winnerPlayerId,  // Extract winner ID from each leg
        player1Scores: leg.scores.filter(score => score.playerId === this.player1.id),
        player2Scores: leg.scores.filter(score => score.playerId === this.player2.id)
      }));
    },

  }
}
</script>

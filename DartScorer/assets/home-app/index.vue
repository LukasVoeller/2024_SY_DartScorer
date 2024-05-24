<template>
  <h1 style="padding-top: 10px">Welcome!</h1>
  <div v-if="false">An error occurred while fetching game data.</div>

  <div v-if="loading" style="display: flex; justify-content: center; padding-top: 10px; padding-bottom: 10px">
    <VueSpinnerDots size="40" color="black" />
  </div>

  <div class="card shadow">
    <h1 style="color: black; align-self: center; padding-top: 15px">Latest 5</h1>
    <table v-if="games.length > 0" class="table table-hover">
      <thead class="table">
      <tr>
        <th>Date</th>
        <th>Mode</th>
        <th>Player 1</th>
        <th>Player 2</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      <template v-for="game in games">
        <tr data-bs-toggle="collapse" :data-bs-target="'#collapse' + game.id">
          <td>{{ formatDate(game.date) }}</td>
          <td>{{ game.gameMode }}</td>
          <td :style="{ color: game.winnerPlayerId === game.player1Id ? '#50BE96' : 'black' }">
            {{ getPlayerName(game.player1Id) }}
          </td>
          <td :style="{ color: game.winnerPlayerId === game.player2Id ? '#50BE96' : 'black' }">
            {{ getPlayerName(game.player2Id) }}
          </td>
          <td>
            <i v-if="game.state === 'Live'" style="color: #FAA094" class="bi bi-record-circle"></i>
            <i v-else-if="game.state === 'Finished'" style="color: #50BE96" class="bi bi-check-circle"></i>
          </td>
        </tr>

        <tr>
          <td colspan="5" style="padding: 0px;">
            <div :id="'collapse' + game.id" class="collapse">

              <div style="padding: 10px">
                <div class="row">
                  <div class="col-8">
                    <p>ID: {{ game.id }}</p>
                    <p>Start score: {{ game.startScore }}</p>
                    <p>Finish type: {{ game.finishType }}</p>
                    <p>Match mode: {{ game.matchMode }}</p>
                    <p>Date: {{ new Date(game.date).toLocaleString() }}</p>

                  </div>
                  <div class="col-4 d-flex flex-column align-items-end align-items-bottom">
                    <button type="button" style="width: 100px; background-color: #50BE96" class="btn btn-success" @click="viewGame(game.id)">View</button>
                  </div>
                </div>
              </div>

            </div>
          </td>
        </tr>

      </template>
      </tbody>
    </table>
  </div>
</template>

<script>

import {EventBus} from "../event-bus";

axios.interceptors.request.use(config => {
  const token = localStorage.getItem('jwt_token');  // Retrieve JWT token from localStorage
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;  // Add JWT token to Authorization header
  }
  return config;
}, error => {
  return Promise.reject(error);
});

import axios from 'axios';
import { VueSpinnerDots } from "vue3-spinners";

export default {
  name: 'HomeComponent',

  components: {
    VueSpinnerDots,
  },

  data() {
    return {
      games: [],
      players: {},
      loading: true,
      error: false,

      visibleRows: {}
    };
  },

  mounted() {
    this.getGameData();
    this.getPlayerData();
  },

  methods: {
    formatDate(dateString) {
      const date = new Date(dateString);
      const day = String(date.getDate()).padStart(2, '0');
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const year = String(date.getFullYear()).slice(-2);
      return `${day}.${month}.${year}`;
    },

    viewGame(id) {
      window.location.href = `/game/${id}`;
    },

    getGameData() {
      axios.get('/api/game/latest-five/')
          .then(response => {
            this.games = response.data;
            this.loading = false;
          })
          .catch(error => {
            console.error('Error fetching game data:', error);
            this.error = true;
            this.loading = false;
          });
    },

    getPlayerData() {
      axios.get('/api/player')
          .then(response => {
            this.players = response.data.reduce((acc, player) => {
              acc[player.id] = player.name;
              return acc;
            }, {});
          })
          .catch(error => {
            console.error('Error fetching player data:', error);
            this.error = true;
          });
    },

    getPlayerName(playerId) {
      return this.players[playerId] || 'Unknown';
    }
  }
}
</script>
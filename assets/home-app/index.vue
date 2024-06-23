<template>
  <!--  <h1 style="padding-top: 10px">Welcome!</h1>-->

  <br>

  <!-- Call to action -->
  <div class="row">
    <div class="col-4">
      <button type="button" @click="navigateToNewGame" style="height: 100px" class="btn btn-success w-100">
        <i class="bi bi-play-circle"></i><br>
        Match
      </button>
    </div>
    <div class="col-4">
      <button type="button" style="height: 100px" class="btn btn-success w-100">
        <i class="bi bi-cone"></i><br>
        Training
      </button>
    </div>
    <div class="col-4">
      <button type="button" style="height: 100px" class="btn btn-success w-100">
        <i class="bi bi-graph-up"></i><br>
        Statistics
      </button>
    </div>
  </div>

  <br>

  <!-- Latest 3 -->
  <div v-if="false">An error occurred while fetching game data.</div>
  <div v-if="loading" style="display: flex; justify-content: center; padding-top: 10px; padding-bottom: 10px">
    <VueSpinnerDots size="40" color="black" />
  </div>

  <div class="card shadow" style="background-color: #343E4C">
    <h1 style="color: black; align-self: center; padding-top: 15px; color: white">Latest 3</h1>
    <table v-if="games.length > 0" class="table table-hover">
      <thead class="table">
      <tr>
        <th style="background-color: #343E4C; color:white;">Date</th>
        <th style="background-color: #343E4C; color:white;">Mode</th>
        <th style="background-color: #343E4C; color:white;">Player 1</th>
        <th style="background-color: #343E4C; color:white;">Player 2</th>
        <th style="background-color: #343E4C; color:white;"></th>
      </tr>
      </thead>
      <tbody>

      <!-- Only display 3 games of the latest 4 -->
      <!-- <template v-for="game in games"> -->
      <template v-for="game in games.slice(0, 3)">
        <tr data-bs-toggle="collapse" :data-bs-target="'#collapse' + game.id">
          <td style="background-color: #343E4C; color:white;">{{ formatDate(game.date) }}</td>
          <td style="background-color: #343E4C; color:white;">{{ game.gameMode }}</td>
          <td :style="{ color: game.winnerPlayerId === game.player1Id ? '#50BE96' : 'white' }" style="background-color: #343E4C">
            {{ getPlayerName(game.player1Id) }}
          </td>
          <td :style="{ color: game.winnerPlayerId === game.player2Id ? '#50BE96' : 'white' }" style="background-color: #343E4C">
            {{ getPlayerName(game.player2Id) }}
          </td>
          <td style="background-color: #343E4C">
            <i v-if="game.state === 'Live'" style="color: #FF5E5E" class="bi bi-record-circle"></i>
            <i v-else-if="game.state === 'Finished'" style="color: #50BE96" class="bi bi-check-circle"></i>
          </td>
        </tr>

        <tr>
          <td colspan="5" style="padding: 0px; background-color: #343E4C">
            <div :id="'collapse' + game.id" class="collapse">

              <div style="padding: 10px;">
                <div class="row">
                  <div class="col-8" style="color: white;">
                    <p style="margin-bottom: 5px">ID: {{ game.id }}</p>
                    <p style="margin-bottom: 5px">Start score: {{ game.startScore }}</p>
                    <p style="margin-bottom: 5px">Finish type: {{ game.finishType }}</p>
                    <p style="margin-bottom: 5px">Match mode: {{ game.matchMode }}</p>
                    <p style="margin-bottom: 5px">Date: {{ new Date(game.date).toLocaleString() }}</p>

                  </div>
                  <div class="col-4 d-flex flex-column align-items-end align-items-bottom">
                    <button type="button" style="width: 100px;" class="btn btn-success" @click="viewGame(game.id)">View</button>
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

  <br>

  <!-- Statistic -->
  <div class="row">
    <div class="col-4">
      <div class="card h-100" style="display: flex; text-align: center; background-color: #343E4C; color: white">
        <div class="card-header">
          <div style="padding: 10px 0;">
            <i class="bi bi-trophy-fill" style="color: goldenrod"></i>
          </div>
          Average
        </div>
        <ul class="list-group list-group-flush">
          <li class="list-group-item" style="background-color: #343E4C; color: white">
            Lukas<br>
            45,6</li>
        </ul>
      </div>
    </div>
    <div class="col-4">
      <div class="card h-100" style="display: flex; text-align: center; background-color: #343E4C; color: white">
        <div class="card-header">
          <div style="text-align: center; padding: 10px 0;">
            <i class="bi bi-trophy-fill" style="color: goldenrod"></i>
          </div>
          Avg. CO
        </div>
        <ul class="list-group list-group-flush">
          <li class="list-group-item" style="background-color: #343E4C; color: white">
            Lukas<br>
            32,1</li>
        </ul>
      </div>
    </div>
    <div class="col-4">
      <div class="card h-100" style="display: flex; text-align: center; background-color: #343E4C; color: white">
        <div class="card-header">
          <div style="text-align: center; padding: 10px 0;">
            <i class="bi bi-trophy-fill" style="color: goldenrod"></i>
          </div>
          Darts 501
        </div>
        <ul class="list-group list-group-flush">
          <li class="list-group-item" style="background-color: #343E4C; color: white">
            Lukas<br>
            13 <img src="/homepage/assets/img/dart-arrow-32px.png" alt="dart arrow" style="max-width: 20px; filter: invert(100%)">
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script lang="ts">

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
import {defineComponent} from "vue";

interface Player {
  id: number;
  name: string;
}

export default defineComponent({
  name: 'HomeComponent',

  components: {
    VueSpinnerDots,
  },

  data() {
    return {
      games: [],
      players: [] as Player[],
      loading: true,
      error: false,

      visibleRows: {}
    };
  },

  mounted() {
    this.fetchLatestFiveGames();
    this.fetchPlayers();
  },

  methods: {
    navigateToNewGame() {
      window.location.href = '/new-game'; // This will cause the browser to navigate to the new game page
    },

    formatDate(dateString: string) {
      const date = new Date(dateString);
      const day = String(date.getDate()).padStart(2, '0');
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const year = String(date.getFullYear()).slice(-2);
      return `${day}.${month}.${year}`;
    },

    viewGame(id: Number) {
      window.location.href = `/game/${id}`;
    },

    fetchLatestFiveGames() {
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

    fetchPlayers() {
      this.loading = true;
      // Fetch players from the API
      axios.get('/api/player')
          .then(response => {
            this.loading = false;
            // Sort players alphabetically by name
            this.players = response.data
            console.log("PLAYER DATA: ", response.data)
          })
          .catch(error => {
            this.loading = false;
            console.error('Error fetching players:', error);
          });

      /*
      axios.get('/api/player')
          .then(response => {
            console.log("PLAYER DATA: ", response.data)

            this.players = response.data.reduce((acc, player) => {
              acc[player.id] = player.name;
              return acc;
            }, {});


          })
          .catch(error => {
            console.error('Error fetching player data:', error);
            this.error = true;
          });
          */
    },

    getPlayerName(playerId: Number) {
      const player = this.players.find((player: Player) => player.id === playerId);
      return player ? player.name : 'Unknown';
    }
  }
})
</script>
<script setup lang="ts">
</script>
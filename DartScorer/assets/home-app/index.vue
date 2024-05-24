<template>
  <h1 style="padding-top: 10px">Welcome!</h1>
  <div v-if="false">An error occurred while fetching game data.</div>



  <div v-if="loading" style="display: flex; justify-content: center; padding-top: 10px; padding-bottom: 10px">
    <VueSpinnerDots size="40" color="black" />
  </div>

  <table v-if="games.length > 0" class="table table-hover">
    <thead class="table">
      <tr>
        <th>Mode</th>
        <th>Player 1</th>
        <th>Player 2</th>
        <th>State</th>
      </tr>
    </thead>
    <tbody>
      <template v-for="game in games">
        <tr data-bs-toggle="collapse" :data-bs-target="'#collapse' + game.id">
          <td>{{ game.mode }}</td>
          <td>{{ game.player1Id }}</td>
          <td>{{ game.player2Id }}</td>
          <td>{{ game.state }}</td>
        </tr>

        <tr>
          <td colspan="5" style="padding: 0px">
            <div :id="'collapse' + game.id" class="collapse">

              <div style="padding: 10px">
                <div class="row">
                  <div class="col">
                    <p>ID: {{ game.id }}</p>
                    <p>Match mode: {{ game.matchMode }}</p>
                    <p>Date: {{ new Date(game.date).toLocaleString() }}</p>
                    <p>Finish: {{ game.finishType }}</p>
                  </div>
                  <div class="col">
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
      loading: true,
      error: false,

      visibleRows: {}
    };
  },

  mounted() {
    this.getGameData();
  },

  methods: {
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
  }
}
</script>
